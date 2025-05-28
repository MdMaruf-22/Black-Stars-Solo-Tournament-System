<?php
session_start();
require_once 'config/db.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
    exit;
} elseif (isset($_SESSION['user_id'])) {
    header("Location: player/dashboard.php");
    exit;
}

// Fetch ongoing and registration tournaments
$stmt = $pdo->prepare("SELECT * FROM tournaments WHERE status != 'finished' ORDER BY created_at DESC");
$stmt->execute();
$tournaments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Black Stars Solo Tournament System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-gray-100 to-gray-200 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-4xl bg-white rounded-xl shadow-xl p-8">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-800 mb-2">🎮 Black Stars Solo Tournament System</h1>
            <p class="text-gray-600 text-lg">Choose your role and explore ongoing tournaments.</p>
        </div>

        <!-- Role Selection -->
        <div class="flex flex-col sm:flex-row justify-center items-center gap-6 mb-12">
            <a href="admin/login.php" class="bg-blue-600 hover:bg-blue-700 transition text-white px-8 py-3 rounded-lg text-lg font-semibold shadow">
                🔐 Admin Login
            </a>
            <a href="player/login.php" class="bg-green-600 hover:bg-green-700 transition text-white px-8 py-3 rounded-lg text-lg font-semibold shadow">
                👤 Player Login
            </a>
        </div>

        <!-- Tournaments -->
        <div>
            <h2 class="text-2xl font-bold text-gray-700 mb-6 text-center">🗓️ Current Tournaments</h2>
            
            <?php if (count($tournaments) > 0): ?>
                <div class="grid gap-6">
                    <?php foreach ($tournaments as $t): ?>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 hover:shadow-md transition">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($t['name']); ?></h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Status: 
                                        <span class="inline-block px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs font-medium">
                                            <?php echo ucfirst($t['status']); ?>
                                        </span>
                                    </p>
                                </div>
                                <a href="tournament_bracket.php?tournament_id=<?php echo $t['id']; ?>" class="text-blue-600 hover:underline font-medium mt-2 sm:mt-0">
                                    📊 View Fixtures & Standings
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-500 text-md mt-4">No ongoing or upcoming tournaments at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
