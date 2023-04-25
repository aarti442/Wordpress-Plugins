<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.crmjetty.com
 * @since      1.0.0
 *
 * @package    salesforce-customer-portal
 * @subpackage salesforce-customer-portal/includes
 */

class BCPCustomerPortal {

    protected $loader;
    protected $plugin_name;
    protected $version;
    protected static $_instance = null;

    public function __construct() {
        if (defined('BCP_CUSTOMER_PORTAL_VERSION')) {
            $this->version = BCP_CUSTOMER_PORTAL_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = BCP_CUSTOMER_PORTAL_NAME;

        $this->bcp_define_constants();
        $this->bcp_load_dependencies();
        $this->bcp_activate_deactivate_plugin();
        $this->bcp_define_globals();
        $this->bcp_load_shortcodes();
        $this->bcp_load_portal_settings_page();
        $this->bcp_set_portal_settings();
        $this->bcp_define_admin_hooks();
        $this->bcp_define_public_hooks();
        $this->run();
    }

    /**
     * Main Biztech Customer Portal Instance.
     *
     * Ensures only one instance of Biztech Customer Portal is loaded or can be loaded.
     *
     * @since 3.0
     * @static
     * @see run_bcp_customer_portal()
     * @return BiztechCustomerPortal - Main instance.
     */
    public static function instance() {

        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Function to define globals
     */
    public function bcp_define_globals() {
        $GLOBALS['bcp_field_prefix'] = "biztechcs__";
        $GLOBALS['url_prefix'] = "biztechcs";
        $GLOBALS['bcp_secure_flag'] = FALSE;
        $GLOBALS['bcp_portal_pages'] = array(
            'bcp_portal_elements',
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
            'bcp_case_deflection',
            'bcp_kb'
        );
        $GLOBALS['bcp_credentials'] = array(
            "bcp_client_id",
            "bcp_client_secret",
            "bcp_username",
            "bcp_password",
            "bcp_security_token",
            "bcp_client_id_jwt",
            "bcp_username_jwt"
        );
        $GLOBALS['percentage_option_values'] = array(
            array(
                "label" => "0 - 25 %",
                "value" => "0-25",
            ),
            array(
                "label" => "26 - 50 %",
                "value" => "26-50",
            ),
            array(
                "label" => "51 - 75 %",
                "value" => "51-75",
            ),
            array(
                "label" => "76 - 100 %",
                "value" => "76-100",
            ),
        );

        $GLOBALS['bcp_portal_page_types'] = array(
            "bcp_dashboard" => array(
                "label" => "Portal Dashboard Page",
                "page_option" => "bcp_dashboard_page"
            ),
            "bcp_list" => array(
                "label" => "Portal List Page",
                "list_label" => " list page",
                "page_option" => "bcp_list_page"
            ),
            "bcp_add_edit" => array(
                "label" => "Portal Add/Edit Page",
                "list_label" => " add/edit page",
                "page_option" => "bcp_add_edit_page"
            ),
            "bcp_detail" => array(
                "label" => "Portal Detail Page",
                "list_label" => " detail page",
                "page_option" => "bcp_detail_page",
            ),
            "bcp_login" => array(
                "label" => "Portal Login Page",
                "page_option" => "bcp_login_page",
            ),
            "bcp_register" => array(
                "label" => "Portal Registration Page",
                "page_option" => "bcp_registration_page",
            ),
            "bcp_forget_password" => array(
                "label" => "Portal Forget Password Page",
                "page_option" => "bcp_forgot_password_page",
            ),
            "bcp_reset_password" => array(
                "label" => "Portal Reset Password Page",
                "page_option" => "bcp_reset_password_page",
            ),
            "bcp_profile" => array(
                "label" => "Portal Edit Profile Page",
                "page_option" => "bcp_profile_page",
            ),
            "bcp_kb" => array(
                "label" => "Portal Knowledge Base",
                "page_option" => "bcp_kb"
            ),
            "bcp_case_deflection" => array(
                "label" => "Portal Case Deflection Page",
                "page_option" => "bcp_case_deflection"
            ),
            "bcp_portal_elements" => array(
                "label" => "Others",
                "page_option" => "bcp_portal_elements"
            ),
        );
    }

    /**
     * Function to define constants
     */
    public function bcp_define_constants() {
        define('BCP_PORTAL', 'salesforce');
        define('BCP_PLUGIN_DIR_PATH', plugin_dir_path(dirname(__FILE__)));
        define('BCP_TEMPLATE_PATH', plugin_dir_path(__FILE__) . 'templates/');
        define('BCP_IMAGES_URL', plugins_url('/public/assets/images/', dirname(__FILE__)));
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $upload_dir = $upload_dir . '/customer-portal-salesforce-v2-logs/';
        define('BCP_DEBUG_PATH', $upload_dir);
    }

    /**
     * Load the required dependencies for this plugin.
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function bcp_load_dependencies() {

        require_once BCP_PLUGIN_DIR_PATH . '/includes/class/class-activation-deactivation.php';
        require_once BCP_PLUGIN_DIR_PATH . '/includes/helpers/bcp-helper.php';
        require_once BCP_PLUGIN_DIR_PATH . '/includes/helpers/template-helper.php';
        require_once BCP_PLUGIN_DIR_PATH . '/includes/helpers/bcp_theme-color.php'; /* Added by Dhart Gajera 07/01/2022 */
        require_once BCP_PLUGIN_DIR_PATH . '/includes/class/class-bcp-crm-api.php';
        require_once BCP_PLUGIN_DIR_PATH . '/includes/class/class-template-functions.php';
        require_once BCP_PLUGIN_DIR_PATH . '/includes/class/class-color.php';
        require_once BCP_PLUGIN_DIR_PATH . '/includes/controller/ApiController.php';
        require_once BCP_PLUGIN_DIR_PATH . '/includes/controller/ApiFormatter.php';
        require_once BCP_PLUGIN_DIR_PATH . '/admin/includes/class-bcp-default-content.php';
        require_once BCP_PLUGIN_DIR_PATH . '/admin/includes/display-settings.php';
        require_once BCP_PLUGIN_DIR_PATH . '/admin/includes/log.php';
        include_once BCP_PLUGIN_DIR_PATH . 'admin/includes/custom-menu.php';
        require_once BCP_PLUGIN_DIR_PATH . '/includes/shortcodes.php';
        require_once BCP_PLUGIN_DIR_PATH . '/includes/actions.php';
        require_once BCP_PLUGIN_DIR_PATH . '/includes/page-templates/bcp-customer-portal-page-templater.php';
        require_once BCP_PLUGIN_DIR_PATH . '/admin/includes/blocks.php';

        require_once BCP_PLUGIN_DIR_PATH . '/includes/class-bcp-customer-portal-loader.php';
        require_once BCP_PLUGIN_DIR_PATH . '/admin/class-bcp-customer-portal-admin-assets.php';
        require_once BCP_PLUGIN_DIR_PATH . '/public/class-bcp-customer-portal-public-assets.php';

        $this->loader = new BCPCustomerPortalLoader();
    }

    /**
     * Activation and Deactivation hooks
     */
    private function bcp_activate_deactivate_plugin() {

        register_activation_hook(BCP_PLUGIN_FILE, array("BCPCustomerPortalActivation", "bcp_portal_activate"));
        register_uninstall_hook(BCP_PLUGIN_FILE, array("BCPCustomerPortalActivation", "bcp_portal_uninstall"));
        //bcp_deactivate_plugin
    }

    /**
     * Admin side load portal page setting
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    private function bcp_load_portal_settings_page() {
        $portal_settings = new BCPCustomerPortalSettings($this->get_plugin_name(), $this->get_version());
        $log_page_setting = new BCPCustomerPortalLogSettings($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_menu', $portal_settings, 'bcp_create_menu');
        $this->loader->add_action('admin_menu', $log_page_setting, 'bcp_create_sub_menu');

        $this->loader->add_action('wp_ajax_bcp_admin_update_layout', $portal_settings, 'sync_crm_layout');
        $this->loader->add_action('wp_ajax_bcp_admin_update_bcp_fields', $portal_settings, 'sync_crm_fields');
        
        $this->loader->add_action('admin_footer-plugins.php', $portal_settings, 'bcp_goodbye_portal');
        $this->loader->add_filter('plugin_action_links_' . plugin_basename(BCP_PLUGIN_FILE) , $portal_settings, 'bcp_filterActionLinks' );
        $this->loader->add_action('wp_ajax_bcp_deactivate_plugin', $portal_settings, 'deactivatePluginCallback');
    }
        
    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     * @author Akshay Kungiri 
     */
    private function bcp_define_admin_hooks() {

        $plugin_admin = new BCPCustomerPortalAdminAssets($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('enqueue_block_editor_assets', $plugin_admin, 'enqueue_admin_block_style_scripts');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'register_scripts', 10, 1);
        $this->loader->add_filter('block_categories_all', $plugin_admin, 'bcp_block_category', 10, 2);
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'register_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_admin, 'enqueue_styles');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     * @author Akshay Kungiri 
     */
    private function bcp_define_public_hooks() {

        $plugin_public = new BCPCustomerPortalPublicAssets($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'bcp_enqueue_styles_and_scripts', 100);
        $this->loader->add_action('wp_print_styles', $plugin_public, 'bcp_standalone_remove_default_theme_css_js', 100);
    }

    /**
     * Load Portal Specific settings
     * This class is basically added for all useful functions
     * 
     * @author Akshay Kungiri 
     */

    private function bcp_set_portal_settings() {
        //Portal Helper Class
        $plugin_helper = new BCPCustomerPortalHelper($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $plugin_helper, 'bcp_init');
        $this->loader->add_action('init', $plugin_helper, 'bcp_verify_user');
        $this->loader->add_action('template_redirect', $plugin_helper, 'bcp_set_headers');

        $this->loader->add_filter('body_class', $plugin_helper, 'bcp_add_body_class');
        $this->loader->add_filter('body_class', $plugin_helper, 'bcp_body_class');
        $this->loader->add_filter('wp_nav_menu_objects', $plugin_helper, 'bcp_change_menu_biztech', 10, 2);

        //Template Functions
        $template_obj = new BCPCustomerPortalTemplateFunctions();
        $template_obj->bcp_define_globals();
        $this->loader->add_action('init', $template_obj, 'bcp_rewrite_portal_url', 10);
        $this->loader->add_action('query_vars', $template_obj, 'register_bcp_custom_query_vars', 1);
        $this->loader->add_action('template_redirect', $template_obj, 'bcp_template_redirect');

        $this->loader->add_filter('page_template', $template_obj, 'bcp_page_template');

        $bcp_modules = fetch_data_option('bcp_portal_modules');
        if (isset($bcp_modules) && is_array($bcp_modules) && count($bcp_modules) > 0) {
            //Portal Admin Blocks (Load All Portal Blocks)
            $portal_admin_blocks = new BCPCustomerPortalAdminBlocks();
           
            $this->loader->add_action('plugins_loaded', $portal_admin_blocks, 'loadPortalBlocks');
            $this->loader->add_action('save_post', $portal_admin_blocks, 'bcp_remove_meta_fields', '99', '3');
            $this->loader->add_action('load-post.php', $portal_admin_blocks, 'bcp_pagetype_selection_setup');
            $this->loader->add_action('load-post-new.php', $portal_admin_blocks, 'bcp_pagetype_selection_setup');
            $this->loader->add_action('init', $portal_admin_blocks, 'bcp_register_type_meta');
            $this->loader->add_action('restrict_manage_posts', $portal_admin_blocks, 'bcp_filter_pagetype_callback');
            $this->loader->add_action('pre_get_posts', $portal_admin_blocks, 'bcp_filter_poststype_callbacks');
            $this->loader->add_action('pre_post_update', $portal_admin_blocks, 'bcp_page_save_validation', 10, 2);
            $this->loader->add_filter('manage_page_posts_columns', $portal_admin_blocks, 'bcp_add_page_type_field_heading', 10);
            $this->loader->add_action('manage_page_posts_custom_column', $portal_admin_blocks, 'bcp_add_page_type_field_value', 10, 2);
            $this->loader->add_action('deleted_post_meta', $portal_admin_blocks, 'bcp_delete_page_option', 10, 4);
        }

        //Menu Multilang Setup Functions
        $multilang_menu = new BCP_Menu_Item_Custom_Meta_Fields($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('load-nav-menus.php', $multilang_menu, 'bcp_manage_multilang_menu_item_meta_fields', 10);
        $this->loader->add_filter('nav_menu_link_attributes', $multilang_menu, 'bcp_multilang_menu_item_change_title', 10, 3);

        //All Portal Regarding Functionality FrontEnd Calls

        $front_callbacks = new BCPCustomerPortalActions($this->get_plugin_name(), $this->get_version());

        //Logout Callback
        if (isset($_REQUEST['bcp-logout']) == 'true') {
            add_action('init', array($front_callbacks, 'bcp_portal_logout'));
        }

        //function to display dashboard (Chart,Counter and recent Activity)
        add_action('wp', array($front_callbacks, 'bcp_page_load_callback'));
        /*** add additional javascript to footer */
        add_action('wp_footer', array($front_callbacks, 'bcp_add_this_script_footer'));
        //Login button click callback
        $this->loader->add_action('wp_ajax_bcp_login', $front_callbacks, 'bcp_login_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_login', $front_callbacks, 'bcp_login_callback');

        //Resend OTP Callback
        $this->loader->add_action('wp_ajax_bcp_resend_otp', $front_callbacks, 'bcp_resend_otp_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_resend_otp', $front_callbacks, 'bcp_resend_otp_callback');

        //Verify OTP Callback
        $this->loader->add_action('wp_ajax_bcp_verify_otp', $front_callbacks, 'bcp_verify_otp_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_verify_otp', $front_callbacks, 'bcp_verify_otp_callback');

        //Change Password Callback
        $this->loader->add_action('wp_ajax_bcp_change_password', $front_callbacks, 'bcp_change_password_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_change_password', $front_callbacks, 'bcp_change_password_callback');

        //Forget password callback
        $this->loader->add_action('wp_ajax_bcp_forgot_password', $front_callbacks, 'bcp_forgot_password_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_forgot_password', $front_callbacks, 'bcp_forgot_password_callback');

        //Form Submit callback
        $this->loader->add_action('wp_ajax_bcp_form_submit', $front_callbacks, 'bcp_form_submit_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_form_submit', $front_callbacks, 'bcp_form_submit_callback');

        //Case Comment Listing Get Callback
        $this->loader->add_action('wp_ajax_bcp_case_comments', $front_callbacks, 'bcp_case_comments_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_case_comments', $front_callbacks, 'bcp_case_comments_callback');

        //Case Notes Listing Get Callback
        $this->loader->add_action('wp_ajax_bcp_case_notes', $front_callbacks, 'bcp_case_notes_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_case_notes', $front_callbacks, 'bcp_case_notes_callback');

        //List Case Attachment Callback
        $this->loader->add_action('wp_ajax_bcp_case_attachments', $front_callbacks, 'bcp_case_attachments_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_case_attachments', $front_callbacks, 'bcp_case_attachments_callback');

        // case close Callback
        $this->loader->add_action('wp_ajax_bcp_case_close', $front_callbacks, 'bcp_case_close_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_case_close', $front_callbacks, 'bcp_case_close_callback');

        // show hide Callback
        $this->loader->add_action('wp_ajax_bcp_show_hide_fields', $front_callbacks, 'bcp_show_hide_fields_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_show_hide_fields', $front_callbacks, 'bcp_show_hide_fields_callback');

        //Download case attachment Callback
        $this->loader->add_action('wp_ajax_bcp_attachment_download', $front_callbacks, 'bcp_attachment_download_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_attachment_download', $front_callbacks, 'bcp_attachment_download_callback');

        //Search result callback
        $this->loader->add_action('wp_ajax_bcp_searchresult', $front_callbacks, 'bcp_searchresult_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_searchresult', $front_callbacks, 'bcp_searchresult_callback');

        //Check license every 30 min
        $this->loader->add_action('bcp_thirty_minutes_event', $front_callbacks, 'add_bcp_thirty_minutes_event');

        //Create cron schedule
      //  $this->loader->add_filter('cron_schedules', $front_callbacks, 'bcp_cron_schedules');

        //Get Solution Data
        $this->loader->add_action('wp_ajax_bcp_solutions', $front_callbacks, 'bcp_solutions');
        $this->loader->add_action('wp_ajax_nopriv_bcp_solutions', $front_callbacks, 'bcp_solutions');

        //Reset Password Callback
        $this->loader->add_action('wp_ajax_bcp_reset_password', $front_callbacks, 'bcp_reset_password_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_reset_password', $front_callbacks, 'bcp_reset_password_callback');

        // Like dislike solution callback
        $this->loader->add_action('wp_ajax_bcp_like_dislike_solutions', $front_callbacks, 'bcp_like_dislike_solutions');
        $this->loader->add_action('wp_ajax_nopriv_bcp_like_dislike_solutions', $front_callbacks, 'bcp_like_dislike_solutions');

        //Check session exits for user
        $this->loader->add_action('wp_ajax_bcp_check_session', $front_callbacks, 'bcp_check_session');
        $this->loader->add_action('wp_ajax_nopriv_bcp_check_session', $front_callbacks, 'bcp_check_session');

        //Clear All Debug logs
        $this->loader->add_action('admin_post_bcp_clear_logs', $front_callbacks, 'bcp_clear_logs_callback');

        //Get All fields by module
        $this->loader->add_action('wp_ajax_bcp_get_fields', $front_callbacks, 'bcp_get_fields_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_get_fields', $front_callbacks, 'bcp_get_fields_callback');

        //List data for any module Callback
        $this->loader->add_action('wp_ajax_bcp_list_portal', $front_callbacks, 'bcp_list_portal_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_list_portal', $front_callbacks, 'bcp_list_portal_callback');

        //Delete data for any module callback
        $this->loader->add_action('wp_ajax_bcp_delete_portal', $front_callbacks, 'bcp_delete_portal_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_delete_portal', $front_callbacks, 'bcp_delete_portal_callback');

        //Get Detail for any module Callback
        $this->loader->add_action('wp_ajax_bcp_detail_portal', $front_callbacks, 'bcp_detail_portal_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_detail_portal', $front_callbacks, 'bcp_detail_portal_callback');

        //Edit method for all modules Callback
        $this->loader->add_action('wp_ajax_bcp_edit_portal', $front_callbacks, 'bcp_edit_portal_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_edit_portal', $front_callbacks, 'bcp_edit_portal_callback');

        //Get KBDocuments data Callback
        $this->loader->add_action('wp_ajax_bcp_KBDocuments', $front_callbacks, 'bcp_KBDocuments');
        $this->loader->add_action('wp_ajax_nopriv_bcp_KBDocuments', $front_callbacks, 'bcp_KBDocuments');

        //Get All Products Based on Pricebook
        $this->loader->add_action('wp_ajax_bcp_get_products', $front_callbacks, 'bcp_get_products_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_get_products', $front_callbacks, 'bcp_get_products_callback');

        //Check Page Type along with Module name
        $this->loader->add_action('wp_ajax_bcp_check_module', $front_callbacks, 'bcp_check_module_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_check_module', $front_callbacks, 'bcp_check_module_callback');

        //Update Option table after page save
        $this->loader->add_action('wp_ajax_bcp_update_option', $front_callbacks, 'bcp_update_option_callback');
        $this->loader->add_action('wp_ajax_nopriv_bcp_update_option', $front_callbacks, 'bcp_update_option_callback');

        //For Change Language Call
        $this->loader->add_action("wp_ajax_bcp_change_language", $front_callbacks, "bcp_change_language");
        $this->loader->add_action("wp_ajax_nopriv_bcp_change_language", $front_callbacks, "bcp_change_language");

        //For Change Language Call
        $this->loader->add_action("wp_ajax_bcp_get_filtered_url", $front_callbacks, "bcp_get_filtered_url_callback");
        $this->loader->add_action("wp_ajax_nopriv_bcp_get_filtered_url", $front_callbacks, "bcp_get_filtered_url_callback");
    }

    /**
     * Load All Shortcodes
     * 
     * @author Akshay Kungiri 
     */
    public function bcp_load_shortcodes() {

        $bcp_shortcode = new BCPCustomerPortalShortcodes($this->get_plugin_name(), $this->get_version());
        $this->loader->add_shortcode('bcp_login', $bcp_shortcode, 'bcp_user_login_callback');
        $this->loader->add_shortcode('bcp_registration', $bcp_shortcode, 'bcp_user_register_callback');
        $this->loader->add_shortcode('bcp_forgot_password', $bcp_shortcode, 'bcp_forgot_password_callback');
        $this->loader->add_shortcode('bcp_reset_password', $bcp_shortcode, 'bcp_reset_password_callback');
        $this->loader->add_shortcode('bcp_profile', $bcp_shortcode, 'bcp_profile_callback');
        $this->loader->add_shortcode('bcp_content', $bcp_shortcode, 'bcp_content_callback');
        $this->loader->add_shortcode('bcp_list', $bcp_shortcode, 'bcp_list_callback');
        $this->loader->add_shortcode('bcp_detail', $bcp_shortcode, 'bcp_detail_callback');
        $this->loader->add_shortcode('bcp_edit', $bcp_shortcode, 'bcp_edit_callback');
        $this->loader->add_shortcode('bcp_counter', $bcp_shortcode, 'bcp_counter_callback');
        $this->loader->add_shortcode('bcp_recent_activity', $bcp_shortcode, 'bcp_recent_activity_callback');
        $this->loader->add_shortcode('bcp_chart', $bcp_shortcode, 'bcp_chart_callback');
        $this->loader->add_shortcode('bcp_search', $bcp_shortcode, 'bcp_search_callback');
        $this->loader->add_shortcode('bcp_knowledgebase', $bcp_shortcode, 'bcp_knowledgebase_callback');
        $this->loader->add_shortcode('bcp_casedeflection', $bcp_shortcode, 'bcp_casedeflection_callback');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     * @author Akshay Kungiri 
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    BCPCustomerPortalLoader    Orchestrates the hooks of the plugin.
     * @author Akshay Kungiri 
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     * @author Akshay Kungiri 
     */
    public function get_version() {
        return $this->version;
    }

}
