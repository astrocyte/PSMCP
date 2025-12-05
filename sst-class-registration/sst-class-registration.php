<?php
/**
 * Plugin Name: SST In-Person Class Registration
 * Description: Handles in-person class registration with auto-enrollment into LearnDash courses, file uploads for SST/OSHA cards, and Zapier integration
 * Version: 1.1
 * Author: Predictive Safety (SST.NYC)
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

// Define plugin constants
define('SST_CLASS_REG_VERSION', '1.2');
define('SST_CLASS_REG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SST_CLASS_REG_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Plugin Class
 */
class SST_Class_Registration_Manager {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        // Load dependencies
        $this->load_dependencies();

        // Initialize components
        add_action('plugins_loaded', [$this, 'init']);
    }

    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        require_once SST_CLASS_REG_PLUGIN_DIR . 'includes/class-database.php';
        require_once SST_CLASS_REG_PLUGIN_DIR . 'includes/class-registration.php';
        require_once SST_CLASS_REG_PLUGIN_DIR . 'includes/class-form-handler.php';
        require_once SST_CLASS_REG_PLUGIN_DIR . 'includes/class-learndash-enrollment.php';
        require_once SST_CLASS_REG_PLUGIN_DIR . 'includes/class-file-handler.php';
        require_once SST_CLASS_REG_PLUGIN_DIR . 'includes/class-zapier.php';

        // Admin includes
        if (is_admin()) {
            require_once SST_CLASS_REG_PLUGIN_DIR . 'admin/class-admin-dashboard.php';
            require_once SST_CLASS_REG_PLUGIN_DIR . 'admin/class-registration-list-table.php';
        }
    }

    /**
     * Initialize plugin components
     */
    public function init() {
        // Initialize database
        SST_Class_Database::get_instance();

        // Initialize form handler
        SST_Class_Form_Handler::get_instance();

        // Initialize file handler
        SST_Class_File_Handler::get_instance();

        // Initialize LearnDash enrollment
        SST_LearnDash_Enrollment::get_instance();

        // Initialize Zapier integration (optional)
        SST_Class_Zapier::get_instance();

        // Initialize admin if in admin area
        if (is_admin()) {
            SST_Class_Admin_Dashboard::get_instance();
        }
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables
        SST_Class_Database::create_tables();

        // Create upload directory
        SST_Class_File_Handler::create_upload_directory();

        // Set default options
        add_option('sst_class_reg_form_id', '');
        add_option('sst_class_reg_auto_enroll', '1'); // Auto-create user accounts
        add_option('sst_class_reg_admin_email', get_option('admin_email'));
        add_option('sst_class_reg_notify_admin', '1');
        add_option('sst_class_reg_notify_student', '1');
        add_option('sst_class_reg_zapier_enabled', '0');
        add_option('sst_class_reg_zapier_webhook_url', '');

        // Default in-person class options (newline-separated)
        $default_classes = implode("\n", [
            '10 Hr SST',
            '16 Hr SST - 62 Hour Renewal Supervisor SST',
            '22 Hr SST - 62 Hour Upgrade Worker to Supervisor',
            '32 Hr SST',
            '32 Hr SST + OSHA 30',
            'OSHA 10',
            'OSHA 30',
            '8 Hr SST Refresher',
            '16 Hr Rigging Worker',
            '16 Hr Special Rigger',
            '30 Hr Master Rigger',
            '32 Hr Rigging Supervisor'
        ]);
        add_option('sst_class_reg_class_options', $default_classes);

        // Auto-create WPForms form if WPForms is active
        $this->maybe_create_wpforms_form();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create WPForms form if it doesn't exist
     */
    private function maybe_create_wpforms_form() {
        // Check if WPForms is active
        if (!function_exists('wpforms')) {
            return;
        }

        // Check if we already have a form configured
        $existing_form_id = get_option('sst_class_reg_form_id', '');
        if (!empty($existing_form_id) && get_post($existing_form_id)) {
            return; // Form already exists
        }

        // Load form JSON from plugin
        $form_json_file = SST_CLASS_REG_PLUGIN_DIR . 'inperson_registration_form.json';
        if (!file_exists($form_json_file)) {
            return;
        }

        $form_content = file_get_contents($form_json_file);
        if (empty($form_content)) {
            return;
        }

        // Validate JSON
        $form_data = json_decode($form_content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return;
        }

        // Create the WPForms post
        $form_id = wp_insert_post([
            'post_title'   => 'In-Person Class Registration',
            'post_status'  => 'publish',
            'post_type'    => 'wpforms',
            'post_content' => $form_content,
        ]);

        if ($form_id && !is_wp_error($form_id)) {
            // Update the form content with the new ID
            $form_data['id'] = $form_id;
            wp_update_post([
                'ID' => $form_id,
                'post_content' => wp_json_encode($form_data),
            ]);

            // Save the form ID to options
            update_option('sst_class_reg_form_id', $form_id);

            // Log success
            error_log('SST Class Registration: Created WPForms form ID ' . $form_id);
        }
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

// Initialize the plugin
function sst_class_registration() {
    return SST_Class_Registration_Manager::get_instance();
}

// Start the plugin
sst_class_registration();
