<?php
// register.php - User registration

require 'database.php';  // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username or email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $stmt->execute(['username' => $username, 'email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        echo "Username or Email already taken.";
    } else {
        // Insert new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashed_password]);
        echo "Registration successful! <a href='login.php'>Login</a>";
    }
}
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Dare Roulette</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h1>Dare Roulette - Register</h1>
</header>

<main>
    <section class="dare">
        <form action="register.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="styled-button">Register</button>
        </form>
    </section>
</main>

<footer>
    <small>&copy; 2025 Dare Roulette</small>
</footer>
</body>
</html>
