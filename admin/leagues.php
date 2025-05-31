<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all leagues
$stmt = $pdo->query("SELECT * FROM leagues ORDER BY id DESC");
$leagues = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Leagues</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-4 flex flex-col items-center">

    <div class="w-full max-w-4xl bg-white p-6 rounded-xl shadow-lg mt-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">📋 All Leagues</h2>
        <div class="mb-4">
            <a href="dashboard.php" class="text-blue-500 hover:underline">⬅️ Back to Dashboard</a>
        </div>

        <?php if (count($leagues) === 0): ?>
            <p class="text-gray-600">No leagues created yet.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300 text-left">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th class="py-2 px-4 border">Name</th>
                            <th class="py-2 px-4 border">Status</th> <!-- New -->
                            <th class="py-2 px-4 border">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($leagues as $league): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="py-2 px-4 border"><?php echo htmlspecialchars($league['name']); ?></td>
                                <td class="py-2 px-4 border">
                                    <?php if ($league['status'] === 'completed'): ?>
                                        <span class="text-green-600 font-semibold">✅ Completed</span>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($league['status']); ?>
                                    <?php endif; ?>
                                </td>

                                <td class="py-2 px-4 border space-x-2">
                                    <a href="view_league.php?league_id=<?php echo $league['id']; ?>" class="text-blue-600 hover:underline">View</a>
                                    |
                                    <?php if ($league['status'] === 'upcoming'): ?>
                                        
                                        <a href="generate_fixtures.php?league_id=<?php echo $league['id']; ?>"
                                            class="text-red-600 hover:underline"
                                            onclick="return confirm('Generate fixtures for this league? This action cannot be undone!')">
                                            Generate Fixtures
                                        </a>
                                    <?php else: ?>
                                        
                                        <span class="text-gray-400 cursor-not-allowed">Fixtures Generated</span>
                                    <?php endif; ?>
                                    |
                                    <a href="delete_league.php?league_id=<?php echo $league['id']; ?>"
                                        class="text-red-500 hover:underline"
                                        onclick="return confirm('Are you sure you want to delete this league? This cannot be undone!')">
                                        Delete
                                    </a>
                                    <?php if ($league['status'] === 'ongoing'): ?>
                                        |
                                        <form action="complete_league.php" method="POST" class="inline" onsubmit="return confirm('Mark this league as completed?');">
                                            <input type="hidden" name="league_id" value="<?php echo $league['id']; ?>">
                                            <button type="submit" class="text-green-600 hover:underline">Mark as Completed</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>