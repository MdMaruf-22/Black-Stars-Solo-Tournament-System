<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
    exit;
} elseif (isset($_SESSION['user_id'])) {
    header("Location: player/dashboard.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM tournaments WHERE status = 'ongoing' ORDER BY created_at DESC");
$stmt->execute();
$tournaments = $stmt->fetchAll();

$leagueStmt = $pdo->prepare("SELECT * FROM leagues WHERE status = 'ongoing' ORDER BY created_at DESC");
$leagueStmt->execute();
$leagues = $leagueStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Black Stars Solo Tournament System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen text-gray-800">

    <!-- Wrapper -->
    <div class="max-w-5xl mx-auto px-4 py-12">

        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-extrabold text-gray-900">🎮 Black Stars Tournaments System</h1>
            <p class="mt-4 text-lg text-gray-600">Select your role and explore active events below.</p>
        </div>

        <!-- Role Buttons -->
        <div class="flex justify-center gap-6 mb-16">
            <a href="admin/login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-lg font-semibold shadow-md transition">
                🔐 Admin Login
            </a>
            <a href="player/login.php" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg text-lg font-semibold shadow-md transition">
                👤 Player Login
            </a>
        </div>

        <!-- Tournaments Section -->
        <section class="mb-16 bg-white p-8 rounded-xl shadow-md">
            <h2 class="text-3xl font-bold text-center mb-6">🗓️ Ongoing Tournaments</h2>

            <?php if (count($tournaments) > 0): ?>
                <div class="grid gap-6">
                    <?php foreach ($tournaments as $t): ?>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 hover:shadow transition">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                                <div>
                                    <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($t['name']); ?></h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Status:
                                        <span class="inline-block px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs font-medium">
                                            <?php echo ucfirst($t['status']); ?>
                                        </span>
                                    </p>
                                </div>
                                <a href="../public/tournament_bracket.php?tournament_id=<?php echo $t['id']; ?>" class="text-blue-600 hover:underline font-medium">
                                    📊 View Fixtures & Standings
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-500 text-md">No ongoing or upcoming tournaments at the moment.</p>
            <?php endif; ?>
        </section>

        <!-- Leagues Section -->
        <section class="bg-white p-8 rounded-xl shadow-md">
            <h2 class="text-3xl font-bold text-center mb-6">🏆 Ongoing Leagues</h2>

            <?php if (count($leagues) > 0): ?>
                <div class="grid gap-6">
                    <?php foreach ($leagues as $l): ?>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 hover:shadow transition">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                                <div>
                                    <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($l['name']); ?></h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Status:
                                        <span class="inline-block px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-medium">
                                            <?php echo ucfirst($l['status']); ?>
                                        </span>
                                    </p>
                                </div>
                                <a href="../public/league_view.php?league_id=<?php echo $l['id']; ?>" class="text-yellow-600 hover:underline font-medium">
                                    📅 View League Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-500 text-md">No ongoing or upcoming leagues at the moment.</p>
            <?php endif; ?>
        </section>

    </div>
</body>
</html>
