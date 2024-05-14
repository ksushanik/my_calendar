<?php
// public/index.php

require_once __DIR__ . '/../src/Router.php';
require_once __DIR__ . '/../src/Controller/EventsController.php';
require_once __DIR__ . '/../src/Controller/AuthController.php';

$router = new Router();
$eventsController = new EventsController();
$authController = new AuthController();

// Задаем базовый путь для ресурсов, чтобы браузер мог правильно их найти.
$baseUrl = '/public';

// Маршруты для авторизации и регистрации
$router->get('/login', [$authController, 'showLoginForm']);
$router->post('/login', [$authController, 'login']);
$router->get('/logout', [$authController, 'logout']);
$router->get('/register', [$authController, 'showRegisterForm']);
$router->post('/register', [$authController, 'register']);

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