<?php
require_once 'database.php';

function getRandomDare() {
    global $pdo;
    $stmt = $pdo->query("SELECT id, title, description FROM dares ORDER BY RAND() LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getRandomDareByCategory($category) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM dares WHERE category = ? ORDER BY RAND() LIMIT 1");
    $stmt->execute([$category]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
