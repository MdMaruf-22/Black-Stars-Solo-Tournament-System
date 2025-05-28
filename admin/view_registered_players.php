<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['tournament_id'])) {
    echo "Tournament ID is missing.";
    exit;
}

$tournamentId = $_GET['tournament_id'];

// Fetch tournament info
$stmt = $pdo->prepare("SELECT name FROM tournaments WHERE id = ?");
$stmt->execute([$tournamentId]);
$tournament = $stmt->fetch();

if (!$tournament) {
    echo "Tournament not found.";
    exit;
}

// Fetch registered players
$stmt = $pdo->prepare("
    SELECT p.username, p.email 
    FROM users p
    JOIN tournament_players tp ON tp.player_id = p.id
    WHERE tp.tournament_id = ?
");
$stmt->execute([$tournamentId]);
$players = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registered Players</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">🎮 Registered Players - <?php echo htmlspecialchars($tournament['name']); ?></h1>

        <?php if (count($players) > 0): ?>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-2 text-left">Username</th>
                        <th class="border p-2 text-left">Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($players as $player): ?>
                        <tr class="border-b">
                            <td class="p-2"><?php echo htmlspecialchars($player['username']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($player['email']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-600">No players have joined this tournament yet.</p>
        <?php endif; ?>

        <div class="mt-4">
            <a href="tournaments_list.php" class="text-blue-600 hover:underline">← Back to Tournaments</a>
        </div>
    </div>
</body>
</html>
