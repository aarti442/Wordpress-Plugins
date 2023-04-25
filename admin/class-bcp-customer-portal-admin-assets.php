<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.crmjetty.com
 * @since      1.0.0
 *
 * @package    Customer_Portal_Salesforce_V2
 * @subpackage Customer_Portal_Salesforce_V2/admin
 */
class BCPCustomerPortalAdminAssets {

    private $plugin_name;
    private $version;
    private $bcp_helper;

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->bcp_helper = new BCPCustomerPortalHelper($this->plugin_name, $this->version);
    }

    /** Admin block register styles
     * 
     * @param type $categories
     * @param type $post
     * @return type
     */
    public function register_styles() {
        wp_register_style($this->plugin_name . 'bcp-admin-style', plugin_dir_url(__FILE__) . '/assets/css/bcp-admin.css', array(), null);
    }

    public function enqueue_styles() {
        
    }
    
    /** Admin block register script
     * 
     * @param type $categories
     * @param type $post
     * @return type
     */
    public function register_scripts($hook) {
        wp_register_script($this->plugin_name . 'bcp-admin', plugin_dir_url(__FILE__) . '/assets/js/bcp-admin.js', array('jquery'), null);
        wp_enqueue_style($this->plugin_name . 'bcp-flag', plugin_dir_url(__DIR__) . '/public/assets/css/common/flags.css');
        if ($hook == 'post-new.php' || $hook == 'post.php' || $hook == 'nav-menus.php') {
            wp_enqueue_script($this->plugin_name . 'bcp-admin');
        }
    }

    public function enqueue_scripts() {
        
    }
    
    /** Admin block style and script
     * 
     * @param type $categories
     * @param type $post
     * @return type
     */

    public function enqueue_admin_block_style_scripts() {

        global $objCp;
        if (isset($objCp->access_token) && $objCp->access_token != '') {

            $sf_all_modules = array();
            $portal_page_types = array();

            wp_enqueue_script($this->plugin_name . 'bcp-block-iconpicker', plugin_dir_url(__FILE__) . '/assets/fonticonpicker/js/jquery.fonticonpicker.js', array('jquery'), $this->version, true);
            wp_enqueue_style($this->plugin_name . 'bcp-block-fonticonpicker', plugin_dir_url(__FILE__) . '/assets/fonticonpicker/css/jquery.fonticonpicker.min.css', array('wp-edit-blocks'), $this->version);
            wp_enqueue_style($this->plugin_name . 'bcp-block-icon-font', plugin_dir_url(__FILE__) . '/assets/fonticonpicker/css/icon-font.min.css', array('wp-edit-blocks'), $this->version);
            wp_enqueue_style($this->plugin_name . 'bcp-block-fonticonpicker-bootstrap', plugin_dir_url(__FILE__) . '/assets/fonticonpicker/css/jquery.fonticonpicker.bootstrap.min.css', array('wp-edit-blocks'), $this->version);
            wp_enqueue_style($this->plugin_name . 'bcp-block-fontello', plugin_dir_url(__FILE__) . '/assets/fonticonpicker/fontello-7275ca86/css/fontello.css', array('wp-edit-blocks'), $this->version);
            wp_enqueue_style($this->plugin_name . 'bcp-block-icomoon', plugin_dir_url(__FILE__) . '/assets/fonticonpicker/icomoon/icomoon.css', array('wp-edit-blocks'), $this->version);

            wp_register_script($this->plugin_name . 'bcp-blocks', plugins_url('assets/js/admin-block.js', __FILE__), array('jquery', 'wp-blocks', 'wp-element', 'wp-editor', 'wp-hooks', 'wp-i18n', 'wp-plugins', 'wp-edit-post'), BCP_CUSTOMER_PORTAL_VERSION, false);
            wp_set_script_translations($this->plugin_name . 'bcp-blocks', 'bcp_portal', plugin_dir_path(__DIR__) . 'languages');

            wp_enqueue_style($this->plugin_name . 'bcp-blocks', plugin_dir_url(__FILE__) . '/assets/css/bcp-admin-blocks.css', array('wp-edit-blocks'), $this->version);

            $bcp_modules = fetch_data_option('bcp_portal_modules');

            if (isset($bcp_modules) && is_array($bcp_modules) && count($bcp_modules) > 0) {
                foreach ($bcp_modules as $module_slug => $module_name) {
                    if ($module_slug === 'call') {
                        if (array_key_exists('task', $bcp_modules)) {
                            continue;
                        }
                        $module_slug = 'task';
                    }
                    $sf_all_modules[] = (object) array('label' => $module_name, 'value' => $module_slug);
                }
            }

            $bcp_all_page_types = $GLOBALS['bcp_portal_page_types'];

            $portal_page_types[] = (object) array('label' => 'Select Page Type', 'value' => '');
            foreach ($bcp_all_page_types as $type_slug => $page_data) {
                $page_label = $page_data['label'];
                $portal_page_types[] = (object) array('label' => $page_label, 'value' => $type_slug);
            }

            wp_localize_script($this->plugin_name . 'bcp-blocks', 'my_ajax_object', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'bcp_all_modules' => $sf_all_modules,
                'image_prefix' => plugin_dir_url(__FILE__) . '/assets/images/',
                'bcp_page_types' => $portal_page_types
            ));
        }
    }

    /** block category function
     * 
     * @param type $categories
     * @param type $post
     * @return type
     */
    public function bcp_block_category($categories, $post) {

        $salesforce_category = array('slug' => 'salesforce', 'title' => __('Salesforce Portal', 'bcp_portal'));

        $category_exists = $this->search($categories, 'slug', 'salesforce');

        if (count($category_exists) === 0) {
            return array_merge($categories, array($salesforce_category));
        }
        return $categories;
    }

    /**
     * Function to search value from array
     * @param type $array
     * @param type $key
     * @param type $value
     * @return type
     */
    public function search($array, $key, $value) {

        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->search($subarray, $key, $value));
            }
        }
        return $results;
    }

}
