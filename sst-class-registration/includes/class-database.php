<?php
/**
 * Database Management Class
 * Handles all database operations for class registration management
 */

if (!defined('ABSPATH')) exit;

class SST_Class_Database {

    private static $instance = null;
    private $table_registrations;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        global $wpdb;
        $this->table_registrations = $wpdb->prefix . 'sst_class_registrations';
    }

    /**
     * Create database tables
     */
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_registrations = $wpdb->prefix . 'sst_class_registrations';

        $sql_registrations = "CREATE TABLE IF NOT EXISTS $table_registrations (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            registration_id VARCHAR(20) UNIQUE NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            sst_number VARCHAR(50),
            class_name VARCHAR(255) NOT NULL,
            osha_card_path VARCHAR(255),
            sst_card_path VARCHAR(255),
            wp_user_id BIGINT(20),
            enrollment_status VARCHAR(20) DEFAULT 'pending',
            status VARCHAR(20) DEFAULT 'new',
            created_at DATETIME NOT NULL,
            processed_at DATETIME,
            processed_by BIGINT(20),
            notes TEXT,
            KEY idx_registration_id (registration_id),
            KEY idx_email (email),
            KEY idx_class_name (class_name),
            KEY idx_status (status),
            KEY idx_enrollment_status (enrollment_status)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_registrations);

        // Update database version
        update_option('sst_class_reg_db_version', SST_CLASS_REG_VERSION);
    }

    /**
     * Get next registration ID (with atomic MySQL lock for race condition protection)
     */
    public function get_next_registration_id() {
        global $wpdb;

        // SECURITY: Use MySQL GET_LOCK for atomic locking (prevents race conditions)
        $lock_name = 'sst_reg_id_lock';
        $lock_timeout = 5; // seconds

        // Acquire lock
        $lock_acquired = $wpdb->get_var($wpdb->prepare(
            "SELECT GET_LOCK(%s, %d)",
            $lock_name,
            $lock_timeout
        ));

        if (!$lock_acquired) {
            error_log('SST Class Registration: Could not acquire lock for registration ID generation');
            // Fallback: generate with timestamp for uniqueness
            return 'REG-' . time() . '-' . wp_rand(100, 999);
        }

        $last_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT MAX(CAST(SUBSTRING(registration_id, 5) AS UNSIGNED)) FROM %i WHERE registration_id LIKE 'REG-%%'",
                $this->table_registrations
            )
        );
        $next_num = $last_id ? ($last_id + 1) : 1;
        $registration_id = sprintf('REG-%03d', $next_num);

        // Release lock
        $wpdb->query($wpdb->prepare("SELECT RELEASE_LOCK(%s)", $lock_name));

        return $registration_id;
    }

    /**
     * Insert new registration
     */
    public function insert_registration($data) {
        global $wpdb;

        $registration_id = $this->get_next_registration_id();

        $insert_data = [
            'registration_id' => $registration_id,
            'first_name' => sanitize_text_field($data['first_name']),
            'last_name' => sanitize_text_field($data['last_name']),
            'email' => sanitize_email($data['email']),
            'phone' => sanitize_text_field($data['phone']),
            'sst_number' => isset($data['sst_number']) ? sanitize_text_field($data['sst_number']) : '',
            'class_name' => sanitize_text_field($data['class_name']),
            'osha_card_path' => isset($data['osha_card_path']) ? sanitize_text_field($data['osha_card_path']) : '',
            'sst_card_path' => isset($data['sst_card_path']) ? sanitize_text_field($data['sst_card_path']) : '',
            'status' => 'new',
            'enrollment_status' => 'pending',
            'created_at' => current_time('mysql')
        ];

        $result = $wpdb->insert($this->table_registrations, $insert_data);

        if ($result) {
            return $registration_id;
        }

        // SECURITY: Log database errors (without PII)
        if ($wpdb->last_error) {
            error_log('SST Class Registration DB Error on insert: ' . $wpdb->last_error);
        }

        return false;
    }

    /**
     * Get registration by ID
     */
    public function get_registration($registration_id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_registrations} WHERE registration_id = %s",
            $registration_id
        ));
    }

    /**
     * Get registration by email
     */
    public function get_registration_by_email($email) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_registrations} WHERE email = %s ORDER BY created_at DESC",
            $email
        ));
    }

    /**
     * Get registration by database ID
     */
    public function get_registration_by_id($id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_registrations} WHERE id = %d",
            $id
        ));
    }

    /**
     * Update registration
     */
    public function update_registration($registration_id, $data) {
        global $wpdb;
        return $wpdb->update(
            $this->table_registrations,
            $data,
            ['registration_id' => $registration_id]
        );
    }

    /**
     * Update registration status
     */
    public function update_status($registration_id, $status, $user_id = null) {
        global $wpdb;

        $update_data = [
            'status' => $status
        ];

        if ($status === 'processed' && $user_id) {
            $update_data['processed_at'] = current_time('mysql');
            $update_data['processed_by'] = $user_id;
        }

        return $wpdb->update(
            $this->table_registrations,
            $update_data,
            ['registration_id' => $registration_id]
        );
    }

    /**
     * Update enrollment status
     */
    public function update_enrollment_status($registration_id, $enrollment_status, $wp_user_id = null) {
        global $wpdb;

        $update_data = [
            'enrollment_status' => $enrollment_status
        ];

        if ($wp_user_id) {
            $update_data['wp_user_id'] = $wp_user_id;
        }

        return $wpdb->update(
            $this->table_registrations,
            $update_data,
            ['registration_id' => $registration_id]
        );
    }

    /**
     * Get registrations with filters
     */
    public function get_registrations($args = []) {
        global $wpdb;

        $defaults = [
            'status' => '',
            'enrollment_status' => '',
            'class_name' => '',
            'search' => '',
            'orderby' => 'created_at',
            'order' => 'DESC',
            'limit' => 20,
            'offset' => 0
        ];

        $args = wp_parse_args($args, $defaults);

        $where = "1=1";

        if (!empty($args['status'])) {
            $where .= $wpdb->prepare(" AND status = %s", $args['status']);
        }

        if (!empty($args['enrollment_status'])) {
            $where .= $wpdb->prepare(" AND enrollment_status = %s", $args['enrollment_status']);
        }

        if (!empty($args['class_name'])) {
            $where .= $wpdb->prepare(" AND class_name = %s", $args['class_name']);
        }

        if (!empty($args['search'])) {
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $where .= $wpdb->prepare(
                " AND (first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR registration_id LIKE %s)",
                $search, $search, $search, $search
            );
        }

        // SECURITY: Whitelist allowed ORDER BY columns to prevent SQL injection
        $allowed_orderby = ['id', 'registration_id', 'first_name', 'last_name', 'email', 'class_name', 'status', 'enrollment_status', 'created_at', 'processed_at'];
        $orderby = in_array($args['orderby'], $allowed_orderby, true) ? $args['orderby'] : 'created_at';
        $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';

        $limit = absint($args['limit']);
        $offset = absint($args['offset']);

        $query = $wpdb->prepare(
            "SELECT * FROM {$this->table_registrations}
             WHERE $where
             ORDER BY $orderby $order
             LIMIT %d OFFSET %d",
            $limit,
            $offset
        );

        return $wpdb->get_results($query);
    }

    /**
     * Get registration count by status
     */
    public function count_registrations($status = '') {
        global $wpdb;

        if (empty($status)) {
            return $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_registrations}");
        }

        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_registrations} WHERE status = %s",
            $status
        ));
    }

    /**
     * Get registration count by enrollment status
     */
    public function count_by_enrollment_status($enrollment_status) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_registrations} WHERE enrollment_status = %s",
            $enrollment_status
        ));
    }

    /**
     * Delete registration
     */
    public function delete_registration($registration_id) {
        global $wpdb;
        return $wpdb->delete($this->table_registrations, ['registration_id' => $registration_id]);
    }

    /**
     * Get table name
     */
    public function get_table_name() {
        return $this->table_registrations;
    }
}
