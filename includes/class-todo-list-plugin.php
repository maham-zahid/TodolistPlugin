<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Todolist_Plugin {

    protected $plugin_name;
    protected $version;

    public function __construct()
    {

        $this->plugin_name = 'todolist-plugin';
        $this->version = '1.0.0';

        // Load dependencies
        $this->load_dependencies();

        // Register admin and plugin hooks
        $this->define_public_hooks();
    }

    private function load_dependencies()
    {
        // Include all necessary files
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-todo-list-plugin-public.php';
    }

    /**
     * Register all hooks related to public side functionality
     */
    private function define_public_hooks()
    {
        $plugin_public = new Todo_List();

        // Enqueue public scripts and styles
        add_action('wp_enqueue_scripts', array($plugin_public, 'enqueue_assets'));

        // Add shortcodes for registration, login, and todo list forms
        add_shortcode('todolist_registration_form', array($plugin_public, 'display_registration_form'));
        add_shortcode('todolist_login_form', array($plugin_public, 'display_login_form'));
        add_shortcode('todolist_form', array($plugin_public, 'render_todo_list'));

        // Register AJAX actions for registration
        add_action('wp_ajax_register_user', array($plugin_public, 'register_user'));
        add_action('wp_ajax_nopriv_register_user', array($plugin_public, 'register_user'));

        // Register AJAX actions for login
        add_action('wp_ajax_login_user', array($plugin_public, 'login_user'));
        add_action('wp_ajax_nopriv_login_user', array($plugin_public, 'login_user'));

        // Register AJAX actions for existing user
        add_action('wp_ajax_check_user_exists', array($plugin_public, 'check_user_exists'));
        add_action('wp_ajax_nopriv_check_user_exists', array($plugin_public, 'check_user_exists'));

        // Register AJAX actions for adding todo items
        add_action('wp_ajax_add_todo_task', array($plugin_public, 'handle_add_todo_task'));

        // Register AJAX actions for fetching todo items
        add_action('wp_ajax_fetch_todo_tasks', array($plugin_public, 'handle_fetch_todo_tasks'));

        // Register AJAX actions for updating todo items
        add_action('wp_ajax_update_todo_task', [$plugin_public, 'handle_update_todo_task']);
    }

    public function run()
    {
        // Run the plugin
    }
}
