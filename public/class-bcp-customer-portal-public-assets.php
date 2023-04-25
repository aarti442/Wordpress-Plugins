<?php

class BCPCustomerPortalPublicAssets {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Enquee styles and scripts for portal
     * @since 1.0.0
     * @return void
     */
    public function bcp_enqueue_styles_and_scripts() {

        $this->bcp_include_login_assets();
        $this->bcp_include_list_assets();
        $this->bcp_include_counter_assets();
        $this->bcp_include_recent_activity_assets();
        $this->bcp_include_chart_assets();
        $this->bcp_include_charts_assets();
        $this->bcp_include_detail_assets();
        $this->bcp_include_form_assets();
        $this->bcp_include_profile_assets();
        $this->bcp_include_manage_content_assets();
        $this->bcp_include_search_assets();
        $this->bcp_include_global_search_assets();
        $this->bcp_include_common_assets();
        $this->bcp_enqueue_plugin_assets();
        $this->bcp_localize_plugin_assets();

    }

    //Defult css and js
    public function bcp_enqueue_plugin_assets() {
        //css
        wp_enqueue_style("bcp-custom-css");
        wp_enqueue_style("bcp-font-awesome-css");

        //css standalone
        $pagetemplate = get_post_meta(get_the_ID(), '_wp_page_template', true);
        $template = str_replace('.php', "", $pagetemplate);
        if ($template == 'page-crm-standalone') {
            wp_enqueue_style('bcp-standalone-style');
        }

        //js
        wp_enqueue_script('bcp-jquery');
        wp_enqueue_script('bcp-jquery-matchHeight-js');
        wp_enqueue_script('bcp-custom-js');
    }

    //Common css and js
    public function bcp_include_common_assets() {
        //css
        wp_register_style('bcp-icons-lnr', plugin_dir_url(__FILE__) . 'assets/css/common/icon.css');
        wp_register_style('bcp-flag-style', plugin_dir_url(__FILE__) . 'assets/css/common/flags.css');
        wp_register_style('bcp-custom-css', plugin_dir_url(__FILE__) . 'assets/css/common/custom.css');
        
        wp_register_style('bcp-font-awesome-css', plugin_dir_url(__FILE__) . 'assets/css/font-awesome.min.css');
        //css standalone
        wp_register_style('bcp-standalone-style', plugin_dir_url(__FILE__) . 'assets/css/standalone-template/style.css');

        if (fetch_data_option('bcp_custom_css') && fetch_data_option('bcp_custom_css') != '') {
            $bcp_custom_css = fetch_data_option('bcp_custom_css');
            wp_add_inline_style('bcp-custom-css', $bcp_custom_css);
        }

        // js
        wp_register_script('bcp-jquery', plugin_dir_url(__FILE__) . 'assets/js/common/jquery.js', false, '3.4.1');
        wp_register_script('bcp-jquery-matchHeight-js', plugin_dir_url(__FILE__) . 'assets/js/common/jquery.matchHeight.js', array('jquery'));
        wp_register_script('bcp-custom-js', plugins_url('assets/js/common/front-manage-page.js', __FILE__), array('jquery', 'wp-i18n'));
        wp_set_script_translations('bcp-custom-js', 'bcp_portal', plugin_dir_path(__DIR__) . 'languages');
    }

    //Manage content Block
    public function bcp_include_manage_content_assets() {
        //CSS
        wp_register_style('bcp-knowledge-articles-style', plugin_dir_url(__FILE__) . 'assets/css/manage-content/knowledge_articles.css');
        wp_register_style('bcp-knowledge-articles-style_1', plugin_dir_url(__FILE__) . 'assets/css/knowledgebase/style_1.css');
        wp_register_style('bcp-knowledge-articles-style_2', plugin_dir_url(__FILE__) . 'assets/css/knowledgebase/style_2.css');

        wp_register_style('bcp-search-block-style', plugin_dir_url(__FILE__) . 'assets/css/search/style.css');

        wp_register_style('bcp-case-deflection-style_1', plugin_dir_url(__FILE__) . 'assets/css/case-deflection/style_1.css');
        wp_register_style('bcp-case-deflection-style_2', plugin_dir_url(__FILE__) . 'assets/css/case-deflection/style_2.css');

        //JS
        wp_register_script('bcp-knowledge-articles-script', plugin_dir_url(__FILE__) . 'assets/js/manage-content/knowledge_articles.js', array('jquery'));
    }

    public function bcp_include_search_assets() {
        
    }

    //Global search Block
    public function bcp_include_global_search_assets() {
        wp_register_style('bcp-global-search-block-style', plugin_dir_url(__FILE__) . 'assets/css/global-search/style.css');
    }

    //Login Block
    public function bcp_include_login_assets() {
        wp_register_style('bcp-login-block-style', plugin_dir_url(__FILE__) . 'assets/css/login/login.css');
    }

    //List Block
    public function bcp_include_list_assets() {
        wp_register_style('bcp-list-block-style_1', plugin_dir_url(__FILE__) . 'assets/css/list/base-listing-1.css');
        wp_register_style('bcp-list-block-style_2', plugin_dir_url(__FILE__) . 'assets/css/list/base-listing-2.css');
        wp_register_style('bcp-list-block-style_3', plugin_dir_url(__FILE__) . 'assets/css/list/base-listing-3.css');
        wp_register_style('bcp-modal-css', plugin_dir_url(__FILE__) . 'assets/css/common/modal.css');
    }

    //Counter Block
    public function bcp_include_counter_assets() {
        wp_register_style('bcp-counter-block-style_1', plugin_dir_url(__FILE__) . 'assets/css/counter/style_1.css');
        wp_register_style('bcp-counter-block-style_2', plugin_dir_url(__FILE__) . 'assets/css/counter/style_2.css');
        wp_register_style('bcp-counter-block-style_3', plugin_dir_url(__FILE__) . 'assets/css/counter/style_3.css');
        wp_register_style('bcp-counter-block-style_4', plugin_dir_url(__FILE__) . 'assets/css/counter/style_4.css');
        wp_register_style('bcp-counter-block-style_5', plugin_dir_url(__FILE__) . 'assets/css/counter/style_5.css');
        wp_register_style('bcp-counter-block-style_6', plugin_dir_url(__FILE__) . 'assets/css/counter/style_6.css');
    }

    //Charts
    public function bcp_include_charts_assets() {
        //Js
        wp_register_script('bcp-charts-js', plugin_dir_url(__FILE__) . 'assets/js/charts/highcharts.js', array('jquery'));
    }

    // detail
    public function bcp_include_detail_assets() {
        wp_register_style('bcp-details-block-style', plugin_dir_url(__FILE__) . 'assets/css/detail/case-details.css');
    }

    // chart 
    public function bcp_include_chart_assets() {
        wp_register_script('bcp-chart-block-js', plugin_dir_url(__FILE__) . 'assets/js/chart/highcharts.js');
        wp_register_script('bcp-chart-block-accessibility-js', plugin_dir_url(__FILE__) . 'assets/js/chart/accessibility.js');
        wp_register_script('bcp-chart-block-style_1', plugin_dir_url(__FILE__) . 'assets/js/chart/variable-pie.js');
        wp_register_script('bcp-chart-block-no-data', plugin_dir_url(__FILE__) . 'assets/js/chart/no-data-to-display.js');
    }

    //Add-Edit Block
    public function bcp_include_form_assets() {
        //Css
        wp_register_style('bcp-form-block-style', plugin_dir_url(__FILE__) . 'assets/css/form/style.css');
        wp_register_style('bcp-daterangepicker-style', plugin_dir_url(__FILE__) . 'assets/css/form/daterangepicker.css');

        wp_register_style('bcp-form-datetimepicker-style', plugin_dir_url(__FILE__) . 'assets/css/form/jquery.datetimepicker.min.css');

        //JS
        wp_register_script('bcp-form-script', plugins_url('assets/js/form/bcp-form.js', __FILE__), array('jquery', 'wp-i18n'));
        wp_set_script_translations('bcp-form-script', 'bcp_portal', plugin_dir_path(__DIR__) . 'languages');
        wp_register_script('bcp-form-moment-min-script', plugin_dir_url(__FILE__) . 'assets/js/form/moment.min.js', array('jquery'));
        wp_register_script('bcp-daterangepicker-script', plugin_dir_url(__FILE__) . 'assets/js/form/daterangepicker.js', array('jquery'));
        wp_register_script('bcp-form-datetimepicker-script', plugin_dir_url(__FILE__) . 'assets/js/form/jquery.datetimepicker.full.min.js', array('jquery'));
        wp_register_script('bcp-jquery-validate-script', plugin_dir_url(__FILE__) . 'assets/js/form/jquery.validate.js', array('jquery'));
    }

    // profile
    public function bcp_include_profile_assets() {
        wp_register_style('bcp-profile-block-style', plugin_dir_url(__FILE__) . 'assets/css/profile/style.css');
    }

    // recent activity
    public function bcp_include_recent_activity_assets() {
        wp_register_style('bcp-recent-activity-block-style', plugin_dir_url(__FILE__) . 'assets/css/recent-activity/recent-activity.css');
    }

    public function bcp_localize_plugin_assets() {

        $localize_data = array('ajaxurl' => admin_url('admin-ajax.php'), 'homeurl' => site_url('/'));
        if (bcp_check_wpml_compatibility()) {
            $bcp_current_lang = apply_filters('wpml_current_language', NULL);
            $localize_data['bcp_current_lang'] = $bcp_current_lang;
        }

        wp_localize_script('bcp-custom-js', 'my_ajax_object', $localize_data);
        wp_localize_script('bcp-form-script', 'my_ajax_object', $localize_data);
    }

    //for standalone page template
    public function bcp_include_standalone_template_assets() {
        
    }

    /**
     * Here we will remove all css and js which enqueue and register by default theme, so that our standalone template page will look same in all theme
     * Please note here we can not remove inline css and js, admin or user have to manually remove it
     * Add any css or js name in list which you don't want to remove from standalone template page
     * Remove css or js from list which you want to remove from standalone template page
     *
     * @since 3.0.0
     */
    function bcp_standalone_remove_default_theme_css_js() {

        global $post, $wp_styles, $wp_scripts, $bcp_portal_pages;

        // These css will not remove from our page, standalone template
        $plugin_css = array(
            'admin-bar',
            'wp-block-library',
            'bcp-icons-lnr',
            'bcp-flag-style',
            'bcp-font-awesome-css',
            'bcp-custom-css',
            'bcp-standalone-style',
            'bcp-knowledge-articles-style',
            'bcp-search-block-style',
            'bcp-login-block-style',
            'bcp-list-block-style_1',
            'bcp-list-block-style_2',
            'bcp-list-block-style_3',
            'bcp-counter-block-style_1',
            'bcp-counter-block-style_2',
            'bcp-counter-block-style_3',
            'bcp-counter-block-style_4',
            'bcp-counter-block-style_5',
            'bcp-counter-block-style_6',
            'bcp-details-block-style',
            'bcp-form-block-style',
            'bcp-form-bootstrap-min-style',
            'bcp-form-bootstrap-datetimepicker-style',
            'bcp-profile-block-style',
            'bcp-recent-activity-block-style',
            'wpml-legacy-horizontal-list-0',
            'wpml-legacy-vertical-list-0',
            'wpml-legacy-dropdown-click-0',
            'wpml-legacy-dropdown-0'
        );

        // These js will not remove from our page, standalone template
        $plugin_script = array(
            'bcp-jquery',
            'bcp-jquery-matchHeight-js',
            'bcp-custom-js',
            'bcp-knowledge-articles-script',
            'bcp-charts-js',
            'bcp-chart-block-js',
            'bcp-chart-block-accessibility-js',
            'bcp-chart-block-style_1',
            'bcp-chart-block-no-data',
            'bcp-form-script',
            'bcp-form-moment-min-script',
            'bcp-form-bootstrap-min-script',
            'bcp-form-bootstrap-datetimepicker-script',
            'bcp-jquery-validate-script',
            'wpml-legacy-dropdown-click-0',
            'wpml-legacy-dropdown-0'
        );

        $post_id = get_the_ID();
        if (isset($post_id) && $post_id != '') {

            $pagetemplate = get_post_meta($post_id, '_wp_page_template', true);

            $template = str_replace('.php', "", $pagetemplate);

            if ($template == 'page-crm-standalone') {

                if (isset($wp_styles->queue) && !empty($wp_styles->queue)) {

                    foreach ($wp_styles->queue as $key => $style) {

                        if (!in_array($style, $plugin_css)) {
                            wp_dequeue_style($wp_styles->queue[$key]);
                        }
                    }
                }

                if (isset($wp_scripts->queue) && !empty($wp_scripts->queue)) {

                    foreach ($wp_scripts->queue as $key => $script) {

                        if (!in_array($script, $plugin_script)) {
                            wp_dequeue_script($wp_scripts->queue[$key]);
                        }
                    }
                }
            }
        }
    }

}
