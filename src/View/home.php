<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мой календарь</title>
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/scripts.js" defer></script>
    <script src="/js/modal.js" defer></script>
</head>
<body>
<div class="container">
    <header>
        <div class="user-logout-block">
            <?php if (isset($_SESSION['user_id']) && isset($_SESSION['username'])): ?>
                <span>Привет, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                | <a href='/logout'>Выйти</a>
            <?php endif; ?>
        </div>
        <h1>Мой календарь</h1>
    </header>

    <section class="new-task">
        <h2>Новая задача</h2>
        <form id="new-task-form" method="post">
            <div>
                <label for="subject">Тема:</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            <div>
                <label for="type">Тип задачи:</label>
                <select id="type" name="type">
                    <option value="встреча">Встреча</option>
                    <option value="звонок">Звонок</option>
                    <option value="совещание">Совещание</option>
                    <option value="дело">Дело</option>
                </select>
            </div>
            <div>
                <label for="location">Местоположение:</label>
                <input type="text" id="location" name="location">
            </div>
            <div>
                <label for="start_time">Дата и время:</label>
                <input type="date" id="start_time" name="start_time" required>
            </div>
            <div>
                <label for="duration">Длительность:</label>
                <input type="text" id="duration" name="duration" required>
            </div>
            <div>
                <label for="comment">Комментарий:</label>
                <textarea id="comment" name="comment"></textarea>
            </div>
            <div>
                <label for="status">Статус:</label>
                <select id="status" name="status">
                    <option value="текущее">Текущее</option>
                    <option value="просроченное">Просроченное</option>
                    <option value="выполненное">Выполненное</option>
                </select>
            </div>
            <div>
                <button type="submit">Добавить</button>
            </div>
        </form>
    </section>
    <section class="task-filter">
        <div class="filters-row">
            <div class="filter-status">
                <!-- Выпадающий список для выбора статуса -->
                <!--                <label for="filter-status">Статус:</label>-->
                <select id="filter-status" name="status">
                    <option value="">Все</option>
                    <option value="текущее">Текущее</option>
                    <option value="просроченное">Просроченное</option>
                    <option value="выполненное">Выполненное</option>
                </select>
            </div>
            <div class="filter-date">
                <!--                <label for="filter-date">Дата:</label>-->
                <input type="date" id="filter-date" name="date" value="">
            </div>
            <div class="date-filter-tabs">
                <button class="date-filter-tab" data-date="today">Сегодня</button>
                <button class="date-filter-tab" data-date="tomorrow">Завтра</button>
                <button class="date-filter-tab" data-date="this-week">На эту неделю</button>
                <button class="date-filter-tab" data-date="next-week">На след. неделю</button>
            </div>
        </div>
    </section>
    <section class="task-list">
        <h2>Список задач</h2>
        <table id="tasks">
            <thead>
            <tr>
                <th>Тип</th>
                <th>Задача</th>
                <th>Местоположение</th>
                <th>Дата и время</th>
                <th>Комментарий</th>
            </tr>
            </thead>
            <tbody>
            <!-- Вывод данных о задачах из базы данных -->
            <?php foreach ($events as $event): ?>
                <tr class="task-row" data-id="<?= htmlspecialchars($event['id']) ?>" data-type="<?= htmlspecialchars($event['type']) ?>" data-subject="<?= htmlspecialchars($event['subject']) ?>" data-location="<?= htmlspecialchars($event['location']) ?>" data-start-time="<?= htmlspecialchars($event['start_time']) ?>" data-comment="<?= htmlspecialchars($event['comment']) ?>">
                    <td><?= htmlspecialchars($event['type']) ?></td>
                    <td><?= htmlspecialchars($event['subject']) ?></td>
                    <td><?= htmlspecialchars($event['location']) ?></td>
                    <td><?= htmlspecialchars($event['start_time']) ?></td>
                    <td><?= htmlspecialchars($event['comment']) ?></td>
                    <td>
                        <!-- Кнопки или иконки для редактирования и удаления, если необходимо -->
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>

<?php include 'task.html'; ?>

</body>
</html>