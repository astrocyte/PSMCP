<?php
/**
 * File Upload Handler Class
 * Handles OSHA and SST card file uploads with security protections
 */

if (!defined('ABSPATH')) exit;

class SST_Class_File_Handler {

    private static $instance = null;
    private $upload_dir;
    private $upload_url;

    // SECURITY: Allowed MIME types
    private $allowed_mimes = [
        'image/jpeg',
        'image/png',
        'application/pdf'
    ];

    // SECURITY: Allowed file extensions
    private $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];

    // SECURITY: Max file size (5MB)
    private $max_file_size = 5242880;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $upload = wp_upload_dir();
        $this->upload_dir = $upload['basedir'] . '/sst-registrations/';
        $this->upload_url = $upload['baseurl'] . '/sst-registrations/';
    }

    /**
     * Create upload directory with protection
     */
    public static function create_upload_directory() {
        $upload = wp_upload_dir();
        $dir = $upload['basedir'] . '/sst-registrations/';

        if (!file_exists($dir)) {
            wp_mkdir_p($dir);

            // Create .htaccess to protect directory
            $htaccess = $dir . '.htaccess';
            $htaccess_content = "Options -Indexes\n";
            $htaccess_content .= "<Files \"*\">\n";
            $htaccess_content .= "    <IfModule mod_authz_core.c>\n";
            $htaccess_content .= "        Require all denied\n";
            $htaccess_content .= "    </IfModule>\n";
            $htaccess_content .= "    <IfModule !mod_authz_core.c>\n";
            $htaccess_content .= "        Order deny,allow\n";
            $htaccess_content .= "        Deny from all\n";
            $htaccess_content .= "    </IfModule>\n";
            $htaccess_content .= "</Files>\n";

            file_put_contents($htaccess, $htaccess_content);

            // Create index.php for additional protection
            file_put_contents($dir . 'index.php', '<?php // Silence is golden');

            // SECURITY: index.html for Nginx servers (prevents directory listing)
            file_put_contents($dir . 'index.html', '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>403 Forbidden</title></head><body><h1>Forbidden</h1></body></html>');

            // SECURITY: web.config for IIS servers
            $web_config = '<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <authorization>
            <deny users="*" />
        </authorization>
    </system.webServer>
</configuration>';
            file_put_contents($dir . 'web.config', $web_config);
        }
    }

    /**
     * Process uploaded file from WPForms
     *
     * @param string|array $file_data WPForms file data
     * @param string $type Type of file (osha or sst)
     * @return string Relative file path or empty string
     */
    public function process_upload($file_data, $type) {
        if (empty($file_data)) {
            return '';
        }

        // WPForms stores file path in value_raw as URL or array of URLs
        $file_url = is_array($file_data) ? $file_data[0] : $file_data;

        if (empty($file_url)) {
            return '';
        }

        // Get upload directory info
        $upload = wp_upload_dir();

        // Convert URL to path
        $file_path = str_replace($upload['baseurl'], $upload['basedir'], $file_url);

        // SECURITY: Validate path is within uploads directory (prevent path traversal)
        $real_path = realpath($file_path);
        $allowed_base = realpath($upload['basedir']);

        if (!$real_path || strpos($real_path, $allowed_base) !== 0) {
            error_log('SST Class Registration: Invalid file path detected');
            return '';
        }

        if (!file_exists($real_path)) {
            error_log('SST Class Registration: Uploaded file not found');
            return '';
        }

        // SECURITY: Validate file size
        if (filesize($real_path) > $this->max_file_size) {
            error_log('SST Class Registration: File exceeds size limit');
            if (file_exists($real_path)) {
                unlink($real_path);
            }
            return '';
        }

        // SECURITY: Validate file extension
        $ext = strtolower(pathinfo($real_path, PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowed_extensions, true)) {
            error_log('SST Class Registration: Invalid file extension');
            if (file_exists($real_path)) {
                unlink($real_path);
            }
            return '';
        }

        // SECURITY: Validate MIME type
        if (!$this->validate_mime_type($real_path)) {
            error_log('SST Class Registration: Invalid file MIME type');
            if (file_exists($real_path)) {
                unlink($real_path);
            }
            return '';
        }

        // Generate new filename with random component
        $new_filename = $type . '_' . wp_generate_password(16, false) . '_' . time() . '.' . $ext;
        $new_path = $this->upload_dir . $new_filename;

        // Ensure directory exists
        if (!file_exists($this->upload_dir)) {
            self::create_upload_directory();
        }

        // Move file to our protected directory
        if (copy($real_path, $new_path)) {
            // Delete original from WPForms upload location
            if (file_exists($real_path) && !unlink($real_path)) {
                error_log('SST Class Registration: Failed to delete original file');
            }
            return $new_filename;
        }

        return '';
    }

    /**
     * SECURITY: Validate MIME type using finfo
     */
    private function validate_mime_type($file_path) {
        if (!function_exists('finfo_open')) {
            // Fallback: just check extension if finfo not available
            return true;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file_path);
        finfo_close($finfo);

        return in_array($mime, $this->allowed_mimes, true);
    }

    /**
     * Get full path to uploaded file
     */
    public function get_file_path($filename) {
        if (empty($filename)) {
            return '';
        }

        // SECURITY: Prevent directory traversal in filename
        $filename = basename($filename);
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            return '';
        }

        return $this->upload_dir . $filename;
    }

    /**
     * Get URL for admin preview (with nonce protection)
     */
    public function get_secure_url($filename, $registration_id) {
        if (empty($filename)) {
            return '';
        }

        return add_query_arg([
            'action' => 'sst_view_registration_file',
            'registration_id' => $registration_id,
            'file' => basename($filename),
            'nonce' => wp_create_nonce('sst_view_file_' . $registration_id)
        ], admin_url('admin-ajax.php'));
    }

    /**
     * Delete uploaded file
     */
    public function delete_file($filename) {
        if (empty($filename)) {
            return false;
        }

        $path = $this->get_file_path($filename);
        if (!empty($path) && file_exists($path)) {
            if (!unlink($path)) {
                error_log('SST Class Registration: Failed to delete file');
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Validate file type by extension
     */
    public function is_valid_file_type($filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, $this->allowed_extensions, true);
    }

    /**
     * Serve file for admin preview (AJAX handler)
     */
    public function serve_file() {
        // SECURITY: Check user capability
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized', 'Unauthorized', ['response' => 403]);
        }

        $registration_id = isset($_GET['registration_id']) ? sanitize_text_field($_GET['registration_id']) : '';
        $filename = isset($_GET['file']) ? sanitize_file_name($_GET['file']) : '';
        $nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : '';

        // SECURITY: Additional filename validation
        if (empty($filename) || strpos($filename, '..') !== false) {
            wp_die('Invalid filename', 'Invalid Request', ['response' => 400]);
        }

        // SECURITY: Verify nonce
        if (!wp_verify_nonce($nonce, 'sst_view_file_' . $registration_id)) {
            wp_die('Invalid request', 'Invalid Request', ['response' => 403]);
        }

        // SECURITY: Verify registration exists and file belongs to it
        $db = SST_Class_Database::get_instance();
        $registration = $db->get_registration($registration_id);

        if (!$registration) {
            wp_die('Registration not found', 'Not Found', ['response' => 404]);
        }

        // SECURITY: Verify file belongs to this registration
        $file_basename = basename($filename);
        if ($registration->osha_card_path !== $file_basename && $registration->sst_card_path !== $file_basename) {
            wp_die('File does not belong to this registration', 'Forbidden', ['response' => 403]);
        }

        $path = $this->get_file_path($filename);

        if (empty($path) || !file_exists($path)) {
            wp_die('File not found', 'Not Found', ['response' => 404]);
        }

        // SECURITY: Validate path is within our upload directory
        $real_path = realpath($path);
        $allowed_base = realpath($this->upload_dir);

        if (!$real_path || strpos($real_path, $allowed_base) !== 0) {
            wp_die('Invalid file path', 'Forbidden', ['response' => 403]);
        }

        // Get MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $real_path);
        finfo_close($finfo);

        // SECURITY: Validate MIME type before serving
        if (!in_array($mime, $this->allowed_mimes, true)) {
            wp_die('Invalid file type', 'Forbidden', ['response' => 403]);
        }

        // SECURITY: Set security headers
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Content-Security-Policy: default-src \'none\'');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Output file
        header('Content-Type: ' . $mime);
        // SECURITY: Force download (attachment) instead of inline display to prevent XSS
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Content-Length: ' . filesize($real_path));

        // Clear output buffer
        if (ob_get_level()) {
            ob_end_clean();
        }

        readfile($real_path);
        exit;
    }
}

// Register AJAX handler for file serving
add_action('wp_ajax_sst_view_registration_file', function() {
    SST_Class_File_Handler::get_instance()->serve_file();
});
