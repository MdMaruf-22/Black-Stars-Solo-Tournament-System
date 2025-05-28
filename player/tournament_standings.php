<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['tournament_id'])) {
    echo "Tournament ID is missing.";
    exit;
}

$tournamentId = $_GET['tournament_id'];

// Fetch tournament info
$stmt = $pdo->prepare("SELECT * FROM tournaments WHERE id = ?");
$stmt->execute([$tournamentId]);
$tournament = $stmt->fetch();

if (!$tournament) {
    echo "Tournament not found.";
    exit;
}

// Get all players in tournament
$stmt = $pdo->prepare("
    SELECT DISTINCT player_id FROM (
        SELECT player1_id AS player_id FROM tournament_matches WHERE tournament_id = ?
        UNION
        SELECT player2_id AS player_id FROM tournament_matches WHERE tournament_id = ?
    ) AS players
");
$stmt->execute([$tournamentId, $tournamentId]);
$players = $stmt->fetchAll(PDO::FETCH_COLUMN);

$standings = [];

foreach ($players as $playerId) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$playerId]);
    $playerName = $stmt->fetchColumn();

    // Matches played
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM tournament_matches
        WHERE tournament_id = ? AND (player1_id = ? OR player2_id = ?)
        AND player1_score IS NOT NULL AND player2_score IS NOT NULL
    ");
    $stmt->execute([$tournamentId, $playerId, $playerId]);
    $played = $stmt->fetchColumn();

    // Wins
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM tournament_matches
        WHERE tournament_id = ? AND 
        (
            (player1_id = ? AND player1_score > player2_score) OR 
            (player2_id = ? AND player2_score > player1_score)
        )
    ");
    $stmt->execute([$tournamentId, $playerId, $playerId]);
    $wins = $stmt->fetchColumn();

    // Draws
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM tournament_matches
        WHERE tournament_id = ? AND 
        (player1_id = ? OR player2_id = ?) AND player1_score = player2_score
        AND player1_score IS NOT NULL AND player2_score IS NOT NULL
    ");
    $stmt->execute([$tournamentId, $playerId, $playerId]);
    $draws = $stmt->fetchColumn();

    // Losses
    $losses = $played - $wins - $draws;

    // Goals For
    $stmt = $pdo->prepare("
        SELECT 
            SUM(
                CASE 
                    WHEN player1_id = ? THEN player1_score 
                    WHEN player2_id = ? THEN player2_score 
                    ELSE 0 
                END
            ) as goals_for
        FROM tournament_matches
        WHERE tournament_id = ? AND (player1_id = ? OR player2_id = ?)
        AND player1_score IS NOT NULL AND player2_score IS NOT NULL
    ");
    $stmt->execute([$playerId, $playerId, $tournamentId, $playerId, $playerId]);
    $goalsFor = $stmt->fetchColumn() ?: 0;

    // Goals Against
    $stmt = $pdo->prepare("
        SELECT 
            SUM(
                CASE 
                    WHEN player1_id = ? THEN player2_score 
                    WHEN player2_id = ? THEN player1_score 
                    ELSE 0 
                END
            ) as goals_against
        FROM tournament_matches
        WHERE tournament_id = ? AND (player1_id = ? OR player2_id = ?)
        AND player1_score IS NOT NULL AND player2_score IS NOT NULL
    ");
    $stmt->execute([$playerId, $playerId, $tournamentId, $playerId, $playerId]);
    $goalsAgainst = $stmt->fetchColumn() ?: 0;

    $goalDifference = $goalsFor - $goalsAgainst;
    $points = ($wins * 3) + $draws;

    $standings[] = [
        'player_id' => $playerId,
        'player_name' => $playerName,
        'played' => $played,
        'wins' => $wins,
        'draws' => $draws,
        'losses' => $losses,
        'goals_for' => $goalsFor,
        'goals_against' => $goalsAgainst,
        'goal_difference' => $goalDifference,
        'points' => $points,
    ];
}

// Sort standings: points DESC, goal_difference DESC, goals_for DESC
usort($standings, function ($a, $b) {
    if ($a['points'] === $b['points']) {
        if ($a['goal_difference'] === $b['goal_difference']) {
            return $b['goals_for'] <=> $a['goals_for'];
        }
        return $b['goal_difference'] <=> $a['goal_difference'];
    }
    return $b['points'] <=> $a['points'];
});
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title><?php echo htmlspecialchars($tournament['name']); ?> - Standings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 py-6 px-4">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6">
            🏆 <?php echo htmlspecialchars($tournament['name']); ?> - Standings
        </h1>

        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 p-2 text-left">Player</th>
                    <th class="border border-gray-300 p-2 text-center">P</th>
                    <th class="border border-gray-300 p-2 text-center">W</th>
                    <th class="border border-gray-300 p-2 text-center">D</th>
                    <th class="border border-gray-300 p-2 text-center">L</th>
                    <th class="border border-gray-300 p-2 text-center">GF</th>
                    <th class="border border-gray-300 p-2 text-center">GA</th>
                    <th class="border border-gray-300 p-2 text-center">GD</th>
                    <th class="border border-gray-300 p-2 text-center">Pts</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($standings) === 0): ?>
                    <tr>
                        <td colspan="9" class="p-4 text-center text-gray-500">No matches played yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($standings as $s): ?>
                        <tr>
                            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($s['player_name']); ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?php echo $s['played']; ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?php echo $s['wins']; ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?php echo $s['draws']; ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?php echo $s['losses']; ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?php echo $s['goals_for']; ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?php echo $s['goals_against']; ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?php echo $s['goal_difference']; ?></td>
                            <td class="border border-gray-300 p-2 text-center font-bold"><?php echo $s['points']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">⬅️ Back to Dashboard</a>
        </div>
    </div>
</body>

</html>
