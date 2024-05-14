<?php
// src/Model/User.php

require_once 'BaseModel.php';

class User extends BaseModel {

    public function checkCredentials($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($user = $result->fetch_assoc()) {
                if (password_verify($password, $user['password'])) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getIdByUsername($username) {
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($user = $result->fetch_assoc()) {
                    return $user['id'];
                }
            }
            $stmt->close();
        }
        return null;
    }

    public function register($username, $password, $email) {
        // Подготовьте SQL-запрос для вставки нового пользователя в базу данных
        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        // Хэширование пароля перед сохранением в базу данных
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt->bind_param("sss", $username, $hashedPassword, $email);

        if ($stmt->execute()) {
            // Успешная регистрация
            return true;
        } else {
            // Не удалось зарегистрировать пользователя
            return false;
        }
    }
}
