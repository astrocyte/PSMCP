<?php
/**
 * WPForms Integration Handler
 * Processes in-person class registration form submissions
 */

if (!defined('ABSPATH')) exit;

class SST_Class_Form_Handler {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // WPForms hooks
        add_filter('wpforms_frontend_form_data', [$this, 'customize_form'], 10, 1);
        add_filter('wpforms_frontend_confirmation_message', [$this, 'custom_success_message'], 10, 4);
        add_action('wpforms_process_complete', [$this, 'process_submission'], 10, 4);

        // Allow .nyc and other TLDs
        add_filter('wpforms_is_email_valid', [$this, 'allow_nyc_emails'], 10, 2);

        // Enqueue camera upload assets
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Enqueue frontend assets for camera upload
     */
    public function enqueue_assets() {
        $registration_form_id = get_option('sst_class_reg_form_id', '');

        // Only load on pages with our form
        if (empty($registration_form_id)) {
            return;
        }

        // Check if current page has our form (via shortcode)
        global $post;
        if ($post && has_shortcode($post->post_content, 'wpforms')) {
            wp_enqueue_style(
                'sst-camera-upload',
                SST_CLASS_REG_PLUGIN_URL . 'assets/css/camera-upload.css',
                [],
                SST_CLASS_REG_VERSION
            );

            wp_enqueue_script(
                'sst-camera-upload',
                SST_CLASS_REG_PLUGIN_URL . 'assets/js/camera-upload.js',
                ['jquery'],
                SST_CLASS_REG_VERSION,
                true
            );

            // Load dashicons for icons
            wp_enqueue_style('dashicons');
        }
    }

    /**
     * Allow .nyc email domains
     */
    public function allow_nyc_emails($is_valid, $email) {
        // If already valid, return true
        if ($is_valid) {
            return true;
        }

        // Allow .nyc domain
        if (preg_match('/\.nyc$/i', $email)) {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        }

        return $is_valid;
    }

    /**
     * Customize form before rendering
     */
    public function customize_form($form_data) {
        $registration_form_id = get_option('sst_class_reg_form_id', '');

        if ($form_data['id'] != $registration_form_id) {
            return $form_data;
        }

        // Disable HTML5 validation for better UX
        $form_data['settings']['form_novalidate'] = '1';

        return $form_data;
    }

    /**
     * Custom success message
     */
    public function custom_success_message($message, $form_data, $fields, $entry_id) {
        $registration_form_id = get_option('sst_class_reg_form_id', '');

        if ($form_data['id'] != $registration_form_id) {
            return $message;
        }

        // Get first name from form submission
        $first_name = '';
        foreach ($fields as $field) {
            if (isset($field['type']) && $field['type'] === 'name') {
                $first_name = isset($field['first']) ? sanitize_text_field($field['first']) : '';
                break;
            }
        }

        // Get course name
        $course_name = '';
        foreach ($fields as $field) {
            if (isset($field['type']) && $field['type'] === 'select') {
                $course_name = isset($field['value']) ? sanitize_text_field($field['value']) : '';
                break;
            }
        }

        $custom_message = '<div class="sst-registration-success">';
        $custom_message .= '<h3>Thanks' . ($first_name ? ', ' . esc_html($first_name) : '') . '!</h3>';
        $custom_message .= '<p>Your registration for <strong>' . esc_html($course_name) . '</strong> has been received.</p>';
        $custom_message .= '<p>You will receive a confirmation email shortly with your account details and class information.</p>';
        $custom_message .= '<p>Questions? Contact us at <a href="mailto:info@sst.nyc">info@sst.nyc</a></p>';
        $custom_message .= '</div>';

        return $custom_message;
    }

    /**
     * Process form submission
     */
    public function process_submission($fields, $entry, $form_data, $entry_id) {
        $registration_form_id = get_option('sst_class_reg_form_id', '');

        // Only process our registration form
        if ($form_data['id'] != $registration_form_id) {
            return;
        }

        // SECURITY: Rate limiting - prevent spam submissions
        if (!$this->check_rate_limit()) {
            error_log('SST Class Registration: Rate limit exceeded for submission');
            return;
        }

        // Extract form fields by type and ID
        $data = $this->extract_form_data($fields);

        if (empty($data['email']) || empty($data['class_name'])) {
            error_log('SST Class Registration: Missing required fields in submission');
            return;
        }

        // Handle file uploads
        $file_handler = SST_Class_File_Handler::get_instance();

        // Process OSHA card upload (field ID 5 based on our form design)
        if (!empty($data['osha_card_raw'])) {
            $data['osha_card_path'] = $file_handler->process_upload($data['osha_card_raw'], 'osha');
        }

        // Process SST card upload (field ID 6 based on our form design)
        if (!empty($data['sst_card_raw'])) {
            $data['sst_card_path'] = $file_handler->process_upload($data['sst_card_raw'], 'sst');
        }

        // Create registration
        $registration = new SST_Class_Registration();
        $result = $registration->create($data);

        if (is_wp_error($result)) {
            error_log('SST Class Registration Error: ' . $result->get_error_message());
            return;
        }

        // Send to Zapier if enabled
        if (get_option('sst_class_reg_zapier_enabled', '0') === '1') {
            $zapier = SST_Class_Zapier::get_instance();
            $zapier->send_registration($result, $data, $entry_id);
        }

        // Send admin notification
        $this->send_admin_notification($result, $data);

        // SECURITY: Log without PII
        error_log('SST Class Registration: Created registration ' . $result);
    }

    /**
     * SECURITY: Check rate limiting for form submissions
     * Limits to 3 submissions per IP per minute
     */
    private function check_rate_limit() {
        $ip = $this->get_client_ip();
        $ip_hash = md5($ip . wp_salt());
        $transient_key = 'sst_reg_limit_' . $ip_hash;

        $attempts = get_transient($transient_key);

        if ($attempts === false) {
            // First attempt
            set_transient($transient_key, 1, 60); // 1 minute window
            return true;
        }

        if ($attempts >= 3) {
            // Rate limit exceeded
            return false;
        }

        // Increment attempts
        set_transient($transient_key, $attempts + 1, 60);
        return true;
    }

    /**
     * Get client IP address safely
     */
    private function get_client_ip() {
        $ip = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Get first IP if multiple
            $ips = explode(',', sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']));
            $ip = trim($ips[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        }

        // Validate IP
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = '0.0.0.0';
        }

        return $ip;
    }

    /**
     * Extract data from WPForms fields array
     *
     * Expected field layout:
     * 0 - Name (first-last)
     * 1 - Email
     * 2 - Phone
     * 3 - Class (select - admin-managed options)
     * 4 - SST Number (text)
     * 5 - OSHA Card (file-upload)
     * 6 - SST Card (file-upload)
     * 7 - Terms (checkbox)
     */
    private function extract_form_data($fields) {
        $data = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
            'class_name' => '',
            'sst_number' => '',
            'osha_card_raw' => '',
            'sst_card_raw' => ''
        ];

        foreach ($fields as $field_id => $field) {
            $type = isset($field['type']) ? $field['type'] : '';

            switch ($type) {
                case 'name':
                    $data['first_name'] = isset($field['first']) ? sanitize_text_field($field['first']) : '';
                    $data['last_name'] = isset($field['last']) ? sanitize_text_field($field['last']) : '';
                    break;

                case 'email':
                    $data['email'] = isset($field['value']) ? sanitize_email($field['value']) : '';
                    break;

                case 'phone':
                    $data['phone'] = isset($field['value']) ? sanitize_text_field($field['value']) : '';
                    break;

                case 'select':
                    // Class selection - value contains class name
                    $data['class_name'] = isset($field['value']) ? sanitize_text_field($field['value']) : '';
                    break;

                case 'text':
                    // SST Number (field ID 4)
                    if ($field_id == 4) {
                        $data['sst_number'] = isset($field['value']) ? sanitize_text_field($field['value']) : '';
                    }
                    break;

                case 'file-upload':
                    // Get raw file data for processing
                    $file_data = isset($field['value_raw']) ? $field['value_raw'] : '';

                    // Field ID 5 = OSHA card, Field ID 6 = SST card
                    if ($field_id == 5) {
                        $data['osha_card_raw'] = $file_data;
                    } elseif ($field_id == 6) {
                        $data['sst_card_raw'] = $file_data;
                    }
                    break;
            }
        }

        return $data;
    }

    /**
     * Get WPForms form select options for settings page
     */
    public static function get_wpforms_list() {
        $forms = [];

        if (!function_exists('wpforms')) {
            return $forms;
        }

        $all_forms = wpforms()->form->get();

        if (!empty($all_forms)) {
            foreach ($all_forms as $form) {
                $forms[$form->ID] = $form->post_title;
            }
        }

        return $forms;
    }

    /**
     * Send admin notification for new registration
     */
    private function send_admin_notification($registration_id, $data) {
        $admin_email = get_option('sst_class_reg_admin_email', get_option('admin_email'));

        $subject = '[SST.NYC] New Class Registration: ' . $data['first_name'] . ' ' . $data['last_name'];

        $message = "New in-person class registration received.\n\n";
        $message .= "REGISTRATION DETAILS:\n";
        $message .= "------------------------\n";
        $message .= "Registration ID: " . $registration_id . "\n";
        $message .= "Name: " . $data['first_name'] . " " . $data['last_name'] . "\n";
        $message .= "Email: " . $data['email'] . "\n";
        $message .= "Phone: " . $data['phone'] . "\n";
        $message .= "Class: " . $data['class_name'] . "\n";

        if (!empty($data['sst_number'])) {
            $message .= "SST Number: " . $data['sst_number'] . "\n";
        }

        $message .= "\nFILES UPLOADED:\n";
        $message .= "------------------------\n";
        $message .= "OSHA Card: " . (!empty($data['osha_card_path']) ? 'Yes' : 'No') . "\n";
        $message .= "SST Card: " . (!empty($data['sst_card_path']) ? 'Yes' : 'No') . "\n";

        $message .= "\nView/process this registration:\n";
        $message .= admin_url('admin.php?page=sst-class-registrations&action=view&id=' . $registration_id);

        wp_mail($admin_email, $subject, $message);
    }
}
