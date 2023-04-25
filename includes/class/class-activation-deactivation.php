<?php

/**
 * SalesforceCustomerPortalActivation Class
 *
 * @package SalesforceCustomerPortal
 * @since   3.3.2
 */
defined('ABSPATH') || exit;

/**
 * Main SalesforceCustomerPortal.
 *
 * @class SalesforceCustomerPortalActivation
 */
class BCPCustomerPortalActivation {
    /**
     * Function activate plugin
     * 
     * @global type $wpdb
     */

    /**
     * Activation Hook
     * Hook in tabs.
     */
    public static function bcp_portal_activate() {

        global $wpdb;

        $bcp_layouts = $wpdb->base_prefix . 'bcp_layouts';
        $bcp_fields = $wpdb->base_prefix . 'bcp_fields';
        $bcp_setting_tbl = $wpdb->base_prefix . 'bcp_login_attempts';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $bcp_layouts (
            id int(11) NOT NULL AUTO_INCREMENT,
            module varchar(150) NOT NULL,
            view varchar(50) NOT NULL,
            fields text NOT NULL,
            layout longtext NOT NULL,
            properties longtext NOT NULL,
            advance_properties longtext NOT NULL,
            section_layout longtext NOT NULL,
            subpanel longtext NOT NULL,
            role varchar(50),
            icon varchar(50),
            title varchar(50),
            sub_title longtext,
            PRIMARY KEY (id)
	) $charset_collate;";
        
        $sql_fields = "CREATE TABLE $bcp_fields (
            id int(11) NOT NULL AUTO_INCREMENT,
            module varchar(150) NOT NULL,
            fields longtext NOT NULL,
            singular varchar(250),
            plular varchar(250),
            allfields longtext NOT NULL,
            PRIMARY KEY (id)
	) $charset_collate;";

        $sql_login_attempt = "CREATE TABLE $bcp_setting_tbl (
            id int(11) NOT NULL AUTO_INCREMENT,
            ip_address varchar(150) NOT NULL,
            login_attempt int(2),
            last_login_date datetime,
            ip_status ENUM('active','inactive'),
            PRIMARY KEY (id)
	) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
        dbDelta($sql_fields);
        dbDelta($sql_login_attempt);

        //create a random salt and store in db
        $random_setting_salt = wp_generate_password(64, true, true);
        $random_session_salt = wp_generate_password(64, true, true);

        if (fetch_data_option("bcp_portal_setting_key") === FALSE) {
            add_option("bcp_portal_setting_key", $random_setting_salt);
        }
        if (fetch_data_option("bcp_portal_session_key") === FALSE) {
            add_option("bcp_portal_session_key", $random_session_salt);
        }

        // Set up Default options.
        ( fetch_data_option('bcp_name') === FALSE ) ? add_option('bcp_name', __('Customer Portal')) : "";

        ( fetch_data_option('bcp_mobile_menu_title') === FALSE ) ? add_option('bcp_mobile_menu_title', __('Portal Menu', 'bcp_portal')) : "";

        ( fetch_data_option('bcp_standalone_footer_content') === FALSE ) ? add_option('bcp_standalone_footer_content', __('© ' . date('Y') . ' Portal. Registered in the India.', 'bcp_portal')) : "";

        if (!wp_next_scheduled('bcp_thirty_minutes_event')) {
            wp_schedule_event(time(), 'every_thirty_minutes', 'bcp_thirty_minutes_event');
        }
        
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $upload_dir = $upload_dir . '/customer-portal-salesforce-v2-logs';
        if ( !is_dir($upload_dir) ) {
            wp_mkdir_p($upload_dir);
        }
    }

    /**
     * Uninstall hook
     * @global type $wpdb
     */
    public static function bcp_portal_uninstall() {

        global $wpdb, $bcp_portal_pages;

        if (get_option("bcp_keep_portal_settings") == "0") {
            $bcp_layouts = $wpdb->base_prefix . 'bcp_layouts';
            $wpdb->query("DROP TABLE IF EXISTS $bcp_layouts");
            
            $bcp_fields = $wpdb->base_prefix . 'bcp_fields';
            $wpdb->query("DROP TABLE IF EXISTS $bcp_fields");

            $bcp_login_attempt = $wpdb->base_prefix . 'bcp_login_attempts';
            $wpdb->query("DROP TABLE IF EXISTS $bcp_login_attempt");

            if (wp_get_nav_menu_object('Customer Portal Menu')) {
                wp_delete_nav_menu('Customer Portal Menu'); //delete the menu
            }

            $pages = array();
            foreach ($bcp_portal_pages as $page) {
                $pages[] = fetch_data_option($page);
            }

            $portal_menus_selection = wp_get_nav_menus();
            $bcp_portal_modules = fetch_data_option('bcp_portal_modules');
            $bcp_portal_modules['knowledge-articles'] = 'Knowledge Articles';

            foreach ($portal_menus_selection as $objMenu) {
                $menu_items = wp_get_nav_menu_items($objMenu->term_id);

                if (!empty($menu_items)) {
                    foreach ($menu_items as $menu_item) {
                        $post_name = $menu_item->post_name;
                        $menu_id = $menu_item->ID;
                        $page_id = $menu_item->object_id;
                        if (strpos($post_name, 'logout') !== false) {
                            wp_delete_post($menu_id);
                        }
                        if (strpos($post_name, 'my-account') !== false) {
                            wp_delete_post($menu_id);
                        }
                        if (in_array($page_id, $pages)) {
                            wp_delete_post($menu_id);
                        }
                        foreach ($bcp_portal_modules as $bcp_portal) {
                            $module = strtolower(str_replace(" ", "-", $bcp_portal));
                            if (strpos($post_name, $module) !== false) {
                                wp_delete_post($menu_id);
                            }
                        }
                    }
                }
            }

            foreach ($bcp_portal_pages as $page) {

                $bcp_page_id = fetch_data_option($page);
                wp_delete_post($bcp_page_id);
            }

            $delete_options = array(
                'bcp_standalone',
                'bcp_standalone_footer_content',
                'bcp_theme_color',
                'bcp_signup_link_enable',
                'bcp_operation_mode',
                'bcp_charts',
                'bcp_top_modules',
                'bcp_recent_activity',
                'bcp_org_prefix',
                'bcp_login_page',
                'bcp_registration_page',
                'bcp_profile_page',
                'bcp_forgot_password_page',
                'bcp_reset_password_page',
                'bcp_manage_page',
                'bcp_list_page',
                'bcp_detail_page',
                'bcp_dashboard_page',
                'bcp_add_edit_page',
                'bcp_portal_menuLocation',
                'bcp_portal_menuSelection',
                'bcp_name',
                'bcp_records_per_page',
                'bcp_mobile_menu_title',
                'bcp_licence',
                'bcp_licence_key',
                'bcp_password',
                'bcp_security_token',
                'bcp_client_id',
                'bcp_client_secret',
                'bcp_username',
                'bcp_portal_template',
                'bcp_logo',
                'bcp_connection_portal',
                'bcp_customer_verify_enable',
                'bcp_customer_approval_enable',
                'bcp_recaptcha_site_key',
                'bcp_recaptcha_secret_key',
                'bcp_portal_visible_reCAPTCHA',
                'bcp_maximum_login_attempts',
                'bcp_lockout_effective_period',
                'bcp_login_password_enable',
                "bcp_portal_modules",
                "bcp_custom_css",
                "bcp_note_enabled",
                "bcp_kb_enabled",
                "bcp_kb",
                "bcp_case_deflection",
                "bcp_portal_language",
                "bcp_enable_multilang",
                "bcp_default_language",
                "first_time_sync",
                "bcp_portal_setting_key",
                "bcp_portal_session_key",
                "bcp_datetime",
                "bcp_role_objects",
                "bcp_keep_portal_settings",
                "sfcp_primary_role",
                'authentication_method',
                'bcp_private_key',
                'bcp_public_key'
            );
            foreach ($delete_options as $option) {
                delete_option($option);
            }
            
            $upload = wp_upload_dir();
            $upload_dir = $upload['basedir'];
            $upload_dir = $upload_dir . '/customer-portal-salesforce-v2-logs/';
            $files = glob($upload_dir . '/*'); // get all file names
            foreach ($files as $file) { // iterate files
                if (is_file($file)) {
                    unlink($file); // delete file
                }
            }

            wp_clear_scheduled_hook('bcp_thirty_minutes_event');
        }
    }

}
