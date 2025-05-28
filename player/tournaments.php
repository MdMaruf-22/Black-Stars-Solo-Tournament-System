<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Get all tournaments with status 'registration'
$stmt = $pdo->prepare("SELECT * FROM tournaments WHERE status = 'registration'");
$stmt->execute();
$tournaments = $stmt->fetchAll();

// Get tournaments player already registered for
$stmt2 = $pdo->prepare("SELECT tournament_id FROM tournament_players WHERE player_id = ?");
$stmt2->execute([$userId]);
$registeredTournaments = $stmt2->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Solo Tournaments</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Available Solo Tournaments</h1>

        <?php if (count($tournaments) > 0): ?>
            <ul>
            <?php foreach ($tournaments as $t): ?>
                <li class="mb-3 p-3 border rounded">
                    <div class="font-semibold"><?php echo htmlspecialchars($t['name']); ?></div>
                    <div>Status: <?php echo htmlspecialchars($t['status']); ?></div>

                    <?php if (in_array($t['id'], $registeredTournaments)): ?>
                        <div class="text-green-600 font-semibold mt-1">Already Registered</div>
                        <a href="tournament_bracket.php?tournament_id=<?php echo $t['id']; ?>" class="text-blue-600 hover:underline text-sm">View Bracket</a>
                    <?php else: ?>
                        <form method="POST" action="register_tournament.php" class="mt-2">
                            <input type="hidden" name="tournament_id" value="<?php echo $t['id']; ?>">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Register</button>
                        </form>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No tournaments open for registration currently.</p>
        <?php endif; ?>

        <a href="player_dashboard.php" class="mt-4 inline-block text-blue-600 hover:underline">← Back to Dashboard</a>
    </div>
</body>
</html>
