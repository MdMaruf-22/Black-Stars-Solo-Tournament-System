<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['league_id'])) {
    die("League ID missing.");
}

$leagueId = $_GET['league_id'];

// Get league info
$stmt = $pdo->prepare("SELECT * FROM leagues WHERE id = ?");
$stmt->execute([$leagueId]);
$league = $stmt->fetch();
if (!$league) {
    die("League not found.");
}

// Get all players in the league
$stmt = $pdo->prepare("
    SELECT u.id, u.username
    FROM users u
    JOIN league_player lp ON lp.player_id = u.id
    WHERE lp.league_id = ?
");
$stmt->execute([$leagueId]);
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize stats
$stats = [];
foreach ($players as $player) {
    $stats[$player['id']] = [
        'name' => $player['username'],
        'played' => 0,
        'won' => 0,
        'drawn' => 0,
        'lost' => 0,
        'gf' => 0,
        'ga' => 0,
        'gd' => 0,
        'points' => 0
    ];
}

// Get all matches in league
$stmt = $pdo->prepare("
    SELECT * FROM matches
    WHERE league_id = ? AND player1_score IS NOT NULL AND player2_score IS NOT NULL
");
$stmt->execute([$leagueId]);
$matches = $stmt->fetchAll();

foreach ($matches as $match) {
    $p1 = $match['player1_id'];
    $p2 = $match['player2_id'];
    $s1 = $match['player1_score'];
    $s2 = $match['player2_score'];

    // Update played, goals
    $stats[$p1]['played']++;
    $stats[$p2]['played']++;

    $stats[$p1]['gf'] += $s1;
    $stats[$p1]['ga'] += $s2;

    $stats[$p2]['gf'] += $s2;
    $stats[$p2]['ga'] += $s1;

    // Result
    if ($s1 > $s2) {
        $stats[$p1]['won']++;
        $stats[$p1]['points'] += 3;
        $stats[$p2]['lost']++;
    } elseif ($s1 < $s2) {
        $stats[$p2]['won']++;
        $stats[$p2]['points'] += 3;
        $stats[$p1]['lost']++;
    } else {
        $stats[$p1]['drawn']++;
        $stats[$p2]['drawn']++;
        $stats[$p1]['points'] += 1;
        $stats[$p2]['points'] += 1;
    }

    // Goal difference
    $stats[$p1]['gd'] = $stats[$p1]['gf'] - $stats[$p1]['ga'];
    $stats[$p2]['gd'] = $stats[$p2]['gf'] - $stats[$p2]['ga'];
}

// Sort standings: points > GD > GF
usort($stats, function ($a, $b) {
    if ($a['points'] !== $b['points']) return $b['points'] - $a['points'];
    if ($a['gd'] !== $b['gd']) return $b['gd'] - $a['gd'];
    return $b['gf'] - $a['gf'];
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>League Standings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen px-4 py-6">

    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            Standings: <?php echo htmlspecialchars($league['name']); ?>
        </h2>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 text-sm text-center">
                <thead class="bg-gray-200 text-gray-700 uppercase">
                    <tr>
                        <th class="py-2 px-3 border">#</th>
                        <th class="py-2 px-3 border">Player</th>
                        <th class="py-2 px-3 border">MP</th>
                        <th class="py-2 px-3 border">W</th>
                        <th class="py-2 px-3 border">D</th>
                        <th class="py-2 px-3 border">L</th>
                        <th class="py-2 px-3 border">GF</th>
                        <th class="py-2 px-3 border">GA</th>
                        <th class="py-2 px-3 border">GD</th>
                        <th class="py-2 px-3 border">PTS</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    <?php $rank = 1; ?>
                    <?php foreach ($stats as $row): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="py-2 px-3 border"><?php echo $rank++; ?></td>
                            <td class="py-2 px-3 border"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="py-2 px-3 border"><?php echo $row['played']; ?></td>
                            <td class="py-2 px-3 border"><?php echo $row['won']; ?></td>
                            <td class="py-2 px-3 border"><?php echo $row['drawn']; ?></td>
                            <td class="py-2 px-3 border"><?php echo $row['lost']; ?></td>
                            <td class="py-2 px-3 border"><?php echo $row['gf']; ?></td>
                            <td class="py-2 px-3 border"><?php echo $row['ga']; ?></td>
                            <td class="py-2 px-3 border"><?php echo $row['gd']; ?></td>
                            <td class="py-2 px-3 border font-semibold"><?php echo $row['points']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">
                🔙 Back to Dashboard
            </a>
        </div>
    </div>

</body>
</html>
