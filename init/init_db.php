<?php
/** @var $conn mysqli */
require_once __DIR__ . '/../config/database.php';

// Удаление существующих таблиц, если они есть
$sqlDrop = "DROP TABLE IF EXISTS `events`";
if ($conn->query($sqlDrop) === TRUE) {
    echo "Existing tables dropped successfully<br>";
} else {
    echo "Error dropping tables: " . $conn->error;
}

// Создание новых таблиц
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

// Выполнение запросов
if ($conn->query($sqlCreateEvents) === TRUE) {
    echo "Table 'events' created successfully<br>";
} else {
    echo "Error creating table 'events': " . $conn->error;
}

$conn->close();