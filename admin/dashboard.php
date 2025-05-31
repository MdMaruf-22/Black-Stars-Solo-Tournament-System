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
<body class="bg-gradient-to-br from-slate-100 to-slate-200 min-h-screen flex flex-col items-center justify-start p-6">

    <!-- Main Container -->
    <div class="w-full max-w-6xl bg-white rounded-3xl shadow-2xl p-8 mt-12">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-10">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-1">
                    🎮 Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                </h1>
                <p class="text-gray-500">Manage your club’s leagues and tournaments easily.</p>
            </div>
            <a href="logout.php" class="mt-4 md:mt-0 inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition shadow">
                🚪 Logout
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- League Management Section -->
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 shadow-md">
                <h2 class="text-2xl font-semibold text-blue-800 mb-6 flex items-center gap-2">📘 League Management</h2>
                <div class="grid grid-cols-1 gap-4">
                    <a href="create_league.php" class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-xl transition transform hover:-translate-y-1 shadow">
                        🆕 Create League
                    </a>
                    <a href="leagues.php" class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-xl transition transform hover:-translate-y-1 shadow">
                        📋 View All Leagues
                    </a>
                    <a href="start_league.php" class="bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-xl transition transform hover:-translate-y-1 shadow">
                        🏁 Start League & Generate Fixtures
                    </a>
                    <a href="manage_matches.php" class="bg-yellow-500 hover:bg-yellow-600 text-white p-4 rounded-xl transition transform hover:-translate-y-1 shadow">
                        🛠️ Manage Matches (Edit/Delete)
                    </a>
                </div>
            </div>

            <!-- Tournament Management Section -->
            <div class="bg-purple-50 border border-purple-200 rounded-2xl p-6 shadow-md">
                <h2 class="text-2xl font-semibold text-purple-800 mb-6 flex items-center gap-2">🏆 Tournament Management</h2>
                <div class="grid grid-cols-1 gap-4">
                    <a href="create_tournament.php" class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-xl transition transform hover:-translate-y-1 shadow">
                        ➕ Create Tournament
                    </a>
                    <a href="tournaments_list.php" class="bg-gray-600 hover:bg-gray-700 text-white p-4 rounded-xl transition transform hover:-translate-y-1 shadow">
                        📊 Manage Tournaments
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-10 text-sm text-gray-500 text-center">
        &copy; <?php echo date('Y'); ?> eFootball Club Admin Panel. All rights reserved.
    </footer>

</body>
</html>
