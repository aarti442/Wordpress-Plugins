<?php

class BCPCustomerPortalAdminBlocks extends BCPCustomerPortal {

    public $styles = array();
    public $modules = array();
    public $modules_keys = array();
    private $bcp_helper;
    protected $plugin_name;
    protected $version;

    public function __construct() {

        if (defined('BCP_CUSTOMER_PORTAL_VERSION')) {
            $this->version = BCP_CUSTOMER_PORTAL_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = BCP_CUSTOMER_PORTAL_NAME;

        $this->styles = array(
            'style_1',
            'style_2',
            'style_3'
        );

        $this->modules = fetch_data_option('bcp_portal_modules');
        $this->modules_keys = array_keys($this->modules);
        $this->bcp_helper = new BCPCustomerPortalHelper($this->plugin_name, $this->version);
    }

    public function loadPortalBlocks() {
       
        $this->load_login_block();
        $this->load_forget_password_block();
        $this->load_registration_block();
        $this->load_reset_password_block();
        $this->load_list_block();
        $this->load_detail_block();
        $this->load_add_edit_block();
        $this->load_chart_block();
        $this->load_profile_block();
        $this->load_search_block();
        $this->load_content_block();
        $this->load_change_password_block();
        $this->load_counter_block();
        $this->load_recent_activity();
        $this->load_kb_block();
        $this->load_bcp_casedeflection();
    }
    
    /**
     * Registration Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_registration_block() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/portal-registration', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'attributes' => array(
                'sign_up_title' => array(
                    'type' => 'string',
                    'default' => 'Portal Sign Up'
                ),
                'capcha_enable' => array(
                    'type' => 'boolean',
                    'default' => false
                ),
                'view_style' => array(
                    'type' => 'string',
                    'default' => $this->styles[0]
                ),
            ),
            'render_callback' => array($this, 'bcp_registration_callback'))
        );
    }

    /**
     * Registration Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_registration_callback($attributes) {

        $attributes = shortcode_atts(
                array(
                    'sign_up_title' => '',
                    'capcha_enable' => '',
                    'view_style' => '',
                ), $attributes
        );

        $capcha_enable = $attributes['capcha_enable'] ? 'yes' : 'no';

        $register_shortcode = "[bcp_registration sign_up_title='" . $attributes['sign_up_title'] . "'"
                . " capcha_enable='" . $capcha_enable . "'"
                . " view_style='" . $attributes['view_style'] . "' ]";

        return $register_shortcode;
    }

    /**
     * Login Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_login_block() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/portal-login', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'attributes' => array(
                'username_text' => array(
                    'type' => 'string',
                    'default' => 'Portal Username'
                ),
                'password_text' => array(
                    'type' => 'string',
                    'default' => 'Portal Password'
                ),
                'login_button_text' => array(
                    'type' => 'string',
                    'default' => 'Login'
                ),
                'show_forgot_password' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'signup_enable' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'capcha_enable' => array(
                    'type' => 'boolean',
                    'default' => false
                ),
            ),
            'render_callback' => array($this, 'bcp_login_callback'))
        );
    }

    /**
     * Login Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_login_callback($attributes) {

        $attributes = shortcode_atts(
                array(
                    'username_text' => '',
                    'password_text' => '',
                    'login_button_text' => '',
                    'show_forgot_password' => '',
                    'signup_enable' => '',
                    'capcha_enable' => '',
                ), $attributes
        );

        $show_forgot_password = $attributes['show_forgot_password'] ? 'yes' : 'no';
        $signup_enable = $attributes['signup_enable'] ? 'yes' : 'no';
        $capcha_enable = $attributes['capcha_enable'] ? 'yes' : 'no';

        $login_shortcode = "[bcp_login username_text='" . $attributes['username_text'] . "' "
                . " password_text='" . $attributes['password_text'] . "' "
                . " login_button_text='" . $attributes['login_button_text'] . "' "
                . " show_forgot_password='" . $show_forgot_password . "' "
                . " signup_enable='" . $signup_enable . "' "
                . " capcha_enable='" . $capcha_enable . "' ]";

        return $login_shortcode;
    }

    /**
     * Forget password Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_forget_password_block() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/portal-forgot-password', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'attributes' => array(
                'email_text' => array(
                    'type' => 'string',
                    'default' => 'Email Address'
                ),
                'submit_button_text' => array(
                    'type' => 'string',
                    'default' => 'Submit'
                ),
                'capcha_enable' => array(
                    'type' => 'boolean',
                    'default' => false
                )
            ),
            'render_callback' => array($this, 'bcp_forgot_password_callback'))
        );
    }

    /**
     * Forget password Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_forgot_password_callback($attributes) {

        $attributes = shortcode_atts(
                array(
                    'email_text' => '',
                    'submit_button_text' => '',
                    'capcha_enable' => '',
                ), $attributes
        );

        $capcha_enable = $attributes['capcha_enable'] ? 'yes' : 'no';

        $forget_password_shortcode = "[bcp_forgot_password email_text='" . $attributes['email_text'] . "'"
                . " submit_button_text='" . $attributes['submit_button_text'] . "'"
                . " capcha_enable='" . $capcha_enable . "' ]";
        return $forget_password_shortcode;
    }

    /**
     * Reset password Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_reset_password_block() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/portal-reset-password', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'attributes' => array(
                'new_password_text' => array(
                    'type' => 'string',
                    'default' => 'Enter new passsword'
                ),
                'confirm_password_text' => array(
                    'type' => 'string',
                    'default' => 'Enter confirm password'
                ),
                'submit_button_text' => array(
                    'type' => 'string',
                    'default' => 'Submit'
                )
            ),
            'render_callback' => array($this, 'bcp_reset_password_callback'))
        );
    }

    /**
     * Reset password Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_reset_password_callback($attributes) {

        $attributes = shortcode_atts(
                array(
                    'new_password_text' => '',
                    'confirm_password_text' => '',
                    'submit_button_text' => '',
                ), $attributes
        );

        $reset_password_shortcode = "[bcp_reset_password new_password_text='" . $attributes['new_password_text'] . "'"
                . " confirm_password_text='" . $attributes['confirm_password_text'] . "'"
                . " submit_button_text='" . $attributes['submit_button_text'] . "' ]";

        return $reset_password_shortcode;
    }

    /**
     * List Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_list_block() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/portal-list-block', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'attributes' => array(
                'pagination' => array(
                    'type' => 'number',
                    'default' => 10
                ),
                'view_style' => array(
                    'type' => 'string',
                    'default' => $this->styles[0]
                ),
            ), 'render_callback' => array($this, 'bcp_list_callback'))
        );
    }

    /**
     * List Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_list_callback($attributes) {

        $attributes = shortcode_atts(
                array(
                    'pagination' => '',
                    'view_style' => '',
                ), $attributes
        );

        $sfcp_list_shortcode = "[bcp_list pagination='" . $attributes['pagination'] . "'"
                . " view_style='" . $attributes['view_style'] . "' ]";

        return $sfcp_list_shortcode;
    }

    /**
     * Detail Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_detail_block() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/portal-detail-block', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'attributes' => array(
                'view_style' => array(
                    'type' => 'string',
                    'default' => $this->styles[0]
                ),
            ), 'render_callback' => array($this, 'bcp_detail_callback'))
        );
    }

    /**
     * Detail Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_detail_callback($attributes) {

        $attributes = shortcode_atts(
                array(
                    'view_style' => '',
                ), $attributes
        );

        $sfcp_detail_shortcode = "[bcp_detail view_style='" . $attributes['view_style'] . "' ]";

        return $sfcp_detail_shortcode;
    }

    /**
     * Add/Edit Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_add_edit_block() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/portal-add-edit-block', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'attributes' => array(
                'view_style' => array(
                    'type' => 'string',
                    'default' => $this->styles[0]
                ),
            ), 'render_callback' => array($this, 'bcp_add_edit_callback'))
        );
    }

    /**
     * Add/Edit Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_add_edit_callback($attributes) {

        $attributes = shortcode_atts(
                array(
                    'view_style' => '',
                ), $attributes
        );

        $sfcp_manage_shortcode = "[bcp_edit view_style='" . $attributes['view_style'] . "' ]";

        return $sfcp_manage_shortcode;
    }

    /**
     * Profile Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_profile_block() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/portal-profile-block', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'attributes' => array(
                'view_style' => array(
                    'type' => 'string',
                    'default' => $this->styles[0]
                ),
                'edit_profile_title' => array(
                    'type' => 'string',
                    'default' => 'Edit Profile'
                ),
                'change_password_text' => array(
                    'type' => 'string',
                    'default' => 'Change Password'
                ),
                'edit_profile_text' => array(
                    'type' => 'string',
                    'default' => 'Save'
                ),
                'change_password_save_label' => array(
                    'type' => 'string',
                    'default' => 'Update'
                )
            ),
            'render_callback' => array($this, 'bcp_profile_callback'))
        );
    }

    /**
     * Profile Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_profile_callback($attributes) {

        $attributes = shortcode_atts(
                array(
                    'view_style' => '',
                    'edit_profile_title' => '',
                    'change_password_text' => '',
                    'edit_profile_text' => '',
                    'change_password_save_label' => ''
                ), $attributes
        );

        return "[bcp_profile edit_profile_title='" . $attributes['edit_profile_title'] . "'"
                . " change_password_text='" . $attributes['change_password_text'] . "'"
                . " edit_profile_text='" . $attributes['edit_profile_text'] . "'"
                . " change_password_save_label='" . $attributes['change_password_save_label'] . "'"
                . " view_style='" . $attributes['view_style'] . "' ]";
    }

    /**
     * Search Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_search_block() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/portal-search-block', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'render_callback' => array($this, 'bcp_search_callback'))
        );
    }

    /**
     * Search Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_search_callback() {

        return "[bcp_search]";
    }

    /**
     * Content Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_content_block() {
      
        if (!function_exists('register_block_type')) {
            return;
        }
         
        register_block_type('sfcp/portal-content-block', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'render_callback' => array($this, 'bcp_content_callback'))
        );
    }

    /**
     * Search Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_content_callback() {  
        return "[bcp_content]";
    }

    /**
     * Change Password Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_change_password_block() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/portal-change-password-block', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'attributes' => array(
                'module_title' => array(
                    'type' => 'string',
                    'default' => 'Change Password'
                ),
                'view_style' => array(
                    'type' => 'string',
                    'default' => $this->styles[0]
                ),
            ), 'render_callback' => 'bcp_change_password_callback')
        );
    }

    /**
     * Change Password Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_change_password_callback($attributes) {

        $attributes = shortcode_atts(
                array(
                    'module_title' => '',
                    'view_style' => ''
                ), $attributes
        );

        $change_password_shortcode = "[bcp_password module_title='" . $attributes['module_title'] . "'"
                . " view_style='" . $attributes['view_style'] . "' ]";
        return $change_password_shortcode;
    }

    /**
     * Counter Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_counter_block() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/portal-counter-block', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'attributes' => array(
                'module_title' => array(
                    'type' => 'string',
                ),
                'selected_module' => array(
                    'type' => 'string',
                    'default' => $this->modules_keys[0]
                ),
                'view_style' => array(
                    'type' => 'string',
                    'default' => $this->styles[0]
                ),
                'selected_icon' => array(
                    'type' => 'string',
                ),
                'counter_block_id' => array(
                    'type' => 'string',
                ),
                'block_color' => array(
                    'type' => 'string',
                ),
                'font_color' => array(
                    'type' => 'string',
                ),
            ), 'render_callback' => array($this, 'bcp_counter_callback'))
        );
    }

    /**
     * Counter Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_counter_callback($attributes) {

        global $post;
        $post_id = $post->ID;
        $counter_shortcode = "";
        if ($post->post_type == "page") {
            $attributes = shortcode_atts(
                    array(
                        'module_title' => '',
                        'selected_module' => '',
                        'view_style' => '',
                        'selected_field' => '',
                        'selected_field_value' => '',
                        'selected_icon' => '',
                        'counter_block_id' => '',
                        'block_color' => '',
                        'block_hover_color' => '',
                        'font_color' => '',
                        'font_hover_color' => ''
                    ), $attributes
            );

            $selected_module = ( $attributes['selected_module'] ? $attributes['selected_module'] : $this->modules_keys[0]);
            if ($selected_module == "task") {
                $selected_module = "call";
            }
            $module_title = ($attributes['module_title'] ? $attributes['module_title'] : "Number of " . ucfirst($selected_module) );
            $selected_field = $attributes['selected_field'];
            $selected_field_value = $attributes['selected_field_value'];

            $page_counter_block_data = get_post_meta($post_id, 'sfcp_page_counter_data', true);

            $page_counter_meta_data = array(
                'module_name' => $selected_module,
                'field' => $selected_field,
                'field_value' => $selected_field_value
            );

            $counter_block_id = $attributes['counter_block_id'];

            if (isset($page_counter_block_data) && !empty($page_counter_block_data)) {
                $page_counter_block_data[$counter_block_id] = $page_counter_meta_data;
                update_post_meta($post_id, 'sfcp_page_counter_data', $page_counter_block_data);
            } else {
                update_post_meta($post_id, 'sfcp_page_counter_data', array($counter_block_id => $page_counter_meta_data));
            }

            $counter_shortcode = "[bcp_counter module_title='" . $module_title . "'"
                    . " selected_module='" . $selected_module . "'"
                    . " view_style='" . $attributes['view_style'] . "'"
                    . " selected_field='" . $selected_field . "'"
                    . " selected_field_value='" . $selected_field_value . "'"
                    . " selected_icon='" . $attributes['selected_icon'] . "'"
                    . " block_color='" . $attributes['block_color'] . "'"
                    . " block_hover_color='" . $attributes['block_hover_color'] . "' "
                    . " font_color='" . $attributes['font_color'] . "'"
                    . " font_hover_color='" . $attributes['font_hover_color'] . "' "
                    . " counter_block_id='" . $counter_block_id . "' ]";
        }
        return $counter_shortcode;
    }

    /**
     * Chart Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_chart_block() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/chart-block', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'attributes' => array(
                'selected_module' => array(
                    'type' => 'string'
                ),
                'module_title' => array(
                    'type' => 'string'
                ),
                'view_style' => array(
                    'type' => 'string',
                    'default' => $this->styles[0]
                ),
                'selected_field' => array(
                    'type' => 'string'
                ),
                'chart_block_id' => array(
                    'type' => 'string',
                ),
                'legend_enable' => array(
                    'type' => 'boolean',
                )
            ), 'render_callback' => array($this, 'bcp_chart_callback'))
        );
    }

    /**
     * Chart Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_chart_callback($attributes) {

        global $post;
        $post_id = $post->ID;
        $chart_shortcode = "";
        if ($post->post_type == "page") {
            $attributes = shortcode_atts(
                    array(
                        'selected_module' => '',
                        'module_title' => '',
                        'view_style' => '',
                        'selected_field' => '',
                        'chart_block_id' => '',
                        'legend_enable' => ''
                    ), $attributes
            );

           if (($pos = array_search('attachment', $this->modules_keys)) !== false)
            {
                unset($this->modules_keys[$pos]);
                $this->modules_keys = array_values($this->modules_keys);
            }
            $selected_module = ( $attributes['selected_module'] ? $attributes['selected_module'] : $this->modules_keys[0] );

            if ($selected_module == "task") {
                $selected_module = "call";
            }

            $module_title = (isset($_SESSION['bcp_modules'][$selected_module]['plural'])? $_SESSION['bcp_modules'][$selected_module]['plural'] : ucfirst($selected_module) );
            $module_title = ($attributes['module_title'] ? $attributes['module_title'] : $module_title . ' Chart' );
            $legend_enable = $attributes['legend_enable'] ? 'true' : 'false';

            $page_chart_block_data = get_post_meta($post_id, 'sfcp_page_chart_data', true);

            $chart_page_meta_data = array(
                'module_name' => $selected_module,
                'field' => $attributes['selected_field'],
            );

            $chart_block_id = $attributes['chart_block_id'];

            if (isset($page_chart_block_data) && !empty($page_chart_block_data)) {
                $page_chart_block_data[$chart_block_id] = $chart_page_meta_data;
                update_post_meta($post_id, 'sfcp_page_chart_data', $page_chart_block_data);
            } else {
                update_post_meta($post_id, 'sfcp_page_chart_data', array($chart_block_id => $chart_page_meta_data));
            }

            $chart_shortcode = "[bcp_chart selected_module='" . $selected_module . "'"
                    . " module_title='" . $module_title . "'"
                    . " selected_field='" . $attributes['selected_field'] . "' "
                    . " view_style='" . $attributes['view_style'] . "'"
                    . " chart_block_id='" . $chart_block_id . "'"
                    . " legend_enable='" . $legend_enable . "' ]";
        }
        return $chart_shortcode;
    }
    
    /**
     * Recent activity Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_recent_activity() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/portal-recent-activity', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'attributes' => array(
                'selected_module' => array(
                    'type' => 'string',
                    'default' => $this->modules_keys[0]
                ),
                'module_title' => array(
                    'type' => 'string',
                ),
                'view_style' => array(
                    'type' => 'string',
                    'default' => $this->styles[0]
                ),
                'field1_selected_option' => array(
                    'type' => 'string',
                ),
                'field2_selected_option' => array(
                    'type' => 'string',
                ),
                'field3_selected_option' => array(
                    'type' => 'string',
                ),
                'activity_block_id' => array(
                    'type' => 'string',
                ),
                'dropdown_field_color' => array(
                    'type' => 'string',
                )
            ), 'render_callback' => array($this, 'bcp_recent_activity_callback'))
        );
    }

    /**
     * Recent activity Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_recent_activity_callback($attributes) {

        global $post;
        $post_id = $post->ID;
        $recent_activity_shortcode = "";
        if ($post->post_type == "page") {
            $attributes = shortcode_atts(
                    array(
                        'selected_module' => '',
                        'module_title' => '',
                        'view_style' => '',
                        'activity_block_id' => '',
                        'field1_selected_option' => '',
                        'field2_selected_option' => '',
                        'field3_selected_option' => '',
                        'dropdown_field_color' => ''
                    ), $attributes
            );


            $selected_module = ( $attributes['selected_module'] ? $attributes['selected_module'] : $this->modules_keys[0]);
            if ($selected_module == "task") {
                $selected_module = "call";
            }
            $module_title = ($attributes['module_title'] ? $attributes['module_title'] : $selected_module );

            $page_activity_block_data = get_post_meta($post_id, 'sfcp_page_activity_data', true);

            $page_activity_meta_data = array(
                'module_name' => $selected_module,
                'field1_selected_option' => $attributes['field1_selected_option'],
                'field2_selected_option' => $attributes['field2_selected_option'],
                'field3_selected_option' => $attributes['field3_selected_option'],
            );

            $activity_block_id = $attributes['activity_block_id'];

            if (isset($page_activity_block_data) && !empty($page_activity_block_data)) {
                $page_activity_block_data[$activity_block_id] = $page_activity_meta_data;
                update_post_meta($post_id, 'sfcp_page_activity_data', $page_activity_block_data);
            } else {
                update_post_meta($post_id, 'sfcp_page_activity_data', array($activity_block_id => $page_activity_meta_data));
            }

            $recent_activity_shortcode = "[bcp_recent_activity selected_module='" . $selected_module . "'"
                    . " module_title='" . $module_title . "'"
                    . " view_style='" . $attributes['view_style'] . "'"
                    . " field1_selected_option='" . $attributes['field1_selected_option'] . "'"
                    . " field2_selected_option='" . $attributes['field2_selected_option'] . "'"
                    . " field3_selected_option='" . $attributes['field3_selected_option'] . "'"
                    . " dropdown_field_color ='" . $attributes['dropdown_field_color'] . "'"
                    . " activity_block_id='" . $activity_block_id . "']";
        }
        return $recent_activity_shortcode;
    }

    /**
     * Kb Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_kb_block() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/portal-knowledgebase-block', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'attributes' => array(
                'view_style' => array(
                    'type' => 'string',
                    'default' => $this->styles[0]
                ),
                'pagination' => array(
                    'type' => 'number',
                    'default' => 10
                ),
            ), 'render_callback' => array($this, 'bcp_kb_block_callback'))
        );
    }

    /**
     * Kb Block Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_kb_block_callback($attributes) {

        $attributes = shortcode_atts(
                array(
                    'view_style' => '',
                    'pagination' => '',
                ), $attributes
        );

        $sfcp_detail_shortcode = "[bcp_knowledgebase"
                . " pagination='" . $attributes['pagination'] . "'"
                . " view_style='" . $attributes['view_style'] . "' ]";

        return $sfcp_detail_shortcode;
    }

    /**
     * Casedeflection Block
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function load_bcp_casedeflection() {

        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('sfcp/portal-casedeflection-block', array(
            'editor_script' => $this->plugin_name . 'bcp-blocks',
            'attributes' => array(
                'module_description' => array(
                    'type' => 'string',
                ),
                'add_case_title' => array(
                    'type' => 'string'
                ),
                'view_style' => array(
                    'type' => 'string',
                    'default' => $this->styles[0]
                ),
                'pagination' => array(
                    'type' => 'number',
                    'default' => 10
                ),
            ), 'render_callback' => array($this, 'bcp_casedeflection_callback'))
        );
    }

    /**
     * Casedeflection Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_casedeflection_callback($attributes) {

        $attributes = shortcode_atts(
                array(
                    'module_description' => '',
                    'add_case_title' => '',
                    'view_style' => '',
                    'pagination' => ''
                ), $attributes
        );

        $sfcp_detail_shortcode = "[bcp_casedeflection "
                . " module_description='" . $attributes['module_description'] . "'"
                . " add_case_title='" . $attributes['add_case_title'] . "' "
                . " view_style='" . $attributes['view_style'] . "'"
                . " pagination='" . $attributes['pagination'] . "' ]";

        return $sfcp_detail_shortcode;
    }

    /**
     * Remove counter, recent activity , chart data
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_remove_meta_fields($post_id, $post_data, $update) {

        if (wp_is_post_autosave($post_id)) {
            return;
        }

        delete_post_meta($post_id, 'sfcp_page_activity_data');
        delete_post_meta($post_id, 'sfcp_page_chart_data');
        delete_post_meta($post_id, 'sfcp_page_counter_data');
    }

    public function bcp_pagetype_selection_setup() {
        add_action('add_meta_boxes', array($this, 'bcp_typeselection_add_metabox'));
    }

    /**
     * Select Page type
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_typeselection_add_metabox() {
        add_meta_box('bcp_type_selection', esc_html__('Select Page type', 'bcp_portal'), array($this, 'bcp_typeselection_metabox_callback'), 'page', 'side', 'default', array('__back_compat_meta_box' => true));
    }

    /**
     * Select Page type callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_typeselection_metabox_callback($post) {

        global $bcp_portal_page_types;
        $bcp_modules = fetch_data_option('bcp_portal_modules');
        $selected_bcp_page_type = get_post_meta($post->ID, '_bcp_page_type', true);
        $selected_bcp_module = get_post_meta($post->ID, '_bcp_modules', true);

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
        ?>
        <div class="wrapper">
            <p class="post-attributes-label-wrapper parent-id-label-wrapper">
                <label class="post-attributes-label" for="bcp_page_type">Select Page Type</label>
            </p>
            <div class="input input-text">
                <select id="bcp_page_type" name="bcp_page_type" class="components-select-control__input bcp_page_type">
                    <option value="">Select Page Type</option>
                    <?php
                    foreach ($bcp_portal_page_types as $type_slug => $page_data) {
                        $page_label = $page_data['label'];
                        printf('<option value="%s"%s>%s</option>', $type_slug, $type_slug == $selected_bcp_page_type ? ' selected="selected"' : '', $page_label);
                    }
                    ?>
                </select>
            </div>
        </div>
        <?php
        if (isset($sf_all_modules) && count($sf_all_modules) > 0) {
            $hide_css = "display:none;";
            if (isset($selected_bcp_module) && $selected_bcp_module != '') {
                $hide_css = "display:block;";
            }
            ?>
            <div class="wrapper module-selection" style="<?php echo $hide_css; ?>" >
                <p class="post-attributes-label-wrapper parent-id-label-wrapper">
                    <label class="post-attributes-label" for="bcp_modules">Select Module </label>
                </p>
                <div class="input input-select">
                    <select id="bcp_modules" name="bcp_modules" class="components-select-control__input bcp_modules">
                        <?php
                        foreach ($sf_all_modules as $module_information) {
                            $page_label = $module_information->label;
                            printf('<option value="%s"%s>%s</option>', $module_information->value, $module_information->value == $selected_bcp_module ? ' selected="selected"' : '', $page_label);
                        }
                        ?>
                    </select>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Register type
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_register_type_meta() {
        register_post_meta('', '_bcp_page_type', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));

        register_post_meta('', '_bcp_modules', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));

        register_post_meta('', '_bcp_portal_elements', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'boolean',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));
    }

    /**
     * Filter Page type
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_filter_pagetype_callback() {
        global $wpdb, $post_type;
        global $bcp_portal_page_types;

        if ($post_type == 'page') {
            $current_v = isset($_GET['bcp_page_type']) ? $_GET['bcp_page_type'] : '';
            ?><select name="bcp_page_type">
                <option value="">Filter Portal Pages</option>
                <?php
                printf('<option value="%s"%s>%s</option>', 'All', 'All' == $current_v ? ' selected="selected"' : '', 'All Portal Pages');
                foreach ($bcp_portal_page_types as $type_slug => $page_data) {
                    $page_label = $page_data['label'];
                    printf('<option value="%s"%s>%s</option>', $type_slug, $type_slug == $current_v ? ' selected="selected"' : '', $page_label);
                }
                ?>
            </select>
            <?php
        }
    }

    /**
     * Filter Page type Callback
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_filter_poststype_callbacks($query) {
        global $post_type, $pagenow;

        if ($pagenow == 'edit.php' && $post_type == 'page') {
            $bcp_page_type = isset($_GET['bcp_page_type']) && !empty($_GET['bcp_page_type']) ? $_GET['bcp_page_type'] : '';
            if (isset($bcp_page_type) && $bcp_page_type != '') {

                if ($bcp_page_type == 'All') {
                    $query->query_vars['meta_query'] = array(
                        array(
                            'key' => '_bcp_portal_elements',
                            'value' => true,
                            'compare' => '=',
                        ),
                    );
                } else {
                    $query->query_vars['meta_query'] = array(
                        array(
                            'key' => '_bcp_page_type',
                            'value' => $bcp_page_type,
                            'compare' => '=',
                        ),
                    );
                }
            }
        }
    }

    /**
     * save validation
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_page_save_validation($post_ID, $data) {
        if (isset($_POST) && count($_POST) > 0) {
            if (isset($_POST['bcp_page_type']) && $_POST['bcp_page_type'] != '') {
                $page_selection_type = $_POST['bcp_page_type'];
                $page_exists = $this->bcp_helper->get_segregate_url($_POST['bcp_modules'], $page_selection_type);

                if ($page_exists['page_exists'] && $page_exists['page_url'] != '') {
                    $error_message = 'There is one already page created for this combination.';
                    wp_die('<b>Page already exists.</b> ' . $error_message, 'Customer Portal Error', ['back_link' => true]);
                }
                update_post_meta($post_ID, '_bcp_portal_elements', true);
                update_post_meta($post_ID, '_bcp_page_type', $page_selection_type);
                if ($_POST['bcp_modules'] && in_array($page_selection_type, array('bcp_list', 'bcp_add_edit', 'bcp_detail'))) {
                    update_post_meta($post_ID, '_bcp_modules', $_POST['bcp_modules']);
                }
            }
        }
    }

    /**
     * Delete page
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_delete_page_option($deleted_meta_ids, $post_id, $meta_key, $only_delete_these_meta_values) {
        if ('_bcp_page_type' == $meta_key) {
            global $bcp_portal_page_types;
            if (isset($bcp_portal_page_types[$only_delete_these_meta_values])) {
                if ($post_id == get_option($bcp_portal_page_types[$only_delete_these_meta_values]['page_option'])) {
                    delete_option($bcp_portal_page_types[$only_delete_these_meta_values]['page_option']);
                }
            }
        }
    }

    public function bcp_add_page_type_field_heading($defaults) {
        $defaults['bcp_type'] = 'Page Type';
        return $defaults;
    }

    /**
     * Add page type field
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_add_page_type_field_value($column_name, $post_ID) {
        if ($column_name == 'bcp_type') {
            $is_bcp_portal_page = get_post_meta($post_ID, '_bcp_portal_elements', true);
            if (isset($is_bcp_portal_page) && $is_bcp_portal_page != '') {
                global $bcp_portal_page_types;
                $bcp_page_type = get_post_meta($post_ID, '_bcp_page_type', true);
                $selected_bcp_module = get_post_meta($post_ID, '_bcp_modules', true);
                if (( $bcp_page_type && $bcp_page_type != '' ) && isset($bcp_portal_page_types[$bcp_page_type])) {
                    if ($selected_bcp_module != '' && in_array($bcp_page_type, array('bcp_list', 'bcp_add_edit', 'bcp_detail'))) {
                        if ($bcp_portal_page_types[$bcp_page_type]['list_label'] != '') {
                            _e(ucfirst($selected_bcp_module) . $bcp_portal_page_types[$bcp_page_type]['list_label'], 'bcp_portal');
                        } else {
                            _e($bcp_portal_page_types[$bcp_page_type]['label'], 'bcp_portal');
                        }
                    } else {
                        _e($bcp_portal_page_types[$bcp_page_type]['label'], 'bcp_portal');
                    }
                } else {
                    _e('Portal Page', 'bcp_portal');
                }
            } else {
                _e('Website Page', 'bcp_portal');
            }
        }
    }

}
