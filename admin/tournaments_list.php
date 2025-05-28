<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all tournaments
$stmt = $pdo->query("SELECT * FROM tournaments ORDER BY created_at DESC");
$tournaments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Solo Tournaments</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Manage Solo Tournaments</h1>

        <a href="create_tournament.php" class="inline-block mb-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Create New Tournament</a>

        <?php if (count($tournaments) > 0): ?>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-2">Name</th>
                        <th class="border p-2">Description</th>
                        <th class="border p-2">Status</th>
                        <th class="border p-2">Start Date</th>
                        <th class="border p-2">End Date</th>
                        <th class="border p-2">Actions</th>
                        <th class="border p-2">Registered Players</th> <!-- NEW COLUMN -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tournaments as $t): ?>
                        <tr class="text-center border-b">
                            <td class="p-2"><?php echo htmlspecialchars($t['name']); ?></td>
                            <td class="p-2 text-left"><?php echo nl2br(htmlspecialchars($t['description'] ?? '')); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($t['status']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($t['start_date']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($t['end_date']); ?></td>
                            <td class="p-2">
                                <div class="flex flex-wrap justify-center gap-2">
                                    <?php if ($t['status'] === 'registration'): ?>
                                        <!-- Start Tournament -->
                                        <form action="start_tournament.php" method="POST" onsubmit="return confirm('Are you sure you want to start this tournament? Once started, no new registrations allowed.');">
                                            <input type="hidden" name="tournament_id" value="<?php echo $t['id']; ?>">
                                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                                Start
                                            </button>
                                        </form>

                                        <!-- Review Requests -->
                                        <a href="admin_tournament_requests.php?tournament_id=<?php echo $t['id']; ?>"
                                            class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                            See Requests
                                        </a>
                                    <?php endif; ?>

                                    <!-- Edit Tournament -->
                                    <a href="edit_tournament.php?id=<?php echo $t['id']; ?>"
                                        class="bg-purple-500 text-white px-3 py-1 rounded hover:bg-purple-600">
                                        Edit
                                    </a>

                                    <!-- Delete Tournament -->
                                    <form action="delete_tournament.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this tournament?');">
                                        <input type="hidden" name="tournament_id" value="<?php echo $t['id']; ?>">
                                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>

                            <td class="p-2">
                                <a href="view_registered_players.php?tournament_id=<?php echo $t['id']; ?>" class="text-blue-600 hover:underline">
                                    View Players
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>


        <?php else: ?>
            <p>No tournaments found.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="inline-block mt-4 text-blue-600 hover:underline">← Back to Dashboard</a>
    </div>
</body>

</html>