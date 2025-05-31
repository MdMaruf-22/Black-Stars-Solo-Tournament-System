<?php
require_once '../config/db.php';

if (!isset($_GET['league_id'])) {
    die("League ID is missing.");
}

$leagueId = $_GET['league_id'];

// Get league info
$stmt = $pdo->prepare("SELECT * FROM leagues WHERE id = ?");
$stmt->execute([$leagueId]);
$league = $stmt->fetch();
if (!$league) {
    die("League not found.");
}

// Get players in the league
$stmt = $pdo->prepare("
    SELECT u.id, u.username
    FROM users u
    JOIN league_player lp ON lp.player_id = u.id
    WHERE lp.league_id = ?
");
$stmt->execute([$leagueId]);
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Init stats
$stats = [];
foreach ($players as $p) {
    $stats[$p['id']] = [
        'name' => $p['username'],
        'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0,
        'gf' => 0, 'ga' => 0, 'gd' => 0, 'points' => 0
    ];
}

// Get results
$stmt = $pdo->prepare("
    SELECT * FROM matches
    WHERE league_id = ? AND player1_score IS NOT NULL AND player2_score IS NOT NULL
");
$stmt->execute([$leagueId]);
$matches = $stmt->fetchAll();

foreach ($matches as $m) {
    $p1 = $m['player1_id']; $p2 = $m['player2_id'];
    $s1 = $m['player1_score']; $s2 = $m['player2_score'];

    $stats[$p1]['played']++; $stats[$p2]['played']++;
    $stats[$p1]['gf'] += $s1; $stats[$p1]['ga'] += $s2;
    $stats[$p2]['gf'] += $s2; $stats[$p2]['ga'] += $s1;

    if ($s1 > $s2) {
        $stats[$p1]['won']++; $stats[$p1]['points'] += 3;
        $stats[$p2]['lost']++;
    } elseif ($s1 < $s2) {
        $stats[$p2]['won']++; $stats[$p2]['points'] += 3;
        $stats[$p1]['lost']++;
    } else {
        $stats[$p1]['drawn']++; $stats[$p2]['drawn']++;
        $stats[$p1]['points'] += 1; $stats[$p2]['points'] += 1;
    }

    $stats[$p1]['gd'] = $stats[$p1]['gf'] - $stats[$p1]['ga'];
    $stats[$p2]['gd'] = $stats[$p2]['gf'] - $stats[$p2]['ga'];
}

// Sort standings
usort($stats, function ($a, $b) {
    return [$b['points'], $b['gd'], $b['gf']] <=> [$a['points'], $a['gd'], $a['gf']];
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($league['name']); ?> - League Standings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen py-10 px-4">

    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6">
        <h1 class="text-3xl font-bold mb-6 text-center">
            🏆 Standings: <?php echo htmlspecialchars($league['name']); ?>
        </h1>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-300">
                <thead class="bg-gray-200 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="py-2 px-3 border">#</th>
                        <th class="py-2 px-3 border text-left">Player</th>
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
                <tbody>
                    <?php $i = 1; foreach ($stats as $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-3 border font-semibold">
                                <?php echo $i === 1 ? '🥇' : ($i === 2 ? '🥈' : ($i === 3 ? '🥉' : $i)); $i++; ?>
                            </td>
                            <td class="py-2 px-3 border text-left"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="py-2 px-3 border"><?php echo $row['played']; ?></td>
                            <td class="py-2 px-3 border"><?php echo $row['won']; ?></td>
                            <td class="py-2 px-3 border"><?php echo $row['drawn']; ?></td>
                            <td class="py-2 px-3 border"><?php echo $row['lost']; ?></td>
                            <td class="py-2 px-3 border"><?php echo $row['gf']; ?></td>
                            <td class="py-2 px-3 border"><?php echo $row['ga']; ?></td>
                            <td class="py-2 px-3 border"><?php echo $row['gd']; ?></td>
                            <td class="py-2 px-3 border font-bold"><?php echo $row['points']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-center">
            <a href="index.php" class="text-blue-600 hover:underline">← Back to Home</a>
        </div>
    </div>

</body>
</html>
