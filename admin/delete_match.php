<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Match ID is required.");
}

$matchId = $_GET['id'];

// Delete the match
$stmt = $pdo->prepare("DELETE FROM matches WHERE id = ?");
$stmt->execute([$matchId]);

header("Location: manage_matches.php");
exit;
?>
