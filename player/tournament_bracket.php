<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['tournament_id'])) {
    echo "Tournament ID is missing.";
    exit;
}

$tournamentId = $_GET['tournament_id'];

// Get tournament name
$stmt = $pdo->prepare("SELECT name FROM tournaments WHERE id = ?");
$stmt->execute([$tournamentId]);
$tournament = $stmt->fetch();

if (!$tournament) {
    echo "Tournament not found.";
    exit;
}

// Get matches grouped by round
$stmt = $pdo->prepare("
    SELECT 
        m.*, 
        u1.username AS player1_name, 
        u2.username AS player2_name 
    FROM tournament_matches m
    JOIN users u1 ON m.player1_id = u1.id
    JOIN users u2 ON m.player2_id = u2.id
    WHERE m.tournament_id = ?
    ORDER BY m.round, m.id
");
$stmt->execute([$tournamentId]);
$matches = $stmt->fetchAll();

$grouped = [];
foreach ($matches as $match) {
    $grouped[$match['round']][] = $match;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($tournament['name']); ?> - Bracket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-6 px-4">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">
            🏆 <?php echo htmlspecialchars($tournament['name']); ?> – Bracket
        </h1>

        <?php if (count($grouped) > 0): ?>
            <?php foreach ($grouped as $round => $roundMatches): ?>
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-indigo-700 mb-2">
                        🔁 <?php echo ucfirst($round); ?>
                    </h2>
                    <ul class="space-y-3">
                        <?php foreach ($roundMatches as $m): ?>
                            <li class="border p-3 rounded bg-gray-50 flex justify-between items-center">
                                <div>
                                    <?php echo htmlspecialchars($m['player1_name']); ?> 
                                    <span class="text-gray-500">vs</span> 
                                    <?php echo htmlspecialchars($m['player2_name']); ?>
                                </div>
                                <div>
                                    <?php if ($m['player1_score'] !== null && $m['player2_score'] !== null): ?>
                                        <span class="text-green-700 font-semibold">
                                            <?php echo $m['player1_score'] . " - " . $m['player2_score']; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-red-600 text-sm">⏳ Not Played</span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-600">No matches yet.</p>
        <?php endif; ?>

        <div class="text-center mt-6">
            <a href="dashboard.php" class="text-blue-600 hover:underline">
                ⬅️ Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
