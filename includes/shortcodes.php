<?php

defined('ABSPATH') or die('No script kiddies please!');

class BCPCustomerPortalShortcodes {

    private $plugin_name;
    private $version;
    private $bcp_helper;
    private $portal_actions;
    private $template;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->bcp_helper = new BCPCustomerPortalHelper($this->plugin_name, $this->version);
        $this->portal_actions = new BCPCustomerPortalActions($this->plugin_name, $this->version);
        $this->template = new TemplateHelper( BCP_PLUGIN_DIR . "/public/partials/" );
        $this->themecolor = new BCPCustomerPortalThemeColor();
    }
    
    /**
     * Login UI
     *
     * @param string $content
     * @return void
     * @author Akshay Kungiri 
     */

    public function bcp_user_login_callback($atts, $content = '') {
        global $wpdb;
        
        if (isset($_REQUEST['action']) && ($_REQUEST['action']) == 'edit') {
            return;
        }
        
        ob_start();
        $bcp_authentication = fetch_data_option("bcp_authentication");  
       
        if ( isset( $bcp_authentication ) && $bcp_authentication != null ) {
            $bcp_licence_validate = fetch_data_option('bcp_licence_validate', date('Y-m-d'));
            if(fetch_data_option( 'bcp_licence' ) == 0 ){
                do_action('bcp_thirty_minutes_event');
            }
            $bcp_licence = fetch_data_option( 'bcp_licence' );
            if ( $bcp_licence ) {
                $bcp_name = fetch_data_option( 'bcp_name' );
                $bcp_logo = fetch_data_option( 'bcp_logo' );

                $bcp_logo_url = "";
                if ($bcp_logo != null) {
                    $bcp_logo_url = wp_get_attachment_image_src($bcp_logo, 'full');
                    $bcp_logo_url = $bcp_logo_url[0];
                }
                
                wp_enqueue_style('bcp-flag-style');
                wp_enqueue_style('bcp-login-block-style');
                wp_enqueue_style('bcp-icons-lnr');
                
                wp_enqueue_script('bcp-jquery-validate-script');
                wp_enqueue_script('bcp-form-script');

                
                $bcp_forgot_password_page = fetch_data_option( 'bcp_forgot_password_page' );
                $bcp_registration_page = fetch_data_option( 'bcp_registration_page' );
                $enable_signup_link = ( isset($atts['signup_enable']) && $atts['signup_enable'] == "yes" && $bcp_registration_page);
                $enable_forgot_pwd_link = ($bcp_forgot_password_page && isset($atts['show_forgot_password']) && $atts['show_forgot_password'] == "yes");
                $enable_reCaptcha = FALSE;
                if ( isset($atts['capcha_enable']) && $atts['capcha_enable'] == "yes" && fetch_data_option('bcp_recaptcha_site_key') != "" && fetch_data_option('bcp_recaptcha_secret_key') != "" ) {
                    $enable_reCaptcha = TRUE;
                }
                
                $bcp_portal_login_min = fetch_data_option("bcp_lockout_effective_period");
                $bcp_maximum_login_attempts = fetch_data_option( 'bcp_maximum_login_attempts' );
                $ip = $this->bcp_helper->bcp_getUserIpAddr();
                $record = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_login_attempts WHERE ip_address='{$ip}'");
                $login_attempt = isset($record->login_attempt) ? $record->login_attempt : 1;
                $last_login_date = isset($record->last_login_date) ? $record->last_login_date : date("Y-m-d H:i:s");
                $disabled = "";
                $bcp_last_login_date = new DateTime($last_login_date);
                $since_start = $bcp_last_login_date->diff( new DateTime() );
                $min_diff = $since_start->i;
                $sec_diff = $since_start->s;
                $bcp_login_password_enable = fetch_data_option('bcp_login_password_enable');
                if ( $bcp_login_password_enable && $login_attempt >= $bcp_maximum_login_attempts && $min_diff < $bcp_portal_login_min ) {
                    $disabled = "disabled='disabled'";
                    $bcp_portal_login_min -= 1;
                    $min_diff = $bcp_portal_login_min - $min_diff;
                    $sec_diff = 60 - $sec_diff;
                    if ($min_diff < 10) {
                        $min_diff = "0".$min_diff;
                    }
                } else {
                    if ( (!$bcp_login_password_enable || $login_attempt >= $bcp_maximum_login_attempts) && !empty($record) ) {
                        $wpdb->update(
                            $wpdb->base_prefix . 'bcp_login_attempts', array(
                                'ip_address' => $ip,
                                'login_attempt' => "0",
                                'last_login_date' => date("Y-m-d H:i:s"),
                                'ip_status' => "active",
                            ), array('id' => $record->id)
                        );
                    }
                }
                
                if(get_option('bcp_theme_color')){
                    $this->themecolor->theme_color_login_style();
                }
                
                $language_text = (isset($atts['language_text']) && $atts['language_text'] != "") ? $atts['language_text'] : __("Select Portal Language", 'bcp_portal');
                $username_text = (isset($atts['username_text']) && $atts['username_text'] != "") ? $atts['username_text'] : __("Username", 'bcp_portal');
                $password_text = (isset($atts['password_text']) && $atts['password_text'] != "") ? $atts['password_text'] : __("Password", 'bcp_portal');
                $login_button_text = (isset($atts['login_button_text']) && $atts['login_button_text'] != "") ? $atts['login_button_text'] : __("Submit", 'bcp_portal');
                
                // Render Data into template related to style
                
                $data = array(
                    'bcp_logo_url'             => $bcp_logo_url,
                    'bcp_name'                 => $bcp_name,
                    'bcp_forgot_password_page' => $bcp_forgot_password_page,
                    'bcp_registration_page'    => $bcp_registration_page,
                    'enable_signup_link'        => $enable_signup_link,
                    'enable_forgot_pwd_link'    => $enable_forgot_pwd_link,
                    'enable_reCaptcha'          => $enable_reCaptcha,
                    'disabled'                  => $disabled,
                    'min_diff'                  => $min_diff,
                    'sec_diff'                  => $sec_diff,
                    'language_text'             => $language_text,
                    'username_text'             => $username_text,
                    'password_text'             => $password_text,
                    'login_button_text'         => $login_button_text,
                    'template_helper'           => $this->template,
                    'bcp_helper'                => $this->bcp_helper,
                );
                if (bcp_check_wpml_compatibility()) {
                    $this->template = new TemplateHelper(BCPWPML_PATH . "/partials/");
                    $this->template->render('/public/login/style1.php', $data);
                } else {
                    $this->template->render('/login/style1.php', $data);
                }

                if ( $enable_reCaptcha ) {
                ?>
                <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
                    async defer>
                </script>
                <script type="text/javascript">
                  var onloadCallback = function() {
                    widget1 = grecaptcha.render('scp-recaptcha', {
                      'sitekey' : '<?php echo fetch_data_option("bcp_recaptcha_site_key"); ?>',
                    });
                  };
                </script>
                <?php } ?>
                <script>
                    jQuery( document ).ready( function() {
                        var timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                        jQuery('.browser_timezone').val(timeZone);
                        <?php if (!empty($disabled)) { ?>
                        
                        var time_min = parseInt(jQuery("#sfcp-login-attempt-min").text());
                        var time_sec = parseInt(jQuery("#sfcp-login-attempt-second").text());
                        //Login attempt counter script
                        setInterval(function(){
                            time_min = parseInt(jQuery("#sfcp-login-attempt-min").text());
                            time_sec = parseInt(jQuery("#sfcp-login-attempt-second").text());
                            console.log(time_sec);
                            if (time_sec == 0) {
                                time_min -= 1;
                                time_sec = 60;
                            } else {
                                time_sec -= 1;
                            }
                            
                            if ( time_min == 0 && time_sec == 0 ) {
                                window.location.reload();
                            }
                            if (time_min < 10) {
                                time_min = "0" + time_min;
                            }
                            if (time_sec < 10) {
                                time_sec = "0" + time_sec;
                            }
                            jQuery("#sfcp-login-attempt-min").text(time_min);
                            jQuery("#sfcp-login-attempt-second").text(time_sec);
                        }, 1000);
                        <?php } ?>
                    });
                </script>
                <?php } else {  ?>
                    <span class='error error-line error-msg settings-error'><?php _e( 'Please contact to administrator.', 'bcp_portal' ); ?></span>
                <?php
            }
        } else {
            ?>
                <span class='error error-line error-msg settings-error'><?php _e( 'Portal is disabled. Please contact your administrator.', 'bcp_portal' ); ?></span>
            <?php
        }
        $content .= ob_get_clean();

        return $content;

    }
    
    /**
     * Registration UI
     *
     * @param string $content
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_user_register_callback($atts , $content = '') {

        global $objCp, $bcp_field_prefix, $wpdb;
        if (isset($_REQUEST['action']) && ($_REQUEST['action']) == 'edit') {
            return;
        }
        
        //enqueue css
        wp_enqueue_style('bcp-icons-lnr');
        wp_enqueue_style('bcp-flag-style');
        wp_enqueue_style('bcp-form-block-style');
        wp_enqueue_style('bcp-form-datetimepicker-style');
        
        //enqueue js
        wp_enqueue_script('bcp-jquery-validate-script');
        wp_enqueue_script('bcp-form-script');
        wp_enqueue_script('bcp-form-moment-min-script');
        wp_enqueue_script('bcp-form-datetimepicker-script');
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        ob_start();
        if (isset($bcp_authentication) && $bcp_authentication != null) {
            $bcp_licence = fetch_data_option('bcp_licence');
            if ($bcp_licence) {
                $bcp_signup_title = (isset($atts['sign_up_title']) && $atts['sign_up_title'] != "") ? __($atts['sign_up_title'], 'bcp_portal') : __("Portal Sign Up", 'bcp_portal');
                $view_style = $atts['view_style'];
                
                if(get_option('bcp_theme_color')){
                    $this->themecolor->theme_color_edit_style();
                }
                if (bcp_check_wpml_compatibility()) {
                    ?>
                    <div class="bcp_hide_lang_wrapper" style="display: none;">
                        <?php echo do_shortcode('[wpml_language_selector_widget]'); ?>
                    </div>
                <?php } ?>                
                <div id="responsedataPortal"></div>
                <script>
                    jQuery(document).ready(function(){
                        var module_name = "contact";
                        var capcha_enable = "<?php echo $atts['capcha_enable']; ?>";
                        var view_style = "<?php echo $view_style; ?>";
                        var form_title = "<?php echo $bcp_signup_title; ?>";
                        bcp_render_edit_form(module_name, "", form_title, view_style, capcha_enable);
                    });
                </script>
                <?php } else { ?>
                <span class='error error-line error-msg settings-error'><?php _e('Please contact to administrator.', 'bcp_portal'); ?></span>
                <?php
            }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Portal is disabled. Please contact your administrator.', 'bcp_portal'); ?></span>
            <?php
        }
        $content .= ob_get_clean();

        return $content;
    }
    
    /**
     * Forgot password UI
     *
     * @param string $content
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_forgot_password_callback( $atts , $content = '') {

        if (isset($_REQUEST['action']) && ($_REQUEST['action']) == 'edit') {
            return;
        }
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        ob_start();
        if (isset($bcp_authentication) && $bcp_authentication != null) {
            $bcp_licence = fetch_data_option('bcp_licence');
            if ($bcp_licence) {
                $bcp_logo = fetch_data_option('bcp_logo');
                $bcp_logo_url = "";
                if ($bcp_logo != null) {
                    $bcp_logo_url = wp_get_attachment_image_src($bcp_logo, 'full');
                    $bcp_logo_url = $bcp_logo_url[0];
                }
                $bcp_name = fetch_data_option( 'bcp_name' );
                
                wp_enqueue_style('bcp-login-block-style');
                wp_enqueue_style('bcp-flag-style');
                wp_enqueue_style('bcp-icons-lnr');
                wp_enqueue_script('bcp-jquery-validate-script');
                wp_enqueue_script('bcp-form-script');
                
                $bcp_portal_visible_reCAPTCHA = (fetch_data_option("bcp_portal_visible_reCAPTCHA") != NULL) ? fetch_data_option("bcp_portal_visible_reCAPTCHA") : array();
                
                $enable_reCaptcha = FALSE;
                if ( isset($atts['capcha_enable']) && $atts['capcha_enable'] == "yes" && fetch_data_option('bcp_recaptcha_site_key') != "" && fetch_data_option('bcp_recaptcha_secret_key') != "" ) {
                    $enable_reCaptcha = TRUE;
                }
                    
                $email_text = (isset($atts['email_text']) && $atts['email_text'] != "") ? $atts['email_text'] : __("Email Address", 'bcp_portal');
                $submit_button_text = (isset($atts['submit_button_text']) && $atts['submit_button_text'] != "") ? $atts['submit_button_text'] : __("Submit", 'bcp_portal');
                $bcp_login_page_link = fetch_data_option('bcp_login_page');

                // Render Data into template related to style
                
                if(get_option('bcp_theme_color')){
                    $this->themecolor->theme_color_forgot_password_style();
                }

                $data = array(
                    'bcp_logo_url'             => $bcp_logo_url,
                    'bcp_name'                 => $bcp_name,
                    'bcp_login_page_link'      => $bcp_login_page_link,
                    'enable_reCaptcha'          => $enable_reCaptcha,
                    'email_text'                => $email_text,
                    'submit_button_text'        => $submit_button_text,
                    'template_helper'           => $this->template,
                    'bcp_helper'                => $this->bcp_helper,
                );
                if (bcp_check_wpml_compatibility()) {
                    $this->template = new TemplateHelper(BCPWPML_PATH . "/partials/");
                    $this->template->render('/public/forgot-password/style1.php', $data);
                } else {
                    $this->template->render('/forgot-password/style1.php', $data);
                }
                    
                if ( $enable_reCaptcha ) {
                ?>
                <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
                    async defer>
                </script>
                <script type="text/javascript">
                  var onloadCallback = function() {
                    widget1 = grecaptcha.render('scp-recaptcha', {
                      'sitekey' : '<?php echo fetch_data_option("bcp_recaptcha_site_key"); ?>',
                    });
                  };
                </script>
                <?php } ?>
                <?php } else { ?>
                <span class='error error-line error-msg settings-error'><?php _e('Please contact to administrator.', 'bcp_portal'); ?></span>
                <?php
            }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Portal is disabled. Please contact your administrator.', 'bcp_portal'); ?></span>
            <?php
        }

        $content .= ob_get_clean();

        return $content;
    }
    
    /**
     * Reset Password UI
     *
     * @param string $content
     * @return void
     * @author Akshay Kungiri 
     */
    function bcp_reset_password_callback( $atts , $content = '') {
        
        
        global $objCp, $bcp_field_prefix;
        
        if ( is_admin() && ( isset($_REQUEST['action']) && ($_REQUEST['action']) == 'edit' ) || isset($_REQUEST['per_page'])) {
            return;
        }
        
        $bcp_token = isset($_REQUEST['bcp_contact_token']) ? $_REQUEST['bcp_contact_token'] : "";
        $bcp_session_contact_id = (isset($_SESSION['bcp_set_password_contact_id']) && $bcp_token == "") ? $this->bcp_helper->bcp_sha_decrypt($_SESSION['bcp_set_password_contact_id']) : "";
        $bcp_access_token = $bcp_field_prefix . 'Access_Token__c';
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        ob_start();
        if ( isset($bcp_authentication) && $bcp_authentication != NULL ) {

            if ( $bcp_token != '' ) {
                $where = array(
                    $bcp_access_token => $bcp_token
                );
                $fields = array(
                    $bcp_access_token => 'Access_Token__c',
                );
                $records = $objCp->getRecords('Contact', $where, json_encode($fields));
            }

            $bcp_licence = fetch_data_option('bcp_licence');
            if ($bcp_licence) {
                if ( $bcp_session_contact_id != "" || ( $bcp_token != '' && isset( $records->records[0] ) && ( isset($records->records[0]->$bcp_access_token) && $records->records[0]->$bcp_access_token == $bcp_token ) && !empty( $records->records[0] ) ) ) {
                    $bcp_logo = fetch_data_option('bcp_logo');
                    $bcp_logo_url = "";
                    if ($bcp_logo != null) {
                        $bcp_logo_url = wp_get_attachment_image_src($bcp_logo, 'full');
                        $bcp_logo_url = $bcp_logo_url[0];
                    }
                    $bcp_name = fetch_data_option('bcp_name');
                    
                    wp_enqueue_style('bcp-login-block-style');
                    wp_enqueue_style('bcp-icons-lnr');
                    wp_enqueue_script('bcp-jquery-validate-script');
                    wp_enqueue_script('bcp-form-script');
                    
                    $new_password_text = (isset($atts['new_password_text']) && $atts['new_password_text'] != "") ? $atts['new_password_text'] : __("Enter new passsword", 'bcp_portal');
                        $confirm_password_text = (isset($atts['confirm_password_text']) && $atts['confirm_password_text'] != "") ? $atts['confirm_password_text'] : __("Enter confirm password", 'bcp_portal');
                        $submit_button_text = (isset($atts['submit_button_text']) && $atts['submit_button_text'] != "") ? $atts['submit_button_text'] : __("Submit", 'bcp_portal');
                    
                    if(get_option('bcp_theme_color')){
                        $this->themecolor->theme_color_reset_password_style();
                    }
                        
                    $data = array(
                        'bcp_logo_url'             => $bcp_logo_url,
                        'bcp_name'                 => $bcp_name,
                        'bcp_token'                => $bcp_token,
                        'new_password_text'         => $new_password_text,
                        'confirm_password_text'     => $confirm_password_text,
                        'submit_button_text'        => $submit_button_text,
                        'template_helper'           => $this->template,
                    );

                    if (bcp_check_wpml_compatibility()) {
                        $this->template = new TemplateHelper(BCPWPML_PATH . "/partials/");
                        $this->template->render('/public/reset-password/style1.php', $data);
                    } else {
                        $this->template->render('/reset-password/style1.php' ,$data);
                    }
                }
            } else {
                ?>
                <span class='error error-line error-msg settings-error'><?php _e('Please contact to administrator.', 'bcp_portal'); ?></span>
                <?php
            }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Portal is disabled. Please contact your administrator.', 'bcp_portal'); ?></span>
            <?php
        }

        $content .= ob_get_clean();

        echo $content;
    }
    
    /**
     * Profile UI
     *
     * @param string $content
     * @return void
     * @author Akshay Kungiri 
     */
    
    public function bcp_profile_callback( $atts , $content = '') {
        global $objCp;

        if(isset($_REQUEST['action']) && ($_REQUEST['action'])=='edit'){
            return;
        }
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        ob_start();
        if (isset($bcp_authentication) && $bcp_authentication != null) {

            $bcp_licence = fetch_data_option('bcp_licence');
            if ($bcp_licence) {
                if ( isset( $_SESSION['bcp_contact_id'] ) && $_SESSION['bcp_contact_id'] != null ) {
                $view_style = $atts['view_style'];
                $edit_profile_title = $atts['edit_profile_title'];
                $change_password_text = $atts['change_password_text'];
                $edit_profile_save_label = $atts['edit_profile_text'];
                $change_password_save_label = $atts['change_password_save_label'];

                wp_enqueue_style('bcp-icons-lnr');
                wp_enqueue_style('bcp-flag-style');
                wp_enqueue_style('bcp-form-block-style');
                wp_enqueue_style('bcp-profile-block-style');
                wp_enqueue_style('bcp-form-datetimepicker-style');
                
                wp_enqueue_script('bcp-form-script');
                wp_enqueue_script('bcp-form-datetimepicker-script');
               
                if(get_option('bcp_theme_color')){
                    $this->themecolor->theme_color_profile_style();
                }
                ?>
                <div id="responsedataPortal"></div>
                <script>
                    jQuery(document).ready(function(){
                        var module_name = "contact";
                        var view = "edit";
                        var view_style = "<?php echo $view_style; ?>";
                        var edit_profile_title = "<?php echo $edit_profile_title; ?>";
                        var change_password_text = "<?php echo $change_password_text; ?>";
                        var edit_profile_save_label = "<?php echo $edit_profile_save_label; ?>";
                        var change_password_save_label = "<?php echo $change_password_save_label; ?>";
                        bcp_render_profile_edit_form(module_name, view, view_style, edit_profile_title, change_password_text, edit_profile_save_label, change_password_save_label);
                    });
                </script>
                
                <?php } else { ?>
                    <span class='error error-line error-msg settings-error'><?php _e( 'Please login to portal and access this page.', 'bcp_portal' ); ?></span>
                    <?php
                }
            } else {
                ?>
                <span class='error error-line error-msg settings-error'><?php _e('Please contact to administrator.', 'bcp_portal'); ?></span>
                <?php
            }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Portal is disabled. Please contact your administrator.', 'bcp_portal'); ?></span>
            <?php
        }

        $content .= ob_get_clean();
        return $content;
    }
    
    /**
     * Function to display content in manage page
     * 
     * @param type $atts
     * @param type $content
     * @return type
     * @author Akshay Kungiri 
     */
    
    public function bcp_content_callback( $atts , $content = '' ) {

        if(isset($_REQUEST['action']) && ($_REQUEST['action'])=='edit'){
            return;
        }
        
        wp_enqueue_style('bcp-icons-lnr');
        wp_enqueue_script( 'bcp-knowledge-articles-script' );
        
        $module_name = get_query_var("module_name");        
        $module_action = get_query_var("module_action");
          
        if($module_name == 'searchresult') {
            wp_enqueue_style('bcp-search-block-style');
            wp_enqueue_style('bcp-global-search-block-style');
        } else if( $module_name == 'solutions' ){
            wp_enqueue_style('bcp-case-deflection-style_1');
        } else if( $module_name == 'KBDocuments'){
            wp_enqueue_style('bcp-knowledge-articles-style_1');
        }
        wp_enqueue_style('bcp-list-block-style_1');        
        
        $bcp_portal_template = fetch_data_option( 'bcp_portal_template' );
        $bcp_current_lang = "";
        if (bcp_check_wpml_compatibility()) {
            $bcp_current_lang = apply_filters('wpml_current_language', NULL);
        }
        
        if(get_option('bcp_theme_color')){
            $this->themecolor->theme_color_knowledgebase_style();
        }
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        ob_start();
        if ( isset( $bcp_authentication ) && $bcp_authentication != null ) {
            if ( isset( $_SESSION['bcp_contact_id'] ) && $_SESSION['bcp_contact_id'] != null ) {

                if ( ! $bcp_portal_template ) {
                    $theme_name = wp_get_theme();
                    $fullwidth_class_gen = '';
                    if ($theme_name == "Twenty Seventeen") {
                        $fullwidth_class_gen = "fullwidth-seventeen-theme";
                    }
                    if ($theme_name == "Twenty Fifteen") {
                        $fullwidth_class_gen = "fullwidth-fifteen-theme";
                    }
                    if ($theme_name == "Twenty Sixteen") {
                        $fullwidth_class_gen = "fullwidth-sixteen-theme";
                    }
                    if ($theme_name == "Twenty Nineteen") {
                        $fullwidth_class_gen = "fullwidth-nineteen-theme";
                    }
                    ?>
                        <div id="fullwidth-wrapper" class="crm_fullwidth sfcp-manage-page <?php echo $fullwidth_class_gen; ?>">
                    <?php } ?>
                <div id="wrapper">
                    <div id="content-wrapper" class="d-flex flex-column">
                        <div id="content">
                            <div class="bcp-container-fluid">
                                <div id="responsedataPortal" data-count="0" class='scp-standalone-content'></div>
                                <div id="otherdata" style="display:none;"></div>
                                <input type="hidden" id="res_count" value="0" >
                            </div>
                        </div>
                    </div>
                </div>  
                    
                <script type="text/javascript">

                    var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';

                    jQuery( function($) {
                        var module_name = "<?php echo $module_name; ?>";
                        var module_action = "<?php echo $module_action; ?>";
                        var data = {
                            'action': 'bcp_' + module_name,
                            'module_name': module_name,
                            'view': module_action,
                        };
                        if (module_name == 'searchresult'){
                            data.serch_term = module_action;
                        }
                        data.lang = "<?php echo $bcp_current_lang; ?>";

                        $( '#responsedataPortal' ).addClass( 'bcp-loading' );
                        $.post( ajaxurl, data, function( response ) {
                            $( '#responsedataPortal' ).html( response );
                            $( '#responsedataPortal' ).removeClass( 'bcp-loading' );
                        });
                    });

                </script>
                <?php if ( ! $bcp_portal_template ) { ?>
                        </div>
                <?php
                }
            } else {
                ?>
                    <span class='error error-line error-msg settings-error'><?php _e( 'Please login to portal and access this page.', 'bcp_portal' ); ?></span>
                <?php
            }
        } else {
            ?>
                <span class='error error-line error-msg settings-error'><?php _e( 'Portal is disabled. Please contact your administrator.', 'bcp_portal' ); ?></span>
            <?php
        }
        $content .= ob_get_clean();

        return $content;
        
    }
    
    /**
     * List UI
     *
     * @param string $content
     * @return void
     * @author Akshay Kungiri 
     */
    
    public function bcp_list_callback( $atts , $content = '' ) {
        
        global $wpdb, $sf, $bcp_field_prefix, $bcp_secure_flag, $objCp;
        
        if(isset($_REQUEST['action']) && ($_REQUEST['action'])=='edit'){
            return;
        }
        $bcp_licence = fetch_data_option( 'bcp_licence' );
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        ob_start();
       
        if ( isset( $bcp_authentication ) && $bcp_authentication != null && $objCp && $bcp_licence) {  
            if (isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id']!='') {
               
                if ( isset( $_SESSION['bcp_modules'] ) && get_query_var('module_name') != "" && array_key_exists( get_query_var('module_name'), $_SESSION['bcp_modules'] ) || get_query_var('module_name') == "product2") {
                $module_name = get_query_var('module_name');
                $pagination = $atts['pagination'];                
                $view_style = $atts['view_style'];
                $search = get_query_var('search');
                
                wp_enqueue_style('bcp-daterangepicker-style');
                wp_enqueue_style('bcp-form-block-style');
                wp_enqueue_style('bcp-icons-lnr');
                wp_enqueue_style('bcp-list-block-'.$view_style);
                
                wp_enqueue_script('bcp-form-moment-min-script');
                wp_enqueue_script('bcp-daterangepicker-script');
                if($module_name == "product2"){
                    wp_enqueue_style('bcp-modal-css');
                    wp_enqueue_script('bcp-form-script');
                    
                }
                
                if(get_option('bcp_theme_color')){
                    $this->themecolor->theme_color_list_style($view_style);
                }

                if( $_SESSION['bcp_relationship_type'] == "Account-Based"){ 
                    if($_SESSION['bcp_module_access_data']->$module_name->accountRelationship != "" && $_SESSION['bcp_module_access_data']->$module_name->contactRelationship != "" && !in_array($module_name,array("contentnote","attachment","contentdocument"))){ 
                        $recordtype = 'myrecord';
                    }else{
                        $recordtype = '';
                    }         
                }else{
                    $recordtype = '';
                }
                
                ?>
                <div id="responsedataPortal"></div>
                
                <script type="text/javascript">
                    var module_name = '<?php echo $module_name;  ?>';
                    var limit = '<?php echo $pagination;  ?>';
                    var view_style = '<?php echo $view_style;  ?>';
                    var search = '<?php echo $search;  ?>';
                    var where_condition = '';
                    var recordtype = '<?php echo $recordtype;  ?>';
                    bcp_list_module_portal(module_name, limit, view_style, search, where_condition,'','','','','',recordtype);
                </script>
                
                <?php } else { ?>
                    <div class="remove_block">
                        <script type="text/javascript">
                            bcp_remove_block_portal("remove_block");
                        </script>
                    </div>
                    <?php    
                }
            } else {
                ?>
                    <span class='error error-line error-msg settings-error'><?php _e( 'Please login first.', 'bcp_portal' ); ?></span>
                <?php
            }
        } else {
            ?>
                <span class='error error-line error-msg settings-error'><?php _e( 'Portal is disabled. Please contact your administrator.', 'bcp_portal' ); ?></span>
            <?php
        }
        $content .= ob_get_clean();

        return $content;
    }
    
    /**
     * Detail UI
     *
     * @param string $content
     * @return void
     * @author Akshay Kungiri 
     */
    
    public function bcp_detail_callback( $atts , $content = '' ) {
        
        global $wpdb, $sf, $bcp_field_prefix, $bcp_secure_flag, $objCp;
        
        if(isset($_REQUEST['action']) && ($_REQUEST['action'])=='edit'){
            return;
        }
        $bcp_licence = fetch_data_option( 'bcp_licence' );
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        ob_start();
        if ( isset( $bcp_authentication ) && $bcp_authentication != null && $objCp && $bcp_licence) {
            if (isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id']!='') {
                if ( isset( $_SESSION['bcp_modules'] ) && get_query_var('module_name') != "" && array_key_exists( get_query_var('module_name'), $_SESSION['bcp_modules'] ) && get_query_var('record') != '' ) {
                    $module_name = get_query_var('module_name');
                    $record_id = get_query_var('record');
                    $submodule = '';
                    
                    if(get_query_var('sub_module')!='') {
                        $submodule = get_query_var('sub_module');   
                    }
                    
                    $view_style = $atts['view_style'];
                    
                    wp_dequeue_style('sfcp-style');
                    wp_enqueue_style('bcp-icons-lnr');
                    wp_enqueue_style('bcp-details-block-style');
                    wp_enqueue_style('bcp-search-block-style');
                    wp_enqueue_style( 'bcp-knowledge-articles-style' );
                    wp_enqueue_script( 'bcp-knowledge-articles-script' );
                    wp_enqueue_style('bcp-list-block-'.$view_style);
                    
                    wp_enqueue_script('bcp-jquery-validate-script');
                    wp_enqueue_script('bcp-form-script');
                    
                    if(get_option('bcp_theme_color')){
                        $this->themecolor->theme_color_detail_view_style($view_style);
                    }
                    $subtab = isset($_REQUEST['subtab']) ? $_REQUEST['subtab'] : "";
                    ?>
                    <div id="responsedataPortal"></div>
                    <script type="text/javascript">
                        var module_name = '<?php echo $module_name;  ?>';
                        var view_style = '<?php echo $view_style;  ?>';
                        var record_id = '<?php echo $record_id;  ?>';
                        var submodule = '<?php echo $submodule;  ?>';
                        var subtab = '<?php echo $subtab;  ?>';
                        bcp_detail_module_portal(module_name, 'detail', record_id, view_style, submodule, subtab);
                    </script>
                <?php } else { ?>
                        <span class='error error-line error-msg settings-error'><?php _e( 'Page not found.' , 'bcp_portal'); ?></span>
                    <?php    
                }
            } else {
                    ?>
                        <span class='error error-line error-msg settings-error'><?php _e( 'Please login first.', 'bcp_portal' ); ?></span>
                <?php
                }
        } else {
            ?>
                <span class='error error-line error-msg settings-error'><?php _e( 'Portal is disabled. Please contact your administrator.', 'bcp_portal' ); ?></span>
            <?php
        }
        $content .= ob_get_clean();

        return $content;
    }
    
    /**
     * Edit UI
     *
     * @param string $content
     * @return void
     * @author Akshay Kungiri 
     */
    
    public function bcp_edit_callback( $atts , $content = '' ) {
        
        global $wpdb, $sf, $bcp_field_prefix, $bcp_secure_flag, $objCp;
           
        if(isset($_REQUEST['action']) && ($_REQUEST['action'])=='edit'){
            return;
        }
        $bcp_licence = fetch_data_option( 'bcp_licence' );
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        ob_start();
        if ( isset( $bcp_authentication ) && $bcp_authentication != null && $objCp && $bcp_licence) {
            if (isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id']!='') {
                
                if ( isset( $_SESSION['bcp_modules'] ) && get_query_var('module_name') != "" && array_key_exists( get_query_var('module_name'), $_SESSION['bcp_modules'] ) ) {
                $module_name = get_query_var('module_name');
                
                if(get_query_var('record')) {
                    $record_id = get_query_var('record');
                } else {
                    $record_id = 0;
                }
                
                $relate_name = '';
                if(get_query_var('relate_module_name')) {
                    $relate_name = get_query_var('relate_module_name');    
                }
                
                $relationship = '';
                if(get_query_var('relationship')) {
                    $relationship = get_query_var('relationship');    
                }
                
                $relate_module_id = '';
                if(get_query_var('relate_module_id')) {
                    $relate_module_id = get_query_var('relate_module_id');    
                }
                
                $view_style = $atts['view_style'];
                wp_dequeue_style('sfcp-style');
                wp_enqueue_style('bcp-icons-lnr');
                wp_enqueue_style('bcp-form-block-style');
                wp_enqueue_style('bcp-form-datetimepicker-style');
                
                wp_enqueue_script('bcp-jquery-validate-script');
                wp_enqueue_script('bcp-form-script');
                wp_enqueue_script('bcp-form-datetimepicker-script');
                
                if(get_option('bcp_theme_color')){
                    $this->themecolor->theme_color_edit_style();
                }
                
                ?>
                <div id="responsedataPortal"></div>
                <script type="text/javascript">
                    var module_name = '<?php echo $module_name;  ?>';
                    var record_id = '<?php echo $record_id;  ?>';
                    var view_style = '<?php echo $view_style;  ?>';
                    var relate_name = '<?php echo $relate_name;  ?>';
                    var relationship = '<?php echo $relationship;  ?>';
                    var relate_module_id = '<?php echo $relate_module_id;  ?>';
                    
                     bcp_edit_module_portal(module_name, 'edit', record_id, view_style, relate_name, relationship, relate_module_id);
                </script>
                <?php } else { ?>
                    <div class="remove_block">
                        <script type="text/javascript">
                            bcp_remove_block_portal("remove_block");
                        </script>
                    </div>
                    <?php    
                }
            } else {
                    ?>
                        <span class='error error-line error-msg settings-error'><?php _e( 'Please login first.', 'bcp_portal' ); ?></span>
                <?php
                }
        } else {
            ?>
                <span class='error error-line error-msg settings-error'><?php _e( 'Portal is disabled. Please contact your administrator.', 'bcp_portal' ); ?></span>
            <?php
        }
        $content .= ob_get_clean();

        return $content;
    }
    
    /**
     * Counter UI
     *
     * @param string $content
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_counter_callback( $atts , $content = '') {
        
        global $counterData, $objCp;
        $selected_module = $atts['selected_module'];
      
        if (isset($_REQUEST['action']) && ($_REQUEST['action']) == 'edit') {
            return;
        }
        $bcp_licence = fetch_data_option('bcp_licence');
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        ob_start();
       /** For check the relationship is empty for selected module */
      
     
        if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
            
            $relationship_field_key = $_SESSION['bcp_module_access_data']->$selected_module->accountRelationship;   
            if($selected_module == "account"){
                $relationship_field_key = $_SESSION['bcp_module_access_data']->$selected_module->contactRelationship;
            }
        }else{
            $relationship_field_key = $_SESSION['bcp_module_access_data']->$selected_module->contactRelationship;
        }
      
        if ( isset( $bcp_authentication ) && $bcp_authentication != null && $objCp && $bcp_licence) {
            
            if ( isset( $_SESSION['bcp_contact_id'] ) && $_SESSION['bcp_contact_id'] != null ) {
                if ( isset( $_SESSION['bcp_modules'] ) && $atts['selected_module'] != "" && array_key_exists( $atts['selected_module'], $_SESSION['bcp_modules'] ) && $relationship_field_key != "") {
                    $view_style = $atts['view_style'];
                    $module_title = __($atts['module_title'], 'bcp_portal');
                    $selected_module_name = $atts['selected_module'];
                    $field_name = $atts['selected_field'];
                    $field_value = $atts['selected_field_value'];
                    $icon = $atts['selected_icon'];
                    $block_color = $atts['block_color'];
                    $block_hover_color = $atts['block_hover_color'];
                    $font_hover_color = $atts['font_hover_color'];
                    $font_color = $atts['font_color'];
                    $counter_block_id = $atts['counter_block_id'];
                    $view_all_url = home_url('customer-portal/' . $selected_module_name . '/list/');

                    wp_dequeue_style('sfcp-style');
                    wp_enqueue_style('bcp-icons-lnr');
                    wp_enqueue_style('bcp-counter-block-'.$view_style);

                    if($counterData!=''){

                        $count = 0;
                        $counterDataArray = json_decode(json_encode($counterData),1);
                        if((in_array($selected_module_name, array_column($counterDataArray, 'module_name'))) &&  (in_array($field_name, array_column($counterDataArray, 'field'))) && (in_array($field_value, array_column($counterDataArray, 'field_value'))) ) {
                            foreach ($counterData as $value) {                                
                                if(($selected_module_name == $value->module_name) && ($field_name == $value->field) && ($field_value == $value->field_value)) {
                                    $count = $value->total;
                                    break;
                                }
                            }
                        }
                        
                        $this->themecolor->theme_color_counter_style($counter_block_id,$block_color, $block_hover_color, $font_color, $font_hover_color, $view_style);
                        $segregate_list_page = $this->bcp_helper->get_segregate_url($selected_module_name, 'bcp_list');
                        if ($segregate_list_page['page_exists'] && $segregate_list_page['page_url']) {
                            $view_all_url = $segregate_list_page['page_url'];
                        }

                        $counter_template = '/counter/'.$view_style.'.php';
                        $data = array(
                            'module_title'      => $module_title,
                            'icon'              => $icon,
                            'field_name'        => $field_name,
                            'field_value'       => $field_value,
                            'counter'           => $count,
                            'module_name'       => $selected_module_name,
                            'view_style'        => $view_style,
                            'counter_block_id'  => $counter_block_id,
                            'view_all_url'      => $view_all_url
                        );
                        $this->template->render($counter_template ,$data);
                    }
                } else {
                    wp_enqueue_script('bcp-form-script');
                    ?>
                    <div class="remove_block">
                        <script type="text/javascript">
                            bcp_remove_block_portal("remove_block");
                        </script>
                    </div>
                    <?php    
                }
            } else {
                ?>
                <span class='error error-line error-msg settings-error'><?php _e( 'Please login first.', 'bcp_portal' ); ?></span>
                <?php
            }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Portal is disabled. Please contact your administrator.', 'bcp_portal'); ?></span>
            <?php
        }
        $content .= ob_get_clean();
        return $content;
    }
    
    /**
     * Recent Activity UI
     *
     * @param string $content
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_recent_activity_callback( $atts , $content = '') {
        global $recentActivityData, $objCp;

        if (isset($_REQUEST['action']) && ($_REQUEST['action']) == 'edit') {
            return;
        }
      
        $bcp_licence = fetch_data_option('bcp_licence');
        $bcp_authentication = fetch_data_option("bcp_authentication"); 
        $selected_module = $atts['selected_module']; 
        ob_start();
        if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
            
            $relationship_field_key = $_SESSION['bcp_module_access_data']->$selected_module->accountRelationship;   
            if($selected_module == "account"){
                $relationship_field_key = $_SESSION['bcp_module_access_data']->$selected_module->contactRelationship;
            }
        }else{
            $relationship_field_key = $_SESSION['bcp_module_access_data']->$selected_module->contactRelationship;
        }
     
        if ( isset( $bcp_authentication ) && $bcp_authentication != null && $objCp && $bcp_licence) {
            
            if ( isset( $_SESSION['bcp_contact_id'] ) && $_SESSION['bcp_contact_id'] != null ) {
                if ( isset( $_SESSION['bcp_modules'] ) && $atts['selected_module'] != "" && array_key_exists( $atts['selected_module'], $_SESSION['bcp_modules'] ) && $relationship_field_key != "" ) {
                    
                    $view_style = $atts['view_style'];
                    $segregate_detail_page_exists = false;
                    $segregate_detail_page_url = '';
                    $module_title = $atts['module_title'];
                    $selected_module_name = $atts['selected_module'];
                    if( $selected_module_name == $module_title ) {
                        $module_title = sprintf(__('Recent %s', 'bcp_portal'), __($_SESSION['bcp_modules'][$selected_module_name]['singular'], 'bcp_portal'));
                    }
                    
                    $field1_label = $field1_selected_option = $atts['field1_selected_option'];
                    $field2_label = $field2_selected_option = $atts['field2_selected_option'];
                    $field3_label = $field3_selected_option = $atts['field3_selected_option'];

                    $field_color = (isset($atts['dropdown_field_color']) ? $atts['dropdown_field_color'] : "#000");

                    wp_dequeue_style('sfcp-style');
                    wp_enqueue_style('bcp-recent-activity-block-style');

                    $finalData = '';
                    if($recentActivityData!=''){
                        foreach ($recentActivityData as $key => $value) {
                            $convertArray = get_object_vars($value);
                            $keys = array_keys($convertArray);
                            if($keys[0] == $selected_module_name) {
                                
                                if (( property_exists($value->field, $field1_selected_option) ) && ( property_exists($value->field, $field2_selected_option) ) && ( property_exists($value->field, $field3_selected_option) )) {
                                    $finalData = $recentActivityData[$key]->$selected_module_name;
                                    
                                    $field1_label = $value->field->$field1_selected_option;
                                    $field2_label = $value->field->$field2_selected_option;
                                    $field3_label = $value->field->$field3_selected_option;
                                    break;
                                }
                            }
                        }
                        
                        if(isset($finalData) && is_object($finalData)){
                            $finalData = get_object_vars($finalData)[0];
                        }
                        
                        $theme_color = fetch_data_option('bcp_theme_color');
                        if($theme_color){
                            $this->themecolor->theme_color_recent_activity_style($view_style);
                        }
                        $view_all_url = home_url('customer-portal/' . $selected_module_name . '/list/');
                        $segregate_list_page = $this->bcp_helper->get_segregate_url($selected_module_name, 'bcp_list');
                        if ($segregate_list_page['page_exists'] && $segregate_list_page['page_url']) {
                            $view_all_url = $segregate_list_page['page_url'];
                        }
                        
                        $segregate_detail_page = $this->bcp_helper->get_segregate_url($selected_module_name, 'bcp_detail');
                        $segregate_detail_page_exists = $segregate_detail_page['page_exists'];
                        if ($segregate_detail_page_exists && ( isset($segregate_detail_page['page_url']) && $segregate_detail_page['page_url'] != '' )) {
                            $segregate_detail_page_url = $segregate_detail_page['page_url'];
                        }

                        $recent_activity_template = 'recent_activity/'.$view_style.'.php';
                        
                        $data = array(
                            'module_title' => __($module_title, 'bcp_portal'),
                            'field_values' => $finalData,
                            'module_name' => $selected_module_name,
                            'field1' => $field1_selected_option,
                            'field1_label' => __($field1_label, 'bcp_portal'),
                            'field2' => $field2_selected_option,
                            'field2_label' => __($field2_label, 'bcp_portal'),
                            'field3' => $field3_selected_option,
                            'field3_label' => __($field3_label, 'bcp_portal'),
                            'field_color' => $field_color,
                            'theme_color' => $theme_color,
                            'action_class' => $this->portal_actions,
                            'segregate_detail_page_exists' => $segregate_detail_page_exists,
                            'segregate_detail_page_url' => $segregate_detail_page_url,
                            'view_all_url' => $view_all_url
                        );
                        $this->template->render($recent_activity_template ,$data);
                    }
                } else {
                    wp_enqueue_script('bcp-form-script');
                    ?>
                    <div class="remove_block">
                        <script type="text/javascript">
                            bcp_remove_block_portal("remove_block");
                        </script>
                    </div>
                    <?php    
                }
            } else {
                ?>
                <span class='error error-line error-msg settings-error'><?php _e('Please login first.', 'bcp_portal'); ?></span>
                <?php
            }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Portal is disabled. Please contact your administrator.', 'bcp_portal'); ?></span>
            <?php
        }

        $content .= ob_get_clean();

        return $content;
    }
    
    /**
     * Search UI
     *
     * @param string $content
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_search_callback( $atts , $content = '' ) {
        global $wpdb, $sf, $bcp_field_prefix, $bcp_secure_flag, $objCp;        
        
        if(isset($_REQUEST['action']) && ($_REQUEST['action'])=='edit'){
            return;
        }

        $bcp_licence = fetch_data_option( 'bcp_licence' );
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        ob_start();
        if ( isset( $bcp_authentication ) && $bcp_authentication != null && $objCp && $bcp_licence) {
            if ( isset( $_SESSION['bcp_contact_id'] ) && $_SESSION['bcp_contact_id'] != null ) {

                wp_enqueue_style('bcp-global-search-block-style');
                wp_enqueue_script('bcp-form-script');

                $g_search_template = '/global-search/style1.php';
                $bcp_modules = (array)$_SESSION['bcp_modules'];
                $keys = array_keys((array)$_SESSION['bcp_module_access_data']);
                
                $module = array();
                foreach ($bcp_modules as $key => $value) {
                    if($key == 'KBDocuments'){
                       $module[$key] = $value; 
                    }
                    if(in_array($key, $keys)){
                       $module[$key] = $value;
                    }
                }
                
                if(get_option('bcp_theme_color')){
                    $this->themecolor->theme_color_global_search_style();
                }

                $data = array(
                    'module_name'    => get_query_var("module_name"),
                    'bcp_modules'     => $module,
                    'show_result'    => false,
                    'search_term'    => urldecode(get_query_var("search")),
                    'action_class'   => $this->portal_actions
                );
                $this->template->render($g_search_template , $data);                            
                
            } else {
                ?>
                    <span class='error error-line error-msg settings-error'><?php _e( 'Please login to portal and access this page.', 'bcp_portal' ); ?></span>
                <?php
            }
        } else {
            ?>
                <span class='error error-line error-msg settings-error'><?php _e( 'Portal is disabled. Please contact your administrator.' , 'bcp_portal'); ?></span>
            <?php
        }
        $content .= ob_get_clean();

        return $content;
    }
        
    /**
     * Chart UI
     *
     * @param string $content
     * @return void
     * @author Akshay Kungiri 
     */
    function bcp_chart_callback( $atts , $content = '') {
        global $chartData, $objCp;

        if (isset($_REQUEST['action']) && ($_REQUEST['action']) == 'edit') {
            return;
        }
        $bcp_licence = fetch_data_option('bcp_licence');
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        ob_start();
        $selected_module = $atts['selected_module'];
        if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
            
            $relationship_field_key = $_SESSION['bcp_module_access_data']->$selected_module->accountRelationship;   
            if($selected_module == "account"){
                $relationship_field_key = $_SESSION['bcp_module_access_data']->$selected_module->contactRelationship;
            }
        }else{
            $relationship_field_key = $_SESSION['bcp_module_access_data']->$selected_module->contactRelationship;
        }
     
        if ( isset( $bcp_authentication ) && $bcp_authentication != null && $objCp && $bcp_licence) {
            
            if ( isset( $_SESSION['bcp_contact_id'] ) && $_SESSION['bcp_contact_id'] != null ) {
                if ( isset( $_SESSION['bcp_modules'] ) && $atts['selected_module'] != "" && array_key_exists( $atts['selected_module'], $_SESSION['bcp_modules'] ) && $relationship_field_key != '') {

                    $view_style = $atts['view_style'];
                    $module_title = __($atts['module_title'] , 'bcp_portal') ;
                    $selected_module_name = $atts['selected_module'];
                    $field_value = $atts['selected_field'];
                    $chart_block_id = $atts['chart_block_id'];
                    $legend_enable = $atts['legend_enable'];

                    $finalData = '';
                    $count = false;

                    if(!empty($chartData)){

                        foreach ($chartData as $key => $value) {

                            $convertArrs = (array) $value;
                            $keys = array_keys($convertArrs);
                            if($count){
                                break;
                            }
                            if($keys[0] == $selected_module_name) {
                                foreach ($convertArrs as $convertArr){
                                    if($convertArr) {
                                        $new_arr = (array) $convertArr[0];
                                        if (array_key_exists($field_value,$new_arr)){
                                            $finalData = $chartData[$key]->$selected_module_name;
                                            $count = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        }

                        if(!empty($finalData)){
                            $chart_template = '/chart/'.$view_style.'.php';
                            $data = array(
                                'module_title'     => $module_title,
                                'field_values'     => $finalData,
                                'module_name'      => $selected_module_name,
                                'field'            => $field_value,
                                'chart_block_id'   => $chart_block_id,
                                'legend_enable'    => $legend_enable,
                            );
                            $this->template->render($chart_template, $data);

                            wp_enqueue_script('bcp-chart-block-js');
                            if($view_style == 'style_1'){
                                wp_enqueue_script('bcp-chart-block-'.$view_style);
                                wp_enqueue_script('bcp-chart-block-accessibility-js');
                            }
                        } else {
                            $chart_template = '/chart/nodata.php';
                            $data = array(
                                'module_title'     => $module_title,
                                'chart_block_id'   => $chart_block_id,
                            );
                            $this->template->render($chart_template, $data);
                        }
                    }
                } else {
                    wp_enqueue_script('bcp-form-script');
                    ?>
                    <div class="remove_block">
                        <script type="text/javascript">
                            bcp_remove_block_portal("remove_block");
                        </script>
                    </div>
                    <?php    
                }

            } else {
                ?>
                <span class='error error-line error-msg settings-error'><?php _e('Please login first.', 'bcp_portal'); ?></span>
                <?php
            }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Portal is disabled. Please contact your administrator.', 'bcp_portal'); ?></span>
            <?php
        }

        $content .= ob_get_clean();

        return $content;
    }
    
    /**
     * Case deflection UI
     *
     * @param string $content
     * @return void
     * @author Akshay Kungiri 
     */
    
    public function bcp_casedeflection_callback($atts, $content = '') {
        if (isset($_REQUEST['action']) && ($_REQUEST['action']) == 'edit') {
            return;
        }

        global $wpdb, $sf, $bcp_field_prefix, $bcp_secure_flag, $objCp;
        $bcp_licence = fetch_data_option('bcp_licence');
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        ob_start();
        if (isset($bcp_authentication) && $bcp_authentication != null && $objCp && $bcp_licence) {
            if (isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id'] != '') {
                $module_name = get_query_var('module_name');
                if ($module_name == 'solutions' && isset($_SESSION['bcp_module_access_data']->solution->access) && $_SESSION['bcp_module_access_data']->solution->access == 'true' && isset($_SESSION['bcp_case_deflection']) && $_SESSION['bcp_case_deflection'] == 1) {
                    $bcp_portal_template = fetch_data_option('bcp_portal_template');
                    $view_style = $atts['view_style'] ? $atts['view_style'] : 'style_1';
                    $module_description = $atts['module_description'] ? $atts['module_description'] : '';
                    $add_case_title = $atts['add_case_title'];
                    $limit = $atts['pagination'] ? $atts['pagination'] : '10';
                    wp_enqueue_style('bcp-icons-lnr');
                    wp_enqueue_style('bcp-case-deflection-' . $view_style);
                    wp_enqueue_script('bcp-knowledge-articles-script');
                    $module_name = get_query_var("module_name");
                    $module_action = get_query_var("module_action");
                    if ($module_name == 'searchresult') {
                        wp_enqueue_style('bcp-global-search-block-style');
                    }
                    wp_enqueue_style('bcp-list-block-style_1');
                    
                    if (!$bcp_portal_template) {
                        $theme_name = wp_get_theme();
                        $fullwidth_class_gen = '';
                        if ($theme_name == "Twenty Seventeen") {
                            $fullwidth_class_gen = "fullwidth-seventeen-theme";
                        }
                        if ($theme_name == "Twenty Fifteen") {
                            $fullwidth_class_gen = "fullwidth-fifteen-theme";
                        }
                        if ($theme_name == "Twenty Sixteen") {
                            $fullwidth_class_gen = "fullwidth-sixteen-theme";
                        }
                        if ($theme_name == "Twenty Nineteen") {
                            $fullwidth_class_gen = "fullwidth-nineteen-theme";
                        }
                        ?>
            <div id="fullwidth-wrapper" class="crm_fullwidth sfcp-manage-page <?php echo $fullwidth_class_gen; ?>">
                <?php } ?>
                <div id="wrapper">
                   <div id="content-wrapper" class="d-flex flex-column">
                      <div id="content">
                         <div class="bcp-container-fluid">
                            <div id="responsedataPortal" data-count="0" class='scp-standalone-content'></div>
                            <div id="otherdata" style="display:none;"></div>
                            <input type="hidden" id="res_count" value="0" >
                         </div>
                      </div>
                   </div>
                </div>
                <script type="text/javascript">
                   var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                   
                   jQuery( function($) {
                       var module_name = "<?php echo $module_name; ?>";
                       var module_action = "<?php echo $module_action; ?>";
                       var view_style = "<?php echo $view_style; ?>";
                       var add_case_title = "<?php echo $add_case_title; ?>";
                       var module_description = "<?php echo $module_description; ?>";
                       var limit = "<?php echo $limit; ?>";
                       var data = {
                           'action': 'bcp_' + module_name,
                           'module_name': module_name,
                           'view': module_action,
                           'view_style' : view_style,
                           'limit' : limit
                       };                       
                       if (module_name == 'searchresult'){
                           data.serch_term = module_action;
                       }
                       
                       if(module_action == 'search'){
                           data.add_case_title = add_case_title;
                           data.module_description = module_description;
                       }
                       
                       $( '#responsedataPortal' ).addClass( 'bcp-loading' );
                       $.post( ajaxurl, data, function( response ) {
                           $( '#responsedataPortal' ).html( response );
                           $( '#responsedataPortal' ).removeClass( 'bcp-loading' );
                       });
                   });
                   
                </script>
                    <?php if (!$bcp_portal_template) { ?>
                        </div>
                        <?php
                    }                    
                }else{
                    wp_enqueue_script('bcp-form-script');
                    ?>
                    <div class="remove_block">
                        <script type="text/javascript">
                            bcp_remove_block_portal("remove_block");
                        </script>
                    </div>
                    <?php  
                }
            } else {
                ?>
                <span class='error error-line error-msg settings-error'><?php _e('Please login first.', 'bcp_portal'); ?></span>
                <?php
            }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Portal is disabled. Please contact your administrator.', 'bcp_portal'); ?></span>
            <?php
        }

        $content .= ob_get_clean();

        return $content;
    }
    
    /**
     * Knowledgebase UI
     *
     * @param string $content
     * @return void
     * @author Akshay Kungiri 
     */
    
    public function bcp_knowledgebase_callback( $atts , $content = '' ) {
        
        if (isset($_REQUEST['action']) && ($_REQUEST['action']) == 'edit') {
            return;
        }
        
        $view_style = $atts['view_style'] ? $atts['view_style'] : 'style_1';
        $limit = $atts['pagination'] ? $atts['pagination'] : '10';
        $bcp_portal_template = fetch_data_option('bcp_portal_template');
        wp_enqueue_style('bcp-icons-lnr');
        wp_enqueue_style('bcp-knowledge-articles-' . $view_style);
        wp_enqueue_script('bcp-form-script');
        wp_enqueue_script('bcp-knowledge-articles-script');

        $module_name = get_query_var("module_name");
        $module_action = get_query_var("module_action");

        if ($module_name == 'searchresult') {
            wp_enqueue_style('bcp-global-search-block-style');
        }
        wp_enqueue_style('bcp-list-block-style_1');
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        ob_start();
        if (isset($bcp_authentication) && $bcp_authentication != null) {
            if (isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id'] != null) {

                if (!$bcp_portal_template) {
                    $theme_name = wp_get_theme();
                    $fullwidth_class_gen = '';
                    if ($theme_name == "Twenty Seventeen") {
                        $fullwidth_class_gen = "fullwidth-seventeen-theme";
                    }
                    if ($theme_name == "Twenty Fifteen") {
                        $fullwidth_class_gen = "fullwidth-fifteen-theme";
                    }
                    if ($theme_name == "Twenty Sixteen") {
                        $fullwidth_class_gen = "fullwidth-sixteen-theme";
                    }
                    if ($theme_name == "Twenty Nineteen") {
                        $fullwidth_class_gen = "fullwidth-nineteen-theme";
                    }
                    ?>
            <div id="fullwidth-wrapper" class="crm_fullwidth sfcp-manage-page <?php echo $fullwidth_class_gen; ?>">
                <?php } ?>
                <div id="wrapper">
                   <div id="content-wrapper" class="d-flex flex-column">
                      <div id="content">
                         <div class="bcp-container-fluid">
                            <div id="responsedataPortal" data-count="0" class='scp-standalone-content'></div>
                            <div id="otherdata" style="display:none;"></div>
                            <input type="hidden" id="res_count" value="0" >
                         </div>
                      </div>
                   </div>
                </div>
                <script type="text/javascript">
                   var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                   
                   jQuery( function($) {
                       var module_name = "<?php echo $module_name; ?>";                       
                       var module_action = "<?php echo $module_action; ?>";
                       var view_style = "<?php echo $view_style; ?>";
                       var limit = "<?php echo $limit; ?>";
                       var data = {
                           'action': 'bcp_' + module_name,
                           'module_name': module_name,
                           'view': module_action,
                           'view_style' : view_style,
                           'limit' : limit,
                       };
                       if (module_name == 'searchresult'){
                           data.serch_term = module_action;
                       }                   
                   
                       $( '#responsedataPortal' ).addClass( 'bcp-loading' );
                       $.post( ajaxurl, data, function( response ) {
                           $( '#responsedataPortal' ).html( response );
                           $( '#responsedataPortal' ).removeClass( 'bcp-loading' );
                       });
                   });
                   
                </script>
                    <?php if (!$bcp_portal_template) { ?>
                        </div>
                    <?php
                        }
                    } else {
                        ?>
            <span class='error error-line error-msg settings-error'><?php _e('Please login to portal and access this page.', 'bcp_portal'); ?></span>
                <?php
            }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Portal is disabled. Please contact your administrator.', 'bcp_portal'); ?></span>
            <?php
        }
        $content .= ob_get_clean();

        return $content;
    }

}

