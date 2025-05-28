<?php
session_start();
require_once '../config/db.php'; // This should define $pdo (PDO object)

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all matches with league and player info using PDO
$stmt = $pdo->prepare("
    SELECT 
        m.id, m.league_id, m.player1_id, m.player2_id, m.player1_score, m.player2_score,
        u1.username AS player1_name,
        u2.username AS player2_name,
        l.name AS league_name
    FROM matches m
    JOIN users u1 ON m.player1_id = u1.id
    JOIN users u2 ON m.player2_id = u2.id
    JOIN leagues l ON m.league_id = l.id
    ORDER BY m.league_id, m.id
");
$stmt->execute();
$matches = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Matches</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow-lg">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">⚙️ Manage All Matches</h1>

        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="p-3 border">League</th>
                    <th class="p-3 border">Player 1</th>
                    <th class="p-3 border">Player 2</th>
                    <th class="p-3 border">Score</th>
                    <th class="p-3 border">Status</th>
                    <th class="p-3 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matches as $row): ?>
                    <tr class="text-center border-b hover:bg-gray-50">
                        <td class="p-3 border"><?= htmlspecialchars($row['league_name']) ?></td>
                        <td class="p-3 border"><?= htmlspecialchars($row['player1_name']) ?></td>
                        <td class="p-3 border"><?= htmlspecialchars($row['player2_name']) ?></td>
                        <td class="p-3 border">
                            <?= ($row['player1_score'] !== null && $row['player2_score'] !== null)
                                ? "{$row['player1_score']} - {$row['player2_score']}"
                                : "Not Played" ?>
                        </td>
                        <td class="p-3 border">
                            <?= ($row['player1_score'] === null) ? "❌ Pending" : "✅ Played" ?>
                        </td>
                        <td class="p-3 border space-x-2">
                            <a href="edit_match.php?id=<?= $row['id'] ?>" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Edit</a>
                            <a href="delete_match.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?');" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-6 text-center">
            <a href="admin_dashboard.php" class="text-blue-600 hover:underline">⬅️ Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
