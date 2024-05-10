document.addEventListener('DOMContentLoaded', function () {
    // Код для обработки создания новой задачи
    const form = document.getElementById('new-task-form');
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        let formData = new FormData(form);
        let jsonObject = {};

        for (const [key, value] of formData.entries()) {
            jsonObject[key] = value;
        }

        fetch('/events/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(jsonObject)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Событие успешно добавлено!');
                    form.reset();

                    // Создаем новую строку в таблице
                    var table = document.getElementById("tasks"); // Получаем таблицу по ID
                    var row = table.insertRow(-1); // Вставляем новую строку в конец таблицы
                    var typeCell = row.insertCell(0);
                    var subjectCell = row.insertCell(1);
                    var locationCell = row.insertCell(2);
                    var startTimeCell = row.insertCell(3);
                    var commentCell = row.insertCell(4);

                    // Заполняем ячейки данными формы. Замените jsonObject, если сервер возвращает более подробные данные.
                    typeCell.innerHTML = jsonObject.type;
                    subjectCell.innerHTML = jsonObject.subject;
                    locationCell.innerHTML = jsonObject.location;
                    startTimeCell.innerHTML = jsonObject.start_time.replace("T", " ") + ":00";
                    commentCell.innerHTML = jsonObject.comment;
                    // actionCell.innerHTML = '...'; // Здесь может быть кнопки для редактирования или удаления

                    // Возможно стоит обновить список событий на странице или получить их снова с сервера
                } else {
                    alert('Ошибка добавления задачи: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
            });
    });

    // Находим элемент выбора даты в DOM
    const dateFilter = document.getElementById('filter-date');

    // Функция для выполнения фильтрации
    function performFiltering() {
        const status = document.getElementById('filter-status').value;
        const date = dateFilter.value; // Получаем значение даты

        let url = `/events/filter?status=${encodeURIComponent(status)}`;
        if (date) {
            url += `&date=${encodeURIComponent(date)}`;
        }

        fetch(url, { method: 'GET' })
            .then(response => response.json())
            .then(data => {
                updateTasks(data);  // Функция обновления таблицы задач на основе полученных данных
            })
            .catch(error => {
                console.error('Ошибка:', error);
            });
    }

    // Устанавливаем текущую дату в качестве значения по умолчанию для фильтра даты
    const today = new Date().toISOString().split('T')[0];
    dateFilter.value = today;

    // Обработчик изменения значения даты
    dateFilter.addEventListener('change', performFiltering);

    // Обработчик изменения значения статуса (уже должен быть в вашем коде)
    const statusSelect = document.getElementById('filter-status');
    statusSelect.addEventListener('change', performFiltering);

    // Вызываем фильтрацию по дате при первой загрузке страницы
    performFiltering();


});

function updateTasks(tasks) {
    var table = document.getElementById("tasks");
    // Очищаем таблицу, кроме заголовка
    while (table.rows.length > 1) {
        table.deleteRow(1);
    }
    // Добавляем задачи в таблицу
    tasks.forEach(task => {
        var row = table.insertRow(-1);
        // Заполняем ячейки. Используйте индексы колонок в соответствии с вашей таблицей
        // вместо 'task.type' используйте актуальные ключи объекта 'task', полученного с сервера
        row.insertCell(0).innerHTML = task.type;
        row.insertCell(1).innerHTML = task.subject;
        row.insertCell(2).innerHTML = task.location;
        row.insertCell(3).innerHTML = new Date(task.start_time).toLocaleString();
        row.insertCell(4).innerHTML = task.comment;
        // row.insertCell(5).innerHTML = 'Действия'; // Это если вам нужна колонка с действиями
    });
}