<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournamentId = $_POST['tournament_id'] ?? null;

    if ($tournamentId) {
        // Check if already registered
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tournament_players WHERE tournament_id = ? AND player_id = ?");
        $stmt->execute([$tournamentId, $userId]);
        if ($stmt->fetchColumn() == 0) {
            // Register player
            $stmt = $pdo->prepare("INSERT INTO tournament_players (tournament_id, player_id) VALUES (?, ?)");
            $stmt->execute([$tournamentId, $userId]);
        }
    }
}

header("Location: tournaments.php");
exit;
