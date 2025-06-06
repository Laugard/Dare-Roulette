<?php
// Vis fejl i browseren
global $pdo;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../controllers/GameController.php';

$controller = new GameController($pdo);
$rotation = isset($_GET['rotation']) ? floatval($_GET['rotation']) : 0;
$dare = $controller->getRandomByCategoryDare('Extreme');
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extreme Dare Roulette</title>
    <link rel="stylesheet" href="../../../public/assets/styles.css">
</head>
<body>
<header>
    <h1>🎲 Extreme Dare Roulette</h1>
</header>

<main>
    <section aria-labelledby="wheel-heading">
        <h2 id="wheel-heading" class="visually-hidden">Roulettehjul</h2>
        <figure>
            <div id="pointer" aria-hidden="true"></div>
            <canvas id="wheel" width="300" height="300" role="img" aria-label="Roulettehjul med 6 felter"></canvas>
            <figcaption class="visually-hidden">Viser roulettehjulet med ukendte udfordringer.</figcaption>
        </figure>
    </section>

    <section aria-label="Spin knap">
        <button id="spinButton" type="button">🎯 Spin hjulet</button>
    </section>

    <?php if ($dare): ?>
        <section class="dare" aria-labelledby="dare-title">
            <article>
                <h2 id="dare-title"><?= htmlspecialchars($dare['title']); ?></h2>
                <p><?= htmlspecialchars($dare['description']); ?></p>
            </article>
        </section>
    <?php endif; ?>

    <nav aria-label="Navigation">
        <a href="party.php" class="styled-button">🔙 Tilbage</a>
    </nav>
</main>

<footer>
    <p><small>&copy; 2025 Dare Roulette</small></p>
</footer>

<script>
    const slices = 6;
    const labels = Array(slices).fill("???");
    const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#8E44AD', '#27AE60', '#E67E22'];
    let spinning = false;

    const canvas = document.getElementById('wheel');
    const ctx = canvas.getContext('2d');
    const spinButton = document.getElementById('spinButton');

    function getRotationFromURL() {
        const params = new URLSearchParams(window.location.search);
        return parseFloat(params.get("rotation")) || 0;
    }

    let initialRotation = getRotationFromURL() % (2 * Math.PI);

    function drawWheel(rotation = 0) {
        const angle = (2 * Math.PI) / slices;
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        labels.forEach((label, i) => {
            const startAngle = i * angle + rotation;
            const endAngle = startAngle + angle;

            ctx.beginPath();
            ctx.moveTo(150, 150);
            ctx.arc(150, 150, 140, startAngle, endAngle);
            ctx.fillStyle = colors[i % colors.length];
            ctx.fill();

            ctx.save();
            ctx.translate(150, 150);
            ctx.rotate(startAngle + angle / 2);
            ctx.fillStyle = '#000';
            ctx.font = '16px Arial';
            ctx.fillText(label, 70, 5);
            ctx.restore();
        });
    }

    drawWheel(initialRotation);

    spinButton.addEventListener('click', () => {
        if (spinning) return;
        spinning = true;

        let rotation = 0;
        const spins = 10 + Math.floor(Math.random() * 5);
        const randomOffset = Math.random() * 2 * Math.PI;
        const totalRotation = spins * 2 * Math.PI + randomOffset;

        const duration = 3000;
        const start = performance.now();

        function animate(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            rotation = eased * totalRotation;
            drawWheel(rotation);

            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                window.location.href = `?rotation=${rotation}`;
            }
        }

        requestAnimationFrame(animate);
    });
</script>
</body>
</html>
