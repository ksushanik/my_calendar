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
