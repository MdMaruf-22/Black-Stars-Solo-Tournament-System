<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);

    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO leagues (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);
        $message = "✅ League created successfully!";
    } else {
        $message = "⚠️ League name is required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create League</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4 text-center text-gray-800">🆕 Create New League</h2>

        <?php if (!empty($message)): ?>
            <div class="mb-4 text-sm text-white px-4 py-2 rounded-lg 
                        <?php echo strpos($message, 'successfully') !== false ? 'bg-green-600' : 'bg-red-500'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <div>
                <label class="block text-gray-700 font-medium">League Name</label>
                <input type="text" name="name" required class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-gray-700 font-medium">Description</label>
                <textarea name="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                ➕ Create League
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="dashboard.php" class="text-blue-500 hover:underline">⬅️ Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
