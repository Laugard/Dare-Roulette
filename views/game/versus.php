<?php
global $pdo;
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/GameController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$controller = new GameController($pdo);

// AJAX: check_winner
if (isset($_GET['ajax']) && $_GET['ajax'] === 'check_winner' && isset($_GET['match_id'])) {
    header('Content-Type: application/json');
    echo json_encode($controller->checkWinner($_GET['match_id']));
    exit();
}

// POST: submit_winner
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_id'])) {
    $controller->submitWinner($userId, $_POST['match_id']);
    header("Location: versus.php");
    exit();
}

// GET: reset
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    $controller->resetCompletedMatches($userId);
    header("Location: versus.php");
    exit();
}

// Hent kamp
$match = $controller->getOrCreateVersusMatch($userId);

// Sikkerhedstjek
if (!in_array($userId, [$match['player1_id'], $match['player2_id']])) {
    die("Du er ikke en del af denne kamp.");
}
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <title>Versus Mode</title>
    <link rel="stylesheet" href="../../public/assets/styles.css">
</head>
<body>

<nav class="top-nav" aria-label="Primær navigation">
    <a href="../gamemodes.php">🎮 Game Modes</a>
    <a href="../profile.php">👤 Profil</a>
</nav>

<header>
    <h1>Versus Mode ⚔️</h1>
</header>

<main>
    <?php if ($match['status'] === 'waiting'): ?>
        <section aria-live="polite">
            <p>⏳ Venter på en modstander...</p>
            <script>
                setInterval(() => {
                    fetch("versus.php?ajax=check_winner&match_id=<?= $match['id'] ?>")
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'active') {
                                location.reload();
                            }
                        });
                }, 2000);
            </script>
        </section>

    <?php elseif ($match['status'] === 'completed'): ?>
        <section>
            <h2>Vinderen er: <?= $match['winner_id'] == $userId ? 'DIG! 🎉' : 'Modstanderen 😔' ?></h2>
            <a href="versus.php?action=reset" class="styled-button">Spil igen</a>
        </section>

    <?php else: ?>
        <section>
            <h2>Dagens Dare</h2>
            <p><strong><?= htmlspecialchars($match['title']) ?></strong></p>
            <p><?= htmlspecialchars($match['description']) ?></p>

            <form method="post">
                <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                <button type="submit" class="styled-button">Dare klaret!</button>
            </form>

            <script>
                setInterval(() => {
                    fetch("versus.php?ajax=check_winner&match_id=<?= $match['id'] ?>")
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'completed') {
                                location.reload();
                            }
                        });
                }, 2000);
            </script>
        </section>
    <?php endif; ?>

    <nav aria-label="Sekundær navigation">
        <a href="../gamemodes.php" class="styled-button">🔙 Tilbage</a>
    </nav>
</main>

</body>
</html>

