<?php
/**
 * @since 1.0.0
 */
require_once 'php-jwt/src/BeforeValidException.php';
require_once 'php-jwt/src/ExpiredException.php';
require_once 'php-jwt/src/SignatureInvalidException.php';
require_once 'php-jwt/src/JWT.php';
use \Firebase\JWT\JWT;
if ( !class_exists( 'BCPCustomerPortalAPI' ) ) {
    
    class BCPCustomerPortalAPI {

        var $access_token;
        var $instance_url;
        var $version_url;
        var $timezone;
        var $field_prefix;
        var $org_prefix;
        var $url_prefix;
        var $crm_name = "salesforce";
        private $bcp_helper;

        function __construct( $authentication = array() ) {

            global $objCp, $bcp_field_prefix, $url_prefix;
           
            if($authentication['authentication_method'] == "user_pwd_flow"){   
                $objCp = $this->authenticate($authentication);
            }else if($authentication['authentication_method'] == "jwt_bearer_flow"){  
                $objCp = $this->authenticateJWT($authentication);
            }
            $this->access_token = isset( $objCp->access_token ) ? $objCp->access_token : '';
            $this->instance_url = isset( $objCp->instance_url ) ? $objCp->instance_url : '';
            $this->version_url = isset( $objCp->version_url ) ? $objCp->version_url : '';
            $this->timezone = isset( $objCp->timezone ) ? $objCp->timezone : '';
            $this->field_prefix = $bcp_field_prefix;
            $this->org_prefix = "";
            if (fetch_data_option("bcp_org_prefix") != '') {
                $this->org_prefix = "/" . fetch_data_option("bcp_org_prefix");
            }
            $this->url_prefix = $url_prefix;
            $this->bcp_helper = new BCPCustomerPortalHelper(BCP_CUSTOMER_PORTAL_NAME, BCP_CUSTOMER_PORTAL_VERSION);
            return $this;
        }
        
        /**
         * 
         * @param type $authentication
         * @return boolean
         */
        function authenticate( $authentication = array() ) {

            $params = "grant_type=password"
            . "&client_id=" . $authentication['bcp_client_id']
            . "&client_secret=" . $authentication['bcp_client_secret']
            . "&username=" . $authentication['bcp_username']
            . "&password=" . $authentication['bcp_password'];

            $operation_mode = $authentication['bcp_operation_mode'];
            if ( $operation_mode == 'live' ) {
                $loginurl = "https://login.salesforce.com/services/oauth2/token";
            } else {
                $loginurl = "https://test.salesforce.com/services/oauth2/token";
            }

            $curl = curl_init( $loginurl );
            curl_setopt( $curl, CURLOPT_HEADER, false );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $curl, CURLOPT_POST, true );
            curl_setopt( $curl, CURLOPT_POSTFIELDS, $params );
            $json_response = curl_exec( $curl );
            curl_close( $curl );

            $authentication = json_decode( $json_response );
           
            if ( isset( $authentication->access_token ) && $authentication->access_token != null ) {

                $url = $authentication->instance_url."/services/data/";
                $curl = curl_init( $url );
                curl_setopt( $curl, CURLOPT_HEADER, false );
                curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
                $json_response = curl_exec( $curl );
                curl_close( $curl );

                $versions = json_decode( $json_response );

                if ( $versions != null ) {
                    $version = end( $versions );
                    if ( isset( $version->url ) ) {
                        $authentication->version_url = $version->url;
                    } else {
                        $authentication->version_url = '/services/data/v39.0';
                    }
                } else {
                    $authentication->version_url = '/services/data/v39.0';
                }
                return $authentication;
            } else {
                return false;
            }
        }

        function authenticateJWT($authentication = array()){
               
                $privateKey = $authentication['bcp_private_key'];
                $publicKey = $authentication['bcp_public_key'];
                $privateKey = <<<EOD
                {$privateKey}
                EOD;
           
                $publicKey = <<<EOD
                {$publicKey}
                EOD;
                $issuedAt = time();
                $expirationTime = $issuedAt + 60 * 60 * 24 * 60;
                $operation_mode = $authentication['bcp_operation_mode'];
                if ( $operation_mode == 'live' ) {
                    $aud = "https://login.salesforce.com";
                    $loginurl = "https://login.salesforce.com/services/oauth2/token";
                } else {
                    $aud = "https://test.salesforce.com";
                    $loginurl = "https://test.salesforce.com/services/oauth2/token";
                }
                $payload = array(
                    "iss" => $authentication['bcp_client_id'],
                    "aud" => $aud,
                    "exp" => $expirationTime,
                    "sub" => $authentication['bcp_username'],
                );
               
                $jwt = JWT::encode($payload, $privateKey, 'RS256');
               
                if($jwt != ""){
                    $params = "grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer"
                    . "&assertion=" . $jwt;
                
                    $curl = curl_init( $loginurl );
                    curl_setopt( $curl, CURLOPT_HEADER, false );
                    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $curl, CURLOPT_POST, true );
                    curl_setopt( $curl, CURLOPT_POSTFIELDS, $params );
                    $json_response = curl_exec( $curl );
                    curl_close( $curl );

                    $authentication = json_decode( $json_response );
                    if ( isset( $authentication->access_token ) && $authentication->access_token != null ) {
                        $url = $authentication->instance_url."/services/data/";
                        $curl = curl_init( $url );
                        curl_setopt( $curl, CURLOPT_HEADER, false );
                        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
                        $json_response = curl_exec( $curl );
                        curl_close( $curl );
        
                        $versions = json_decode( $json_response );
        
                        if ( $versions != null ) {
                            $version = end( $versions );
                            if ( isset( $version->url ) ) {
                                $authentication->version_url = $version->url;
                            } else {
                                $authentication->version_url = '/services/data/v39.0';
                            }
                        } else {
                            $authentication->version_url = '/services/data/v39.0';
                        }
                        return $authentication;
                    } else {
                        return false;
                    }
                }else{
                    return false;
                }
        }
        
        /**
         * all portal call curl request
         * @since 1.0.0
         * @param string $url
         * @param string $method
         * @param array $data
         * @param integer $json_decode
         * @return void
         */
        function call( $url = '', $method = '', $data = array(), $json_decode = 1 ) {
          
            $http_header = array( "Authorization: Bearer $this->access_token", "X-PrettyPrint: 1" );
            $curl = curl_init( $url );
            curl_setopt( $curl, CURLOPT_HEADER, false );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
            
            $log_request_time = date("Y-m-d H:i:s");
            $log_request = "";
            $log_request_url = $url;
            $log_request_method = $method;
            $log_request = "";
            $log_response = "";
            $log_response_time = "";
            
            if ( $method != null ) {
                curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $method );
            }

            if ( $data != null ) {
                $json_data = json_encode( $data );
                $log_request = $json_data;
                curl_setopt( $curl, CURLOPT_POSTFIELDS, $json_data );
                $http_header[] = 'Content-Type: application/json';
                $http_header[] = 'Content-Length: ' . strlen( $json_data );
            }

            curl_setopt( $curl, CURLOPT_HTTPHEADER, $http_header );
            $json_response = curl_exec( $curl );

            $log_Status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close( $curl );
          
            if (strpos($json_response, 'INVALID_SESSION_ID') !== false ) {   // If session expired than again create it
               
                $bcp_client_id = fetch_data_option('bcp_client_id'); $bcp_client_id = ( $bcp_client_id != '' ? $this->bcp_helper->bcp_sha_decrypt( $bcp_client_id, "bcp_portal_setting_key" ) : '' );
                $bcp_client_secret = fetch_data_option('bcp_client_secret'); $bcp_client_secret = ( $bcp_client_secret != '' ? $this->bcp_helper->bcp_sha_decrypt( $bcp_client_secret, "bcp_portal_setting_key" ) : '' );
                $bcp_username = fetch_data_option('bcp_username'); $bcp_username = ( $bcp_username != '' ? $this->bcp_helper->bcp_sha_decrypt( $bcp_username, "bcp_portal_setting_key" ) : '' );
                $bcp_password = fetch_data_option('bcp_password'); $bcp_password = ( $bcp_password != '' ? $this->bcp_helper->bcp_sha_decrypt( $bcp_password, "bcp_portal_setting_key" ) : '' );
                $bcp_security_token = fetch_data_option('bcp_security_token'); $bcp_security_token = ( $bcp_security_token != '' ? $this->bcp_helper->bcp_sha_decrypt( $bcp_security_token, "bcp_portal_setting_key" ) : '' );
                $bcp_operation_mode = fetch_data_option('bcp_operation_mode');
                $bcp_operation_mode = ( $bcp_operation_mode != '' ? $bcp_operation_mode : '' );

                $authentication_method = fetch_data_option('authentication_method');
                $bcp_public_key = fetch_data_option('bcp_public_key');
                $bcp_private_key = fetch_data_option('bcp_private_key');
                $client_id_jwt = fetch_data_option('bcp_client_id_jwt');
               
                $client_id_jwt = ( $client_id_jwt != '' ? $this->bcp_helper->bcp_sha_decrypt($client_id_jwt, "bcp_portal_setting_key") : '' );
                
                $username_jwt = fetch_data_option('bcp_username_jwt');
                $username_jwt = ( $username_jwt != '' ? $this->bcp_helper->bcp_sha_decrypt($username_jwt, "bcp_portal_setting_key") : '' );
               
                if ( ($bcp_client_id && $bcp_client_secret && $bcp_username && $bcp_password) || ($bcp_public_key && $bcp_private_key && $client_id_jwt && $username_jwt) ) {
                 
                   
                    if($authentication_method == "user_pwd_flow"){
                        $authentication_params = array(
                            "bcp_client_id"        => $bcp_client_id,
                            "bcp_client_secret"    => $bcp_client_secret,
                            "bcp_username"         => $bcp_username,
                            "bcp_password"         => $bcp_password.$bcp_security_token,
                            "bcp_operation_mode"   => $bcp_operation_mode,
                            "authentication_method" => $authentication_method,
                        );
                    }else if($authentication_method == "jwt_bearer_flow"){
                       
                        $authentication_params = array(
                            "bcp_client_id" => $client_id_jwt,
                            "bcp_username" => $username_jwt,
                            "bcp_operation_mode" => $bcp_operation_mode,
                            "authentication_method" => $authentication_method,
                            "bcp_public_key" => $bcp_public_key,
                            "bcp_private_key" => $bcp_private_key,
                        );
                    }
                    
                    $authentication = new ApiController( $authentication_params );
                   
                    if ( $authentication ) {
                        $portal_helper = new BCPCustomerPortalHelper(BCP_CUSTOMER_PORTAL_NAME, BCP_CUSTOMER_PORTAL_VERSION);
                        $bcp_authentication = $portal_helper->bcp_sha_encrypt(serialize($authentication));
                        update_option('bcp_api_access_token',$authentication->access_token);
                        update_option('bcp_authentication',$bcp_authentication);
                    }
                }
                
                // After successfull session created again call to perform previous action
                $http_header = array( "Authorization: Bearer $authentication->access_token", "X-PrettyPrint: 1" );
                $curl = curl_init( $url );
                curl_setopt( $curl, CURLOPT_HEADER, false );
                curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

                if ( $method != null ) {
                    curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $method );
                }

                if ( $data != null ) {
                    $json_data = json_encode( $data );

                    curl_setopt( $curl, CURLOPT_POSTFIELDS, $json_data );
                    $http_header[] = 'Content-Type: application/json';
                    $http_header[] = 'Content-Length: ' . strlen( $json_data );
                }

                curl_setopt( $curl, CURLOPT_HTTPHEADER, $http_header );
                $json_response = curl_exec( $curl );
                $log_Status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close( $curl );
            }
            
            $log_response_time = date("Y-m-d H:i:s");
            $log_response = $json_response;
            
            $log_response_decode = json_decode($log_response_decode);
            if ( fetch_data_option('bcp_enable_logs') == 1 ) {
                $is_success = 0;
                if(fetch_data_option('bcp_enable_success_logs') == 1){ 
                    /** Log All failed/success API call */
                    if ($log_Status_code == "200" || $log_Status_code == "201") {
                        $is_success = 1;
                    }
                    $portal_helper = new BCPCustomerPortalHelper(BCP_CUSTOMER_PORTAL_NAME, BCP_CUSTOMER_PORTAL_VERSION);
                    $portal_helper->bcp_custom_debug($log_request_url, $log_request_method, $log_request, $log_request_time, $log_response, $log_response_time, $is_success, $log_Status_code);
                }else{
                    /** Log Only failed API call */ 
                  
                    if ($log_Status_code != "200" && $log_Status_code != "201" ) {
                        $portal_helper = new BCPCustomerPortalHelper(BCP_CUSTOMER_PORTAL_NAME, BCP_CUSTOMER_PORTAL_VERSION);
                        $portal_helper->bcp_custom_debug($log_request_url, $log_request_method, $log_request, $log_request_time, $log_response, $log_response_time, $is_success, $log_Status_code);
                    }
                }
            }

            if ( $json_decode ) {
                return json_decode( $json_response );
            } else {
                return $json_response;
            }
        }
        
        /**
         * Portal Login
         * @since 1.0.0
         * @param [type] $data
         * @return void
         */
        public function portal_login( $data ) {

            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/rest/PortalAuthentication/";
            $response = $this->call( $url, 'POST', $data, 1);
            return $response;
        }
        
        /**
         * Send opt when user login
         * @since 1.0.0
         * @param [type] $data
         * @return void
         */
        public function portal_otp($data) {
            
            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/rest/PortalOtp/";
            $response = $this->call( $url, 'POST', $data, 1);
            return $response;
        }
        
        /**
         * Get any module records with parameters
         *
         * @since 1.0.0
         * @param [type] $module
         * @param [type] $where
         * @param [type] $fields
         * @param [type] $order_by
         * @param [type] $limit
         * @param integer $offset
         * @param [type] $search
         * @param [type] $search_field
         * @return void
         */
        
        public function getRecords( $module, $where = array(), $fields = "", $order = "", $order_by = "", $limit = "", $offset = "", $search = "" ) {
            
            $request_parameters = array();
            $request_parameters['module'] = $module;
            $request_parameters['cid'] = isset($_SESSION['bcp_contact_id']) ? $_SESSION['bcp_contact_id']: "";
            if (!empty($limit)) {
                $request_parameters['limit'] = $limit;
            }
            if (!empty($offset)) {
                $request_parameters['offset'] = $offset;
            }
            $request_parameters['order'] = $order;
            $request_parameters['order_by'] = $order_by;
            $request_parameters['default_where'] = $where;
            if ( !empty($fields) ) {
                $fields = json_decode( $fields , 1 );
                if ( array_key_exists( 'Id', (array) $fields ) ) {
                    $fields = implode( ',', array_keys( (array) $fields ) );
                } else {
                    $fields = 'Id,'.implode( ',', array_keys( (array) $fields ) );
                }
            } else {
                $fields = 'Id';
            }
            $fields = explode(",", $fields);
            $request_parameters['fields'] = $fields;
            if (!empty($search)) {
                $request_parameters['search'] = $search;
            }
            
            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/v1/getRecords/";
            $response = $this->call( $url, "POST", $request_parameters, 1 );
            return $response;
        }
        
        /**
         * Insert record to module
         *
         * @since 1.0.0
         * @param [type] $module
         * @param [type] $data
         * @return void
         */
        public function insertRecord( $module, $data ) {
            
            $url = $this->instance_url.$this->version_url."/sobjects/$module/";
            $response = $this->call( $url, 'POST', $data );
            return $response;
        }
        
        /**
         * Update recrod
         *
         * @since 1.0.0
         * @param [type] $module
         * @param [type] $data
         * @param [type] $record_id
         * @return void
         */
        public function updateRecord( $module, $data, $record_id ) {

            $url = $this->instance_url.$this->version_url."/sobjects/$module/$record_id";
            $response = $this->call( $url, 'PATCH', $data );
            return $response;
        }
        
        /**
         * Delete recrod
         *
         * @since 1.0.0
         * @param [type] $module
         * @param [type] $record_id
         * @return void
         */
        public function deleteRecord( $module, $record_id ) {

            $url = $this->instance_url.$this->version_url."/sobjects/$module/$record_id";
            $response = $this->call( $url, 'DELETE' );

            return $response;
        }
        
        /**
         * Delete multiple records
         *
         * @since 1.0.0
         * @param [type] $module
         * @param [type] $record_id
         * @return void
         */
        public function deleteMultipleRecords( $record_ids = array() ) {

            $record_ids = implode(",", $record_ids);
            $url = $this->instance_url.$this->version_url."/composite/sobjects?ids=$record_ids";
            $response = $this->call( $url, 'DELETE' );

            return $response;
        }
        
        /**
         * Get field definations
         *
         * @since 1.0.0
         * @param array $modules
         * @return void
         */
        public function getFieldsDefinitions( $modules = array() ) {

            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/v1/fieldDefinationData/";
            $response = $this->call( $url, "POST", $modules, 1 );

            return $response;
        }
        
        /**
         * Get module layout
         *
         * @since 1.0.0
         * @param string $module_name
         * @param string $view
         * @return void
         */
        public function getLayouts($module_name = "", $view = "") {

            $query = "SELECT " . $this->field_prefix  . "Module_Name__c," . $this->field_prefix  . "View_Name__c," . $this->field_prefix  . "Layout_Data__c," . $this->field_prefix  . "Field_Properties__c," . $this->field_prefix  . "Advance_Field_Properties__c," . $this->field_prefix . "Customer_Portal_Role__c FROM " . $this->field_prefix  . "Layout__c";
            if ($module_name != "" && $view != "") {
                $query .= " WHERE ". $this->field_prefix  . "Module_Name__c = '".$module_name."' AND ". $this->field_prefix  . "View_Name__c = '".$view."' AND biztechcs__Customer_Portal_Role__r.biztechcs__is_primary__c = TRUE";
            }
            $query = str_replace( ' ', '+', $query );
            $url = $this->instance_url.$this->version_url."/query/?q=$query";
            $response = $this->call( $url );
            
            return $response;
        }
        
        /**
         * Run custom query
         *
         * @since 1.0.0
         * @param [type] $query
         * @return void
         */
        public function query( $query ) {

            $query = str_replace( ' ', '+', $query );
            $url = $this->instance_url.$this->version_url."/query/?q=$query";
            $response = $this->call( $url );

            return $response;
        }
        
        /**
         * Run custom call
         *
         * @since 1.0.0
         * @param [type] $url
         * @param [type] $method
         * @param array $data
         * @param integer $json_decode
         * @return void
         */
        public function customCall( $url, $method, $data = array(), $json_decode = 1 ) {

            $url = $this->instance_url.$url;
            if ( $json_decode ) {
                $response = $this->call( $url, $method, $data );
            } else {
                $response = $this->call( $url, $method, $data, 0 );
            }

            return $response;
        }

        /**
         * Global Search Data
         *
         * @since 1.0.0
         * @param [type] $cid
         * @param [type] $searchVal
         * @param [type] $data
         * @param string $limit
         * @param integer $offset
         * @return void
         */        
        public function globalSearchData($cid,$searchVal,$data,$limit = "",$offset=0) {
            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/v1/globalSearchData/";
            $parameter = array(
                "cid"   => $cid,
                "searchval" => $searchVal,
                "modules"   => $data,
            );
            if ($limit != "") {
                $parameter['limit'] = $limit;
                $parameter['offset'] = $offset;
            }
            $response = $this->call( $url, 'POST', $parameter );

            return $response;
        }
        
        /**
         * Function to search ContentNotes
         * 
         * @param type $cid
         * @param type $searchVal
         * @param type $data
         * @param type $limit
         * @param type $offset
         * @return type
         */
        public function globalContentNoteSearchData($cid, $searchVal, $data, $limit = "", $offset=0) {
            $url = $this->instance_url."/services/apexrest{$this->org_prefix}/v1/note_globalSearchData/";
            $parameter = array(
                "cid"   => $cid,
                "searchval" => $searchVal,
                "modules"   => $data,
            );
            if ($limit != "") {
                $parameter['limit'] = $limit;
                $parameter['offset'] = $offset;
            }
            
            $response = $this->call( $url, 'POST', $parameter );

            return $response;
        }
        
        /**
         * Function to get Notes related Cases
         * 
         * @param type $cid
         * @param type $record
         * @param type $relationship_field
         * @return type
         */
        public function getCaseNotesData($cid, $record, $relationship_field) {
            $url = $this->instance_url."/services/apexrest{$this->org_prefix}/v1/note_caseNoteData/";
            $parameter = array(
                "cid"                   => $cid,
                "record"                => $record,
                "relationship_field"    => $relationship_field,
            );
            
            $response = $this->call( $url, 'POST', $parameter );
            return $response;
        }
        
        /**
         * Search in solution
         *
         * @since 1.0.0
         * @param [type] $data
         * @return void
         */
        public function search( $data ) {

            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/rest/solutionlist";
            $response = $this->call( $url, 'POST', $data, 1);
            return $response;
        }
        
        /**
         * To send registration email
         *
         * @param type $cid
         * @param type $email
         * @return type
         */
        public function PortalForgotPasswordEmail( $email ) {

            $data = array(
                'Email' => $email
            );
            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/rest/PortalForgotPasswordEmail";
            $response = $this->call( $url, 'POST', $data, 1);
            return $response;
        }

        /**
         * Check if knowledgebase package is installed in salesfoce or not
         *
         * @since 1.0.0
         * @return void - 1 - When package installed, 0 - When not installed
         */
        public function CheckKBPackage() {

            $data = array(
                'temp' => 'temp',
            );
            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/rest/portal_knowledge_status";
            $response = $this->call( $url, 'POST', $data, 1);
            return $response;
        }

        /**
         * Get all module name with key
         *
         * @since 1.0.0
         * @return void
         */
        public function PortalModuleName() {

            $data = array(
                'temp' => 'temp',
            );
            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/rest/PortalModuleName";
            $response = $this->call( $url, 'POST', $data, 1);
            return $response;
        }

        /**
         * Custom call for dashboard to get topmoduls recentactivites charts
         *
         * @since 1.0.0
         * @param [type] $cid
         * @return void
         */
        public function PortalDashboard($cid,$bcp_counter = array(), $bcp_recent_activity = array(), $bcp_chart = array()) {
            
            $data = array(
                'cid' => $cid,
                'sfcp_counter' => $bcp_counter,
                'sfcp_recent_activity' => $bcp_recent_activity,
                'sfcp_chart' => $bcp_chart,
            );
            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/v1/portaldashboard/";
            $response = $this->call( $url, 'POST', $data, 1);
            return $response;
        }
        
        /**
         * Function for Dashboard ContentNote Call
         * 
         * @param type $cid
         * @param type $bcp_counter
         * @param type $bcp_recent_activity
         * @return type
         */
        public function PortalContentNoteDashboard($cid, $bcp_counter = array(), $bcp_recent_activity = array()) {
            
            $data = array(
                'cid' => $cid,
                'sfcp_counter' => $bcp_counter,
                'sfcp_recent_activity' => $bcp_recent_activity,
            );
            $url = $this->instance_url."/services/apexrest{$this->org_prefix}/v1/note_dashboard/";
            $response = $this->call( $url, 'POST', $data, 1);
            return $response;
        }

        /**
         * Custom call to update data
         *
         * @since 1.0.0
         * @param [type] $module_name
         * @param array $fields
         * @param array $view_layout_fields
         * @param string $id,
         * @param string $Id,
         * @param array $others
         * @return void
         */
        public function PortalUpdateData($module_name, $fields = array(), $view_layout_fields = array(), $id = "", $others = array()) {
            $data = array(
                'module' => $module_name,
                'data' => $fields,
                'retrievefields' => $view_layout_fields,
            );
            if ($others != NULL) {
                $data = array_merge($data,$others);
            }
            if ($id != "") {
                $data['id'] = $id;
            }

            if ($module_name == "contentnote") {
                $data['module_name'] = $data['module'];
                unset($data['module']);
                $url = $this->instance_url."/services/apexrest{$this->org_prefix}/v1/note_uploadFile/";
            } else {
                $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/v1/updateData/";
            }

            $response = $this->call( $url, 'POST', $data, 1);
            return $response;
        }

        /**
         * Custom call to update data
         *
         * @since 1.0.0
         * @param [type] $module_name
         * @param [type] $relate_id
         * @param [type] $cid
         * @param array $fields
         * @return void
         */
        public function PortalAttachmentAdd($module_name, $relate_id, $cid, $fields = array(), $for_module, $relationship_field) {
            $data = array(
                'cid' => $cid,
                'data' => $fields,
                'module_name' => $module_name,
                'for_module' => $for_module,
                'relationship_field' => $relationship_field,
                'relateid' => $relate_id
                );

            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/v1/uploadFile/";
            $response = $this->call( $url, 'POST', $data, 1);
            return $response;
        }

        /**
         * Custom call to get records
         *
         * @since 1.0.0
         * @param [type] $cid
         * @param [type] $module_name
         * @param array $fields
         * @param string $orderby
         * @param string $order
         * @param string $limit
         * @param integer $offset
         * @param string $search
         * @param array $search_field
         * @return void
         */
        public function PortalListData($cid, $relationship_field , $module_name, $fields = array(), $orderby = "", $order = "", $limit = "", $offset = 0, $search = "", $search_field = array(), $default_where = "") {
            $data = array(
                'module'    => $module_name,
                'fields'      => $fields,
                'cid'    => $cid,
                'relationship_field' => $relationship_field
            );
            if ($order != "" && $orderby != "") {
                $data['order'] = $order;
                $data['orderby'] = $orderby;
            }
            if ($search != "") {
                $data['search'] = $search_field;
            }
            if ($limit != "") {
                $data['limit'] = $limit;
            }
            if ($offset != "") {
                $data['offset'] = $offset;
            }
            if ($default_where != "") {
                $data['default_where'] = $default_where;
            }
            if ($module_name == "contentnote") {
                unset($data['relationship_field']);
                $url = $this->instance_url."/services/apexrest{$this->org_prefix}/v1/note_listData/";
            } else {
                $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/v1/listData/";
            }
            $response = $this->call( $url, 'POST', $data, 1);
            return $response;
        }

        /**
         * Custom call to get records
         *
         * @since 1.0.0
         * @param [type] $org_prefix
         * @param [type] $cid
         * @param [type] $module_name
         * @param array $fields
         * @param string $orderby
         * @param string $order
         * @param string $limit
         * @param integer $offset
         * @param string $search
         * @param string $search_field
         * @return void
         */
        public function PortalKBListData( $cid, $module_name, $fields = array(), $orderby = "", $order = "", $limit = "", $offset = 0, $search = "", $search_field = "") {
            $data = array(
                'module'    => $module_name,
                'fields'      => $fields,
                'cid'    => $cid,
            );
            if ($order != "" && $orderby != "") {
                $data['order'] = $order;
                $data['orderby'] = $orderby;
            }
            if ($search != "" && $search_field != "") {
                $data['search'][$search_field] = urldecode($search);
            }
            if ($limit != "") {
                $data['limit'] = $limit;
            }
            if ($offset != "") {
                $data['offset'] = $offset;
            }
            $url = $this->instance_url."/services/apexrest{$this->org_prefix}/v1/kbarticlelistData/";
            $response = $this->call( $url, 'POST', $data, 1);
            return $response;
        }

        /**
         * Custom call to get record by id
         *
         * @since 1.0.0
         * @param [type] $cid
         * @param [type] $module_name
         * @param [type] $record
         * @param array $fields
         * @param array $others
         * @return void
         */
        public function PortalGetDetailData($cid, $module_name, $record, $fields = array(), $others = array()) {
            $data = array(
                'cid'               => $cid,
                'module'            => $module_name,
                'record'            => $record,
                'retrievefields'    => $fields,

            ); 
            if ($others != NULL) {
                $data = array_merge($data,$others);
            }
            if ($module_name == "contentnote") {
                $url = $this->instance_url."/services/apexrest{$this->org_prefix}/v1/note_detailData/";
            } else {
                $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/v1/detailData/";
            }
            $response = $this->call( $url, 'POST', $data, 1);
            return $response;
        }
        
        /**
         * Update solution likes
         *
         * @since 1.0.0
         * @param [type] $cid
         * @param [type] $id
         * @param [type] $like
         * @param [type] $dislike
         * @return void
         */
        public function PortalUpdateSolutionsLikes($cid, $id, $like, $dislike) {
            
            $data = array(
                'cid'               => $cid,
                'solutionid'        => $id,
                'like'              => $like,
                'dislike'           => $dislike
            );
            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/v1/updateSolutionRatingAPI/";
            $response = $this->call( $url, 'POST', $data, 1);
            return $response;
        }
        
        /**
         * Singup
         *
         * @since 1.0.0
         * @param [type] $module
         * @param [type] $data
         * @return void
         */
        public function PortaSignup( $module, $data ) {
            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/v1/portalSignup/";
            $param['data'] = $data;
            $response = $this->call( $url, $method = 'POST', $param );
            return $response;
        }
        
        /**
         * Change Password In Reset and Change Password
         * 
         * @since 3.3.1
         * @param type $cid
         * @param type $data
         * @param type $flag
         * @return type
         */
        public function PortalChangePassword($cid, $data, $flag = FALSE) {
            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/v1/UpdatePassword/";
            $parameter = array(
                "cid"   => $cid,
                "data"  => $data,
                "flag"  => $flag,
            );
            $response = $this->call( $url, 'POST', $parameter );
            return $response;
        }
        
        /**
         * Function is used to get portal module description
         * 
         * @param type $module_name
         * @return type
         */
        public function GetModuleDescription($module_name) {
            $url = $this->instance_url.$this->version_url."/sobjects/$module_name/describe";
            $response = $this->call( $url, 'GET');
            return $response;
        }
        
        /**
         * Custom call to get All Products
         * @return void
         */
        public function PortalGetPricebookData() {
            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/v1/detailData/g/getPriceBookData/";
            $response = $this->call( $url, 'GET');
            return $response;
        }
        
        /**
         * Case close call
         * @return void
         */
        public function CaseCloseCall($cid, $recordId, $action) {
            $url = $this->instance_url."/services/apexrest/{$this->url_prefix}/v1/CaseClose/";
            if($action == 'close') {
                $IsClose = 0;
            } else if($action == 're-open') {
                $IsClose = 1;
            }
            $parameter = array(
                "cid"       => $cid,
                "recordId"  => $recordId,
                "IsClose"   => $IsClose,
            );
            $response = $this->call( $url, 'POST', $parameter );
            return $response;
        }
        
        /**
         * Portal layout call
         * @return void
         */
        public function PortalLayoutsCall($layout_type, $layout_params = array(), $where_condition = "") {

            $data = array('flag' => $layout_type);
            if ($layout_params != NULL) {
                $data['data'] = $layout_params;
            }
            if ($where_condition != "") {
                $data['whereCondition'] = $where_condition;
            }
            $url = $this->instance_url . "/services/apexrest/{$this->url_prefix}/rest/LayoutSync";
            $response = $this->call($url, 'POST', $data, 1);
            return $response;
        }
        /**
         * Portal layout call
         * 
        * @param type $module
        * @param type $record_id
        * @param type $fields
        * @param type $extra
        * @return type
        */
        public function getPortalRecord($module, $record_id, $fields, $extra = '') {

            $url = $this->instance_url . $this->version_url . "/sobjects/$module/$record_id/$extra";
            if ($fields != null) {
                $fields = json_decode($fields);
                $fields = implode(',', array_keys((array) $fields));
                $url .= "?fields=$fields";
            }
            if ($extra == 'body') {
                $response = $this->call($url, '', array(), 0);
            } else {
                $response = $this->call($url);
            }

            return $response;
        }

    }
}

if ( !function_exists( 'sf_authentication' ) ) {
    /**
     * Salesforce authentication
     *
     * @param [type] $client_id
     * @param [type] $client_secret
     * @param [type] $username
     * @param [type] $password
     * @return void
     */
    
}
