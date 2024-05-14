<?php
// src/Controller/AuthController.php

require_once __DIR__ . '/../Model/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function showLoginForm() {
        // Подключение HTML шаблона с формой входа
        include_once __DIR__ . '/../View/loginForm.html';
    }

    public function login() {
        session_start();
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if ($this->userModel->checkCredentials($username, $password)) {
            // Установка пользовательских данных в сессии
            $_SESSION['user_id'] = $this->userModel->getIdByUsername($username);
            $_SESSION['username'] = $username;

            // Перенаправление на главную страницу календаря
            header("Location: /");
            exit();
        } else {
            // Возвращение на форму входа с сообщением об ошибке
            $error = 'Неправильный логин или пароль.';
            include_once __DIR__ . '/../View/loginForm.html';
        }
    }

    public function logout() {
        // Очистка пользовательских данных из сессии
        session_start();
        $_SESSION = [];
        session_destroy();

        // Перенаправление на страницу входа
        header("Location: /login");
        exit();
    }

    public function showRegisterForm() {
        include_once __DIR__ . '/../View/registerForm.html';
    }

    public function register() {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];

        if ($this->userModel->register($username, $password, $email)) {
            // Перенаправляем на главную страницу после успешной регистрации
            header("Location: /");
            exit();
        } else {
            // Возвращаем на форму регистрации с сообщением об ошибке
            $error = 'Ошибка при регистрации. Пожалуйста, попробуйте еще раз.';
            include_once __DIR__ . '/../View/registerForm.html';
        }
    }
}
