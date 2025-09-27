<?php
/**
 * ACF JSON Import Button
 *
 * Adds a button to the WordPress admin bar to import field groups from JSON files.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Fire_ACF_JSON_Import {

    /**
     * Constructor
     */
    public function __construct() {
        // Add button to admin bar
        add_action('admin_bar_menu', array($this, 'add_admin_bar_button'), 100);

        // Add styling for admin bar button
        add_action('admin_head', array($this, 'admin_bar_styles'));

        // Register admin action to process imports
        add_action('admin_init', array($this, 'process_import'));

        // Register admin notices
        add_action('admin_notices', array($this, 'admin_notices'));
    }

    /**
     * Add import button to WordPress admin bar
     *
     * @param WP_Admin_Bar $wp_admin_bar WordPress Admin Bar object
     */
    public function add_admin_bar_button($wp_admin_bar) {
        // Only show on ACF field groups page
        if (!is_admin() || !function_exists('get_current_screen')) {
            return;
        }

        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'acf-field-group') {
            return;
        }

        // Add the parent node
        $wp_admin_bar->add_node(array(
            'id'    => 'import-acf-json',
            'title' => '<span class="ab-icon dashicons dashicons-update"></span><span class="ab-label">' . __('Import ACF', 'fire') . '</span>',
            'href'  => wp_nonce_url(admin_url('edit.php?post_type=acf-field-group&import_acf_json=1'), 'import_acf_json'),
            'meta'  => array(
                'title' => __('Import ACF field groups from JSON files', 'fire'),
            ),
        ));
    }

    /**
     * Add CSS for admin bar button
     */
    public function admin_bar_styles() {
        // Only on ACF field groups page
        if (!is_admin() || !function_exists('get_current_screen')) {
            return;
        }

        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'acf-field-group') {
            return;
        }

        ?>
        <style type="text/css">
            #wp-admin-bar-import-acf-json .ab-icon {
                top: 2px;
                margin-right: 5px;
            }

            #wp-admin-bar-import-acf-json .ab-icon:before {
                color: #a7aaad;
                font-size: 18px;
                line-height: 1.3;
            }

            #wp-admin-bar-import-acf-json:hover .ab-icon:before {
                color: #72aee6;
            }
        </style>
        <?php
    }

    /**
     * Process import request
     */
    public function process_import() {
        // Check if we should process an import
        if (!isset($_GET['import_acf_json']) || !isset($_GET['_wpnonce'])) {
            return;
        }

        // Verify nonce
        if (!wp_verify_nonce($_GET['_wpnonce'], 'import_acf_json')) {
            wp_die(__('Security check failed', 'fire'));
        }

        // Check user capabilities
        if (!current_user_can(acf_get_setting('capability'))) {
            wp_die(__('You do not have permission to do this', 'fire'));
        }

        // Import JSON files
        $this->import_json_files();

        // Redirect back to field groups page
        wp_redirect(add_query_arg('import_complete', 'true', admin_url('edit.php?post_type=acf-field-group')));
        exit;
    }

    /**
     * Import JSON field group files
     */
    private function import_json_files() {
        // Define the default ACF JSON paths and the fire theme path
        $theme_dir = get_stylesheet_directory();
        $fire_acf_json_dir = $theme_dir . '/acf-json';

        // Check if directory exists
        if (!is_dir($fire_acf_json_dir)) {
            set_transient('acf_json_import_message', array(
                'type' => 'error',
                'message' => sprintf(__('Directory %s does not exist.', 'fire'), $fire_acf_json_dir)
            ), 60);
            return;
        }

        // Get all JSON files from the acf-json directory
        $json_files = array();
        $files = glob($fire_acf_json_dir . '/*.json');

        if (empty($files)) {
            // No JSON files found
            set_transient('acf_json_import_message', array(
                'type' => 'error',
                'message' => sprintf(__('No JSON files found in %s directory.', 'fire'), 'acf-json')
            ), 60);
            return;
        }

        $imported = 0;
        $skipped = 0;
        $errors = 0;
        $error_messages = array();

        // Loop through files and import if not already in database
        foreach ($files as $file) {
            // Read JSON file
            $json = json_decode(file_get_contents($file), true);

            // Skip if not valid
            if (empty($json) || empty($json['key'])) {
                $skipped++;
                $error_messages[] = sprintf(__('Invalid JSON in file: %s', 'fire'), basename($file));
                continue;
            }

            // Determine post type
            $post_type = acf_determine_internal_post_type($json['key']);
            if (!$post_type) {
                $post_type = 'acf-field-group'; // Default to field group
            }

            try {
                // Check if already exists
                $post = acf_get_internal_post_type_post($json['key'], $post_type);

                // Skip if already exists
                if ($post) {
                    $skipped++;
                    continue;
                }

                // Import the item based on post type
                if ($post_type === 'acf-field-group') {
                    // Prepare for import
                    $json = acf_prepare_field_group_for_import($json);

                    // Import the field group
                    $field_group = acf_import_field_group($json);

                    if ($field_group) {
                        $imported++;
                    } else {
                        $errors++;
                        $error_messages[] = sprintf(__('Failed to import field group: %s', 'fire'), $json['title']);
                    }
                } else {
                    // For other ACF post types
                    $json = acf_prepare_internal_post_type_for_import($json, $post_type);
                    $result = acf_import_internal_post_type($json, $post_type);

                    if ($result) {
                        $imported++;
                    } else {
                        $errors++;
                        $error_messages[] = sprintf(__('Failed to import item: %s', 'fire'), $json['title']);
                    }
                }
            } catch (Exception $e) {
                $errors++;
                $error_messages[] = $e->getMessage();
            }
        }

        // Set message
        $message = sprintf(
            __('Import complete. %d items imported, %d skipped, %d errors.', 'fire'),
            $imported,
            $skipped,
            $errors
        );

        // Add error details if any
        if (!empty($error_messages)) {
            $message .= '<br><br>' . __('Errors:', 'fire') . '<br>';
            $message .= implode('<br>', array_slice($error_messages, 0, 5));

            if (count($error_messages) > 5) {
                $message .= '<br>' . sprintf(__('... and %d more errors.', 'fire'), count($error_messages) - 5);
            }
        }

        set_transient('acf_json_import_message', array(
            'type' => ($errors > 0) ? 'warning' : 'success',
            'message' => $message
        ), 60);
    }

    /**
     * Display admin notices
     */
    public function admin_notices() {
        $screen = get_current_screen();

        // Only show notices on ACF field groups page
        if (!$screen || $screen->post_type !== 'acf-field-group') {
            return;
        }

        // Check for import complete message
        if (isset($_GET['import_complete'])) {
            $message = get_transient('acf_json_import_message');

            if ($message) {
                printf(
                    '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
                    esc_attr($message['type']),
                    wp_kses_post($message['message'])
                );

                // Clear transient
                delete_transient('acf_json_import_message');
            }
        }
    }
}

/**
 * Initialize ACF JSON Import functionality
 */
function fire_init_acf_json_import() {
    // Only run if ACF is active
    if (function_exists('acf_get_setting')) {
        new Fire_ACF_JSON_Import();
    }
}
add_action('init', 'fire_init_acf_json_import');