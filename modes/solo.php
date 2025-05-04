<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

if (!isset($_SESSION['user_id'])) {
    die("Du skal være logget ind for at bruge Solo Mode.");
}

global $pdo;
$userId = $_SESSION['user_id'];

// Når man klikker "Dare klaret!"
if (isset($_POST['completed'])) {
    $xpToAdd = (int)$_POST['xp'];

    $stmt = $pdo->prepare("UPDATE users SET xp = xp + ? WHERE id = ?");
    $stmt->execute([$xpToAdd, $userId]);

    header("Location: solo.php");
    exit();
}

// Hent dare
$dare = getRandomDareByCategory('Solo');

// Hent brugerens xp og level
$stmt = $pdo->prepare("SELECT xp, level FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$totalXP = $user['xp'];
$currentLevel = $user['level'];

// Genberegn korrekt level
$calculatedLevel = floor($totalXP / 100) + 1;
if ($calculatedLevel > $currentLevel) {
    $stmt = $pdo->prepare("UPDATE users SET level = ? WHERE id = ?");
    $stmt->execute([$calculatedLevel, $userId]);
    $currentLevel = $calculatedLevel;
}

// XP status for progress bar
$xpThisLevel = $totalXP % 100;
$progressPercent = min(100, ($xpThisLevel / 100) * 100);
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solo Mode</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .xp-bar-container {
            width: 100%;
            max-width: 400px;
            background: #ddd;
            border-radius: 20px;
            overflow: hidden;
            margin-top: 10px;
            height: 25px;
            position: relative;
        }

        .xp-bar {
            height: 100%;
            background: linear-gradient(to right, #4caf50, #81c784);
            width: <?= $progressPercent ?>%;
            transition: width 0.3s ease-in-out;
            border-radius: 20px;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .xp-text-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            pointer-events: none;
        }

        .xp-text {
            font-weight: bold;
            color: black;
            font-size: 14px;
        }
    </style>
</head>
<body>

<nav class="top-nav">
    <a href="../gamemodes.php">🎮 Game Modes</a>
    <a href="../pages/profile.php">👤 Profil</a>
</nav>
<header>
    <h1>Solo Mode 💪</h1>
</header>



<main>
    <section class="solo-dare">
        <h2><?= htmlspecialchars($dare['title']); ?></h2>
        <p><?= htmlspecialchars($dare['description']); ?></p>
        <p><strong>XP for denne dare:</strong> <?= htmlspecialchars($dare['xp_reward']) ?> XP</p>

        <form method="post">
            <input type="hidden" name="xp" value="<?= (int)$dare['xp_reward'] ?>">
            <button type="submit" name="completed" class="styled-button">Dare klaret!</button>
        </form>

        <hr>

        <h3>🧠 XP</h3>
        <p><strong>Level:</strong> <?= $currentLevel ?></p>

        <div style="display: flex; justify-content: center;">
            <div class="xp-bar-container">
                <div class="xp-bar"></div>
                <div class="xp-text-wrapper">
                    <span class="xp-text"><?= $xpThisLevel ?>/100 XP</span>
                </div>
            </div>
        </div>

    </section>
</main>

<footer>
    <small>&copy; 2025 Dare Roulette</small>
</footer>
</body>
</html>
