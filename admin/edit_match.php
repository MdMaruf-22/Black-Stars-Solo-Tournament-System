<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Match ID not specified.");
}

$matchId = $_GET['id'];

// Fetch match info
$stmt = $pdo->prepare("
    SELECT m.*, u1.username AS p1_name, u2.username AS p2_name 
    FROM matches m
    JOIN users u1 ON m.player1_id = u1.id
    JOIN users u2 ON m.player2_id = u2.id
    WHERE m.id = ?
");
$stmt->execute([$matchId]);
$match = $stmt->fetch();

if (!$match) {
    die("Match not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score1 = $_POST['player1_score'];
    $score2 = $_POST['player2_score'];

    $update = $pdo->prepare("UPDATE matches SET player1_score = ?, player2_score = ? WHERE id = ?");
    $update->execute([$score1, $score2, $matchId]);

    header("Location: manage_matches.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Match</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">✏️ Edit Match Result</h2>

        <form method="post" class="space-y-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    <?= htmlspecialchars($match['p1_name']) ?> Score
                </label>
                <input type="number" name="player1_score" value="<?= $match['player1_score'] ?>" required class="w-full p-2 border rounded">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    <?= htmlspecialchars($match['p2_name']) ?> Score
                </label>
                <input type="number" name="player2_score" value="<?= $match['player2_score'] ?>" required class="w-full p-2 border rounded">
            </div>

            <div class="flex justify-between items-center">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">💾 Save</button>
                <a href="manage_matches.php" class="text-blue-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
