<?php

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function findByUsernameOrEmail($usernameOrEmail) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :ue OR email = :ue");
        $stmt->execute(['ue' => $usernameOrEmail]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function exists($username, $email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($username, $email, $hashedPassword) {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword
        ]);
    }

    public function getTopUsers($limit = 100) {
        $stmt = $this->pdo->prepare("SELECT username, level, xp FROM users ORDER BY level DESC, xp DESC LIMIT ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function addXp($userId, $xpToAdd) {
        $stmt = $this->pdo->prepare("UPDATE users SET xp = xp + ? WHERE id = ?");
        $stmt->execute([$xpToAdd, $userId]);
    }
    public function updateLevel($userId, $newLevel) {
        $stmt = $this->pdo->prepare("UPDATE users SET level = ? WHERE id = ?");
        $stmt->execute([$newLevel, $userId]);
    }

}
