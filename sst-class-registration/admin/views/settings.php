<?php
/**
 * Admin View - Settings Page
 */

if (!defined('ABSPATH')) exit;

// Handle messages
$message = isset($_GET['message']) ? $_GET['message'] : '';

// Get current settings
$form_id = get_option('sst_class_reg_form_id', '');
$class_options = get_option('sst_class_reg_class_options', '');
$auto_enroll = get_option('sst_class_reg_auto_enroll', '1');
$admin_email = get_option('sst_class_reg_admin_email', get_option('admin_email'));
$notify_admin = get_option('sst_class_reg_notify_admin', '1');
$notify_student = get_option('sst_class_reg_notify_student', '1');
$zapier_enabled = get_option('sst_class_reg_zapier_enabled', '0');
$zapier_webhook_url = get_option('sst_class_reg_zapier_webhook_url', '');

// Get WPForms list
$wpforms = SST_Class_Form_Handler::get_wpforms_list();
?>

<div class="wrap">
    <h1>In-Person Class Registration Settings</h1>

    <?php if ($message === 'saved'): ?>
        <div class="notice notice-success is-dismissible">
            <p>Settings saved successfully.</p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <?php wp_nonce_field('sst_class_reg_settings'); ?>
        <input type="hidden" name="action" value="sst_class_reg_save_settings">

        <!-- Form Settings -->
        <div class="postbox" style="margin-top: 20px;">
            <h2 class="hndle" style="padding: 10px 15px; margin: 0;">Form Settings</h2>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="form_id">WPForms Form</label>
                        </th>
                        <td>
                            <?php if (!empty($wpforms)): ?>
                                <select name="form_id" id="form_id">
                                    <option value="">-- Select Form --</option>
                                    <?php foreach ($wpforms as $id => $title): ?>
                                        <option value="<?php echo esc_attr($id); ?>" <?php selected($form_id, $id); ?>>
                                            <?php echo esc_html($title); ?> (ID: <?php echo $id; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description">Select the WPForms form that handles in-person class registrations.</p>
                            <?php else: ?>
                                <input type="text" name="form_id" id="form_id" value="<?php echo esc_attr($form_id); ?>" class="regular-text">
                                <p class="description">WPForms not detected. Enter the form ID manually.</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Class Options -->
        <div class="postbox">
            <h2 class="hndle" style="padding: 10px 15px; margin: 0;">In-Person Class Options</h2>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="class_options">Available Classes</label>
                        </th>
                        <td>
                            <textarea name="class_options" id="class_options" rows="10" class="large-text code"><?php echo esc_textarea($class_options); ?></textarea>
                            <p class="description">
                                Enter one class option per line. These will appear in the registration form dropdown.<br>
                                Example: <code>10 Hr SST</code>, <code>32 Hr SST</code>, <code>OSHA 30</code>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Account Settings -->
        <div class="postbox">
            <h2 class="hndle" style="padding: 10px 15px; margin: 0;">Account Settings</h2>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">Auto-Create User Accounts</th>
                        <td>
                            <label>
                                <input type="checkbox" name="auto_enroll" value="1" <?php checked($auto_enroll, '1'); ?>>
                                Automatically create WordPress user accounts when students register
                            </label>
                            <p class="description">User accounts allow students to log in, track certificates, and be upsold on other courses.</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="postbox">
            <h2 class="hndle" style="padding: 10px 15px; margin: 0;">Email Notifications</h2>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="admin_email">Admin Email</label>
                        </th>
                        <td>
                            <input type="email" name="admin_email" id="admin_email" value="<?php echo esc_attr($admin_email); ?>" class="regular-text">
                            <p class="description">Email address for admin notifications.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Notification Options</th>
                        <td>
                            <label>
                                <input type="checkbox" name="notify_admin" value="1" <?php checked($notify_admin, '1'); ?>>
                                Send email to admin when new registration is received
                            </label><br>
                            <label>
                                <input type="checkbox" name="notify_student" value="1" <?php checked($notify_student, '1'); ?>>
                                Send confirmation email to student after registration
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Zapier Integration -->
        <div class="postbox">
            <h2 class="hndle" style="padding: 10px 15px; margin: 0;">Zapier Integration (Google Sheets Sync)</h2>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">Enable Zapier</th>
                        <td>
                            <label>
                                <input type="checkbox" name="zapier_enabled" value="1" <?php checked($zapier_enabled, '1'); ?>>
                                Send registration data to Zapier webhook
                            </label>
                            <p class="description">Use this to sync registrations to Google Sheets or other apps.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="zapier_webhook_url">Webhook URL</label>
                        </th>
                        <td>
                            <input type="url" name="zapier_webhook_url" id="zapier_webhook_url" value="<?php echo esc_attr($zapier_webhook_url); ?>" class="large-text">
                            <p class="description">
                                Enter your Zapier "Catch Hook" webhook URL.<br>
                                <a href="https://zapier.com/apps/webhook/integrations" target="_blank">Create a Zap with Webhooks by Zapier</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <p class="submit">
            <button type="submit" class="button button-primary">Save Settings</button>
        </p>
    </form>

    <!-- Setup Instructions -->
    <div class="postbox" style="margin-top: 30px;">
        <h2 class="hndle" style="padding: 10px 15px; margin: 0;">Setup Instructions</h2>
        <div class="inside">
            <h4>1. Create the WPForms Registration Form</h4>
            <p>Create a new form in WPForms with these fields (in this exact order):</p>
            <ol>
                <li><strong>Name</strong> - First/Last format, required</li>
                <li><strong>Email</strong> - Required</li>
                <li><strong>Phone</strong> - US format, required</li>
                <li><strong>Dropdown</strong> - "Select Class", required (add your class options manually or use dynamic)</li>
                <li><strong>Text</strong> - "SST Number", optional</li>
                <li><strong>File Upload</strong> - "OSHA Card", optional (jpg, png, pdf)</li>
                <li><strong>File Upload</strong> - "SST Card", optional (jpg, png, pdf)</li>
                <li><strong>Checkbox</strong> - "Terms and Conditions", required</li>
            </ol>

            <h4>2. Configure Settings</h4>
            <p>Select your form above and configure the class options and notification settings.</p>

            <h4>3. Optional: Google Sheets Sync</h4>
            <p>To sync registrations to Google Sheets:</p>
            <ol>
                <li>Create a new Zap at <a href="https://zapier.com" target="_blank">zapier.com</a></li>
                <li>Trigger: "Webhooks by Zapier" &rarr; "Catch Hook"</li>
                <li>Copy the webhook URL and paste it above</li>
                <li>Action: "Google Sheets" &rarr; "Create Spreadsheet Row"</li>
                <li>Map the fields: registration_id, full_name, email, phone, class_name, sst_number, etc.</li>
            </ol>
        </div>
    </div>
</div>
