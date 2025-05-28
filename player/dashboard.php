<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Get player info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Get joined leagues
$stmt = $pdo->prepare("
    SELECT l.* FROM leagues l
    JOIN league_player lp ON lp.league_id = l.id
    WHERE lp.player_id = ?
");
$stmt->execute([$userId]);
$joinedLeagues = $stmt->fetchAll();

// Get joined solo tournaments
$stmt = $pdo->prepare("
    SELECT t.* FROM tournaments t
    JOIN tournament_players tp ON tp.tournament_id = t.id
    WHERE tp.player_id = ?
");
$stmt->execute([$userId]);
$joinedTournaments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Player Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen px-4 py-6">

    <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            Welcome, <?php echo htmlspecialchars($user['username']); ?>!
        </h2>

        <!-- League Section -->
        <div class="space-y-3 mb-6">
            <a href="join_league.php" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                🔗 Join New League
            </a>
        </div>

        <h3 class="text-xl font-semibold text-gray-700 mb-3">My Leagues</h3>

        <?php if (count($joinedLeagues) > 0): ?>
            <ul class="space-y-4">
                <?php foreach ($joinedLeagues as $league): ?>
                    <li class="border p-4 rounded-lg bg-gray-50">
                        <div class="font-semibold text-lg text-gray-800">
                            <?php echo htmlspecialchars($league['name']); ?>
                        </div>
                        <div class="mt-2 space-x-4">
                            <a href="league_standings.php?league_id=<?php echo $league['id']; ?>"
                                class="text-blue-600 hover:underline text-sm">
                                📊 View Standings
                            </a>
                            <a href="matches.php" class="text-green-600 hover:underline text-sm">
                                ⚔️ Update Match Score
                            </a>
                            <a href="fixtures.php?league_id=<?php echo $league['id']; ?>" class="text-purple-600 hover:underline text-sm">
                                📅 View Fixtures
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-500">You haven’t joined any leagues yet.</p>
        <?php endif; ?>

        <!-- Solo Tournament Section -->
        <h3 class="text-xl font-semibold text-gray-700 mb-3 mt-8">🎯 My Solo Tournaments</h3>

        <div class="space-y-3 mb-4">
            <a href="join_tournament.php" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                ➕ Join Solo Tournament
            </a>
        </div>

        <?php if (count($joinedTournaments) > 0): ?>
            <ul class="space-y-4">
                <?php foreach ($joinedTournaments as $tournament): ?>
                    <li class="border p-4 rounded-lg bg-gray-50">
                        <div class="font-semibold text-lg text-gray-800">
                            <?php echo htmlspecialchars($tournament['name']); ?>
                        </div>
                        <div class="mt-2 space-x-4">
                            <a href="tournament_bracket.php?tournament_id=<?php echo $tournament['id']; ?>" class="text-purple-600 hover:underline text-sm">
                                📋 View Fixtures
                            </a>
                            <a href="my_tournament_matches.php" class="text-green-600 hover:underline text-sm">
                                ⚔️ Update My Match Scores
                            </a>
                            <a href="tournament_standings.php?tournament_id=<?php echo $tournament['id']; ?>" class="text-blue-600 hover:underline text-sm">
                                📊 View Standings
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-500">You're not registered in any solo tournaments yet.</p>
        <?php endif; ?>

        <div class="mt-6 text-center">
            <a href="logout.php" class="text-red-600 hover:underline">
                🚪 Logout
            </a>
        </div>
    </div>

</body>

</html>