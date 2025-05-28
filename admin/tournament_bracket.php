<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$tournamentId = $_GET['tournament_id'] ?? null;

if (!$tournamentId) {
    die("Invalid tournament ID.");
}


// Fetch tournament info
$stmt = $pdo->prepare("SELECT * FROM tournaments WHERE id = ?");
$stmt->execute([$tournamentId]);
$tournament = $stmt->fetch();

if (!$tournament) {
    die("Tournament not found.");
}

// Fetch all matches grouped by rounds ordered
$stmt = $pdo->prepare("
    SELECT tm.*, 
           p1.username AS player1_name, 
           p2.username AS player2_name 
    FROM tournament_matches tm
    LEFT JOIN users p1 ON tm.player1_id = p1.id
    LEFT JOIN users p2 ON tm.player2_id = p2.id
    WHERE tm.tournament_id = ?
    ORDER BY tm.round, tm.id
");
$stmt->execute([$tournamentId]);
$matches = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);

$matchesByRound = [];
foreach ($matches as $match) {
    $matchesByRound[$match['round']][] = $match;
}

// Handle POST to update result
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_id'])) {
    $matchId = $_POST['match_id'];
    $player1Score = isset($_POST['player1_score']) ? intval($_POST['player1_score']) : null;
    $player2Score = isset($_POST['player2_score']) ? intval($_POST['player2_score']) : null;

    // Validate match ownership: player must be either player1 or player2
    $stmt = $pdo->prepare("SELECT * FROM tournament_matches WHERE id = ? AND tournament_id = ?");
    $stmt->execute([$matchId, $tournamentId]);
    $match = $stmt->fetch();

    if (!$match) {
        die("Match not found.");
    }

    if ($match['player1_id'] != $userId && $match['player2_id'] != $userId) {
        die("You cannot update a match that you are not part of.");
    }

    // Determine winner_id based on scores
    if ($player1Score === null || $player2Score === null) {
        $error = "Both scores must be entered.";
    } else if ($player1Score == $player2Score) {
        $error = "Draw is not allowed in knockout tournaments.";
    } else {
        $winnerId = ($player1Score > $player2Score) ? $match['player1_id'] : $match['player2_id'];

        // Update match result
        $stmt = $pdo->prepare("UPDATE tournament_matches SET player1_score = ?, player2_score = ?, winner_id = ? WHERE id = ?");
        $stmt->execute([$player1Score, $player2Score, $winnerId, $matchId]);

        // Generate next round match if all matches in current round have winners
        generateNextRoundMatch($pdo, $tournamentId, $match['round']);

        header("Location: tournament_bracket.php?tournament_id=$tournamentId");
        exit;
    }
}

// Function to generate next round matches
function generateNextRoundMatch($pdo, $tournamentId, $currentRound)
{
    // We need to:
    // 1) Check if all matches in current round have a winner
    // 2) If yes, create next round matches pairing winners

    // First get all matches in current round with winners set
    $stmt = $pdo->prepare("SELECT * FROM tournament_matches WHERE tournament_id = ? AND round = ?");
    $stmt->execute([$tournamentId, $currentRound]);
    $matches = $stmt->fetchAll();

    foreach ($matches as $m) {
        if ($m['winner_id'] === null) {
            // Still waiting for match results
            return;
        }
    }

    // Determine next round name (based on typical knockout rounds)
    $roundOrder = [
        "Round of 64" => "Round of 32",
        "Round of 32" => "Round of 16",
        "Round of 16" => "Quarterfinal",
        "Quarterfinal" => "Semifinal",
        "Semifinal" => "Final",
        "Final" => null
    ];

    $nextRound = $roundOrder[$currentRound] ?? null;
    if (!$nextRound) {
        // Tournament ended
        // Update tournament status as completed if all matches done
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tournament_matches WHERE tournament_id = ? AND winner_id IS NULL");
        $stmt->execute([$tournamentId]);
        $pendingCount = $stmt->fetchColumn();
        if ($pendingCount == 0) {
            $stmt = $pdo->prepare("UPDATE tournaments SET status = 'completed' WHERE id = ?");
            $stmt->execute([$tournamentId]);
        }
        return;
    }

    // Get winners in order for next round
    $winners = [];
    foreach ($matches as $m) {
        $winners[] = $m['winner_id'];
    }

    // Pair winners to create next round matches
    for ($i = 0; $i < count($winners); $i += 2) {
        $player1 = $winners[$i];
        $player2 = $winners[$i + 1] ?? null; // In case of odd number, could be null

        $winnerId = null;
        // If player2 is null (bye), player1 automatically wins
        if ($player1 !== null && $player2 === null) {
            $winnerId = $player1;
        }

        // Check if match already exists to avoid duplicates
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tournament_matches WHERE tournament_id = ? AND round = ? AND player1_id = ? AND player2_id = ?");
        $stmt->execute([$tournamentId, $nextRound, $player1, $player2]);
        $exists = $stmt->fetchColumn();

        if ($exists == 0) {
            $stmt = $pdo->prepare("INSERT INTO tournament_matches (tournament_id, round, player1_id, player2_id, winner_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$tournamentId, $nextRound, $player1, $player2, $winnerId]);
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($tournament['name']); ?> - Bracket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-3xl font-bold mb-6 text-center"><?php echo htmlspecialchars($tournament['name']); ?> - Tournament Bracket</h1>

        <?php if (isset($error)): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if ($tournament['status'] === 'completed'): ?>
            <p class="text-green-700 font-bold text-center mb-6">🏆 Tournament Completed!</p>
        <?php endif; ?>

        <?php foreach ($matchesByRound as $round => $matches): ?>
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4"><?php echo htmlspecialchars($round); ?></h2>
                <div class="space-y-4">
                    <?php foreach ($matches as $match): ?>
                        <div class="border p-4 rounded flex items-center justify-between bg-gray-50">
                            <div class="flex-1">
                                <span class="font-semibold"><?php echo htmlspecialchars($match['player1_name'] ?? 'TBD'); ?></span>
                                <span> vs </span>
                                <span class="font-semibold"><?php echo htmlspecialchars($match['player2_name'] ?? 'TBD'); ?></span>
                            </div>
                            <div class="flex-1 text-center">
                                <?php if ($match['player1_score'] !== null && $match['player2_score'] !== null): ?>
                                    <span class="font-semibold"><?php echo $match['player1_score'] . " - " . $match['player2_score']; ?></span>
                                <?php else: ?>
                                    <span class="text-gray-400">Not played</span>
                                <?php endif; ?>
                            </div>

                            <div class="flex-1 text-right">
                                <?php if (($match['player1_id'] == $userId || $match['player2_id'] == $userId) && $tournament['status'] ===
                                    'active'
                                ): ?>
                                    <form method="post" class="inline-block">
                                        <input type="hidden" name="match_id" value="<?php echo $match['id']; ?>">
                                        <input type="number" name="player1_score" min="0" class="w-14 border rounded px-1 mr-2" required value="<?php echo htmlspecialchars($match['player1_score'] ?? ''); ?>">
                                        <input type="number" name="player2_score" min="0" class="w-14 border rounded px-1 mr-2" required value="<?php echo htmlspecialchars($match['player2_score'] ?? ''); ?>">
                                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Update</button>
                                    </form>
                                <?php else: ?>
                                    <!-- No action -->
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">⬅️ Back to Dashboard</a>
        </div>
    </div>
</body>

</html>