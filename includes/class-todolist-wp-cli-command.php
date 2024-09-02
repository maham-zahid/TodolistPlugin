<?php

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
    return;
}

class WPCLI_Todo_Command {

    /**
     * Adds a to-do item for a given user.
     */

    public function add( $args, $assoc_args ) {
        if ( count( $args ) < 2 ) {
            WP_CLI::error( 'Insufficient arguments. Usage: wp todo add <user_id> <task>' );
            return;
        }

        list( $user_id, $task ) = $args;

        if ( ! is_numeric( $user_id ) || empty( $task ) ) {
            WP_CLI::error( 'Invalid user ID or task. Please provide a valid user ID and task.' );
            return;
        }

        if ( ! get_userdata( $user_id ) ) {
            WP_CLI::error( "User with ID {$user_id} does not exist." );
            return;
        }

        $task = sanitize_text_field( $task );

        $tasks = get_user_meta( $user_id, 'todo_list', true );
        if ( ! is_array( $tasks ) ) {
            $tasks = array();
        }

        $tasks[] = array(
            'id' => uniqid(), 
            'task' => $task,
            'status' => 'pending'
        );

        
        $result = update_user_meta( $user_id, 'todo_list', $tasks );

        if ( $result ) {
            WP_CLI::success( "Task '{$task}' added to user {$user_id}'s to-do list." );
        } else {
            WP_CLI::error( "Failed to add task." );
        }
    }

    /**
     * Fetches the to-do items for a given user.
     */
    public function fetch( $args, $assoc_args ) {
        if ( count( $args ) < 1 ) {
            WP_CLI::error( 'Insufficient arguments. Usage: wp todo fetch <user_id>' );
            return;
        }

        list( $user_id ) = $args;

        if ( ! is_numeric( $user_id ) ) {
            WP_CLI::error( 'Invalid user ID. Please provide a valid user ID.' );
            return;
        }

        if ( ! get_userdata( $user_id ) ) {
            WP_CLI::error( "User with ID {$user_id} does not exist." );
            return;
        }

        $tasks = get_user_meta( $user_id, 'todo_list', true );
        if ( ! is_array( $tasks ) ) {
            $tasks = array();
        }

        if ( empty( $tasks ) ) {
            WP_CLI::success( "No tasks found for user {$user_id}." );
        } else {
            foreach ( $tasks as $task ) {
                WP_CLI::line( "Task ID: {$task['id']}, Task: {$task['task']}, Status: {$task['status']}" );
            }
        }
    }

   /**
     * Updates the status of a to-do item for a given user.
     */
    public function update( $args, $assoc_args ) {
        if ( count( $args ) < 3 ) {
            WP_CLI::error( 'Insufficient arguments. Usage: wp todo update <user_id> <task_id> <status>' );
            return;
        }

        list( $user_id, $task_id, $status ) = $args;

        if ( ! is_numeric( $user_id ) || empty( $task_id ) || empty( $status ) ) {
            WP_CLI::error( 'Invalid arguments. Please provide a valid user ID, task ID, and status.' );
            return;
        }

        if ( ! get_userdata( $user_id ) ) {
            WP_CLI::error( "User with ID {$user_id} does not exist." );
            return;
        }

        $valid_statuses = ['pending', 'in_progress', 'completed'];
        if ( ! in_array( $status, $valid_statuses ) ) {
            WP_CLI::error( 'Invalid status. Valid statuses are: ' . implode( ', ', $valid_statuses ) );
            return;
        }

        $tasks = get_user_meta( $user_id, 'todo_list', true );
        if ( ! is_array( $tasks ) ) {
            $tasks = array();
        }

        $task_found = false;
        foreach ( $tasks as &$task ) {
            if ( $task['id'] === $task_id ) {
                $task['status'] = $status;
                $task_found = true;
                break;
            }
        }

        if ( ! $task_found ) {
            WP_CLI::error( "Task with ID {$task_id} not found for user {$user_id}." );
            return;
        }

        $result = update_user_meta( $user_id, 'todo_list', $tasks );

        if ( $result ) {
            WP_CLI::success( "Task with ID {$task_id} updated to status '{$status}'." );
        } else {
            WP_CLI::error( "Failed to update task." );
        }
    }
}


// Register the command with WP-CLI.
WP_CLI::add_command( 'todo', 'WPCLI_Todo_Command' );
