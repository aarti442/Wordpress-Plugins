<?php
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Check user login and redirect to manage page
 *
 * @since 1.0.0
 * @param [type] $login
 * @return void
 */
class BCPCustomerPortalActions {

    private $plugin_name;
    private $version;
    private $bcp_helper;
    private $template;
    public $view_path;
    public $different_name_modules;
    public $modules_with_products;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->bcp_helper = new BCPCustomerPortalHelper($this->plugin_name, $this->version);
        $this->view_path = BCP_PLUGIN_DIR . "/public/partials/";
        $this->template = new TemplateHelper($this->view_path);
        $this->different_name_modules = array('contentdocument', 'contentnote', 'attachment');
       
        $this->modules_with_products = array('order','opportunity');
        $this->themecolor = new BCPCustomerPortalThemeColor();
    }

    /**
     * Check permission for any module
     *
     * @since 4.0.0
     * @param [type] $module_name
     * @param string $view
     * @param string $id
     * @return void
     * @author Akshay Kungiri 
     */
    public function check_pemission($module_name, $view = "access", $id = "") {

        global $objCp, $bcp_field_prefix;
       
        $bcp_module_access_data = $_SESSION['bcp_module_access_data'];
        $contact_id = $_SESSION['bcp_contact_id'];
     
        if ($contact_id != '') {

            if (isset($bcp_module_access_data->$module_name) && $bcp_module_access_data->$module_name->$view == 'true') {
                if ($id != "") {
                    if ($module_name == 'case' || $module_name == 'asset') {
                        $where = array(
                            'ContactId' => $contact_id,
                        );
                    } else {
                        $where = array(
                            $bcp_field_prefix . 'ContactId__c' => $contact_id
                        );
                    }
                    if ($module_name == 'account') {
                        if (!isset($_SESSION['bcp_account_id']) || $_SESSION['bcp_account_id'] != $id) {
                            if ($view == 'delete') {
                                echo "-1";
                            } else {
                                
                                echo "<span class='error error-line error-msg settings-error'> " . __('You don\'t have sufficient permission to access this data.', 'bcp_portal') . "</span>";
                            }
                            wp_die();
                        }
                    } else {
                        if ($view == 'delete' && $module_name == "case") {
                            $where['Id'] = $id;
                            $records = $objCp->getRecords($module_name, $where);
                            if (empty($records->records)) {
                                echo "-1";
                                wp_die();
                            }
                        }
                    }
                }
            } else {
                if ($view == 'delete') {
                    echo "-1";
                } else {
                  
                    echo "<span class='error error-line error-msg settings-error'> " . __('You don\'t have sufficient permission to access this data.', 'bcp_portal') . "</span>";
                }
                wp_die();
            }
        } else {

            $bcp_login_page_id = fetch_data_option('bcp_login_page');
            $bcp_login_page = $bcp_login_page_id != '' ? get_permalink($bcp_login_page_id) : '';
            if ($bcp_login_page == '') {
                $bcp_login_page = home_url('/portal-login/');
            }
            wp_redirect($bcp_login_page);
        }
    }

    /**
     * After login data set in session
     *
     * @since 4.0.0
     * @param [type] $module_name
     * @param string $view
     * @param string $id
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_authorize($login) {
       
        global $objCp, $wpdb, $bcp_field_prefix, $bcp_secure_flag;
       
        if (isset($login->success) && $login->success != 0 && isset($login->contact_detail->portal_status) && $login->contact_detail->portal_status == 'Approved' && isset($login->contact_detail->isverify) && $login->contact_detail->isverify) {

            session_regenerate_id();
            
            $ip = $this->bcp_helper->bcp_getUserIpAddr();
            $record = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_login_attempts WHERE ip_address='{$ip}'");
            if (!empty($record)) {
                $wpdb->update($wpdb->base_prefix . 'bcp_login_attempts', array(
                    'ip_address' => $ip,
                    'login_attempt' => "0",
                    'last_login_date' => date("Y-m-d H:i:s"),
                    'ip_status' => "active",
                        ), array('id' => $record->id)
                );
            }

            $_SESSION['browser_timezone'] = $_REQUEST['browser_timezone'];
          
            if (isset($login->ModuleAccessData) && $login->ModuleAccessData != null) {
                $bcp_accessible_modules = array();

                $ModuleAccessData = json_decode($login->ModuleAccessData);

                $bcp_module_access_data = ( $ModuleAccessData != NULL ) ? $ModuleAccessData : array();
                $_SESSION['bcp_module_access_data'] = $bcp_module_access_data;
                $modules_with_products = array();
                if (!empty($bcp_module_access_data)) {
                    foreach ($bcp_module_access_data as $key => $module_rights) {
                       
                        if ($module_rights->access == 'false' || ( $key == 'knowledgearticleversion' && isset($login->knowledgebase_enabled) && !$login->knowledgebase_enabled )) {
                            unset($_SESSION['bcp_module_access_data']->$key);
                        } else {
                            if (!in_array($key, $bcp_accessible_modules)) {
                                $bcp_accessible_modules[] = $key;
                            }
                        }
                    }
                }
            }
            
            if (isset($login->ModuleAccessData) && $login->ModuleAccessData != null) {
                $ModuleAccessData = json_decode($login->ModuleAccessData);
                $lists = ( $ModuleAccessData != NULL ) ? $ModuleAccessData : array();
                $_SESSION['bcp_all_module_access_list'] = $lists;
            }
            $_SESSION['bcp_default_currency'] = ( ( isset($login->defaultCurrency) && $login->defaultCurrency != '' ) ? $login->defaultCurrency : 'USD' );

            $_SESSION['bcp_contact_id'] = $login->contact_detail->cId;
            $_SESSION['bcp_contact_name'] = ( $login->contact_detail->Name ? $login->contact_detail->Name : $login->contact_detail->LastName );
            $_SESSION['bcp_case_deflection'] = ( ( isset($login->PortalRoleData->case_deflection) && $login->PortalRoleData->case_deflection == '1' ) ? 1 : 0 );
            if (isset($login->AccountId) && $login->AccountId != '') {
                $_SESSION['bcp_account_id'] = $login->AccountId;
            }
            $_SESSION['bcp_disabled_object'] = ( $login->Disable_Object ? $login->Disable_Object : '' );
            $_SESSION['bcp_relationship_type'] = ( $login->PortalRoleData->relationship_type ? $login->PortalRoleData->relationship_type : '' );
            
            $bcp_modules = array();

            $module_layouts = $wpdb->get_results("SELECT * FROM " . $wpdb->base_prefix . "bcp_fields WHERE module IN ('" . implode("','", $bcp_accessible_modules) . "') ");
            if (isset($module_layouts) && is_array($module_layouts) && count($module_layouts) > 0) {
                foreach ($module_layouts as $layout_data) {
                    $bcp_modules[$layout_data->module]['singular'] = $layout_data->singular;
                    $bcp_modules[$layout_data->module]['plural'] = $layout_data->plular;
                }
            }
            
            if (!empty($bcp_modules)) {

                if (isset($bcp_modules['contact'])) {
                    unset($bcp_modules['contact']);
                }

                if (isset($_SESSION['bcp_module_access_data']->contentdocument)) {

                    $bcp_modules['contentdocument'] = array(
                        'singular' => __('Content Document', 'bcp_portal'),
                        'plural' => __('Content Documents', 'bcp_portal'),
                    );
                }
                if (isset($_SESSION['bcp_module_access_data']->call)) {

                    $bcp_modules['call'] = array(
                        'singular' => __('Call', 'bcp_portal'),
                        'plural' => __('Calls', 'bcp_portal'),
                    );
                }
                if (isset($_SESSION['bcp_module_access_data']->knowledgearticleversion)) {

                    $bcp_modules['KBDocuments'] = array(
                        'singular' => __('Knowledge Base Article', 'bcp_portal'),
                        'plural' => __('Knowledge Base Articles', 'bcp_portal'),
                    );
                }
                if (isset($_SESSION['bcp_module_access_data']->contentnote)) {

                    $bcp_modules['contentnote'] = array(
                        'singular' => __('Note', 'bcp_portal'),
                        'plural' => __('Notes', 'bcp_portal'),
                    );
                }

                asort($bcp_modules);
            }
            $bcp_portal_role = isset($login->ContactPortalRole) ? $login->ContactPortalRole : "";
            $_SESSION['bcp_portal_role'] = $bcp_portal_role;
            
            $portalSubPanel = json_decode($login->SubPanelData->portalSubPanel);
            if (isset($_SESSION['bcp_portalSubPanel'])) {
                unset($_SESSION['bcp_portalSubPanel']);
            }
            if (isset($portalSubPanel->$bcp_portal_role) && !empty($portalSubPanel->$bcp_portal_role)) {
                $_SESSION['bcp_portalSubPanel'] = $portalSubPanel->$bcp_portal_role;
            }
            
            $_SESSION['bcp_modules'] = $bcp_modules;
            $bcp_dashboard_page = fetch_data_option('bcp_dashboard_page');
            if ($bcp_dashboard_page) {
                $bcp_dashboard_page = get_permalink($bcp_dashboard_page);
                $response['url'] = $bcp_dashboard_page;
            }
        } elseif (isset($login->contact_detail->isverify) && !$login->contact_detail->isverify) {
            $_SESSION['bcp_error_message'] = __("Please verify your email to login.", 'bcp_portal');
            $response['url'] = add_query_arg(array('error' => '1'), $_REQUEST['bcp_current_url']);
        } elseif (isset($login->contact_detail->portal_status) && $login->contact_detail->portal_status != 'Approved') {
            $_SESSION['bcp_error_message'] = __("Account approval pending! Kindly contact your administrator.", 'bcp_portal');
            $response['url'] = add_query_arg(array('error' => '1'), $_REQUEST['bcp_current_url']);
        } else {
            $_SESSION['bcp_error_message'] = __("Incorrect Username or Password or Don't have an account yet.", 'bcp_portal');
            $response['url'] = $_REQUEST['bcp_current_url'] . '?error=1';
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Login call back
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_login_callback() {

        global $objCp, $wpdb, $bcp_field_prefix;
       
        if (!isset($_POST['bcp_portal_nonce_field']) || !wp_verify_nonce($_POST['bcp_portal_nonce_field'], 'bcp_portal_nonce')) {
            $response['url'] = $_REQUEST['bcp_current_url'] . '?error=1';
            $_SESSION['bcp_error_message'] = __("The form has expired due to inactivity. Please try again.", 'bcp_portal');
            echo json_encode($response);
            exit();
        }

        $bcp_login_page = fetch_data_option('bcp_login_page');
        $bcp_login_password_enable = fetch_data_option('bcp_login_password_enable');

        if ($bcp_login_page) {
            $_REQUEST['bcp_current_url'] = get_permalink($bcp_login_page);
        }

        // For Captcha validation
        $secret_key = fetch_data_option('bcp_recaptcha_secret_key');
        $captcha_responce = ( isset($_REQUEST['g-recaptcha-response']) ? $_REQUEST['g-recaptcha-response'] : "" );
        if ($captcha_responce != "") {
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $_POST['g-recaptcha-response']);
            $responseData = json_decode($verifyResponse);
            if (isset($responseData->success) && !$responseData->success) {
                $error_msg = __("Secret key of reCaptcha is not properly configured. Please contact to administrator.", 'bcp_portal');
                $_SESSION['bcp_error_message'] = $error_msg;
                $current_url = isset($_REQUEST['bcp_current_url']) ? esc_url($_REQUEST['bcp_current_url']) : '';
                $redirect_url = add_query_arg(array('error' => '1'), $current_url);
                $response['url'] = $current_url . '?error=1';
                echo json_encode($response);
                exit;
            }
        }

        $ip = $this->bcp_helper->bcp_getUserIpAddr();
        $record = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_login_attempts WHERE ip_address='{$ip}'");
        if (!empty($record) && !$bcp_login_password_enable) {
            $sfcp_portal_login_min = fetch_data_option("bcp_lockout_effective_period");
            $bcp_maximum_login_attempts = fetch_data_option('bcp_maximum_login_attempts');
            $login_attempt = isset($record->login_attempt) ? $record->login_attempt : 1;
            $last_login_date = isset($record->last_login_date) ? $record->last_login_date : date("Y-m-d H:i:s");
            $bcp_last_login_date = new DateTime($last_login_date);
            $since_start = $bcp_last_login_date->diff(new DateTime());
            $min_diff = $since_start->i;
            if ($login_attempt >= $bcp_maximum_login_attempts && $min_diff < $sfcp_portal_login_min) {
                $response['url'] = $_REQUEST['bcp_current_url'] . '?error=1';
                echo json_encode($response);
                exit;
            }
        }
        if ((isset($_REQUEST['bcp_username']) && isset($_REQUEST['bcp_password'])) || isset($_REQUEST['sfcp_otp'])) {
            $tmp_contact_ids = isset($_COOKIE['sfcp_sss_ccc_1123']) ? unserialize(stripslashes($_COOKIE['sfcp_sss_ccc_1123'])) : array();
            $contact_usernames = array();
            foreach ($tmp_contact_ids as $value) {
                $c_id = $this->bcp_decrypt_string($value);
                array_push($contact_usernames, $c_id);
            }
            $username = (isset($_REQUEST['bcp_username']) ? $_REQUEST['bcp_username'] : "");
            $password = (isset($_REQUEST['bcp_password']) ? $_REQUEST['bcp_password'] : "");
            $data = array(
                'Username' => $username,
                'Password' => $password,
                "OtpFlag" => false,
            );
            if (fetch_data_option("sfcp_customer_otp_enable") == "1" && !in_array($username, $contact_usernames)) {
                $data["OtpFlag"] = true;
            }
           
            $login = $objCp->portalLogin($data);
            
            $otp_response = isset($login->otp_response) ? json_decode($login->otp_response) : "";
            if (fetch_data_option("sfcp_customer_otp_enable") == "1" && isset($otp_response->success) && $otp_response->success == "1") {
                $_SESSION['bcp_otp_contact_id'] = $login->ContactId;
                $_SESSION['bcp_user_name'] = $username;
                $_SESSION['bcp_success_message'] = $otp_response->message;
                $response['url'] = $_REQUEST['bcp_current_url'] . '?otp=1';
                echo json_encode($response);
                exit;
            }
            if ($login->success == '0') {

                if ($bcp_login_password_enable) {
                    if (empty($record)) {
                        $wpdb->insert(
                                $wpdb->base_prefix . 'bcp_login_attempts', array(
                            'ip_address' => $ip,
                            'login_attempt' => 1,
                            'last_login_date' => date("Y-m-d H:i:s"),
                            'ip_status' => "active",
                                )
                        );
                    } else {
                        $login_attempt = isset($record->login_attempt) ? $record->login_attempt : 1;
                        $login_attempt += 1;
                        $wpdb->update(
                                $wpdb->base_prefix . 'bcp_login_attempts', array(
                            'ip_address' => $ip,
                            'login_attempt' => $login_attempt,
                            'last_login_date' => date("Y-m-d H:i:s"),
                            'ip_status' => "active",
                                ), array('id' => $record->id)
                        );
                    }
                }
                if (isset($login->status) && $login->status == "PASSWORD_EXPIRED" && isset($login->ContactId)) {
                    $contact_id = $login->ContactId;
                    $_SESSION['bcp_set_password_contact_id'] = $this->bcp_helper->bcp_sha_encrypt($contact_id);
                    $_SESSION['bcp_error_message'] = $login->message;
                    $sfcp_reset_pwd_page = fetch_data_option('bcp_reset_password_page');
                    $sfcp_reset_pwd = get_permalink($sfcp_reset_pwd_page);
                    $response['url'] = $sfcp_reset_pwd . '?error=1';
                    echo json_encode($response);
                    exit;
                } else {
                    $_SESSION['bcp_error_message'] = $login->message;
                    $response['url'] = $_REQUEST['bcp_current_url'] . '?error=1';
                    echo json_encode($response);
                    exit;
                }
            }
            $this->bcp_authorize($login);
        }
    }

    /**
     * Portal OTP submit callback
     *
     * @since 1.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_resend_otp_callback() {

        global $objCp;
        $data['ContactId'] = $_SESSION['bcp_otp_contact_id'];
        $data['Resend'] = true;
        $data['OTP'] = "";
        $login = $objCp->portalOtp($data);
        $result = json_decode($login->otp_response);
        $bcp_login_page = fetch_data_option('bcp_login_page');
        if ($bcp_login_page) {
            $bcp_login_page = get_permalink($bcp_login_page);
        }
        if (isset($result->success) && $result->success == "1") {
            $_SESSION['bcp_success_message'] = (isset($result->message) ? $result->message : __("Otp Send Successfully.", 'bcp_portal'));
            $sfcp_redirect_page = add_query_arg(array('success' => '1'), $bcp_login_page);
            $response['url'] = $bcp_login_page . '?success=1';
        } else {
            $message = isset($result->message) ? $result->message : __("Invalid otp.", 'bcp_portal');
            $_SESSION['bcp_error_message'] = $message;
            $redirect_url = add_query_arg(array('error' => '1'), $bcp_login_page);
            $response['url'] = $bcp_login_page . '?error=1';
        }
        echo json_encode($response);
        exit;
    }

    /**
     * Verify submited OTP
     *
     * @since 1.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_verify_otp_callback() {

        global $objCp, $bcp_secure_flag;

        if (!isset($_POST['bcp_portal_nonce_field']) || !wp_verify_nonce($_POST['bcp_portal_nonce_field'], 'bcp_portal_nonce')) {
            $response['url'] = $_REQUEST['bcp_current_url'] . '?error=1';
            $_SESSION['bcp_error_message'] = __("The form has expired due to inactivity. Please try again.", 'bcp_portal');
            echo json_encode($response);
            exit();
        }

        $data['ContactId'] = $_SESSION['bcp_otp_contact_id'];
        $data['Resend'] = false;
        $data['OTP'] = (isset($_REQUEST['sfcp_otp']) ? $_REQUEST['sfcp_otp'] : "");
        $login = $objCp->portalOtp($data);
        $otp_response = isset($login->otp_response) ? json_decode($login->otp_response) : "";
        $bcp_login_page = fetch_data_option('bcp_login_page');
        if ($bcp_login_page) {
            $bcp_login_page = get_permalink($bcp_login_page);
        }
        if (isset($login->PortalRoleData) && $login->PortalRoleData != "") {
            if (isset($_SESSION['bcp_otp_contact_id'])) {
                $username = $_SESSION['bcp_user_name'];
                unset($_SESSION['bcp_user_name']);
                unset($_SESSION['bcp_otp_contact_id']);
                $contact_usernames = isset($_COOKIE['sfcp_sss_ccc_1123']) ? unserialize(stripslashes($_COOKIE['sfcp_sss_ccc_1123'])) : array();
                array_push($contact_usernames, $this->bcp_encrypt_string($username));
                setcookie("sfcp_sss_ccc_1123", serialize($contact_usernames), 0, "/", "", $bcp_secure_flag, true);
            }
            $this->bcp_authorize($login);
        } else {
            $message = isset($otp_response->message) ? $otp_response->message : __("Invalid otp.", 'bcp_portal');
            $_SESSION['bcp_error_message'] = $message;
            $redirect_url = add_query_arg(array('error' => '1'), $bcp_login_page);
            $response['url'] = $bcp_login_page . '?error=1';
            echo json_encode($response);
            exit;
        }
    }

    /**
     * Portal Logut
     *
     * @since 1.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_portal_logout() {
        session_start();
        unset($_SESSION['bcp_contact_id']);
        unset($_SESSION['bcp_module_access_data']);
        unset($_SESSION['bcp_modules']);
        unset($_SESSION['bcp_case_deflection']);
        unset($_SESSION['bcp_portal_role']);
        unset($_SESSION['bcp_language']);
        unset($_SESSION['browser_timezone']);
        unset($_SESSION['bcp_account_id']);
        unset($_SESSION['bcp_contact_name']);
       // unset($bcp_authentication);
        session_regenerate_id();
        $bcp_login_page = fetch_data_option('bcp_login_page');
        if ($bcp_login_page != null) {
            $bcp_login_page = get_permalink($bcp_login_page);
            wp_redirect($bcp_login_page);
            exit;
        }
    }

    /**
     * Change password based on submited data
     *
     * @since 1.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_change_password_callback() {

        global $objCp, $bcp_field_prefix;

        $bcp_profile_page = fetch_data_option('bcp_profile_page');

        $request = $_REQUEST;
        $record_id = $request['record_id'];
        unset($request['action']);
        unset($request['bcp_current_url']);
        unset($request['submit']);
        unset($request['record_id']);

        if (isset($request[$bcp_field_prefix . 'Current_Password__c']) && $request[$bcp_field_prefix . 'Current_Password__c'] != '' &&
                isset($request[$bcp_field_prefix . 'Password__c']) && $request[$bcp_field_prefix . 'Password__c'] != '' &&
                isset($request[$bcp_field_prefix . 'Retype_Password__c']) && $request[$bcp_field_prefix . 'Retype_Password__c'] != ''
        ) {

            $pwd_key = $bcp_field_prefix . 'Password__c';
            $current_password = $request[$bcp_field_prefix . 'Current_Password__c'];
            $new_password = $request[$bcp_field_prefix . 'Password__c'];
            $retype_password = $request[$bcp_field_prefix . 'Retype_Password__c'];

            if ($new_password != $retype_password) {
                $response['success'] = FALSE;
                $response['message'] = __('Confirm password must match.', 'bcp_portal');
            } else {

                $where_cond = array(
                    "Id" => $_SESSION['bcp_contact_id'],
                );
                $argemunts = array(
                    "module" => "Contact",
                    "where" => $where_cond,
                    "fields" => json_encode(array($pwd_key => 'Password')),
                );
                $records = $objCp->getModuleRecords($argemunts);
                $contact = isset($records->records[0]) ? $records->records[0] : array();

                if (isset($contact->$pwd_key) && $contact->$pwd_key == md5($current_password)) {

                    $request_filters['Password__c'] = $new_password;
                    $record_id = $contact->Id;
                    $arguments = array(
                        "record_id" => $record_id,
                        "data" => $request_filters,
                    );
                    $result = $objCp->checkPortalChangePassword($arguments);
                    if (isset($result->success) && $result->success) {
                        $response['success'] = TRUE;
                        $response['message'] = __('Your password has been updated successfully.', 'bcp_portal');
                    } else {
                        $message = (isset($result->message) && !empty($result->message)) ? $result->message : __("Enter a valid password", 'bcp_portal');
                        $response['success'] = FALSE;
                        $response['message'] = $message;
                    }
                } else {

                    $response['success'] = FALSE;
                    $response['message'] = __('Invalid Password!', 'bcp_portal');
                }
            }
        } else {

            $response['success'] = FALSE;
            $response['message'] = __('Please enter value in required fields.', 'bcp_portal');
        }
        echo json_encode($response);
        wp_die();
    }

    /**
     * Forgot password 
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_forgot_password_callback() {

        global $objCp, $bcp_field_prefix;

        if (!isset($_POST['bcp_portal_nonce_field']) || !wp_verify_nonce($_POST['bcp_portal_nonce_field'], 'bcp_portal_nonce')) {
            $response['url'] = $_REQUEST['bcp_current_url'] . '?error=1';
            $_SESSION['bcp_error_message'] = __("The form has expired due to inactivity. Please try again.", 'bcp_portal');
            echo json_encode($response);
            exit();
        }

        $bcp_forgot_password_page = fetch_data_option('bcp_forgot_password_page');
        if ($bcp_forgot_password_page) {
            $_REQUEST['bcp_current_url'] = get_permalink($bcp_forgot_password_page);
        }

        // For Captcha validation
        $secret_key = fetch_data_option('bcp_recaptcha_secret_key');
        $captcha_responce = ( isset($_REQUEST['g-recaptcha-response']) ? $_REQUEST['g-recaptcha-response'] : "" );
        if ($captcha_responce != "") {
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $_POST['g-recaptcha-response']);
            $responseData = json_decode($verifyResponse);
            if (isset($responseData->success) && !$responseData->success) {
                $error_msg = __("Secret key of reCaptcha is not properly configured. Please contact to administrator.", 'bcp_portal');
                $_SESSION['bcp_error_message'] = $error_msg;
                $current_url = isset($_REQUEST['bcp_current_url']) ? esc_url($_REQUEST['bcp_current_url']) : '';
                $response['url'] = $current_url . '?error=1';
            }
        }

        if (isset($_REQUEST['sfcp_email'])) {
            $isSent = $objCp->PortalForgotPassword(array("email" => $_REQUEST['sfcp_email']));

            if (isset($isSent->success) && $isSent->success) {
                $bcp_login_page = fetch_data_option('bcp_login_page');
                $bcp_login_page = get_permalink($bcp_login_page);
                $_SESSION['bcp_success_message'] = __('Your reset password link has been sent successfully.', 'bcp_portal');
                $response['url'] = $bcp_login_page . '?success=1';
            } else {
                if (isset($isSent->message) && $isSent->message != "") {
                    $_SESSION['bcp_error_message'] = $isSent->message;
                } else {
                    $_SESSION['bcp_error_message'] = __('Email does not exist.', 'bcp_portal');
                }
                $response['url'] = $_REQUEST['bcp_current_url'] . '?error=1';
            }
            $_SESSION['bcp_success_message'] = __('Your reset password link has been sent successfully.', 'bcp_portal');
        }
        echo json_encode($response);
        exit;
    }

    /**
     * Handle form submited data
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_form_submit_callback() {
        global $objCp, $bcp_field_prefix, $wpdb, $bcp_secure_flag;
        //ini_set("display_errors", "on");
        $module_name = $_REQUEST['module_name'];
        $tmp_module_name = $module_name;
        $bcp_modules = isset($_SESSION['bcp_modules']) ? $_SESSION['bcp_modules'] : "";
        if($module_name == 'CaseComment'){
            $bcp_modules['CaseComment']['singular'] = __('Comment', 'bcp_portal');
        }
       
        $id = isset($_REQUEST['Id']) ? sanitize_text_field($_REQUEST['Id']) : "";
        

        if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
            $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->accountRelationship;
            $contact_id = $_SESSION['bcp_account_id'];
            
            if($module_name == "account"){
                $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                $contact_id = $_SESSION['bcp_contact_id'];
            }
        }else{
            $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
            $contact_id = $_SESSION['bcp_contact_id'];
        }

        $relate_module_relationship = isset($_REQUEST['relationship']) ? $_REQUEST['relationship'] : "";
        $relate_module_name = isset($_REQUEST['relate_module_name']) ? $_REQUEST['relate_module_name'] : "";
        $relate_module_id = isset($_REQUEST['relate_module_id']) ? $_REQUEST['relate_module_id'] : "";

        if ($module_name == 'contact') {
            $relationship_field_key = 'Id';
            $contact_id = $_SESSION['bcp_contact_id'];
        }
        $response = array();
        $response['module_name'] = $module_name;

        if (!isset($_POST['bcp_portal_nonce_field']) || !wp_verify_nonce($_POST['bcp_portal_nonce_field'], 'bcp_portal_nonce')) {
            $response['redirect_url'] = "";
            $response['success'] = FALSE;
            $response['msg'] = __("The form has expired due to inactivity. Please try again.", 'bcp_portal');
            echo json_encode($response);
            exit();
        }

        if (isset($_SESSION['bcp_portal_role'])) {
            $bcp_portal_role = $_SESSION['bcp_portal_role'];
        } else {
            $bcp_portal_role = fetch_data_option("sfcp_primary_role");
        }

        $Username__c = $bcp_field_prefix . 'Username__c'; // For Sigup Form Submit
        $Password__c = $bcp_field_prefix . 'Password__c'; // For Sigup Form Submit
        $enable_portal__c = $bcp_field_prefix . 'enable_portal__c';
        $Customer_Portal_Role__c = $bcp_field_prefix . 'Customer_Portal_Role__c';
        $exclude_fields = array("Email", $Username__c, $Password__c, $enable_portal__c, $Customer_Portal_Role__c);

        $request = $_REQUEST;
        if (!isset($_SESSION['browser_timezone']) && !$_SESSION['browser_timezone']) {
            $_SESSION['browser_timezone'] = $request['browser_timezone'] ? $request['browser_timezone'] : date_default_timezone_get();
        }
        $parentId = "";
        $parentmodule = '';
        if (isset($request['ParentId']) && isset($request['ParentModule'])) {
            $parentId = sanitize_text_field($request['ParentId']);
            $parentmodule = sanitize_text_field($request['ParentModule']);
            unset($request['ParentId']);
            unset($request['ParentModule']);
        }else{
            if(isset($relate_module_name) && isset($relate_module_id)){
                $parentmodule = $relate_module_name;
                $parentId = $relate_module_id;
            }
        }
        unset($request['module_name']);
        if (in_array($module_name, $this->different_name_modules)) {
            $layout = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts WHERE module='$module_name' AND view='edit'");
        } else {
            $layout = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts WHERE module='$module_name' AND view='edit' AND role='$bcp_portal_role'");
        }
        $fields = ( isset($layout->fields) ? (array) ( json_decode($layout->fields) ) : array() );
        if (!empty($fields) || $module_name == "contentdocument" || $module_name == "CaseComment" || $module_name == "contentnote" || $module_name == "attachment") {
            $field_definition_result = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_fields WHERE module='$module_name'");
            $layout_data = isset($field_definition_result->fields) ? unserialize($field_definition_result->fields) : array();
            $layout_fields = array();
            $request_filters = array();
            $bcp_error = 0;
            $bcp_error_msg = array();
            if (!empty($layout_data)) {
                foreach ($layout_data as $layout_data_value) {
                    $layout_fields[$layout_data_value->name] = $layout_data_value;
                }
            }
           
            foreach ($fields as $key => $value) {
                if (isset($layout_fields[$key])) {
                    $layout_field = $layout_fields[$key];
                   
                    $req_value = (isset($request[$key]) ? $request[$key] : "");

                    if ($key == 'AccountId' && $module_name == 'contract' && isset($_SESSION['bcp_account_id'])) {
                        $req_value = $_SESSION['bcp_account_id'];
                        $request_filters[$key] = $req_value;
                    }

                    if ($module_name == "contact" && $contact_id != "" && in_array($key, $exclude_fields)) {
                        continue;
                    }

                    if ($layout_field->name == 'Email' || $layout_field->name == $Username__c || $layout_field->name == $Password__c) {
                        $layout_field->nillable = FALSE;
                    }

                    if ($layout_field->name == 'Email' && $module_name == "lead") {
                        $layout_field->nillable = true;
                    }

                    if ((isset($layout_field->nillable) && !$layout_field->nillable && isset($layout_field->createable) && !$layout_field->createable && isset($layout_field->defaultedOnCreate) && !$layout_field->defaultedOnCreate) && empty($req_value)) {
                        $bcp_error = 1;
                        array_push($bcp_error_msg, array(
                            "Key" => $key,
                            "Value" => __("This field is required.", 'bcp_portal'),
                        ));
                    } else {
                        if (isset($layout_field->type) && $layout_field->type && isset($request[$key]) && !empty($request[$key])) {
                            switch ($layout_field->type) {
                                case "email":
                                    if (!is_email($req_value)) {
                                        $bcp_error = 1;
                                        array_push($bcp_error_msg, array(
                                            "Key" => $key,
                                            "Value" => __("Please enter a valid email address.", 'bcp_portal'),
                                        ));
                                    } else {
                                        $request_filters[$key] = sanitize_email($req_value);
                                    }
                                    break;
                                case "double":
                                    if (is_numeric($req_value)) {
                                        $request_filters[$key] = (double) ($req_value);
                                    } else {
                                        $bcp_error = 1;
                                        array_push($bcp_error_msg, array(
                                            "Key" => $key,
                                            "Value" => __("Please enter a valid number.", 'bcp_portal'),
                                        ));
                                    }
                                    break;
                                case "boolean":
                                    if ($req_value == "1") {
                                        $request_filters[$key] = TRUE;
                                    } else {
                                        $request_filters[$key] = FALSE;
                                    }
                                    break;
                                case "multipicklist":
                                    if (is_array($req_value)) {
                                        $req_value = implode(';', $req_value);
                                        $req_value = trim($req_value, ';');
                                        $req_value = sanitize_text_field($req_value);
                                        $request_filters[$key] = $req_value;
                                    }
                                    break;
                                case "date":
                                case "datetime":
                                case "time":
                                    if ($layout_field->type == 'date') {
                                        $req_value = date("Y-m-d", strtotime($request[$key]));
                                    } else if ($layout_field->type == 'datetime') {
                                        $req_value = $this->bcp_convert_date($request[$key], "Y-m-d H:i:s");
                                    } else if ($layout_field->type == 'time') {
                                        $date = new DateTime($request[$key]);
                                        $req_value = $date->format('H:i:s');
                                    }
                                    $request_filters[$key] = $req_value;
                                    break;
                                case "textarea":
                                    $request_filters[$key] = sanitize_textarea_field(stripslashes($req_value));
                                    break;
                                default :
                                    $request_filters[$key] = sanitize_text_field(stripslashes($req_value));
                                    break;
                            }
                        }
                    }
                }
            }
           
            if ($module_name == "contentdocument" || $module_name == "attachment") {
                $filename = isset($_FILES['Attachment']['name']) ? $_FILES['Attachment']['name'] : "";
                $filename = isset($_FILES['Body']['name']) ? $_FILES['Body']['name'] : $filename;
                $file_info = pathinfo($filename);
                $file_extension = isset($file_info['extension']) ? $file_info['extension'] : "";
                $allowed_files = array('jpg', 'JPG', 'JPEG', 'PNG', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'zip');

                if (isset($_FILES['Attachment']['name']) && $_FILES['Attachment']['name'] == "") {
                    $bcp_error = 1;
                    array_push($bcp_error_msg, array(
                        "Key" => "Attachment",
                        "Value" => __("This field is required.", 'bcp_portal'),
                    ));
                } else if (!in_array($file_extension, $allowed_files)) {
                    $bcp_error = 1;
                    array_push($bcp_error_msg, array(
                        "Key" => "Attachment",
                        "Value" => __("Please add any of the supported file format.", 'bcp_portal'),
                    ));
                }
                if (isset($_REQUEST['Description']) && empty($_REQUEST['Description'])) {
                    $bcp_error = 1;
                    array_push($bcp_error_msg, array(
                        "Key" => "Description",
                        "Value" => __("This field is required.", 'bcp_portal'),
                    ));
                }
            }

            if ($module_name == "CaseComment" && (isset($_REQUEST['CommentBody']) && empty(trim($_REQUEST['CommentBody']))) ) {
                $bcp_error = 1;
                array_push($bcp_error_msg, array(
                    "Key" => "CommentBody",
                    "Value" => __("This field is required.", 'bcp_portal'),
                ));
            }
            
            if ($module_name == "contentnote") {

                if (isset($_REQUEST['Content']) && empty(trim($_REQUEST['Content']))) {
                    $bcp_error = 1;
                    array_push($bcp_error_msg, array(
                        "Key" => "Content",
                        "Value" => __("This field is required.", 'bcp_portal'),
                    ));
                }

                $tmp_module_name = 'note';
            }

            if (!$bcp_error) {

                if ($module_name == "case" || $module_name == "contact") {
                    $request_filters[$bcp_field_prefix . 'source_from__c'] = 'Portal';
                }

                if ($module_name == "call" || $module_name == "task") {
                    $request_filters['tasksubtype'] = $module_name;
                    $tmp_module_name = 'task';
                }
            
                if ($module_name == "contact" && $contact_id == "") {

                    // For Captcha validation
                    $secret_key = fetch_data_option('bcp_recaptcha_secret_key');
                    $captcha_responce = ( isset($_REQUEST['g-recaptcha-response']) ? $_REQUEST['g-recaptcha-response'] : "" );
                    if ($captcha_responce != "") {
                        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $_POST['g-recaptcha-response']);
                        $responseData = json_decode($verifyResponse);
                        if (isset($responseData->success) && !$responseData->success) {
                            $error_msg = __("Secret key of reCaptcha is not properly configured. Please contact to administrator.", 'bcp_portal');
                            $response['redirect_url'] = "";
                            $message = $error_msg;
                            $response['success'] = FALSE;
                            $response['msg'] = $message;
                            echo json_encode($response);
                            exit;
                        }
                    }

                    //Enable Customer Portal For Registered User
                    $request_filters[$bcp_field_prefix . 'enable_portal__c'] = TRUE;
                    //For approval flow 
                    $request_filters[$bcp_field_prefix . 'portal_status__c'] = fetch_data_option('bcp_customer_approval_enable') == '1' ? 'Pending' : 'Approved';
                    //For verification flow from v3.2.0
                    $request_filters[$bcp_field_prefix . 'IsVerify__c'] = fetch_data_option('bcp_customer_verify_enable') == '1' ? FALSE : TRUE;

                    $arguments = array(
                        "module" => "Contact",
                        "data" => $request_filters,
                    );
                    
                    $c_data = $objCp->PortaSignupCall($arguments);
                    if (isset($c_data->success) && $c_data->success) {
                        $bcp_login_page = fetch_data_option('bcp_login_page');
                        $bcp_login_page = ( $bcp_login_page ? get_permalink($bcp_login_page) : site_url() );
                        $response['redirect_url'] = add_query_arg(array('success' => '1'), $bcp_login_page);
                        $_SESSION['bcp_success_message'] = __('You have successfully registered.', 'bcp_portal');
                        $response['msg'] = __('You have successfully registered.', 'bcp_portal');
                        $response['success'] = TRUE;
                    } else {
                        $response['redirect_url'] = "";
                        $message = isset($c_data->message) ? $c_data->message : __("There is something error while signup. Please contact to administrator.", 'bcp_portal');
                        $response['success'] = FALSE;
                        $response['msg'] = $message;
                    }
                } else {
                    //Set this to recognize when any record added or edited from portal
                    if (!empty($id) && $module_name == "case") {
                        $request_filters[$bcp_field_prefix . 'Modified_From__c'] = 'Portal';
                    }

                    if ($module_name == 'case') {
                        //$request_filters['ContactId'] = $contact_id;
                        $request_filters['ContactId'] = $_SESSION['bcp_contact_id'];
                    }

                    if ( isset($_REQUEST['pricebook']) && in_array($module_name, $this->modules_with_products)) {
                        $pricebook = $_REQUEST['pricebook'];
                        $request_filters['AccountId'] = $_SESSION['bcp_account_id'];
                        if(!$id){
                            $request_filters['PriceBook2Id'] = $pricebook;
                        }
                        if ($module_name == 'opportunity') {
                            $request_filters['PriceBook2Id'] = $pricebook;
                        }
                       
                    }
                    // Add contact name when he comment anything
                    if ($module_name == 'CaseComment' && isset($request['CommentBody'])) {
                        $request_filters['CommentBody'] = sanitize_textarea_field($request['CommentBody']);
                        $request_filters['ParentId'] = $request['ParentId'];
                        $request_filters['IsPublished'] = TRUE;
                        $request_filters['CommentBody'] .= "\n\n###By {$_SESSION['bcp_contact_name']}";
                        
                        if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
                            $relationship_field_key = $_SESSION['bcp_module_access_data']->case->accountRelationship;
                            if($module_name == "account"){
                                $relationship_field_key = $_SESSION['bcp_module_access_data']->case->contactRelationship;                             
                            }
                        }else{
                            $relationship_field_key = $_SESSION['bcp_module_access_data']->case->contactRelationship ;
                        }
                    } else if ( $module_name == 'attachment' ) {
                        // @author: Dharti Gajera. Added attachment data.
                        if ( isset( $_FILES['Body'] ) ) {
                            $case_file = base64_encode( file_get_contents( $_FILES['Body']['tmp_name'] ) );
                            $request_filters['Name'] = $_FILES['Body']['name'];
                            $request_filters['Body'] = $case_file;
                            $request_filters['ContentType'] = $_FILES['Body']['type'];
                        }
                    } else {
                        // @author: Akshay Kungiri. Changed to add account when custom relationship set.
                        if ( ( $module_name == "account" && $relationship_field_key != "ContactId" ) || $module_name != "account") {
                            $request_filters[$relationship_field_key] = $contact_id;                 
                        }
                    }

                    if (!empty($relate_module_relationship) && !empty($relate_module_id)) {
                        $request_filters[$relate_module_relationship] = $relate_module_id;
                    }
                   
                   
                    //Get Detail Layout----
                    $view_layout = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts WHERE module='$module_name' AND view='detail' AND role='$bcp_portal_role'");
                    $field_definition_result = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_fields WHERE module='$module_name'");
                    $layout_data = isset($field_definition_result->fields) ? unserialize($field_definition_result->fields) : array();
                    $layout_fields = ( isset($view_layout->fields) ? ( (array) json_decode($view_layout->fields) ) : array() );
                    $layout_fields = array_keys($layout_fields);
                    if (isset($layout_fields['AccountId'])) {
                        $layout_fields['Account.name'] = "Account Name";
                    }
                  
                    $data_fields = array();
                    if ($layout_data != null) {
                        $tmp_layout_fields = array();
                        foreach ($layout_data as $layout_data_key => $layout_data_value) {
                            if(in_array($layout_data_key,$layout_fields)){
                                $ignore_field = false;
                                $field_name = $layout_data_value->name;
                                $tmp_layout_fields[$field_name] = $layout_data_value;
                                if ($layout_data_value->type == "reference" && $layout_data_value->relationshipName != "Parent") {
                                    $ref_object_name = $layout_data_value->referenceTo[0];
                                    $ref_field_name = "Name";
                                    if ($ref_object_name == 'Case') {
                                        $ref_field_name = 'CaseNumber';
                                    }
                                    $relate_module_table = $layout_data_value->relationshipName;
                                    $field_name = $relate_module_table . "." . $ref_field_name;
                                    array_push($data_fields, $field_name);
                                    $ignore_field = true;
                                }
                                if (!$ignore_field && in_array($field_name, $layout_fields)) {
                                    array_push($data_fields, $field_name);
                                }
                            }
                        }
                    }
                    //End Of Layout-------
                    //Call To Insert or Update Records
                    $view_fields = $data_fields;
                    $others = array();
                    $others['cid'] = $contact_id;

                    $relate_to_module = ( $parentmodule != "" ) ? $parentmodule : "Contact";
                    $relate_id = ( $parentId != "" ) ? $parentId : $contact_id;
                    if (empty($id) && $module_name != "contentnote" && in_array($module_name, $this->different_name_modules)) { // Attached note add
                        
                        if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
                            $relationship_field_key = ( $parentmodule != "" ) ? $_SESSION['bcp_module_access_data']->$parentmodule->accountRelationship : $_SESSION['bcp_module_access_data']->case->accountRelationship;

                           
                            if($module_name == "account"){
                                $relationship_field_key = ( $parentmodule != "" ) ? $_SESSION['bcp_module_access_data']->$parentmodule->contactRelationship : $_SESSION['bcp_module_access_data']->case->contactRelationship;                             
                            }
                        }else{
                            $relationship_field_key = ( $parentmodule != "" ) ? $_SESSION['bcp_module_access_data']->$parentmodule->contactRelationship : $_SESSION['bcp_module_access_data']->case->contactRelationship ;
                        }
                        

                        if ($module_name == "contentdocument" && isset($_FILES['Attachment'])) {
                            $case_file = base64_encode(file_get_contents($_FILES['Attachment']['tmp_name']));
                            $file_name = str_replace(' ', '-', $_FILES['Attachment']['name']);
                            $title = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_name);
                            $request_filters['title'] = sanitize_text_field($title);
                            $request_filters['filedata'] = $case_file;
                            $request_filters['pathonclient'] = $file_name;
                            $request_filters['description'] = sanitize_textarea_field($_REQUEST['Description']);
                            unset($request_filters['Description']);
                        }
                        
                        if ($module_name == "attachment" ) {
                            $ParentIds = ( $relate_module_id != "" ) ? $relate_module_id : $contact_id;
                            $arguments = $request_filters;
                            $arguments['ParentId'] = $ParentIds;
                       
                            $case = $objCp->insertRecord($module_name,$arguments);
                        } else {
                            $arguments = array(
                                "cid" => $contact_id,
                                "fields" => $request_filters,
                                "module_name" => $module_name,
                                "for_module" => $relate_to_module,
                                "relationship_field" => $relationship_field_key,
                                "relate_id" => $relate_id,
                            );
                            if ($relate_module_id !== "" && $relate_module_id != "") {
                                $arguments['for_module'] = $relate_module_name;
                                $arguments['relationship_field'] = $relate_module_relationship;
                                $arguments['relate_id'] = $relate_module_id;
                            }
                           
                            $case = $objCp->getPortalAttachmentAdd($arguments);
                        }
                    } else {
                        // Remove tasksubtype because it it note editable once added.
                        if ((isset($_REQUEST['Id']) && $_REQUEST['Id'] != '') && $module_name == "call" || $module_name == "task") {
                            unset($request_filters['tasksubtype']);
                        }
                        if ($module_name === 'contentnote') {
                            $relate_id = ( $relate_module_id != "" ) ? $relate_module_id : $relate_id;
                            
                            if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
                                $relationship_field_key = ( $parentmodule != "" ) ? $_SESSION['bcp_module_access_data']->$parentmodule->accountRelationship : $_SESSION['bcp_module_access_data']->case->accountRelationship;
    
                               
                                if($module_name == "account"){
                                    $relationship_field_key = ( $parentmodule != "" ) ? $_SESSION['bcp_module_access_data']->$parentmodule->contactRelationship : $_SESSION['bcp_module_access_data']->case->contactRelationship;                             
                                }
                            }else{
                                $relationship_field_key = ( $parentmodule != "" ) ? $_SESSION['bcp_module_access_data']->$parentmodule->contactRelationship : $_SESSION['bcp_module_access_data']->case->contactRelationship ;
                            }
                            $request_filters = array(
                                'title' => $_POST['Title'],
                                'content' => $_POST['Content'],
                            );
                            $others['id'] = $id;
                            $others['relateid'] = $relate_id;
                            $others['for_module'] = $relate_to_module;
                            $others['data'] = $request_filters;
                            $others["relationship_field"] = $relationship_field_key;
                            $arguments = array(
                                "module_name" => $module_name,
                                "fields" => array(),
                                "view_layout_fields" => array("Title", "TextPreview", "FileExtension", "FileType", "Content", "ContentSize", "IsReadOnly", "SharingPrivacy", "LastViewedDate", "CreatedDate"),
                                "others" => $others,
                            );
                        } else {
                            if( $_SESSION['bcp_relationship_type'] == "Account-Based" && $module_name != "CaseComment" && $module_name != "account" && $module_name != "contact"){
                                $contactRelationship = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                                $request_filters[$contactRelationship] = $_SESSION['bcp_contact_id'];
                            }
                            if($module_name == "opportunity"){
                                $request_filters['ContactId'] = $_SESSION['bcp_contact_id'];
                            }
                           /* if ($relate_module_id !== "" && $relate_module_id != "" && $relate_module_name != "") {
                                $relationship_field_key = $relate_module_relationship ; 
                                $others['cid'] = $relate_module_id;
                            }*/
                            
                            $others['relationship_field'] = $relationship_field_key;
                            $arguments = array(
                                "module_name" => $tmp_module_name,
                                "fields" => $request_filters,
                                "view_layout_fields" => $view_fields,
                                "id" => $id,
                                "others" => $others,
                            );
                        }
                        $case = $objCp->getPortalUpdateData($arguments); 
                    }
                    //-------------------------
                    //Check Status and return response
                    if (isset($case->success) && $case->success) {

                        if ($module_name == "contact") {
                            if (isset($request_filters['LastName'])) {
                                $_SESSION['bcp_contact_name'] = isset($request_filters['FirstName']) ? $request_filters['FirstName'] . " " . $request_filters['LastName'] : $request_filters['LastName'];
                                $response['bcp_contact_name'] = isset($request_filters['FirstName']) ? $request_filters['FirstName'] . " " . $request_filters['LastName'] : $request_filters['LastName'];
                            }
                            $response['success'] = TRUE;
                            $response['msg'] = __("Profile updated successfully.", 'bcp_portal');
                        } else {

                            if (isset($case->data->Id)) {
                                $id = $case->data->Id;
                            }
                            if (isset($case->data->id)) {
                                $id = $case->data->id;
                            }
                            if (isset($case->id)) {
                                $id = $case->id;
                            }

                            if (!empty($relate_module_relationship) && !empty($relate_module_id)) {
                                $redirect_url = home_url("/customer-portal/" . $relate_module_name . "/detail/" . $relate_module_id);
                                $segregate_detail_page = $this->bcp_helper->get_segregate_url($relate_module_name, 'bcp_detail');
                            } else {
                                $redirect_url = home_url("/customer-portal/" . $module_name . "/detail/" . $id);
                                $segregate_detail_page = $this->bcp_helper->get_segregate_url($module_name, 'bcp_detail');
                            }

                            $segregate_detail_page_exists = $segregate_detail_page['page_exists'];
                            if ($segregate_detail_page_exists && ( isset($segregate_detail_page['page_url']) && $segregate_detail_page['page_url'] != '' )) {
                                if (!empty($relate_module_relationship) && !empty($relate_module_id)) {
                                    $redirect_url = $segregate_detail_page['page_url'] . $relate_module_id;
                                } else {
                                    $redirect_url = $segregate_detail_page['page_url'] . $id;
                                }
                            }
                            
                            if (!empty($relate_module_relationship) && !empty($relate_module_id)) {
                                $redirect_url.="?subtab=".ucfirst($module_name)."-".$relate_module_relationship;
                            }

                            if ($module_name == "account") {
                                $_SESSION['bcp_account_id'] = $id;
                                $AccountFilters['AccountId'] = $id;
                                $arguments = array(
                                    "module_name" => "Contact",
                                    "fields" => $AccountFilters,
                                    "view_layout_fields" => array(),
                                    "id" => $contact_id,
                                    "others" => $others,
                                );
                                $AccountCase = $objCp->getPortalUpdateData($arguments);
                            }

                            /*
                             * Add Products Call to the Order and Opportunity
                             */
                            
                            if (in_array($module_name, $this->modules_with_products)) {
                              
                                $selected_products = $_REQUEST['selected-product'];
                               
                                $existing_product_ids = isset($_REQUEST['selected_product_ids']) ? json_decode(stripslashes($_REQUEST['selected_product_ids']), 1) : array();
                                if (isset($selected_products) && is_array($selected_products) && count($selected_products) > 0) {
                                    $item_arguments = array();

                                    $module_sub_key = '';
                                    if ($module_name == 'order') {
                                        $tmp_module_name = 'orderitem';
                                        $module_sub_key = 'OrderId';
                                    }
                                    if ($module_name == 'opportunity') {
                                        $tmp_module_name = 'OpportunityLineItem';
                                        $module_sub_key = 'OpportunityId';
                                    }
                                    
                                    $order_product_fields['PriceBook2Id'] = $pricebook;
                                    $order_product_fields['Products'] = array();                                    
                                    // Add unchecked existing ids to remove from product line items. @author: Akshay Kungiri.
                                    $existing_product_selected = array();
                                    foreach ($existing_product_ids as $existing_product_id) {
                                        if (!in_array($existing_product_id, $selected_products)) {
                                            array_push($existing_product_selected, $existing_product_id);
                                        }
                                    }
                                    if (!empty($existing_product_selected)) { // Delete Product Line Items which has not selected.
                                        $res = $objCp->deleteMultipleRecords($existing_product_selected);
                                    }
                                    //-----------------
                                    
                                    $updateProducts = array();
                                    $insertProducts = array();
                              
                                    $selectedProductLineItems = $_REQUEST['selectedProductLineItems'];
                                    $product_line_items = $_REQUEST['productLineItems'];
                                    
                                       
                                    foreach ($selected_products as $p_index => $product_id) {
                                        
                                        if (array_key_exists($product_id, (array)$selectedProductLineItems)) {
                                            $tmp_qty = $selectedProductLineItems[$product_id]['Quantity'];
                                            $tmp_price = $selectedProductLineItems[$product_id]['ProductPrice'];
                                            $order_item_id = $product_id;
                                            // @author: Akshay Kungiri. Change: Update Line Items in edit api call. Pass Id for both opportunity and order.
                                            $updateProducts['id'][$p_index] = trim($order_item_id);
                                            $updateProducts['Products'][$p_index] = array(
                                                'Quantity'  => $tmp_qty,
                                                'UnitPrice' => $tmp_price, // @author: Akshay Kungiri. Change: Pass UnitPrice to update Total Price.
                                            );
                                        }

                                        if (array_key_exists($product_id, $product_line_items)) {
                                           
                                          
                                            $tmp_qty = $product_line_items[$product_id]['Quantity'];
                                            $tmp_product_price = $product_line_items[$product_id]['ProductPrice'];
                                           
                                            $PriceBookEntryId = $product_line_items[$product_id]['PriceBookEntryId'];
                                            $order_item_id = $product_line_items[$product_id]['Id'];
                                            $insertProducts['Products'][$p_index] = array(
                                                $module_sub_key => $id,
                                                'Product2Id' => $product_id,
                                                'Quantity' => $tmp_qty,
                                                'UnitPrice' => $tmp_product_price,
                                                'PriceBookEntryId' => $PriceBookEntryId,
                                            );
                                        }
                                    }
                                  
                                    $item_arguments['module_name'] = $tmp_module_name;
                                    $item_arguments['view_layout_fields'] = array("Id", "Product2Id", "Product2.Name", "Product2.ProductCode", "Product2.Description", "Product2.Family", "Product2.DisplayURL", "Product2.CreatedDate", "UnitPrice", "Quantity", "TotalPrice");
                                    $item_arguments['others'] = $others;
                                    $order_products_info = array();
                                    $item_index = 0;
                                    if (!empty($updateProducts)) {
                                        $products = $updateProducts['Products'];
                                        sort($products);
                                        $item_arguments['fields']['Products'] = $products;
                                        $item_arguments['id'] = $updateProducts['id'];
                                       
                                        $order_products = $objCp->getPortalUpdateData($item_arguments);
                                       
                                        if (isset($order_products->success) && $order_products->success) {
                                            if (isset($order_products->data) && $order_products->data != '') {
                                                $order_products = json_decode($order_products->data);
                                                foreach ($order_products as $order_product_data) {
                                                    if(!isset($order_products_info[$item_index])){
                                                        $order_products_info[$item_index] = (object)$order_products_info[$item_index];
                                                    }
                                                    $order_products_info[$item_index]->Id = $order_product_data->Id;
                                                    $order_products_info[$item_index]->OrderId = $id;
                                                    $order_products_info[$item_index]->UnitPrice = $order_product_data->UnitPrice;
                                                    $order_products_info[$item_index]->TotalPrice = $order_product_data->TotalPrice;
                                                    $order_products_info[$item_index]->Quantity = $order_product_data->Quantity;
                                                    $order_products_info[$item_index]->Product2Id = $order_product_data->Product2Id;
                                                    $order_products_info[$item_index]->Name = $order_product_data->Product2->Name;
                                                    $order_products_info[$item_index]->ProductCode = $order_product_data->Product2->ProductCode;
                                                    $item_index++;
                                                }
                                            }
                                        }
                                        unset($item_arguments['id']);
                                    }
                                    if (!empty($insertProducts)) {
                                        $products = $insertProducts['Products'];
                                        sort($products);
                                        $item_arguments['fields']['Products'] = $products;
                                        
                                        $order_products = $objCp->getPortalUpdateData($item_arguments);
                                       
                                        if (isset($order_products->success) && $order_products->success) {
                                            /**Remove products cookie */
                                            unset($_COOKIE['pselected_module']); 
                                            setcookie('pselected_module', null, -1, '/'); 
                                            unset($_COOKIE['pselected_products']); 
                                            setcookie('pselected_products', null, -1, '/'); 
                                            unset($_COOKIE['pselected_pricebook']); 
                                            setcookie('pselected_pricebook', null, -1, '/'); 
                                            if (isset($order_products->data) && $order_products->data != '') {
                                                $order_products = json_decode($order_products->data);
                                                foreach ($order_products as $order_product_data) {
                                                    if(!isset($order_products_info[$item_index])){
                                                        $order_products_info[$item_index] = (object)$order_products_info[$item_index];
                                                    }
                                                    $order_products_info[$item_index]->Id = $order_product_data->Id;
                                                    $order_products_info[$item_index]->OrderId = $id;
                                                    $order_products_info[$item_index]->UnitPrice = $order_product_data->UnitPrice;
                                                    $order_products_info[$item_index]->TotalPrice = $order_product_data->TotalPrice;
                                                    $order_products_info[$item_index]->Quantity = $order_product_data->Quantity;
                                                    $order_products_info[$item_index]->Product2Id = $order_product_data->Product2Id;
                                                    $order_products_info[$item_index]->Name = $order_product_data->Product2->Name;
                                                    $order_products_info[$item_index]->ProductCode = $order_product_data->Product2->ProductCode;
                                                    $item_index++;
                                                }
                                            }
                                        }
                                    }
                                    $case->products = $order_products_info;
                                }
                            }
                         
                            //Add Attachment When Case Add-------
                            if ($module_name == "case" && isset($_FILES['Attachment']) && $_FILES['Attachment']['name'] != null) {
                                $attchmendData = $_FILES['Attachment'];
                                $case_file = base64_encode(file_get_contents($attchmendData['tmp_name']));
                                $file_name = str_replace(' ', '-', $attchmendData['name']);
                                $pathInfo = pathinfo($file_name);
                                $extension = isset($pathInfo['extension']) ? $pathInfo['extension'] : "";
                                $title = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_name);
                                $request_filters_attach = array();
                                $request_filters_attach['title'] = sanitize_text_field($title);
                                $request_filters_attach['filedata'] = $case_file;
                                $request_filters_attach['pathonclient'] = $file_name;
                                $request_filters_attach['description'] = $case->data->Description ? $case->data->Description : $file_name;
                                
                                $arguments = array(
                                    "cid" => $contact_id,
                                    "module_name" => "case",
                                    "relate_id" => $id,
                                    "fields" => $request_filters_attach,
                                );
                                $case_attach = $objCp->getPortalAttachmentAdd($arguments);
                                $case_attach->data->fileextension = $extension;
                                $case_attach->data->Id = $case_attach->data->id;
                                $case->attachments[0] = $case_attach->data;
                            }
                            //END -- Add Attachment When Case Add-----------

                            if (in_array($module_name, $this->different_name_modules)) {
                                if(!isset($case->data)){
                                    $case->data = (object) array();
                                }
                                $case->data->Id = $id;
                                if (isset($file_name) && $file_name) {
                                    $pathInfo = pathinfo($file_name);
                                    $extension = isset($pathInfo['extension']) ? $pathInfo['extension'] : "";
                                    if ($extension) {
                                        $case->data->fileextension = $extension;
                                    }
                                }

                                $response['record'] = $case;
                                $response['parent_module'] = $parentmodule;
                                $response['redirect_url'] = $redirect_url;
                            }
                            
                            $response['success'] = TRUE;
                            if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {
                                $response['msg'] = sprintf(__('%s updated successfully.', 'bcp_portal'), __($bcp_modules[$module_name]['singular'], 'bcp_portal'));
                            } else {
                                $response['msg'] = sprintf(__('%s added successfully.', 'bcp_portal'), __($bcp_modules[$module_name]['singular'], 'bcp_portal'));
                            }
                            if ($module_name != "CaseComment" && $module_name != "attachment" && ( $module_name != "contentdocument" && $parentmodule != "case" )) {
                                setcookie("record_msg", $response['msg'], time() + 3600, "/", "", '', true);
                                $response['redirect_url'] = $redirect_url;
                                //$_SESSION['record'] = $case;
                                $_SESSION['record_msg'] = __($response['msg'], 'bcp_portal');
                            }
                            if ($module_name != "CaseComment" && $module_name != "attachment" && ( $module_name != "contentdocument" && $parentmodule == "case" )) {
                                setcookie("record_msg", $response['msg'], time() + 3600, "/", "", '', true);
                                $response['redirect_url'] = $redirect_url;
                                //$_SESSION['record'] = $case;
                                $_SESSION['record_msg'] = __($response['msg'], 'bcp_portal');
                            }

                            if (!empty($relate_module_relationship) && !empty($relate_module_id)) {
                                unset($_SESSION['record']);
                            }
                        }
                    } else {
                        if ( isset($case->errorCode) && $case->errorCode == 'ENTITY_IS_DELETED' ) {
                            $_SESSION['bcp_error_message'] = $case->response->message;
                            unset($_SESSION['bcp_contact_id']);
                        } else if ( is_array($case) && isset($case[0]->errorCode) && isset($case[0]->message) ) {
                            $response['success'] = FALSE;
                            $message = ucfirst($case[0]->message);
                            $response['msg'] = __($message, 'bcp_portal');
                            $response['redirect_url'] = "";
                        } else {
                            $response['success'] = FALSE;
                            $response['msg'] = isset($case->message) ? $case->message : __("There is some error occuered while adding data. Please contact to administrator", 'bcp_portal');
                            $response['redirect_url'] = "";
                        }
                    }
                    //-------------------------------
                }
            } else {
                $response['redirect_url'] = "";
                $response['success'] = FALSE;
                $response['msg'] = 1;
                $response['msg_fields'] = $bcp_error_msg;
            }
        } else {
            $response['redirect_url'] = "";
            $response['success'] = FALSE;
            $response['msg'] = __("Please set layout.", 'bcp_portal');
        }
        echo json_encode($response);
        wp_die();
    }

    /**
     * Case notes get
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_case_notes_callback() {

        global $objCp;
        if (isset($_SESSION['record'])) {
            unset($_SESSION['record']);
        }
        $record = $_REQUEST['record'];
        $attachment_detail = $record['data'];
        $title = htmlentities($attachment_detail['Title']);
        $comment_body = nl2br(htmlentities(stripslashes_deep(base64_decode($attachment_detail['Content']))));
        $auth_names = explode(" ", $title);
        $Auth_letter = strtoupper($auth_names[0])[0];
        $Auth_letter .= (isset($auth_names[1])) ? strtoupper($auth_names[1])[0] : "";
        $action_class = new BCPCustomerPortalActions($this->plugin_name, $this->version);
        ?>

        <div class="user-details-1">
            <div class="bcp-flex bcp-justify-content-between user-details-information">
                <div class="user bcp-flex bcp-align-center">
                    <div class="profile-pic"><?php echo $Auth_letter; ?></div>
                    <div class="user-name"><?php echo $title; ?></div>
                </div>
                <div class="post-date">
                    <p><?php echo $action_class->bcp_show_date($record->CreatedDate); ?></p>
                </div>
            </div>
            <div class="user-description">
                <p><?php echo html_entity_decode($comment_body); ?></p>
            </div>
        </div>
        <?php
        wp_die();
    }

    /**
     * Case comments listing
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_case_comments_callback() {

        global $objCp;

        $where = array(
            'ParentId' => $_REQUEST['record_id'],
        );

        $fields = array(
            'CommentBody' => 'CommentBody',
            'CreatedDate' => 'CreatedDate',
            'ParentId' => 'string',
            'IsPublished' => "Boolean",
            'CreatedBy.name' => 'CreatedBy.name',
        );

        $fields = json_encode($fields);
        $argemunts = array(
            "module" => "CaseComment",
            "where" => $where,
            "fields" => $fields,
        );
        $records = $objCp->getModuleRecords($argemunts);
        if (isset($records->records) && $records->records != null) {
            $comments = $records->records;
            if ($_REQUEST['view_style'] == 'style_1') {
                $style = 'casecomments_1';
            } else if ($_REQUEST['view_style'] == 'style_2') {
                $style = 'casecomments_2';
            }

            $detail_template = '/detail/' . $style . '.php';
            $data = array(
                'comments' => $comments,
                'record_id' => $_REQUEST['record_id'],
                'action_class' => new BCPCustomerPortalActions($this->plugin_name, $this->version)
            );
            $this->template->render($detail_template, $data);
        }
        wp_die();
    }

    /**
     * List case attachment
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_case_attachments_callback() {
        global $objCp;
        if (isset($_SESSION['record'])) {
            unset($_SESSION['record']);
        }
        $record = $_REQUEST['record'];
        $attachment_detail = $record['data'];
        $desc = nl2br(htmlentities(stripslashes_deep($attachment_detail['description'])));
        ?>
        <div class="user-details-1">
            <div class="bcp-flex bcp-justify-content-between user-details-information">
                <div class="user d-flex align-items-center">
                    <div class="attachment-name"><?php echo $attachment_detail['title']; ?></div>
                </div>
                <div class="post-date">
                    <a title="<?php _e('Preview', 'bcp_portal'); ?>" href="<?php echo admin_url('admin-ajax.php'); ?>?action=bcp_attachment_download&record_id=<?php echo $attachment_detail['id']; ?>&title=<?php echo $attachment_detail['title']; ?>&type=<?php echo $attachment_detail['fileextension']; ?>&bcp_file_preview=1" class="scp-caseattachment-btn" target="_blank"><i class="fa fa-eye preview-cstm" aria-hidden="true"></i> </a>
                    &nbsp;&nbsp;
                    <a title="<?php _e('Download', 'bcp_portal'); ?>" href="<?php echo admin_url('admin-ajax.php'); ?>?action=bcp_attachment_download&record_id=<?php echo $attachment_detail['id']; ?>&title=<?php echo $attachment_detail['title']; ?>&type=<?php echo $attachment_detail['fileextension']; ?>" class="attachment-download general-link-btn scp-download-btn scp-caseattachment-btn"><i class="fa fa-download" aria-hidden="true"></i></a>
                </div>
            </div>
        <?php
        if (strlen($desc) > 100) {

            $first_half = substr($desc, 0, 100);
            $remain_half = substr($desc, 101, strlen($desc));
            ?>
                <span class="quote ellipses-quote"><?php echo $first_half; ?>
                    <span class="moreellipses" style="display: inline;">...&nbsp;</span>
                    <span class="morecontent">
                        <span style="display: none;"><?php echo $remain_half; ?></span>&nbsp;&nbsp;
                        <a href="javascript:void(0);" onclick="read_more_less(this)" class="scp-caseattachment-font readmorelink" title="<?php _e('Read more', 'bcp_portal'); ?>"><?php _e('Read more', 'bcp_portal'); ?></a>
                    </span>
                </span>
                <input type="hidden" value="<?php _e('Read More', 'bcp_portal'); ?>" class="bcp_crm_read_more_text" />
                <input type="hidden" value="<?php _e('Read Less', 'bcp_portal'); ?>" class="bcp_crm_read_less_text" />
                <br />
            <?php } else { ?>
                <span class="description"><?php echo $desc; ?></span>
                <?php } ?>                            
        </div>
            <?php
            wp_die();
        }

        /**
         * Download case attachment
         *
         * @since 4.0.0
         * @return void
         * @author Akshay Kungiri 
         */
        public function bcp_attachment_download_callback() {
            global $objCp;
            $record_id = $_REQUEST['record_id'];
            $bcp_file_preview = isset($_REQUEST['bcp_file_preview']) ? $_REQUEST['bcp_file_preview'] : '0';

            $this->check_pemission('contentdocument', 'access', $record_id);

            if ($record_id != '' || $record_id != NULL) {

                $attachment_detail = $objCp->getRecord( 'Attachment', $record_id,'','' );
                if ( isset( $attachment_detail->attributes ) ) {
                $file_name = str_replace( ' ', '-', $attachment_detail->Name);
                $file_mime_type = $this->bcp_mime_content_type(basename($file_name));
                $file_content = $objCp->getRecord( 'Attachment', $record_id, '', 'body' );
                } else {
                    $attachment_detail = $objCp->getRecord( 'ContentDocument', $record_id, '', '' );
                    $file_name = str_replace( ' ', '-', $attachment_detail->Title).'.'.$attachment_detail->FileExtension;
                    $file_mime_type = $this->bcp_mime_content_type(basename($file_name));
                    $file_content = $objCp->customCall( "/services/data/v35.0/chatter/files/$record_id/content", '', '', 0 );
                }

                if ($file_content) {
                    ob_clean();
                    header("Content-type: $file_mime_type");
                    if ($bcp_file_preview == '1') {
                        header('Content-Disposition: inline; filename=' . basename($file_name));
                    } else {
                        header('Content-Disposition: attachment; filename=' . basename($file_name));
                    }

                    header('Content-Description: File Transfer');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    echo $file_content;
                }
            }
            wp_die();
        }

        /**
         * Return file mime type
         *
         * @since 4.0.0
         * @param [type] $file_name
         * @return void
         * @author Akshay Kungiri 
         */
        public function bcp_mime_content_type($file_name) {

            $all_mime_types = $this->generateUpToDateMimeArray();
            $extenstion = pathinfo($file_name, PATHINFO_EXTENSION);
            $file_mime_type = '';

            if (isset($all_mime_types[$extenstion]) && $all_mime_types[$extenstion] != NULL) {
                $file_mime_type = $all_mime_types[$extenstion];
            }

            return $file_mime_type;
        }

        /**
         * Get updated mime types supported by apache
         *
         * @since 4.0.0
         * @return void
         * @author Akshay Kungiri 
         */
        public function generateUpToDateMimeArray() {

            $url = 'http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types';
            $s = array();

            foreach (@explode("\n", @file_get_contents($url))as $x) {

                if (isset($x[0]) && $x[0] !== '#' && preg_match_all('#([^\s]+)#', $x, $out) && isset($out[1]) && ( $c = count($out[1]) ) > 1) {

                    for ($i = 1; $i < $c; $i++) {

                        $s[$out[1][$i]] = $out[1][0];
                    }
                }
            }
            return !empty($s) ? $s : false;
        }

        /**
         * When user search in portal
         *
         * @since 4.0.0
         * @return void
         * @author Akshay Kungiri 
         */
        public function bcp_searchresult_callback() {
            global $objCp, $KBLists, $ContentNoteLists;
            $bcp_field_prefix_org = fetch_data_option('bcp_org_prefix');
            $theme_color = fetch_data_option('bcp_theme_color');
            if ($bcp_field_prefix_org != '')
                $bcp_field_prefix_org = rtrim($bcp_field_prefix_org, '_') . '__';

            $contact_id = $_SESSION['bcp_contact_id'];
            $module_access_data = $_SESSION['bcp_module_access_data'];
            $search_term = trim(isset($_REQUEST['serch_term']) ? $_REQUEST['serch_term'] : '');
            $search_term = urldecode($search_term);
            $search_term = htmlentities($search_term);
            $KBLists = array();
            $ContentNoteLists = array();

            $module_fields = array(
                'case' => array(
                    'retriveFields' => array(
                        0 => 'Id',
                        1 => 'CreatedDate',
                        2 => 'Subject',
                        3 => 'CaseNumber',
                    ),
                    'searchFields' => array(
                        0 => 'Subject',
                        1 => 'CaseNumber',
                    ),
                ),
                'contract' => array(
                    'retriveFields' => array(
                        0 => 'Id',
                        1 => 'CreatedDate',
                        2 => 'ContractNumber',
                    ),
                    'searchFields' => array(
                        0 => 'ContractNumber',
                        1 => 'Name'
                    ),
                ),
                'order' => array(
                    'retriveFields' => array(
                        0 => 'Id',
                        1 => 'CreatedDate',
                        2 => 'OrderNumber',
                    ),
                    'searchFields' => array(
                        0 => 'OrderNumber',
                        1 => 'Name'
                    ),
                ),
                'call' => array(
                    'retriveFields' => array(
                        0 => 'Id',
                        1 => 'CreatedDate',
                        2 => 'Subject',
                        3 => 'Status'
                    ),
                    'searchFields' => array(
                        0 => 'Subject',
                        1 => 'Status',
                    ),
                ),
                'contentdocument' => array(
                    'retriveFields' => array(
                        0 => 'ContentDocumentId',
                        1 => 'ContentDocumentCreatedDate',
                        2 => 'ContentDocument.Title',
                    ),
                    'searchFields' => array(
                        0 => 'ContentDocument.Title',
                    ),
                ),
                'event' => array(
                    'retriveFields' => array(
                        0 => 'Id',
                        1 => 'CreatedDate',
                        2 => 'Subject',
                    ),
                    'searchFields' => array(
                        0 => 'Subject',
                    ),
                ),
                'opportunity' => array(
                    'retriveFields' => array(
                        0 => 'Id',
                        1 => 'CreatedDate',
                        2 => 'Name',
                    ),
                    'searchFields' => array(
                        0 => 'Name',
                    ),
                ),
                'task' => array(
                    'retriveFields' => array(
                        0 => 'Id',
                        1 => 'CreatedDate',
                        2 => 'Subject',
                        3 => 'Status'
                    ),
                    'searchFields' => array(
                        0 => 'Subject',
                        1 => 'Status',
                    ),
                ),
                'workorder' => array(
                    'retriveFields' => array(
                        0 => 'Id',
                        1 => 'CreatedDate',
                        2 => 'WorkOrderNumber',
                    ),
                    'searchFields' => array(
                        0 => 'WorkOrderNumber',
                    ),
                ),
                'returnorder' => array(
                    'retriveFields' => array(
                        0 => 'Id',
                        1 => 'CreatedDate',
                        2 => 'ReturnOrderNumber',
                    ),
                    'searchFields' => array(
                        0 => 'ReturnOrderNumber',
                    ),
                ),
            );

            $module_data = array();

            $default_retrive_fields = array(
                "Id",
                "CreatedDate",
                "Name"
            );

            $default_search_fields = array(
                "Id",
                "Name"
            );
            
            foreach ($module_access_data as $mkey => $mvalue) {
                if ($mkey == "account" || $mkey == "knowledgearticleversion" || $mkey == "solution" || $mkey == "contentnote") {
                    continue;
                }
                $retrive_fields = isset($module_fields[$mkey]['retriveFields']) ? $module_fields[$mkey]['retriveFields'] : $default_retrive_fields;
                $search_fields = isset($module_fields[$mkey]['searchFields']) ? $module_fields[$mkey]['searchFields'] : $default_search_fields;
               

                if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
                    $relationship_field = ( isset($mvalue->accountRelationship) && $mvalue->accountRelationship != "No_Relationship" ) ? $mvalue->accountRelationship : "No_Relationship";
                    $contact_id = $_SESSION['bcp_account_id'];
                    if($module_name == "account"){
                        $relationship_field = ( isset($mvalue->contactRelationship) && $mvalue->contactRelationship != "No_Relationship" ) ? $mvalue->contactRelationship : "No_Relationship";
                        $contact_id = $_SESSION['bcp_contact_id'];
                    }
                }else{
                    $relationship_field = ( isset($mvalue->contactRelationship) && $mvalue->contactRelationship != "No_Relationship" ) ? $mvalue->contactRelationship : "No_Relationship";
                    $contact_id = $_SESSION['bcp_contact_id'];
                }
                $module_data[$mkey] = array(
                    "retriveFields" => $retrive_fields,
                    "searchFields" => $search_fields,
                    "relationship_field" => $relationship_field,
                );
                if($relationship_field == ""){
                    unset($module_data[$mkey]);
                }
            }
            $arguments = array(
                "cid" => $contact_id,
                "searchVal" => $search_term,
                "data" => $module_data,
                "limit" => 5,
                "offset" => 0,
            );
            $search_result = $objCp->globalSearchDataCall($arguments);
            if (isset($module_access_data->knowledgearticleversion)) {
                $data_fields = array(
                    'Id',
                    'title',
                    'UrlName',
                    'ArticleNumber',
                    'KnowledgeArticleId',
                    'Summary',
                    'LastModifiedById'
                );
                $KBArguments = array(
                    "cid" => $contact_id,
                    "searchVal" => $search_term,
                    "searchfield" => "title",
                    "data" => $data_fields,
                    "limit" => 5,
                    "offset" => 0,
                );
                $KBLists = $objCp->getPortalKBListData($KBArguments);
            }

            if (isset($module_access_data->contentnote)) {
                $contentData = array(
                    'contentnote' => array(
                        'retriveFields' => array(
                            0 => 'ContentNoteId',
                            1 => 'ContentNote.CreatedDate',
                            2 => 'ContentNote.TextPreview',
                        ),
                        'searchFields' => array(
                            0 => 'ContentNote.TextPreview',
                        ),
                    ),
                );
                $ContentNoteArguments = array(
                    "cid" => $contact_id,
                    "searchVal" => $search_term,
                    "data" => $contentData,
                    "limit" => 5,
                    "offset" => 0,
                );
                $ContentNoteLists = $objCp->getContentNoteSearch($ContentNoteArguments);
                if (isset($ContentNoteLists->contentnote)) {
                    $search_result->contentnote = $ContentNoteLists->contentnote;
                }
            }

            if(get_option('bcp_theme_color')){
                $this->themecolor->theme_color_global_search_style();
            }

            $g_search_template = '/global-search/style1.php';
            $data = array(
                'search_term' => $search_term,
                'search_result' => $search_result,
                'KBLists' => $KBLists,
                'bcp_modules' => $_SESSION['bcp_modules'],
                'show_result' => true,
                'helper_class' => new BCPCustomerPortalHelper($this->plugin_name, $this->version),
                'action_class' => new BCPCustomerPortalActions($this->plugin_name, $this->version)
            );
            $this->template->render($g_search_template, $data);

            wp_die();
        }

        /**
         * Ajax pagination based on data
         *
         * @since 4.0.0
         * @param [type] $total_record
         * @param integer $per_page
         * @param integer $page
         * @param [type] $module
         * @param [type] $action
         * @param [type] $order_by
         * @param [type] $search
         * @return void
         * @author Akshay Kungiri 
         */
        public function bcp_pagination($total_record, $per_page = 10, $page = 1, $module, $action, $order_by, $search, $style = 'style_3') {
            if ($total_record > 2000) {
                $total_record = 2000;
            }
            $total = $total_record;
            $adjacents = '2';

            $page = ($page == 0 ? 1 : $page);

            $prev = $page - 1;
            $next = $page + 1;

            $lastpage = ceil($total / $per_page);

            $lpm1 = $lastpage - 1;

            $pagination = '';
            if ($lastpage > 1) {
                $pagination .= '<nav class="dcp-navigation">';

                $ul_class = '';
                if ($style == 'style_2')
                    $ul_class = 'justify-content-end';
                if ($style == 'style_3')
                    $ul_class = 'justify-content-center';

                $pagination .= '<ul class="' . $ul_class . '">';
                $prevClass = 'disable';
                if ($page > 1) {
                    $prevClass = 'bcp-action';
                }
                $pagination .= '<li class="page-item"><a class="page-link ' . $prevClass . '" paged="1" module-name="' . $module . '" module-action="' . $action . '" paged="' . $prev . '" order-by="' . $order_by . '" search="' . $search . '" title="Previous"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>';
                $pagination .= '<li class="page-item"><a class="page-link ' . $prevClass . ' page-prev" module-name="' . $module . '" module-action="' . $action . '" paged="' . $prev . '" order-by="' . $order_by . '" search="' . $search . '" title="Previous"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>';

                if ($lastpage < 7 + ($adjacents * 2)) {
                    for ($counter = 1; $counter <= $lastpage; $counter++) {
                        if ($counter == $page) {
                            $pagination .= '<li class="page-item"><a class="page-link bcp-action active"  title="' . $counter . '">' . $counter . '</a></li>';
                        } else {
                            $pagination .= '<li class="page-item"><a class="page-link bcp-action" module-name="' . $module . '" module-action="' . $action . '" paged="' . $counter . '" order-by="' . $order_by . '" search="' . $search . '"  title="' . $counter . '">' . $counter . '</a></li>';
                        }
                    }
                } elseif ($lastpage > 5 + ($adjacents * 2)) {

                    if ($page < 1 + ($adjacents * 2)) {

                        for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                            if ($counter == $page) {
                                $pagination .= '<li class="page-item"><a class="page-link bcp-action active" title="' . $counter . '">' . $counter . '></a></li>';
                            } else {
                                $pagination .= '<li class="page-item"><a class="page-link bcp-action" module-name="' . $module . '" module-action="' . $action . '" paged="' . $counter . '" order-by="' . $order_by . '" search="' . $search . '" title="' . $counter . '">' . $counter . '</a></li>';
                            }
                        }

                        $pagination .= '<li class="page-item dot">...</li>';
                        $pagination .= '<li class="page-item"><a class="page-link bcp-action" module-name="' . $module . '" module-action="' . $action . '" paged="' . $lpm1 . '" order-by="' . $order_by . '" search="' . $search . '" title="' . $lpm1 . '">' . $lpm1 . '</a></li>';
                        $pagination .= '<li class="page-item"><a class="page-link bcp-action" module-name="' . $module . '" module-action="' . $action . '" paged="' . $lastpage . '" order-by="' . $order_by . '" search="' . $search . '" title="' . $lastpage . '">' . $lastpage . '</a></li>';
                    } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {

                        $pagination .= '<li class="page-item"><a class="page-link bcp-action" module-name="' . $module . '" module-action="' . $action . '" paged="1" order-by="' . $order_by . '" search="' . $search . '" title="1">1</a></li>';
                        $pagination .= '<li class="page-item"><a class="page-link bcp-action" module-name="' . $module . '" module-action="' . $action . '" paged="2" order-by="' . $order_by . '" search="' . $search . '" title="2">2</a></li>';
                        $pagination .= '<li class="page-item dot">...</li>';

                        for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                            if ($counter == $page) {
                                $pagination .= '<li class="page-item"><a class="page-link bcp-action active" title="' . $counter . '">' . $counter . '</a></li>';
                            } else {
                                $pagination .= '<li class="page-item"><a class="page-link bcp-action" module-name="' . $module . '" module-action="' . $action . '" paged="' . $counter . '" order-by="' . $order_by . '" search="' . $search . '" title="' . $counter . '">' . $counter . '</a></li>';
                            }
                        }

                        $pagination .= '<li class="page-item dot">...</li>';
                        $pagination .= '<li class="page-item"><a class="page-link bcp-action" module-name="' . $module . '" module-action="' . $action . '" paged="' . $lpm1 . '" order-by="' . $order_by . '" search="' . $search . '" title="' . $lpm1 . '">' . $lpm1 . '</a></li>';
                        $pagination .= '<li class="page-item"><a class="page-link bcp-action" module-name="' . $module . '" module-action="' . $action . '" paged="' . $lastpage . '" order-by="' . $order_by . '" search="' . $search . '" title="' . $lastpage . '">' . $lastpage . '</a></li>';
                    } else {

                        $pagination .= '<li class="page-item"><a class="page-link bcp-action" module-name="' . $module . '" module-action="' . $action . '" paged="1" order-by="' . $order_by . '" search="' . $search . '" title="1">1</a></li>';
                        $pagination .= '<li class="page-item"><a class="page-link bcp-action" module-name="' . $module . '" module-action="' . $action . '" paged="2" order-by="' . $order_by . '" search="' . $search . '" title="2">2</a></li>';
                        $pagination .= '<li class="page-item dot">...</li>';

                        for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                            if ($counter == $page) {
                                $pagination .= '<li class="page-item"><a class="page-link bcp-action active" title="' . $counter . '">' . $counter . '</a></li>';
                            } else {
                                $pagination .= '<li class="page-item"><a class="page-link bcp-action" module-name="' . $module . '" module-action="' . $action . '" paged="' . $counter . '" order-by="' . $order_by . '" search="' . $search . '" title="' . $counter . '">' . $counter . '</a></li>';
                            }
                        }
                    }
                }
                $nextClass = 'disable';
                if ($page < $counter - 1) {
                    $nextClass = 'bcp-action';
                }
                $pagination .= '<li class="page-item"><a class="page-link ' . $nextClass . ' page-next" module-name="' . $module . '" module-action="' . $action . '" paged="' . $next . '" order-by="' . $order_by . '" search="' . $search . '" title="Next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>';
                $pagination .= '<li class="page-item"><a class="page-link ' . $nextClass . '" paged="' . ($counter - 1) . '" module-name="' . $module . '" module-action="' . $action . '" paged="' . $next . '" order-by="' . $order_by . '" search="' . $search . '" title="Next"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li>';

                $pagination .= '</ul>';
                $pagination .= '</nav>';
            }

            return $pagination;
        }

        /**
         * Entrypt string algorithm
         *
         * @param [type] $input
         * @return void
         * @author Akshay Kungiri 
         */
        public function bcp_encrypt_string($input) {

            $inputlen = strlen($input);
            $randkey = rand(1, 9);

            $i = 0;
            while ($i < $inputlen) {

                $inputchr[$i] = ( ord($input[$i]) - $randkey );
                $i++;
            }

            $encrypted = implode('.', $inputchr) . '.' . ( ord($randkey) + 50 );

            return $encrypted;
        }

        /**
         * Decrypt string algorithm
         *
         * @since 4.0.0
         * @param [type] $input
         * @return void
         * @author Akshay Kungiri 
         */
        public function bcp_decrypt_string($input) {

            $input_count = strlen($input);
            $real = '';

            $dec = explode(".", $input);
            $x = count($dec);
            $y = $x - 1;

            $calc = (int) $dec[$y] - 50;
            $randkey = chr($calc);

            $i = 0;

            while ($i < $y) {

                $array[$i] = $dec[$i] + $randkey;
                $real .= chr($array[$i]);

                $i++;
            };

            $input = $real;

            return $input;
        }

        /**
         * Check license every 30 min
         *
         * @since 4.0.0
         * @return void
         * @author Akshay Kungiri 
         */
        public function add_bcp_thirty_minutes_event() {
            $licence = $this->bcp_check_licence();
            if (isset($licence->suc) && $licence->suc) {
                update_option('bcp_licence', 1);
                update_option('bcp_licence_validate', date('Y-m-d'));
            } else {
                update_option('bcp_licence', 0);
                update_option('bcp_licence_validate', '');
            }
        }

        /**
         * Create cron schedule
         *
         * @since 4.0.0
         * @param [type] $schedules
         * @return void
         * @author Akshay Kungiri 
         */
        public function bcp_cron_schedules($schedules) {

            $schedules['every_thirty_minutes'] = array(
                'interval' => 86400,
                'display' => __('Every 24 Hours', 'bcp_portal'),
            );

            return $schedules;
        }

        /**
         * Check license for cron
         *
         * @since 4.0.0
         * @return void
         * @author Akshay Kungiri 
         */
        public function bcp_check_licence() {

            $licence_key = fetch_data_option('bcp_licence_key');
            $response = '';

            if ($licence_key != '') {

                $domains = array(
                    site_url(),
                );

                $domains = urlencode(implode(',', $domains));
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, sprintf('https://www.crmjetty.com/extension/licence.php'));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, 'key=' . urlencode($licence_key) . '&domains=' . $domains . '&sec=Salesforce-customer-portal-WordPress');
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); // Compliant; default value is TRUE
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // Compliant; default value is 2 to "check the existence of a common name and also verify that it matches the hostname provided
                $response = curl_exec($ch);
                curl_close($ch);

                $response = json_decode($response);
            }

            return $response;
        }

        /**
         * Convert date to UTC from Browser Timezone
         *
         * @since 4.0.0
         * @param [type] $date
         * @param string $Format
         * @return void
         * @author Akshay Kungiri 
         */
        public function bcp_convert_date($datetime, $Format = "") {

            $browser_timezone = $_SESSION['browser_timezone'];

            $date = new DateTime($datetime, new DateTimeZone($browser_timezone));
            $date->setTimeZone(new DateTimeZone("UTC"));

            $value = $date->format($Format);

            return $value;
        }

        /**
         * Show date in format based on browser timezone
         *
         * @since 4.0.0
         * @param [type] $date
         * @param string $showFormat
         * @return void
         * @author Akshay Kungiri 
         */
        public function bcp_show_date($date, $showFormat = "DateTime", $Format = "") {

            $crm_timezone = new DateTimeZone('UTC');
            $date = new DateTime($date, $crm_timezone);
            $date->setTimezone($crm_timezone);

            $browser_timezone = isset($_SESSION['browser_timezone']) ? $_SESSION['browser_timezone'] : "";

            if ($browser_timezone != "") {
                $newTZ2 = new DateTimeZone($browser_timezone);
                $date->setTimezone($newTZ2);
            }
            if ($Format == "") {
                $Format = fetch_data_option('date_format') . ' ' . fetch_data_option('time_format');
                if ($showFormat == "Date") {
                    $Format = fetch_data_option('date_format');
                }
                if ($showFormat == "Time") {
                    $Format = fetch_data_option('time_format');
                }
            }
            $value = $date->format($Format);

            return $value;
        }

        /**
         * Get solution data
         *
         * @since 1.0.0
         * @return void
         * @author Akshay Kungiri 
         */
        public function bcp_solutions() {

            $view = $_REQUEST['view'];
            $view_style = isset($_REQUEST['view_style']) ? $_REQUEST['view_style'] : 'style_1';
            $module_name = $_REQUEST['module_name'];
            $add_case_title = isset($_REQUEST['add_case_title']) ? $_REQUEST['add_case_title'] : __("Add", 'bcp_portal') . " " . __($_SESSION['bcp_modules']['case']['singular'], 'bcp_portal');
            $module_description = isset($_REQUEST['module_description']) ? $_REQUEST['module_description'] : __("You may find probable solution you're looking, just search it or", 'bcp_portal');
            $limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
            $limit = (int) $limit;

            $request_function = "";
            if ($view == 'search') {
                $request_function = 'bcp_' . $module_name . '_searchpage';
                echo $this->$request_function($view_style, $add_case_title, $module_description, $limit);
            } else if ($view == 'list') {
                $request_function = 'bcp_' . $module_name . '_list';
                $case_id = isset($_REQUEST['case_id']) ? $_REQUEST['case_id'] : "";
                $paged = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 0;
                $offset = ( ( $paged * $limit ) - $limit);
                $searchterm = isset($_REQUEST['search_term']) ? $_REQUEST['search_term'] : "";
                echo $this->$request_function($case_id, $paged, $offset, $searchterm, $limit);
            }

            wp_die();
        }

        /**
         * solution list
         *
         * @since 4.0.0
         * @param [type] $module_name
         * @param string $view
         * @param string $id
         * @return void
         * @author Akshay Kungiri 
         */
        function bcp_solutions_list($case_id = "", $paged, $offset, $search_term = "") {

            global $objCp, $case_record, $bcp_field_prefix;

            $content = '';
            ob_start();
            $bcp_authentication = fetch_data_option("bcp_authentication");  
            if (isset($bcp_authentication) && $bcp_authentication != null) {

                if (isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id'] != null) {
                    
                    $segregate_edit_page = $this->bcp_helper->get_segregate_url('case', 'bcp_add_edit');
                    $add_url = home_url("customer-portal/case/edit/");
                    if ($segregate_edit_page['page_exists'] && (isset($segregate_edit_page['page_url']) && $segregate_edit_page['page_url'] != '')) {
                        $add_url = $segregate_edit_page['page_url'];
                    }
                    $view_style = $_REQUEST['view_style'];
                    $limit = (int) ( fetch_data_option('bcp_records_per_page') ? fetch_data_option('bcp_records_per_page') : 10 );
                    $paged = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1;
                    $offset = ( ( $paged * $limit ) - $limit);
                    $total_records = 0;
                    $solutions = "";
                    $rating_data = array();
                    $cid = $_SESSION['bcp_contact_id'];
                    $like_field = $slike_field = $bcp_field_prefix . "Like__c";
                    $dislike_field = $sdislike_field = $bcp_field_prefix . "Dislike__c";
                    $sol_id_field = $bcp_field_prefix . "SolutionId__c";

                    if ($search_term != "") {

                        $data = array(
                            'title' => $search_term,
                            'limit' => $limit,
                            'offset' => $offset,
                            'cid' => $cid,
                        );
                        $responce = $objCp->search($data);
                        $total_records = $responce->totalcount;
                        $solutions = $responce->records[0];
                        if (isset($responce->ratingdata->data)) {
                            $rat_array = $responce->ratingdata->data;
                            foreach ($rat_array as $value) {
                                if ($value->$like_field != "" || $value->$dislike_field != "") {
                                    $sol_id = $value->$sol_id_field;
                                    $rating_data[$sol_id] = 0;
                                    if ($value->$like_field == 1) {
                                        $rating_data[$sol_id] = 1;
                                    }
                                }
                            }
                        }
                    } else {

                        $total_records = 0;
                        if (isset($case_record->solutions) && $case_record->solutions != "") {
                            $case_solutions = $case_record->solutions;
                            $like_field = "Like__c";
                            $dislike_field = "Dislike__c";
                            $solutions = $case_solutions->data;
                            $total_records = $case_solutions->count;
                        } else {
                            $cid = $_SESSION['bcp_contact_id'];
                            $data_fields = array('id');
                            $others = array();
                            $others['showcasesolution'] = TRUE;
                            $others['solution_limit'] = $limit;
                            $others['solution_offset'] = $offset;
                            $others['relationship_field'] = $_SESSION['bcp_module_access_data']->case->relationship;
                            if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
                                $relationship_field_key = $_SESSION['bcp_module_access_data']->case->accountRelationship;
                                $cid = $_SESSION['bcp_account_id'];
                            }else{
                                $relationship_field_key = $_SESSION['bcp_module_access_data']->case->contactRelationship;
                                $cid = $_SESSION['bcp_contact_id'];
                            }
                            
                            $case_record_solutions = $objCp->PortalGetDetailData($cid, "case", $case_id, $data_fields, $others);
                            $case_solutions = (isset($case_record_solutions->solutions)) ? $case_record_solutions->solutions : "";
                            $solutions = (isset($case_solutions->data)) ? $case_solutions->data : array();
                            $total_records = (isset($case_solutions->count)) ? $case_solutions->count : "";
                        }
                    }
                    
                    if (count($solutions) == 0) {
                        $solution_message = __("No solution found.", 'bcp_portal');
                    } else {
                        echo "<input type='hidden' class='view_style' value='".$view_style."' />";
                        $solution_message = __("Didn't find solution?", 'bcp_portal');
                        if ($view_style == 'style_1') {
                            ?>
                            <div class="not-found-block solution-found-block">
                                <h3><?php _e("Solution", 'bcp_portal') ?></h3>
                                <div class="sfcp-validation casecomment-msg"></div>
                            <?php
                            $cnt = 0;
                            foreach ($solutions as $value) {
                                $title = $value->SolutionName;
                                $id = $value->Id;
                                $sort_desc = htmlspecialchars($value->SolutionNote);
                                $sort_desc = nl2br($sort_desc);
                                $total_like = (isset($value->$like_field) ? $value->$like_field : $value->Like__c);
                                $total_dislike = (isset($value->$dislike_field) ? $value->$dislike_field : $value->Dislike__c);
                                $liked_class = "";
                                $disliked_class = "";
                                if (isset($value->ratingdata->data)) {
                                    $rat_array = $value->ratingdata->data;
                                    if (isset($rat_array[0])) {
                                        $value_sol = $rat_array[0];
                                        if ($value_sol->$slike_field == 1) {
                                            $liked_class = "sfcp_liked";
                                        }
                                        if ($value_sol->$sdislike_field == 1) {
                                            $disliked_class = "sfcp_disliked";
                                        }
                                    }
                                }
                                $like_title = __("Like", 'bcp_portal');
                                $dislike_title = __("Dislike", 'bcp_portal');
                                if (isset($rating_data[$id]) && $rating_data[$id] == 1) {
                                    $liked_class = "sfcp_liked";
                                    $like_title = __("Unlike", 'bcp_portal');
                                }
                                if (isset($rating_data[$id]) && $rating_data[$id] == 0) {
                                    $disliked_class = "sfcp_disliked";
                                }
                                ?>
                                    <div class="accordian">
                                        <div class="acc__card">
                                            <div class="acc__title"><?php echo $title ?>
                                                <div class="like-block">
                                                    <div class="sfcp_likes_loading"></div>
                                                    <p class="sfcp_dislike_div">
                                                        <span title="<?php echo $dislike_title; ?>" class="lnr lnr-thumbs-down sfcp_solution_like_btn <?php echo $disliked_class; ?>" data-id="<?php echo $id; ?>" id="sfcp_solution_dislike_btn"></span>
                                                        <small><?php echo $total_dislike; ?></small>
                                                    </p>
                                                    <p class="sfcp_like_div">
                                                        <span title="<?php echo $like_title; ?>" class="lnr lnr-thumbs-up sfcp_solution_like_btn <?php echo $liked_class; ?>" data-id="<?php echo $id; ?>" id="sfcp_solution_like_btn"></span>
                                                        <small><?php echo $total_like; ?></small>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="acc__panel"><?php echo $sort_desc; ?></div>
                                        </div>                    
                                    </div>
                                    <?php
                                    $cnt++;
                                }
                                echo $this->bcp_pagination($total_records, $limit, $paged, 'solutions', 'list', "", $search_term);
                                ?>
                            </div>
                        <?php } else if ($view_style == 'style_2') { ?>
                        <div class="not-found-block solution-found-block">
                            <h3><?php _e('Solution', 'bcp_portal'); ?></h3>
                            <div class="sfcp-validation casecomment-msg"></div>
                            <div class="accordian-wrapper">
                        <?php
                        $cnt = 0;
                        foreach ($solutions as $value) {

                            $title = $value->SolutionName;
                            $id = $value->Id;
                            $sort_desc = htmlspecialchars($value->SolutionNote);
                            $sort_desc = nl2br($sort_desc);
                            $total_like = (isset($value->$like_field) ? $value->$like_field : $value->Like__c);
                            $total_dislike = (isset($value->$dislike_field) ? $value->$dislike_field : $value->Dislike__c);
                            $liked_class = "";
                            $disliked_class = "";
                            if (isset($value->ratingdata->data)) {
                                $rat_array = $value->ratingdata->data;
                                if (isset($rat_array[0])) {
                                    $value_sol = $rat_array[0];
                                    if ($value_sol->$slike_field == 1) {
                                        $liked_class = "sfcp_liked";
                                    }
                                    if ($value_sol->$sdislike_field == 1) {
                                        $disliked_class = "sfcp_disliked";
                                    }
                                }
                            }
                            $like_title = __("Like", 'bcp_portal');
                            $dislike_title = __("Dislike", 'bcp_portal');
                            if (isset($rating_data[$id]) && $rating_data[$id] == 1) {
                                $liked_class = "sfcp_liked";
                                $like_title = __("Unlike", 'bcp_portal');
                            }
                            if (isset($rating_data[$id]) && $rating_data[$id] == 0) {
                                $disliked_class = "sfcp_disliked";
                            }
                            ?>
                                    <div class="accordian">
                                        <div class="acc__card">
                                            <div class="acc__title">
                                    <?php echo $title ?>
                                                <div class="like-block">
                                                    <div class="sfcp_likes_loading"></div>
                                                    <p class="sfcp_dislike_div">
                                                        <span title="<?php echo $dislike_title; ?>" class="lnr lnr-thumbs-down sfcp_solution_like_btn <?php echo $disliked_class; ?>" data-id="<?php echo $id; ?>" id="sfcp_solution_dislike_btn"></span>
                                                        <small><?php echo $total_dislike; ?></small>
                                                    </p>
                                                    <p class="sfcp_like_div">
                                                        <span title="<?php echo $like_title; ?>" class="lnr lnr-thumbs-up sfcp_solution_like_btn <?php echo $liked_class; ?>" data-id="<?php echo $id; ?>" id="sfcp_solution_like_btn"></span>
                                                        <small><?php echo $total_like; ?></small>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="acc__panel">
                                    <?php echo $sort_desc; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    $cnt++;
                                }
                                echo $this->bcp_pagination($total_records, $limit, $paged, 'solutions', 'list', "", $search_term);
                                ?>                                
                            </div>
                        </div>
                    <?php }
                }

                if ($search_term != "") {
                    ?>
                    <div class="not-found-block">
                        <div class="dcp-flex">
                            <p><?php echo $solution_message; ?></p>
                            <a href="<?php echo $add_url; ?>" class="bcp-btn" title="<?php _e("Add", 'bcp_portal'); ?>">
                                <span class="icon lnr lnr-plus-circle"></span>&nbsp;&nbsp;
                    <?php echo sprintf(__('Add %s', 'bcp_portal'), __($_SESSION['bcp_modules']['case']['singular'], 'bcp_portal')); ?>
                            </a>
                        </div>
                    </div>
                    <?php
                } else {
                    if (count($solutions) == 0 && $search_term != "") {
                        ?>
                        <div class="not-found-block">
                            <div class="dcp-flex">
                                <p><?php echo $solution_message; ?></p>
                                <a href="<?php echo $add_url; ?>" class="bcp-btn" title="<?php _e("Add", 'bcp_portal'); ?>">
                                    <span class="icon lnr lnr-plus-circle"></span>&nbsp;&nbsp;
                        <?php echo sprintf(__('Add %s', 'bcp_portal'), __($_SESSION['bcp_modules']['case']['singular'], 'bcp_portal')); ?>
                                </a>
                            </div>
                        </div>
                    <?php } else if (count($solutions) == 0) { ?>
                        <div class="not-found-block">
                            <div class="dcp-flex">
                                <p><?php echo $solution_message; ?></p>
                            </div>
                        </div>
                        <?php
                    }
                }
            } else {
                ?>
                <span class='error error-line error-msg settings-error'><?php _e('Please login to portal and access this page.', 'bcp_portal'); ?></span>
            <?php }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Portal is disabled. Please contact your administrator.', 'bcp_portal'); ?></span>
        <?php
        }
        $content .= ob_get_clean();

        return $content;
    }

    /**
     * Solution search page
     *
     * @since 4.0.0
     * @param [type] $view_style
     * @param string $add_case_title
     * @param string $module_description
     * @param string $limit
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_solutions_searchpage($view_style, $add_case_title, $module_description, $limit) {

        global $objCp;
        $list_template = '/case-deflection/' . $view_style . '.php';
        $theme_color = fetch_data_option('bcp_theme_color');

        $segregate_edit_page = $this->bcp_helper->get_segregate_url('case', 'bcp_add_edit');
        $add_url = home_url("customer-portal/case/edit/");
        if ($segregate_edit_page['page_exists'] && (isset($segregate_edit_page['page_url']) && $segregate_edit_page['page_url'] != '')) {
            $add_url = $segregate_edit_page['page_url'];
        }

        $data = array(
            'page_heading' => ucwords($_SESSION['bcp_modules']['case']['singular']),
            'add_case_title' => $add_case_title,
            'module_description' => $module_description,
            'theme_color' => $theme_color,
            'add_url' => $add_url
        );

        $this->template->render($list_template, $data);
        echo "<input type='hidden' name='view_style' class='view_style' value='" . $view_style . "'> ";
        echo "<input type='hidden' name='limit' class='limit' value='" . $limit . "'> ";
        ?>
        <script type="text/javascript">
            jQuery("#search_solutions_form").submit(function (e) {

                e.preventDefault();

                var search_term, error_message, bcp_required_error;
                bcp_required_error = 0;
                jQuery('.bcp-required', this).each(function () {

                    // If next element has class error-line, than remove it
                    if (jQuery(this).next().hasClass('error-line')) {
                        jQuery(this).next('').remove();
                    }

                    this.value = jQuery.trim(jQuery(this).val());
                    search_term = jQuery(this).val();
                    if (!search_term || search_term.length < 3) {

                        jQuery(this).addClass('error-line');
                        if (!search_term) {
                            error_message = '<?php _e('This field is required.', 'bcp_portal'); ?>';
                        } else {
                            error_message = '<?php _e('Enter atleast 3 characters to start search.', 'bcp_portal'); ?>';
                        }
                        jQuery('<label class="error-line">' + error_message + '</label>').insertAfter(this);
                        bcp_required_error = 1;
                    }

                });

                if (bcp_required_error) {
                    return false;
                }


                var search_term = jQuery("#bcp_solution_keyword").val();
                var view_style = jQuery(".view_style").val();
                var limit = jQuery(".limit").val();
                if (jQuery.trim(search_term) == "") {
                    return false;
                }
                var data = {
                    'action': 'bcp_solutions',
                    'module_name': 'solutions',
                    'view': 'list',
                    'search_term': search_term,
                    'view_style': view_style,
                    'limit': limit
                };
                jQuery('#solutionlist-items').addClass('bcp-loading');
                jQuery.post(ajaxurl, data, function (response) {
                    jQuery('#solutionlist-items').html(response);
                    jQuery('#solutionlist-items').removeClass('bcp-loading');
                });
            });
        </script>
        <?php
    }

    /**
     * Reset password call
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_reset_password_callback() {

        global $objCp, $bcp_field_prefix;

        if (!isset($_POST['bcp_portal_nonce_field']) || !wp_verify_nonce($_POST['bcp_portal_nonce_field'], 'bcp_portal_nonce')) {
            $response['url'] = $_REQUEST['bcp_current_url'] . '?error=1';
            $_SESSION['bcp_error_message'] = __("The form has expired due to inactivity. Please try again.", 'bcp_portal');
            echo json_encode($response);
            exit();
        }

        $bcp_login_page_id = fetch_data_option('bcp_login_page');
        $bcp_login_page = get_permalink($bcp_login_page_id);
        $bcp_token = ( isset($_REQUEST['bcp_contact_token']) && !empty($_REQUEST['bcp_contact_token']) ) ? $_REQUEST['bcp_contact_token'] : "";
        $where = array(
            $bcp_field_prefix . 'Access_Token__c' => $bcp_token
        );
        $bcp_session_contact_id = ( isset($_SESSION['bcp_set_password_contact_id']) && $bcp_token == "") ? $this->bcp_helper->bcp_sha_decrypt($_SESSION['bcp_set_password_contact_id']) : "";
        if ($bcp_token == "" && $bcp_session_contact_id != "") {
            $where = array(
                'id' => $bcp_session_contact_id
            );
        }
        $records = $objCp->getRecords('Contact', $where);
        if (isset($records->records[0]) && !empty($records->records[0])) {
            $record_id = $records->records[0]->Id;
            $newPwd = $_REQUEST['sfcp_new_password'];
            $request_filters = array(
                "Password__c" => $newPwd,
                "Access_Token__c" => "",
            );
            $flag = FALSE;
            if ($bcp_session_contact_id != "") {
                $flag = TRUE;
            }
            $result = $objCp->PortalChangePassword($record_id, $request_filters, $flag);

            if (isset($result->success) && $result->success) {
                if ($bcp_session_contact_id != "") {
                    $this->bcp_authorize($result);
                } else {
                    $response['url'] = $bcp_login_page . '?success=1';
                    $_SESSION['bcp_success_message'] = __("Your password updated successfully", 'bcp_portal');
                }
            } else {
                $sfcp_reset_pwd_page = fetch_data_option('bcp_reset_password_page');
                $bcp_login_page = add_query_arg(array('bcp_contact_token' => $bcp_token, 'error' => '1'), get_permalink($sfcp_reset_pwd_page));
                $message = (isset($result->message) && !empty($result->message)) ? $result->message : __("Enter a valid password", 'bcp_portal');
                $_SESSION['bcp_error_message'] = $message;
                $response['url'] = $bcp_login_page;
            }
        } else {
            $response['url'] = $bcp_login_page . '?error=1';
            $_SESSION['bcp_error_message'] = __("Invalid data in body.", 'bcp_portal');
        }

        echo json_encode($response);
        exit;
    }

    /**
     * If user dislike the solution
     *
     * @since 1.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_like_dislike_solutions() {
        global $objCp, $bcp_field_prefix;
        $contact_id = $_SESSION['bcp_contact_id'];
        $likes = $_REQUEST['is_like'];
        $like = FALSE;
        $dislike = FALSE;
        if ($likes != "-1") {
            $dislike = ($likes == '0') ? TRUE : FALSE;
            $like = ($likes == '0') ? FALSE : TRUE;
        }
        $id = $_REQUEST['id'];

        $arguments = array(
            "cid" => $contact_id,
            "id" => $id,
            "like" => $like,
            "dislike" => $dislike,
        );
        $response = $objCp->getPortalUpdateSolutionsLikes($arguments);
        if (isset($response->success) && $response->success == true) {
            echo 1;
        } else {
            echo 0;
        }
        wp_die();
    }

    /**
     * Check session exits for user
     *
     * @since 1.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_check_session() {
        $response['redirect_url'] = '';
        if (!isset($_SESSION['bcp_contact_id']) && isset($_SESSION['bcp_contact_name']) && $_SESSION['bcp_contact_name'] != '') {
            if ($_SESSION['bcp_error_message'] == '') {
                $_SESSION['bcp_error_message'] = __("Your session has been expired. Please login to continue.", 'bcp_portal');
            }
            $bcp_login_page = fetch_data_option('bcp_login_page');
            $bcp_login_page = get_permalink($bcp_login_page);
            $response['redirect_url'] = add_query_arg(array('bcp-logout' => 'true'), $bcp_login_page);
        }
        echo json_encode($response);
        wp_die();
    }

    /**
     * Registration submited handle
     *
     * @since 1.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_clear_logs_callback() {

        $files = glob(BCP_DEBUG_PATH . '/*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }
        $redirect_url = site_url() . "/wp-admin/admin.php?page=bcp-manage-logs&success=1";
        $_SESSION['bcp_success_clear_logs'] = __("Logs has beed removed successfully.", 'bcp_portal');
        wp_redirect($redirect_url);
        exit();
    }

    /**
     * Get all fields module wise
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_get_fields_callback() {
        global $objCp, $bcp_field_prefix, $wpdb;
        $module_name = $_POST['module_name'];
        $field_types = $_POST['field_type'] ? $_POST['field_type'] : '';

        $field_search_types = array();
        if (in_array($field_types, array('charts', 'counter'))) {
            if ($field_types == 'charts') {
                $field_search_types = array('picklist');
            } else {
                $field_search_types = array('picklist', 'multipicklist');
            }
        }
       
        $module_data = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_fields WHERE module='" . $module_name . "'");
        $module_fields = unserialize($module_data->allfields);
        
        $fields = array();
        $fields['fields_found'] = false;
        if (isset($module_fields) && count((array)$module_fields) > 0) {
            $fields['fields_found'] = true;
            $i = 0;
            foreach ($module_fields as $index => $field_information) {
                if (in_array($field_types, array('charts', 'counter'))) {
                    if ($field_types == 'counter' && $i === 0) {
                        $fields['fields_data'][] = (object) array('label' => 'All', 'value' => 'All');
                        $fields['field_values']['All'][] = (object) array('label' => 'All', 'value' => 'All');
                    }
                    if (in_array($field_information->type, $field_search_types) && isset($field_information->picklistValues) && count($field_information->picklistValues) > 0) {
                        $fields['fields_data'][] = (object) array('label' => $field_information->label, 'value' => $field_information->name);
                        foreach ($field_information->picklistValues as $options) {
                            $fields['field_values'][$field_information->name][] = (object) array('label' => $options->label, 'value' => $options->value);
                        }
                    }
                } else if ($field_types == 'recent_activity') {

                    $fields['fields_data']['all_field_data'][] = (object) array('label' => $field_information->label, 'value' => $field_information->name, 'type' => $field_information->type);
                    if (in_array($field_information->type, array('picklist', 'multipicklist'))) {
                        $fields['fields_data']['dropdown_fields'][] = (object) array('label' => $field_information->label, 'value' => $field_information->name, 'type' => $field_information->type);
                    } else if (in_array($field_information->type, array('datetime', 'date'))) {
                        $fields['fields_data']['date_fields'][] = (object) array('label' => $field_information->label, 'value' => $field_information->name, 'type' => $field_information->type);
                    }
                }
                $i++;
            }
            if (count((array)$fields['fields_data']) == 0) {
                $fields['no_fields_found'] = true;
            }
        }
        echo json_encode($fields);
        wp_die();
    }

    /**
     * List data for any module
     *
     * @since 1.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_list_portal_callback() {

        global $wpdb, $sf, $bcp_field_prefix, $bcp_secure_flag, $objCp, $percentage_option_values;
       //print_r($objCp);
        $bcp_modules = $_SESSION['bcp_modules'];

        $order_by = ( isset($_REQUEST['order_by']) ? $_REQUEST['order_by'] : 'CreatedDate DESC' );
        if (isset($_REQUEST['limit'])) {
            $limit = $_REQUEST['limit'];
        } else {
            $limit = (int) ( fetch_data_option('bcp_records_per_page') ? fetch_data_option('bcp_records_per_page') : 10 );
        }
        $paged = (int) ( isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1 );
        $offset = ( ( $paged * $limit ) - $limit);
        $search = ( isset($_REQUEST['search']) ? $_REQUEST['search'] : '' );
        $search = urldecode($search);
        $search = htmlentities($search);
        $module_name = $_REQUEST['module_name'];
        $tmp_module_name = $module_name;
        $view_style = (isset($_REQUEST['view_style']) && $_REQUEST['view_style'] != '') ? $_REQUEST['view_style'] : 'style_1';
        $where_condition = isset($_REQUEST['where_condition']) ? $_REQUEST['where_condition'] : "";
        $relationshipname = isset($_REQUEST['relationship']) ? $_REQUEST['relationship'] : "";
        $view = isset($_REQUEST['view']) ? $_REQUEST['view'] : "";
        $relate_id = isset($_REQUEST['relate_id']) ? $_REQUEST['relate_id'] : "";
        $relate_module = isset($_REQUEST['relate_module']) ? $_REQUEST['relate_module'] : "";
        
        if($module_name == "product2"){
            /**Remove products cookie */
            unset($_COOKIE['pselected_module']); 
            setcookie('pselected_module', null, -1, '/'); 
            unset($_COOKIE['pselected_products']); 
            setcookie('pselected_products', null, -1, '/'); 
            unset($_COOKIE['pselected_pricebook']); 
            setcookie('pselected_pricebook', null, -1, '/'); 
            $available_products = $objCp->getPricebooks();
            if ($available_products->success && $available_products->totalSize > 0) {
                $available_pricebooks = array();
                foreach ($available_products->records as $pricebook) {
                    $available_pricebooks[$pricebook->Id] = $pricebook->Name;
                }
            }
            $list_template = '/form/product_selection.php';
            $data = array(
                'module_name' => $module_name,
                'available_pricebooks' => $available_pricebooks,
                'productlistmenu' => true,
                'action_class' => new BCPCustomerPortalActions($this->plugin_name, $this->version),
            );

            $this->template->render($list_template, $data);
            exit;
        }

        if (!is_array($where_condition) && !empty($where_condition)) {
            $where_condition = str_replace("\\", "", $where_condition);
            $where_condition = json_decode($where_condition, 1);
        }
        $where_cond_array = $where_condition;

        if ($module_name != 'KBDocuments') {
            $this->check_pemission($module_name);

            if (( isset($_SESSION['bcp_module_access_data']->$module_name) && $_SESSION['bcp_module_access_data']->$module_name->access == 'false' ) ||
                    (!isset($_SESSION['bcp_module_access_data']->$module_name) )) {
                ?>
                <span class='error error-line error-msg settings-error'><?php _e('Page not found.', 'bcp_portal'); ?></span>
                <?php
                wp_die();
            }
        }
        $bcp_portal_role = isset($_SESSION['bcp_portal_role']) ? $_SESSION['bcp_portal_role'] : "";
        
        if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
            $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->accountRelationship;
            $cid = $_SESSION['bcp_account_id'];
            if($module_name == "account"){
                $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                $cid = $_SESSION['bcp_contact_id'];
            }
            if($_SESSION['bcp_module_access_data']->$module_name->accountRelationship != "" && $_SESSION['bcp_module_access_data']->$module_name->contactRelationship != "" && !in_array($module_name,array("contentnote","attachment","contentdocument")) && $view != "subpanel"){
                $showtabs = true;
            }else{
                $showtabs = false;
            }
           
        }else{
            $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
            $cid = $_SESSION['bcp_contact_id'];
            $showtabs = false;
        }
       
       // echo $relationship_field_key;
        if (in_array($module_name, $this->different_name_modules)) {
            $layout = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts WHERE module='$module_name' AND view='list'");
        } else {
            $layout = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts WHERE module='$module_name' AND view='list' AND role='$bcp_portal_role'");
        }
        
        $field_definition_result = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_fields WHERE module='$module_name'");
        $layout_data = (object) array();
        if ($layout != null) {
             
            // Get Relate field Value in list call by passing relationship and field name
            $layout->layout = isset($field_definition_result->fields) ? $field_definition_result->fields : "";
            $layout_data->fields = unserialize($layout->layout);
           
            $bcp_layout_fields = json_decode($layout->fields, true);
            $bcp_layout_fields = array_keys($bcp_layout_fields);
            $fields_meta = ( isset($layout->properties) && !empty($layout->properties) ) ? json_decode($layout->properties, 1) : array();
            $display_advance_filter = FALSE;
            $module_icon = $layout->icon;
            $module_title = $layout->title;
            $module_sub_title = $layout->sub_title;
            $data_fields = array();
            $layout_fields = array();


            if ($layout_data->fields != null) {
                foreach ($layout_data->fields as $layout_data_key => $layout_data_value) {
                    if(in_array($layout_data_key,$bcp_layout_fields)){
                        $ignore_field = false;
                        $field_name = $layout_data_value->name;
                        if (isset($fields_meta[$field_name]['searchable']) && $fields_meta[$field_name]['searchable'] == "true") {
                            $display_advance_filter = TRUE;
                        }
                        $layout_fields[$field_name] = $layout_data_value;
                        if ($layout_data_value->type == "reference" && $layout_data_value->relationshipName != "Parent") {
                            $ref_object_name = $layout_data_value->referenceTo[0];
                            $ref_field_name = "Name";
                            if ($ref_object_name == 'Case') {
                                $ref_field_name = 'Subject';
                            }
                            $relate_module_table = $layout_data_value->relationshipName;
                            $field_name = $relate_module_table . "." . $ref_field_name;
                            array_push($data_fields, $field_name);
                            $ignore_field = true;
                        }
                        if (!$ignore_field && in_array($field_name, $bcp_layout_fields)) {
                            array_push($data_fields, $field_name);
                        }
                    }
                }
            }
          
            if (is_array($where_condition) && count($where_condition)) {

                $temp_where_and = array();

                foreach ($where_condition as $key => $value) {

                    if (is_array($value)) {
                        if (isset($layout_data->fields->$key->type) && $layout_data->fields->$key->type == "percent") {
                            $p_where_cond = "";
                            if ($module_name == "opportunity") {
                                $key = $module_name . "." . $key;
                            }
                            foreach ($value as $p_value) {
                                $p_value_arr = explode("-", $p_value);
                                $p_val_min = $p_value_arr[0];
                                $p_val_max = $p_value_arr[1];
                                if ($p_where_cond != "") {
                                    $p_where_cond .= " OR ";
                                }
                                $p_where_cond .= "(" . $key . " >= " . $p_val_min . " AND " . $key . " <= " . $p_val_max . " )";
                            }
                            $temp_where_and[] = $p_where_cond;
                        } else if (count($value) > 1) {
                            //with using IN operator
                            if ($module_name == "opportunity") {
                                $key = $module_name . "." . $key;
                            }
                            $temp_where_and[] = $key . " IN ('" . implode("','", $value) . "')";
                        } elseif (count($value) == 1 && isset($value['date-range']) && count($value['date-range']) == 2) {
                            if ($module_name == "opportunity") {
                                $key = $module_name . "." . $key;
                            }
                            $temp_where_and[] = $key . " >= " . date("Y-m-d\TH:i:s\Z", strtotime($value['date-range'][0])) . "";
                            $temp_where_and[] = $key . " <= " . date("Y-m-d\TH:i:s\Z", strtotime("+1 day", strtotime($value['date-range'][1]))) . "";
                        } else {
                            if ($module_name == "opportunity") {
                                $key = $module_name . "." . $key;
                            }
                            $temp_where_and[] = $key . " = '" . $value[0] . "'";
                        }
                    } else {
                        if (!empty($value)) {
                            $s_key = $key;
                            if ($module_name == "opportunity") {
                                $s_key = $module_name . "." . $key;
                            }
                            if (isset($layout_data->fields->$key->type) && $layout_data->fields->$key->type == "string") {
                                $temp_where_and[] = $s_key . " LIKE '%" . $value . "%'";
                            } else {
                                $temp_where_and[] = $s_key . " = '" . $value . "'";
                            }
                        }
                    }
                }
                $where_condition = implode(' AND ', $temp_where_and);
            } else {
                $where_condition = str_replace("\'", "'", $where_condition);
            }
            
            if ($module_name == "call") {
                $where_condition .= (empty($where_condition)) ? " tasksubtype='call'" : " AND tasksubtype='call'";
            }

            $search_module['case']['search_field'] = array(
                'Subject' => $search,
                'CaseNumber' => $search,
            );
            $search_module['contract']['search_field'] = array(
                'ContractNumber' => $search,
                'Name' => $search
            );
            $search_module['order']['search_field'] = array(
                'OrderNumber' => $search,
                'Name' => $search
            );
            $search_module['call']['search_field'] = array(
                'Subject' => $search,
                'Status' => $search,
            );
            $search_module['contentdocument']['search_field'] = array(
                'ContentDocument.title' => $search,
            );
            $search_module['contentnote']['search_field'] = array(
                'ContentNote.TextPreview' => $search,
            );
            $search_module['event']['search_field'] = array(
                'Subject' => $search,
            );
            $search_module['opportunity']['search_field'] = array(
                'Opportunity.Name' => $search,
            );
            $search_module['task']['search_field'] = array(
                'Subject' => $search,
                'Status' => $search,
            );
            $search_module['kbarticle']['search_field'] = array(
                'title' => $search,
            );
            $search_module['workorder']['search_field'] = array(
                'Subject' => $search,
            );

            $default_search_fields = array(
                "Name" => $search,
            );

            $search_field = array();
            if ($search != "") {
                $search_field = isset($search_module[$module_name]) ? $search_module[$module_name]['search_field'] : $default_search_fields;
            }
            if (($module_name == 'account' && isset($_SESSION['bcp_account_id']) && $_SESSION['bcp_account_id'] != '') && $view == '') {
                $where_condition .= (empty($where_condition)) ? "Id='" . $_SESSION['bcp_account_id'] . "'" : "AND Id='" . $_SESSION['bcp_account_id'] . "'";
            }

            if ($module_name == 'task') {
                $where_condition .= (empty($where_condition)) ? " tasksubtype!='call'" : " AND tasksubtype!='call'";
            }
            if ($module_name == 'call') {
                $tmp_module_name = "task";
                $where_condition .= (empty($where_condition)) ? " tasksubtype='call'" : " AND tasksubtype='call'";
            }

            if ($view == 'subpanel' && $relationshipname != '' && $relate_id != '') {
                $where_condition .= (empty($where_condition)) ? $relationshipname . "='" . $relate_id . "'" : "AND " . $relationshipname . "='" . $relate_id . "'";
                /*** To show the data in subpanel for parent object */
                if(($module_name == "task" || $module_name == "event") && $_SESSION['bcp_relationship_type'] == "Account-Based"){
                  //  $cid = $_SESSION['bcp_contact_id'];
                    $relationship_field_key = "AccountId";
                }
                
            }
            $selectRecordType = $_REQUEST['selectRecordType'];
            if($selectRecordType == "" ){
                $selectRecordType == "myrecord";
            }
         
            if(isset($selectRecordType) && $selectRecordType == "myrecord"){
               
                $contact_relationship = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                $where_condition .= (empty($where_condition)) ? " $contact_relationship='".$_SESSION['bcp_contact_id']."'" : " AND $contact_relationship='".$_SESSION['bcp_contact_id']."'" ;   
               
            }

            $orderby = "";
            $order = "";
            if ($order_by != "") {
                $order_by_array = explode(" ", $order_by);
                $orderby = $order_by_array[0];
                $order = $order_by_array[1];
            }
          

            if ($module_name == "case" && !in_array('IsClosed', $data_fields)) {
                $data_fields[] = 'IsClosed';
            }
            /**** To Check if record is created by logeedin contact */
           //if( $_SESSION['bcp_relationship_type'] == "Account-Based" && $module_name != "account"){
            if( $module_name != "account" && $module_name != "contentnote" ){
                $contact_relationship = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                array_push($data_fields,$contact_relationship);
                $data_fields = array_unique($data_fields);
            }
         
            $arguments = array(
                "cid" => $cid,
                "relationship_field" => $relationship_field_key,
                "module_name" => $tmp_module_name,
                "fields" => $data_fields,
                "order" => $order,
                "order_by" => $orderby,
                "limit" => $limit,
                "offset" => $offset,
                "search" => $search,
                "search_field" => $search_field,
                "where_condition" => $where_condition,
            );

            if ($module_name == "contentdocument") {
                if ($orderby != "") {
                    $attachment_orderby = "ContentDocument." . $orderby;
                } else {
                    $attachment_orderby = "ContentDocument.CreatedDate";
                    $order = "asc";
                }
                $arguments["order"] = $order;
                $arguments["order_by"] = $attachment_orderby;
                $arguments["module_name"] = $module_name;
                $arguments['fields'] = array();
            }
            $records = $objCp->getPortalListData($arguments);
            if ($module_title != '') {
                $heading_modulename = $module_title;
            } else {
                $heading_modulename = $bcp_modules[$module_name]['plural'];
            }
            if ($module_name == "contentdocument") {
                $heading_modulename = __('File', 'bcp_portal');
            }
            
            $add_module_href = '';
            $add_module = false;
            $segregate_detail_page_exists = false;
            $segregate_detail_page_url = '';
            $segregate_edit_page_exists = false;
            $segregate_edit_page_url = '';
            $segregate_deflection_page_exists = false;


            if ($view == 'subpanel' && (isset($_SESSION['bcp_module_access_data']->$module_name->create) && $_SESSION['bcp_module_access_data']->$module_name->create == "true")) {

                $url = 'customer-portal/' . $module_name . '/edit/Relate/' . $relate_module . '/Relation/' . $relationshipname . '/' . $relate_id;
                $add_module = true;
                $add_module_href = home_url($url);

                $segregate_edit_page = $this->bcp_helper->get_segregate_url($module_name, 'bcp_add_edit');
                $segregate_edit_page_exists = $segregate_edit_page['page_exists'];
                if ($segregate_edit_page_exists && (isset($segregate_edit_page['page_url']) && $segregate_edit_page['page_url'] != '')) {
                    $segregate_edit_page_url = $segregate_edit_page['page_url'];
                    $add_module_href = $segregate_edit_page_url . 'Relate/' . $relate_module . '/Relation/' . $relationshipname . '/' . $relate_id;
                }
            } else {

                if (isset($_SESSION['bcp_module_access_data']->$module_name->create) && $_SESSION['bcp_module_access_data']->$module_name->create == 'true') {
                    if ($module_name == 'account' && isset($records->records) && !empty($records->records)) {
                        $add_module = false;
                        $display_advance_filter = FALSE; // Added by Dharti Gajera
                    } elseif ($module_name == 'case' && isset($_SESSION['bcp_module_access_data']->solution->access) && $_SESSION['bcp_module_access_data']->solution->access == 'true' && isset($_SESSION['bcp_case_deflection']) && $_SESSION['bcp_case_deflection'] == 1) {
                        $add_module = true;
                        $add_module_href = home_url('/customer-portal/solutions/search/');

                        $segregate_deflection_page = $this->bcp_helper->get_segregate_url($module_name, 'bcp_case_deflection');
                        $segregate_deflection_page_exists = $segregate_deflection_page['page_exists'];
                        if ($segregate_deflection_page_exists && (isset($segregate_deflection_page['page_url']) && $segregate_deflection_page['page_url'] != '')) {
                            $add_module_href = $segregate_deflection_page['page_url'];
                        }

                        $segregate_edit_page = $this->bcp_helper->get_segregate_url($module_name, 'bcp_add_edit');
                        $segregate_edit_page_exists = $segregate_edit_page['page_exists'];
                        if ($segregate_edit_page_exists && (isset($segregate_edit_page['page_url']) && $segregate_edit_page['page_url'] != '')) {
                            $segregate_edit_page_url = $segregate_edit_page['page_url'];
                        }
                    } else {
                        $add_module = true;
                        $add_module_href = home_url('/customer-portal/' . $module_name . '/edit/');

                        $segregate_edit_page = $this->bcp_helper->get_segregate_url($module_name, 'bcp_add_edit');
                        $segregate_edit_page_exists = $segregate_edit_page['page_exists'];
                        if ($segregate_edit_page_exists && (isset($segregate_edit_page['page_url']) && $segregate_edit_page['page_url'] != '')) {
                            $segregate_edit_page_url = $segregate_edit_page['page_url'];
                            $add_module_href = $segregate_edit_page_url;
                        }
                    }
                }
            }

            $segregate_list_page = $this->bcp_helper->get_segregate_url($module_name, 'bcp_list');
            $segregate_list_page_url = '';
            if ($segregate_list_page['page_exists']) {
                $segregate_list_page_url = $segregate_list_page['page_url'];
            }
            $module_singular_name = $bcp_modules[$module_name]['singular'];
            $total_records = '';
            if (isset($records->records) && $records->records != null) {
                $total_records = $records->totalSize;

                $apiFormatter = new ApiFormatter();
                $list_records = $apiFormatter->getFormattedListCall($layout, $records, $module_name);
                $segregate_detail_page = $this->bcp_helper->get_segregate_url($module_name, 'bcp_detail');
                $segregate_detail_page_exists = $segregate_detail_page['page_exists'];
                if ($segregate_detail_page_exists && ( isset($segregate_detail_page['page_url']) && $segregate_detail_page['page_url'] != '' )) {
                    $segregate_detail_page_url = $segregate_detail_page['page_url'];
                }
            } else {
                // Added By: Dharti Gajera. When record is empty hide Advance filter.
                if (isset($records->records) && empty($records->records) && empty($where_cond_array)) {
                    $display_advance_filter = FALSE;
                }

                $list_records = '';
                if (isset($records->response->errorCode) && $records->response->errorCode == 'ENTITY_IS_DELETED') {
                    $_SESSION['bcp_error_message'] = $records->response->message;
                    unset($_SESSION['bcp_contact_id']);
                }
            }
            if ($view) {
                unset($_SESSION['record_msg']);
            }

            $list_template = '/list/' . $view_style . '.php';
            $data = array(
                'module_name' => $module_name,
                'heading_modulename' => $heading_modulename,
                'add_module' => $add_module,
                'add_module_href' => $add_module_href,
                'custom_detail_page' => $segregate_detail_page_exists,
                'custom_detail_page_url' => $segregate_detail_page_url,
                'custom_edit_page' => $segregate_edit_page_exists,
                'custom_edit_page_url' => $segregate_edit_page_url,
                'module_singular_name' => $module_singular_name,
                'list_records' => $list_records,
                'fields' => json_decode($layout->fields, 1),
                'layout' => unserialize($layout->layout),
                'where_condition' => $where_condition,
                'where_cond_array' => (!empty($where_cond_array) ) ? $where_cond_array : array(),
                "percentage_option_values" => $percentage_option_values,
                'fields_meta' => $fields_meta,
                'display_advance_filter' => $display_advance_filter,
                'total_records' => $total_records,
                'order_by' => $order_by,
                'limit' => $limit,
                'paged' => $paged,
                'search' => $search,
                'style' => $view_style,
                'orderby' => $orderby,
                'order' => $order,
                'module_icon' => $module_icon,
                'module_sub_title' => $module_sub_title,
                'view' => $view,
                'relate_id' => $relate_id,
                'relationshipname' => $relationshipname,
                'relate_module' => $relate_module,
                'action_class' => new BCPCustomerPortalActions($this->plugin_name, $this->version),
                'showtabs' => $showtabs,
                'selectRecordType' =>$selectRecordType
            );

            $this->template->render($list_template, $data);

            echo '<input type="hidden" name="url" class="module-url" value="' . home_url('/customer-portal/' . $module_name . '/list') . '" />';
            echo '<input type="hidden" name="view_style" class="view_style" value="' . $view_style . '" />';
            echo '<input type="hidden" name="limit" class="limit" value="' . $limit . '" />';

            echo '<input type="hidden" name="relationship" class="relationship" value="' . $relationshipname . '" />';
            echo '<input type="hidden" name="relate_module" class="relate_module" value="' . $relate_module . '" />';
            echo '<input type="hidden" name="relate_id" class="relate_id" value="' . $relate_id . '" />';

            if (isset($segregate_list_page_url) && $segregate_list_page_url != '') {
                $segregate_list_page_url = strtok($segregate_list_page_url, '?');
                echo '<input type="hidden" name="search-url" class="search-url" value="' . $segregate_list_page_url . '" />';
            }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Please set layout in Salesforce.', 'bcp_portal'); ?></span>
            <?php
        }
        wp_die();
    }

    /**
     * Delete data for any module
     *
     * @since 1.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_delete_portal_callback() {

        global $objCp, $bcp_secure_flag;

        $bcp_modules = $_SESSION['bcp_modules'];
        $module_name = $_REQUEST['module_name'];
        $record_id = $_REQUEST['record_id'];
        $relate_module = (isset($_REQUEST['relate_module']) && $_REQUEST['relate_module'] != '') ? $_REQUEST['relate_module'] : '';
        $relate_id = (isset($_REQUEST['relate_id']) && $_REQUEST['relate_id'] != '') ? $_REQUEST['relate_id'] : '';

        $this->check_pemission($module_name, "delete", $record_id);
        if (( isset($_SESSION['bcp_module_access_data']->$module_name) &&
                ( $_SESSION['bcp_module_access_data']->$module_name->access == 'false' ||
                $_SESSION['bcp_module_access_data']->$module_name->delete == 'false' ||
                ( $record_id != '' && $record_id != '0' && $_SESSION['bcp_module_access_data']->$module_name->delete == 'false' ) ) ) ||
                (!isset($_SESSION['bcp_module_access_data']->$module_name) )) {

            $response['success'] = false;
            $response['message'] = __("You don't have access to delete this record.", 'bcp_portal');
            $response['module_name'] = $module_name;
        } else {
            $tmp_module_name = $module_name;
            if ($module_name == "call") {
                $tmp_module_name = "task";
            }
            $arguments = array(
                "module_name" => $tmp_module_name,
                "record_id" => $record_id,
            );
            $delete = $objCp->getdeleteRecord($arguments);
            $response = array(
                'success' => false,
                'module_name' => $module_name,
            );
            if ($delete != null && isset($delete[0]) && is_object($delete[0])) {
                $response['message'] = (isset($delete[0]->message)) ? $delete[0]->message : __("You don't have access to delete this record.", 'bcp_portal');
            } else {
                if ($module_name == 'account') {
                    unset($_SESSION['bcp_account_id']);
                }
                $response['success'] = true;
                $response['message'] = $bcp_modules[$module_name]['singular'] . __(' deleted successfully.', 'bcp_portal');
                setcookie("record_msg", $response['message'], time() + 3600, "/", "", '', true);
                $_SESSION['record_msg'] = $response['message'];
                $response['relate_module'] = $relate_module;
                $response['relate_id'] = $relate_id;
            }
        }

        echo json_encode($response);
        wp_die();
    }

    /**
     * Get Detail for any module
     *
     * @since 1.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_detail_portal_callback() {
        global $wpdb, $sf, $bcp_field_prefix, $case_record, $bcp_secure_flag, $objCp;

        $bcp_portal_template = fetch_data_option('bcp_portal_template');

        $recentFields = array();
        $recentFields['case'] = "CaseNumber";
        $recentFields['account'] = "Name";
        $recentFields['asset'] = "Name";
        $recentFields['order'] = "OrderNumber";
        $recentFields['contract'] = "ContractNumber";
        $recentFields['call'] = "Subject";
        $recentFields['contentdocument'] = "Title";
        $recentFields['attachment'] = "Name";

        $module_name = $_REQUEST['module_name'];
        $view_style = $_REQUEST['view_style'];

        // Use this to load file dynamically and use dynamic function
        $request_module_file = BCP_TEMPLATE_PATH . $module_name . '.php';
        $request_function = 'sfcp_' . $module_name . '_detail';

        $bcp_modules = $_SESSION['bcp_modules'];
        if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
            $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->accountRelationship;
            $cid = $_SESSION['bcp_account_id'];
            if($module_name == "account"){
                $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                $cid = $_SESSION['bcp_contact_id'];
            }
        }else{
            $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
            $cid = $_SESSION['bcp_contact_id'];
        }

        if (( isset($_SESSION['bcp_module_access_data']->$module_name) && $_SESSION['bcp_module_access_data']->$module_name->access == 'false' ) ||
                (!isset($_SESSION['bcp_module_access_data']->$module_name) )) {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Page not found.', 'bcp_portal'); ?></span>
            <?php
            wp_die();
        }

        if (isset($_REQUEST['record_id'])) {
            $record_id = $_REQUEST['record_id'];
            $this->check_pemission($module_name, "access", $record_id);
        }
        $bcp_portal_role = isset($_SESSION['bcp_portal_role']) ? $_SESSION['bcp_portal_role'] : "";

        /*if ($module_name == "call") {
            $layout = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts WHERE module='call' AND view='detail'");
        } else */
        if (in_array($module_name, $this->different_name_modules)) {
            $layout = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts WHERE module='$module_name' AND view='detail'");
        } else {
            $layout = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts WHERE module='$module_name' AND view='detail' AND role='$bcp_portal_role'");
        }
        $field_definition_result = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_fields WHERE module='$module_name'");
        $layout_data = (object) array();
        if ($layout != null) {
            // Get Relate field Value in list call by passing relationship and field name
            $layout->layout = isset($field_definition_result->fields) ? $field_definition_result->fields : "";
            $layout_data->fields = unserialize($layout->layout);
            $bcp_layout_fields = json_decode($layout->fields, true);
            $bcp_layout_section_layouts = json_decode($layout->section_layout, true);
            $bcp_layout_fields = array_keys($bcp_layout_fields);
            $module_icon = $layout->icon;
            $module_title = $layout->title;
            $module_sub_title = $layout->sub_title;

            $bcp_layout_section_layout = array();
            foreach ($bcp_layout_section_layouts as $bcp_layout_sectionlayout) {
                $rows = $bcp_layout_sectionlayout['Field_rows'];
                foreach ($rows as $value2) {
                    $value2 = (array) $value2;
                 
                    $row_array = array_values($value2);
                    foreach ($row_array as $value3) {
                        if (!empty($value3['name'])) {
                            $bcp_layout_section_layout[] = $value3['name'];
                        }
                    }
                }
            }
            
            $data_fields = array();
            $layout_fields = array();
            $layout_field_properties = ( isset($layout->properties) && !empty($layout->properties) ) ? json_decode($layout->properties, 1) : array();
            if ($layout_data->fields != null) {
                foreach ($layout_data->fields as $layout_data_key => $layout_data_value) {
                    if(in_array($layout_data_key,$bcp_layout_fields)){
                        $ignore_field = false;
                        $field_name = $layout_data_value->name;
                        $layout_fields[$field_name] = $layout_data_value;
                        if ($layout_data_value->type == "reference" && $layout_data_value->relationshipName != "Parent") {
                            $ref_object_name = $layout_data_value->referenceTo[0];
                            $ref_field_name = "Name";
                            if ($ref_object_name == 'Case') {
                                $ref_field_name = 'Subject';
                            }
                            $relate_module_table = $layout_data_value->relationshipName;
                            $field_name = $relate_module_table . "." . $ref_field_name;
                            array_push($data_fields, $field_name);
                            $ignore_field = true;
                        }
                        if (!$ignore_field && in_array($field_name, $bcp_layout_section_layout)) {
                            array_push($data_fields, $field_name);
                        }
                    }
                }
            }
            
            $record_id = $_REQUEST['record_id'];
            if (isset($_SESSION['record']) && $module_name != 'case') {
                $case_record = $_SESSION['record'];
                $record = $case_record->data;
                unset($_SESSION['record']);
            } else {
               
                $others = array();
                if ($module_name == 'case') {
                    if (isset($_REQUEST['submodule']) && $_REQUEST['submodule'] == "solutions") {
                        $limit = (int) ( fetch_data_option('bcp_records_per_page') ? fetch_data_option('bcp_records_per_page') : 10 );
                        $others['showcasesolution'] = TRUE;
                        $others['solution_limit'] = $limit;
                        $others['solution_offset'] = 0;
                    } else {
                        $others['showcasecomment'] = TRUE;
                        $others['showcaseattachment'] = TRUE;
                        $others['showcasenote'] = TRUE;
                    }
                }
                if ($module_name == 'order') {
                    $others['showorderproduct'] = TRUE;
                }

                if ($module_name == 'opportunity') {
                    $others['showopportunityproduct'] = TRUE;
                }

                $others['relationship_field'] = $relationship_field_key;

                $tmp_module_name = $module_name;
                if ($module_name == "call") {
                    $tmp_module_name = "task";
                }

                if ($module_name == "case" && !in_array('IsClosed', $data_fields)) {
                    $data_fields[] = 'IsClosed';
                }
                /**** To Check if record is created by logeedin contact */
                if( $module_name != "account"){
                    $contact_relationship = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                    array_push($data_fields,$contact_relationship);
                    $data_fields = array_unique($data_fields);
                }

                $arguments = array(
                    "cid" => $cid,
                    "module_name" => $tmp_module_name,
                    "fields" => $data_fields,
                    "record_id" => $_REQUEST['record_id'],
                    "others" => $others,
                );
             
                $case_record = $objCp->getPortalDetailData($arguments);
             
                if (isset($case_record->success) && isset($case_record->data) && !empty((array) $case_record->data)) {
                    $record = $case_record->data;
                    /* Set the value of contact relationship to check record is created by which contact**/
                    $contact_relationship = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                    $contact_relationship_value = $record->$contact_relationship;
                    $RecordCreatedByContact = $contact_relationship_value;
                    unset($record->$contact_relationship);
                } else {
                    if (isset($case_record->response->errorCode) && $case_record->response->errorCode == 'ENTITY_IS_DELETED') {
                        $_SESSION['bcp_error_message'] = $case_record->response->message;
                        unset($_SESSION['bcp_contact_id']);
                    } else {
                        echo "<span class='error error-line error-msg settings-error'> " . __('You don\'t have sufficient permission to access this data.', 'bcp_portal') . "</span>";
                        wp_die();
                    }
                }
            }
            unset($record->attributes);
            $section_layout = (array) json_decode($layout->section_layout);

            if ($module_title != '') {
                $heading_modulename = $module_title;
            } else {
                $heading_modulename = ucwords($bcp_modules[$module_name]['plural']);
            }
            if ($module_name == "contentdocument") {
                $heading_modulename = __('File', 'bcp_portal');
            }

            $module_singular_name = $bcp_modules[$module_name]['singular'];
            $add_module_url = $edit_page_url = '';
            if ($module_name == 'account' && isset($_SESSION['bcp_account_id']) && $_SESSION['bcp_account_id'] != '') {
                $add_option = false;
            } else {
                if (isset($_SESSION['bcp_module_access_data']->$module_name->create) && $_SESSION['bcp_module_access_data']->$module_name->create == "true") {

                    $segregate_edit_page_exists = false;
                    $segregate_edit_page_url = '';
                    $edit_page_url = home_url('customer-portal/' . $module_name . '/edit/' . $record_id);
                    if ($module_name == 'case' && isset($_SESSION['bcp_module_access_data']->solution->access) && $_SESSION['bcp_module_access_data']->solution->access == 'true' && isset($_SESSION['bcp_case_deflection']) && $_SESSION['bcp_case_deflection'] == 1) { // If this is case module and contact has solution module access than change url to solution
                        $url = 'customer-portal/solutions/search/';
                        $add_module_url = home_url($url);
                        $add_option = true;

                        $segregate_deflection_page = $this->bcp_helper->get_segregate_url($module_name, 'bcp_case_deflection');
                        $segregate_deflection_page_exists = $segregate_deflection_page['page_exists'];
                        if ($segregate_deflection_page_exists && (isset($segregate_deflection_page['page_url']) && $segregate_deflection_page['page_url'] != '')) {
                            $add_module_url = $segregate_deflection_page['page_url'];
                        }

                        $segregate_edit_page = $this->bcp_helper->get_segregate_url($module_name, 'bcp_add_edit');
                        $segregate_edit_page_exists = $segregate_edit_page['page_exists'];
                        if ($segregate_edit_page_exists && (isset($segregate_edit_page['page_url']) && $segregate_edit_page['page_url'] != '')) {
                            $edit_page_url = $segregate_edit_page['page_url'] . $record_id;
                        }
                    } else {
                        $url = 'customer-portal/' . $module_name . '/edit/';
                        $add_module_url = home_url($url);
                        $add_option = true;
                        $segregate_edit_page = $this->bcp_helper->get_segregate_url($module_name, 'bcp_add_edit');
                        $segregate_edit_page_exists = $segregate_edit_page['page_exists'];
                        if ($segregate_edit_page_exists && (isset($segregate_edit_page['page_url']) && $segregate_edit_page['page_url'] != '')) {
                            $segregate_edit_page_url = $segregate_edit_page['page_url'];
                            $add_module_url = $segregate_edit_page_url;
                            $edit_page_url = $segregate_edit_page['page_url'] . $record_id;
                        }
                    }
                }
            }
            $record_name = '';
            if ($module_name == 'case') {
                $record_name = $record->Subject;
            } else if ($module_name == 'contract') {
                $record_name = $record->ContractNumber;
            } else if ($module_name == 'order' && isset($record->OrderNumber)) {
                $record_name = $record->OrderNumber;
            } else if (isset($record->Name)) {
                $record_name = $record->Name;
            }

            $edit_option = false;
            if (isset($_SESSION['bcp_module_access_data']->$module_name->edit) && $_SESSION['bcp_module_access_data']->$module_name->edit == 'true') {
                $edit_option = true;
                if ($module_name == 'case' && $record->IsClosed == 1) {
                    $edit_option = FALSE;
                }
            }
            $delete_option = false;
            if (isset($_SESSION['bcp_module_access_data']->$module_name->delete) && $_SESSION['bcp_module_access_data']->$module_name->delete == 'true' && $module_name != 'account') {
                $delete_option = true;
            }

            $subpanel_option = false;
            $bcp_subpanel = array();
           
            if (isset($_SESSION['bcp_module_access_data']->$module_name->subPanel) && $_SESSION['bcp_module_access_data']->$module_name->subPanel == 'true') {
                $subpanel_option = true;
                $bcp_subpanel = $_SESSION['bcp_portalSubPanel']->$module_name;
                
                if (!empty($bcp_subpanel)) {
                    $bcp_subpanel = json_decode(json_encode($bcp_subpanel), true);
                    foreach ($bcp_subpanel as $j => $subpanel) {
                        if ($module_name == strtolower($subpanel['moduleName'])) {
                            continue;
                        }
                        if (!array_key_exists(strtolower($subpanel['moduleName']), $_SESSION['bcp_modules']) && $subpanel['moduleName'] != "Product") {
                            unset($bcp_subpanel[$j]);
                            continue;
                        }
                        if ($module_name == 'case' && (strtolower($subpanel['moduleName']) == 'contentnote' || strtolower($subpanel['moduleName']) == 'contentdocument')) {
                            unset($bcp_subpanel[$j]);
                            continue;
                        }
                    }
                    $bcp_subpanel = array_values($bcp_subpanel);
                }
            }

            if ($record != null) {
                $layout_fields = array();
                if ($layout_data->fields != null) {
                    foreach ($layout_data->fields as $layout_data_value) {
                        $layout_fields[$layout_data_value->name] = $layout_data_value;
                    }
                }
                $field_object[] = array();
                $i = 0;
                $title = '';

                foreach ($section_layout as $value1) {
                    $rows = $value1->Field_rows;
                    $field_object[$i]['section_name'] = $value1->Section_name;
                    $m = 0;
                    foreach ($rows as $key2 => $value2) {
                        $value2 = (array) $value2;
                        $row_array = array_values($value2);
                        $j = 0;

                        foreach ($row_array as $value) {
                            if (isset($layout_fields[$value->name]) && $layout_fields[$value->name] != '' && ($value->name != '')) {
                                $layout_label = $value->label;
                                $layout_name = $value->name;
                                $layout_field = $layout_fields[$layout_name];

                                $field_name = $layout_field;
                                if (isset($layout_field->name)) {
                                    $field_name = $layout_field->name;
                                }

                                $value = ( ( is_object($record) && isset($record->$field_name) ) ? $record->$field_name : '' );
                                $filetype = '';
                                if ($field_name == 'FileExtension') {
                                    $filetype = $value;
                                }
                                
                                if ($field_name == 'ContentType') {
                                    $filetype = $value;
                                }

                                if ($layout_fields[$layout_name]->type == 'reference') {

                                    $relate_module_name = strtolower($layout_fields[$layout_name]->referenceTo[0]);
                                    // Get Relate field Value in list call by passing relationship and field name
                                    if ($layout_fields[$layout_name]->type == "reference") {
                                        $relation_field_name = $layout_fields[$layout_name]->relationshipName;
                                        $ref_object_name = $layout_fields[$layout_name]->referenceTo[0];

                                        $value = ( ( is_object($record) && isset($record->$relation_field_name->Name) ) ? stripslashes($record->$relation_field_name->Name) : '' );
                                        if ($ref_object_name == 'Case') {
                                            $value = ( ( is_object($record) && isset($record->$relation_field_name->Subject) ) ? stripslashes($record->$relation_field_name->Subject) : '' );
                                        }
                                    }
                                }
                                if ($layout_fields[$layout_name]->type == 'currency' && $value) {
                                    $value = $_SESSION['bcp_default_currency'] . ' ' . number_format($value, 2);
                                } elseif ($layout_fields[$layout_name]->type == 'multipicklist' && $value) {
                                    $value = str_replace(";", " , ", $value);
                                } elseif ($layout_fields[$layout_name]->type == 'datetime' && $value) {
                                    $value = $this->bcp_show_date($value);
                                } elseif ($layout_fields[$layout_name]->type == 'boolean') {
                                    $value = ( $value ? "Checked" : "Not Checked" );
                                } elseif ($value && $layout_fields[$layout_name]->type == 'time') {
                                    $value = date("H:i", strtotime($value));
                                }

                                $value = stripslashes($value);
                                $value = htmlentities($value);
                                $value = nl2br($value);
                                $value = __($value, "bcp_portal");
                                if ($layout_name == 'Title') {
                                    $title = (!( is_object($value) ) && $value != NULL ? $value : '-' );
                                }
                                if ($layout_name == 'Name') {
                                    $title = (!( is_object($value) ) && $value != NULL ? $value : '-' );
                                }
                                if (isset($layout_name)) {

                                    if ($layout_name == 'FileExtension' || $layout_name == 'ContentType') {
                                        $field_object[$i]['rows'][$m][$j]['lable'] = 'Download';
                                        $field_object[$i]['rows'][$m][$j]['value'] = '<a title="Download" href="' . admin_url('admin-ajax.php') . '?action=bcp_attachment_download&record_id=' . $record_id . '&title=' . $title . '&type=' . $filetype . '" class="scp-download sfcp-btn-download"><span class="lnr lnr-download"></span>  </a><a title="Preview" href="' . admin_url('admin-ajax.php') . '?action=bcp_attachment_download&record_id=' . $record_id . '&title=' . $title . '&type=' . $value . '&bcp_file_preview=1" class="sfcp-btn-download" target="_blank"><span class="lnr lnr-eye"></span> </a>';
                                    } else {
                                        $label = ( isset($layout_field_properties[$layout_name]['portal_lable']) && !empty($layout_field_properties[$layout_name]['portal_lable']) ) ? $layout_field_properties[$layout_name]['portal_lable'] : $layout_label;
                                        $helptip = ( isset($layout_field_properties[$layout_name]['help_tip']) && !empty($layout_field_properties[$layout_name]['help_tip']) ) ? $layout_field_properties[$layout_name]['help_tip'] : "";
                                        $field_object[$i]['rows'][$m][$j]['helptip'] = $helptip;
                                        $field_object[$i]['rows'][$m][$j]['lable'] = $label;
                                        $field_object[$i]['rows'][$m][$j]['value'] = (!( is_object($value) ) && $value != NULL ? $value : '-' );
                                    }
                                    $j++;
                                }
                            }
                        }
                        $m++;
                    }
                    $i++;
                }
            }

            foreach ($field_object as $k => $fieldsobject) {
                if (!isset($fieldsobject['rows']) && empty($fieldsobject['rows'])) {
                    unset($field_object[$k]);
                }
            }
            //sort($field_object);

            $casecomments_show = false;
            $attachments_show = false;
            $solutions_show = false;
            $product_show = false;
            $notes_show = false;
            $attachments = '';
            $comments = '';
            $products = '';
            $notes = "";
            $count_show_data = 0;
            if ($module_name == 'case' && isset($_REQUEST['submodule']) && $_REQUEST['submodule'] != '') {
                $solutions_show = true;
                $count_show_data++;
            } else {
                if ($module_name == 'case') {
                    $casecomments_show = true;
                    $count_show_data++;
                    $comments = $case_record->comments;
                }
                if (isset($_SESSION['bcp_module_access_data']->contentdocument->create) && $_SESSION['bcp_module_access_data']->contentdocument->create == 'true' && $module_name == 'case') {
                    $attachments_show = true;
                    $count_show_data++;
                    $attachments = $case_record->attachments;
                }
                if (isset($_SESSION['bcp_module_access_data']->contentnote->create) && $_SESSION['bcp_module_access_data']->contentnote->create == 'true' && $module_name == 'case') {
                    $notes_show = true;
                    $count_show_data++;
                    $arguments = array(
                        "cid" => $cid,
                        "record" => $record_id,
                        "relationship_field" => $relationship_field_key,
                    );
                    $notes_response = $objCp->getCaseNotes($arguments);
                    $notes = (isset($notes_response->success) && $notes_response->success ) ? $notes_response->data : array();
                }
            }
            if (isset($case_record->products) && in_array($module_name, $this->modules_with_products)) {
                $product_show = true;
                $count_show_data++;
                $products = $case_record->products;
            }

            $IsClosed = isset($record->IsClosed) ? $record->IsClosed : "";

            $detail_template = '/detail/' . $view_style . '.php';

            $data = array(
                'module_name' => $module_name,
                'heading_modulename' => $heading_modulename,
                'heading' => 'Detail',
                'record_id' => $record_id,
                'record_name' => $record_name,
                'add_option' => $add_option,
                'edit_option' => $edit_option,
                'delete_option' => $delete_option,
                'subpanel_option' => $subpanel_option,
                'add_module_url' => $add_module_url,
                'edit_module_url' => $edit_page_url,
                'module_singular_name' => $module_singular_name,
                'casecomments_show' => $casecomments_show,
                'attachments_show' => $attachments_show,
                'solutions_show' => $solutions_show,
                'notes_show' => $notes_show,
                'fields' => $field_object,
                'comments' => $comments,
                'attachments' => $attachments,
                'notes' => $notes,
                'product_show' => $product_show,
                'products' => $products,
                'module_icon' => $module_icon,
                'module_sub_title' => $module_sub_title,
                'view_style' => $view_style,
                'IsClosed' => $IsClosed,
                'subpanels' => $bcp_subpanel,
                'subtab' => isset($_REQUEST['subtab']) ? $_REQUEST['subtab'] : "",
                'count_show_data' => $count_show_data,
                'bcp_portal_template' => $bcp_portal_template,
                'bcp_helper' => $this->bcp_helper,
                'action_class' => new BCPCustomerPortalActions($this->plugin_name, $this->version),
                'RecordCreatedByContact' => $RecordCreatedByContact
            );

            $this->template->render($detail_template, $data);
        } elseif (file_exists($request_module_file)) {

            include( $request_module_file );
            if (function_exists($request_function)) {
                echo $request_function($module_name, $_REQUEST['record_id']);
            } else {
                ?>
                <span class='error error-line error-msg settings-error'><?php _e('Please set layout in Salesforce.', 'bcp_portal'); ?></span>
                <?php
            }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Please set layout in Salesforce.', 'bcp_portal'); ?></span>
            <?php
        }
        echo '<input type="hidden" name="url" class="module-url" value="' . home_url('/customer-portal/' . $module_name . '/list') . '" />';

        wp_die();
    }

    /**
     * Get page content data using Dashboard page
     *
     * @since 1.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    function bcp_page_load_callback() {
        global $objCp, $counterData, $recentActivityData, $chartData, $post;
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        if (isset($bcp_authentication) && $bcp_authentication != null && $objCp->access_token != '') {
            $bcp_licence = fetch_data_option('bcp_licence');
            if ($bcp_licence) {
                if (isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id'] != null) {
                    $page_id = get_the_ID();
                    $contentNoteCounter = array();
                    $contentNoteActivity = array();
                    $counter_datas = get_post_meta($page_id, 'sfcp_page_counter_data', true);
                    $recent_activity_datas = get_post_meta($page_id, 'sfcp_page_activity_data', true);
                    $chart_datas = get_post_meta($page_id, 'sfcp_page_chart_data', true);
                    if ($counter_datas) {
                        $counter_datas = array_values($counter_datas);
                        foreach ($counter_datas as $i => $counter_info) {
                            if (property_exists($_SESSION['bcp_module_access_data'], $counter_info['module_name'])) {
                                $module_name = $counter_info['module_name'];
                              
                                if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
                                    $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->accountRelationship;
                                    
                                    if($module_name == "account"){
                                        $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                                    }
                                }else{
                                    $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                                }
                                
                               
                                $counter_datas[$i]['relationship_field'] = $relationship_field_key;
                                if ($module_name == "contentnote") {
                                    $contentNoteCounter = $counter_datas[$i];
                                    unset($counter_datas[$i]);
                                }
                                 /** check if relationship is empty */
                                if($relationship_field_key == ""){
                                    unset($counter_datas[$i]);
                                }
                            } else {
                                unset($counter_datas[$i]);
                            }
                        }
                    } else {
                        $counter_datas = array();
                    }
                   
                    $counter_datas = array_map("unserialize", array_unique(array_map("serialize", $counter_datas)));
                    sort($counter_datas);
                 
                    if ($recent_activity_datas) {
                        $recent_activity_datas = array_values($recent_activity_datas);
                    } else {
                        $recent_activity_datas = array();
                    }

                    if ($chart_datas) {
                        $chart_datas = array_values($chart_datas);
                        foreach ($chart_datas as $j => $chart_info) {
                            if (property_exists($_SESSION['bcp_module_access_data'], $chart_info['module_name'])) {
                                $module_name = $chart_info['module_name'];
                                if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
                                    $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->accountRelationship;
                                    if($module_name == "account"){
                                        $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                                    }
                                   
                                }else{
                                    $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                                }
                                $chart_datas[$j]['relationship_field'] = $relationship_field_key;
                                 /** check if relationship is empty */
                                if($relationship_field_key == ""){
                                    unset($chart_datas[$j]);
                                }
                            } else {
                                unset($chart_datas[$j]);
                            }
                        }
                    } else {
                        $chart_datas = array();
                    }
                    sort($chart_datas);

                    
                    if ($counter_datas != '' || $recent_activity_datas != '' || $chart_datas != '') {
                        $counter = true;

                        $recentActivitys = array();
                        $i = 0;
                        if (!empty($recent_activity_datas)) {
                            foreach ($recent_activity_datas as $item) {
                                $module_name = $item['module_name'];
                                if (property_exists($_SESSION['bcp_module_access_data'], $module_name)) {
                                    if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
                                        $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->accountRelationship;
                                        if($module_name == "account"){
                                            $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                                        }
                                    }else{
                                        $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                                    }
                                    unset($item['module_name']);
                                    $fields = array_values($item);
                                    $fields = array_unique($fields);
                                    $recentActivitys[$i]['module_name'] = $module_name;
                                    $recentActivitys[$i]['field'] = $fields;
                                    $recentActivitys[$i]['relationship_field'] = $relationship_field_key;
                                    if ($module_name == "contentnote") {
                                        $contentNoteActivity = $recentActivitys[$i];
                                        unset($recentActivitys[$i]);
                                        continue;
                                    }
                                    /** check if relationship is empty */
                                    if($relationship_field_key == ""){
                                        unset($recentActivitys[$i]);
                                    }
                                    $i++;
                                } else {
                                    unset($recentActivitys[$i]);
                                }
                            }
                        }
                        sort($recentActivitys);

                        /* counter check */
                        if (!empty($counter_datas)) {
                            if ($counterData != '') {
                                foreach ($counter_datas as $item) {
                                    // module_name condition check
                                    if ((in_array($item['module_name'], array_column($counterData, 'module_name'))) && (in_array($item['field'], array_column($counterData, 'field'))) && (in_array($item['field_value'], array_column($counterData, 'field_value')))) {
                                        $counter = true;
                                    } else {
                                        $counter = false;
                                        break;
                                    }
                                }
                            } else {
                                $counter = false;
                            }
                        }

                        /* recent_activity_data check */
                        if (!empty($recent_activity_datas)) {
                            if ($recentActivityData != '') {
                                foreach ($recent_activity_datas as $item) {
                                    // module_name condition check
                                    if ((in_array($item['module_name'], array_column($recentActivityData, 'module_name'))) && (in_array($item['field1_selected_option'], array_column($recentActivityData, 'field1_selected_option'))) && (in_array($item['field2_selected_option'], array_column($recentActivityData, 'field2_selected_option'))) && (in_array($item['field3_selected_option'], array_column($recentActivityData, 'field3_selected_option'))) && (in_array($item['field4_selected_option'], array_column($recentActivityData, 'field4_selected_option')))) {
                                        $counter = true;
                                    } else {
                                        $counter = false;
                                        break;
                                    }
                                }
                            } else {
                                $counter = false;
                            }
                        }

                        /* chart_data check */
                        if (!empty($chart_datas)) {
                            if ($chartData != '') {
                                foreach ($chart_datas as $item) {
                                    // module_name condition check
                                    if ((in_array($item['module_name'], array_column($chartData, 'module_name'))) && (in_array($item['field'], array_column($chartData, 'field')))) {
                                        $counter = true;
                                    } else {
                                        $counter = false;
                                        break;
                                    }
                                }
                            } else {
                                $counter = false;
                            }
                        }

                        /* set dashboard call and set session data */
                        if ($counter == null) {
                            $counterData = '';
                            $recentActivityData = '';
                            $chartData = '';
                            $contentNoteResponse = "";
                            $contact_id = $_SESSION['bcp_contact_id'];
                            if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
                                $contact_id = $_SESSION['bcp_account_id'];
                                if($module_name == "account"){  
                                    $contact_id = $_SESSION['bcp_contact_id'];
                                }
                            }else{
                                $contact_id = $_SESSION['bcp_contact_id'];
                            }
                            $arguments = array(
                                "cid" => $contact_id,
                                "counter_datas" => $counter_datas,
                                "recentActivitys" => $recentActivitys,
                                "chart_datas" => $chart_datas,
                            );
                            
                            $dashboard_list = $objCp->getPortalDashboard($arguments);
                           
                            if (isset($dashboard_list->response->errorCode) && $dashboard_list->response->errorCode == 'ENTITY_IS_DELETED') {
                                $_SESSION['bcp_error_message'] = $dashboard_list->response->message;
                                unset($_SESSION['bcp_contact_id']);
                            }

                            $bcp_counter = (isset($dashboard_list->sfcp_counter)) ? $dashboard_list->sfcp_counter : array();
                            $recent_activity = (isset($dashboard_list->sfcp_recent_activity)) ? $dashboard_list->sfcp_recent_activity : array();
                            $bcp_chart = ( isset($dashboard_list->sfcp_chart) ) ? $dashboard_list->sfcp_chart : array();

                            if (!empty($contentNoteActivity) || !empty($contentNoteCounter)) {
                                $ContentNoteArguments = array(
                                    "cid" => $contact_id,
                                    "counter_datas" => $contentNoteCounter,
                                    "recentActivitys" => $contentNoteActivity,
                                );
                                $contentNoteResponse = $objCp->getPortalContentNoteDashboard($ContentNoteArguments);
                                if (isset($contentNoteResponse->success) && isset($contentNoteResponse->sfcp_counter) && !empty($contentNoteResponse->sfcp_counter)) {
                                    array_push($bcp_counter, $contentNoteResponse->sfcp_counter[0]);
                                }

                                if (isset($contentNoteResponse->success) && isset($contentNoteResponse->sfcp_recent_activity) && !empty($contentNoteResponse->sfcp_recent_activity)) {
                                    array_push($recent_activity, $contentNoteResponse->sfcp_recent_activity[0]);
                                }
                            }
                            $counterData = $bcp_counter;
                            $recentActivityData = $recent_activity;
                            $chartData = $bcp_chart;
                        }
                    }
                }
            }
        }
    }

    /**
     * Function to update contact layout and return layout
     * 
     * @global type $wpdb
     * @global type $sf
     * @global type $bcp_field_prefix
     * @global type $objCp
     * @return type
     * @author Akshay Kungiri 
     */
    function bcp_update_contact_layout() {
        global $wpdb, $sf, $bcp_field_prefix, $objCp;
        $module_name = "contact";

        $bcp_portal_role = get_option('sfcp_primary_role');

        $layout = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts WHERE module='{$module_name}' AND view='edit' AND role='" . $bcp_portal_role . "'");
        return $layout;
    }

    /**
     * Edit method for all modules
     *
     * @since 1.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    function bcp_edit_portal_callback() {
        global $wpdb, $sf, $bcp_field_prefix, $objCp;
       
        $module_name = $_REQUEST['module_name'];
        $view_style = $_REQUEST['view_style'];
        $record_id = isset($_REQUEST['record_id']) ? $_REQUEST['record_id'] : "";
        $relationship = isset($_REQUEST['relationship']) ? $_REQUEST['relationship'] : "";
        $relate_module_name = isset($_REQUEST['relate_module_name']) ? $_REQUEST['relate_module_name'] : "";
        $relate_module_id = isset($_REQUEST['relate_module_id']) ? $_REQUEST['relate_module_id'] : "";

        if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
            $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->accountRelationship;
            $contact_id = $_SESSION['bcp_account_id'];
            if($module_name == "account"){
                $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                $contact_id = $_SESSION['bcp_contact_id'];
            }
           
        }else{
            $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
            $contact_id = $_SESSION['bcp_contact_id'];
        }

        /***Set contact id for contact module for account based */
        if (isset($_SESSION['bcp_contact_id']) && $module_name == "contact") {
            $contact_id = $_SESSION['bcp_contact_id'];
            $record_id = $contact_id;
        }
        $bcp_portal_role = "";
        $record_products = "";
        $bcp_modules = isset($_SESSION['bcp_modules']) ? $_SESSION['bcp_modules'] : array();
        $Username__c = $bcp_field_prefix . 'Username__c'; // For Sigup Form Submit
        $Password__c = $bcp_field_prefix . 'Password__c'; // For Sigup Form Submit
        $enable_portal__c = $bcp_field_prefix . 'enable_portal__c';
        $Customer_Portal_Role__c = $bcp_field_prefix . 'Customer_Portal_Role__c';
        $exclude_fields = array($Password__c, $enable_portal__c, $Customer_Portal_Role__c);

        $bcp_portal_template = fetch_data_option('bcp_portal_template');
        $bcp_login_page = !empty(fetch_data_option('bcp_login_page')) ? get_permalink(fetch_data_option('bcp_login_page')) : site_url();

        if ($module_name == "contact") {
            $layout = $this->bcp_update_contact_layout();
        } else {
            if (in_array($module_name, $this->different_name_modules)) { 
                $layout = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts WHERE module='$module_name' AND view='edit'");
            } else {
              
                $bcp_portal_role = isset($_SESSION['bcp_portal_role']) ? $_SESSION['bcp_portal_role'] : "";
                $layout = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts WHERE module='$module_name' AND view='edit' AND role='$bcp_portal_role'");
            }
        }
        $field_definition_result = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_fields WHERE module='$module_name'");
       
        if ($layout != null) {
            $layout->layout = isset($field_definition_result->fields) ? unserialize($field_definition_result->fields) : "";
            $section_layout = (array) json_decode($layout->section_layout);
            $module_icon = $layout->icon;
            $module_title = $layout->title;
            $module_sub_title = $layout->sub_title;
            if ($section_layout != null) {

                if (isset($_SESSION['bcp_contact_id'])) {
                    if ($module_title != '') {
                        $display_name = $module_title;
                    } else {
                        if ($module_name == "contact") {
                            $display_name = __("Profile");
                        } else {
                            if ($record_id != '' && $record_id != '0') {
                                $display_name = sprintf(__('Edit %s', 'bcp_portal'), __($bcp_modules[$module_name]['singular'], 'bcp_portal'));
                            } else {
                                $display_name = sprintf(__('Add %s', 'bcp_portal'), __($bcp_modules[$module_name]['singular'], 'bcp_portal'));
                            }
                        }
                    }
                } else {
                    $display_name = $_REQUEST['form_title'];
                }
                if ($module_name == "contentdocument") {
                    if ($record_id != '' && $record_id != '0') {
                        $display_name = sprintf(__('Edit %s', 'bcp_portal'), __('File', 'bcp_portal'));
                    } else {
                        $display_name = sprintf(__('Add %s', 'bcp_portal'), __('File', 'bcp_portal'));
                    }
                }
                $record = '';

                if ($record_id) {
                    $data_fields = array();
                    foreach ($section_layout as $bcp_layout_sectionlayout) {
                        $rows = $bcp_layout_sectionlayout->Field_rows;
                        foreach ($rows as $value2) {
                            $value2 = (array) $value2;
                            $row_array = array_values($value2);
                            foreach ($row_array as $value3) {
                                if (!empty($value3->name)) {
                                    $data_fields[] = $value3->name;
                                }
                            }
                        }
                    }
                    $others = array();
                    $others['relationship_field'] = $relationship_field_key;
                    if ($module_name == 'order') {
                        $others['showorderproduct'] = TRUE;
                    }

                    if ($module_name == 'opportunity') {
                        $others['showopportunityproduct'] = TRUE;
                    }
                  
                    if ($module_name == "contact") {
                        $where_cond = array(
                            "Id" => $record_id,
                        );
                        $argemunts = array(
                            "module" => $module_name,
                            "where" => $where_cond,
                            "fields" => $layout->fields,
                            "others" => $others
                        );
                        $records = $objCp->getModuleRecords($argemunts);
                        if (isset($records->response->errorCode) && $records->response->errorCode == 'ENTITY_IS_DELETED') {
                            $_SESSION['bcp_error_message'] = $records->response->message;
                            unset($_SESSION['bcp_contact_id']);
                        }
                        $record = isset($records->records[0]) ? $records->records[0] : array();
                    } else {
                        $tmp_module_name = $module_name;
                        if ($module_name == "call") {
                            $tmp_module_name = "task";
                        }

                        if ($module_name == "case" && !in_array('IsClosed', $data_fields)) {
                            $data_fields[] = 'IsClosed';
                        }

                        $arguments = array(
                            "cid" => $contact_id,
                            "module_name" => $tmp_module_name,
                            "fields" => $data_fields,
                            "record_id" => $record_id,
                            "others" => $others
                        );
                       
                        $record = $objCp->getPortalDetailData($arguments);
                       
                        if (isset($record->success) && isset($record->data) && $record->data != "") {

                            if (isset($record->products) && is_array($record->products) && count($record->products) > 0) {
                                $record_products = $record->products;
                            }
                            if ($module_name == 'case' && $record->data->IsClosed == 1) {
                                echo "<span class='error error-line error-msg settings-error'> " . __('You don\'t have sufficient permission to access this data.', 'bcp_portal') . "</span>";
                                wp_die();
                            } else {
                                $record = $record->data;
                            }
                        } else {
                            echo "<span class='error error-line error-msg settings-error'> " . __('You don\'t have sufficient permission to access this data.', 'bcp_portal') . "</span>";
                            wp_die();
                        }
                    }
                    $record = (array) $record;
                }
                if (empty($record_id) || (!empty($record_id) && !empty((array) $record))) {
                   
                    $apiEditFormatter = new ApiFormatter();
                    $fields_object = $apiEditFormatter->getFormattedFormCall($layout, $record, $module_name, $record_id, $exclude_fields);
                   
                  
                    if ($module_name == "contact" && !empty($record_id)) {
                        $form_template = '/profile/' . $view_style . '.php';
                    } else {
                        $form_template = 'form/' . $view_style . '.php';
                        if (class_exists('BCPCustomerPortalWPML') && $module_name == "contact") {
                            $this->template = new TemplateHelper(BCPWPML_PATH . "/partials/");
                            $form_template = 'public/form/' . $view_style . '.php';
                        }
                    }
                    $edit_profile_title = '';
                    $change_password_text = '';
                    $edit_profile_save_label = '';
                    $change_password_save_label = '';

                    if ($module_name == "contact" && $contact_id) {
                        $edit_profile_title = $_REQUEST['edit_profile_title'];
                        $change_password_text = $_REQUEST['change_password_text'];
                        $edit_profile_save_label = $_REQUEST['edit_profile_save_label'];
                        $change_password_save_label = $_REQUEST['change_password_save_label'];
                    }

                    $show_product_selection = false;
                    $available_pricebooks = false;
                    $available_products = '';
                    //@author: Akshay Kungiri. Change: Key product_selection to productSelection bec in Login API Call.
                    $productSelection_data = isset($_SESSION['bcp_module_access_data']->$module_name->productSelection) ? $_SESSION['bcp_module_access_data']->$module_name->productSelection : "";
                    if ($productSelection_data == 'true') {
                        $show_product_selection = true;
                        
                        $available_products = $objCp->getPricebooks();
                        
                        if ($available_products->success && $available_products->totalSize > 0) {
                            $available_pricebooks = array();
                            foreach ($available_products->records as $pricebook) {
                                $available_pricebooks[$pricebook->Id] = $pricebook->Name;
                            }
                        }
                    }

                    $data = array(
                        'module_name' => $module_name,
                        'relationship_field' => $relationship_field_key,
                        'fields' => $fields_object,
                        'heading_module' => $display_name,
                        'contact_id' => $contact_id,
                        'record_id' => $record_id,
                        'bcp_login_page' => $bcp_login_page,
                        'module_icon' => $module_icon,
                        'module_sub_title' => $module_sub_title,
                        'bcp_portal_template' => $bcp_portal_template,
                        'edit_profile_title' => $edit_profile_title,
                        'change_password_text' => $change_password_text,
                        'edit_profile_save_label' => $edit_profile_save_label,
                        'change_password_save_label' => $change_password_save_label,
                        'show_product_selection' => $show_product_selection,
                        'available_pricebooks' => $available_pricebooks,
                        'order_products' => $record_products,
                        'record_fields' => $record,
                        'relationship' => $relationship,
                        'relate_modulename' => $relate_module_name,
                        'relate_module_id' => $relate_module_id,
                        'bcp_helper' => new BCPCustomerPortalHelper($this->plugin_name, $this->version),
                    );
                    $this->template->render($form_template, $data);
                } else {
                    ?>
                    <span class='error error-line error-msg settings-error'><?php _e('You don\'t have sufficient permission to access this data.', 'bcp_portal'); ?></span>
                    <?php
                }
            } else {
                ?>
                <span class='error error-line error-msg settings-error'><?php _e('Please set layout in Salesforce.', 'bcp_portal'); ?></span>
                <?php
            }
        } elseif (file_exists($request_module_file)) {
            include( $request_module_file );
            if (function_exists($request_function)) {
                echo $request_function($module_name, $record_id);
            } else {
                ?>
                <span class='error error-line error-msg settings-error'><?php _e('Please set layout in Salesforce.', 'bcp_portal'); ?></span>
                <?php
            }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Please set layout in Salesforce.', 'bcp_portal'); ?></span>
            <?php
        }
        wp_die();
    }

    /**
     * Get Products based on Pricebook
     * @since 4.0.0
     * @author Akshay Kungiri 
     */
    public function bcp_get_products_callback() {
        global $wpdb, $bcp_field_prefix, $objCp;
        
        $per_page = 5;
        $contain_products = array();
        $show_load_more = false;
        $productlist = array();
        $selected_products = array();
        $final_products = array();
        $selected_product_ids = array();
        $selected_product_line_items_ids = array();

        $price_book = $_REQUEST['price_book'];
        $curent_page = $_REQUEST['page'];
        $module_name = $_REQUEST['module_name'];

        $current_page = ($curent_page == 0 ? 1 : $curent_page);
        $page_offset = ($current_page - 1) * $per_page;
        $productlist['total_products'] = 0;
        $show_productSelection = true;
        $operation_mode = 'Add';
      
        if (isset($_POST['contain_products']) && $_POST['contain_products'] && is_array($_POST['contain_products']) && count($_POST['contain_products'])) {
            $contain_products = $_POST['contain_products'];
            $operation_mode = 'Edit';
        }

        if ($operation_mode == 'Edit' && $module_name == 'order') {
            $show_productSelection = false;
        }

        if ($current_page == 1 && isset($contain_products) && is_array($contain_products) && count($contain_products) > 0) {

            $contain_products = $_POST['contain_products'];

            if (isset($contain_products) && is_array($contain_products) && count($contain_products) > 0) {

                $productlist['total_products'] = count($contain_products);
                $selected_product_ids = array_column($contain_products, 'Product2Id');
                
                foreach ($contain_products as $key => $exitsting_record) {

                    $selected_products[$key]['PriceBookEntryId'] = $exitsting_record['PriceBookEntryId'];
                    $selected_products[$key]['product_id'] = $exitsting_record['Product2Id'];
                    $selected_products[$key]['price'] = $exitsting_record['UnitPrice'];
                    $selected_products[$key]['product_name'] = $exitsting_record['Name'];
                    $selected_products[$key]['product_code'] = $exitsting_record['ProductCode'];
                    $selected_products[$key]['product_quantity'] = $exitsting_record['Quantity'];
                    $selected_products[$key]['Id'] = $exitsting_record['Id'];
                }
            }
        }

        if ($show_productSelection && ( isset($price_book) && $price_book != '' )) {

            $fields = array(
                "Id" => "Id",
                "Product2Id" => "Product id",
                "Product2.Name" => "Name",
                "Product2.ProductCode" => "Product Code",
                "Product2.Description" => "Description",
                "Product2.Family" => "Family",
                "Product2.DisplayURL" => "DisplayURL",
                "Product2.CreatedDate" => "CreatedDate",
                "UnitPrice" => "Price"
            );

            $fields = json_encode($fields);

            $where = array( 'PriceBook2Id' => $price_book, 'Product2.' . $bcp_field_prefix . 'visible__c' => true, 'IsActive' => TRUE );

            if (isset($selected_product_ids) && is_array($selected_product_ids) && count($selected_product_ids) > 0) {
                $where['Product2Id'] = $selected_product_ids;
            }

            $records = $objCp->getRecords('PriceBookEntry', $where, $fields, 'asc', 'Product2.CreatedDate', $per_page, $page_offset);
            
            if (isset($records->success) && isset($records->records) && count($records->records) > 0) {
                $productlist['total_products'] += $records->totalSize;
                $all_products = $records->records;

                foreach ($all_products as $key => $product_record) {
                    $productlist['product'][$key]['PriceBookEntryId'] = $product_record->Id;
                    $productlist['product'][$key]['product_id'] = $product_record->Product2Id;
                    $productlist['product'][$key]['price'] = $product_record->UnitPrice;
                    $productlist['product'][$key]['product_name'] = $product_record->Product2->Name;
                    $productlist['product'][$key]['product_code'] = $product_record->Product2->ProductCode;
                    $productlist['product'][$key]['product_quantity'] = '1';
                }

                if ($per_page * $curent_page < $records->totalSize) {
                    $show_load_more = true;
                    $curent_page++;
                }
            } else if ( !empty($selected_product_ids) ) {
                $productlist['total_products'] = count($selected_product_ids);
            } else {
                $productlist['total_products'] = 0;
            }
        }

        $final_products = isset($productlist['product']) ? $productlist['product'] : array();

        if (isset($selected_products) && is_array($selected_products) && count($selected_products) > 0) {
            $final_products = $selected_products;
        }

        if (( isset($productlist['product']) && is_array($productlist['product']) && count($productlist['product']) > 0 ) && (isset($selected_products) && is_array($selected_products) && count($selected_products) > 0)) {
            $final_products = array_merge($selected_products, $productlist['product']);
        }

        if (isset($final_products) && is_array($final_products) && $productlist['total_products'] > 0) {
            $product_html = '';
            ob_start();

            foreach ($final_products as $product_data) {
                $is_checked = '';
                $qty_disabled = " disabled=disabled ";
                $checkbox_disabled = '';
                if (count($contain_products)) {

                    if (in_array($product_data['product_id'], array_column($contain_products, 'Product2Id'))) {
                        $is_checked = " checked=checked ";
                        $qty_disabled = '';
                        if ($module_name == 'order') {
                            $checkbox_disabled = " disabled=disabled ";
                        }
                    }
                }
                $product_data_id = isset($product_data['Id']) ? $product_data['Id'] : "";
                $productLineItemsName = "productLineItems";
                $key = $product_data['product_id'];
                if ($product_data_id != "") {
                    $productLineItemsName = "selectedProductLineItems";
                    $key = $product_data['Id'];
                }
                $selected_product_line_items_ids = array_column($contain_products, 'Id');
                /****** */
                
                ?>
                <tr title="<?php echo $product_data['product_name']; ?>">
                    <td>
                        <input type="checkbox" class="bcp-product select-product-chk" name="selected-product[]" value="<?php echo $key; ?>" <?php
                        echo $checkbox_disabled;
                        echo $is_checked;
                        ?> >
                        <input type="hidden" name="<?php echo $productLineItemsName.'['.$key.']'; ?>[ProductId]" value="<?php echo $product_data['product_id']; ?>">
                        <input type="hidden" name="<?php echo $productLineItemsName.'['.$key.']'; ?>[ProductName]" value="<?php echo $product_data['product_name']; ?>">
                        <input type="hidden" name="<?php echo $productLineItemsName.'['.$key.']'; ?>[ProductPrice]" value="<?php echo $product_data['price']; ?>">
                        <input type="hidden" name="<?php echo $productLineItemsName.'['.$key.']'; ?>[PriceBookEntryId]" value="<?php echo $product_data['PriceBookEntryId']; ?>">
                        <input type="hidden" name="<?php echo $productLineItemsName.'['.$key.']'; ?>[Id]" value="<?php echo $product_data_id; ?>">
                        
                        <?php if ($module_name == 'order' && ( isset($is_checked) && $is_checked != '' )) { ?>
                        <input type="hidden" name="selected-product[]" value="<?php echo $key; ?>">
                        <?php } ?>
                    </td>

                    <td class="bcp_product_name"><?php echo $product_data['product_name']; ?></td>
                    <td class="bcp_product_unit_price"><?php echo $_SESSION['bcp_default_currency'] . ' ' . number_format(($product_data['price']), 2, '.', ','); ?></td>
                    <td class="bcp_product_select_qty">
                        <input type="number" min="1" max="100000000000" class="product_qty form-control" name="<?php echo $productLineItemsName.'['.$key.']'; ?>[Quantity]" value="<?php echo $product_data['product_quantity']; ?>" <?php echo $qty_disabled; ?> >
                    </td>
                    <td class="bcp_product_total_price" data-amount="<?php echo $product_data['price']; ?>" data-symbol="<?php echo $_SESSION['bcp_default_currency']; ?>"> <?php echo $_SESSION['bcp_default_currency'] . ' ' . number_format(($product_data['price'] * $product_data['product_quantity']), 2, '.', ','); ?> </td>
                </tr>
                <?php
            }
            ?>
            <input type="hidden" name="selected_product_ids" value='<?php echo json_encode($selected_product_line_items_ids); ?>' />
            <?php
            $product_html .= ob_get_clean();
            echo json_encode(array('status' => true, 'show_load_more' => $show_load_more, 'next_page' => $curent_page, 'productlist_rows' => $product_html));
            wp_die();
        } else {
            echo json_encode(array('status' => false, 'message' => 'There was some error while fetching the products'));
            wp_die();
        }
    }

    /**
     * Get KBDocuments data
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_KBDocuments() {

        global $objCp;
        $bcp_field_prefix_org = fetch_data_option('bcp_org_prefix');
        if ($bcp_field_prefix_org != '') {
            $bcp_field_prefix_org = rtrim($bcp_field_prefix_org, '_') . '__';
        }

        $bcp_modules = $_SESSION['bcp_modules'];
        $module_name = $_REQUEST['module_name'];
        $view_style = isset($_REQUEST['view_style']) ? $_REQUEST['view_style'] : 'style_1';

        // Use this to load file dynamically and use dynamic function
        $kblist_template = '/knowledgebase/' . $view_style . '.php';

        $paged = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1;
        $limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
        $limit = (int) $limit;
        $offset = ( ( $paged * $limit ) - $limit);
        $searchterm = isset($_REQUEST['search_term']) ? $_REQUEST['search_term'] : "";
        $cid = $_SESSION['bcp_contact_id'];
        $module_display_name = isset($bcp_modules[$module_name]['plural']) ? $bcp_modules[$module_name]['plural'] : 'KNOWLEDGE BASE ARTICLES';

        $data = array(
            "cid" => $cid,
            "offset" => $offset,
            "limit" => $limit,
            "search" => $searchterm,
            "paged" => $paged,
            "module_display_name" => $module_display_name,
            "action_class" => new BCPCustomerPortalActions($this->plugin_name, $this->version)
        );
        $this->template->render($kblist_template, $data);
        echo "<input type='hidden' name='limit' class='limit' value='" . $limit . "'> ";
        wp_die();
    }

    /**
     * check module for pages
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_check_module_callback() {
        $response = array();
        $response['page_exists'] = false;
        $module_name = $_POST['module_name'];
        $page_type = $_POST['page_type'];
        $post_id = isset($_POST['PostId']) ? $_POST['PostId'] : "";
        if ($page_type === 'bcp_portal_elements') {
            echo json_encode($response);
            wp_die();
        }

        $all_portal_page_args = array(
            'post_type' => 'page',
            'fields' => 'ids',
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
        if (isset($post_id) && $post_id != '') {
            $all_portal_page_args['post__not_in'] = array($post_id);
        }

        $bcp_page_exists = new WP_Query($all_portal_page_args);
        $total_pages = $bcp_page_exists->found_posts;
        if (isset($total_pages) && $total_pages > 0) {
            $response['page_exists'] = true;
            $response['page_url'] = get_the_permalink($bcp_page_exists->posts[0]);
        }
        echo json_encode($response);
        wp_die();
    }

    public function bcp_update_option_callback() {
        global $bcp_portal_page_types;
        $post_id = $_POST['PostId'];
        $is_portal_page = get_post_meta($post_id, '_bcp_portal_elements', true);
        $bcp_page_type = get_post_meta($post_id, '_bcp_page_type', true);
        $bcp_modules = get_post_meta($post_id, '_bcp_modules', true);
        if ($is_portal_page && (isset($bcp_page_type) && $bcp_page_type != '') && in_array($bcp_page_type, array('bcp_list', 'bcp_add_edit', 'bcp_detail')) && $bcp_modules == 'All') {
            update_option($bcp_portal_page_types[$bcp_page_type]['page_option'], $post_id);
        }

        if ($is_portal_page && (isset($bcp_page_type) && $bcp_page_type != '') && !in_array($bcp_page_type, array('bcp_list', 'bcp_add_edit', 'bcp_detail'))) {
            if (isset($bcp_portal_page_types[$bcp_page_type])) {
                update_option($bcp_portal_page_types[$bcp_page_type]['page_option'], $post_id);
            }
        }
    }

    /**
     * Case close call
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_case_close_callback() {
        global $objCp;
        $record_id = $_REQUEST['record_id'];
        $contact_id = $_SESSION['bcp_contact_id'];
        $action = $_REQUEST['view'];

        $this->check_pemission('case', 'access', $record_id);

        if ($record_id != '' || $record_id != NULL) {
            $arguments = array(
                "cid" => $contact_id,
                "recordId" => $record_id,
                "action" => $action,
            );
            $data = $objCp->getCaseCloseCall($arguments);

            if ($data->response->status) {
                $response['redirect_url'] = "";
                $response['success'] = TRUE;
                $response['msg'] = $data->response->message;

                setcookie("record_msg", $response['msg'], time() + 3600, "/", "", '', true);
                $_SESSION['record_msg'] = $response['msg'];
                echo json_encode($response);
                wp_die();
            }
        }
    }

    /**
     * Show hide fields
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_show_hide_fields_callback() {
        global $objCp, $wpdb, $bcp_field_prefix;
        $selectvalue = $_REQUEST['selectvalue'];
        $module_name = $_REQUEST['module_name'];
        $fieldname = $_REQUEST['fieldname'];
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "";

        if ($module_name == 'contact') {
            $arguments = array(
                "module" => $module_name,
                "view" => "edit",
            );
            $layout = $objCp->getModuleLayouts($arguments);
            $Role_Data__c = $bcp_field_prefix . 'Customer_Portal_Role__c';

            $contact_layout = $layout->records[0];
            $bcp_portal_role = $contact_layout->$Role_Data__c;
        } else {
            $bcp_portal_role = isset($_SESSION['bcp_portal_role']) ? $_SESSION['bcp_portal_role'] : "";
        }

        if (in_array($module_name, $this->different_name_modules)) {
            $layout = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts WHERE module='$module_name' AND view='edit'");
        } else {
            $bcp_portal_role = isset($_SESSION['bcp_portal_role']) ? $_SESSION['bcp_portal_role'] : "";
            $layout = $wpdb->get_row("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts WHERE module='$module_name' AND view='edit' AND role='$bcp_portal_role'");
        }

        $layout_advance_properties_properties = ( isset($layout->advance_properties) && !empty($layout->advance_properties) ) ? json_decode($layout->advance_properties, 1) : array();

        $i = 0;

        foreach ($layout_advance_properties_properties as $layout_advance_properties_property) {
            $catagotyfields = $layout_advance_properties_property['FieldVisibilityCatagory'][0]['Catagotyfield'];

            $CatagotyfieldOption = ( isset($layout_advance_properties_property['FieldVisibilityCatagory'][0]['CatagotyfieldOption']) ) ? $layout_advance_properties_property['FieldVisibilityCatagory'][0]['CatagotyfieldOption'] : array();

            $DefaultVisibility = $layout_advance_properties_property['DefaultVisibility'];
            $Fieldnames = $layout_advance_properties_property['FieldName'];
            $MultiPicklistOption = $layout_advance_properties_property['FieldVisibilityCatagory'][0]['MultiPicklistOption'];
            if ($type == 'multi-select') {
                if ($catagotyfields == $fieldname) {
                    if (($MultiPicklistOption == 'ANY') && !empty($selectvalue) && (count(array_intersect($selectvalue, $CatagotyfieldOption)) >= 1)) {
                        if ($DefaultVisibility == 'Hidden') {
                            $response[$i]['FieldName'] = $Fieldnames;
                            $response[$i]['Visibility'] = 'show';
                        } else {
                            $response[$i]['FieldName'] = $Fieldnames;
                            $response[$i]['Visibility'] = 'hide';
                        }
                    } else if (($MultiPicklistOption == 'EXACT') && !empty($selectvalue)) {
                        $temp = array($CatagotyfieldOption);
                        if (in_array($selectvalue, $temp)) {
                            if ($DefaultVisibility == 'Hidden') {
                                $response[$i]['FieldName'] = $Fieldnames;
                                $response[$i]['Visibility'] = 'show';
                            } else {
                                $response[$i]['FieldName'] = $Fieldnames;
                                $response[$i]['Visibility'] = 'hide';
                            }
                        }
                    } else {
                        if ($DefaultVisibility == 'Hidden') {
                            $response[$i]['FieldName'] = $Fieldnames;
                            $response[$i]['Visibility'] = 'hide';
                        } else {
                            $response[$i]['FieldName'] = $Fieldnames;
                            $response[$i]['Visibility'] = 'show';
                        }
                    }
                }
            } else {
                if (in_array($selectvalue, $CatagotyfieldOption) && $catagotyfields == $fieldname && $MultiPicklistOption == 'ANY') {
                    if ($DefaultVisibility == 'Hidden') {
                        $response[$i]['FieldName'] = $Fieldnames;
                        $response[$i]['Visibility'] = 'show';
                    } else {
                        $response[$i]['FieldName'] = $Fieldnames;
                        $response[$i]['Visibility'] = 'hide';
                    }
                } else if ($catagotyfields == $fieldname && !in_array($selectvalue, $CatagotyfieldOption)) {
                    if ($DefaultVisibility == 'Hidden') {
                        $response[$i]['FieldName'] = $Fieldnames;
                        $response[$i]['Visibility'] = 'hide';
                    } else {
                        $response[$i]['FieldName'] = $Fieldnames;
                        $response[$i]['Visibility'] = 'show';
                    }
                }
            }
            $i++;
        }
        echo json_encode($response);
        wp_die();
    }

    /**
     * Change language
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_change_language() {
        global $objCp;
        $lang_code = $_POST['lang_code'];
        $_SESSION['bcp_language'] = $lang_code;
        wp_die();
    }

    /**
     * Get filter url
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function bcp_get_filtered_url_callback() {
        $page_url = strip_tags($_POST['page_url']);
        $tmp = parse_url($page_url);
        $filtered_url = home_url($tmp['path']);

        if (strpos($filtered_url, '?') !== false) {
            $parts = substr($filtered_url, strpos($filtered_url, "?") + 1);
            if ($parts != "") {
                $final_filtered_url = $_SERVER['HTTP_ORIGIN'] . $tmp['path'] . '?' . $parts;
            }
        } else {
            $final_filtered_url = $_SERVER['HTTP_ORIGIN'] . $tmp['path'];
        }

        echo $final_filtered_url;
        wp_die();
    }

    public function bcp_add_this_script_footer(){
        
        $bcp_all_module_access_list = (array) $_SESSION['bcp_all_module_access_list'];
        $flag = array();
        foreach($bcp_all_module_access_list as $key => $value){
            $module_name = $key;
            if( $_SESSION['bcp_relationship_type'] == "Account-Based" && $module_name != "account"){
                $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->accountRelationship;
                if($relationship_field_key == ""){ ?>
                        <script>
                            var modulename = "<?php echo $module_name;?>";
                            jQuery('.'+modulename).hide();
                        </script>
                <?php 
                }
              
            }
            if($_SESSION['bcp_module_access_data']->$module_name->productSelection == "true"){
                array_push($flag,1);
            }
        }
       
        if(empty($flag)){ ?>
                 <script>
                    jQuery('.product2').hide();
                </script>
        <?php }
    }
}
