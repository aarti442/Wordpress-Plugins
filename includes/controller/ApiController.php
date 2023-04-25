<?php

/*
 * This is ApiController Class file. This will manage api calls and response.
 * 
 * The use of this file is common call for all kind of crm portals.
 */

if (!class_exists("ApiController")) {

    class ApiController extends BCPCustomerPortalAPI {

        function __construct($authentication) {
            $objCp = parent::__construct($authentication);
            return $objCp;
        }

        /**
         * Check if knowledgebase package is installed in salesfoce or not
         */
        function checkKBPackageInstalled() {
            return $this->CheckKBPackage();
        }

        /**
         * To send registration email
         */
        function PortalForgotPassword($args = array()) {
            $email = isset($args['email']) ? $args['email'] : "";
            return $this->PortalForgotPasswordEmail($email);
        }

        /**
         * Change Password In Reset and Change Password
         */
        function checkPortalChangePassword($args = array()) {
            $record_id = isset($args['record_id']) ? $args['record_id'] : "";
            $request_filters = isset($args['data']) ? $args['data'] : "";
            return $this->PortalChangePassword($record_id, $request_filters);
        }

        /**
         * Portal Login
         */
        function portalLogin($args = array()) {
            return $this->portal_login($args);
        }

        /**
         * Send opt when user login
         */
        function portalOtp($args = array()) {
            return $this->portal_otp($args);
        }

        /**
         * Get field definations
         */
        function getModuleFieldsDefinitions($args = array()) {
            return $this->getFieldsDefinitions($args);
        }

        /**
         * Get any module records with parameters
         */
        function getModuleRecords($args = array()) {
            $module = isset($args['module']) ? $args['module'] : "";
            $where = isset($args['where']) ? $args['where'] : "";
            $fields = isset($args['fields']) ? $args['fields'] : "";
            $order = isset($args['order']) ? $args['order'] : "";
            $order_by = isset($args['order_by']) ? $args['order_by'] : "";
            $limit = isset($args['limit']) ? $args['limit'] : "";
            $offset = isset($args['offset']) ? $args['offset'] : "";
            $search = isset($args['search']) ? $args['search'] : "";
            return $this->getRecords($module, $where, $fields, $order, $order_by, $limit, $offset, $search);
        }

        /**
         * Get module layout
         */
        function getModuleLayouts($args = array()) {
            $module = isset($args['module']) ? $args['module'] : "";
            $view = isset($args['view']) ? $args['view'] : "";
            return $this->getLayouts($module, $view);
        }

        /**
         * Custom call to get records
         */
        function getPortalListData($args = array()) {
            $cid = isset($args['cid']) ? $args['cid'] : "";
            $relationship_field = isset($args['relationship_field']) ? $args['relationship_field'] : "";
            $module_name = isset($args['module_name']) ? $args['module_name'] : "";
            $fields = isset($args['fields']) ? $args['fields'] : array();
            $order = isset($args['order']) ? $args['order'] : "";
            $order_by = isset($args['order_by']) ? $args['order_by'] : "";
            $limit = isset($args['limit']) ? $args['limit'] : "";
            $offset = isset($args['offset']) ? $args['offset'] : "";
            $search = isset($args['search']) ? $args['search'] : "";
            $search_field = isset($args['search_field']) ? $args['search_field'] : "";
            $where_condition = isset($args['where_condition']) ? $args['where_condition'] : "";
            return $this->PortalListData($cid, $relationship_field, $module_name, $fields, $order_by, $order, $limit, $offset, $search, $search_field, $where_condition);
        }

        /**
         * Delete recrod
         */
        function getdeleteRecord($args = array()) {
            $record_id = isset($args['record_id']) ? $args['record_id'] : "";
            $module_name = isset($args['module_name']) ? $args['module_name'] : "";
            return $this->deleteRecord($module_name, $record_id);
        }

        /**
         * Custom call to get record by id
         */
        function getPortalDetailData($args = array()) {
            $cid = isset($args['cid']) ? $args['cid'] : "";
            $module_name = isset($args['module_name']) ? $args['module_name'] : "";
            $data_fields = isset($args['fields']) ? $args['fields'] : array();
            $record_id = isset($args['record_id']) ? $args['record_id'] : "";
            $others = isset($args['others']) ? $args['others'] : array();
            return $this->PortalGetDetailData($cid, $module_name, $record_id, $data_fields, $others);
        }

        /**
         * Update solution likes
         */
        function getPortalUpdateSolutionsLikes($args = array()) {
            $cid = isset($args['cid']) ? $args['cid'] : "";
            $id = isset($args['id']) ? $args['id'] : "";
            $like = isset($args['like']) ? $args['like'] : "";
            $dislike = isset($args['dislike']) ? $args['dislike'] : "";
            return $this->PortalUpdateSolutionsLikes($cid, $id, $like, $dislike);
        }

        /**
         * Custom call for dashboard to get topmoduls recentactivites charts
         */
        function getPortalDashboard($args = array()) {
            $cid = isset($args['cid']) ? $args['cid'] : "";
            $counter_datas = isset($args['counter_datas']) ? $args['counter_datas'] : array();
            $recentActivitys = isset($args['recentActivitys']) ? $args['recentActivitys'] : array();
            $chart_datas = isset($args['chart_datas']) ? $args['chart_datas'] : array();
            return $this->PortalDashboard($cid, $counter_datas, $recentActivitys, $chart_datas);
        }

        /**
         * Function for Dashboard ContentNote Call
         */
        function getPortalContentNoteDashboard($args = array()) {
            $cid = isset($args['cid']) ? $args['cid'] : "";
            $counter_datas = isset($args['counter_datas']) ? $args['counter_datas'] : array();
            $recentActivitys = isset($args['recentActivitys']) ? $args['recentActivitys'] : array();
            return $this->PortalContentNoteDashboard($cid, $counter_datas, $recentActivitys);
        }

        /**
         * Custom call to update data
         */
        function getPortalAttachmentAdd($args = array()) {
            $cid = isset($args['cid']) ? $args['cid'] : "";
            $module_name = isset($args['module_name']) ? $args['module_name'] : "";
            $relate_id = isset($args['relate_id']) ? $args['relate_id'] : "";
            $fields = isset($args['fields']) ? $args['fields'] : array();
            $for_module = isset($args['for_module']) ? $args['for_module'] : "";
            $relationship_field = isset($args['relationship_field']) ? $args['relationship_field'] : "";
            return $this->PortalAttachmentAdd($module_name, $relate_id, $cid, $fields, $for_module, $relationship_field);
        }

        /**
         * Custom call to update data
         */
        function getPortalUpdateData($args = array()) {
            $module_name = isset($args['module_name']) ? $args['module_name'] : "";
            $fields = isset($args['fields']) ? $args['fields'] : array();
            $view_layout_fields = isset($args['view_layout_fields']) ? $args['view_layout_fields'] : array();
            $id = isset($args['id']) ? $args['id'] : "";
            $others = isset($args['others']) ? $args['others'] : array();
            return $this->PortalUpdateData($module_name, $fields, $view_layout_fields, $id, $others);
        }

        /**
         * Run custom call
         */
        function getcustomCall($args = array()) {
            $url = isset($args['url']) ? $args['url'] : "";
            $method = isset($args['method']) ? $args['method'] : "";
            $data = isset($args['data']) ? $args['data'] : "";
            $json_decode = isset($args['json_decode']) ? $args['json_decode'] : "";
            return $this->customCall($url, $method, $data, $json_decode);
        }

        /**
         * Custom call to get records
         */
        function getKBListCall($args = array()) {
            $cid = isset($args['cid']) ? $args['cid'] : "";
            $module_name = isset($args['module_name']) ? $args['module_name'] : "";
            $fields = isset($args['fields']) ? $args['fields'] : array();
            $limit = isset($args['limit']) ? $args['limit'] : "";
            $offset = isset($args['offset']) ? $args['offset'] : "";
            $search = isset($args['search']) ? $args['search'] : "";
            $search_field = isset($args['search_field']) ? $args['search_field'] : "";
            return $this->PortalKBListData($cid, $module_name, $fields, "", "", $limit, $offset, $search, $search_field);
        }

        /**
         * Function is used to get portal module description
         */
        function GetModuleDescriptionCall($args = array()) {
            $module_name = isset($args['module_name']) ? $args['module_name'] : "";
            return $this->GetModuleDescription($module_name);
        }

        /**
         * Global Search Data
         */
        function globalSearchDataCall($args = array()) {
            $cid = isset($args['cid']) ? $args['cid'] : "";
            $searchVal = isset($args['searchVal']) ? $args['searchVal'] : "";
            $data = isset($args['data']) ? $args['data'] : "";
            $limit = isset($args['limit']) ? $args['limit'] : "";
            $offset = isset($args['offset']) ? $args['offset'] : "";
            return $this->globalSearchData($cid, $searchVal, $data, $limit, $offset);
        }

        /**
         * Custom call to get records KBList
         */
        function getPortalKBListData($args = array()) {
            $cid = isset($args['cid']) ? $args['cid'] : "";
            $search_term = isset($args['searchVal']) ? $args['searchVal'] : "";
            $search_field = isset($args['searchfield']) ? $args['searchfield'] : "";
            $data_fields = isset($args['data']) ? $args['data'] : "";
            $limit = isset($args['limit']) ? $args['limit'] : "";
            $offset = isset($args['offset']) ? $args['offset'] : "";
            return $this->PortalKBListData($cid, "kbarticle", $data_fields, "", "", $limit, $offset, $search_term, $search_field);
        }

        /**
         * Function to search ContentNotes
         */
        function getContentNoteSearch($args = array()) {
            $cid = isset($args['cid']) ? $args['cid'] : "";
            $searchVal = isset($args['searchVal']) ? $args['searchVal'] : "";
            $data = isset($args['data']) ? $args['data'] : "";
            $limit = isset($args['limit']) ? $args['limit'] : "";
            $offset = isset($args['offset']) ? $args['offset'] : "";
            return $this->globalContentNoteSearchData($cid, $searchVal, $data, $limit, $offset);
        }

        /**
         * Function to get Notes related Cases
         */
        function getCaseNotes($args = array()) {
            $cid = isset($args['cid']) ? $args['cid'] : "";
            $record = isset($args['record']) ? $args['record'] : "";
            $relationship_field = isset($args['relationship_field']) ? $args['relationship_field'] : "";
            return $this->getCaseNotesData($cid, $record, $relationship_field);
        }

        /**
         * Singup call
         */
        function PortaSignupCall($args = array()) {
            $module = isset($args['module']) ? $args['module'] : "";
            $data = isset($args['data']) ? $args['data'] : "";
            return $this->PortaSignup($module, $data);
        }

        /**
         * Update recrod
         */
        function updateRecordCall($args = array()) {
            $module = isset($args['module']) ? $args['module'] : "";
            $data = isset($args['data']) ? $args['data'] : "";
            $record_id = isset($args['record_id']) ? $args['record_id'] : "";
            return $this->updateRecord($module, $data, $record_id);
        }

        /**
         * Search in solution
         */
        function searchCall($args = array()) {
            return $this->search($args);
        }

        /**
         * Get any module records with parameters
         */
        function getPricebooks() {
            $fields = array("Name" => "Name");
            $where_cond = array('isActive' => true);
            return $this->getRecords('Pricebook2', $where_cond, json_encode($fields), "", "", "", "", "");
        }

        /**
         * Case close call
         */
        function getCaseCloseCall($args = array()) {
            $cid = isset($args['cid']) ? $args['cid'] : "";
            $recordId = isset($args['recordId']) ? $args['recordId'] : "";
            $action = isset($args['action']) ? $args['action'] : "";
            return $this->CaseCloseCall($cid, $recordId, $action);
        }

        /**
         * Portal layout call
         */
        public function getPortalLayoutsCall($args = array()) {
            $layout_type = isset($args['type']) ? $args['type'] : 'layout';
            $layout_params = isset($args['params']) ? $args['params'] : array();
            $where_condition = isset($args['where_condition']) ? $args['where_condition'] : "";
            return $this->PortalLayoutsCall($layout_type, $layout_params, $where_condition);
        }
        
        /**
         * Get Record call
         */
        public function getRecord( $module, $record_id, $fields, $extra = '' ) {
            return $this->getPortalRecord( $module, $record_id, $fields, $extra );
        }

    }

}
