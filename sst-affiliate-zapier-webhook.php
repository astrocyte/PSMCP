<?php
/**
 * Plugin Name: SST Affiliate Zapier Webhook
 * Description: Sends WPForms affiliate submissions to Zapier webhook
 * Version: 1.0
 * Author: SST.NYC
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

/**
 * Disable HTML5 email validation to allow .nyc and other new TLDs
 */
add_filter('wpforms_frontend_form_data', 'sst_allow_nyc_emails');

function sst_allow_nyc_emails($form_data) {
    // Only apply to our affiliate form (ID: 5066)
    if (isset($form_data['id']) && $form_data['id'] == '5066') {
        // Find the email field and disable HTML5 validation
        if (isset($form_data['fields'])) {
            foreach ($form_data['fields'] as $field_id => $field) {
                if ($field['type'] === 'email') {
                    $form_data['fields'][$field_id]['disable_html5'] = true;
                }
            }
        }
    }
    return $form_data;
}

/**
 * Add custom success message for affiliate form
 */
add_filter('wpforms_frontend_confirmation_message', 'sst_affiliate_success_message', 10, 4);

function sst_affiliate_success_message($message, $form_data, $fields, $entry_id) {
    // Only apply to our affiliate form (ID: 5066)
    if ($form_data['id'] != '5066') {
        return $message;
    }

    // Get the applicant's first name for personalization
    $first_name = isset($fields[0]['first']) ? esc_html($fields[0]['first']) : 'there';

    // Custom success message
    $message = '
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <div style="font-size: 48px; margin-bottom: 10px;">🎉</div>
        <h2 style="color: white; margin: 0 0 15px 0; font-size: 28px;">Thank You, ' . $first_name . '!</h2>
        <p style="font-size: 18px; margin: 0 0 20px 0; color: #f0f0f0;">Your affiliate application has been successfully submitted!</p>

        <div style="background: rgba(255,255,255,0.2); padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="color: white; margin: 0 0 15px 0; font-size: 20px;">What Happens Next?</h3>
            <ul style="list-style: none; padding: 0; margin: 0; text-align: left; max-width: 500px; margin: 0 auto;">
                <li style="padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.2);">✅ Check your email for confirmation</li>
                <li style="padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.2);">⏱️ We\'ll review your application within 2-3 business days</li>
                <li style="padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.2);">🎁 Once approved, you\'ll receive your affiliate link & QR code</li>
                <li style="padding: 8px 0;">💰 Start earning 10% commission on every referral!</li>
            </ul>
        </div>

        <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.3);">
            <p style="margin: 0; font-size: 14px; color: #f0f0f0;">Questions? Email us at <strong>affiliates@sst.nyc</strong></p>
        </div>
    </div>
    ';

    return $message;
}

/**
 * Send affiliate form submissions to Zapier
 */
add_action('wpforms_process_complete', 'sst_send_affiliate_to_zapier', 10, 4);

function sst_send_affiliate_to_zapier($fields, $entry, $form_data, $entry_id) {
    // Only process our affiliate form (ID: 5066)
    if ($form_data['id'] != '5066') {
        return;
    }

    // Extract form data
    // Field IDs from the form configuration:
    // 0 = Full Name, 1 = Email, 2 = Phone, 3 = Company, 4 = Referral Source, 5 = Motivation, 6 = Terms

    $data = [
        'entry_id' => $entry_id,
        'timestamp' => current_time('mysql'),
        'first_name' => isset($fields[0]['first']) ? sanitize_text_field($fields[0]['first']) : '',
        'last_name' => isset($fields[0]['last']) ? sanitize_text_field($fields[0]['last']) : '',
        'email' => isset($fields[1]['value']) ? sanitize_email($fields[1]['value']) : '',
        'phone' => isset($fields[2]['value']) ? sanitize_text_field($fields[2]['value']) : '',
        'company' => isset($fields[3]['value']) ? sanitize_text_field($fields[3]['value']) : '',
        'referral_source' => isset($fields[4]['value']) ? sanitize_text_field($fields[4]['value']) : '',
        'motivation' => isset($fields[5]['value']) ? sanitize_textarea_field($fields[5]['value']) : '',
        'terms_accepted' => isset($fields[6]['value']) ? 'Yes' : 'No',
        'form_id' => $form_data['id'],
        'site_url' => get_site_url()
    ];

    // Zapier webhook URL - REPLACE THIS WITH YOUR ACTUAL ZAPIER WEBHOOK URL
    $zapier_webhook_url = get_option('sst_zapier_webhook_url', '');

    // Skip if webhook URL not configured yet
    if (empty($zapier_webhook_url)) {
        error_log('SST Affiliate Webhook: No webhook URL configured. Set it in WordPress admin.');
        return;
    }

    // Send to Zapier
    $response = wp_remote_post($zapier_webhook_url, [
        'method' => 'POST',
        'timeout' => 15,
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode($data),
    ]);

    // Log errors for debugging
    if (is_wp_error($response)) {
        error_log('SST Affiliate Webhook Error: ' . $response->get_error_message());
    } else {
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code != 200) {
            error_log('SST Affiliate Webhook: Zapier returned status code ' . $status_code);
        }
    }
}

/**
 * Add admin settings page for webhook URL
 */
add_action('admin_menu', 'sst_webhook_admin_menu');

function sst_webhook_admin_menu() {
    add_options_page(
        'SST Affiliate Webhook Settings',
        'SST Webhook',
        'manage_options',
        'sst-affiliate-webhook',
        'sst_webhook_settings_page'
    );
}

function sst_webhook_settings_page() {
    // Save settings
    if (isset($_POST['sst_webhook_url']) && check_admin_referer('sst_webhook_settings')) {
        update_option('sst_zapier_webhook_url', sanitize_text_field($_POST['sst_webhook_url']));
        echo '<div class="notice notice-success"><p>Webhook URL saved!</p></div>';
    }

    $webhook_url = get_option('sst_zapier_webhook_url', '');
    ?>
    <div class="wrap">
        <h1>SST Affiliate Zapier Webhook Settings</h1>

        <form method="post" action="">
            <?php wp_nonce_field('sst_webhook_settings'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="sst_webhook_url">Zapier Webhook URL</label>
                    </th>
                    <td>
                        <input
                            type="url"
                            id="sst_webhook_url"
                            name="sst_webhook_url"
                            value="<?php echo esc_attr($webhook_url); ?>"
                            class="regular-text"
                            placeholder="https://hooks.zapier.com/hooks/catch/..."
                        />
                        <p class="description">
                            Get this URL from Zapier: Create a new Zap → Choose "Webhooks by Zapier" →
                            "Catch Hook" → Copy the webhook URL and paste it here.
                        </p>
                    </td>
                </tr>
            </table>

            <?php submit_button('Save Webhook URL'); ?>
        </form>

        <hr>

        <h2>Testing</h2>
        <p>To test the webhook:</p>
        <ol>
            <li>Set up a Zapier Zap with "Webhooks by Zapier" → "Catch Hook" trigger</li>
            <li>Copy the webhook URL from Zapier and paste it above</li>
            <li>Save the settings</li>
            <li>Submit a test form at: <a href="<?php echo get_site_url(); ?>/affiliate-signup/" target="_blank">Affiliate Signup Page</a></li>
            <li>Check Zapier to see if the data was received</li>
        </ol>

        <h2>Troubleshooting</h2>
        <p><strong>Current Status:</strong></p>
        <ul>
            <li>Webhook URL configured: <?php echo !empty($webhook_url) ? '✅ Yes' : '❌ No'; ?></li>
            <li>Form ID monitoring: 5066 (SST Affiliate Program Application)</li>
            <li>Plugin active: ✅ Yes</li>
        </ul>

        <?php if (!empty($webhook_url)): ?>
        <p>
            <a href="<?php echo get_site_url(); ?>/affiliate-signup/" class="button button-secondary" target="_blank">
                Test Form Submission →
            </a>
        </p>
        <?php endif; ?>
    </div>
    <?php
}
