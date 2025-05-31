<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$errors = [];
$success = "";

// Fetch current user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Basic validation
    if (empty($username) || empty($email)) {
        $errors[] = "Username and email are required.";
    }

    // Password update (if filled)
    if (!empty($password)) {
        if ($password !== $confirm) {
            $errors[] = "Passwords do not match.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$username, $email, $hashedPassword, $userId]);
            $success = "Profile and password updated successfully.";
        }
    } elseif (empty($errors)) {
        // Update without password
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $userId]);
        $success = "Profile updated successfully.";
    }

    // Refresh data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-100 to-indigo-100 min-h-screen flex items-center justify-center px-4 py-10">

    <div class="bg-white p-8 rounded-xl shadow-lg max-w-lg w-full">
        <h1 class="text-3xl font-bold text-center text-indigo-700 mb-6">✏️ Edit Profile</h1>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
                <?php foreach ($errors as $e): ?>
                    <p>⚠️ <?php echo $e; ?></p>
                <?php endforeach; ?>
            </div>
        <?php elseif (!empty($success)): ?>
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">
                ✅ <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">New Password <span class="text-gray-400 text-sm">(leave blank to keep current)</span></label>
                <input type="password" name="password" class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" name="confirm" class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="view_profile.php" class="text-gray-600 hover:text-indigo-600 text-sm">🔙 Back to Profile</a>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition font-semibold">💾 Save Changes</button>
            </div>
        </form>
    </div>

</body>
</html>
