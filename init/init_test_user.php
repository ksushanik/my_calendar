<?php
require_once __DIR__ . '/../config/database.php';

// Тестовые данные пользователя
$username = 'user';
$passwordPlain = '123';
$email = 'testuser@example.com';

// Хеширование пароля
$passwordHashed = password_hash($passwordPlain, PASSWORD_DEFAULT);

// SQL запрос для добавления тестового пользователя
$sqlInsertUser = "INSERT INTO `users` (`username`, `password`, `email`) VALUES (?, ?, ?);";

// Подготовка запроса
$stmt = $conn->prepare($sqlInsertUser);
if ($stmt) {
    // Привязка параметров
    $stmt->bind_param("sss", $username, $passwordHashed, $email);

    // Выполнение запроса
    if ($stmt->execute()) {
        echo "Test user '$username' created successfully<br/>";
    } else {
        echo "Error creating user '$username': " . $stmt->error;
    }

    // Закрытие выражения
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

// Закрытие соединения с базой данных
$conn->close();
