<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tournament_id'])) {
    $tournament_id = $_POST['tournament_id'];

    $stmt = $pdo->prepare("UPDATE tournaments SET status = 'completed' WHERE id = ?");
    $stmt->execute([$tournament_id]);
    header("Location: tournaments_list.php");
    exit;
} else {
    header("Location: tournaments_list.php");
    exit;
}
