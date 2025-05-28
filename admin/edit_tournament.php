<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Tournament ID is missing.";
    exit;
}

$tournamentId = $_GET['id'];

// Fetch tournament data
$stmt = $pdo->prepare("SELECT * FROM tournaments WHERE id = ?");
$stmt->execute([$tournamentId]);
$tournament = $stmt->fetch();

if (!$tournament) {
    echo "Tournament not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;

    if ($name) {
        $stmt = $pdo->prepare("UPDATE tournaments SET name = ?, description = ?, start_date = ?, end_date = ? WHERE id = ?");
        $stmt->execute([$name, $description, $start_date, $end_date, $tournamentId]);

        header("Location: tournaments_list.php");
        exit;
    } else {
        $error = "Tournament name is required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Tournament</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Edit Tournament</h1>

        <?php if (isset($error)): ?>
            <p class="text-red-600 mb-3"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label class="block mb-2 font-semibold">Tournament Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($tournament['name']); ?>" class="w-full p-2 border rounded mb-4" required>

            <label class="block mb-2 font-semibold">Description</label>
            <textarea name="description" class="w-full p-2 border rounded mb-4" rows="4"><?php echo htmlspecialchars($tournament['description'] ?? ''); ?></textarea>

            <label class="block mb-2 font-semibold">Start Date</label>
            <input type="date" name="start_date" value="<?php echo htmlspecialchars($tournament['start_date']); ?>" class="w-full p-2 border rounded mb-4">

            <label class="block mb-2 font-semibold">End Date</label>
            <input type="date" name="end_date" value="<?php echo htmlspecialchars($tournament['end_date']); ?>" class="w-full p-2 border rounded mb-4">

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Tournament</button>
        </form>

        <a href="tournaments_list.php" class="inline-block mt-4 text-blue-600 hover:underline">← Back to Tournaments</a>
    </div>
</body>
</html>
