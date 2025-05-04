<?php
global $pdo;
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/GameController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$controller = new GameController($pdo);
$data = $controller->handle($_SESSION['user_id'], 'Solo');

// PHP 5-kompatibel fallback
$dare = isset($data['dare']) ? $data['dare'] : array('title' => '', 'description' => '', 'xp_reward' => 0);
$level = isset($data['level']) ? $data['level'] : 1;
$xpThisLevel = isset($data['xpThisLevel']) ? $data['xpThisLevel'] : 0;
$progressPercent = isset($data['progressPercent']) ? $data['progressPercent'] : 0;
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solo Mode</title>
    <link rel="stylesheet" href="../../public/assets/styles.css">
</head>
<body>

<nav class="top-nav" aria-label="Primær navigation">
    <a href="../gamemodes.php">🎮 Game Modes</a>
    <a href="../profile.php">👤 Profil</a>
</nav>

<header>
    <h1>Solo Mode 💪</h1>
</header>

<main>
    <section class="dare" aria-labelledby="dare-title">
        <h2 id="dare-title"><?= htmlspecialchars($dare['title']) ?></h2>
        <p><?= htmlspecialchars($dare['description']) ?></p>
        <p><strong>XP for denne dare:</strong> <?= htmlspecialchars($dare['xp_reward']) ?> XP</p>

        <form method="post">
            <input type="hidden" name="xp" value="<?= (int) $dare['xp_reward'] ?>">
            <button type="submit" name="completed" class="styled-button">Dare klaret!</button>
        </form>

        <hr>

        <section aria-labelledby="xp-title">
            <h3 id="xp-title">🧠 XP</h3>
            <p><strong>Level:</strong> <?= (int) $level ?></p>

            <div class="xp-wrapper">
                <div class="xp-bar-container">
                    <div class="xp-bar" style="width: <?= (float) $progressPercent ?>%;"></div>
                    <div class="xp-text-wrapper">
                        <span class="xp-text"><?= (int) $xpThisLevel ?>/100 XP</span>
                    </div>
                </div>
            </div>
        </section>
    </section>
</main>

<footer>
    <small>&copy; 2025 Dare Roulette</small>
</footer>

</body>
</html>
