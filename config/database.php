<?php
// Данные для подключения к базе данных
$servername = "localhost";
$username = "root";
$password = "123";
$dbname = "my_calendar";
$port = 3308;

// Создание соединения с базой данных
function connectDB($servername, $username, $password, $dbname, $port)
{
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Вызов функции connectDB() для установки соединения с базой данных
$conn = connectDB($servername, $username, $password, $dbname, $port);
