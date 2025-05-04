<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Dare.php';

class GameController {
    private $pdo;
    private $userModel;
    private $dareModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->dareModel = new Dare($pdo);
    }

    public function getRandomByCategoryDare($category) {
        return $this->dareModel->getRandomByCategory($category);
    }

    public function getRandomDare() {
        return $this->dareModel->getRandomDare();
    }

    public function handle($userId, $category = 'Solo') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['completed'])) {
            $xpToAdd = (int)$_POST['xp'];
            $this->userModel->addXp($userId, $xpToAdd);
            header("Location: solo.php");
            exit();
        }

        $user = $this->userModel->getById($userId);
        $totalXP = $user['xp'];
        $level = $user['level'];

        $calculatedLevel = floor($totalXP / 100) + 1;
        if ($calculatedLevel > $level) {
            $this->userModel->updateLevel($userId, $calculatedLevel);
            $level = $calculatedLevel;
        }

        $xpThisLevel = $totalXP % 100;
        $progressPercent = min(100, ($xpThisLevel / 100) * 100);
        $dare = $this->dareModel->getRandomByCategory($category);

        return compact('dare', 'level', 'xpThisLevel', 'progressPercent');
    }

    public function getOrCreateVersusMatch($userId) {
        $stmt = $this->pdo->prepare("SELECT vm.*, d.title, d.description 
            FROM versus_matches vm 
            JOIN dares d ON vm.dare_id = d.id 
            WHERE (player1_id = ? OR player2_id = ?) 
            ORDER BY vm.created_at DESC LIMIT 1");
        $stmt->execute([$userId, $userId]);
        $match = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($match && $match['status'] === 'completed') {
            $seenKey = 'seen_match_' . $match['id'];
            if (isset($_SESSION[$seenKey])) {
                $this->deleteMatch($match['id']);
                unset($_SESSION[$seenKey]);
                header("Location: versus.php");
                exit();
            } else {
                $_SESSION[$seenKey] = true;
                return $match;
            }
        }

        if (!$match || $match['status'] === 'deleted') {
            $stmt = $this->pdo->prepare("SELECT * FROM versus_matches WHERE status = 'waiting' AND player1_id != ? ORDER BY created_at ASC LIMIT 1");
            $stmt->execute([$userId]);
            $found = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($found) {
                $stmt = $this->pdo->prepare("UPDATE versus_matches SET player2_id = ?, status = 'active' WHERE id = ?");
                $stmt->execute([$userId, $found['id']]);
                return $this->getMatchWithDare($found['id']);
            } else {
                $dare = $this->dareModel->getRandomDare();
                $stmt = $this->pdo->prepare("INSERT INTO versus_matches (player1_id, dare_id) VALUES (?, ?)");
                $stmt->execute([$userId, $dare['id']]);
                return $this->getMatchWithDare($this->pdo->lastInsertId());
            }
        }

        return $match;
    }

    public function getMatchWithDare($matchId) {
        $stmt = $this->pdo->prepare("SELECT vm.*, d.title, d.description 
            FROM versus_matches vm 
            JOIN dares d ON vm.dare_id = d.id 
            WHERE vm.id = ?");
        $stmt->execute([$matchId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkWinner($matchId) {
        $stmt = $this->pdo->prepare("SELECT status, winner_id FROM versus_matches WHERE id = ?");
        $stmt->execute([$matchId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function submitWinner($userId, $matchId) {
        $stmt = $this->pdo->prepare("UPDATE versus_matches SET winner_id = ?, status = 'completed' WHERE id = ? AND winner_id IS NULL");
        $stmt->execute([$userId, $matchId]);
    }

    public function deleteMatch($matchId) {
        $stmt = $this->pdo->prepare("DELETE FROM versus_matches WHERE id = ?");
        $stmt->execute([$matchId]);
    }

    public function resetCompletedMatches($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM versus_matches WHERE (player1_id = ? OR player2_id = ?) AND status = 'completed'");
        $stmt->execute([$userId, $userId]);
    }
}
