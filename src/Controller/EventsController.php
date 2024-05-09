<?php
// src/Controller/EventsController.php
require_once __DIR__ . '/../Model/Event.php';

class EventsController
{
    private $eventModel;

    public function __construct()
    {
        $this->eventModel = new Event();
    }

    public function index()
    {
        $events = $this->eventModel->getAll();

        // Здесь предполагается, что у вас будет метод для подключения вашего шаблона
        include_once __DIR__ . '/View/home.php';
    }

    public function store()
    {
        // Пример получения данных из POST запроса
        $subject = $_POST['subject'];
        $type = $_POST['type'];
        // Добавьте другие параметры

        // Предполагается, что метод create() вашей модели добавляет новое событие в базу данных
        $result = $this->eventModel->create([
            'subject' => $subject,
            'type' => $type,
            // Добавьте другие параметры
        ]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Событие добавлено']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Произошла ошибка при добавлении события']);
        }
    }
}
