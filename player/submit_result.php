<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$playerId = $_SESSION['user_id'];
$message = "";

if (!isset($_GET['match_id'])) {
    die("Match ID missing.");
}

$matchId = $_GET['match_id'];
if (!isset($_GET['league_id'])) {
    $_SESSION['flash'] = "League ID missing.";
    header("Location: dashboard.php");
    exit;
}
$leagueId = $_GET['league_id'];

// Get match details
$stmt = $pdo->prepare("
    SELECT m.*, u1.username AS p1_name, u2.username AS p2_name
    FROM matches m
    JOIN users u1 ON m.player1_id = u1.id
    JOIN users u2 ON m.player2_id = u2.id
    WHERE m.id = ? AND (m.player1_id = ? OR m.player2_id = ?)
");
$stmt->execute([$matchId, $playerId, $playerId]);
$match = $stmt->fetch();

if (!$match) {
    die("Unauthorized access.");
}

if ($match['player1_score'] !== null || $match['player2_score'] !== null) {
    die("Result already submitted.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $score1 = (int) $_POST['score1'];
    $score2 = (int) $_POST['score2'];

    // Update match result
    $stmt = $pdo->prepare("UPDATE matches SET player1_score = ?, player2_score = ? WHERE id = ?");
    $stmt->execute([$score1, $score2, $matchId]);

    // Determine outcome
    $isDraw = $score1 === $score2;
    $player1Win = $score1 > $score2;
    $player2Win = $score2 > $score1;

    // Update Player 1 stats
    $stmt = $pdo->prepare("
        UPDATE users
        SET matches_played = matches_played + 1,
            goals_scored = goals_scored + ?,
            goals_conceded = goals_conceded + ?,
            wins = wins + ?,
            losses = losses + ?,
            draws = draws + ?
        WHERE id = ?
    ");
    $stmt->execute([
        $score1,
        $score2,
        $player1Win ? 1 : 0,
        $player2Win ? 1 : 0,
        $isDraw ? 1 : 0,
        $match['player1_id']
    ]);

    // Update Player 2 stats
    $stmt->execute([
        $score2,
        $score1,
        $player2Win ? 1 : 0,
        $player1Win ? 1 : 0,
        $isDraw ? 1 : 0,
        $match['player2_id']
    ]);

    $_SESSION['flash'] = "✅ Result submitted successfully.";
    header("Location: matches.php?league_id=$leagueId");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Submit Match Result</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4 py-12">
    <div class="bg-white shadow-md rounded-lg max-w-md w-full p-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Submit Result</h2>

        <p class="text-center text-gray-700 mb-8">
            <strong class="text-blue-600"><?php echo htmlspecialchars($match['p1_name']); ?></strong> 
            vs 
            <strong class="text-blue-600"><?php echo htmlspecialchars($match['p2_name']); ?></strong>
        </p>

        <form method="post" class="space-y-6">
            <div>
                <label class="block text-gray-700 mb-1" for="score1">
                    <?php echo htmlspecialchars($match['p1_name']); ?> Score:
                </label>
                <input 
                    type="number" 
                    id="score1" 
                    name="score1" 
                    min="0" 
                    required
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                />
            </div>

            <div>
                <label class="block text-gray-700 mb-1" for="score2">
                    <?php echo htmlspecialchars($match['p2_name']); ?> Score:
                </label>
                <input 
                    type="number" 
                    id="score2" 
                    name="score2" 
                    min="0" 
                    required
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                />
            </div>

            <button 
                type="submit" 
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-md transition duration-200"
            >
                Submit Result
            </button>
        </form>

        <p class="mt-6 text-center">
            <a href="matches.php?league_id=<?php echo $leagueId; ?>" class="text-blue-600 hover:underline">← Back to Matches</a>
        </p>
    </div>
</body>
</html>
