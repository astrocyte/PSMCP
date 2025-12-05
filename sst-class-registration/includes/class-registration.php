<?php
/**
 * Registration Model Class
 * Business logic for class registrations
 */

if (!defined('ABSPATH')) exit;

class SST_Class_Registration {

    private $db;

    public function __construct() {
        $this->db = SST_Class_Database::get_instance();
    }

    /**
     * Create a new registration
     */
    public function create($data) {
        // Validate required fields
        $required = ['first_name', 'last_name', 'email', 'phone', 'class_name'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return new WP_Error('missing_field', sprintf('Missing required field: %s', $field));
            }
        }

        // SECURITY: Input length validation to prevent DoS/buffer attacks
        $max_lengths = [
            'first_name' => 100,
            'last_name' => 100,
            'email' => 100,
            'phone' => 20,
            'sst_number' => 50,
            'class_name' => 255
        ];

        foreach ($max_lengths as $field => $max_len) {
            if (isset($data[$field]) && strlen($data[$field]) > $max_len) {
                return new WP_Error('field_too_long', sprintf('Field %s exceeds maximum length of %d characters', $field, $max_len));
            }
        }

        // Validate email
        if (!is_email($data['email'])) {
            return new WP_Error('invalid_email', 'Invalid email address');
        }

        // Validate class is in allowed list
        $allowed_classes = self::get_class_options();
        if (!in_array($data['class_name'], $allowed_classes)) {
            // Log but still allow - class might be custom or from old form
            error_log('SST Class Registration: Class "' . $data['class_name'] . '" not in predefined list');
        }

        // Insert registration
        $registration_id = $this->db->insert_registration($data);

        if (!$registration_id) {
            return new WP_Error('db_error', 'Failed to create registration');
        }

        // Trigger action for other components
        do_action('sst_class_registration_created', $registration_id, $data);

        return $registration_id;
    }

    /**
     * Get list of available class options from settings
     */
    public static function get_class_options() {
        $options = get_option('sst_class_reg_class_options', '');

        if (empty($options)) {
            // Default classes based on SST.NYC offerings
            return [
                '10 Hr SST',
                '16 Hr SST - 62 Hour Renewal Supervisor SST',
                '22 Hr SST - 62 Hour Upgrade Worker to Supervisor',
                '32 Hr SST',
                '32 Hr SST + OSHA 30',
                'OSHA 10',
                'OSHA 30',
                '8 Hr SST Refresher'
            ];
        }

        // Options stored as newline-separated values
        return array_filter(array_map('trim', explode("\n", $options)));
    }

    /**
     * Get registration by ID
     */
    public function get($registration_id) {
        return $this->db->get_registration($registration_id);
    }

    /**
     * Get registration by email
     */
    public function get_by_email($email) {
        return $this->db->get_registration_by_email($email);
    }

    /**
     * Update registration
     */
    public function update($registration_id, $data) {
        $result = $this->db->update_registration($registration_id, $data);

        if ($result !== false) {
            do_action('sst_class_registration_updated', $registration_id, $data);
        }

        return $result;
    }

    /**
     * Mark registration as processed
     */
    public function mark_processed($registration_id, $user_id = null) {
        $result = $this->db->update_status($registration_id, 'processed', $user_id);

        if ($result) {
            do_action('sst_class_registration_processed', $registration_id);
        }

        return $result;
    }

    /**
     * Cancel registration
     */
    public function cancel($registration_id) {
        $result = $this->db->update_status($registration_id, 'cancelled');

        if ($result) {
            do_action('sst_class_registration_cancelled', $registration_id);
        }

        return $result;
    }

    /**
     * Delete registration
     */
    public function delete($registration_id) {
        // Get registration first for file cleanup
        $registration = $this->get($registration_id);

        if ($registration) {
            // Delete uploaded files
            if (!empty($registration->osha_card_path)) {
                $file_handler = SST_Class_File_Handler::get_instance();
                $file_handler->delete_file($registration->osha_card_path);
            }
            if (!empty($registration->sst_card_path)) {
                $file_handler = SST_Class_File_Handler::get_instance();
                $file_handler->delete_file($registration->sst_card_path);
            }
        }

        $result = $this->db->delete_registration($registration_id);

        if ($result) {
            do_action('sst_class_registration_deleted', $registration_id);
        }

        return $result;
    }

    /**
     * Get registrations list
     */
    public function get_list($args = []) {
        return $this->db->get_registrations($args);
    }

    /**
     * Count registrations
     */
    public function count($status = '') {
        return $this->db->count_registrations($status);
    }

    /**
     * Check if email already registered for course
     */
    public function is_duplicate($email, $course_id) {
        global $wpdb;
        $table = $this->db->get_table_name();

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE email = %s AND course_id = %d AND status != 'cancelled'",
            $email, $course_id
        ));

        return $count > 0;
    }
}
