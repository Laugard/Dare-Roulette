<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
global $pdo;

$matchId = isset($_GET['match_id']) ? $_GET['match_id'] : 0;

$stmt = $pdo->prepare("SELECT status, winner_id FROM versus_matches WHERE id = ?");
$stmt->execute([$matchId]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($match);
