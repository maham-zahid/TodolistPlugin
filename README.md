# TodolistPlugin

A versatile WordPress plugin designed to help users manage personal tasks and to-do lists, featuring user registration and login functionalities, REST API endpoints for task management, and WP CLI commands for efficient task handling.

## Motivation

During my tenure as a software development intern, I was tasked with creating a custom WordPress plugin to enhance my understanding of plugin development and contribute to a live project. This project allowed me to explore WordPress’s extensive plugin architecture, sharpen my skills in PHP, JavaScript, and MySQL, and deliver a practical solution for task management. The objective was to build a user-friendly to-do list plugin with secure registration and login features, providing an opportunity to apply best practices in plugin development and learn about integrating multiple technologies within a professional context.

## Code Style

This plugin adheres to standard WordPress coding practices to ensure clarity, maintainability, and compatibility with other WordPress components. Key code style aspects include:

- **PHP Coding Standards:** All PHP code follows the WordPress PHP Coding Standards, including proper indentation, naming conventions, and thorough documentation.
- **HTML & CSS:** HTML structure complies with semantic HTML5 standards, and CSS is organized modularly, using BEM (Block, Element, Modifier) methodology where applicable.
- **JavaScript:** JavaScript is developed in line with WordPress JavaScript Coding Standards, utilizing jQuery for DOM manipulation while avoiding conflicts with other plugins.
- **Security Best Practices:** The code is written with a strong focus on security, including proper data sanitization, validation, and escaping to prevent vulnerabilities like SQL injection and XSS.
- **Version Control:** Managed under Git version control with clear commit messages and structured branching for a streamlined development process.

## Video Tutorial

Watch the Video Tutorial to get a comprehensive overview of the plugin’s features and functionality.

- Video of Module 3 (Todo-list) (https://drive.google.com/file/d/1iWrswROpT_wXYXU1kAbNDV6F9A7forut/view)
- Video of Module 6 (Rest API)  (https://drive.google.com/file/d/1OiDG5_woc0pyEn9LEXFY1i5xV1UMZx-n/view)
- Video of Module 7 (WP CLI)    (https://drive.google.com/file/d/1koGYLAr0wAgZv0kj6KMUEsEkcg4rzHQh/view?usp=sharing)

## Tech/Framework Used

The To-Do List Plugin integrates several technologies and frameworks to provide robust functionality and seamless WordPress integration:

- **WordPress:** Utilizes WordPress APIs and hooks for plugin integration.
- **PHP:** Handles core functionalities of the plugin, adhering to WordPress PHP Coding Standards.
- **JavaScript:** Enhances user interactions and dynamic content with jQuery and JavaScript.
- **AJAX:** Facilitates real-time content updates without full page reloads.
- **REST API:** Enables front-end and back-end communication for data retrieval and updates.
- **WP CLI:** Provides command-line tools for managing tasks within the plugin.
- **HTML5:** Structures the user interface with semantic and accessible HTML.
- **CSS3:** Styles the front-end interface using modern CSS techniques.
- **WordPress Shortcodes API:** Allows embedding of plugin functionality in posts, pages, or widgets via shortcodes.

## Features

### User Registration and Login

- **Registration Form:** Users can sign up with their name, email, and password. Duplicate users are detected with appropriate notifications.
- **Login Form:** Users can log in using their credentials and are redirected to the To-Do List page upon successful login.

### To-Do List Management

- **Add Tasks:** Create new tasks with titles.
- **Mark Tasks as Complete:** Update task status to completed.

### User-Specific Task Management

- **Personalized Lists:** Each user has a unique to-do list.
- **Individual Task Views:** Ensures privacy with user-specific tasks.

### Admin Features

- **User Management:** Admins can view and manage all users and their tasks.
- **Task Overview:** Admins get a comprehensive overview of tasks across the platform.

### Nonce Verification

- **Security Measures:** Employs nonce verification for secure form submissions and AJAX requests.

### AJAX Integration

- **Real-Time Updates:** Enables task operations without page reloads.
- **Seamless User Experience:** Provides instant feedback and improved interaction.

### REST API 

- **Get User's To-Do List:** REST API endpoint that accepts a user ID as a parameter and responds with a list of the user's to-do items in JSON format.
- **Add Task to User's To-Do List:** REST API endpoint that accepts a user ID, task item, and status, and adds the task to the user's to-do list. The response includes the task ID.
- **Update Task Status:** REST API endpoint that accepts a task ID and status, updating the task status. The response indicates success with a boolean value.

### WP CLI 

- **Add To-Do Item:** WP CLI command to add a to-do item for a given user.
- **Get To-Do Items:** WP CLI command to retrieve the to-do items of a given user.
- **Change Task Status:** WP CLI command to change the status of a specific task ID for a user.

## Installation

1. Download the plugin ZIP file.
2. In your WordPress admin panel, navigate to Plugins > Add New.
3. Click on "Upload Plugin" and select the downloaded ZIP file.
4. Click "Install Now" and then "Activate."

## Contributing

Contributions are welcome! Please submit issues or pull requests and adhere to the code style guidelines. Ensure all changes are well-documented.

## Contact

For questions or feedback, please reach out to Maham Zahid at mahamzahhid333@gmail.com.
