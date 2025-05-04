<?php
// login.php - User login
global $pdo;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $username_or_email = $_POST['username_or_email'];
    $password = $_POST['password'];

    // Check if the username or email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username_or_email OR email = :username_or_email");
    $stmt->execute(['username_or_email' => $username_or_email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, start session and redirect to game page
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: ../gamemodes.php");
        exit;
    } else {
        echo "Invalid username/email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dare Roulette</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<header>
    <h1>Dare Roulette - Login</h1>
</header>

<main>
    <section class="dare">
        <form action="login.php" method="POST">
            <label for="username_or_email">Username or Email</label>
            <input type="text" id="username_or_email" name="username_or_email" required>

            <label for="password">Password</label>
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
