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

// Include necessary class files
require_once plugin_dir_path( __FILE__ ) . 'includes/class-todo-list-plugin.php';

// Initialize the core functionality
function run_todolist_plugin() {
    $plugin = new Todolist_Plugin();
    $plugin->run();
}
run_todolist_plugin();