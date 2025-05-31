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

$userId = $_SESSION['user_id'];
$tournamentId = $_GET['tournament_id'];

$stmt = $pdo->prepare("
    SELECT m.*, t.name AS tournament_name, u1.username AS p1_name, u2.username AS p2_name
    FROM tournament_matches m
    JOIN tournaments t ON m.tournament_id = t.id
    JOIN users u1 ON m.player1_id = u1.id
    JOIN users u2 ON m.player2_id = u2.id
    WHERE (m.player1_id = ? OR m.player2_id = ?)
      AND m.tournament_id = ?
    ORDER BY m.round ASC
");
$stmt->execute([$userId, $userId, $tournamentId]);
$matches = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Tournament Matches</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-6 px-4">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">🎯 My Solo Tournament Matches</h2>

        <?php if (count($matches) > 0): ?>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2 text-left">Tournament</th>
                        <th class="p-2 text-left">Match</th>
                        <th class="p-2 text-left">Round</th>
                        <th class="p-2 text-left">Status</th>
                        <th class="p-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $match): ?>
                        <tr class="border-b">
                            <td class="p-2"><?php echo htmlspecialchars($match['tournament_name']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($match['p1_name']) . " vs " . htmlspecialchars($match['p2_name']); ?></td>
                            <td class="p-2"><?php echo ucfirst($match['round']); ?></td>
                            <td class="p-2">
                                <?php if (is_null($match['player1_score']) && is_null($match['player2_score'])): ?>
                                    ❌ Not Played
                                <?php else: ?>
                                    ✅ <?php echo $match['player1_score'] . " - " . $match['player2_score']; ?>
                                <?php endif; ?>
                            </td>
                            <td class="p-2">
                                <?php if (is_null($match['player1_score']) && is_null($match['player2_score'])): ?>
                                    <a href="update_tournament_match.php?match_id=<?php echo $match['id']; ?>" class="text-green-600 hover:underline text-sm">
                                        ⚔️ Update Score
                                    </a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-500">You have no tournament matches yet.</p>
        <?php endif; ?>

        <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">⬅️ Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
