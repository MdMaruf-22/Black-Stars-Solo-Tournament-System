<?php
session_start();
require_once '../config/db.php';

// Get league ID from query
if (!isset($_GET['league_id'])) {
    die("League ID missing.");
}

$leagueId = $_GET['league_id'];

// Get league info
$stmt = $pdo->prepare("SELECT name FROM leagues WHERE id = ?");
$stmt->execute([$leagueId]);
$league = $stmt->fetch();

if (!$league) {
    die("League not found.");
}

// Fetch all matches in this league
$stmt = $pdo->prepare("
    SELECT m.*, u1.username AS p1_name, u2.username AS p2_name
    FROM matches m
    JOIN users u1 ON m.player1_id = u1.id
    JOIN users u2 ON m.player2_id = u2.id
    WHERE m.league_id = ?
    ORDER BY m.id ASC
");
$stmt->execute([$leagueId]);
$matches = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($league['name']) ?> - Fixtures</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-6">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">
            📅 Fixtures – <?= htmlspecialchars($league['name']) ?>
        </h1>

        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="p-3 border">Match</th>
                    <th class="p-3 border">Score</th>
                    <th class="p-3 border">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matches as $match): ?>
                    <tr class="text-center border-b hover:bg-gray-50">
                        <td class="p-3 border font-medium">
                            <?= htmlspecialchars($match['p1_name']) ?> vs <?= htmlspecialchars($match['p2_name']) ?>
                        </td>
                        <td class="p-3 border">
                            <?php
                                if ($match['player1_score'] !== null && $match['player2_score'] !== null) {
                                    echo "{$match['player1_score']} - {$match['player2_score']}";
                                } else {
                                    echo "Not Played";
                                }
                            ?>
                        </td>
                        <td class="p-3 border">
                            <?= ($match['player1_score'] === null || $match['player2_score'] === null) ? "❌ Not Played" : "✅ Played" ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">⬅️ Back to Home</a>
        </div>
    </div>
</body>
</html>
