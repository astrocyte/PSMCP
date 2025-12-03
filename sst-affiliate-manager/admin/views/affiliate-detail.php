<?php
/**
 * Single Affiliate Detail View
 */

if (!defined('ABSPATH')) exit;

// Display messages
if (isset($_GET['message'])) {
    $message = sanitize_text_field($_GET['message']);
    $messages = [
        'approved' => 'Affiliate approved successfully!',
        'updated' => 'Affiliate updated successfully!'
    ];

    if (isset($messages[$message])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($messages[$message]) . '</p></div>';
    }
}
?>

<div class="wrap">
    <h1>Affiliate: <?php echo esc_html($affiliate->affiliate_id); ?> - <?php echo esc_html($affiliate->first_name . ' ' . $affiliate->last_name); ?></h1>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="sst_update_affiliate">
        <input type="hidden" name="affiliate_id" value="<?php echo esc_attr($affiliate->affiliate_id); ?>">
        <?php wp_nonce_field('sst_update_affiliate'); ?>

        <table class="form-table">
            <tr>
                <th>Status</th>
                <td>
                    <select name="status">
                        <option value="pending" <?php selected($affiliate->status, 'pending'); ?>>Pending</option>
                        <option value="approved" <?php selected($affiliate->status, 'approved'); ?>>Approved</option>
                        <option value="rejected" <?php selected($affiliate->status, 'rejected'); ?>>Rejected</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Commission Rate</th>
                <td>
                    <input type="number" name="commission_rate" value="<?php echo esc_attr($affiliate->commission_rate); ?>" step="0.01" min="0" max="100" style="width: 80px;">%
                    <p class="description">Default is 10%. You can negotiate higher rates.</p>
                </td>
            </tr>
        </table>

        <h2>Contact Information</h2>
        <table class="form-table">
            <tr>
                <th>Email</th>
                <td><a href="mailto:<?php echo esc_attr($affiliate->email); ?>"><?php echo esc_html($affiliate->email); ?></a></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><a href="tel:<?php echo esc_attr($affiliate->phone); ?>"><?php echo esc_html($affiliate->phone); ?></a></td>
            </tr>
            <tr>
                <th>Company</th>
                <td><?php echo esc_html($affiliate->company); ?></td>
            </tr>
        </table>

        <h2>Application Details</h2>
        <table class="form-table">
            <tr>
                <th>How they heard about us</th>
                <td><?php echo esc_html($affiliate->referral_source); ?></td>
            </tr>
            <tr>
                <th>Motivation</th>
                <td><?php echo nl2br(esc_html($affiliate->motivation)); ?></td>
            </tr>
            <tr>
                <th>Applied</th>
                <td><?php echo date('F j, Y \a\t g:i A', strtotime($affiliate->created_at)); ?></td>
            </tr>
            <?php if ($affiliate->status === 'approved'): ?>
            <tr>
                <th>Approved</th>
                <td><?php echo date('F j, Y \a\t g:i A', strtotime($affiliate->approved_at)); ?></td>
            </tr>
            <?php endif; ?>
        </table>

        <h2>Referral Information</h2>
        <table class="form-table">
            <tr>
                <th>Referral Link</th>
                <td>
                    <input type="text" value="<?php echo esc_attr($affiliate->referral_link); ?>" readonly class="regular-text" onclick="this.select()">
                    <button type="button" class="button" onclick="navigator.clipboard.writeText('<?php echo esc_js($affiliate->referral_link); ?>'); alert('Copied!')">Copy</button>
                </td>
            </tr>
            <?php if (!empty($affiliate->coupon_code)): ?>
            <tr>
                <th>Coupon Performance</th>
                <td>
                    <?php
                    if (class_exists('WooCommerce')) {
                        $coupon = new WC_Coupon($affiliate->coupon_code);
                        if ($coupon->get_id()) {
                            $usage_count = $coupon->get_usage_count();
                            echo '<p><strong>Total Uses:</strong> ' . esc_html($usage_count) . '</p>';

                            // Calculate commission from orders
                            $commission_manager = new SST_Commission_Manager();
                            $stats = $commission_manager->get_affiliate_stats($affiliate->coupon_code);

                            echo '<p><strong>Total Sales:</strong> $' . number_format($stats['total_sales'], 2) . '</p>';
                            echo '<p><strong>Commission Earned:</strong> $' . number_format($stats['total_commission'], 2) . '</p>';
                            echo '<p><strong>Commission Paid:</strong> <span style="color: #5cb85c;">$' . number_format($stats['commission_paid'], 2) . '</span></p>';
                            echo '<p><strong>Commission Pending:</strong> <span style="color: #f0ad4e;">$' . number_format($stats['commission_pending'], 2) . '</span></p>';
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>WooCommerce Coupon</th>
                <td>
                    <input type="text" value="<?php echo esc_attr($affiliate->coupon_code); ?>" readonly class="regular-text" onclick="this.select()">
                    <button type="button" class="button" onclick="navigator.clipboard.writeText('<?php echo esc_js($affiliate->coupon_code); ?>'); alert('Copied!')">Copy</button>
                    <p class="description">
                        This unique coupon code was automatically generated for this affiliate.
                        <a href="<?php echo admin_url('post.php?post=' . wc_get_coupon_id_by_code($affiliate->coupon_code) . '&action=edit'); ?>" target="_blank">Edit in WooCommerce</a>
                    </p>
                </td>
            </tr>
            <?php elseif ($affiliate->status === 'approved'): ?>
            <tr>
                <th>WooCommerce Coupon</th>
                <td>
                    <p style="color: #d63638;">Coupon not generated. WooCommerce may not be active or coupon generation failed.</p>
                </td>
            </tr>
            <?php endif; ?>
        </table>

        <h2>Admin Notes</h2>
        <table class="form-table">
            <tr>
                <th>Internal Notes</th>
                <td>
                    <textarea name="notes" rows="5" class="large-text"><?php echo esc_textarea($affiliate->notes); ?></textarea>
                    <p class="description">These notes are only visible to admins.</p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="submit" class="button button-primary" value="Update Affiliate">

            <?php if ($affiliate->status === 'pending'): ?>
                <button type="button" class="button button-primary" onclick="if(confirm('Approve this affiliate?')) document.getElementById('approve-form').submit();" style="background: #5cb85c; border-color: #4cae4c;">Approve & Send Email</button>
            <?php endif; ?>

            <button type="button" class="button" onclick="if(confirm('Delete this affiliate? This cannot be undone.')) document.getElementById('delete-form').submit();" style="color: #a00;">Delete Affiliate</button>
        </p>
    </form>

    <!-- Approve form -->
    <form id="approve-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display: none;">
        <input type="hidden" name="action" value="sst_approve_affiliate">
        <input type="hidden" name="affiliate_id" value="<?php echo esc_attr($affiliate->affiliate_id); ?>">
        <?php wp_nonce_field('sst_approve_affiliate'); ?>
    </form>

    <!-- Reject form -->
    <form id="reject-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display: none;">
        <input type="hidden" name="action" value="sst_reject_affiliate">
        <input type="hidden" name="affiliate_id" value="<?php echo esc_attr($affiliate->affiliate_id); ?>">
        <?php wp_nonce_field('sst_reject_affiliate'); ?>
    </form>

    <!-- Delete form -->
    <form id="delete-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display: none;">
        <input type="hidden" name="action" value="sst_delete_affiliate">
        <input type="hidden" name="affiliate_id" value="<?php echo esc_attr($affiliate->affiliate_id); ?>">
        <?php wp_nonce_field('sst_delete_affiliate'); ?>
    </form>
</div>
