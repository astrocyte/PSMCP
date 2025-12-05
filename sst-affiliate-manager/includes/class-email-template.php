<?php
/**
 * Email Template Manager
 * Handles loading, rendering, and saving email templates with placeholder support
 */

if (!defined('ABSPATH')) exit;

class SST_Email_Template {

    private static $instance = null;

    /**
     * Available placeholders for each template type
     */
    private $template_placeholders = [
        'affiliate-approved' => [
            '{{FIRST_NAME}}' => 'Affiliate first name',
            '{{LAST_NAME}}' => 'Affiliate last name',
            '{{FULL_NAME}}' => 'Full name',
            '{{EMAIL}}' => 'Affiliate email',
            '{{AFFILIATE_ID}}' => 'Affiliate ID (e.g., AFF-001)',
            '{{COMMISSION_RATE}}' => 'Commission rate percentage',
            '{{REFERRAL_LINK}}' => 'Unique referral link',
            '{{COUPON_CODE}}' => 'WooCommerce coupon code',
            '{{SITE_URL}}' => 'Website URL',
        ],
        'sale-notification' => [
            '{{FIRST_NAME}}' => 'Affiliate first name',
            '{{ORDER_NUMBER}}' => 'WooCommerce order number',
            '{{ORDER_TOTAL}}' => 'Order total amount',
            '{{COMMISSION_AMOUNT}}' => 'Commission earned',
            '{{COMMISSION_RATE}}' => 'Commission rate percentage',
            '{{ORDER_DATE}}' => 'Order date/time',
            '{{PRODUCTS}}' => 'List of products purchased',
            '{{COUPON_CODE}}' => 'Coupon code used',
        ],
        'payment-notification' => [
            '{{FIRST_NAME}}' => 'Affiliate first name',
            '{{PAYMENT_AMOUNT}}' => 'Amount paid',
            '{{PAYMENT_DATE}}' => 'Payment date',
            '{{PAYMENT_NOTES}}' => 'Payment notes',
            '{{TOTAL_SALES}}' => 'All-time total sales',
            '{{TOTAL_COMMISSION}}' => 'All-time commission earned',
            '{{COMMISSION_PAID}}' => 'Total commission paid to date',
            '{{COMMISSION_PENDING}}' => 'Pending commission balance',
        ],
    ];

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    /**
     * Get available template types
     */
    public function get_template_types() {
        return [
            'affiliate-approved' => [
                'name' => 'Affiliate Approved',
                'description' => 'Sent when an affiliate application is approved',
                'default_subject' => "You're In. Now Go Make Some Money.",
            ],
            'sale-notification' => [
                'name' => 'Sale Notification',
                'description' => 'Sent when someone uses an affiliate\'s coupon',
                'default_subject' => 'New Sale! You earned {{COMMISSION_AMOUNT}} commission',
            ],
            'payment-notification' => [
                'name' => 'Payment Notification',
                'description' => 'Sent when commission payment is processed',
                'default_subject' => 'Commission Payment: {{PAYMENT_AMOUNT}} sent!',
            ],
        ];
    }

    /**
     * Get placeholders for a template type
     */
    public function get_placeholders($template_type) {
        return isset($this->template_placeholders[$template_type])
            ? $this->template_placeholders[$template_type]
            : [];
    }

    /**
     * Get template content (from database or default file)
     */
    public function get_template($template_type) {
        // Check for custom template in database
        $custom = get_option('sst_affiliate_email_' . $template_type);

        if (!empty($custom) && !empty($custom['subject']) && !empty($custom['body'])) {
            return $custom;
        }

        // Fall back to default template file
        return $this->get_default_template($template_type);
    }

    /**
     * Get default template from file
     */
    public function get_default_template($template_type) {
        $template_file = SST_AFFILIATE_PLUGIN_DIR . 'templates/emails/' . $template_type . '.txt';

        if (!file_exists($template_file)) {
            return $this->get_hardcoded_default($template_type);
        }

        $content = file_get_contents($template_file);

        // Parse subject from first line (format: Subject: Your Subject Here)
        $lines = explode("\n", $content, 2);
        $subject = '';
        $body = $content;

        if (strpos($lines[0], 'Subject:') === 0) {
            $subject = trim(str_replace('Subject:', '', $lines[0]));
            $body = isset($lines[1]) ? trim($lines[1]) : '';
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    /**
     * Hardcoded defaults as ultimate fallback
     */
    private function get_hardcoded_default($template_type) {
        $types = $this->get_template_types();
        $default_subject = isset($types[$template_type]['default_subject'])
            ? $types[$template_type]['default_subject']
            : 'Notification from SST.NYC';

        return [
            'subject' => $default_subject,
            'body' => "Hi {{FIRST_NAME}},\n\nThis is an automated notification from SST.NYC.\n\n- The SST Team",
        ];
    }

    /**
     * Save custom template to database
     */
    public function save_template($template_type, $subject, $body) {
        $template = [
            'subject' => sanitize_text_field($subject),
            'body' => wp_kses_post($body),
            'modified' => current_time('mysql'),
        ];

        return update_option('sst_affiliate_email_' . $template_type, $template);
    }

    /**
     * Reset template to default
     */
    public function reset_template($template_type) {
        return delete_option('sst_affiliate_email_' . $template_type);
    }

    /**
     * Render template with placeholders replaced
     */
    public function render($template_type, $data = []) {
        $template = $this->get_template($template_type);

        $subject = $this->replace_placeholders($template['subject'], $data);
        $body = $this->replace_placeholders($template['body'], $data);

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    /**
     * Replace placeholders in content
     */
    private function replace_placeholders($content, $data) {
        foreach ($data as $placeholder => $value) {
            // Support both {{PLACEHOLDER}} and PLACEHOLDER formats
            $content = str_replace('{{' . $placeholder . '}}', $value, $content);
            $content = str_replace($placeholder, $value, $content);
        }

        // Replace any remaining unreplaced placeholders with empty string
        $content = preg_replace('/\{\{[A-Z_]+\}\}/', '', $content);

        return $content;
    }

    /**
     * Send templated email
     */
    public function send($template_type, $to, $data = []) {
        $rendered = $this->render($template_type, $data);

        $headers = ['Content-Type: text/plain; charset=UTF-8'];

        return wp_mail($to, $rendered['subject'], $rendered['body'], $headers);
    }

    /**
     * Preview template with sample data
     */
    public function preview($template_type) {
        $sample_data = $this->get_sample_data($template_type);
        return $this->render($template_type, $sample_data);
    }

    /**
     * Get sample data for preview
     */
    private function get_sample_data($template_type) {
        $common = [
            'FIRST_NAME' => 'John',
            'LAST_NAME' => 'Doe',
            'FULL_NAME' => 'John Doe',
            'EMAIL' => 'john@example.com',
            'SITE_URL' => site_url(),
        ];

        $specific = [
            'affiliate-approved' => [
                'AFFILIATE_ID' => 'AFF-001',
                'COMMISSION_RATE' => '10',
                'REFERRAL_LINK' => site_url('/?ref=johndoe'),
                'COUPON_CODE' => 'JOHND10',
            ],
            'sale-notification' => [
                'ORDER_NUMBER' => '1234',
                'ORDER_TOTAL' => '$199.00',
                'COMMISSION_AMOUNT' => '$19.90',
                'COMMISSION_RATE' => '10',
                'ORDER_DATE' => date('F j, Y g:i A'),
                'PRODUCTS' => "- 32 Hour SST Course x1\n- OSHA 10 Course x1",
                'COUPON_CODE' => 'JOHND10',
            ],
            'payment-notification' => [
                'PAYMENT_AMOUNT' => '$150.00',
                'PAYMENT_DATE' => date('F j, Y'),
                'PAYMENT_NOTES' => 'Monthly payout via PayPal',
                'TOTAL_SALES' => '$1,500.00',
                'TOTAL_COMMISSION' => '$150.00',
                'COMMISSION_PAID' => '$150.00',
                'COMMISSION_PENDING' => '$0.00',
            ],
        ];

        return array_merge($common, isset($specific[$template_type]) ? $specific[$template_type] : []);
    }

    /**
     * Check if template has been customized
     */
    public function is_customized($template_type) {
        $custom = get_option('sst_affiliate_email_' . $template_type);
        return !empty($custom);
    }
}
