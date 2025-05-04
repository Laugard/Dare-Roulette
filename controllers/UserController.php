<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function getLeaderboard($limit = 100) {
        return $this->userModel->getTopUsers($limit);
    }
    public function getProfile($id) {
        return $this->userModel->getById($id);
    }

}
