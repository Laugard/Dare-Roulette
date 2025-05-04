<?php
global $pdo;
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

$auth = new AuthController($pdo);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $auth->register($username, $email, $password);

    if ($result === true) {
        $message = "Registrering gennemført! <a href='login.php'>Log ind</a>";
    } else {
        $message = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <title>Registrer - Dare Roulette</title>
    <link rel="stylesheet" href="../../public/assets/styles.css">
</head>
<body>
<header>
    <h1>Dare Roulette - Registrering</h1>
</header>

<main>
    <section class="dare">
        <?php if ($message): ?>
            <p style="color:<?= $result === true ? 'green' : 'red' ?>;"><?= $message ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="username">Brugernavn</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Adgangskode</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="styled-button">Registrer</button>
        </form>
    </section>
</main>

<footer>
    <small>&copy; 2025 Dare Roulette</small>
</footer>
</body>
</html>
