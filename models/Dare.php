<?php
class Dare {
    private $pdo;


    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getRandomByCategory($category) {
        $stmt = $this->pdo->prepare("SELECT * FROM dares WHERE category = ? ORDER BY RAND() LIMIT 1");
        $stmt->execute([$category]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getRandomDare() {
        $stmt = $this->pdo->query("SELECT id, title, description FROM dares ORDER BY RAND() LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}