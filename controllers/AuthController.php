<?php

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function login($usernameOrEmail, $password) {
        $user = $this->userModel->findByUsernameOrEmail($usernameOrEmail);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }

        return false;
    }

    public function register($username, $email, $password) {
        if ($this->userModel->exists($username, $email)) {
            return "Username or Email already taken.";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $success = $this->userModel->create($username, $email, $hashedPassword);

        return $success ? true : "Registration failed. Try again.";
    }
}
