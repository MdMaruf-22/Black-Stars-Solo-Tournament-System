<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if (!isset($_GET['league_id'])) {
    echo "League ID not specified.";
    exit;
}

$leagueId = $_GET['league_id'];
$playerId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT m.*, u1.username AS p1_name, u2.username AS p2_name, l.name AS league_name
    FROM matches m
    JOIN users u1 ON m.player1_id = u1.id
    JOIN users u2 ON m.player2_id = u2.id
    JOIN leagues l ON m.league_id = l.id
    WHERE (m.player1_id = ? OR m.player2_id = ?) AND m.league_id = ?
    ORDER BY m.id DESC
");
$stmt->execute([$playerId, $playerId, $leagueId]);

$matches = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Matches</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen py-10 px-4">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">⚽ My Matches</h2>
        <div class="mt-8 text-center">
            <a href="dashboard.php"
                class="text-blue-600 hover:underline text-sm">⬅ Back to Dashboard</a>
        </div>
        <div class="space-y-6">
            <?php if (count($matches) === 0): ?>
                <p class="text-center text-gray-600">You have no matches yet.</p>
            <?php endif; ?>

            <?php foreach ($matches as $match): ?>
                <div class="bg-white shadow-md rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-700">
                            <?php echo htmlspecialchars($match['league_name']); ?>
                        </h3>
                        <span class="text-sm text-gray-500">Match #<?php echo $match['id']; ?></span>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <div class="text-gray-700">
                            <span class="font-medium"><?php echo htmlspecialchars($match['p1_name']); ?></span>
                            vs
                            <span class="font-medium"><?php echo htmlspecialchars($match['p2_name']); ?></span>
                        </div>
                        <div class="text-lg font-bold text-blue-600">
                            <?php
                            if ($match['player1_score'] !== null && $match['player2_score'] !== null) {
                                echo $match['player1_score'] . " - " . $match['player2_score'];
                            } else {
                                echo "Not Played";
                            }
                            ?>
                        </div>
                    </div>

                    <div class="mt-4">
                        <?php if ($match['player1_score'] === null && $match['player2_score'] === null): ?>
                            <a href="submit_result.php?match_id=<?php echo $match['id']; ?>&league_id=<?php echo $leagueId; ?>"
                                class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200">
                                ✅ Submit Result
                            </a>
                        <?php else: ?>
                            <span class="text-sm text-gray-500">Result submitted</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-8 text-center">
            <a href="dashboard.php"
                class="text-blue-600 hover:underline text-sm">⬅ Back to Dashboard</a>
        </div>
    </div>
</body>

</html>