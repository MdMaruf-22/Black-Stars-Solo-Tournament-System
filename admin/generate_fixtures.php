<?php
require_once '../config/db.php';

if (!isset($_GET['league_id'])) {
    die("League ID is required.");
}

$leagueId = $_GET['league_id'];

// Check if fixtures already exist
$stmt = $pdo->prepare("SELECT COUNT(*) FROM matches WHERE league_id = ?");
$stmt->execute([$leagueId]);
if ($stmt->fetchColumn() > 0) {
    die("⚠️ Fixtures already generated for this league.");
}

// Get all registered players in the league
$stmt = $pdo->prepare("
    SELECT player_id FROM league_player WHERE league_id = ?
");
$stmt->execute([$leagueId]);
$players = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (count($players) < 2) {
    die("❌ At least 2 players required.");
}

// Double round-robin fixture generation
$matches = [];

for ($i = 0; $i < count($players); $i++) {
    for ($j = $i + 1; $j < count($players); $j++) {
        $p1 = $players[$i];
        $p2 = $players[$j];

        // First leg
        $matches[] = [$p1, $p2];

        // Second leg (reverse)
        $matches[] = [$p2, $p1];
    }
}

// Insert into DB
$stmt = $pdo->prepare("INSERT INTO matches (league_id, player1_id, player2_id) VALUES (?, ?, ?)");

foreach ($matches as $match) {
    $stmt->execute([$leagueId, $match[0], $match[1]]);
}

echo "✅ Fixtures generated successfully. <a href='leagues.php'>Go back</a>";
?>
