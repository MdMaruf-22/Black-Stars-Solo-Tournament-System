<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch all tournaments that are not finished
$stmt = $pdo->prepare("
    SELECT t.*, 
        EXISTS (
            SELECT 1 FROM tournament_players tp 
            WHERE tp.tournament_id = t.id AND tp.player_id = ?
        ) AS joined,
        EXISTS (
            SELECT 1 FROM tournament_join_requests r 
            WHERE r.tournament_id = t.id AND r.player_id = ?
        ) AS requested
    FROM tournaments t
    WHERE t.status = 'registration'
");
$stmt->execute([$userId, $userId]);
$tournaments = $stmt->fetchAll();

// Handle join request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tournament_id'], $_POST['fee_code'])) {
    $tournamentId = $_POST['tournament_id'];
    $feeCode = trim($_POST['fee_code']);

    if (strlen($feeCode) !== 3 || !ctype_digit($feeCode)) {
        $error = "Fee code must be exactly 3 digits.";
    } else {
        // Check if already requested or joined
        $stmt = $pdo->prepare("SELECT 1 FROM tournament_join_requests WHERE tournament_id = ? AND player_id = ?");
        $stmt->execute([$tournamentId, $userId]);

        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO tournament_join_requests (tournament_id, player_id, fee_code) VALUES (?, ?, ?)");
            $stmt->execute([$tournamentId, $userId, $feeCode]);
            header("Location: join_tournament.php?requested=1");
            exit;
        } else {
            $error = "You’ve already requested to join this tournament.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Join Solo Tournaments</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 py-6 px-4">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">🎯 Available Solo Tournaments</h1>

        <?php if (isset($_GET['requested'])): ?>
            <div class="mb-4 p-3 bg-yellow-100 text-yellow-700 rounded">Your join request has been sent for approval!</div>
        <?php elseif (isset($error)): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (count($tournaments) > 0): ?>
            <ul class="space-y-4">
                <?php foreach ($tournaments as $t): ?>
                    <li class="border p-4 rounded bg-gray-50">
                        <div class="font-semibold text-lg text-gray-800"><?= htmlspecialchars($t['name']) ?></div>
                        <?php if (!empty($t['description'])): ?>
                            <div class="font-semibold text-lg text-gray-800"><?= nl2br(htmlspecialchars($t['description'])) ?></div>
                        <?php endif; ?>
                        <div class="text-sm text-gray-600 mb-1">Status: <?= ucfirst($t['status']) ?></div>

                        <?php if ($t['joined']): ?>
                            <span class="text-green-600 text-sm font-medium">✅ Already Joined</span>
                        <?php elseif ($t['requested']): ?>
                            <span class="text-yellow-600 text-sm font-medium">⌛ Request Pending</span>
                        <?php else: ?>
                            <form method="POST" class="mt-2 flex items-center space-x-2">
                                <input type="hidden" name="tournament_id" value="<?= $t['id'] ?>">
                                <input type="text" name="fee_code" maxlength="3" pattern="\d{3}" required placeholder="Fee code (3 digits)" class="border p-1 rounded w-28 text-sm">
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-1 rounded hover:bg-indigo-700">➕ Request Join</button>
                            </form>
                        <?php endif; ?>
                    </li>

                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-500">No active tournaments available at the moment.</p>
        <?php endif; ?>

        <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">⬅ Back to Dashboard</a>
        </div>
    </div>
</body>

</html>