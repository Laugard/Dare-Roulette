<?php
$host = "localhost";
$dbname = "dare_roulette";
$username = "root";  // XAMPP standardbruger
$password = "";      // XAMPP standard adgangskode (tom)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Databaseforbindelse fejlede: " . $e->getMessage());
}
?>
