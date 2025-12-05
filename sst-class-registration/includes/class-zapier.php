<?php
/**
 * Zapier Webhook Integration
 * Sends registration data to Zapier for Google Sheets sync
 */

if (!defined('ABSPATH')) exit;

class SST_Class_Zapier {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Zapier integration is initialized via settings
    }

    /**
     * Send registration data to Zapier webhook
     *
     * @param string $registration_id The registration ID
     * @param array $data Registration data
     * @param int $entry_id WPForms entry ID
     * @return bool Success status
     */
    public function send_registration($registration_id, $data, $entry_id = 0) {
        $webhook_url = get_option('sst_class_reg_zapier_webhook_url', '');

        if (empty($webhook_url)) {
            error_log('SST Class Registration: Zapier webhook URL not configured');
            return false;
        }

        // SECURITY: Prepare and sanitize payload
        $payload = [
            'registration_id' => sanitize_text_field($registration_id),
            'entry_id' => absint($entry_id),
            'timestamp' => current_time('mysql'),
            'first_name' => sanitize_text_field($data['first_name'] ?? ''),
            'last_name' => sanitize_text_field($data['last_name'] ?? ''),
            'full_name' => sanitize_text_field(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
            'email' => sanitize_email($data['email'] ?? ''),
            'phone' => sanitize_text_field($data['phone'] ?? ''),
            'class_name' => sanitize_text_field($data['class_name'] ?? ''),
            'sst_number' => sanitize_text_field($data['sst_number'] ?? ''),
            'has_osha_card' => !empty($data['osha_card_path']),
            'has_sst_card' => !empty($data['sst_card_path']),
            'site_url' => site_url()
            // SECURITY: Removed admin_url from external webhook payload
        ];

        // Send to Zapier
        $response = wp_remote_post($webhook_url, [
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($payload),
            'timeout' => 30,
            'sslverify' => true
        ]);

        if (is_wp_error($response)) {
            error_log('SST Class Registration Zapier Error: ' . $response->get_error_message());
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code >= 200 && $response_code < 300) {
            error_log('SST Class Registration: Sent to Zapier successfully - ' . $registration_id);
            return true;
        }

        error_log('SST Class Registration Zapier Error: HTTP ' . $response_code);
        return false;
    }

    /**
     * Send enrollment status update to Zapier
     *
     * @param string $registration_id The registration ID
     * @param string $status New enrollment status
     * @param int $wp_user_id WordPress user ID if created
     * @return bool Success status
     */
    public function send_enrollment_update($registration_id, $status, $wp_user_id = 0) {
        $webhook_url = get_option('sst_class_reg_zapier_webhook_url', '');

        if (empty($webhook_url)) {
            return false;
        }

        $db = SST_Class_Database::get_instance();
        $registration = $db->get_registration($registration_id);

        if (!$registration) {
            return false;
        }

        $payload = [
            'registration_id' => $registration_id,
            'timestamp' => current_time('mysql'),
            'event_type' => 'enrollment_update',
            'enrollment_status' => $status,
            'wp_user_id' => $wp_user_id,
            'email' => $registration->email,
            'class_name' => $registration->class_name,
            'site_url' => site_url()
        ];

        $response = wp_remote_post($webhook_url, [
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($payload),
            'timeout' => 30
        ]);

        return !is_wp_error($response);
    }

    /**
     * Test webhook connection
     *
     * @param string $webhook_url URL to test
     * @return array Result with success status and message
     */
    public static function test_webhook($webhook_url) {
        if (empty($webhook_url)) {
            return [
                'success' => false,
                'message' => 'Webhook URL is empty'
            ];
        }

        $test_payload = [
            'test' => true,
            'timestamp' => current_time('mysql'),
            'message' => 'SST Class Registration webhook test',
            'site_url' => site_url()
        ];

        $response = wp_remote_post($webhook_url, [
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($test_payload),
            'timeout' => 30
        ]);

        if (is_wp_error($response)) {
            return [
                'success' => false,
                'message' => 'Error: ' . $response->get_error_message()
            ];
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code >= 200 && $response_code < 300) {
            return [
                'success' => true,
                'message' => 'Webhook test successful (HTTP ' . $response_code . ')'
            ];
        }

        return [
            'success' => false,
            'message' => 'Webhook returned HTTP ' . $response_code
        ];
    }
}
