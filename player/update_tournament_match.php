<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

if (!isset($_GET['match_id'])) {
    echo "Match ID is missing.";
    exit;
}

$matchId = $_GET['match_id'];

// Fetch match and validate access
$stmt = $pdo->prepare("
    SELECT m.*, u1.username AS p1_name, u2.username AS p2_name
    FROM tournament_matches m
    JOIN users u1 ON m.player1_id = u1.id
    JOIN users u2 ON m.player2_id = u2.id
    WHERE m.id = ?
");
$stmt->execute([$matchId]);
$match = $stmt->fetch();

if (!$match) {
    echo "Match not found.";
    exit;
}

// Check if the logged-in user is one of the players
if ($match['player1_id'] != $userId && $match['player2_id'] != $userId) {
    echo "You are not authorized to update this match.";
    exit;
}

// Handle score update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score1 = $_POST['player1_score'];
    $score2 = $_POST['player2_score'];

    if ($score1 === '' || $score2 === '') {
        $error = "Both scores are required.";
    } else {
        // Update scores
        $stmt = $pdo->prepare("UPDATE tournament_matches SET player1_score = ?, player2_score = ? WHERE id = ?");
        $stmt->execute([$score1, $score2, $matchId]);

        // Update player profile stats
        updateUserStats($pdo, $match['player1_id'], $score1, $score2);
        updateUserStats($pdo, $match['player2_id'], $score2, $score1);

        // After updating score, try generating next round if all matches in this round are finished
        generateNextRoundIfReady($pdo, $match['tournament_id'], $match['round']);

        header("Location: tournament_bracket.php?tournament_id=" . $match['tournament_id']);
        exit;
    }
}
function updateUserStats($pdo, $userId, $goalsScored, $goalsConceded) {
    // Determine win/loss
    $win = $goalsScored > $goalsConceded ? 1 : 0;
    $loss = $goalsScored < $goalsConceded ? 1 : 0;
    $draw = $goalsScored == $goalsConceded ? 1 : 0;

    // Update user profile stats
    $stmt = $pdo->prepare("
        UPDATE users 
        SET 
            matches_played = matches_played + 1,
            wins = wins + ?, 
            losses = losses + ?, 
            draws = draws + ?, 
            goals_scored = goals_scored + ?, 
            goals_conceded = goals_conceded + ? 
        WHERE id = ?
    ");
    $stmt->execute([$win, $loss, $draw, $goalsScored, $goalsConceded, $userId]);
}


function getRoundNameByMatchCount($matchCount) {
    switch ($matchCount) {
        case 8:
            return 'Round of 16';
        case 4:
            return 'Quarterfinal';
        case 2:
            return 'Semifinal';
        case 1:
            return 'Final';
        default:
            return 'Round of ' . ($matchCount * 2);
    }
}

function getNextPowerOfTwoAtLeast($num) {
    $power = 1;
    while ($power < $num) {
        $power *= 2;
    }
    return $power;
}

function calculateStandings($pdo, $tournamentId) {
    // Calculate standings from tournament_matches dynamically
    $stmt = $pdo->prepare("
        SELECT 
            player_id,
            SUM(points) AS points,
            SUM(goals_scored) AS goals_scored,
            SUM(goals_conceded) AS goals_conceded,
            SUM(goals_scored - goals_conceded) AS goal_difference
        FROM (
            SELECT 
                player1_id AS player_id,
                CASE 
                    WHEN player1_score > player2_score THEN 3
                    WHEN player1_score = player2_score THEN 1
                    ELSE 0
                END AS points,
                player1_score AS goals_scored,
                player2_score AS goals_conceded
            FROM tournament_matches
            WHERE tournament_id = ? AND player1_score IS NOT NULL AND player2_score IS NOT NULL

            UNION ALL

            SELECT 
                player2_id AS player_id,
                CASE 
                    WHEN player2_score > player1_score THEN 3
                    WHEN player2_score = player1_score THEN 1
                    ELSE 0
                END AS points,
                player2_score AS goals_scored,
                player1_score AS goals_conceded
            FROM tournament_matches
            WHERE tournament_id = ? AND player1_score IS NOT NULL AND player2_score IS NOT NULL
        ) AS combined
        GROUP BY player_id
        ORDER BY 
            points DESC,
            goal_difference DESC,
            goals_scored DESC,
            goals_conceded ASC
    ");
    $stmt->execute([$tournamentId, $tournamentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function generateNextRoundIfReady($pdo, $tournamentId, $currentRound) {
    // 1. Check if all matches in current round are finished (scores not null)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tournament_matches WHERE tournament_id = ? AND round = ? AND (player1_score IS NULL OR player2_score IS NULL)");
    $stmt->execute([$tournamentId, $currentRound]);
    $unfinishedCount = $stmt->fetchColumn();

    if ($unfinishedCount > 0) {
        // Not all matches finished, no next round generation yet
        return;
    }

    // 2. Get winners and losers of current round
    $stmt = $pdo->prepare("SELECT player1_id, player2_id, player1_score, player2_score FROM tournament_matches WHERE tournament_id = ? AND round = ? ORDER BY id ASC");
    $stmt->execute([$tournamentId, $currentRound]);
    $matches = $stmt->fetchAll();

    $winners = [];
    $losers = [];
    foreach ($matches as $m) {
        if ($m['player1_score'] > $m['player2_score']) {
            $winners[] = $m['player1_id'];
            $losers[] = $m['player2_id'];
        } elseif ($m['player2_score'] > $m['player1_score']) {
            $winners[] = $m['player2_id'];
            $losers[] = $m['player1_id'];
        } else {
            // Tie - default player1 winner
            $winners[] = $m['player1_id'];
            $losers[] = $m['player2_id'];
        }
    }
    // 🛑 Stop if tournament is over (only one winner remains)
    if (count($winners) === 1) {
        return;
    }
    $winnerCount = count($winners);
    $nextRoundSize = getNextPowerOfTwoAtLeast($winnerCount);
    $playersForNextRound = $winners;

    if ($winnerCount < $nextRoundSize) {
        $needLosers = $nextRoundSize - $winnerCount;

        // 3. Calculate dynamic standings
        $standings = calculateStandings($pdo, $tournamentId);

        // Extract player IDs ordered by standings
        $orderedPlayers = array_column($standings, 'player_id');

        // Filter out winners to get only losers in standings order
        $losersOrdered = array_filter($orderedPlayers, fn($p) => !in_array($p, $winners));

        // Add top losers to fill spots
        $playersForNextRound = array_merge($playersForNextRound, array_slice($losersOrdered, 0, $needLosers));
    }

    // 4. Determine next round name by match count
    $nextRoundMatchCount = count($playersForNextRound) / 2;
    $nextRound = getRoundNameByMatchCount($nextRoundMatchCount);

    // 5. Check if next round already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tournament_matches WHERE tournament_id = ? AND round = ?");
    $stmt->execute([$tournamentId, $nextRound]);
    if ($stmt->fetchColumn() > 0) {
        return; // Already created
    }

    // 6. Insert next round matches
    $stmt = $pdo->prepare("INSERT INTO tournament_matches (tournament_id, player1_id, player2_id, round, player1_score, player2_score) VALUES (?, ?, ?, ?, NULL, NULL)");

    for ($i = 0; $i < count($playersForNextRound); $i += 2) {
        $p1 = $playersForNextRound[$i];
        $p2 = $playersForNextRound[$i + 1];
        $stmt->execute([$tournamentId, $p1, $p2, $nextRound]);
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Update Match Score</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-6 px-4">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4">⚽ Update Match Score</h2>

        <p class="mb-4 text-gray-700">
            <strong><?php echo htmlspecialchars($match['p1_name']); ?></strong> vs 
            <strong><?php echo htmlspecialchars($match['p2_name']); ?></strong>
            (<?php echo htmlspecialchars($match['round']); ?>)
        </p>

        <?php if (isset($error)): ?>
            <p class="text-red-600 mb-3"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    <?php echo htmlspecialchars($match['p1_name']); ?>'s Score:
                </label>
                <input type="number" name="player1_score" class="w-full border p-2 rounded" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    <?php echo htmlspecialchars($match['p2_name']); ?>'s Score:
                </label>
                <input type="number" name="player2_score" class="w-full border p-2 rounded" required>
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                ✅ Submit Score
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="tournament_bracket.php?tournament_id=<?php echo $match['tournament_id']; ?>" class="text-blue-600 hover:underline">
                ⬅️ Back to Bracket
            </a>
        </div>
    </div>
</body>
</html>
