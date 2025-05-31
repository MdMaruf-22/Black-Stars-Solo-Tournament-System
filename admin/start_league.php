<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// Get leagues
$stmt = $pdo->query("SELECT * FROM leagues WHERE status = 'upcoming' ORDER BY created_at DESC");
$leagues = $stmt->fetchAll();

// Handle fixture generation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['league_id'])) {
    $leagueId = $_POST['league_id'];

    // Get all players in the league
    $stmt = $pdo->prepare("SELECT player_id FROM league_player WHERE league_id = ?");
    $stmt->execute([$leagueId]);
    $players = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($players) < 2) {
        $message = "Need at least 2 players to generate fixtures.";
    } else {
        // Generate fixtures (each player plays all others once)
        for ($i = 0; $i < count($players); $i++) {
            for ($j = $i + 1; $j < count($players); $j++) {
                $p1 = $players[$i];
                $p2 = $players[$j];

                $stmt = $pdo->prepare("INSERT INTO matches (league_id, player1_id, player2_id) VALUES (?, ?, ?)");
                $stmt->execute([$leagueId, $p1, $p2]);
            }
        }

        $message = "Fixtures generated successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Start League & Generate Fixtures</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

    <div class="bg-white shadow-md rounded-lg w-full max-w-xl p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
            Start League & Auto-Generate Fixtures
        </h2>

        <?php if ($message): ?>
            <div class="mb-4 p-4 text-green-800 bg-green-100 rounded border border-green-300 text-center">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <div>
                <label for="league_id" class="block text-sm font-medium text-gray-700 mb-1">Select a League</label>
                <select id="league_id" name="league_id" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select League --</option>
                    <?php foreach ($leagues as $league): ?>
                        <option value="<?php echo $league['id']; ?>">
                            <?php echo htmlspecialchars($league['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition">
                Generate Fixtures
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-sm text-blue-600 hover:underline">
                ⬅️ Back to Admin Dashboard
            </a>
        </div>
    </div>

</body>
</html>
