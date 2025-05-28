<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
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

// Get registered players
$stmt = $pdo->prepare("
    SELECT u.username FROM league_player lp
    JOIN users u ON lp.player_id = u.id
    WHERE lp.league_id = ?
");
$stmt->execute([$leagueId]);
$players = $stmt->fetchAll();

// Get fixtures
$stmt = $pdo->prepare("
    SELECT m.*, u1.username AS p1, u2.username AS p2
    FROM matches m
    JOIN users u1 ON m.player1_id = u1.id
    JOIN users u2 ON m.player2_id = u2.id
    WHERE m.league_id = ?
    ORDER BY m.id
");
$stmt->execute([$leagueId]);
$matches = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>League Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10 px-4">
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">🏆 League: <?php echo htmlspecialchars($league['name']); ?></h2>
            <a href="leagues.php" class="text-blue-600 hover:underline text-sm">⬅️ Back to All Leagues</a>
        </div>

        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">👥 Registered Players</h3>
            <ul class="list-disc pl-6 text-gray-600">
                <?php foreach ($players as $p): ?>
                    <li><?php echo htmlspecialchars($p['username']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-4">📅 Fixtures</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300 rounded-md">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th class="py-2 px-4 border">ID</th>
                            <th class="py-2 px-4 border">Match</th>
                            <th class="py-2 px-4 border">Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matches as $match): ?>
                            <tr class="text-gray-700 bg-white hover:bg-gray-50 transition">
                                <td class="py-2 px-4 border text-center"><?php echo $match['id']; ?></td>
                                <td class="py-2 px-4 border"><?php echo htmlspecialchars($match['p1'] . " vs " . $match['p2']); ?></td>
                                <td class="py-2 px-4 border text-center">
                                    <?php
                                    if ($match['player1_score'] === null || $match['player2_score'] === null) {
                                        echo "⏳ Not played";
                                    } else {
                                        echo "{$match['player1_score']} - {$match['player2_score']}";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($matches) === 0): ?>
                            <tr>
                                <td colspan="3" class="py-4 px-4 text-center text-gray-500">No matches scheduled yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
