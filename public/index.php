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

// Добавьте другие маршруты для редактирования, удаления, просмотра событий и т.д.

// Запускаем маршрутизатор для обработки текущего запроса.
$router->dispatch();

