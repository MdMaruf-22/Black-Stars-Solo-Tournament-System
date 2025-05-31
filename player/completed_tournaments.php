<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Get completed solo tournaments the user joined with winner name from Final round
$stmt = $pdo->prepare("
    SELECT t.*, 
           CASE 
               WHEN tm.player1_score > tm.player2_score THEN u1.username
               WHEN tm.player2_score > tm.player1_score THEN u2.username
               ELSE 'Draw'
           END AS winner_name
    FROM tournaments t
    JOIN tournament_players tp ON tp.tournament_id = t.id
    LEFT JOIN tournament_matches tm ON tm.tournament_id = t.id AND tm.round = 'Final'
    LEFT JOIN users u1 ON tm.player1_id = u1.id
    LEFT JOIN users u2 ON tm.player2_id = u2.id
    WHERE tp.player_id = ? AND t.status = 'completed'
    GROUP BY t.id
");
$stmt->execute([$userId]);
$completedTournaments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Completed Tournaments</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8 px-4">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">🏁 Completed Solo Tournaments</h2>
        <?php if (count($completedTournaments) > 0): ?>
            <ul class="space-y-4">
                <?php foreach ($completedTournaments as $tournament): ?>
                    <li class="p-4 border bg-gray-50 rounded">
                        <div class="font-semibold text-lg text-gray-700">
                            <?php echo htmlspecialchars($tournament['name']); ?>
                        </div>

                        <?php if (!empty($tournament['winner_name'])): ?>
                            <div class="text-sm text-green-700 mt-1">
                                🏆 Winner: <strong><?php echo htmlspecialchars($tournament['winner_name']); ?></strong>
                            </div>
                        <?php else: ?>
                            <div class="text-sm text-gray-500 mt-1">
                                🏆 Winner: Not determined
                            </div>
                        <?php endif; ?>

                        <div class="mt-2 space-x-4">
                            <a href="tournament_standings.php?tournament_id=<?php echo $tournament['id']; ?>" class="text-blue-600 hover:underline text-sm">
                                📊 Final Standings
                            </a>
                            <a href="tournament_bracket.php?tournament_id=<?php echo $tournament['id']; ?>" class="text-purple-600 hover:underline text-sm">
                                📋 Bracket
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-500">You haven't completed any tournaments yet.</p>
        <?php endif; ?>
        <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">⬅ Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
