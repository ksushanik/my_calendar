<?php
// models/Event.php

/** @var $conn mysqli */
require_once 'BaseModel.php';

class Event extends BaseModel {

    public function getAll($user_id) {
        // Теперь метод принимает user_id
        $sql = "SELECT * FROM events WHERE user_id = ?";
        $events = [];

        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param('i', $user_id); // Привязываем user_id к запросу
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $events[] = $row;
                }
                $result->free(); // Освободить память.
            }
            $stmt->close();
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
        $sql = "INSERT INTO events (subject, type, location, start_time, duration, comment, status, user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("sssssssi",
                $data['subject'], $data['type'], $data['location'],
                $data['start_time'], $data['duration'], $data['comment'],
                $data['status'], $data['user_id']); // Добавляем user_id в привязку параметров

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

    public function update($id, $data, $user_id) {
        // Теперь $user_id передается в качестве параметра
        $sql = "UPDATE events SET subject = ?, type = ?, location = ?, start_time = ?, duration = ?, comment = ?, status = ? WHERE id = ? AND user_id = ?";

        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("sssssssii",
                $data['subject'], $data['type'], $data['location'],
                $data['start_time'], $data['duration'], $data['comment'],
                $data['status'], $id, $user_id); // Обновляем привязку параметров

            if ($stmt->execute()) {
                $updated = $stmt->affected_rows > 0;
                $stmt->close();
                return $updated;
            } else {
                $stmt->close();
                return false; // Ошибка выполнения
            }
        } else {
            return false; // Ошибка подготовки
        }
    }

    public function delete($id, $user_id) {
        $sql = "DELETE FROM events WHERE id = ? AND user_id = ?";

        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("ii", $id, $user_id); // Обновляем привязку параметров

            if ($stmt->execute()) {
                $deleted = $stmt->affected_rows > 0;
                $stmt->close();
                return $deleted;
            } else {
                $stmt->close();
                return false; // Ошибка выполнения
            }
        } else {
            return false; // Ошибка подготовки
        }
    }

    /**
     * @throws Exception
     */
    public function getFilteredEvents($user_id, $status, $startDate, $endDate) {
        $queryParams = [$user_id];
        $types = 'i';
        $query = "SELECT * FROM events WHERE user_id = ?";


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
            $bindNames[] = $types;
            for ($i = 0; $i < count($queryParams); $i++) {
                $bindName = 'bind' . $i;
                $$bindName = &$queryParams[$i];
                $bindNames[] = &$$bindName;
            }
            call_user_func_array([$stmt, 'bind_param'], $bindNames);

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

    public function belongsToUser($id, $user_id) {
        $sql = "SELECT id FROM events WHERE id = ? AND user_id = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("ii", $id, $user_id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->fetch_assoc()) {
                    return true; // Событие принадлежит пользователю
                }
            }
        }
        return false; // Событие не найдено или не принадлежит пользователю
    }
}
