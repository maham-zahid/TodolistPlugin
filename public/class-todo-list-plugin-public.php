<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Public site functionality of plugin
 */

class Todo_List {
    private $version = '1.0.0';

    public function __construct()
    {

    }

    // Enqueue styles and scripts
    public function enqueue_assets() {
        wp_enqueue_style('custom-authentication-css', plugin_dir_url(__FILE__) . 'css/styles.css');
        wp_enqueue_script('custom-authentication-js', plugin_dir_url(__FILE__) . 'js/validation.js', array('jquery'), null, true);
        
        wp_localize_script('custom-authentication-js', 'myPluginData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'customAuthNonce' => wp_create_nonce('custom-auth-nonce'),
            'todoListNonce' => wp_create_nonce('todo-list-nonce')
        ));
    }

    // Display registration form
    public function display_registration_form() {
        ob_start();
        ?>
        <div class="container">
            <div class="form-wrapper">
                <h2 class="form-wrapper__heading">Register User</h2>
                <form class="form" id="registerForm" method="POST" onsubmit="return validateRegistrationForm()">
                    <div class="form__group">
                        <label for="email" class="form__label">Email</label>
                        <input type="text" id="email" name="email" placeholder="abc@gmail.com" class="form__input" required>
                        <span class="form__error" id="email-error"></span>
                    </div>
                    <div class="form__group">
                        <label for="password" class="form__label">Password</label>
                        <input type="password" id="password" name="password" placeholder="Password" class="form__input" required>
                        <span class="form__error" id="password-error"></span>
                    </div>
                    <div class="form__group">
                        <label for="confirmPassword" class="form__label">Confirm Password</label>
                        <input type="password" id="confpassword" name="confirmPassword" placeholder="Confirm Password" class="form__input" required>
                        <span class="form__error" id="confpassword-error"></span>
                    </div>
                    <button type="submit" name="register" class="form__button">Register</button>
                    <p class="form__text">Already have an account? <a href="<?php echo site_url('/login'); ?>" class="form__link">Login</a></p>
                </form>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    // Display login form
    public function display_login_form() {
        ob_start();
        ?>
        <div class="container">
            <div class="form-wrapper">
                <h2 class="form-wrapper__heading">Log in</h2>
                <form class="form" id="loginForm" method="POST" onsubmit="return validateLoginForm()">
                    <div class="form__group">
                        <label for="email" class="form__label">Email</label>
                        <input type="text" id="email" name="email" placeholder="abc@gmail.com" class="form__input" required>
                        <span class="form__error" id="email-error"></span>
                    </div>
                    <div class="form__group">
                        <label for="password" class="form__label">Password</label>
                        <input type="password" id="password" name="password" placeholder="Password" class="form__input" required>
                        <span class="form__error" id="password-error"></span>
                    </div>
                    <button type="submit" name="login" class="form__button">Log in</button>
                    <p class="form__text">Don't have an account? <a href="<?php echo site_url('/register'); ?>" class="form__link">Register</a></p>
                </form>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    
 // Handle user registration via AJAX
public function register_user() {
    check_ajax_referer('custom-auth-nonce', 'nonce');

    $email = sanitize_email($_POST['email']);
    $password = sanitize_text_field($_POST['password']);

    // Check if user already exists
    if (email_exists($email)) {
        wp_send_json_error(array('message' => 'User already exists'));
    } else {
        $user_id = wp_create_user($email, $password, $email);
        if (is_wp_error($user_id)) {
            wp_send_json_error(array('message' => $user_id->get_error_message()));
        } else {
            wp_send_json_success(array('message' => 'Registration successful!'));
        }
    }
}


    public function check_user_exists() {
        check_ajax_referer('custom-auth-nonce', 'nonce');

        global $wpdb;
        $table_name = $wpdb->prefix . 'users';
        $email = sanitize_email($_POST['email']);

        $user_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE email = %s", $email));

        if ($user_exists) {
            wp_send_json_success(array('exists' => true));
        } else {
            wp_send_json_success(array('exists' => false));
        }
    }

    
    // Handle user login via AJAX
public function login_user() {
    check_ajax_referer('custom-auth-nonce', 'nonce');

    $email = sanitize_email($_POST['email']);
    $password = sanitize_text_field($_POST['password']);

    $user = wp_authenticate($email, $password);

    if (is_wp_error($user)) {
        wp_send_json_error(array('message' => 'Incorrect email and password'));
    } else {
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);
        wp_send_json_success(array('message' => 'Login successful!'));
    }
}


    public function render_todo_list() {

        ob_start();
        ?>
        <div class="todo-list-container">
            <h2>My To-Do List</h2>
            <form id="todo-form">
                <input type="text" id="todo-item" placeholder="Add a new item" required>
                <button type="submit">Add</button>
            </form>
            <ul id="todo-list">
                <?php if ($tasks): ?>
                    <?php foreach ($tasks as $task): ?>
                        <li class="todo-item" data-id="<?php echo esc_attr($task->id); ?>">
                            <input type="checkbox" class="todo-item__checkbox" <?php echo ($task->status == 'completed') ? 'checked' : ''; ?>>
                            <span class="todo-item__text"><?php echo esc_html($task->task); ?></span>
                            <select class="todo-item__status">
                                <option value="pending" <?php selected($task->status, 'pending'); ?>>Pending</option>
                                <option value="completed" <?php selected($task->status, 'completed'); ?>>Completed</option>
                            </select>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No tasks found.</li>
                <?php endif; ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }

    public function handle_add_todo_task() {
        // Verify nonce for security
        check_ajax_referer('todo-list-nonce', 'nonce');
    
        // Check if the user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'You must be logged in to add a task.'));
            return;
        }
    
        // Get the current user ID
        $user_id = get_current_user_id();
    
        // Sanitize and validate the task input
        $task = sanitize_text_field($_POST['task']);
        if (empty($task)) {
            wp_send_json_error(array('message' => 'Task cannot be empty.'));
            return;
        }
    
        // Fetch existing tasks from usermeta
        $tasks = get_user_meta($user_id, 'todo_list', true);
        if (!is_array($tasks)) {
            $tasks = array();
        }
    
        // Add the new task
        $tasks[] = array(
            'id' => uniqid(), // Unique ID for the task
            'task' => $task,
            'status' => 'pending'
        );
    
        // Save the updated tasks
        update_user_meta($user_id, 'todo_list', $tasks);
    
        wp_send_json_success(array('message' => 'Task added successfully.'));
    }
    public function handle_fetch_todo_tasks() {
        // Verify nonce for security
        check_ajax_referer('todo-list-nonce', 'nonce');
    
        // Check if the user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'You must be logged in to view tasks.'));
            return;
        }
    
        // Get the current user ID
        $user_id = get_current_user_id();
    
        // Fetch tasks from usermeta
        $tasks = get_user_meta($user_id, 'todo_list', true);
        if (!is_array($tasks)) {
            $tasks = array();
        }
    
        wp_send_json_success(array('tasks' => $tasks));
    }

    public function handle_update_todo_task() {
        // Check the nonce
        check_ajax_referer('todo-list-nonce', 'nonce');
    
        // Verify user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'User not logged in.']);
        }
    
        // Get current user ID
        $user_id = get_current_user_id();
    
        // Get task details from AJAX request
        $task_id = sanitize_text_field($_POST['task_id']);
        $status = sanitize_text_field($_POST['status']);
    
        // Validate status
        $valid_statuses = ['pending', 'in_progress', 'completed'];
        if (!in_array($status, $valid_statuses)) {
            wp_send_json_error(['message' => 'Invalid status.']);
        }
    
        // Fetch tasks from usermeta
        $tasks = get_user_meta($user_id, 'todo_list', true);
        if (!is_array($tasks)) {
            $tasks = array();
        }
    
        // Find the task and update its status
        foreach ($tasks as &$task) {
            if ($task['id'] === $task_id) {
                $task['status'] = $status;
                break;
            }
        }
    
        // Save the updated tasks
        update_user_meta($user_id, 'todo_list', $tasks);
    
        wp_send_json_success(['message' => 'Task updated successfully.']);
    }
    
    
}