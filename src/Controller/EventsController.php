<?php
// src/Controller/EventsController.php
require_once __DIR__ . '/../Model/Event.php';

class EventsController {
    private $eventModel;

    public function __construct() {
        $this->eventModel = new Event();
    }

    public function index() {
        $events = $this->eventModel->getAll();

        // Подключение шаблона
        include_once __DIR__ . '/View/home.php';
    }

    public function store() {
        $data = json_decode(file_get_contents('php://input'), true);
        $data['status'] = isset($data['status']) ? $data['status'] : 'текущее'; // Если не передано, используется значение по умолчанию

        if (!empty($data)) {
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
        // Получаем данные из тела запроса
        $data = json_decode(file_get_contents('php://input'), true);

        // Проверяем, что $data не пустой массив и $id задан
        if (!empty($data) && isset($id)) {
            $result = $this->eventModel->update($id, $data);

            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Событие успешно обновлено.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении события.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Некорректные входные данные.']);
        }
        exit();
    }

    public function filterEvents() {
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

        $events = $this->eventModel->getFilteredEvents($status, $start_date, $end_date);

        header('Content-Type: application/json');
        echo json_encode($events);
    }
}
