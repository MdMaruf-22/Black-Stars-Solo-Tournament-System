<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$playerId = $_SESSION['user_id'];
$message = "";

// Handle join request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['league_id'])) {
    $leagueId = $_POST['league_id'];

    // Check if already joined
    $stmt = $pdo->prepare("SELECT * FROM league_player WHERE league_id = ? AND player_id = ?");
    $stmt->execute([$leagueId, $playerId]);

    if ($stmt->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO league_player (league_id, player_id) VALUES (?, ?)");
        $stmt->execute([$leagueId, $playerId]);
        $message = "You have successfully joined the league.";
    } else {
        $message = "You already joined this league.";
    }
}

// Fetch all leagues
$stmt = $pdo->query("SELECT * FROM leagues WHERE status = 'upcoming' ORDER BY created_at DESC");
$leagues = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Join League</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen px-4 py-6">

    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Available Leagues</h2>

        <?php if ($message): ?>
            <div class="mb-4 p-4 text-green-800 bg-green-100 border border-green-300 rounded">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <ul class="space-y-6">
            <?php foreach ($leagues as $league): ?>
                <li class="border p-4 rounded-lg bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <?php echo htmlspecialchars($league['name']); ?>
                    </h3>
                    <p class="text-gray-600 mb-4">
                        <?php echo htmlspecialchars($league['description']); ?>
                    </p>

                    <form method="post" class="inline-block">
                        <input type="hidden" name="league_id" value="<?php echo $league['id']; ?>">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            Join League
                        </button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">
                ⬅️ Back to Dashboard
            </a>
        </div>
    </div>

</body>
</html>

