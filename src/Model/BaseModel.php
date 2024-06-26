<?php
// Model/BaseModel.php

require_once __DIR__ . '/../../config/database.php';

class BaseModel {
    protected $conn;

    public function __construct() {
        global $conn; // Используем подключение, созданное в database.php
        $this->conn = $conn;
    }
}