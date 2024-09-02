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

// Register the WP-CLI command.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-todolist-wp-cli-command.php';
}

// Initialize the core functionality
function run_todolist_plugin() {
    $plugin = new Todolist_Plugin();
    $plugin->run();
}
run_todolist_plugin();

// Schedule the cron event on plugin activation
function todolistplugin_activate() {
    if ( ! wp_next_scheduled( 'send_pending_tasks_email_event' ) ) {
        wp_schedule_event( time(), 'daily', 'send_pending_tasks_email_event' );
    }
}

// Clear the cron event on plugin deactivation
function todolistplugin_deactivate() {
    $timestamp = wp_next_scheduled( 'send_pending_tasks_email_event' );
    if ( $timestamp ) {
        wp_unschedule_event( $timestamp, 'send_pending_tasks_email_event' );
    }
}

// Hook into plugin activation and deactivation
register_activation_hook( __FILE__, 'todolistplugin_activate' );
register_deactivation_hook( __FILE__, 'todolistplugin_deactivate' );


