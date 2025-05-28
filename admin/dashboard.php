<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-start p-4">

    <!-- Header -->
    <div class="w-full max-w-3xl bg-white rounded-xl shadow-md p-6 mt-10">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">
            Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?> 🎮
        </h1>
        <p class="text-gray-500 mb-6">Manage your club leagues and tournaments from here.</p>

        <!-- Navigation Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- League Management -->
            <a href="create_league.php" class="flex items-center justify-center bg-blue-600 text-white font-semibold py-3 px-5 rounded-xl hover:bg-blue-700 transition">
                🆕 Create League
            </a>

            <a href="leagues.php" class="flex items-center justify-center bg-green-600 text-white font-semibold py-3 px-5 rounded-xl hover:bg-green-700 transition">
                📋 View All Leagues
            </a>

            <a href="start_league.php" class="flex items-center justify-center bg-indigo-600 text-white font-semibold py-3 px-5 rounded-xl hover:bg-indigo-700 transition">
                🏁 Start League & Generate Fixtures
            </a>

            <a href="manage_matches.php" class="flex items-center justify-center bg-yellow-600 text-white font-semibold py-3 px-5 rounded-xl hover:bg-yellow-700 transition">
                🛠️ Manage Matches (Edit/Delete)
            </a>

            <a href="create_tournament.php" class="flex items-center justify-center bg-purple-600 text-white font-semibold py-3 px-5 rounded-xl hover:bg-purple-700 transition">
                🏆 Create Tournament 
            </a>

            <!-- Coming Soon -->
            <a href="tournaments_list.php" class="flex items-center justify-center bg-gray-400 text-white font-semibold py-3 px-5 rounded-xl ">
                🏆 Manage Tournament
            </a>

            <!-- Logout -->
            <a href="logout.php" class="flex items-center justify-center bg-red-600 text-white font-semibold py-3 px-5 rounded-xl hover:bg-red-700 transition">
                🚪 Logout
            </a>
        </div>
    </div>

    <!-- Footer -->
    <p class="mt-6 text-sm text-gray-500">eFootball 2025 Club Admin Panel</p>

</body>
</html>
