<?php

class BCPCustomerPortalHelper {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Check if required function are available or not
     *
     * @return string - Error message if not enable required function and success when everything is fine.
     * @since 1.0.0
     */
    public function bcp_check_environment() {

        $conn_err = 1;
        if (!( version_compare(phpversion(), '5.0.0', '>=') )) {
            $conn_err = __('Plugin requires minimum PHP 5.0.0 to function properly. Please upgrade PHP or deactivate Plugin', 'bcp_portal');
            return $conn_err;
        }
        if (!function_exists('curl_version')) {
            $conn_err = __('Please enable PHP CURL extension to make this plugin work.', 'bcp_portal');
            return $conn_err;
        }
        if (!function_exists('json_decode')) {
            $conn_err = __('Please enable PHP JSON extension to make this plugin work.', 'bcp_portal');
            return $conn_err;
        }
        if ($conn_err == 1) {
            return 'yes';
        }
    }

    /**
     * Check user is authenticated everytime when site run
     * @since 1.0.0
     * @return void
     */
    public function bcp_init() {

        global $objCp, $bcp_modules, $bcp_secure_flag;
        if (!session_id()) {
            session_start();
        }

        if ($this->bcp_check_environment() == 'yes') {

            $bcp_date = fetch_data_option("bcp_datetime");
            $bcp_authentication = fetch_data_option("bcp_authentication");  
        
            if (!is_ssl() ) {
                $bcp_flag = 0;
                $_SESSION['bcp_authentication'] = $bcp_authentication; // to check the ssl in front login page                
            } else { // to check ssl in front also
                $bcp_authentication = NULL;
                $_SESSION['bcp_authentication'] = NULL;
            }
            if (fetch_data_option("bcp_connection_portal") == "0") {
                $bcp_authentication = NULL;
                if (isset($_SESSION['bcp_contact_id'])) {
                    unset($_SESSION['bcp_contact_id']);
                    $bcp_login_page_id = fetch_data_option('bcp_login_page');
                    $bcp_login_page = get_permalink($bcp_login_page_id);
                    wp_redirect($bcp_login_page);
                }
            }
            if($_SESSION['bcp_authentication'] == NULL){
                if (isset($_SESSION['bcp_contact_id'])) {
                    unset($_SESSION['bcp_contact_id']);
                    $bcp_login_page_id = fetch_data_option('bcp_login_page');
                    $bcp_login_page = get_permalink($bcp_login_page_id);
                    wp_redirect($bcp_login_page);
                }
            }

            if (isset($bcp_authentication)) {
                $objCp = unserialize($this->bcp_sha_decrypt($bcp_authentication));
               
              /*  $expreobjCp = $objCp;
                $expreobjCp->access_token = "00D6F000001gUsS!AQwAQIIOZkrqD9Q9O0TDXQVebtmorViY4BrDXVqTT8ClGWzEdQL58J6c.DaTubJ5mFkgY.wonPCOre8rApeecBqjM7987979988";
                print_r($this->bcp_sha_encrypt(serialize($expreobjCp)));*/
            }
        }
    }

    /**
     * Verify user is valid by token key
     * @since 1.0.0
     * @return void
     */
    public function bcp_verify_user() {
        global $objCp, $bcp_field_prefix;

        $bcp_login_page_id = fetch_data_option('bcp_login_page');
        $bcp_login_page = get_permalink($bcp_login_page_id);
        // If this is login request from signup mail than redict to login page
        if (isset($_REQUEST['bcp-login']) && $_REQUEST['bcp-login'] == '1') {
            unset($_SESSION['bcp_otp_contact_id']);
            wp_redirect($bcp_login_page);
            exit();
        }
        // If this is user verification request from password reset mail than verify and redict to password reset page
        if (isset($_REQUEST['bcp_token'])) {
            $bcp_token = $_REQUEST['bcp_token'];

            if (isset($_REQUEST['type']) && $_REQUEST['type'] == "verify_email") {
                $where = array(
                    $bcp_field_prefix . 'email_verify_access_token__c' => $bcp_token
                );
            } else {
                $where = array(
                    $bcp_field_prefix . 'Access_Token__c' => $bcp_token
                );
            }
            $records = $objCp->getRecords('Contact', $where);
            if (isset($records->records[0]) && !empty($records->records[0])) {
                if (isset($_REQUEST['type']) && $_REQUEST['type'] == "verify_email") {
                    $contact_id = $records->records[0]->Id;
                    $request_filters = array(
                        $bcp_field_prefix . 'email_verify_access_token__c' => "",
                        $bcp_field_prefix . 'IsVerify__c' => TRUE,
                    );
                    $arguments = array(
                        "module" => "Contact",
                        "data" => $request_filters,
                        "record_id" => $contact_id,
                    );
                    $result = $objCp->updateRecordCall($arguments);
                    $_SESSION['bcp_success_message'] = __("Email verified successfully.", 'bcp_portal');
                    $bcp_login_page = add_query_arg(array('success' => '1'), $bcp_login_page);
                    wp_redirect($bcp_login_page);
                } else {
                    $sfcp_reset_pwd_page = fetch_data_option('bcp_reset_password_page');
                    $sfcp_reset_pwd = add_query_arg(array('bcp_contact_token' => $bcp_token), get_permalink($sfcp_reset_pwd_page));
                    wp_redirect($sfcp_reset_pwd);
                }
            } else {
                $_SESSION['bcp_error_message'] = __("Your link is expired or invalid.", 'bcp_portal');
                $bcp_login_page = add_query_arg(array('error' => '1'), $bcp_login_page);
                wp_redirect($bcp_login_page);
            }
            exit();
        }
    }

    /**
     * Add portal class to body tag
     *
     * @param [type] $classes
     * @return void
     */
    public function bcp_add_body_class($classes) {
        global $post;
        $body_class_array = array();
        if (isset($post->ID) && fetch_data_option("bcp_registration_page") == $post->ID) {
            array_push($body_class_array, "sfcp-signup-body");
        }
        return array_merge($classes, $body_class_array);
    }

    /**
     * Function to set headers for page
     */
    public function bcp_set_headers() {

        $myallkeysubpages = array(
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
        );
        global $post;
        foreach ($myallkeysubpages as $pages) {   //check for portal pages
            if (isset($post->ID) && fetch_data_option($pages) == $post->ID) {
                header_remove("x-powered-by");
                header("X-Frame-Options: deny");
                header("X-Content-Type-Options: nosniff");
                header("X-XSS-Protection: 1; mode=block");
                header("Set-Cookie: HttpOnly; Secure");
                header("Cache-Control: no-cache; no-store; must-revalidate");
            }
        }
    }

    public function bcp_add_post_state($post_states, $post) {
        $post_id = $post->ID;

        if ($post_id == fetch_data_option('bcp_login_page')) {
            $post_states[] = 'Portal Login Page';
        }
        if ($post_id == fetch_data_option('bcp_registration_page')) {
            $post_states[] = 'Portal Registration Page';
        }
        if ($post_id == fetch_data_option('bcp_profile_page')) {
            $post_states[] = 'Portal Profile Page';
        }
        if ($post_id == fetch_data_option('bcp_forgot_password_page')) {
            $post_states[] = 'Portal Forgot Password Page';
        }
        if ($post_id == fetch_data_option('bcp_reset_password_page')) {
            $post_states[] = 'Portal Reset Password Page';
        }
        if ($post_id == fetch_data_option('bcp_manage_page')) {
            $post_states[] = 'Portal Manage Page';
        }
        if ($post_id == fetch_data_option('bcp_list_page')) {
            $post_states[] = 'Portal List Page';
        }
        if ($post_id == fetch_data_option('bcp_detail_page')) {
            $post_states[] = 'Portal Detail Page';
        }
        if ($post_id == fetch_data_option('bcp_dashboard_page')) {
            $post_states[] = 'Portal Dashboard Page';
        }

        if ($post_id == fetch_data_option('bcp_add_edit_page')) {
            $post_states[] = 'Portal Edit Page';
        }

        if ($post_id == fetch_data_option('bcp_case_deflection')) {
            $post_states[] = 'Portal Case Deflection Page';
        }

        if ($post_id == fetch_data_option('bcp_kb')) {
            $post_states[] = 'Portal Knowledge Base Page';
        }

        return $post_states;
    }

    public function bcp_body_class($classes) {
        $template = fetch_data_option('bcp_portal_template');
        if ($template == 0 && $template != null) {
            $theme_name = wp_get_theme();
            if ($theme_name == "Twenty Fourteen") {
                $classes[] = "sfcp-fourteen-theme";
            }
            if ($theme_name == "Twenty Fifteen") {
                $classes[] = "sfcp-fifteen-theme";
            }
            if ($theme_name == "Twenty Sixteen") {
                $classes[] = "sfcp-sixteen-theme";
            }
            if ($theme_name == "Twenty Nineteen") {
                $classes[] = "sfcp-nineteen-theme";
            }
            if ($theme_name == "Twenty Seventeen") {
                $classes[] = "sfcp-seventeen-theme";
            }
            if ($theme_name == "Twenty Twenty") {
                $classes[] = "sfcp-twenty-theme";
            }
        }
        return $classes;
    }

    public function bcp_change_menu_biztech($items, $args) {
        global $objCp;
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        if (isset($bcp_authentication) && $bcp_authentication != null && $objCp->access_token != '') {
            if (in_array(get_the_ID(), $this->bcp_pages_list())) {

                $crm_menuLocation = '';
                if (fetch_data_option('bcp_portal_menuLocation') != NULL) {
                    $crm_menuLocation = fetch_data_option('bcp_portal_menuLocation');
                }

                $crm_menuSelection = '';
                if (fetch_data_option('bcp_portal_menuSelection') != NULL) {
                    $crm_menuSelection = fetch_data_option('bcp_portal_menuSelection');
                }

                $template = fetch_data_option('bcp_portal_template');

                if (isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id'] != '') {
                    if (($template === '1' && $args->menu_class == 'crm-standalone-menu') || ($args->theme_location == $crm_menuLocation && $crm_menuSelection != '' && $crm_menuLocation != '')) {
                        $items = NULL;

                        $items = wp_get_nav_menu_items($crm_menuSelection, array('update_post_term_cache' => false));

                        $bcp_all_module_access_list = $_SESSION['bcp_all_module_access_list'];
                        $bcp_all_disabled_modules = $_SESSION['bcp_disabled_object'];
                        if (isset($bcp_all_disabled_modules) && $bcp_all_disabled_modules != '') {
                            $bcp_all_disabled_modules = json_decode($bcp_all_disabled_modules);
                            $bcp_all_disabled_modules = array_keys((array) $bcp_all_disabled_modules);
                        }
                        
                        $keys = array_keys((array) $bcp_all_module_access_list);

                        foreach ($items as $key => $item) {
                            $url = $item->url;
                            $urls = explode('/', $url);
                            if (count($urls) > 4) {
                                $urls = $urls[count($urls) - 3];
                                if (in_array($urls, $keys) && $bcp_all_module_access_list->$urls->access == 'false') {
                                    unset($items[$key]);
                                }

                                if (( isset($bcp_all_disabled_modules) && is_array($bcp_all_disabled_modules) && count($bcp_all_disabled_modules) > 0 ) && (in_array($urls, $bcp_all_disabled_modules))) {
                                    unset($items[$key]);
                                }
                            }
                        }

                        _wp_menu_item_classes_by_context($items);
                        $sorted_menu_items = array();
                        $menu_items_with_children = array();
                        foreach ((array) $items as $menu_item) {
                            $sorted_menu_items[$menu_item->menu_order] = $menu_item;
                            if ($menu_item->menu_item_parent) {
                                $menu_items_with_children[$menu_item->menu_item_parent] = true;
                            }
                        }
                        // Add the menu-item-has-children class where applicable
                        if ($menu_items_with_children) {
                            foreach ($sorted_menu_items as $menu_item) {
                                if (isset($menu_items_with_children[$menu_item->ID])) {
                                    $menu_item->classes[] = 'menu-item-has-children';
                                }
                            }
                        }
                    }
                }
            }
            return $items;
        }
        return $items;
    }

    /**
     * Ouput or response will print in app-debug.txt file in app's error folder
     * Note: Only use when you need to debug data in connection or server level, each store has its own debug function and debug file
     *
     * @param mix $data - Data which will print in debug file, data may array, object, string
     * @since 1.0.0
     */
    public function bcp_custom_debug($request_url, $request_method = "GET", $request, $request_time, $response, $response_time, $status = 0, $status_code = "", $trace = FALSE) {

        global $bcp_field_prefix;

        $file_dir = BCP_DEBUG_PATH;
        $total_files = glob($file_dir . "debug_*.log");
        $file_path = $file_dir . "debug_1.log";
        if (count($total_files) > 0) {
            $file_path = end($total_files);
        }
        if (in_array(substr(sprintf('%o', fileperms($file_dir)), -4), array('0777', '0755'))) {
            if (count($total_files) == 0 || file_exists($file_path) && filesize($file_path) > 2097152) {
                if (count($total_files) != 0) {
                    $next_files = count($total_files) + 1;
                    $file_path = $file_dir . "debug_" . $next_files . ".log";
                }
                $file1 = fopen($file_path, "a");
                fclose($file1);
            }
            $old_data = file_get_contents($file_path);
            $request = json_decode($request, 1);
            if (array_key_exists("Password", $request)) {
                $request['Password'] = "*********";
            }
            if (isset($request['data']['Password__c'])) {
                $request['data']['Password__c'] = "*********";
            }
            if (isset($request['data']['biztechcs__Password__c'])) {
                $request['data']['biztechcs__Password__c'] = "*********";
            }
            $request = json_encode($request);
            $new_data = array(
                "request_url" => $request_url,
                "method" => $request_method,
                "request" => $request,
                "request_time" => $request_time,
                "response" => $response,
                "response_time" => $response_time,
                "status" => $status,
                "status_code" => $status_code
            );

            $new_data = json_encode($new_data);

            if ($old_data != '') {
                $old_data = PHP_EOL . $old_data;
            }

            $final_data = $new_data . $old_data;

            if ($trace) {
                $tmp_backtrace = array();
                $final_backtrace = 'Backtrace - ' . PHP_EOL;
                $backtrace = debug_backtrace();

                if (is_array((array) $backtrace)) {
                    $backtrace_count = count($backtrace);
                    foreach ($backtrace as $key => $tmp_trace) {

                        $class = isset($tmp_trace['class']) ? $tmp_trace['class'] : '';
                        $function = isset($tmp_trace['function']) ? $tmp_trace['function'] : '';
                        $file = isset($tmp_trace['file']) ? $tmp_trace['file'] : '';
                        $line = isset($tmp_trace['line']) ? $tmp_trace['line'] : '';
                        $type = isset($tmp_trace['type']) ? $tmp_trace['type'] : '';

                        $tmp_backtrace[] = ( $backtrace_count - $key ) . '. ' . $class . $type . $function . ' ' . $file . ':' . $line . PHP_EOL;
                    }
                }

                if (!empty($tmp_backtrace)) {
                    $tmp_backtrace = array_reverse($tmp_backtrace);
                    $final_backtrace .= implode('', $tmp_backtrace);

                    $final_data .= $final_backtrace;
                }
            }

            file_put_contents($file_path, $final_data);
        }
    }

    /*
     *  Password Decryption Method
     */

    public function bcp_sha_encrypt($data, $salt_key = "bcp_portal_session_key") {
        $sSalt = fetch_data_option($salt_key);
        if (!empty($sSalt)) {
            $sSalt = substr(hash('sha256', $sSalt, true), 0, 32);
            $method = 'aes-256-cbc';
            $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
            $encrypted = base64_encode(openssl_encrypt($data, $method, $sSalt, OPENSSL_RAW_DATA, $iv));
            return $encrypted;
        } else {
            return $data;
        }
    }

    /*
     *  Password Decryption Method
     */

    public function bcp_sha_decrypt($data, $salt_key = "bcp_portal_session_key") {
        $sSalt = fetch_data_option($salt_key);
        if (!empty($sSalt)) {
            $sSalt = substr(hash('sha256', $sSalt, true), 0, 32);
            $method = 'aes-256-cbc';
            $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
            $decrypted = openssl_decrypt(base64_decode($data), $method, $sSalt, OPENSSL_RAW_DATA, $iv);
            return $decrypted;
        } else {
            return $data;
        }
    }

    /**
     * Function to get User IP Address
     */
    public function bcp_getUserIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /*
     * Get Portal Pages list
     */

    public function bcp_pages_list() {
        global $bcp_portal_pages;
        foreach ($bcp_portal_pages as $page) {
            $page_ids[] = fetch_data_option($page);
        }
        return $page_ids;
    }

    /*
     * Get segregate url
     */
    
    public function get_segregate_url($module_name, $page_type) {

        $response = array();
        $response['page_exists'] = false;

        $all_portal_page_args = array(
            'post_type' => 'page',
            'fields' => 'ids',
            'posts_per_page' => '1',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_bcp_portal_elements',
                    'value' => '1',
                    'compare' => '=',
                ),
                array(
                    'key' => '_bcp_page_type',
                    'value' => $page_type,
                    'compare' => '=',
                )
            )
        );

        if ((isset($module_name) && $module_name != '') && in_array($page_type, array('bcp_list', 'bcp_add_edit', 'bcp_detail'))) {
            $module_search_params = array(
                'key' => '_bcp_modules',
                'value' => $module_name,
                'compare' => '=',
            );

            array_push($all_portal_page_args['meta_query'], $module_search_params);
        }

        if (isset($_POST['post_ID']) && $_POST['post_ID'] != '') {
            $all_portal_page_args['post__not_in'] = array($_POST['post_ID']);
        }

        $bcp_page_exists = new WP_Query($all_portal_page_args);
        $total_pages = $bcp_page_exists->found_posts;
        if (isset($total_pages) && $total_pages > 0) {
            $response['page_exists'] = true;
            $response['page_url'] = get_the_permalink($bcp_page_exists->posts[0]);
        }
        return $response;
    }
    
    /*
     * Get all installed languages
     */
    public function get_all_installed_languages() {
        $languages = get_available_languages();
        if (!in_array('en_US', $languages)) {
            array_unshift($languages, 'en_US');
        }
        return $this->get_language_information($languages);
    }

    /*
     * Get languages information
     */
    public function get_language_information($languages) {
        require_once ABSPATH . 'wp-admin/includes/translation-install.php';
        $translations = wp_get_available_translations();
        $translations_keys = array_keys($translations);
        $final_languages = array();
        if (isset($languages) && is_array($languages) && count($languages) > 0) {
            foreach ($languages as $locale) {

                if (in_array($locale, $translations_keys)) {
                    $translation = $translations[$locale];
                    $final_languages[$locale] = array(
                        'language' => $translation['language'],
                        'native_name' => $translation['native_name'],
                        'lang' => $translation['iso']
                    );
                    unset($translations[$locale]);
                } else {
                    $final_languages[$locale] = array(
                        'language' => $locale,
                        'native_name' => ( $locale == 'en_US' ? "English" : "" ),
                        'lang' => $locale,
                    );
                }
            }
        }

        return $final_languages;
    }

    /*
     * Get languages dropdown
     */
    public function get_language_dropdown() {
        $dropdown_content = '';
        ob_start();

        $bcp_enable_multilang = fetch_data_option('bcp_enable_multilang');
        $bcp_default_language = fetch_data_option('bcp_default_language') ? fetch_data_option('bcp_default_language') : 'en_US';
        $enabled_languages = fetch_data_option('bcp_portal_language');
        if ($enabled_languages) {
            $enabled_languages_data = $this->get_language_information($enabled_languages);
        }

        if (isset($enabled_languages_data) && is_array($enabled_languages_data) && count($enabled_languages_data) > 1 && $bcp_enable_multilang && !empty($bcp_default_language)) {
            if (isset($_SESSION['bcp_language']) && !empty($_SESSION['bcp_language'])) {
                $bcp_default_language = $_SESSION['bcp_language'];
            }

            if ($bcp_default_language == 'en_US') {
                $flag_icon = 'flag-us';
                $lang_name = 'English';
            } else {
                $flag_icon = 'flag-' . $enabled_languages_data[$bcp_default_language]['lang'][1];
                $lang_name = $enabled_languages_data[$bcp_default_language]['native_name'];
            }
            ?>
            <!-- language-selection-section Start -->
            <div class="bcp-form-group language-selection-section">
                <!-- bcp_lang_dropdown Start -->
                <button class="dropdown bcp_lang_dropdown">
                    <a href="javascript:void(0);" class="language-dropdown-toggle">
                        <span class="flag <?php echo $flag_icon; ?>"></span> <?php echo $lang_name; ?><span class="fa fa-angle-down"></span>
                    </a>
                    <!-- sfcp-language-selector Start -->
                    <ul class="sfcp-language-selector language-dropdown-menu scrollbar scrollbar-primary">
                        <li style="display: none;">
                            <a href="javascript:void(0);" lang_code="en_US">
                                <span class="flag flag-us"></span>
                                English
                            </a>
                        </li>
                        <?php
                        echo '';
                        foreach ($enabled_languages_data as $lanuage_info) {
                        $selected_class = $lanuage_info['language'] == $bcp_default_language ? 'active_lang' : '';
                        $lang_flag = $lanuage_info['lang'][1];
                        if ($lanuage_info['language'] === 'en_US') {
                            $lang_flag = 'us';
                        }
                        ?>
                        <li class="<?php echo $selected_class; ?>">
                            <a href="javascript:void(0);" lang_code="<?php echo $lanuage_info['language']; ?>">
                                <span class="flag flag-<?php echo $lang_flag; ?>"></span>
                                <?php echo $lanuage_info['native_name']; ?>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                    <!-- sfcp-language-selector End -->
                </button>
                <!-- bcp_lang_dropdown End -->
            </div>
            <!-- language-selection-section End -->
            <?php
        }
        $dropdown_content .= ob_get_clean();
        return $dropdown_content;
    }

}
