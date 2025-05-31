<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Get completed leagues the user participated in
$stmt = $pdo->prepare("
    SELECT l.* FROM leagues l
    JOIN league_player lp ON lp.league_id = l.id
    WHERE lp.player_id = ? AND l.status = 'completed'
");
$stmt->execute([$userId]);
$completedLeagues = $stmt->fetchAll();

// Helper function to determine winner
function getLeagueWinner($pdo, $leagueId) {
    // Get players
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
            'id' => $player['id'],
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

    // Get matches
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

        if (!isset($stats[$p1]) || !isset($stats[$p2])) continue;

        $stats[$p1]['played']++;
        $stats[$p2]['played']++;
        $stats[$p1]['gf'] += $s1;
        $stats[$p1]['ga'] += $s2;
        $stats[$p2]['gf'] += $s2;
        $stats[$p2]['ga'] += $s1;

        if ($s1 > $s2) {
            $stats[$p1]['won']++;
            $stats[$p1]['points'] += 3;
            $stats[$p2]['lost']++;
        } elseif ($s2 > $s1) {
            $stats[$p2]['won']++;
            $stats[$p2]['points'] += 3;
            $stats[$p1]['lost']++;
        } else {
            $stats[$p1]['drawn']++;
            $stats[$p2]['drawn']++;
            $stats[$p1]['points'] += 1;
            $stats[$p2]['points'] += 1;
        }

        $stats[$p1]['gd'] = $stats[$p1]['gf'] - $stats[$p1]['ga'];
        $stats[$p2]['gd'] = $stats[$p2]['gf'] - $stats[$p2]['ga'];
    }

    // Sort by points, gd, gf
    usort($stats, function ($a, $b) {
        if ($a['points'] !== $b['points']) return $b['points'] - $a['points'];
        if ($a['gd'] !== $b['gd']) return $b['gd'] - $a['gd'];
        return $b['gf'] - $a['gf'];
    });

    return $stats[0]['name'] ?? null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Completed Leagues</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8 px-4">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">🏁 Completed Leagues</h2>
        <?php if (count($completedLeagues) > 0): ?>
            <ul class="space-y-4">
                <?php foreach ($completedLeagues as $league): ?>
                    <?php $winner = getLeagueWinner($pdo, $league['id']); ?>
                    <li class="p-4 border bg-gray-50 rounded">
                        <div class="font-semibold text-lg text-gray-700">
                            <?php echo htmlspecialchars($league['name']); ?>
                        </div>

                        <?php if ($winner): ?>
                            <div class="text-sm text-green-700 mt-1">
                                🏆 Winner: <strong><?php echo htmlspecialchars($winner); ?></strong>
                            </div>
                        <?php else: ?>
                            <div class="text-sm text-gray-500 mt-1">
                                🏆 Winner: Not determined
                            </div>
                        <?php endif; ?>

                        <div class="mt-2 space-x-4">
                            <a href="league_standings.php?league_id=<?php echo $league['id']; ?>" class="text-blue-600 hover:underline text-sm">
                                📊 Final Standings
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-500">You haven't completed any leagues yet.</p>
        <?php endif; ?>
        <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">⬅ Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
