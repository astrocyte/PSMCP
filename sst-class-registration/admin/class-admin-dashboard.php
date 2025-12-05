<?php
/**
 * Admin Dashboard
 * Main admin interface for class registration management
 */

if (!defined('ABSPATH')) exit;

class SST_Class_Admin_Dashboard {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_post_sst_process_registration', [$this, 'handle_process']);
        add_action('admin_post_sst_delete_registration', [$this, 'handle_delete']);
        add_action('admin_post_sst_update_registration', [$this, 'handle_update']);
        add_action('admin_post_sst_create_account', [$this, 'handle_create_account']);
        add_action('admin_post_sst_class_reg_save_settings', [$this, 'handle_save_settings']);
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'Class Registrations',
            'Class Registrations',
            'manage_options',
            'sst-class-registrations',
            [$this, 'render_dashboard'],
            'dashicons-welcome-learn-more',
            31
        );

        add_submenu_page(
            'sst-class-registrations',
            'All Registrations',
            'All Registrations',
            'manage_options',
            'sst-class-registrations',
            [$this, 'render_dashboard']
        );

        add_submenu_page(
            'sst-class-registrations',
            'Settings',
            'Settings',
            'manage_options',
            'sst-class-reg-settings',
            [$this, 'render_settings']
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'sst-class-reg') === false && strpos($hook, 'sst-class-registrations') === false) {
            return;
        }

        wp_enqueue_style('sst-class-reg-admin', SST_CLASS_REG_PLUGIN_URL . 'admin/css/admin-styles.css', [], SST_CLASS_REG_VERSION);
    }

    /**
     * Render main dashboard
     */
    public function render_dashboard() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        $registration_id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : '';

        switch ($action) {
            case 'view':
                $this->render_single_registration($registration_id);
                break;
            default:
                $this->render_registration_list();
                break;
        }
    }

    /**
     * Render registration list
     */
    private function render_registration_list() {
        require_once SST_CLASS_REG_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    /**
     * Render single registration view
     */
    private function render_single_registration($registration_id) {
        $db = SST_Class_Database::get_instance();
        $registration = $db->get_registration($registration_id);

        if (!$registration) {
            wp_die('Registration not found');
        }

        require_once SST_CLASS_REG_PLUGIN_DIR . 'admin/views/registration-detail.php';
    }

    /**
     * Render settings page
     */
    public function render_settings() {
        require_once SST_CLASS_REG_PLUGIN_DIR . 'admin/views/settings.php';
    }

    /**
     * Handle process action (mark as processed)
     */
    public function handle_process() {
        check_admin_referer('sst_process_registration');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $registration_id = isset($_POST['registration_id']) ? sanitize_text_field($_POST['registration_id']) : '';

        $registration = new SST_Class_Registration();
        $result = $registration->mark_processed($registration_id, get_current_user_id());

        // SECURITY: Use wp_safe_redirect to prevent open redirect vulnerabilities
        $redirect_url = wp_get_referer() ? wp_get_referer() : admin_url('admin.php?page=sst-class-registrations');
        if ($result) {
            wp_safe_redirect(add_query_arg(['message' => 'processed'], $redirect_url));
        } else {
            wp_safe_redirect(add_query_arg(['error' => 'process_failed'], $redirect_url));
        }
        exit;
    }

    /**
     * Handle delete action
     */
    public function handle_delete() {
        check_admin_referer('sst_delete_registration');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $registration_id = isset($_POST['registration_id']) ? sanitize_text_field($_POST['registration_id']) : '';

        $registration = new SST_Class_Registration();
        $registration->delete($registration_id);

        // SECURITY: Use wp_safe_redirect
        wp_safe_redirect(add_query_arg(['message' => 'deleted'], admin_url('admin.php?page=sst-class-registrations')));
        exit;
    }

    /**
     * Handle update action
     */
    public function handle_update() {
        check_admin_referer('sst_update_registration');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $registration_id = isset($_POST['registration_id']) ? sanitize_text_field($_POST['registration_id']) : '';

        $update_data = [];

        if (isset($_POST['status'])) {
            $update_data['status'] = sanitize_text_field($_POST['status']);
        }

        if (isset($_POST['notes'])) {
            $update_data['notes'] = sanitize_textarea_field($_POST['notes']);
        }

        $registration = new SST_Class_Registration();
        $registration->update($registration_id, $update_data);

        // SECURITY: Use wp_safe_redirect
        $redirect_url = wp_get_referer() ? wp_get_referer() : admin_url('admin.php?page=sst-class-registrations');
        wp_safe_redirect(add_query_arg(['message' => 'updated'], $redirect_url));
        exit;
    }

    /**
     * Handle manual account creation
     */
    public function handle_create_account() {
        check_admin_referer('sst_create_account');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $registration_id = isset($_POST['registration_id']) ? sanitize_text_field($_POST['registration_id']) : '';

        $enrollment = SST_LearnDash_Enrollment::get_instance();
        $result = $enrollment->manual_create_account($registration_id);

        // SECURITY: Use wp_safe_redirect
        $redirect_url = wp_get_referer() ? wp_get_referer() : admin_url('admin.php?page=sst-class-registrations');
        if (is_wp_error($result)) {
            wp_safe_redirect(add_query_arg(['error' => 'account_creation_failed'], $redirect_url));
        } else {
            wp_safe_redirect(add_query_arg(['message' => 'account_created'], $redirect_url));
        }
        exit;
    }

    /**
     * Handle save settings
     */
    public function handle_save_settings() {
        check_admin_referer('sst_class_reg_settings');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        // Form settings
        update_option('sst_class_reg_form_id', sanitize_text_field($_POST['form_id']));

        // Class options (textarea, one per line)
        update_option('sst_class_reg_class_options', sanitize_textarea_field($_POST['class_options']));

        // Account creation settings
        update_option('sst_class_reg_auto_enroll', isset($_POST['auto_enroll']) ? '1' : '0');

        // Email settings
        update_option('sst_class_reg_admin_email', sanitize_email($_POST['admin_email']));
        update_option('sst_class_reg_notify_admin', isset($_POST['notify_admin']) ? '1' : '0');
        update_option('sst_class_reg_notify_student', isset($_POST['notify_student']) ? '1' : '0');

        // Zapier settings
        update_option('sst_class_reg_zapier_enabled', isset($_POST['zapier_enabled']) ? '1' : '0');
        update_option('sst_class_reg_zapier_webhook_url', esc_url_raw($_POST['zapier_webhook_url']));

        // SECURITY: Use wp_safe_redirect
        $redirect_url = wp_get_referer() ? wp_get_referer() : admin_url('admin.php?page=sst-class-reg-settings');
        wp_safe_redirect(add_query_arg(['message' => 'saved'], $redirect_url));
        exit;
    }
}
