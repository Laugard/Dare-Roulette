<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
global $pdo;

if (!isset($_SESSION['user_id'])) {
    die("Ikke logget ind");
}

$userId = $_SESSION['user_id'];
$matchId = isset($_POST['match_id']) ? $_POST['match_id'] : 0;

$stmt = $pdo->prepare("UPDATE versus_matches SET winner_id = ?, status = 'completed' WHERE id = ? AND winner_id IS NULL");
$stmt->execute([$userId, $matchId]);

header("Location: versus.php");
exit();
