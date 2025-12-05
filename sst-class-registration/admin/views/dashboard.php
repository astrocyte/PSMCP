<?php
/**
 * Admin Dashboard View - Registration List
 */

if (!defined('ABSPATH')) exit;

$list_table = new SST_Registration_List_Table();
$list_table->prepare_items();

// Handle messages
$message = isset($_GET['message']) ? $_GET['message'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<div class="wrap">
    <h1 class="wp-heading-inline">In-Person Class Registrations</h1>
    <hr class="wp-header-end">

    <?php if ($message): ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?php
                switch ($message) {
                    case 'processed':
                        echo 'Registration marked as processed.';
                        break;
                    case 'deleted':
                        echo 'Registration deleted.';
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

    <form method="get">
        <input type="hidden" name="page" value="sst-class-registrations">
        <?php
        $list_table->views();
        $list_table->search_box('Search Registrations', 'search');
        $list_table->display();
        ?>
    </form>
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
.sst-badge-new {
    background: #0073aa;
    color: #fff;
}
.sst-badge-pending {
    background: #f0ad4e;
    color: #fff;
}
.sst-badge-success {
    background: #46b450;
    color: #fff;
}
.sst-badge-error {
    background: #dc3232;
    color: #fff;
}
.sst-badge-cancelled {
    background: #999;
    color: #fff;
}
</style>
