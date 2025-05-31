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
    WHERE lp.player_id = ? AND l.status != 'completed'
");
$stmt->execute([$userId]);
$joinedLeagues = $stmt->fetchAll();

// Get joined tournaments
$stmt = $pdo->prepare("
    SELECT t.* FROM tournaments t
    JOIN tournament_players tp ON tp.tournament_id = t.id
    WHERE tp.player_id = ? AND t.status != 'completed'
");
$stmt->execute([$userId]);
$joinedTournaments = $stmt->fetchAll();
?>

<!-- No changes to PHP part -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Player Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen p-4 sm:p-6">
    <div class="max-w-6xl mx-auto">

        <!-- Header -->
        <div class="bg-white rounded-xl shadow-md px-4 sm:px-6 py-4 sm:py-5 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">🎮 Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
                <p class="text-sm text-gray-500">Manage your leagues and tournaments below</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="../profile/view_profile.php" class="text-blue-600 hover:text-blue-800 font-medium text-sm">👤 View Profile</a>
                <a href="logout.php" class="text-red-600 hover:text-red-800 font-medium text-sm">🚪 Logout</a>
            </div>
        </div>

        <!-- Leagues Section -->
        <section class="bg-white rounded-xl shadow p-4 sm:p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-700">🏆 My Leagues</h2>
                <div class="flex flex-wrap gap-2">
                    <a href="join_league.php" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">🔗 Join League</a>
                    <a href="completed_leagues.php" class="px-4 py-2 bg-gray-500 text-white rounded-md text-sm hover:bg-gray-600">Past Leagues</a>
                </div>
            </div>

            <?php if (count($joinedLeagues) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($joinedLeagues as $league): ?>
                        <div class="border p-4 rounded-lg bg-gray-50 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($league['name']); ?></h3>
                            <div class="mt-2 flex flex-wrap gap-3 text-sm text-gray-600">
                                <a href="league_standings.php?league_id=<?php echo $league['id']; ?>" class="text-blue-600 hover:underline">📊 Standings</a>
                                <a href="matches.php?league_id=<?php echo $league['id']; ?>" class="text-green-600 hover:underline">⚔️ Update Match</a>
                                <a href="fixtures.php?league_id=<?php echo $league['id']; ?>" class="text-purple-600 hover:underline">📅 Fixtures</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-sm text-center mt-4">You haven't joined any leagues yet.</p>
            <?php endif; ?>
        </section>

        <!-- Solo Tournaments Section -->
        <section class="bg-white rounded-xl shadow p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-700">🎯 My Solo Tournaments</h2>
                <div class="flex flex-wrap gap-2">
                    <a href="join_tournament.php" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">➕ Join Tournament</a>
                    <a href="completed_tournaments.php" class="px-4 py-2 bg-gray-500 text-white rounded-md text-sm hover:bg-gray-600">Past Tournaments</a>
                </div>
            </div>

            <?php if (count($joinedTournaments) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($joinedTournaments as $tournament): ?>
                        <div class="border p-4 rounded-lg bg-gray-50 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($tournament['name']); ?></h3>
                            <div class="mt-2 flex flex-wrap gap-3 text-sm text-gray-600">
                                <a href="tournament_bracket.php?tournament_id=<?php echo $tournament['id']; ?>" class="text-purple-600 hover:underline">📋 Fixtures</a>
                                <a href="my_tournament_matches.php?tournament_id=<?php echo $tournament['id']; ?>" class="text-green-600 hover:underline">⚔️ My Matches</a>
                                <a href="tournament_standings.php?tournament_id=<?php echo $tournament['id']; ?>" class="text-blue-600 hover:underline">📊 Standings</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-sm text-center mt-4">You're not registered in any tournaments yet.</p>
            <?php endif; ?>
        </section>

    </div>
</body>
</html>

