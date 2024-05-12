<?php
// public/index.php

require_once __DIR__ . '/../src/Router.php';
require_once __DIR__ . '/../src/Controller/EventsController.php';

$router = new Router();
$eventsController = new EventsController();

// Задаем базовый путь для ресурсов, чтобы браузер мог правильно их найти.
$baseUrl = '/public';

// Добавляем маршруты в роутер
$router->get('/', [$eventsController, 'index']);

// Обработка добавления нового события
$router->post('/events/store', [$eventsController, 'store']);

// Маршрут для фильтрации событий
$router->get('/events/filter', [$eventsController, 'filterEvents']);

// Маршрут для обновления события
$router->post('/events/([0-9]+)/update', function ($id) use ($eventsController) {
    $eventsController->update($id);
});

// Маршрут для получения события по id
$router->get('/events/([0-9]+)', function ($id) use ($eventsController) {
    $eventsController->show($id);
});

// Маршрут для удаления события
$router->delete('/events/([0-9]+)/delete', function ($id) use ($eventsController) {
    $eventsController->delete($id);
});

// Запускаем маршрутизатор для обработки текущего запроса.
$router->dispatch();