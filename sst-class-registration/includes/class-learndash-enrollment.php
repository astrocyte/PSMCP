<?php
/**
 * User Account Handler
 * Creates WordPress user accounts for in-person class registrations
 * (No LearnDash enrollment - these are in-person classes without LMS components)
 */

if (!defined('ABSPATH')) exit;

class SST_LearnDash_Enrollment {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Listen for new registrations
        add_action('sst_class_registration_created', [$this, 'maybe_create_account'], 10, 2);
    }

    /**
     * Create account if enabled
     */
    public function maybe_create_account($registration_id, $data) {
        if (get_option('sst_class_reg_auto_enroll', '1') !== '1') {
            return;
        }

        $this->process_registration($registration_id, $data);
    }

    /**
     * Process registration - create user account
     *
     * @param string $registration_id The registration ID
     * @param array $data Registration data
     * @return bool Success status
     */
    public function process_registration($registration_id, $data) {
        $db = SST_Class_Database::get_instance();

        // Check if user already exists
        $existing_user = get_user_by('email', $data['email']);
        $is_new_user = false;

        if ($existing_user) {
            $user_id = $existing_user->ID;

            // Update user meta with SST number if provided
            if (!empty($data['sst_number'])) {
                update_user_meta($user_id, 'sst_number', sanitize_text_field($data['sst_number']));
            }
            // Update phone if provided
            if (!empty($data['phone'])) {
                update_user_meta($user_id, 'phone', sanitize_text_field($data['phone']));
            }
        } else {
            // Create new WordPress user
            $user_id = $this->create_user($data);
            $is_new_user = true;

            if (is_wp_error($user_id)) {
                $db->update_registration($registration_id, [
                    'enrollment_status' => 'failed',
                    'notes' => 'Failed to create user: ' . $user_id->get_error_message()
                ]);
                // SECURITY: Log without PII
                error_log('SST Class Registration: Failed to create user for registration ' . $registration_id . ': ' . $user_id->get_error_message());
                return false;
            }
        }

        // Update registration with user ID
        $db->update_registration($registration_id, [
            'wp_user_id' => $user_id,
            'enrollment_status' => 'registered',
            'processed_at' => current_time('mysql')
        ]);

        // Send registration confirmation email if enabled
        if (get_option('sst_class_reg_notify_student', '1') === '1') {
            $this->send_registration_email($user_id, $data, $is_new_user);
        }

        // Notify admin if enabled
        if (get_option('sst_class_reg_notify_admin', '1') === '1') {
            $this->send_admin_notification($registration_id, $data);
        }

        do_action('sst_class_student_registered', $registration_id, $user_id);

        return true;
    }

    /**
     * Create WordPress user account
     */
    private function create_user($data) {
        $username = $this->generate_username($data['email']);
        $password = wp_generate_password(12, true, true);

        $user_id = wp_create_user(
            $username,
            $password,
            $data['email']
        );

        if (!is_wp_error($user_id)) {
            // Update user profile
            wp_update_user([
                'ID' => $user_id,
                'first_name' => sanitize_text_field($data['first_name']),
                'last_name' => sanitize_text_field($data['last_name']),
                'display_name' => sanitize_text_field($data['first_name'] . ' ' . $data['last_name']),
                'role' => 'subscriber'
            ]);

            // Store additional meta
            if (!empty($data['sst_number'])) {
                update_user_meta($user_id, 'sst_number', sanitize_text_field($data['sst_number']));
            }
            update_user_meta($user_id, 'phone', sanitize_text_field($data['phone']));
            update_user_meta($user_id, 'registered_via', 'sst_inperson_class');

            // Send WordPress new user notification with login credentials
            wp_new_user_notification($user_id, null, 'user');
        }

        return $user_id;
    }

    /**
     * Generate unique username from email
     */
    private function generate_username($email) {
        $base_username = strstr($email, '@', true);
        $base_username = sanitize_user($base_username, true);

        // Ensure it's at least 3 characters
        if (strlen($base_username) < 3) {
            $base_username = $base_username . '_user';
        }

        $username = $base_username;
        $counter = 1;

        while (username_exists($username)) {
            $username = $base_username . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Send registration confirmation email to student
     */
    private function send_registration_email($user_id, $data, $is_new_user = false) {
        $user = get_user_by('ID', $user_id);

        if (!$user) {
            return;
        }

        $class_name = isset($data['class_name']) ? $data['class_name'] : 'In-Person SST Class';

        // SECURITY: Strip newlines to prevent email header injection
        $safe_class_name = str_replace(["\r", "\n"], '', $class_name);
        $subject = 'Welcome to SST.NYC - You\'re Registered for ' . $safe_class_name;

        $message = "Hi " . $data['first_name'] . ",\n\n";
        $message .= "Great news! You have been successfully registered for:\n\n";
        $message .= "Class: " . $class_name . "\n\n";

        if ($is_new_user) {
            $message .= "We've created an account for you on SST.NYC.\n\n";
            $message .= "Username: " . $user->user_login . "\n";
            $message .= "You should receive a separate email with password setup instructions.\n\n";
        }

        $message .= "You can access your account at:\n";
        $message .= site_url('/my-account/') . "\n\n";

        $message .= "IMPORTANT: This is an IN-PERSON class. You will receive ";
        $message .= "additional details about the class location, date, and schedule separately.\n\n";

        $message .= "What to bring:\n";
        $message .= "- Valid photo ID\n";
        $message .= "- Any existing SST or OSHA cards (if applicable)\n\n";

        $message .= "Questions? Contact us at info@sst.nyc or call us.\n\n";

        $message .= "See you in class!\n\n";
        $message .= "The SST.NYC Team\n";
        $message .= site_url();

        $headers = ['Content-Type: text/plain; charset=UTF-8'];

        wp_mail($data['email'], $subject, $message, $headers);
    }

    /**
     * Send admin notification
     */
    private function send_admin_notification($registration_id, $data) {
        $admin_email = get_option('sst_class_reg_admin_email', get_option('admin_email'));

        $class_name = isset($data['class_name']) ? $data['class_name'] : 'Unknown';

        // SECURITY: Strip newlines to prevent email header injection
        $safe_name = str_replace(["\r", "\n"], '', $data['first_name'] . ' ' . $data['last_name']);
        $subject = '[SST.NYC] New In-Person Class Registration: ' . $safe_name;

        $message = "New in-person class registration received:\n\n";
        $message .= "Registration ID: " . $registration_id . "\n";
        $message .= "Name: " . $data['first_name'] . ' ' . $data['last_name'] . "\n";
        $message .= "Email: " . $data['email'] . "\n";
        $message .= "Phone: " . $data['phone'] . "\n";
        $message .= "Class: " . $class_name . "\n";

        if (!empty($data['sst_number'])) {
            $message .= "SST Number: " . $data['sst_number'] . "\n";
        }

        $message .= "\nUploaded Documents:\n";
        $message .= "- OSHA Card: " . (!empty($data['osha_card_path']) ? 'Yes' : 'No') . "\n";
        $message .= "- SST Card: " . (!empty($data['sst_card_path']) ? 'Yes' : 'No') . "\n";

        $message .= "\nView all registrations:\n";
        $message .= admin_url('admin.php?page=sst-class-registrations');

        $headers = ['Content-Type: text/plain; charset=UTF-8'];

        wp_mail($admin_email, $subject, $message, $headers);
    }

    /**
     * Manual account creation trigger (for admin use)
     */
    public function manual_create_account($registration_id) {
        $db = SST_Class_Database::get_instance();
        $registration = $db->get_registration($registration_id);

        if (!$registration) {
            return new WP_Error('not_found', 'Registration not found');
        }

        $data = [
            'first_name' => $registration->first_name,
            'last_name' => $registration->last_name,
            'email' => $registration->email,
            'phone' => $registration->phone,
            'sst_number' => $registration->sst_number,
            'class_name' => $registration->class_name
        ];

        return $this->process_registration($registration_id, $data);
    }
}
