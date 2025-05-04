<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
global $pdo;

if (!isset($_SESSION['user_id'])) {
    die("Ikke logget ind");
}

$userId = $_SESSION['user_id'];

// Slet brugerens afsluttede kampe
$stmt = $pdo->prepare("DELETE FROM versus_matches WHERE (player1_id = ? OR player2_id = ?) AND status = 'completed'");
$stmt->execute([$userId, $userId]);

header("Location: versus.php");
exit();
