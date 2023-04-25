<?php

/**
 * BCPCustomerPortalTemplateFunctions Class
 *
 * @package SalesforceCustomerPortal
 * @since   3.3.2
 */
defined('ABSPATH') || exit;

/**
 * Main SalesforceCustomerPortal Class.
 *
 * @class BCPCustomerPortalTemplateFunctions
 */
class BCPCustomerPortalTemplateFunctions extends BCPCustomerPortal {

    private $bcp_helper;

    public function __construct() {
        $this->bcp_helper = new BCPCustomerPortalHelper($this->plugin_name, $this->version);
    }

    /**
     * Function to define globals
     */
    public function bcp_define_globals() {
        $GLOBALS['salesforcePortal'] = "salesforce";
        $GLOBALS['bcp_field_prefix'] = "biztechcs__";
        $GLOBALS['url_prefix'] = "biztechcs";
        $GLOBALS['bcp_secure_flag'] = TRUE;
        $GLOBALS['counterData'] = NULL;
        $GLOBALS['recentActivityData'] = NULL;
        $GLOBALS['chartData'] = NULL;

        $GLOBALS['sfcp_portal_pages'] = array(
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
    }

    /**
     * Apply page templates for all portal pages
     * @since 1.0.0
     * @param [type] $page_template
     * @return void
     */
    public function bcp_page_template($page_template) {

        if (fetch_data_option('bcp_portal_template')) {
            if (get_post_type(get_the_ID()) == 'page') {
                $pages = array(
                    fetch_data_option('bcp_login_page'),
                    fetch_data_option('bcp_registration_page'),
                    fetch_data_option('bcp_profile_page'),
                    fetch_data_option('bcp_forgot_password_page'),
                    fetch_data_option('bcp_reset_password_page'),
                    fetch_data_option('bcp_manage_page'),
                    fetch_data_option('bcp_list_page'),
                    fetch_data_option('bcp_detail_page'),
                    fetch_data_option('bcp_dashboard_page'),
                    fetch_data_option('bcp_add_edit_page'),
                );

                if (in_array(get_the_ID(), $pages)) {
                    $page_template = plugin_dir_path(__FILE__) . '../page-templates/page-crm-standalone.php';
                }
            }
        }

        return $page_template;
    }

    /**
     * Function to set customer portal url
     */
    public function bcp_rewrite_portal_url() {

        global $post;

        $bcp_manage_page = fetch_data_option('bcp_manage_page');
        $listpage_id = fetch_data_option('bcp_list_page');
        $detailpage_id = fetch_data_option('bcp_detail_page');
        $bcp_add_edit_pageid = fetch_data_option('bcp_add_edit_page');
        
            $all_portal_page_args = array(
                'post_type' => 'page',
                'fields' => 'ids',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => '_bcp_portal_elements',
                        'value' => '1',
                        'compare' => '=',
                    )
                )
            );
            $all_portal_pages = new WP_Query($all_portal_page_args);
            
            if ($all_portal_pages->have_posts()) {
                while ($all_portal_pages->have_posts()) {
                    $all_portal_pages->the_post();
                    $post_id = get_the_ID();
                   
                    $post = get_post($post_id);
                    $post_slug = $this->get_full_slug($post);
                    $bcp_page_module = get_post_meta($post_id, '_bcp_modules', true);
                    $bcp_page_type = get_post_meta($post_id, '_bcp_page_type', true);

                    if (( isset($bcp_page_type) && $bcp_page_type == 'bcp_list' ) && ( isset($bcp_page_module) && $bcp_page_module != '' )) {

                        add_rewrite_rule(
                                '^' . $post_slug . '/([^/]*)/?',
                                'index.php?page_id=' . $post_id . '&module_name=' . $bcp_page_module . '&search=$matches[1]',
                                'top'
                        );

                        add_rewrite_rule(
                                '^' . $post_slug . '/?',
                                'index.php?page_id=' . $post_id . '&module_name=' . $bcp_page_module . '&search=$matches[2]',
                                'top');
                    }

                    if (( isset($bcp_page_type) && $bcp_page_type == 'bcp_detail' ) && ( isset($bcp_page_module) && $bcp_page_module != '' )) {

                        add_rewrite_rule(
                                '^' . $post_slug . '/([^/]*)$/?',
                                'index.php?page_id=' . $post_id . '&module_name=' . $bcp_page_module . '&record=$matches[1]',
                                'top'
                        );

                        add_rewrite_rule(
                                '^' . $post_slug . '/([^/]*)/solutions$/?',
                                'index.php?page_id=' . $post_id . '&module_name=' . $bcp_page_module . '&record=$matches[1]&sub_module=solutions',
                                'top'
                        );
                    }

                    if (( isset($bcp_page_type) && $bcp_page_type == 'bcp_add_edit' ) && ( isset($bcp_page_module) && $bcp_page_module != '' )) {
                        
                        add_rewrite_rule(
                                '^' . $post_slug . '/([^/]*)/Relate/([^/]*)/Relation/([^/]*)/([^/]*)$/?',
                                'index.php?page_id=' . $post_id . '&module_name=' . $bcp_page_module . '&record=$matches[1]&relate_module_name=$matches[2]&relationship=$matches[3]&relate_module_id=$matches[4]',
                                'top'
                        );
                        
                        add_rewrite_rule(
                                '^' . $post_slug . '/Relate/([^/]*)/Relation/([^/]*)/([^/]*)$/?',
                                'index.php?page_id=' . $post_id . '&module_name=' . $bcp_page_module . '&relate_module_name=$matches[1]&relationship=$matches[2]&relate_module_id=$matches[3]',
                                'top'
                        );
                        
                        add_rewrite_rule(
                                '^' . $post_slug . '/([^/]*)$/?',
                                'index.php?page_id=' . $post_id . '&module_name=' . $bcp_page_module . '&record=$matches[1]',
                                'top'
                        );

                        add_rewrite_rule(
                                '^' . $post_slug . '/?',
                                'index.php?page_id=' . $post_id . '&module_name=' . $bcp_page_module,
                                'top'
                        );
                    }

                    if ( isset($bcp_page_type) && $bcp_page_type == 'bcp_kb') {

                        add_rewrite_rule(
                                '^' . $post_slug . '/?',
                                'index.php?page_id=' . $post_id . '&module_name=KBDocuments&module_action=list',
                                'top'
                        );
                    }
                    
                    if ( isset($bcp_page_type) && $bcp_page_type == 'bcp_case_deflection') {
                        
                        add_rewrite_rule(
                                '^' . $post_slug . '/?',
                                'index.php?page_id=' . $post_id . '&module_name=solutions&module_action=search',
                                'top'
                        );
                    }
                }
            }
            wp_reset_postdata();

        add_rewrite_rule(
                '^customer-portal/KBDocuments/list/?',
                'index.php?page_id=' . $bcp_manage_page . '&module_name=KBDocuments&default_redirect=1&module_action=list',
                'top'
        );

        add_rewrite_rule(
                '^customer-portal/([^/]*)/list/([^/]*)/?',
                'index.php?page_id=' . $listpage_id . '&module_name=$matches[1]&search=$matches[2]&module_action=bcp_list',
                'top'
        );

        add_rewrite_rule(
                '^customer-portal/([^/]*)/list$/?',
                'index.php?page_id=' . $listpage_id . '&module_name=$matches[1]&module_action=bcp_list',
                'top'
        );

        add_rewrite_rule(
                '^customer-portal/([^/]*)/detail/([^/]*)$/?',
                'index.php?page_id=' . $detailpage_id . '&module_name=$matches[1]&record=$matches[2]&module_action=bcp_detail',
                'top'
        );

        add_rewrite_rule(
                '^customer-portal/([^/]*)/detail/([^/]*)/solutions$/?',
                'index.php?page_id=' . $detailpage_id . '&module_name=$matches[1]&record=$matches[2]&sub_module=solutions&module_action=bcp_detail',
                'top'
        );
        
        add_rewrite_rule(
                '^customer-portal/([^/]*)/edit/([^/]*)/Relate/([^/]*)/Relation/([^/]*)/([^/]*)/?',
                'index.php?page_id=' . $bcp_add_edit_pageid . '&module_name=$matches[1]&record=$matches[2]&relate_module_name=$matches[3]&relationship=$matches[4]&relate_module_id=$matches[5]&module_action=bcp_add_edit',
                'top'
        );
        
        add_rewrite_rule(
                '^customer-portal/([^/]*)/edit/Relate/([^/]*)/Relation/([^/]*)/([^/]*)/?',
                'index.php?page_id=' . $bcp_add_edit_pageid . '&module_name=$matches[1]&relate_module_name=$matches[2]&relationship=$matches[3]&relate_module_id=$matches[4]&module_action=bcp_add_edit',
                'top'
        );

        add_rewrite_rule(
                '^customer-portal/([^/]*)/edit/([^/]*)$/?',
                'index.php?page_id=' . $bcp_add_edit_pageid . '&module_name=$matches[1]&record=$matches[2]&module_action=bcp_add_edit',
                'top'
        );

        add_rewrite_rule(
                '^customer-portal/([^/]*)/edit$/?',
                'index.php?page_id=' . $bcp_add_edit_pageid . '&module_name=$matches[1]&module_action=bcp_add_edit',
                'top'
        );

        add_rewrite_rule(
                '^customer-portal/([^/]*)/([^/]*)/?',
                'index.php?page_id=' . $bcp_manage_page . '&module_name=$matches[1]&module_action=$matches[2]',
                'top'
        );


        flush_rewrite_rules();
    }
    
    /**
     * Function to set full slug of url
     */

    public function get_full_slug($current_post) {
        $current_slug = $current_post->post_name;
        $full_slug = '';
        $parent_ancestors = get_post_ancestors($current_post->ID);
        if (isset($parent_ancestors) && is_array($parent_ancestors) && count($parent_ancestors) > 0) {
            $parent_ancestors = array_reverse($parent_ancestors);
            foreach ($parent_ancestors as $parent_id) {
                $parent_post = get_post($parent_id);
                $full_slug .= $parent_post->post_name . '/';
            }
            $current_slug = $full_slug . $current_slug;
        }
        return $current_slug;
    }

    /**
     * Function to register variable in query parameter
     * @param type $vars
     * @return type
     */
    public function register_bcp_custom_query_vars($vars) {
        array_push($vars, 'module_name');
        array_push($vars, 'record');
        array_push($vars, 'sub_module');
        array_push($vars, 'module_action');
        array_push($vars, 'search');
        array_push($vars, 'default_redirect');
        array_push($vars, 'relate_module_name');
        array_push($vars, 'relationship');
        array_push($vars, 'relate_module_id');
        return $vars;
    }

    /**
     * If portal page then redirect to manage page
     * @since 1.0.0
     * @return void
     */
    function bcp_template_redirect() {

        $bcp_login_page = fetch_data_option('bcp_login_page');
        $bcp_registration_page = fetch_data_option('bcp_registration_page');
        $bcp_forgot_password_page = fetch_data_option('bcp_forgot_password_page');
        $bcp_reset_password_page = fetch_data_option('bcp_reset_password_page');
        if (( $bcp_login_page == get_the_ID() || $bcp_registration_page == get_the_ID() || $bcp_forgot_password_page == get_the_ID() || $bcp_reset_password_page == get_the_ID() ) && isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id'] != null) {
            $bcp_dashboard_page = fetch_data_option('bcp_dashboard_page');
            $bcp_dashboard_page = get_permalink($bcp_dashboard_page);
            wp_redirect($bcp_dashboard_page);
            exit();
        }

        $bcp_profile_page = fetch_data_option('bcp_profile_page');
        $bcp_manage_page = fetch_data_option('bcp_manage_page');
        $bcp_dashboard_page = fetch_data_option('bcp_dashboard_page');
        $bcp_list_page = fetch_data_option('bcp_list_page');
        $bcp_detail_page = fetch_data_option('bcp_detail_page');
        $bcp_add_edit_page = fetch_data_option('bcp_add_edit_page');
        $bcp_kb_page = fetch_data_option('bcp_kb');
        $bcp_case_deflection = fetch_data_option('bcp_case_deflection');
        $bcp_authentication = fetch_data_option("bcp_authentication");  
       
        if (( $bcp_reset_password_page == get_the_ID()) && isset($bcp_authentication) && $bcp_authentication != NULL) {
            global $objCp, $bcp_field_prefix;
            $bcp_access_token = $bcp_field_prefix . 'Access_Token__c';
            $bcp_token = isset($_REQUEST['bcp_contact_token']) ? $_REQUEST['bcp_contact_token'] : "";
            $bcp_session_contact_id = (isset($_SESSION['bcp_set_password_contact_id']) && $bcp_token == "") ? $this->bcp_helper->bcp_sha_decrypt($_SESSION['bcp_set_password_contact_id']) : "";

            if ($bcp_token == '' && $bcp_session_contact_id == '') {
                $bcp_login_page = get_permalink($bcp_login_page);
                wp_redirect($bcp_login_page);
                exit();
            } else {
                $where = array(
                    $bcp_access_token => $bcp_token
                );
                $fields = array(
                    $bcp_access_token => 'Access_Token__c',
                );
                $records = $objCp->getRecords('Contact', $where, json_encode($fields));
                
                if ($bcp_session_contact_id != "" || ( $bcp_token != '' && isset($records->records[0]) && ( isset($records->records[0]->$bcp_access_token) && $records->records[0]->$bcp_access_token == $bcp_token ) && !empty($records->records[0]) )) {
                    
                } else {
                    $bcp_login_page = add_query_arg(array('error' => '1'), $bcp_login_page);
                    $_SESSION['bcp_error_message'] = __("Access token is not valid.", 'bcp_portal');
                    wp_redirect($bcp_login_page);
                    exit();
                }
            }
        }

        if (( $bcp_case_deflection == get_the_ID() || $bcp_kb_page == get_the_ID() || $bcp_list_page == get_the_ID() || $bcp_detail_page == get_the_ID() || $bcp_add_edit_page == get_the_ID() || $bcp_dashboard_page == get_the_ID() || $bcp_profile_page == get_the_ID() || $bcp_manage_page == get_the_ID() ) && !isset($_SESSION['bcp_contact_id'])) {
            $bcp_login_page = get_permalink($bcp_login_page);
            wp_redirect($bcp_login_page);
            exit();
        }

        $module_name = get_query_var('module_name');
        $module_action = get_query_var('module_action');
        $record_id = get_query_var('record');

        if (( isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id'] != null ) && (isset($module_name) && $module_name != '') && ( isset($module_action) && in_array($module_action, array('bcp_list', 'bcp_detail', 'bcp_add_edit', 'list')) )) {
            if (get_query_var('default_redirect') == '1' && $module_name == 'KBDocuments' && $module_action == 'list' && ( isset($bcp_kb_page) && $bcp_kb_page != '' )) {
                if (get_post_status($bcp_kb_page) != 'trash') {
                    wp_redirect(get_the_permalink($bcp_kb_page));
                    exit();
                }
            }

            $segregate_exists = $this->bcp_helper->get_segregate_url($module_name, $module_action);
            if ($segregate_exists['page_exists'] && ( isset($segregate_exists['page_url']) && $segregate_exists['page_url'] != '' )) {
                $redirect_url = $segregate_exists['page_url'];
                $search_field = get_query_var('search');
                if ($module_action == 'bcp_list' && $search_field != '') {
                    $redirect_url .= '/' . $search_field;
                } else if ($module_action == 'bcp_detail' && ( isset($record_id) && $record_id != '' )) {
                    $sub_module = get_query_var('sub_module');
                    if (isset($sub_module) && $sub_module != '') {
                        $redirect_url .= '/' . $record_id . '/' . $sub_module;
                    } else {
                        $redirect_url .= '/' . $record_id;
                    }
                } else if ($module_action == 'bcp_add_edit') {
                    if (isset($record_id) && $record_id != '') {
                        $redirect_url .= '/' . $record_id;
                    }
                }
                wp_redirect($redirect_url);
                exit();
            }
        }
    }

}
