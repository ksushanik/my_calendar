// Получаем доступ к модальному окну и элементам внутри него
const editTaskModal = document.getElementById('editTaskModal');
const editTaskForm = document.getElementById('editTaskForm');
const closeButton = editTaskModal.querySelector('.close');

// Функция для открытия модального окна
function openEditModal(taskId, taskType, taskSubject, taskLocation, taskStartTime, taskComment) {
    console.log('Открытие модального окна с данными:', taskId, taskSubject);
    document.getElementById('editTaskId').value = taskId;
    document.getElementById('editTaskType').value = taskType;
    document.getElementById('editTaskName').value = taskSubject;
    document.getElementById('editTaskLocation').value = taskLocation;
    document.getElementById('editTaskStartTime').value = taskStartTime;
    document.getElementById('editTaskComment').value = taskComment;

    // Отображаем модальное окно
    editTaskModal.style.display = 'block';
}

function setupTaskClickHandlers() {
    const taskRows = document.querySelectorAll('.task-row');
    taskRows.forEach(row => {
        row.addEventListener('click', function() {
            // Извлекаем данные из data-* атрибутов
            console.log("Клик по строке с ID задачи:", this.dataset.id);
            const taskId = this.dataset.id;
            const taskType = this.dataset.type; // Убедитесь, что такие поля есть в модальном окне
            const taskSubject = this.dataset.subject;
            const taskLocation = this.dataset.location; // Такое поле тоже должно быть в модальном окне
            const taskStartTime = this.dataset.startTime; // И это поле тоже
            const taskComment = this.dataset.comment;

            // Вызываем функцию открытия модального окна и передаем туда все данные
            openEditModal(taskId, taskType, taskSubject, taskLocation, taskStartTime, taskComment);
        });
    });
}

// Функция для закрытия модального окна
function closeEditModal() {
    editTaskModal.style.display = 'none';
}

// Сохранение изменений задачи
editTaskForm.addEventListener('submit', function(event) {
    event.preventDefault();
    // Здесь код для обработки формы
    console.log('Форма отправлена!');
    // Для закрытия модального окна после сохранения
    closeEditModal();
});

// Событие для закрытия модального окна при нажатии на кнопку закрыть (X)
closeButton.addEventListener('click', function() {
    closeEditModal();
});

// Закрытие модального окна при клике вне его контента
window.addEventListener('click', function(event) {
    if (event.target === editTaskModal) {
        closeEditModal();
    }
});

// Функция для сохранения изменений задачи
editTaskForm.addEventListener('submit', function(event) {
    event.preventDefault();

    // Получаем данные из формы
    const taskId = document.getElementById('editTaskId').value;
    const updatedData = {
        subject: document.getElementById('editTaskName').value,
        type: document.getElementById('editTaskType').value,
        location: document.getElementById('editTaskLocation').value,
        start_time: document.getElementById('editTaskStartTime').value,
        comment: document.getElementById('editTaskComment').value,
        duration: document.getElementById('editTaskDuration').value,
        status: document.getElementById('editTaskStatus').value
    };

    // Отправляем запрос на сервер для обновления задачи
    fetch(`/events/${taskId}/update`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(updatedData)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Ошибка запроса: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Захватим строки таблицы с dataset ID
                const rowToUpdate = document.querySelector(`tr[data-id='${taskId}']`);

                if(rowToUpdate) {
                    rowToUpdate.cells[0].textContent = updatedData.type;
                    rowToUpdate.cells[1].textContent = updatedData.subject;
                    rowToUpdate.cells[2].textContent = updatedData.location;
                    rowToUpdate.cells[3].textContent = new Date(updatedData.start_time).toLocaleString();
                    rowToUpdate.cells[4].textContent = updatedData.comment;
                }

                alert('Событие успешно обновлено!');
                closeEditModal();
                // Здесь можно добавить код для обновления данных на странице без перезагрузки,
                // например, обновить текст соответствующей строки таблицы.
            } else {
                alert('Ошибка при обновлении события.');
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Не удалось обновить событие.');
        });
});

