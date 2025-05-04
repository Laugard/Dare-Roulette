<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

global $pdo;
$auth = new AuthController($pdo);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['username_or_email'];
    $password = $_POST['password'];

    if ($auth->login($usernameOrEmail, $password)) {
        header("Location: ../../views/gamemodes.php");
        exit;
    } else {
        $error = "Forkert brugernavn/email eller adgangskode.";
    }
}
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <title>Login - Dare Roulette</title>
    <link rel="stylesheet" href="../../public/assets/styles.css">
</head>
<body>
<header>
    <h1>Dare Roulette - Login</h1>
</header>

<main>
    <section class="dare">
        <?php if ($error): ?>
            <p style="color:red;"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="username_or_email">Brugernavn eller Email</label>
            <input type="text" id="username_or_email" name="username_or_email" required>

            <label for="password">Adgangskode</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="styled-button">Login</button>
        </form>
    </section>
</main>

<footer>
    <small>&copy; 2025 Dare Roulette</small>
</footer>
</body>
</html>
