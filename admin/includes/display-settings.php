<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('ABSPATH') || exit;

class BCPCustomerPortalSettings {

    private $plugin_name;
    private $version;
    private $bcp_helper;
    private $template;
    private $portal_actions;
    private $default_content;

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->bcp_helper = new BCPCustomerPortalHelper($this->plugin_name, $this->version);
        $this->template = new TemplateHelper(BCP_PLUGIN_DIR . "/admin/partials/");
        $this->portal_actions = new BCPCustomerPortalActions($this->plugin_name, $this->version);
        $this->default_content = new BCP_Default_Content($this->plugin_name, $this->version);

        $this->include_settings_partials();
    }

    /**
     * Function to create menu
     */
    public function bcp_create_menu() {
        add_menu_page(__('Customer Portal', 'bcp_portal'), __('Customer Portal', 'bcp_portal'), 'manage_options', 'bcp-settings', array($this, 'bcp_settings_callback'));
        add_submenu_page('bcp-settings', __('Settings', 'bcp_portal'), __('Settings', 'bcp_portal'), 'manage_options', 'bcp-settings', array($this, 'bcp_settings_callback'));
        add_submenu_page('bcp-settings', __('Manage Pages', 'bcp_portal'), __('Manage Pages', 'bcp_portal'), 'manage_options', 'bcp-filter-pages', array($this, 'bcp_filter_pages_callback'));
    }

    /**
     * Includes other files
     */
    public function include_settings_partials() {
        include_once plugin_dir_path(__FILE__) . '../partials/detail_page_content.php';
    }

    /**
     * Check if meta exist in post or not
     * 
     * @global type $wpdb
     * @param type $filename
     * @return type
     */
    public function bcp_does_file_exists($filename) {

        global $wpdb;
        return intval($wpdb->get_var("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value LIKE '%/$filename'"));
    }

    /**
     * Function to save portal settings options
     * 
     * @global type $bcp_portal_pages
     * @global type $bcp_credentials
     */
    public function bcp_save_settings() {

        global $bcp_portal_pages, $bcp_credentials;

        $request = $_REQUEST;
        unset($request['page']);
        unset($request['submit']);
        
        foreach ($request as $key => $value) {

            if ($value != '' && !is_array($value))
                $value = trim($value);

            if (in_array($key, $bcp_credentials)) {
                if ($value != "") {
                    $token = $this->bcp_helper->bcp_sha_encrypt($value, "bcp_portal_setting_key");
                    update_option($key, $token);
                }
            } else {
                update_option($key, $value);
            }
        }
        if (isset($_REQUEST['bcp_portal_template'])) {
            update_option('bcp_portal_template', $_REQUEST['bcp_portal_template']);
        } else {
            if (fetch_data_option('bcp_portal_template') == NULL) {
                update_option('bcp_portal_template', '1');
            }
        }

        $template = fetch_data_option('bcp_portal_template');
        $bcp_portal_template = ( $template == '1' ) ? 'page-crm-standalone.php' : '';

        $all_portal_page_args = array(
            'post_type' => 'page',
            'fields' => 'ids',
            'posts_per_page' => '-1',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_bcp_portal_elements',
                    'value' => '1',
                    'compare' => '=',
                )
            )
        );

        $bcp_page_exists = new WP_Query($all_portal_page_args);
        $total_pages = $bcp_page_exists->found_posts;
        if ($total_pages > 0) {
            foreach ($bcp_page_exists->posts as $page_id) {
                update_post_meta($page_id, '_wp_page_template', $bcp_portal_template);
            }
        } else {
            foreach ($bcp_portal_pages as $page) {
                $page_id = fetch_data_option($page);
                if ($page_id != '') {
                    update_post_meta($page_id, '_wp_page_template', $bcp_portal_template);
                }
            }
        }
    }

    /**
     * 
     * @global type $bcp_portal_pages
     * @global type $bcp_credentials
     * @global type $bcp_field_prefix
     * @global type $detail_content
     * @global type $bcp_modules
     * @global type $wpdb
     * @global type $objCp
     * @param type $without_ajax
     */
    public function sync_crm_layout() {

        global $bcp_portal_pages, $bcp_credentials, $bcp_field_prefix, $detail_content, $bcp_modules, $wpdb, $objCp;
        
        $Module_Name__c = $bcp_field_prefix . 'Module_Name__c';
        $View_Name__c = $bcp_field_prefix . 'View_Name__c';
        $Layout_Data__c = $bcp_field_prefix . 'Layout_Data__c';
        $Properties_Data__c = $bcp_field_prefix . 'Field_Properties__c';
        $Advance_Field_Properties__c = $bcp_field_prefix . 'Advance_Field_Properties__c';
        $Role_Data__c = $bcp_field_prefix . 'Customer_Portal_Role__c';
        $icon = $bcp_field_prefix . 'Icon_Class_Name__c';
        $title = $bcp_field_prefix . 'Title__c';
        $sub_title = $bcp_field_prefix . 'Description__c';
        $Section_Layout_Data__c = $bcp_field_prefix . 'Section_Layout_Data__c';
        
        /*********************** */
        
        $bcp_roles = !empty($_REQUEST['bcp_roles']) ? json_decode(stripslashes($_REQUEST['bcp_roles']), 1) :  array();
        if($_REQUEST['selected_modules'][0] == "select_all"){
            unset($_REQUEST['selected_modules'][0]);
        }
        $_REQUEST['selected_modules'] = array_values($_REQUEST['selected_modules']);
       
        $selected_modules_sync = $_REQUEST['selected_modules'];
        $bcp_changed_layout_array = [];
        foreach($bcp_roles as $key => $value){
            foreach($selected_modules_sync as $k => $v){
                $portal_access = array('list','edit','detail');
                $bcp_changed_layout_array[$value][$v] = $portal_access;
            }
            
        }
        $bcp_changed_layout = $bcp_changed_layout_array;
        /************************* */
       // print_r($bcp_changed_layout_array);exit;

       // $bcp_changed_layout = !empty($_REQUEST['bcp_updated_layout']) ? json_decode(stripslashes($_REQUEST['bcp_updated_layout']), 1) : array();

       
        $where_condition = "";
        $wp_layout_where_query = "";
        if (!empty($bcp_changed_layout)) {
            foreach ($bcp_changed_layout as $role => $module_arr) {
                $where_condition .= ( !empty($where_condition) ? " OR " : "" );
                $where_condition .= "( ";
                $where_condition .= "( ".$Role_Data__c." = '".$role."'";
                $module_condition = "";
                foreach ($module_arr as $module => $view) {
                    $module_condition .= ( !empty($module_condition) ? " OR " : "" );
                    $module_condition .= "(";
                    $module_condition .= $Module_Name__c." = '".$module."'";
                    $view_condition = "";
                    foreach ($view as $view_name) {
                        $view_condition .= ( !empty($view_condition) ? " OR ". $View_Name__c." = '".$view_name."'" : $View_Name__c." = '".$view_name."'" );
                    }
                    $module_condition .= (!empty($view_condition) ? " AND ( ".$view_condition." )" : "");
                    $module_condition .= " )";
                }
                $where_condition .= (!empty($module_condition) ? " AND ( ".$module_condition." )" : "");
                $where_condition .= " ) )";
            }
        }
        if (!empty($where_condition)) {
            $wp_layout_where_query = str_replace($Role_Data__c, "`role`", $where_condition);
            $wp_layout_where_query = str_replace($Module_Name__c, "`module`", $wp_layout_where_query);
            $wp_layout_where_query = "WHERE ".str_replace($View_Name__c, "`view`", $wp_layout_where_query);
        }
        $response = array();
        $subpanel_array = array();
        
        $layout_params = ['type' => 'layout', 'where_condition' => $where_condition];
        $bcp_portal_layout = $objCp->getPortalLayoutsCall($layout_params);
        if ($bcp_portal_layout->success && $bcp_portal_layout->statusCode == '200') {
            if (isset($bcp_portal_layout->LayoutData) && $bcp_portal_layout->LayoutData != '') {
                $wpdb->query("DELETE FROM {$wpdb->base_prefix}bcp_layouts $wp_layout_where_query");
                $layouts = ( isset($bcp_portal_layout->LayoutData) && $bcp_portal_layout->LayoutData != '' ) ? json_decode($bcp_portal_layout->LayoutData) : '';
                //if (empty($where_condition)) {
                    $file = BCP_PLUGIN_DIR_PATH . "/includes/layouts.json";
                    $layouts_other = json_decode(file_get_contents($file));
                    $layouts_other_array = [];
                    foreach($layouts_other as $key => $value){
                        if(in_array($value->biztechcs__Module_Name__c, $selected_modules_sync)){
                           array_push($layouts_other_array,$value);
                        } 
                    }
                    
                    $layouts = array_merge($layouts, $layouts_other_array);
                    
                //}
                $tmp_layout = array();
                foreach ($layouts as $record) {
                    
                    $fields_layouts = (array) (json_decode($record->$Layout_Data__c));
                    $fields_layouts = array_keys($fields_layouts);
                    if (!isset($tmp_layout[$record->$Module_Name__c])) {
                        $tmp_layout[$record->$Module_Name__c] = $fields_layouts;
                    } else {
                        $tmp_layout[$record->$Module_Name__c] = array_merge($tmp_layout[$record->$Module_Name__c], $fields_layouts);
                    }
                    $key = ( isset($record->$Module_Name__c) ? $record->$Module_Name__c : '' );
                    $layout_role = $record->$Role_Data__c;
                    $subpaneldata = array();
                    $iconkey = ( isset($record->$icon) ? $record->$icon : '' );
                    $title_data = ( isset($record->$title) ? $record->$title : '' );        
                    $sub_title_data = ( isset($record->$sub_title) ? $record->$sub_title : '' );
                    $wpdb->insert(
                        $wpdb->base_prefix . 'bcp_layouts', array(
                        'module'                => $record->$Module_Name__c,
                        'view'                  => $record->$View_Name__c,
                        'fields'                => (isset($record->$Layout_Data__c) ? $record->$Layout_Data__c : ""),
                        'properties'            => isset($record->$Properties_Data__c) ? $record->$Properties_Data__c : "",
                        'advance_properties'    => isset($record->$Advance_Field_Properties__c) ? $record->$Advance_Field_Properties__c : "",
                        'section_layout'        => isset($record->$Section_Layout_Data__c) ? $record->$Section_Layout_Data__c : "",
                        'subpanel'              => '',
                        'role'                  => $layout_role,
                        'icon'                  => $iconkey,
                        'title'                 => $title_data,
                        'sub_title'             => $sub_title_data,
                        )
                    );
                }
           
                $module_layouts = array();
                foreach ($tmp_layout as $module_name => $module_fields) {
                    $fields_def_array = array();
                    $fields_def_array['module'] = $module_name;
                    $fields_def_array['fields'] = $module_fields;
                    array_push($module_layouts, $fields_def_array);
                }
                $result = $objCp->getModuleFieldsDefinitions($module_layouts);
                
                if (isset($result->success) && $result->success) {
                    unset($result->success);
                    foreach ($result as $key => $value) {
                        $wpdb->query("DELETE FROM {$wpdb->base_prefix}bcp_fields WHERE module='".$key."'");
                        $singular = $value->name;
                        $plular = $value->pluralname;
                        $wpdb->insert(
                            $wpdb->base_prefix . 'bcp_fields', array(
                                'module'    => $key,
                                'fields'    => serialize($value->fields),
                                'singular'  => $singular,
                                'plular'    => $plular,
                                'allfields'    => serialize($value->allFields),
                            )
                        );
                    }
                }
                update_option('first_time_sync', '1');
                $response['success'] = true;
                $response['message'] = '<div class="notice notice-success is-dismissible"><p>'. __('Layout synchronized successfully.', 'bcp_portal') .'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
            }
        } else {
            $response['success'] = false;
            $response['message'] = '<div class="notice notice-error is-dismissible"><p>'. __('There was an error in synchronizing layout.', 'bcp_portal') .'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
        }
        echo json_encode($response);
        wp_die();
    }

    
    public function sync_crm_fields() {

        global $bcp_portal_pages, $bcp_credentials, $bcp_field_prefix, $detail_content, $bcp_modules, $wpdb, $objCp;
        $bcp_portal_module = fetch_data_option("bcp_portal_modules");
        $bcp_portal_module['contact'] = "Contacts";
        $response = array();
        if(!empty($bcp_portal_module)) {
            $wpdb->query("TRUNCATE TABLE {$wpdb->base_prefix}bcp_fields ");
            foreach ($bcp_portal_module as $module_name => $module_label){
                $arguments = array( "module_name" => $module_name );
                if ($module_name == "call") {
                    $arguments = array( "module_name" => "task" );
                }
                $module_data = $objCp->GetModuleDescriptionCall( $arguments );
                $module_fields = $module_data->fields;
                $singular = $module_data->label;
                $plular = $module_data->labelPlural;
                if (isset($module_fields) && count($module_fields) > 0) {
                    $wpdb->insert(
                        $wpdb->base_prefix . 'bcp_fields', array(
                            'module'    => $module_name,
                            'fields'    => serialize($module_fields),
                            'singular'  => $singular,
                            'plular'    => $plular,
                        )
                    );
                }
            }
        }
        $response['success'] = TRUE;
        $response['message'] = __('Fields synchronized successfully.', 'bcp_portal');
        echo json_encode($response);
        wp_die();
    }
    /**
     * admin settings callback  function
     * Function to create page on save settings
     * 
     * @global type $objCp
     * @global type $bcp_field_prefix
     * @global type $detail_content
     */

    function bcp_create_portal_pages() {

        global $bcp_portal_pages;

        $image_url = BCP_PLUGIN_DIR_PATH . '/admin/assets/images/login-bg.jpg';
        $thumb_id = 'login-bg-scaled.jpg';
        if (null == ($thumb_id = $this->bcp_does_file_exists('login-bg-scaled.jpg'))) {

            $upload_dir = wp_upload_dir();

            $image_data = file_get_contents($image_url);

            $filename = basename($image_url);

            if (wp_mkdir_p($upload_dir['path'])) {
                $file = $upload_dir['path'] . '/' . $filename;
            } else {
                $file = $upload_dir['basedir'] . '/' . $filename;
            }

            file_put_contents($file, $image_data);

            $wp_filetype = wp_check_filetype($filename, null);

            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attach_id = wp_insert_attachment($attachment, $file);
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attach_data = wp_generate_attachment_metadata($attach_id, $file);
            wp_update_attachment_metadata($attach_id, $attach_data);
        } else {
            $attach_id = $this->bcp_does_file_exists('login-bg-scaled.jpg');
        }

        $bcp_login_page = fetch_data_option('bcp_login_page');
        if (!$bcp_login_page) {
            $login_page_id = $this->default_content->CreateLoginPage();
            if ($login_page_id) {
                set_post_thumbnail($login_page_id, $attach_id);
            }
        }

        $bcp_registration_page = fetch_data_option('bcp_registration_page');
        if (!$bcp_registration_page) {
            $registration_page_id = $this->default_content->CreateRegistrationPage();
        }

        $bcp_profile_page = fetch_data_option('bcp_profile_page');
        if (!$bcp_profile_page) {
            $profile_page_id = $this->default_content->CreateProfilePage();
        }

        $bcp_forgot_password_page = fetch_data_option('bcp_forgot_password_page');
        if (!$bcp_forgot_password_page) {
            $forget_password_page_id = $this->default_content->CreateForgotPasswordPage();
            if ($forget_password_page_id) {
                set_post_thumbnail($forget_password_page_id, $attach_id);
            }
        }

        $bcp_reset_password_page = fetch_data_option('bcp_reset_password_page');
        if (!$bcp_reset_password_page) {
            $reset_page_id = $this->default_content->CreateResetPageContent();
            if ($reset_page_id) {
                set_post_thumbnail($reset_page_id, $attach_id);
            }
        }

        $bcp_manage_page = fetch_data_option('bcp_manage_page');
        if (!$bcp_manage_page) {
            $this->default_content->CreateManagePage();
        }

        $bcp_list_page = fetch_data_option('bcp_list_page');
        if (!$bcp_list_page) {
            $this->default_content->CreateListPage();
        }

        $bcp_detail_page = fetch_data_option('bcp_detail_page');
        if (!$bcp_detail_page) {
            $this->default_content->CreateDetailPage();
        }

        $bcp_dashboard_page = fetch_data_option('bcp_dashboard_page');
        if (!$bcp_dashboard_page) {
            $this->default_content->CreateDashboardPage();
        }

        $bcp_add_edit_page = fetch_data_option('bcp_add_edit_page');
        if (!$bcp_add_edit_page) {
            $this->default_content->CreateEditPage();
        }

        $template = fetch_data_option('bcp_portal_template');
        $bcp_portal_template = ( $template == '1' ) ? 'page-crm-standalone.php' : '';

        foreach ($bcp_portal_pages as $page) {
            $page_id = fetch_data_option($page);
            if ($page_id != '') {
                update_post_meta($page_id, '_wp_page_template', $bcp_portal_template);
            }
        }
    }

    /**
     * Function to Create Portal Menus
     * 
     * @global type $objCp
     * @global type $bcp_field_prefix
     * @global type $detail_content
     */
    function bcp_create_portal_nav_menus() {

        global $bcp_modules;

        $dashboardPage_id = fetch_data_option('bcp_dashboard_page');
        $profilePage_id = fetch_data_option('bcp_profile_page');

        if (!wp_get_nav_menu_object('Customer Portal Menu')) {

            $menu_id = wp_create_nav_menu('Customer Portal Menu'); //create the menu

            if (!fetch_data_option('bcp_portal_menuSelection')) {
                update_option('bcp_portal_menuSelection', $menu_id);
            }

            $menu_items = wp_get_nav_menu_items($menu_id);

            $menuArr = array();
            foreach ($menu_items as $menu_item) {
                $menuArr[] = $menu_item->post_title;
            }
            $module_names = array();
            foreach ($bcp_modules as $key => $value) {
                if ($key == 'contact') {
                    continue;
                }
                $module = json_decode($value);
                $module_names[$key] = $module->PluralName;
            }
            asort($module_names);
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => 'Dashboard',
                'menu-item-object' => 'page',
                'menu-item-object-id' => $dashboardPage_id,
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish',
                'menu-item-parent-id' => 0,
                    )
            );

            $total_modules = count($module_names);
            $first_half_elements = array_slice($module_names, 0, 6);

            foreach ($first_half_elements as $key => $value) {
                $module_name = $value;

                if (!in_array($module_name, $menuArr)) {
                    wp_update_nav_menu_item($menu_id, 0, array(
                        'menu-item-title' => __($module_name, 'bcp_portal'),
                        'menu-item-classes' => $key,
                        'menu-item-url' => home_url('customer-portal/' . $key . '/list/'),
                        'menu-item-status' => 'publish'
                    ));
                }
            }

            if (count($module_names) > 6) {
                $second_half_elements = array_slice($module_names, 6, $total_modules);
                if (isset($second_half_elements) && count($second_half_elements) > 0) {
                    $more_id = wp_update_nav_menu_item($menu_id, 0, array(
                        'menu-item-title' => __('More', 'bcp_portal'),
                        'menu-item-classes' => $key . 'more-modules',
                        'menu-item-url' => '#',
                        'menu-item-status' => 'publish',
                        'menu-item-status' => 'publish',
                        'menu-item-attr_title' => array($key), //used for menu icons in standalone
                    ));

                    foreach ($second_half_elements as $key => $value) {
                        $module_name = $value;

                        if (!in_array($module_name, $menuArr)) {
                            wp_update_nav_menu_item($menu_id, 0, array(
                                'menu-item-title' => __($module_name, 'bcp_portal'),
                                'menu-item-classes' => $key,
                                'menu-item-url' => home_url('customer-portal/' . $key . '/list/'),
                                'menu-item-status' => 'publish',
                                'menu-item-parent-id' => $more_id,
                                'menu-item-position' => 0,
                            ));
                        }
                    }
                }
            }

            $id = wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => __('My Account', 'bcp_portal'),
                'menu-item-classes' => $key,
                'menu-item-url' => '#',
                'menu-item-status' => 'publish',
                'menu-item-status' => 'publish',
                'menu-item-attr_title' => array($key), //used for menu icons in standalone
                    ));
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => __('Profile', 'bcp_portal'),
                'menu-item-object' => 'page',
                'menu-item-object-id' => $profilePage_id,
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish',
                'menu-item-parent-id' => $id,
                'menu-item-position' => 0,
                    )
            );
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => __('Logout', 'bcp_portal'),
                'menu-item-classes' => 'logout',
                'menu-item-url' => home_url('?bcp-logout=1'),
                'menu-item-status' => 'publish',
                'menu-item-parent-id' => $id,
            ));
        } else {
            $bcp_portal_menuSelection = fetch_data_option('bcp_portal_menuSelection');
            if ($bcp_portal_menuSelection != '') {

                $menu = wp_get_nav_menu_object($bcp_portal_menuSelection);
                $portal_menu = wp_get_nav_menu_object('Customer Portal Menu');

                if (isset($menu->term_id)) {
                    $menu_items = wp_get_nav_menu_items($menu->term_id);
                }

                $menuArr = array();
                $menuobjectIdArr = array();
                if (isset($menu_items) && !empty($menu_items) && count($menu_items) > 0) {
                    foreach ($menu_items as $menu_item) {
                        $menuArr[] = $menu_item->post_title;
                        $menuobjectIdArr[] = $menu_item->object_id;
                    }
                }

                $module_names = array();
                foreach ($bcp_modules as $key => $value) {
                    if ($key == 'contact') {
                        continue;
                    }
                    $module = json_decode($value);
                    $module_names[$key] = $module->PluralName;
                }

                if (!empty(array_intersect($module_names, $menuArr)) || $portal_menu == $menu) {
                    if (!in_array($dashboardPage_id, $menuobjectIdArr)) {
                        wp_update_nav_menu_item($menu->term_id, 0, array(
                            'menu-item-title' => 'Dashboard',
                            'menu-item-object' => 'page',
                            'menu-item-object-id' => $dashboardPage_id,
                            'menu-item-type' => 'post_type',
                            'menu-item-status' => 'publish',
                            'menu-item-parent-id' => 0,
                                )
                        );
                    }
                }

                if (!empty(array_intersect($module_names, $menuArr)) || $portal_menu == $menu) {
                    if (!in_array($profilePage_id, $menuobjectIdArr)) {
                        $id = wp_update_nav_menu_item($menu->term_id, 0, array(
                            'menu-item-title' => __('My Account', 'bcp_portal'),
                            'menu-item-classes' => $key,
                            'menu-item-url' => '#',
                            'menu-item-status' => 'publish'
                                ));
                        wp_update_nav_menu_item($menu->term_id, 0, array(
                            'menu-item-title' => __('Profile', 'bcp_portal'),
                            'menu-item-object' => 'page',
                            'menu-item-object-id' => $profilePage_id,
                            'menu-item-type' => 'post_type',
                            'menu-item-status' => 'publish',
                            'menu-item-parent-id' => $id,
                            'menu-item-position' => 0,
                                )
                        );
                        wp_update_nav_menu_item($menu->term_id, 0, array(
                            'menu-item-title' => __('Logout', 'bcp_portal'),
                            'menu-item-classes' => 'logout',
                            'menu-item-url' => home_url('?bcp-logout=1'),
                            'menu-item-status' => 'publish',
                            'menu-item-parent-id' => $id,
                        ));
                    }
                }
            }
        }
    }

    /**
     * Function to set modules array in option
     * 
     * @global type $bcp_modules
     */
    function bcp_set_modules_in_options() {

        global $bcp_modules;

        if (isset($bcp_modules->KBDocuments)) {
            unset($bcp_modules->KBDocuments);
        }

        $bcp_modules = (array) $bcp_modules;
        asort($bcp_modules);
        $bcp_access_modules = array();
        $cnt = 0;
        $access_top_modules = array();
        foreach ($bcp_modules as $key => $value) {
            $bcp_module_value = json_decode($value, 1);
            $bcp_access_modules[$key] = $bcp_module_value['SingleName'];
            if ($cnt < 4) {
                array_push($access_top_modules, $key);
            }
            $cnt++;
        }

        if (fetch_data_option('bcp_portal_modules') === FALSE) {
            add_option("bcp_portal_modules", $bcp_access_modules);
        } else {
            update_option("bcp_portal_modules", $bcp_access_modules);
        }
    }

    /*
     * Function to redirect on Filtered Pages
     */

    public function bcp_filter_pages_callback() {
        $licence_key = fetch_data_option('bcp_licence_key');

        if ($licence_key != '') {
            ?>
            <script type="text/javascript">
                window.location = "<?php echo admin_url('edit.php?s&post_status=all&post_type=page&action=-1&m=0&bcp_page_type=All&filter_action=Filter&paged=1&action2=-1'); ?>";
            </script>
        <?php } else { ?>
            <div class="notice notice-error is-dismissible"><p><?php _e("Please check license key is configured or not.", 'bcp_portal'); ?></p></div>
            <?php
        }
    }

    /**
     * Function to display portal setting page
     * 
     * @global type $objCp
     * @global type $bcp_field_prefix
     * @global type $detail_content
     * @global type $bcp_portal_pages
     */
    function bcp_settings_callback() {

        global $objCp, $bcp_portal_pages, $bcp_modules, $wpdb, $bcp_field_prefix;
        $objCp = NULL;
        
        if (isset($_REQUEST['submit'])) {
            $this->bcp_save_settings();
        }

        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style($this->plugin_name . "bcp-admin-style");
        wp_enqueue_script($this->plugin_name . "bcp-admin");
        wp_localize_script($this->plugin_name . 'bcp-admin', 'my_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
        add_thickbox();

        $success_message = "<p>" . __('Settings saved.', 'bcp_portal') . "</p>";
        $error_message = '';

        $licence_key = fetch_data_option('bcp_licence_key');
        $bcp_licence = fetch_data_option('bcp_licence');
        
        $client_id = fetch_data_option('bcp_client_id');
        $client_id = ( $client_id != '' ? $this->bcp_helper->bcp_sha_decrypt($client_id, "bcp_portal_setting_key") : '' );
        $client_secret = fetch_data_option('bcp_client_secret');
        $client_secret = ( $client_secret != '' ? $this->bcp_helper->bcp_sha_decrypt($client_secret, "bcp_portal_setting_key") : '' );
        $username = fetch_data_option('bcp_username');
        $username = ( $username != '' ? $this->bcp_helper->bcp_sha_decrypt($username, "bcp_portal_setting_key") : '' );
        $password = fetch_data_option('bcp_password');
        $password = ( $password != '' ? $this->bcp_helper->bcp_sha_decrypt($password, "bcp_portal_setting_key") : '' );
        $security_token = fetch_data_option('bcp_security_token');
        $security_token = ( $security_token != '' ? $this->bcp_helper->bcp_sha_decrypt($security_token, "bcp_portal_setting_key") : '' );
        $operation_mode = fetch_data_option('bcp_operation_mode');
        $operation_mode = ( $operation_mode != '' ? $operation_mode : '' );

        $authentication_method = fetch_data_option('authentication_method');
        $bcp_public_key = fetch_data_option('bcp_public_key');
        $bcp_private_key = fetch_data_option('bcp_private_key');
        $client_id_jwt = fetch_data_option('bcp_client_id_jwt');
       
        $client_id_jwt = ( $client_id_jwt != '' ? $this->bcp_helper->bcp_sha_decrypt($client_id_jwt, "bcp_portal_setting_key") : '' );
        
        $username_jwt = fetch_data_option('bcp_username_jwt');
        $username_jwt = ( $username_jwt != '' ? $this->bcp_helper->bcp_sha_decrypt($username_jwt, "bcp_portal_setting_key") : '' );


        $licence = $this->portal_actions->bcp_check_licence();

        // If licence is success
        if (!is_ssl() ) {
            
            if (isset($licence->suc) && $licence->suc && ($client_id != '' && $client_secret != '' && $username != '' && $password != '' && $security_token != '' && $authentication_method == "user_pwd_flow") ||      ($client_id_jwt != '' && $bcp_private_key != '' && $username_jwt != '' && $bcp_public_key != '' && $authentication_method == "jwt_bearer_flow")) {
                if (fetch_data_option("bcp_connection_portal") != "0" || isset($_REQUEST['submit'])) {
                    if($authentication_method == "user_pwd_flow"){
                        $authentication = array(
                            "bcp_client_id" => $client_id,
                            "bcp_client_secret" => $client_secret,
                            "bcp_username" => $username,
                            "bcp_password" => $password . $security_token,
                            "bcp_operation_mode" => $operation_mode,
                            "authentication_method" => $authentication_method
                        );
                    }else if($authentication_method == "jwt_bearer_flow"){
                        $authentication = array(
                            "bcp_client_id" => $client_id_jwt,
                            "bcp_username" => $username_jwt,
                            "bcp_operation_mode" => $operation_mode,
                            "authentication_method" => $authentication_method,
                            "bcp_public_key" => $bcp_public_key,
                            "bcp_private_key" => $bcp_private_key,
                        );
                    }
               
                    $objCp = new ApiController($authentication);
                    
                    $crm_timezone = new DateTimeZone('UTC');
                    $bcp_date = new DateTime();
                    $bcp_date->setTimezone($crm_timezone);
                    $bcp_datetime = $bcp_date->format("Y-m-d H:i:s");
                    update_option("bcp_datetime", $bcp_datetime);

                    if ($objCp) {
                        $bcp_authentication = $this->bcp_helper->bcp_sha_encrypt(serialize($objCp));
                     
                        if($objCp->access_token != ""){
                            update_option('bcp_api_access_token',$objCp->access_token);
                            update_option('bcp_authentication',$bcp_authentication);
                        }else{
                            update_option('bcp_api_access_token',"");
                            update_option('bcp_authentication',"");
                        }
                        $role_params = ['type' => 'role'];
                        $bcp_portal_role_result = $objCp->getPortalLayoutsCall($role_params);
                        if (isset($bcp_portal_role_result->portalRoleData) && !empty($bcp_portal_role_result->portalRoleData)) {
                            $role_array = json_decode($bcp_portal_role_result->portalRoleData);
                            $role_array_of_name = array();
                            foreach ($role_array as $role_information) {
                                $layout_role = $role_information->Id;
                                $layout_role_name = $role_information->Name;
                                $role_array_of_name[$layout_role] = $layout_role_name;
                                $Is_Primary__c = $bcp_field_prefix . 'Is_Primary__c';
                                $role_Primary__c = $role_information->$Is_Primary__c;
                                if($role_Primary__c){
                                    update_option("sfcp_primary_role", $layout_role);
                                }
                            }
                            update_option("bcp_role_objects", $role_array_of_name);
                        }

                        $bcp_modules = array();

                        $bcp_modules = $objCp->PortalModuleName();
                        if (isset($bcp_modules->contact)) {
                            unset($bcp_modules->contact);
                        }
                        $this->bcp_create_portal_pages();

                        if (isset($bcp_modules->status) && $bcp_modules->status == 'success') {
                            unset($bcp_modules->status);
                            $this->bcp_create_portal_nav_menus();
                            $this->bcp_set_modules_in_options();
                            $kb_package_status = $objCp->checkKBPackageInstalled(); // Check knowledgebase package

                            if (isset($kb_package_status->response->success) && $kb_package_status->response->success == TRUE) {

                                $data = $kb_package_status->response->data;
                                $bcp_org_prefix = $data->ORG_Prifix;
                                $bcp_note_enabled = $data->Note_Enabled;
                                $bcp_kb_installed = $data->KB_Package_Installed;
                                update_option('bcp_org_prefix', $bcp_org_prefix);
                                update_option('bcp_note_enabled', $bcp_note_enabled);
                                update_option('bcp_kb_enabled', $bcp_kb_installed);
                            }
                            update_option("bcp_connection_portal", "1");
                            if (isset($_REQUEST['bcp_password']) && $_REQUEST['bcp_password'] != '') {
                                update_option('first_time_sync', '0');
                                $success_message .= "<p>" . __('Authentication successful.', 'bcp_portal') . "</p>";
                            }
                            
                            $module_layouts = $wpdb->get_results("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts");
                            $first_time_sync = fetch_data_option('first_time_sync');
                            if (!$first_time_sync || empty($module_layouts)) {
                                $error_message .= "<p>" . __('Synchronize Layout in Layout Sync Tab.', 'bcp_portal') . "</p>";
                            }
                        } else {
                            $success_message = '';
                            $authentication = NULL;
                            $objCp = NULL;
                            $error_message .= "<p>" . __('Authentication failed.', 'bcp_portal') . " <a title=" . __('Possible reasons', 'bcp_portal') . " href='/#TB_inline?width=600&inlineId=FAQ' class='thickbox'>" . __('Possible reasons', 'bcp_portal') . "</a></p>";
                        }
                    } else {
                        update_option("bcp_connection_portal", "0");
                        update_option('bcp_api_access_token',"");
                        update_option('bcp_authentication',"");
                        $error_message .= "<p>" . __('Authentication failed.', 'bcp_portal') . " <a title=" . __('Possible reasons', 'bcp_portal') . " href='/#TB_inline?width=600&inlineId=FAQ' class='thickbox'>" . __('Possible reasons', 'bcp_portal') . "</a></p>";
                    }
                } else {
                    if (fetch_data_option("bcp_username") !== FALSE) {
                        update_option("bcp_connection_portal", "0");
                        update_option('bcp_api_access_token',"");
                        update_option('bcp_authentication',"");
                        $error_message .= "<p>" . __('Authentication failed.', 'bcp_portal') . " <a title=" . __('Possible reasons', 'bcp_portal') . " href='/#TB_inline?width=600&inlineId=FAQ' class='thickbox'>" . __('Possible reasons', 'bcp_portal') . "</a></p>";
                    }
                }
            } else {
                if (fetch_data_option("bcp_connection_portal") !== FALSE) {
                    update_option("bcp_connection_portal", "0");
                    update_option('bcp_api_access_token',"");
                    update_option('bcp_authentication',"");
                    $error_message .= "<p>" . __('Authentication failed.', 'bcp_portal') . " <a title=" . __('Possible reasons', 'bcp_portal') . " href='/#TB_inline?width=600&inlineId=FAQ' class='thickbox'>" . __('Possible reasons', 'bcp_portal') . "</a></p>";
                }
            }
        } else {
            $error_message .= "<p>" . __('SSL certificate is necessary to integrate portal with salesforce.', 'bcp_portal') . "</p>";
        }
        ?>
        <div class="wrap">
            <h1><?php _e('Salesforce Customer Portal Settings', 'bcp_portal'); ?></h1>
        <?php
        if (isset($licence->suc) && $licence->suc) {
            update_option('bcp_licence', 1);
            update_option('bcp_licence_validate', date('Y-m-d'));
            $bcp_licence = 1;
            if ($success_message && isset($_REQUEST['submit'])) {
                ?>
                    <div class="notice notice-success is-dismissible"><?php echo $success_message; ?></div>
                <?php }
                if ($error_message) { ?>
                    <div class="notice notice-error is-dismissible"><?php echo $error_message; ?></div>
                    <?php
                }
            } else {
                if ($licence) {
                    $licence->msg = isset($licence->msg) && $licence->msg != '' ? $licence->msg : __('License validation failed! Please use the license key provided from CRMJetty or contact us at', 'bcp_portal') . ' <a href="mailto:support@crmjetty.com">support@crmjetty.com</a>.';
                    update_option('bcp_licence', 0);
                    ?>
                    <div class="notice notice-error is-dismissible"><p><?php echo $licence->msg; ?></p></div>
                    <?php
                }
            }
            $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'authentication';
            ?>
            <h2 class="nav-tab-wrapper">        
                <a href="?page=bcp-settings&tab=authentication" class="nav-tab <?php echo $active_tab == 'authentication' ? 'nav-tab-active' : ''; ?>"> <?php _e('Authentication', 'bcp_portal'); ?> </a>                 <?php if ($objCp && $licence->suc == 1) { ?>
                    <a href="?page=bcp-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('General', 'bcp_portal'); ?> </a>
                    <a href="?page=bcp-settings&tab=password" class="nav-tab <?php echo $active_tab == 'password' ? 'nav-tab-active' : ''; ?>"><?php _e('Password', 'bcp_portal'); ?></a>
                    <a href="?page=bcp-settings&tab=layout" class="nav-tab <?php echo $active_tab == 'layout' ? 'nav-tab-active' : ''; ?>"><?php _e('Layout', 'bcp_portal'); ?></a>
                    <a href="?page=bcp-settings&tab=recaptcha" class="nav-tab <?php echo $active_tab == 'recaptcha' ? 'nav-tab-active' : ''; ?>"><?php _e('reCaptcha', 'bcp_portal'); ?></a>                    
                    <a href="?page=bcp-settings&tab=menu" class="nav-tab <?php echo $active_tab == 'menu' ? 'nav-tab-active' : ''; ?>"><?php _e('Menu', 'bcp_portal'); ?></a>
                    <a href="?page=bcp-settings&tab=layoutsync" class="nav-tab <?php echo $active_tab == 'layoutsync' ? 'nav-tab-active' : ''; ?>"><?php _e('Layout Sync', 'bcp_portal'); ?></a>
                <?php
                    do_action('bcp_admin_settings_menu', $active_tab);
                }
                ?>
            </h2>
            <!-- bcp_portal_setting_form Start -->
            <form method="post" class="form-wrap" id="bcp_portal_setting_form">
                <?php
                $connection_established = ( $objCp && gettype($objCp) == 'object' ? true : false );

                $connection_established = false;
                if ($active_tab == 'authentication') {

                    $authentication_data = array(
                        'client_id' => $client_id,
                        'client_secret' => $client_secret,
                        'username' => $username,
                        'password' => $password,
                        'security_token' => $security_token,
                        'licence_key' => $licence_key,
                        'bcp_licence' => $bcp_licence,
                        'authentication_method' => $authentication_method,
                        'bcp_connection' => $connection_established,
                        "bcp_public_key" => $bcp_public_key,
                        "bcp_private_key" => $bcp_private_key,
                        "username_jwt" => $username_jwt,
                        "client_id_jwt" => $client_id_jwt
                    );
                    $this->template->render('authentication.php', $authentication_data);
                }
                if ($objCp && $active_tab == 'general') {
                    $this->template->render('general.php', array('bcp_connection' => $connection_established, 'bcp_helper' => $this->bcp_helper));
                }
                if ($objCp && $active_tab == 'password') {
                    $this->template->render('password.php', array('bcp_connection' => $connection_established));
                }
                if ($objCp && $active_tab == 'layout') {
                    $this->template->render('layout.php', array('bcp_connection' => $connection_established));
                }
                if ($objCp && $active_tab == 'recaptcha') {
                    $this->template->render('recaptcha.php', array('bcp_connection' => $connection_established));
                }
                if ($objCp && $active_tab == 'menu') {
                    $this->template->render('menu.php', array('bcp_connection' => $connection_established));
                }
                if ($objCp && $active_tab == 'layoutsync') {
                    $this->template->render('layoutsync.php', array('bcp_connection' => $connection_established));
                } else {
                do_action('bcp_admin_settings_content', $objCp, $active_tab, $this->bcp_helper);
                ?>
                <p><input type="submit" class="button-primary" name="submit" value="<?php _e('Save Changes', 'bcp_portal'); ?>" /></p>
                <?php } ?>
            </form>
            <!-- bcp_portal_setting_form End -->
        </div>
        <div id="FAQ" style="display:none;">
        <?php echo $this->bcp_faq_section(); ?>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function () {
                var last_valid_selection = null;
                jQuery('#sfcp_top_modules').change(function (event) {
                    if (jQuery(this).val().length > 4) {
                        jQuery(this).val(last_valid_selection);
                    } else {
                        last_valid_selection = jQuery(this).val();
                    }
                });
                jQuery('.sfcp-color-field').wpColorPicker();
            });
        </script>
        <?php
    }

    /**
     * FAQ section for backend
     *
     * @return void
     */
    public function bcp_faq_section() {
        ?>
        <ul id="faq-list-set">
            <li>
                <p class="brush-question"><?php _e('Does it support custom modules of CRM?', 'bcp_portal'); ?></p>
                <p class="brush-answer"><?php _e('Yes,it is providing custom modules support with extra efforts. You can contact us for more details.', 'bcp_portal'); ?></p>
            </li>
            <li>
                <p class="brush-question"><?php _e('For adding custom module does the customer need to provide any details?', 'bcp_portal'); ?></p>
                <p class="brush-answer"><?php _e('Yes, customer need to provide their FTP and site admin details for both the frameworks(CRM and CMS)along with the list of modules which they want to enable for the portal.', 'bcp_portal'); ?></p>
            </li>
            <li>
                <p class="brush-question"><?php _e('What data is required to make connection with CRM?', 'bcp_portal'); ?></p>
                <p class="brush-answer"><?php _e('You just need your CRM instance URL, CRM admin username, CRM admin password and your security token to make connection. For further details you can review our user manual guide.', 'bcp_portal'); ?></p>
            </li>
            <li>
                <p class="brush-question"><?php _e('Does it only support HTTPS?', 'bcp_portal'); ?></p>
                <p class="brush-answer"><?php _e('Yes. As per Salesforce Standards your Portal must have valid SSL.', 'bcp_portal'); ?></p>
            </li>
            <li>
                <p class="brush-question"><?php _e('Reason for Authentication failed', 'bcp_portal'); ?></p>
                <p class="brush-answer"><?php _e('Invalid Client id - secret id, Invalid username or password may cause the authentication failed. If there is JWT berear flow then Invalid Public key or Private key will be the reason for Authentication failed.', 'bcp_portal'); ?></p>
            </li>
        </ul>
        <?php
    }

    /** filter action links function
     * 
     * @param type $links
     * @return type
     */
    public function bcp_filterActionLinks($links) {
        if (isset($links['deactivate'])) {
            $deactivation_link = $links['deactivate'];
            // Insert an onClick action to allow form before deactivating
            $deactivation_link = str_replace('<a ', '<div class="bcp-deactivate-form-wrapper">
                     <span class="bcp-deactivate-form" id="bcp-deactivate-form-' . esc_attr($this->plugin_name) . '"></span>
                 </div><a id="bcp-deactivate-link-' . esc_attr($this->plugin_name) . '" ', $deactivation_link);
            $links['deactivate'] = $deactivation_link;
        }
        return $links;
    }

    public function deactivatePluginCallback() {

        $bcp_keep_settings = $_POST['bcp_keep_settings'];
        (get_option("bcp_keep_portal_settings") == NULL) ? add_option("bcp_keep_portal_settings", $bcp_keep_settings) : update_option("bcp_keep_portal_settings", $bcp_keep_settings);
        echo json_encode(array(
            "status" => 1,
        ));
        die();
    }

    /**
     * Form text strings
     * These can be filtered
     * @since 1.0.0
     */
    public function bcp_goodbye_portal() {
        // Get our strings for the form
        // Build the HTML to go in the form
        $html = '<div class="bcp-deactivate-form-head"><strong>' . __("Sorry To see you go", "'bcp_portal'") . '</strong></div>';
        $html .= '<div class="bcp-deactivate-form-body">';
        $html .= '<span title="' . __('Un-check this if you don\\\'t plan to use Salesforce Customer Portal in the future on this website.', $this->plugin_name)
                . '"><input type="checkbox" name="bcp-keep-settings" id="bcp-keep-settings" value="yes" checked> <label for="bcp-keep-settings">'
                . esc_html__('Keep the Salesforce Customer Portal settings and pages on plugin deletion.', 'bcp_portal') . '</label></span><br>';
        $html .= '<hr/>';
        $html .= '</div><!-- .bcp-deactivate-form-body -->';
        $html .= '<p class="deactivating-spinner"><span class="spinner"></span> ' . __('Submitting form', 'bcp_portal') . '</p>';
        $html .= '<div class="bcp-deactivate-form-footer"><p>';
        $html .= '<a id="bcp-deactivate-submit-form" class="button button-primary" href="#">'
                . __('<span>Submit&nbsp;and&nbsp;</span>Deactivate', 'bcp_portal')
                . '</a>';
        $html .= '</p></div>';
        ?>
        <div class="bcp-deactivate-form-bg"></div>
        <style type="text/css">
            .bcp-deactivate-form-active .bcp-deactivate-form-bg {
                background: rgba( 0, 0, 0, .5 );
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
            }
            .bcp-deactivate-form-wrapper {
                position: relative;
                z-index: 999;
                display: none;
            }
            .bcp-deactivate-form-active .bcp-deactivate-form-wrapper {
                display: block;
            }
            .bcp-deactivate-form {
                display: none;
            }
            .bcp-deactivate-form-active .bcp-deactivate-form {
                position: absolute;
                bottom: 30px;
                left: 0;
                max-width: 500px;
                min-width: 360px;
                background: #fff;
                white-space: normal;
            }
            .bcp-deactivate-form-head {
                background: #2271b1;
                color: #fff;
                padding: 8px 18px;
            }
            .bcp-deactivate-form-body {
                padding: 8px 18px 0;
                color: #444;
            }
            .bcp-deactivate-form-body label[for="bcpremove-settings"] {
                font-weight: bold;
            }
            .deactivating-spinner {
                display: none;
            }
            .deactivating-spinner .spinner {
                float: none;
                margin: 4px 4px 0 18px;
                vertical-align: bottom;
                visibility: visible;
            }
            .bcp-deactivate-form-footer {
                padding: 0 18px 8px;
            }
            .bcp-deactivate-form-footer label[for="anonymous"] {
                visibility: hidden;
            }
            .bcp-deactivate-form-footer p {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin: 0;
            }
            #bcpdeactivate-submit-form span {
                display: none;
            }
            .bcp-deactivate-form.process-response .bcp-deactivate-form-body,
            .bcp-deactivate-form.process-response .bcp-deactivate-form-footer {
                position: relative;
            }
            .bcp-deactivate-form.process-response .bcp-deactivate-form-body:after,
            .bcp-deactivate-form.process-response .bcp-deactivate-form-footer:after {
                content: "";
                display: block;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba( 255, 255, 255, .5 );
            }
        </style>
        <script>
            jQuery(document).ready(function ($) {
                var deactivateURL = $("#bcp-deactivate-link-<?php echo esc_attr($this->plugin_name); ?>"),
                        formID = '#bcp-deactivate-form-<?php echo esc_attr($this->plugin_name); ?>',
                        formContainer = $(formID),
                        deactivated = true;

                $(deactivateURL).attr('onclick', "javascript:event.preventDefault();");
                $(deactivateURL).on("click", function () {

                    var url = deactivateURL.attr('href');

                    var SubmitFeedback = function (data, formContainer) {
                        data['action'] = 'bcp_deactivate_plugin';
                        data['security'] = '<?php echo wp_create_nonce("bcp_deactivate_plugin"); ?>';
                        data['dataType'] = 'json';
                        data['bcp_keep_settings'] = formContainer.find('#bcp-keep-settings:checked').length;

                        // As soon as we click, the body of the form should disappear
                        formContainer.addClass('process-response');
                        // Fade in spinner
                        formContainer.find(".deactivating-spinner").fadeIn();
                        $.post(
                                ajaxurl,
                                data,
                                function (response) {
                                    // Redirect to original deactivation URL
                                    window.location.href = url;
                                }
                        );
                    }

                    $('body').toggleClass('bcp-deactivate-form-active');
                    formContainer.fadeIn({complete: function () {
                            var offset = formContainer.offset();
                            if (offset.top < 50) {
                                $(this).parent().css('top', (50 - offset.top) + 'px')
                            }
                            $('html,body').animate({scrollTop: Math.max(0, offset.top - 50)});
                        }});
                    formContainer.html('<?php echo $html; ?>');
                    if (deactivated) {
                        deactivated = false;
                        $('#bcp-deactivate-submit-form').removeAttr("disabled");
                        formContainer.off('click', '#bcp-deactivate-submit-form');
                        formContainer.on('click', '#bcp-deactivate-submit-form', function (e) {
                            e.preventDefault();
                            var data = {
                                reason: formContainer.find('input[name="bcp-deactivate-reason"]:checked').val(),
                                details: formContainer.find('#bcp-deactivate-details').val(),
                                anonymous: formContainer.find('#anonymous:checked').length,
                            };
                            SubmitFeedback(data, formContainer);
                        });
                    }

                    formContainer.on('click', '#bcp-deactivate-submit-form', function (e) {
                        e.preventDefault();
                        SubmitFeedback({}, formContainer);
                    });

                    // If we click outside the form, the form will close
                    $('.bcp-deactivate-form-bg').on('click', function () {
                        formContainer.fadeOut();
                        $('body').removeClass('bcp-deactivate-form-active');
                    });
                });
            });

        </script>
        <?php
    }

}
