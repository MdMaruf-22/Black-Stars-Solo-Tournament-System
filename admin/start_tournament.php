<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournamentId = $_POST['tournament_id'] ?? null;

    if ($tournamentId) {
        // Check tournament status
        $stmt = $pdo->prepare("SELECT status FROM tournaments WHERE id = ?");
        $stmt->execute([$tournamentId]);
        $tournament = $stmt->fetch();

        if (!$tournament || $tournament['status'] !== 'registration') {
            die("Tournament is not in registration phase or doesn't exist.");
        }

        // Get all registered players
        $stmt = $pdo->prepare("SELECT player_id FROM tournament_players WHERE tournament_id = ?");
        $stmt->execute([$tournamentId]);
        $players = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $playerCount = count($players);
        if ($playerCount < 2) {
            die("Not enough players to start the tournament.");
        }

        // Update tournament status to ongoing
        $stmt = $pdo->prepare("UPDATE tournaments SET status = 'ongoing' WHERE id = ?");
        $stmt->execute([$tournamentId]);

        // Shuffle players randomly
        shuffle($players);

        // Determine round name based on player count (or nearest power of two)
        // You can still keep this to display round name, even if no BYEs
        $nearestPow2 = pow(2, ceil(log($playerCount, 2)));

        $roundNames = [
            2 => "Final",
            4 => "Semifinal",
            8 => "Quarterfinal",
            16 => "Round of 16",
            32 => "Round of 32",
            64 => "Round of 64"
        ];

        // Only keep rounds that apply
        $roundNames = array_filter($roundNames, fn($key) => $key <= $nearestPow2, ARRAY_FILTER_USE_KEY);

        // Current round name is the largest round (nearestPow2)
        $round = $roundNames[$nearestPow2] ?? "Round";

        // Insert matches for pairs only — no BYEs
        $matchCount = floor($playerCount / 2);
        for ($i = 0; $i < $matchCount * 2; $i += 2) {
            $player1 = $players[$i];
            $player2 = $players[$i + 1];

            $winner = null; // no automatic winner, to be decided later

            $stmt = $pdo->prepare("INSERT INTO tournament_matches (tournament_id, round, player1_id, player2_id, winner_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$tournamentId, $round, $player1, $player2, $winner]);
        }

        // Optional: Handle leftover player if odd number
        if ($playerCount % 2 !== 0) {
            $leftoverPlayer = $players[$playerCount - 1];
            // You can decide how to handle leftover player here.
            // For example, create a bye match or auto-advance this player to next round.
            // For now, you may just log or notify admin.
        }

        header("Location: tournaments_list.php");
        exit;
    }
}
?>
