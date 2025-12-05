<?php
/**
 * Admin View - Single Registration Detail
 */

if (!defined('ABSPATH')) exit;

// $registration is passed from the dashboard
$file_handler = SST_Class_File_Handler::get_instance();

// Handle messages
$message = isset($_GET['message']) ? $_GET['message'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <a href="<?php echo admin_url('admin.php?page=sst-class-registrations'); ?>">&larr;</a>
        Registration: <?php echo esc_html($registration->registration_id); ?>
    </h1>
    <hr class="wp-header-end">

    <?php if ($message): ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?php
                switch ($message) {
                    case 'updated':
                        echo 'Registration updated.';
                        break;
                    case 'processed':
                        echo 'Registration marked as processed.';
                        break;
                    case 'account_created':
                        echo 'User account created successfully.';
                        break;
                    default:
                        echo esc_html($message);
                }
                ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo esc_html($error); ?></p>
        </div>
    <?php endif; ?>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">

            <!-- Main Content -->
            <div id="post-body-content">
                <div class="postbox">
                    <h2 class="hndle">Student Information</h2>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th>Full Name</th>
                                <td><strong><?php echo esc_html($registration->first_name . ' ' . $registration->last_name); ?></strong></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><a href="mailto:<?php echo esc_attr($registration->email); ?>"><?php echo esc_html($registration->email); ?></a></td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td><a href="tel:<?php echo esc_attr($registration->phone); ?>"><?php echo esc_html($registration->phone); ?></a></td>
                            </tr>
                            <tr>
                                <th>Class</th>
                                <td><?php echo esc_html($registration->class_name); ?></td>
                            </tr>
                            <tr>
                                <th>SST Number</th>
                                <td><?php echo $registration->sst_number ? esc_html($registration->sst_number) : '<em>Not provided</em>'; ?></td>
                            </tr>
                            <tr>
                                <th>Registered</th>
                                <td><?php echo date('F j, Y g:i A', strtotime($registration->created_at)); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Uploaded Documents -->
                <div class="postbox">
                    <h2 class="hndle">Uploaded Documents</h2>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th>OSHA Card</th>
                                <td>
                                    <?php if (!empty($registration->osha_card_path)): ?>
                                        <a href="<?php echo esc_url($file_handler->get_secure_url($registration->osha_card_path, $registration->registration_id)); ?>" target="_blank" class="button">
                                            <span class="dashicons dashicons-media-default" style="vertical-align: middle;"></span> View OSHA Card
                                        </a>
                                    <?php else: ?>
                                        <em>Not uploaded</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>SST Card</th>
                                <td>
                                    <?php if (!empty($registration->sst_card_path)): ?>
                                        <a href="<?php echo esc_url($file_handler->get_secure_url($registration->sst_card_path, $registration->registration_id)); ?>" target="_blank" class="button">
                                            <span class="dashicons dashicons-media-default" style="vertical-align: middle;"></span> View SST Card
                                        </a>
                                    <?php else: ?>
                                        <em>Not uploaded</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Update Form -->
                <div class="postbox">
                    <h2 class="hndle">Update Registration</h2>
                    <div class="inside">
                        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                            <?php wp_nonce_field('sst_update_registration'); ?>
                            <input type="hidden" name="action" value="sst_update_registration">
                            <input type="hidden" name="registration_id" value="<?php echo esc_attr($registration->registration_id); ?>">

                            <table class="form-table">
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <select name="status">
                                            <option value="new" <?php selected($registration->status, 'new'); ?>>New</option>
                                            <option value="processed" <?php selected($registration->status, 'processed'); ?>>Processed</option>
                                            <option value="cancelled" <?php selected($registration->status, 'cancelled'); ?>>Cancelled</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td>
                                        <textarea name="notes" rows="4" class="large-text"><?php echo esc_textarea($registration->notes); ?></textarea>
                                    </td>
                                </tr>
                            </table>

                            <p class="submit">
                                <button type="submit" class="button button-primary">Update Registration</button>
                            </p>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div id="postbox-container-1" class="postbox-container">
                <!-- Status -->
                <div class="postbox">
                    <h2 class="hndle">Status</h2>
                    <div class="inside">
                        <p><strong>Registration Status:</strong><br>
                        <?php
                        $status_badges = [
                            'new' => '<span class="sst-badge sst-badge-new">New</span>',
                            'processed' => '<span class="sst-badge sst-badge-success">Processed</span>',
                            'cancelled' => '<span class="sst-badge sst-badge-cancelled">Cancelled</span>'
                        ];
                        echo isset($status_badges[$registration->status]) ? $status_badges[$registration->status] : esc_html($registration->status);
                        ?>
                        </p>

                        <p><strong>Account Status:</strong><br>
                        <?php
                        $enrollment_badges = [
                            'pending' => '<span class="sst-badge sst-badge-pending">Pending</span>',
                            'registered' => '<span class="sst-badge sst-badge-success">Created</span>',
                            'failed' => '<span class="sst-badge sst-badge-error">Failed</span>'
                        ];
                        echo isset($enrollment_badges[$registration->enrollment_status]) ? $enrollment_badges[$registration->enrollment_status] : esc_html($registration->enrollment_status);
                        ?>
                        </p>

                        <?php if ($registration->wp_user_id): ?>
                            <p><strong>WordPress User:</strong><br>
                            <a href="<?php echo admin_url('user-edit.php?user_id=' . $registration->wp_user_id); ?>">
                                View User Profile (#<?php echo $registration->wp_user_id; ?>)
                            </a>
                            </p>
                        <?php endif; ?>

                        <?php if ($registration->processed_at): ?>
                            <p><strong>Processed:</strong><br>
                            <?php echo date('M j, Y g:i A', strtotime($registration->processed_at)); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Actions -->
                <div class="postbox">
                    <h2 class="hndle">Actions</h2>
                    <div class="inside">
                        <?php if ($registration->status === 'new'): ?>
                            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="margin-bottom: 10px;">
                                <?php wp_nonce_field('sst_process_registration'); ?>
                                <input type="hidden" name="action" value="sst_process_registration">
                                <input type="hidden" name="registration_id" value="<?php echo esc_attr($registration->registration_id); ?>">
                                <button type="submit" class="button button-primary" style="width: 100%;">
                                    <span class="dashicons dashicons-yes" style="vertical-align: middle;"></span> Mark as Processed
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($registration->enrollment_status !== 'registered'): ?>
                            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="margin-bottom: 10px;">
                                <?php wp_nonce_field('sst_create_account'); ?>
                                <input type="hidden" name="action" value="sst_create_account">
                                <input type="hidden" name="registration_id" value="<?php echo esc_attr($registration->registration_id); ?>">
                                <button type="submit" class="button" style="width: 100%;">
                                    <span class="dashicons dashicons-admin-users" style="vertical-align: middle;"></span> Create User Account
                                </button>
                            </form>
                        <?php endif; ?>

                        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" onsubmit="return confirm('Are you sure you want to delete this registration?');">
                            <?php wp_nonce_field('sst_delete_registration'); ?>
                            <input type="hidden" name="action" value="sst_delete_registration">
                            <input type="hidden" name="registration_id" value="<?php echo esc_attr($registration->registration_id); ?>">
                            <button type="submit" class="button" style="width: 100%; color: #a00;">
                                <span class="dashicons dashicons-trash" style="vertical-align: middle;"></span> Delete Registration
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.sst-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}
.sst-badge-new { background: #0073aa; color: #fff; }
.sst-badge-pending { background: #f0ad4e; color: #fff; }
.sst-badge-success { background: #46b450; color: #fff; }
.sst-badge-error { background: #dc3232; color: #fff; }
.sst-badge-cancelled { background: #999; color: #fff; }
</style>
