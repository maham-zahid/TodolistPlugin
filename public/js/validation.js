// Validation and AJAX for Login and Registration Forms
function validateLoginForm() {
    clearErrors();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    let isValid = true;

    if (isEmpty(email)) {
        showError('email-error', 'Please enter your email');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showError('email-error', 'Please enter a valid email address');
        isValid = false;
    }

    if (isEmpty(password)) {
        showError('password-error', 'Please enter your password');
        isValid = false;
    } else if (getPasswordStrength(password) !== 'strong') {
        showError('password-error', 'Password must be at least 8 characters long and include at least one uppercase letter, one number, and one special character');
        isValid = false;
    }

    if (isValid) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', myPluginData.ajax_url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert(response.data.message);
                    window.location.href = 'http://todolistplugin.local/todo-list';
                } else {
                    alert(response.data.message);
                }
            }
        };
        xhr.send(`action=login_user&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&nonce=${myPluginData.customAuthNonce}`);
    }

    return false; // Prevent default form submission to wait for AJAX
}

function validateRegistrationForm(event) {
    clearErrors();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confpassword').value;
    let isValid = true;

    if (isEmpty(email)) {
        showError('email-error', 'Please enter your email');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showError('email-error', 'Please enter a valid email address');
        isValid = false;
    }

    if (isEmpty(password)) {
        showError('password-error', 'Please enter your password');
        isValid = false;
    } else if (getPasswordStrength(password) !== 'strong') {
        showError('password-error', 'Password must be at least 8 characters long and include at least one uppercase letter, one number, and one special character');
        isValid = false;
    }

    if (password !== confirmPassword) {
        showError('confpassword-error', 'Passwords do not match');
        isValid = false;
    }

    if (isValid) {
        checkUserExists(email, function(emailExists) {
            if (emailExists) {
                alert('User already exists.');
            } else {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', myPluginData.ajax_url, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            alert('Registration successful!');
                            window.location.href = 'http://todolistplugin.local/login-page';
                        } else {
                            alert(response.data.message);
                            window.location.href = 'http://todolistplugin.local/login-page';
                        }
                    }
                };
                xhr.send(`action=register_user&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&nonce=${myPluginData.customAuthNonce}`);
            }
        });
    }

    return false; // Prevent default form submission to wait for AJAX
}

function checkUserExists(email, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', myPluginData.ajax_url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            callback(response.exists);
        }
    };
    xhr.send(`action=check_user_exists&email=${encodeURIComponent(email)}&nonce=${myPluginData.customAuthNonce}`);
}

// Utility functions
function isEmpty(value) {
    return value.trim() === '';
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function getPasswordStrength(password) {
    const strongPasswordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+[\]{};':"\\|,.<>/?]).{8,}$/;
    return strongPasswordRegex.test(password) ? 'strong' : 'weak';
}

function showError(id, message) {
    document.getElementById(id).textContent = message;
}

function clearErrors() {
    const errors = document.querySelectorAll('.form__error');
    errors.forEach(error => error.textContent = '');
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle add task form submission
    document.getElementById('todo-form').addEventListener('submit', function(event) {
        event.preventDefault();

        let task = document.getElementById('todo-item').value;
        let nonce = myPluginData.todoListNonce;

        fetch(myPluginData.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'add_todo_task',
                nonce: nonce,
                task: task
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the task list after adding a task
                fetchTodoTasks();
            } else {
                alert(data.data.message);
            }
        });
    });

    // Handle task status change
    document.getElementById('todo-list').addEventListener('change', function(event) {
        if (event.target.classList.contains('todo-item__status')) {
            let taskId = event.target.closest('.todo-item').dataset.id;
            let status = event.target.value;
            let nonce = myPluginData.todoListNonce;

            fetch(myPluginData.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'update_todo_task',
                    nonce: nonce,
                    task_id: taskId,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the checkbox based on the status
                    let checkbox = event.target.closest('.todo-item').querySelector('.todo-item__checkbox');
                    checkbox.checked = (status === 'completed');
                    alert(data.data.message);
                } else {
                    alert(data.data.message);
                }
            });
        }
    });

    // Fetch and display todo tasks
    function fetchTodoTasks() {
        let nonce = myPluginData.todoListNonce;

        fetch(myPluginData.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'fetch_todo_tasks',
                nonce: nonce
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let tasks = data.data.tasks;
                let todoList = document.getElementById('todo-list');
                todoList.innerHTML = '';

                if (tasks.length > 0) {
                    tasks.forEach(task => {
                        let listItem = document.createElement('li');
                        listItem.className = 'todo-item';
                        listItem.dataset.id = task.id;
                        listItem.innerHTML = `
                            <input type="checkbox" class="todo-item__checkbox" ${task.status === 'completed' ? 'checked' : ''}>
                            <span class="todo-item__text">${task.task}</span>
                            <select class="todo-item__status">
                                <option value="pending" ${task.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="in_progress" ${task.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                                <option value="completed" ${task.status === 'completed' ? 'selected' : ''}>Completed</option>
                            </select>
                        `;
                        todoList.appendChild(listItem);
                    });
                } else {
                    todoList.innerHTML = '<li>No tasks available.</li>';
                }
            } else {
                alert(data.data.message);
            }
        });
    }

    // Fetch tasks when the page loads
    fetchTodoTasks();
});

