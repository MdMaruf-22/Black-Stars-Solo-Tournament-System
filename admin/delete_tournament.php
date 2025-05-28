<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id']) || !isset($_POST['tournament_id'])) {
    header("Location: login.php");
    exit;
}

$tournamentId = $_POST['tournament_id'];

$stmt = $pdo->prepare("DELETE FROM tournaments WHERE id = ?");
$stmt->execute([$tournamentId]);

header("Location: tournaments_list.php");
exit;
