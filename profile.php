<?php
session_start();
require 'database.php'; // Assuming db.php contains the database connection

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user's data from the database
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Mocking Daredevil Titles and Stat Meter Data (Replace with actual logic)
$statMeter = [
    'daredevil_points' => 74, // Example stat
    'challenges_completed' => 53 // Example stat
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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h1>Your Profile 🎲</h1>
</header>

<main>
    <section class="profile">
        <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

        <!-- Daredevil Titles -->
        <h3>Your Daredevil Titles</h3>
        <ul>
            <?php foreach ($daredevilTitles as $title): ?>
                <li><?php echo htmlspecialchars($title); ?></li>
            <?php endforeach; ?>
        </ul>

        <!-- Stat Meter -->
        <h3>Your Daredevil Stats</h3>
        <div class="stat-meter">
            <p><strong>Daredevil Points: </strong><?php echo $statMeter['daredevil_points']; ?>/100</p>
            <div class="progress-bar">
                <div class="progress" style="width: <?php echo $statMeter['daredevil_points']; ?>%;"></div>
            </div>
            <p><strong>Challenges Completed: </strong><?php echo $statMeter['challenges_completed']; ?>/100</p>
            <div class="progress-bar">
                <div class="progress" style="width: <?php echo $statMeter['challenges_completed']; ?>%;"></div>
            </div>
        </div>

        <a href="index.php" class="styled-button">Logout</a>
        <a href="gamemodes.php" class="styled-button">Back</a>

    </section>
</main>

<footer>
    <small>&copy; 2025 Dare Roulette</small>
</footer>
</body>
</html>
