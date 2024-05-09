<?php
// models/Event.php

/** @var $conn mysqli */
require_once 'BaseModel.php';

class Event extends BaseModel {

    public function getAll() {
        $sql = "SELECT * FROM events";
        $events = [];
        if ($result = $this->conn->query($sql)) {
            while ($row = $result->fetch_assoc()) {
                $events[] = $row;
            }
            $result->free(); // Освободить память.
        }
        return $events;
    }

    public function findById($id) {
        $sql = "SELECT * FROM events WHERE id = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $event = $result->fetch_assoc();
                $stmt->close();
                return $event;
            } else {
                $stmt->close();
                // В реальных условиях здесь лучше бросить исключение
                return false;
            }
        } else {
            // В реальных условиях здесь лучше бросить исключение
            return false;
        }
    }

    public function create($data) {
        $sql = "INSERT INTO events (subject, type, location, start_time, duration, comment, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("sssssss",
                $data['subject'], $data['type'], $data['location'],
                $data['start_time'], $data['duration'], $data['comment'],
                $data['status']);
            if ($stmt->execute()) {
                $lastId = $stmt->insert_id;
                $stmt->close();
                return $lastId;
            } else {
                $stmt->close();
                // В реальных условиях здесь лучше бросить исключение
                return false;
            }
        } else {
            // В реальных условиях здесь лучше бросить исключение
            return false;
        }
    }

    public function update($id, $data) {
        $sql = "UPDATE events SET subject = ?, type = ?, location = ?, start_time = ?, duration = ?, comment = ?, status = ? WHERE id = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("sssssssi",
                $data['subject'], $data['type'], $data['location'],
                $data['start_time'], $data['duration'], $data['comment'],
                $data['status'], $id);
            if ($stmt->execute()) {
                $updated = $stmt->affected_rows > 0;
                $stmt->close();
                return $updated;
            } else {
                $stmt->close();
                // В реальных условиях здесь лучше бросить исключение
                return false;
            }
        } else {
            // В реальных условиях здесь лучше бросить исключение
            return false;
        }
    }

    public function delete($id) {
        $sql = "DELETE FROM events WHERE id = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $deleted = $stmt->affected_rows > 0;
                $stmt->close();
                return $deleted;
            } else {
                $stmt->close();
                // В реальных условиях здесь лучше бросить исключение
                return false;
            }
        } else {
            // В реальных условиях здесь лучше бросить исключение
            return false;
        }
    }
}
