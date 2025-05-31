<?php
require_once '../config/db.php';

$tournamentId = $_GET['tournament_id'] ?? null;

if (!$tournamentId) {
    die("Invalid tournament ID.");
}

// Fetch tournament info
$stmt = $pdo->prepare("SELECT * FROM tournaments WHERE id = ?");
$stmt->execute([$tournamentId]);
$tournament = $stmt->fetch();

if (!$tournament) {
    die("Tournament not found.");
}

// Define valid knockout round order
$validRoundsOrder = [
    "Final",
    "Semifinal",
    "Quarterfinal",
    "Round of 16",
    "Round of 32",
    "Round of 64"
];

// Fetch all matches
$stmt = $pdo->prepare("
    SELECT tm.*, 
           p1.username AS player1_name, 
           p2.username AS player2_name 
    FROM tournament_matches tm
    LEFT JOIN users p1 ON tm.player1_id = p1.id
    LEFT JOIN users p2 ON tm.player2_id = p2.id
    WHERE tm.tournament_id = ?
");
$stmt->execute([$tournamentId]);
$matches = $stmt->fetchAll();

// Group matches by valid round
$matchesByRound = [];
foreach ($matches as $match) {
    if (in_array($match['round'], $validRoundsOrder)) {
        $matchesByRound[$match['round']][] = $match;
    }
}

// Sort rounds based on predefined order
uksort($matchesByRound, function ($a, $b) use ($validRoundsOrder) {
    return array_search($a, $validRoundsOrder) - array_search($b, $validRoundsOrder);
});
?>

<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($tournament['name']); ?> - Public Bracket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-3xl font-bold mb-6 text-center"><?php echo htmlspecialchars($tournament['name']); ?> - Public Bracket</h1>

        <?php if ($tournament['status'] === 'completed'): ?>
            <p class="text-green-700 font-bold text-center mb-6">🏆 Tournament Completed!</p>
        <?php endif; ?>

        <?php foreach ($matchesByRound as $round => $matches): ?>
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4"><?php echo htmlspecialchars($round); ?></h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-2 px-4 border-b text-left">Player 1</th>
                                <th class="py-2 px-4 border-b text-left">Player 2</th>
                                <th class="py-2 px-4 border-b text-center">Score</th>
                                <th class="py-2 px-4 border-b text-left">Winner</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matches as $match): ?>
                                <tr class="border-t">
                                    <td class="py-2 px-4"><?php echo htmlspecialchars($match['player1_name'] ?? 'TBD'); ?></td>
                                    <td class="py-2 px-4"><?php echo htmlspecialchars($match['player2_name'] ?? 'TBD'); ?></td>
                                    <td class="py-2 px-4 text-center">
                                        <?php if ($match['player1_score'] !== null && $match['player2_score'] !== null): ?>
                                            <?php echo $match['player1_score'] . ' - ' . $match['player2_score']; ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-2 px-4 font-semibold text-green-700">
                                        <?php
                                        if ($match['player1_score'] !== null && $match['player2_score'] !== null) {
                                            if ($match['player1_score'] > $match['player2_score']) {
                                                echo htmlspecialchars($match['player1_name']);
                                            } elseif ($match['player2_score'] > $match['player1_score']) {
                                                echo htmlspecialchars($match['player2_name']);
                                            } else {
                                                echo 'Draw';
                                            }
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="mt-6 text-center">
            <a href="index.php" class="text-blue-600 hover:underline">⬅️ Back to Home</a>
        </div>
    </div>
</body>

</html>
