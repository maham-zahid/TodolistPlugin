<?php
/*
 * Plugin Name:       Todo List Plugin
 * Plugin URI:        http://todolistplugin.local/wp-admin/todolist-plugin.php
 * Description:       A custom plugin for login, signup, and to-do list management with validations.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Maham
 * Author URI:        https://maham.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       todo-list-plugin
 * Domain Path:       /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include required files
include(plugin_dir_path(__FILE__) . 'public/class-todo-list-plugin-public.php');



// Initialize the plugin
function todo_list_plugin_init() {
    $plugin = new Todo_List();
    $plugin->init(); // Ensure Todo_List class handles the functionality
}
add_action('plugins_loaded', 'todo_list_plugin_init');

// Handle AJAX request to check if user exists
add_action('wp_ajax_check_user_exists', 'todo_list_check_user_exists');
add_action('wp_ajax_nopriv_check_user_exists', 'todo_list_check_user_exists');

function todo_list_check_user_exists() {
    check_ajax_referer('custom-auth-nonce', 'nonce');

    if (!isset($_POST['email'])) {
        wp_send_json_error(array('message' => 'Email is required'));
    }

    $email = sanitize_email($_POST['email']);
    $user = get_user_by('email', $email);

    wp_send_json_success(array('exists' => $user ? true : false));
}

// Main Plugin Class
class Todo_List_Plugin {
    public function __construct() {
        $this->public = new Todo_List();
        add_action('init', array($this->public, 'init'));
    }
}

// Instantiate the main plugin class
new Todo_List_Plugin();
