<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['league_id'])) {
    $league_id = $_GET['league_id'];

    // Optional: delete related fixtures/teams if needed before deleting the league

    $stmt = $pdo->prepare("DELETE FROM leagues WHERE id = ?");
    $stmt->execute([$league_id]);

    // Redirect back to leagues page
    header("Location: leagues.php");
    exit;
} else {
    // Invalid access
    header("Location: leagues.php");
    exit;
}
