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
        // Предположим, что все ключи в $data совместимы со столбцами вашей таблицы
        $sql = "INSERT INTO events (subject, type, location, start_time, duration, comment, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("sssssss",
                $data['subject'], $data['type'], $data['location'],
                $data['start_time'], $data['duration'], $data['comment'],
                $data['status']);
            if ($stmt->execute()) {
                $stmt->close();
                return true; // Успех
            } else {
                $stmt->close();
                return false; // Ошибка выполнения
            }
        }
        return false; // Ошибка подготовки
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

    /**
     * @throws Exception
     */
    public function getFilteredEvents($status, $startDate, $endDate) {
        $queryParams = [];
        $types = '';
        $query = "SELECT * FROM events WHERE 1=1";

        if (!empty($status)) {
            $query .= " AND status = ?";
            $queryParams[] = $status;
            $types .= 's';
        }

        if (!empty($startDate)) {
            $query .= " AND start_time >= ?";
            $queryParams[] = $startDate;
            $types .= 's';
        }

        if (!empty($endDate)) {
            // Создаем объект DateTime из $endDate
            $endDateTime = new DateTime($endDate);

            // Добавляем один день
            $endDateTime->modify('+1 day');

            // Форматируем обратно в строку для использования в запросе
            $endDateWithOneMoreDay = $endDateTime->format('Y-m-d');

            // Используем новую дату в запросе
            $query .= " AND start_time < ?";
            $queryParams[] = $endDateWithOneMoreDay;
            $types .= 's';
        }

        $stmt = $this->conn->prepare($query);

        if ($stmt) {
            if (!empty($queryParams)) {
                $bindNames[] = $types;
                for ($i = 0; $i < count($queryParams); $i++) {
                    $bindName = 'bind' . $i;
                    $$bindName = &$queryParams[$i];
                    $bindNames[] = &$$bindName;
                }
                call_user_func_array([$stmt, 'bind_param'], $bindNames);
            }

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $events = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                return $events;
            } else {
                $stmt->close();  // Закрываем выражение
                return [];       // Возвращаем пустой результат при ошибке выполнения
            }
        } else {
            return []; // Возвращаем пустой результат при ошибке подготовки запроса
        }
    }
}
