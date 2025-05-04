<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

global $pdo;

if (!isset($_SESSION['user_id'])) {
    die("Du skal være logget ind for at bruge Versus Mode.");
}

$userId = $_SESSION['user_id'];

// 1. Find sidste kamp (completed eller aktiv)
$stmt = $pdo->prepare("SELECT vm.*, d.title, d.description 
    FROM versus_matches vm 
    JOIN dares d ON vm.dare_id = d.id 
    WHERE (player1_id = ? OR player2_id = ?) 
    ORDER BY vm.created_at DESC 
    LIMIT 1");
$stmt->execute([$userId, $userId]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Hvis det er en completed kamp, og brugeren HAR set den → slet den og load ny
if ($match && $match['status'] === 'completed') {
    $seenKey = 'seen_match_' . $match['id'];

    if (isset($_SESSION[$seenKey])) {
        // Slet completed kamp og load ny
        $stmt = $pdo->prepare("DELETE FROM versus_matches WHERE id = ?");
        $stmt->execute([$match['id']]);
        unset($_SESSION[$seenKey]);
        header("Location: versus.php");
        exit();
    } else {
        // Sæt at brugeren har set den nu
        $_SESSION[$seenKey] = true;
    }
}

// 3. Hvis ingen kamp eksisterer længere, opret ny/tilslut
if (!$match || $match['status'] === 'deleted') {
    // 3A: Find kamp at tilslutte
    $stmt = $pdo->prepare("SELECT * FROM versus_matches WHERE status = 'waiting' AND player1_id != ? ORDER BY created_at ASC LIMIT 1");
    $stmt->execute([$userId]);
    $found = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($found) {
        $stmt = $pdo->prepare("UPDATE versus_matches SET player2_id = ?, status = 'active' WHERE id = ?");
        $stmt->execute([$userId, $found['id']]);

        $stmt = $pdo->prepare("SELECT vm.*, d.title, d.description 
            FROM versus_matches vm 
            JOIN dares d ON vm.dare_id = d.id 
            WHERE vm.id = ?");
        $stmt->execute([$found['id']]);
        $match = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // 3B: Opret ny kamp
        $dare = getRandomDare(); // title, description, id
        $stmt = $pdo->prepare("INSERT INTO versus_matches (player1_id, dare_id) VALUES (?, ?)");
        $stmt->execute([$userId, $dare['id']]);
        $matchId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("SELECT vm.*, d.title, d.description 
            FROM versus_matches vm 
            JOIN dares d ON vm.dare_id = d.id 
            WHERE vm.id = ?");
        $stmt->execute([$matchId]);
        $match = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$isPlayer1 = $match['player1_id'] == $userId;
$isPlayer2 = $match['player2_id'] == $userId;

if (!$isPlayer1 && !$isPlayer2) {
    die("Du er ikke en del af denne kamp.");
}
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <title>Versus Mode</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<nav class="top-nav">
    <a href="../gamemodes.php">🎮 Game Modes</a>
    <a href="../pages/profile.php">👤 Profil</a>
</nav>
<h1>Versus Mode ⚔️</h1>

<?php if ($match['status'] === 'waiting'): ?>
    <p>⏳ Venter på en modstander...</p>
    <script>
        setInterval(() => {
            fetch("check_winner.php?match_id=<?= $match['id'] ?>")
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'active') {
                        location.reload();
                    }
                });
        }, 2000);
    </script>

<?php elseif ($match['status'] === 'completed'): ?>
    <h2>Vinderen er: <?= $match['winner_id'] == $userId ? 'DIG! 🎉' : 'Modstanderen 😔' ?></h2>
    <a href="versus_reset.php" class="styled-button">Spil igen</a>

<?php else: ?>
    <p><strong>Dare:</strong> <?= htmlspecialchars($match['title']) ?></p>
    <p><?= htmlspecialchars($match['description']) ?></p>

    <form method="post" action="submit_winner.php">
        <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
        <button type="submit" class="styled-button">Dare klaret!</button>
    </form>

    <script>
        setInterval(() => {
            fetch("check_winner.php?match_id=<?= $match['id'] ?>")
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'completed') {
                        location.reload();
                    }
                });
        }, 2000);
    </script>
<?php endif; ?>

<a href="../gamemodes.php" class="styled-button">🔙 Tilbage</a>
</body>
</html>
