<?php
require_once 'database.php';
require_once 'functions.php';

$dare = getRandomDare();
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dare Roulette</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Dare Roulette 🎲</h1>
<main class="dare-container">
    <h2><?= htmlspecialchars($dare['title']); ?></h2>
    <p><?= htmlspecialchars($dare['description']); ?></p>
    <button onclick="location.reload();">Ny Dare</button>
</main>
</body>
</html>
