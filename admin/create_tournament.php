<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;

    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO tournaments (name, description, start_date, end_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $description, $start_date, $end_date]);
        header('Location: tournaments_list.php');
        exit;
    } else {
        $error = "Tournament name is required.";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Create Tournament</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Create New Solo Tournament</h1>

        <?php if (isset($error)): ?>
            <p class="text-red-600 mb-3"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label class="block mb-2 font-semibold">Tournament Name</label>
            <input type="text" name="name" class="w-full p-2 border rounded mb-4" required>

            <label class="block mb-2 font-semibold">Description</label>
            <textarea name="description" rows="4" class="w-full p-2 border rounded mb-4"></textarea>

            <label class="block mb-2 font-semibold">Start Date</label>
            <input type="date" name="start_date" class="w-full p-2 border rounded mb-4">

            <label class="block mb-4 font-semibold">End Date</label>
            <input type="date" name="end_date" class="w-full p-2 border rounded mb-4">

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create Tournament</button>
        </form>

        <a href="tournaments_list.php" class="inline-block mt-4 text-blue-600 hover:underline">← Back to Tournaments List</a>
    </div>
</body>

</html>