<?php
require_once 'database.php';

function getRandomDare() {
    global $pdo;
    $stmt = $pdo->query("SELECT title, description FROM dares ORDER BY RAND() LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
