<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['league_id'])) {
    $league_id = $_POST['league_id'];

    // Mark league as completed
    $stmt = $pdo->prepare("UPDATE leagues SET status = 'completed' WHERE id = ?");
    $stmt->execute([$league_id]);

    header("Location: leagues.php");
    exit;
} else {
    header("Location: leagues.php");
    exit;
}
