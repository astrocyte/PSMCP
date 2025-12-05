<?php
/**
 * Registration List Table
 * Extends WP_List_Table for displaying registrations
 */

if (!defined('ABSPATH')) exit;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class SST_Registration_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => 'registration',
            'plural' => 'registrations',
            'ajax' => false
        ]);
    }

    /**
     * Get columns
     */
    public function get_columns() {
        return [
            'cb' => '<input type="checkbox" />',
            'registration_id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'class_name' => 'Class',
            'sst_number' => 'SST #',
            'cards' => 'Cards',
            'enrollment_status' => 'Account',
            'status' => 'Status',
            'created_at' => 'Registered'
        ];
    }

    /**
     * Sortable columns
     */
    public function get_sortable_columns() {
        return [
            'registration_id' => ['registration_id', false],
            'name' => ['last_name', false],
            'email' => ['email', false],
            'class_name' => ['class_name', false],
            'status' => ['status', false],
            'created_at' => ['created_at', true]
        ];
    }

    /**
     * Column defaults
     */
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'registration_id':
                $view_url = add_query_arg([
                    'page' => 'sst-class-registrations',
                    'action' => 'view',
                    'id' => $item->registration_id
                ], admin_url('admin.php'));
                return '<a href="' . esc_url($view_url) . '"><strong>' . esc_html($item->registration_id) . '</strong></a>';

            case 'name':
                return esc_html($item->first_name . ' ' . $item->last_name);

            case 'email':
                return '<a href="mailto:' . esc_attr($item->email) . '">' . esc_html($item->email) . '</a>';

            case 'phone':
                return '<a href="tel:' . esc_attr($item->phone) . '">' . esc_html($item->phone) . '</a>';

            case 'class_name':
                return esc_html($item->class_name);

            case 'sst_number':
                return $item->sst_number ? esc_html($item->sst_number) : '-';

            case 'cards':
                $icons = [];
                if (!empty($item->osha_card_path)) {
                    $icons[] = '<span title="OSHA Card uploaded" class="dashicons dashicons-id-alt" style="color:#0073aa;"></span>';
                }
                if (!empty($item->sst_card_path)) {
                    $icons[] = '<span title="SST Card uploaded" class="dashicons dashicons-id" style="color:#46b450;"></span>';
                }
                return $icons ? implode(' ', $icons) : '-';

            case 'enrollment_status':
                return $this->get_enrollment_badge($item->enrollment_status);

            case 'status':
                return $this->get_status_badge($item->status);

            case 'created_at':
                return date('M j, Y', strtotime($item->created_at));

            default:
                return isset($item->$column_name) ? esc_html($item->$column_name) : '';
        }
    }

    /**
     * Get enrollment status badge
     */
    private function get_enrollment_badge($status) {
        $badges = [
            'pending' => '<span class="sst-badge sst-badge-pending">Pending</span>',
            'registered' => '<span class="sst-badge sst-badge-success">Created</span>',
            'failed' => '<span class="sst-badge sst-badge-error">Failed</span>'
        ];
        return isset($badges[$status]) ? $badges[$status] : '<span class="sst-badge">' . esc_html($status) . '</span>';
    }

    /**
     * Get status badge
     */
    private function get_status_badge($status) {
        $badges = [
            'new' => '<span class="sst-badge sst-badge-new">New</span>',
            'processed' => '<span class="sst-badge sst-badge-success">Processed</span>',
            'cancelled' => '<span class="sst-badge sst-badge-cancelled">Cancelled</span>'
        ];
        return isset($badges[$status]) ? $badges[$status] : '<span class="sst-badge">' . esc_html($status) . '</span>';
    }

    /**
     * Checkbox column
     */
    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="registration[]" value="%s" />', $item->registration_id);
    }

    /**
     * Get views (status filters)
     */
    public function get_views() {
        $db = SST_Class_Database::get_instance();
        $current_status = isset($_GET['status']) ? $_GET['status'] : '';

        $views = [];

        $all_count = $db->count_registrations();
        $new_count = $db->count_registrations('new');
        $processed_count = $db->count_registrations('processed');
        $cancelled_count = $db->count_registrations('cancelled');

        $base_url = admin_url('admin.php?page=sst-class-registrations');

        $views['all'] = sprintf(
            '<a href="%s" class="%s">All <span class="count">(%d)</span></a>',
            $base_url,
            empty($current_status) ? 'current' : '',
            $all_count
        );

        $views['new'] = sprintf(
            '<a href="%s" class="%s">New <span class="count">(%d)</span></a>',
            add_query_arg('status', 'new', $base_url),
            $current_status === 'new' ? 'current' : '',
            $new_count
        );

        $views['processed'] = sprintf(
            '<a href="%s" class="%s">Processed <span class="count">(%d)</span></a>',
            add_query_arg('status', 'processed', $base_url),
            $current_status === 'processed' ? 'current' : '',
            $processed_count
        );

        $views['cancelled'] = sprintf(
            '<a href="%s" class="%s">Cancelled <span class="count">(%d)</span></a>',
            add_query_arg('status', 'cancelled', $base_url),
            $current_status === 'cancelled' ? 'current' : '',
            $cancelled_count
        );

        return $views;
    }

    /**
     * Bulk actions
     */
    public function get_bulk_actions() {
        return [
            'process' => 'Mark as Processed',
            'delete' => 'Delete'
        ];
    }

    /**
     * Prepare items
     */
    public function prepare_items() {
        $db = SST_Class_Database::get_instance();

        $per_page = 20;
        $current_page = $this->get_pagenum();

        // SECURITY: Whitelist allowed orderby columns to prevent SQL injection
        $allowed_orderby = ['id', 'registration_id', 'first_name', 'last_name', 'email', 'class_name', 'status', 'enrollment_status', 'created_at', 'processed_at'];
        $orderby = isset($_GET['orderby']) && in_array($_GET['orderby'], $allowed_orderby, true) ? $_GET['orderby'] : 'created_at';

        // SECURITY: Whitelist allowed order directions
        $order = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC'], true) ? strtoupper($_GET['order']) : 'DESC';

        $args = [
            'limit' => $per_page,
            'offset' => ($current_page - 1) * $per_page,
            'orderby' => $orderby,
            'order' => $order
        ];

        // Status filter
        if (!empty($_GET['status'])) {
            $args['status'] = sanitize_text_field($_GET['status']);
        }

        // Search
        if (!empty($_GET['s'])) {
            $args['search'] = sanitize_text_field($_GET['s']);
        }

        $this->items = $db->get_registrations($args);
        $total_items = $db->count_registrations(isset($args['status']) ? $args['status'] : '');

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ]);

        $this->_column_headers = [
            $this->get_columns(),
            [],
            $this->get_sortable_columns()
        ];
    }
}
