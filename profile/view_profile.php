<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-tr from-indigo-200 via-purple-100 to-pink-200 min-h-screen flex items-center justify-center">

    <div class="bg-white rounded-3xl shadow-2xl p-10 max-w-2xl w-full text-center relative overflow-hidden">
        <!-- Glowing Background Circle -->
        <div class="absolute -top-16 -right-16 w-60 h-60 bg-purple-300 opacity-30 rounded-full blur-3xl"></div>

        <!-- Avatar -->
        <div class="w-28 h-28 mx-auto mb-6 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-700 flex items-center justify-center text-white text-4xl font-bold shadow-md">
            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
        </div>

        <!-- User Info -->
        <h1 class="text-3xl font-extrabold text-gray-900"><?php echo htmlspecialchars($user['username']); ?></h1>
        <p class="text-gray-500 text-sm mb-8"><?php echo htmlspecialchars($user['email']); ?></p>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-5 text-sm mb-10">
            <div class="bg-indigo-100 p-4 rounded-xl shadow-sm">
                <p class="text-gray-600">🏟️ Matches</p>
                <p class="text-2xl font-bold text-indigo-800"><?php echo $user['matches_played']; ?></p>
            </div>
            <div class="bg-green-100 p-4 rounded-xl shadow-sm">
                <p class="text-gray-600">✅ Wins</p>
                <p class="text-2xl font-bold text-green-800"><?php echo $user['wins']; ?></p>
            </div>
            <div class="bg-red-100 p-4 rounded-xl shadow-sm">
                <p class="text-gray-600">❌ Losses</p>
                <p class="text-2xl font-bold text-red-700"><?php echo $user['losses']; ?></p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-xl shadow-sm">
                <p class="text-gray-600">🤝 Draws</p>
                <p class="text-2xl font-bold text-yellow-800"><?php echo $user['draws']; ?></p>
            </div>
            <div class="bg-pink-100 p-4 rounded-xl shadow-sm">
                <p class="text-gray-600">🥅 Goals Scored</p>
                <p class="text-2xl font-bold text-pink-800"><?php echo $user['goals_scored']; ?></p>
            </div>
            <div class="bg-gray-200 p-4 rounded-xl shadow-sm">
                <p class="text-gray-600">🛡️ Goals Conceded</p>
                <p class="text-2xl font-bold text-gray-700"><?php echo $user['goals_conceded']; ?></p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap justify-center gap-4">
            <a href="../player/dashboard.php" class="px-5 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition font-medium shadow">
                🔙 Dashboard
            </a>
            <a href="edit_profile.php" class="px-5 py-2 bg-gray-700 text-white rounded-full hover:bg-gray-800 transition font-medium shadow">
                ✏️ Edit Profile
            </a>
        </div>
    </div>

</body>

</html>
