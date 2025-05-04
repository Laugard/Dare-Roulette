<?php
global $pdo;
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/UserController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$controller = new UserController($pdo);
$user = $controller->getProfile($_SESSION['user_id']);

// Dummy data (kan senere også flyttes til controller hvis det skal være dynamisk)
$statMeter = [
    'daredevil_points' => 74,
    'challenges_completed' => 53
];

$daredevilTitles = [
    'Daredevil of the Day',
    'Risk-Taker',
    'Fearless Challenger',
    'Ultimate Daredevil',
    'GOAT STATUS',
    'Monarch of Daredevils',
    'S Rank Daredevil'
];
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - Dare Roulette</title>
    <link rel="stylesheet" href="../public/assets/styles.css">
</head>
<body>
<nav class="top-nav">
    <a href="gamemodes.php">🎮 Game Modes</a>
    <a href="profile.php">👤 Profil</a>
</nav>
<header>
    <h1>Your Profile 🎲</h1>
</header>

<main>
    <section class="profile">
        <h2>Welcome, <?= htmlspecialchars($user['username']) ?>!</h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

        <h3>Your Daredevil Titles</h3>
        <ul>
            <?php foreach ($daredevilTitles as $title): ?>
                <li><?= htmlspecialchars($title) ?></li>
            <?php endforeach; ?>
        </ul>

        <h3>Your Daredevil Stats</h3>
        <div class="stat-meter">
            <p><strong>Daredevil Points: </strong><?= $statMeter['daredevil_points'] ?>/100</p>
            <div class="progress-bar">
                <div class="progress" style="width: <?= $statMeter['daredevil_points'] ?>%;"></div>
            </div>
            <p><strong>Challenges Completed: </strong><?= $statMeter['challenges_completed'] ?>/100</p>
            <div class="progress-bar">
                <div class="progress" style="width: <?= $statMeter['challenges_completed'] ?>%;"></div>
            </div>
        </div>

        <a href="../public/index.php" class="styled-button">Logout</a>
        <a href="gamemodes.php" class="styled-button">Back</a>
    </section>
</main>

<footer>
    <small>&copy; 2025 Dare Roulette</small>
</footer>
</body>
</html>
