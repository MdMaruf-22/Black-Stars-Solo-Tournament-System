<?php
require_once 'config/db.php';

$tournamentId = $_GET['tournament_id'] ?? null;

if (!$tournamentId) {
    die("Tournament ID not specified.");
}

// Fetch tournament info
$stmt = $pdo->prepare("SELECT * FROM tournaments WHERE id = ?");
$stmt->execute([$tournamentId]);
$tournament = $stmt->fetch();

if (!$tournament) {
    die("Tournament not found.");
}

// Fetch matches
$stmt = $pdo->prepare("
    SELECT m.*, 
           p1.username AS player1_name, 
           p2.username AS player2_name, 
           w.username AS winner_name
    FROM tournament_matches m
    LEFT JOIN users p1 ON m.player1_id = p1.id
    LEFT JOIN users p2 ON m.player2_id = p2.id
    LEFT JOIN users w ON m.winner_id = w.id
    WHERE m.tournament_id = ?
    ORDER BY FIELD(round, 'Final', 'Semifinal', 'Quarterfinal', 'Round of 16', 'Round of 32', 'Round of 64'), m.id
");
$stmt->execute([$tournamentId]);
$matches = $stmt->fetchAll();

// Fetch standings (win count per player)
$stmt = $pdo->prepare("
    SELECT p.username, COUNT(*) AS wins
    FROM tournament_matches m
    JOIN users p ON m.winner_id = p.id
    WHERE m.tournament_id = ?
    GROUP BY p.username
    ORDER BY wins DESC
");
$stmt->execute([$tournamentId]);
$standings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($tournament['name']); ?> - Fixtures & Standings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">
            📅 <?php echo htmlspecialchars($tournament['name']); ?> - Fixtures & Standings
        </h1>

        <!-- Fixture Table -->
        <h2 class="text-xl font-semibold text-gray-700 mb-3">Match Fixtures</h2>
        <div class="overflow-x-auto mb-8">
            <table class="min-w-full bg-white border">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 border">Round</th>
                        <th class="px-4 py-2 border">Player 1</th>
                        <th class="px-4 py-2 border">Player 2</th>
                        <th class="px-4 py-2 border">Winner</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $match): ?>
                        <tr class="text-center border-b">
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($match['round']); ?></td>
                            <td class="px-4 py-2 border"><?php echo $match['player1_name'] ?? 'BYE'; ?></td>
                            <td class="px-4 py-2 border"><?php echo $match['player2_name'] ?? 'BYE'; ?></td>
                            <td class="px-4 py-2 border">
                                <?php echo $match['winner_name'] ?? '<span class="text-gray-400 italic">Pending</span>'; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Standings Table -->
        <h2 class="text-xl font-semibold text-gray-700 mb-3">🏆 Standings</h2>
        <?php if (count($standings) > 0): ?>
            <table class="min-w-full bg-white border">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 border">Player</th>
                        <th class="px-4 py-2 border">Wins</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($standings as $row): ?>
                        <tr class="text-center border-b">
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="px-4 py-2 border"><?php echo $row['wins']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-500">No matches have been won yet.</p>
        <?php endif; ?>

        <div class="mt-6 text-center">
            <a href="index.php" class="text-blue-600 hover:underline">⬅ Back to Home</a>
        </div>
    </div>
</body>
</html>
