<?php
// init/init_db.php

/** @var $conn mysqli */
require_once __DIR__ . '/../config/database.php';

// Функция для удаления таблицы
function dropTable($conn, $tableName) {
    $sqlDrop = "DROP TABLE IF EXISTS `$tableName`;";
    if ($conn->query($sqlDrop) === TRUE) {
        echo "Table '$tableName' dropped successfully<br>";
    } else {
        echo "Error dropping table '$tableName': " . $conn->error . "<br>";
    }
}

// Функция для создания таблицы
function createTable($conn, $tableName, $sqlCreate) {
    if ($conn->query($sqlCreate) === TRUE) {
        echo "Table '$tableName' created successfully<br>";
    } else {
        echo "Error creating table '$tableName': " . $conn->error . "<br>";
    }
}

// Удаление существующих таблиц
dropTable($conn, 'events');
dropTable($conn, 'users');

// SQL запрос для создания таблицы событий
$sqlCreateEvents = "CREATE TABLE `events` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `subject` VARCHAR(255) NOT NULL,
  `type` ENUM('встреча', 'звонок', 'совещание', 'дело') NOT NULL,
  `location` VARCHAR(255) DEFAULT NULL,
  `start_time` DATETIME NOT NULL,
  `duration` TIME NOT NULL,
  `comment` TEXT,
  `status` ENUM('текущее', 'просроченное', 'выполненное') NOT NULL DEFAULT 'текущее',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

createTable($conn, 'events', $sqlCreateEvents);

// SQL запрос для создания таблицы пользователей
$sqlCreateUsers = "CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

createTable($conn, 'users', $sqlCreateUsers);

// Закрытие соединения с базой данных
$conn->close();