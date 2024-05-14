<?php
// src/Controller/EventsController.php
require_once __DIR__ . '/../Model/Event.php';

class EventsController {
    private $eventModel;

    public function __construct() {
        $this->eventModel = new Event();
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $events = $this->eventModel->getAll($user_id);
        include_once __DIR__ . '/../View/home.php';
    }

    public function store() {
        session_start();
        $data = json_decode(file_get_contents('php://input'), true);
        $data['status'] = isset($data['status']) ? $data['status'] : 'текущее'; // Если не передано, используется значение по умолчанию

        if (!empty($data) && isset($_SESSION['user_id'])) {
            $data['user_id'] = $_SESSION['user_id'];
            $result = $this->eventModel->create($data);
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Событие успешно добавлено.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении события.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Некорректные входные данные.']);
        }
        exit();
    }

    public function update($id) {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Недостаточно прав.']);
            exit();
        }

        $user_id = $_SESSION['user_id']; // Получение ID пользователя из сессии
        $data = json_decode(file_get_contents('php://input'), true);

        // Проверяем, что событие принадлежит текущему пользователю, перед тем как обновлять
        if (!empty($data) && isset($id) && $this->eventModel->belongsToUser($id, $user_id)) {
            // Передаем user_id в метод обновления
            $result = $this->eventModel->update($id, $data, $user_id);

            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Событие успешно обновлено.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении события.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Некорректные входные данные или недостаточно прав.']);
        }
        exit();
    }

    /**
     * @throws Exception
     */
    public function filterEvents() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Недостаточно прав.']);
            exit();
        }

        $user_id = $_SESSION['user_id']; // Получаем user_id из сессии
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

        // Передаем user_id в метод getFilteredEvents
        $events = $this->eventModel->getFilteredEvents($user_id, $status, $startDate, $endDate);

        header('Content-Type: application/json');
        echo json_encode($events);
    }

    public function show($id) {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            // У пользователя нет прав на удаление, если он не в сессии
            echo json_encode(['success' => false, 'message' => 'Недостаточно прав.']);
            exit();
        }
        $task = $this->eventModel->findById($id);

        header('Content-Type: application/json');
        if ($task) {
            echo json_encode($task);
        } else {
            echo json_encode(['success' => false, 'message' => 'Событие не найдено.']);
        }
        exit();
    }

    public function delete($id) {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            // У пользователя нет прав на удаление, если он не в сессии
            echo json_encode(['success' => false, 'message' => 'Недостаточно прав.']);
            exit();
        }
        $user_id = $_SESSION['user_id'];
        if (isset($id)) {
            $result = $this->eventModel->delete($id, $user_id);

            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Событие успешно удалено.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при удалении события.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Не указан идентификатор события.']);
        }
        exit();
    }
}
