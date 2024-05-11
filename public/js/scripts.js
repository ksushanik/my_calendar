// public/js/scripts.js
document.addEventListener('DOMContentLoaded', function () {
    const formEl = document.getElementById('new-task-form');
    const statusSelectEl = document.getElementById('filter-status');
    const dateFilterEl = document.getElementById('filter-date');
    const tasksTableEl = document.getElementById("tasks");

    // Инициализируем переменные для фильтров
    let currentStatus = document.getElementById('filter-status').value;
    let currentPeriod = 'today';

    formEl.addEventListener('submit', function (event) {
        event.preventDefault();

        let formData = new FormData(formEl);
        let jsonObject = {};
        for (const [key, value] of formData.entries()) {
            jsonObject[key] = value;
        }

        fetch('/events/store', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(jsonObject)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Событие успешно добавлено!');
                    formEl.reset();
                    mainFilterHandler(); // Обновление списка задач после добавления
                } else {
                    alert('Ошибка добавления задачи: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
            });
    });


    statusSelectEl.addEventListener('change', mainFilterHandler);
    dateFilterEl.addEventListener('change', function () {
        currentPeriod = 'custom-date';
        mainFilterHandler();
    });

    document.querySelectorAll('.date-filter-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            const selectedPeriod = this.getAttribute('data-date');
            performDateFiltering(selectedPeriod);
            setActiveTab(selectedPeriod);
        });
    });

    function mainFilterHandler() {
        currentStatus = document.getElementById('filter-status').value;
        // Если период не 'custom-date', берем выбранную дату из dateFilterEl.
        if (currentPeriod === 'custom-date') {
            const customDate = dateFilterEl.value;
            performDateFiltering(currentPeriod, customDate);
        } else {
            performDateFiltering(currentPeriod);
        }
    }

    function performDateFiltering(period, customDate = null) {
        currentPeriod = period; // Обновляем текущий период при каждом вызове

        const {startDate, endDate} = calculatePeriod(period, customDate);

        if (startDate && endDate) {
            let url = `/events/filter?status=${encodeURIComponent(currentStatus)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

            fetch(url, {method: 'GET'})
                .then(response => response.json())
                .then(data => {
                    updateTasks(data); // Функция обновления таблицы задач на основе полученных данных
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                });
        }
    }

    // Обработчики событий для статуса и вкладок даты будут вызывать mainFilterHandler
    document.getElementById('filter-status').addEventListener('change', mainFilterHandler);
    document.querySelectorAll('.date-filter-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            const selectedPeriod = this.getAttribute('data-date');
            setActiveTab(selectedPeriod);
            currentPeriod = selectedPeriod;
            mainFilterHandler(); // Обновляем, используя измененный период
        });
    });

    function setActiveTab(selectedPeriod) {
        document.querySelectorAll('.date-filter-tab').forEach(tab => {
            if (tab.getAttribute('data-date') === selectedPeriod) {
                tab.classList.add('active');
            } else {
                tab.classList.remove('active');
            }
        });
    }

    // Вызов обработчика фильтрации при инициализации для отображения текущих задач
    mainFilterHandler();

    function calculatePeriod(period, customDate = null) {
        const startDate = new Date();
        let endDate = new Date(startDate);

        if (period === 'tomorrow') {
            startDate.setDate(startDate.getDate() + 1);
            endDate.setDate(endDate.getDate() + 1);
        } else if (period === 'this-week') {
            const dayOfWeek = startDate.getDay();
            const difference = startDate.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
            startDate.setDate(difference);
            endDate.setDate(difference + 6);
        } else if (period === 'next-week') {
            const nextMonday = startDate.getDate() - startDate.getDay() + 7 + (startDate.getDay() === 0 ? -6 : 1);
            startDate.setDate(nextMonday);
            endDate.setDate(nextMonday + 6);
        } else if (period === 'custom-date' && customDate) {
            startDate.setTime(Date.parse(customDate));
            endDate.setTime(Date.parse(customDate));
        }

        // Форматируем даты в 'YYYY-MM-DD'
        const formattedStartDate = formatDateToYYYYMMDD(startDate);
        const formattedEndDate = formatDateToYYYYMMDD(endDate);

        return {
            startDate: formattedStartDate,
            endDate: formattedEndDate
        };
    }

    function updateTasks(tasks) {
        while (tasksTableEl.rows.length > 1) {
            tasksTableEl.deleteRow(1);
        }

        tasks.forEach(task => {
            let row = tasksTableEl.insertRow(-1);

            // Убедитесь, что все следующие атрибуты data-* существуют и задаются корректно
            row.setAttribute('data-id', task.id); // Пример значения: task.id
            row.setAttribute('data-type', task.type);
            row.setAttribute('data-subject', task.subject);
            row.setAttribute('data-location', task.location);
            row.setAttribute('data-start-time', task.start_time);
            row.setAttribute('data-comment', task.comment);

            // Устанавливаем класс для строки
            row.className = 'task-row';

            // Заполнение информации о задаче
            row.insertCell(0).textContent = task.type;
            row.insertCell(1).textContent = task.subject;
            row.insertCell(2).textContent = task.location;
            row.insertCell(3).textContent = new Date(task.start_time).toLocaleString();
            row.insertCell(4).textContent = task.comment;
        });

        // Не забывайте вызывать эту функцию после добавления всех строк в таблицу
        setupTaskClickHandlers();
    }

    function formatDateToYYYYMMDD(date) {
        const yyyy = date.getFullYear().toString();
        const mm = (date.getMonth() + 1).toString().padStart(2, '0');
        const dd = date.getDate().toString().padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }
});

