<?php
global $pdo;
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/UserController.php';

$controller = new UserController($pdo);
$users = $controller->getLeaderboard();
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Dare Roulette</title>
    <link rel="stylesheet" href="../public/assets/styles.css">
    <style>
        .leaderboard {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1em;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
        }
        th {
            background-color: #222;
            color: #fff;
        }
        tr:nth-child(1) td {
            background: gold;
            font-weight: bold;
        }
        tr:nth-child(2) td {
            background: silver;
        }
        tr:nth-child(3) td {
            background: #cd7f32;
        }
    </style>
</head>
<body>
<nav class="top-nav">
    <a href="gamemodes.php">🎮 Game Modes</a>
    <a href="profile.php">👤 Profil</a>
</nav>
<header>
    <h1>🏆 Leaderboard</h1>
</header>

<main>
    <section class="leaderboard">
        <h2>Top 100 Spillere</h2>
        <table>
            <tr>
                <th>#</th>
                <th>Brugernavn</th>
                <th>Level</th>
                <th>Total XP</th>
            </tr>
            <?php foreach ($users as $index => $user): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= $user['level'] ?></td>
                    <td><?= $user['xp'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <a href="gamemodes.php" class="styled-button" style="margin-top: 20px;">🔙 Tilbage</a>
    </section>
</main>

<footer>
    <small>&copy; 2025 Dare Roulette</small>
</footer>
</body>
</html>
