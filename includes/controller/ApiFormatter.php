<?php

/* 
 * This is ApiFormatter Class file. This will format api calls and response.
 * 
 * The use of this file is common format for all kind of crm calls.
 */

if ( !class_exists("ApiFormatter") ) {
    
    class ApiFormatter {
        
        private $bcp_action;
        public function __construct() {
            $this->bcp_action = new BCPCustomerPortalActions(BCP_CUSTOMER_PORTAL_NAME, BCP_CUSTOMER_PORTAL_VERSION);
        }
        
        /**
         * Function to Related Fields By Module
         * @param type $module_name
         * @return string
         */
        function getRelatedFieldsByModule($module_name) {

            $module_fields = array(
                'case' => array(
                    'Label' => 'Subject',
                    'Value' => 'Id',
                ),
                'contract' => array(
                    'Label' => 'ContractNumber',
                    'Value' => 'Id',
                ),
                'order' => array(
                    'Label' => 'OrderNumber',
                    'Value' => 'Id',
                ),
                'call' => array(
                    'Label' => 'Subject',
                    'Value' => 'Id',
                ),
                'contentdocument' => array(
                    'Label' => 'ContentDocument.Title',
                    'Value' => 'ContentDocumentId',
                ),
                'event' => array(
                    'Label' => 'Subject',
                    'Value' => 'Id',
                ),
                'opportunity' => array(
                    'Label' => 'Name',
                    'Value' => 'Id',
                ),
                'task' => array(
                    'Label' => 'Subject',
                    'Value' => 'Id',
                ),
            );

            $default_search_fields = array(
                'Label' => 'Name',
                'Value' => 'Id',
            );

            return (isset($module_fields[$module_name]) ? $module_fields[$module_name] : $default_search_fields);
        }        
        
        /**
         * Function to Format List call
         * @param type $layout
         * @param type $records
         * @param type $module_name
         * @return string
         */
        
        function getFormattedListCall($layout, $records, $module_name) {
            
            $list_Arr = array();
            $layout_Arr = array();
            $recordArr = array();

            $layout_data = unserialize( $layout->layout );

            $layout_fields = array();
            if ( $layout_data != null ) {
                foreach( $layout_data as $layout_data_value ) {
                    $field_name = $layout_data_value->name;
                    $layout_fields[$field_name] = $layout_data_value;
                }
            }
            
            if (isset($records->records) && $records->records != null) {
                $fields = json_decode($layout->fields);
                if ($module_name == "contentdocument") {
                    $fields->FileExtension = 'FileExtension';
                }
                // Added By: Dharti Gajera. attachment add ContentType.
                if ($module_name == "attachment") {
                    $fields->ContentType = 'ContentType';
                }
                $layout_Arr = $fields;
                $i=0;
                foreach ($records->records as $record) { 

                    
                    unset($record->attributes);
                    foreach ($fields as $key => $field_value) {
                        $layout_field = $layout_fields[$key];
                        $field_name = $layout_field->name;
                        
                        $value = ( ( is_object($record) && isset($record->$field_name) ) ? stripslashes($record->$field_name) : '' );

                        if($field_name == "Content" && $module_name == "contentnote"){
                            $value = base64_decode($value);
                        }
                        
                        // Get Relate field Value in list call by passing relationship and field name
                        if ($layout_fields[$key]->type == "reference") {
                            $relation_field_name = $layout_fields[$key]->relationshipName;

                            $ref_object_name = $layout_fields[$key]->referenceTo[0];
                            $value = ( ( is_object( $record ) && isset( $record->$relation_field_name->Name ) ) ? stripslashes($record->$relation_field_name->Name) : '' );
                            if($ref_object_name == 'Case'){
                                $value = ( ( is_object( $record ) && isset( $record->$relation_field_name->Subject ) ) ? stripslashes($record->$relation_field_name->Subject) : '' );
                            }
                        } else if ($layout_fields[$key]->type == 'currency' && $value) {
                            $value = $_SESSION['bcp_default_currency'] . ' ' . number_format($value, 2);
                        } else if ($layout_fields[$key]->type == 'datetime' && $value) {
                            $value = $this->bcp_action->bcp_show_date($value);
                        } else if ($layout_fields[$key]->type == 'multipicklist' && $value) {
                            $value = str_replace(";", " , ", $value);
                        } else if ( $layout_fields[$key]->type == 'boolean' ) {
                            $value = ( $value ? "Checked" : "Not Checked" );
                        } else if ( $layout_fields[$key]->type == 'time' && $value ){
                            $value = date("H:i", strtotime($value));
                        }
                        // Added By: Dharti Gajera. attachment add ContentType.
                        if ($layout_fields[$key]->type == "") {
                            continue;
                        }
                        
                        if (!is_object($value)) {
                            if (strlen($value) > 100) {
                                $value = substr($value, 0, 100);
                            }
                        } else {
                            $value = '-';
                        }
                        if ($value == '' || $value == NULL) {
                            $value = '-';
                        }

                        $list_Arr[$i][$field_name] = __($value, "bcp_portal");
                    }
                    if($module_name == 'case') {
                        if($record->IsClosed) {
                            $list_Arr[$i]['IsClosed'] = TRUE;
                        } else {
                            $list_Arr[$i]['IsClosed'] = FALSE;
                        }
                    }
                    if($record->Id) {
                        $list_Arr[$i]['record_id'] = $record->Id;
                    }
                    if(isset($record->FileExtension)){
                        if (!in_array($module_name, array('contentnote', 'contentdocument')) && $record->FileExtension) {
                            $list_Arr[$i]['FileExtension'] = $record->FileExtension;
                        }
                    }
                    // Added By: Dharti Gajera. attachment add ContentType.
                    if(isset($record->ContentType)){
                        $list_Arr[$i]['ContentType'] = $record->ContentType;
                    }

                    //if($_SESSION['bcp_relationship_type'] == "Account-Based"){
                        $contact_relationship = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                        $contact_relationship_value = $record->$contact_relationship;
                        $list_Arr[$i]['RecordCreatedByContact'] = $contact_relationship_value;
                   // }

                    $i++;
                }
            }

            if(!empty($layout_Arr) && !empty($list_Arr)) {
                $recordArr['layout'] = $layout_Arr;
                $recordArr['records'] = $list_Arr;
                if (isset($_SESSION['bcp_module_access_data']->$module_name->edit) && $_SESSION['bcp_module_access_data']->$module_name->edit == 'true') {
                    $recordArr['Action'][] = 'edit';
                }
                if ($module_name == 'case' && isset($_SESSION['bcp_module_access_data']->solution->access) && $_SESSION['bcp_module_access_data']->solution->access == 'true') {
                    $recordArr['Action'][] = 'solution';
                }
                if (isset($_SESSION['bcp_module_access_data']->$module_name->delete) && $_SESSION['bcp_module_access_data']->$module_name->delete == 'true' && $module_name != "account") {
                    $recordArr['Action'][] = 'delete';

                }
                if($module_name == "case"){
                    $recordArr['Action'][] = 're-open';
                    $recordArr['Action'][] = 'close';
                }
                if ($module_name == 'contentdocument' || $module_name == 'attachment') {
                    $recordArr['Action'][] = 'download';
                    $recordArr['Action'][] = 'preview';
                }
            }
            return $recordArr;
            
        }
        
        /**
         * Function to Format Form call
         * @param type $module_name
         * @return string
         */
        function getFormattedFormCall($layout, $record = array(), $module_name,$record_id="",$exclude_fields) {
            global $bcp_field_prefix, $wpdb, $objCp;
               
            $Username__c = $bcp_field_prefix . 'Username__c'; // For Sigup Form Submit
            $Password__c = $bcp_field_prefix . 'Password__c'; // For Sigup Form Submit

            $contact_id = isset( $_SESSION['bcp_contact_id'] ) ? $_SESSION['bcp_contact_id'] : "";
            $layout_data = $layout->layout;
            $layout_fields = array();
            $layout_field_properties = ( isset($layout->properties) && !empty($layout->properties) ) ? json_decode($layout->properties, 1) : array();
            $layout_advance_properties_properties = ( isset($layout->advance_properties) && !empty($layout->advance_properties) ) ? json_decode($layout->advance_properties, 1) : array();

            $Fieldnames = array();
            $catagotyfields = array();

            foreach ($layout_advance_properties_properties as $layout_advance_properties_property) {
                if($layout_advance_properties_property['DefaultVisibility'] == 'Hidden') {
                    $Fieldnames[] = $layout_advance_properties_property['FieldName'];
                }
                $catagotyfields[] = $layout_advance_properties_property['FieldVisibilityCatagory'][0]['Catagotyfield'];
            }
            $catagotyfields = array_unique($catagotyfields);


            $fields_hides = array();
            $i=0;
            foreach ($layout_advance_properties_properties as $layout_advance_properties_property) {

                $Catagotyfield = $layout_advance_properties_property['FieldVisibilityCatagory'][0]['Catagotyfield'];
                $CatagotyfieldOption = $layout_advance_properties_property['FieldVisibilityCatagory'][0]['CatagotyfieldOption'];
                $DefaultVisibility = $layout_advance_properties_property['DefaultVisibility'];
                $FieldName = $layout_advance_properties_property['FieldName'];
                $MultiPicklistOption = $layout_advance_properties_property['FieldVisibilityCatagory'][0]['MultiPicklistOption'];
                $data_value = ( ( $record_id && isset( $record[$Catagotyfield] ) ) ? $record[$Catagotyfield] : '' );
                $selectvalue = explode(";", $data_value);

                if(($MultiPicklistOption == 'ANY') && !empty($selectvalue) && (count(array_intersect($selectvalue,$CatagotyfieldOption)) >= 1)) {
                        if($DefaultVisibility == 'Hidden') {
                            $fields_hides[$FieldName]['fieldname'] = $FieldName;
                            $fields_hides[$FieldName]['fieldshow'] = 'show';
                        } else {
                            $fields_hides[$FieldName]['fieldname'] = $FieldName;
                            $fields_hides[$FieldName]['fieldshow'] = 'hide';
                        }
                } else if(($MultiPicklistOption == 'EXACT') && !empty($selectvalue)) {
                    $temp = array($CatagotyfieldOption);
                    if(in_array($selectvalue, $temp)) {
                        if($DefaultVisibility == 'Hidden') {
                            $fields_hides[$FieldName]['fieldname'] = $FieldName;
                            $fields_hides[$FieldName]['fieldshow'] = 'show';
                        } else {
                            $fields_hides[$FieldName]['fieldname'] = $FieldName;
                            $fields_hides[$FieldName]['fieldshow'] = 'hide';
                        }
                    }
                } else {
                    if($DefaultVisibility == 'Hidden') {
                        $fields_hides[$FieldName]['fieldname'] = $FieldName;
                        $fields_hides[$FieldName]['fieldshow'] = 'hide';
                    } else {
                        $fields_hides[$FieldName]['fieldname'] = $FieldName;
                        $fields_hides[$FieldName]['fieldshow'] = 'show';
                    }
                }
                $i++;
            }

            if ($layout_data != null) {
                foreach ($layout_data as $layout_data_value) {
                    $layout_fields[$layout_data_value->name] = $layout_data_value;
                }
            }

            if($module_name == 'contentdocument'){
                $fields = (array) json_decode($layout->section_layout);
                $section_layout = $fields;
            } else {
                $section_layout = (array) json_decode($layout->section_layout);
            }

            $fields_object = array();
            $i = 0;

            foreach ($section_layout as $value1) {
                $rows = $value1->Field_rows;
                $fields_object[$i]['section_name'] = $value1->Section_name;
                $m=0;
                foreach ($rows as $value2) {
                    $value2 = (array)$value2;
                    $row_array = array_values($value2);
                    $j = 0;
                    foreach ($row_array as $value) {
                        if(isset($layout_fields[$value->name])) {
                            $layout_name = $value->name;
                            $layout_field = $layout_fields[$layout_name];

                            if( in_array($layout_name,$exclude_fields) && $contact_id != "" && $module_name == "contact" ) {
                                continue;
                            }

                            if ( $layout_name == 'AccountId' && $module_name == 'contract' ) { 
                                continue;
                            }
                            
                            $ftype = "text";
                            $required = "";
                            $disabled = '';
                            $helptip = '';
                            $placeholder = '';
                            $validationMessage = '';
                            $class = "";
                            $referenceTo = "";
                            $multiPicklistSelectionLimit = "";
                            $hideFields = "";
                            $default_picklist = "";


                            $label = ( isset($layout_field_properties[$layout_name]['portal_lable']) && !empty($layout_field_properties[$layout_name]['portal_lable']) ) ? $layout_field_properties[$layout_name]['portal_lable'] : $value->label;

                            $name = isset($layout_field->name) ? $layout_field->name : "";
                            if ( ( $layout_field->name == 'Email' && $module_name == "contact" ) || $layout_field->name == $Username__c || $layout_field->name == $Password__c){
                                $layout_field->nillable = FALSE;
                            }

                            if ($module_name == 'case' && $layout_name == 'Subject') {
                                $layout_field->nillable = FALSE;
                            }

                            if ($module_name == 'contentnote' && $layout_name == 'Content') {
                                $layout_field->nillable = FALSE;
                            }

                            if ( ( isset($layout_field_properties[$layout_name]['required']) && $layout_field_properties[$layout_name]['required'] == "true" ) || (!$layout_field->nillable && !$layout_field->createable && !$layout_field->defaultedOnCreate ) ) {
                                $required = 1;

                                if (in_array($layout_field->type, array( 'email', 'phone', 'url', 'text', 'textarea', 'string', 'picklist', 'multipicklist' ))) {
                                    $class .= " sfcp-requiredfield";
                                }
                            }

                            if ($record_id != '' && !$layout_field->updateable) { // If this is update request and field is not updateable than make it disabled
                                $disabled = "disabled='disabled'";
                            }

                            if ( $layout_field->name == $Username__c && $contact_id != "" ) {
                                $disabled = "disabled='disabled'";
                            }
                            if ( $layout_field->name == 'Email' && $module_name == "contact" && $contact_id != ""  ) { // If this isemail field or not updateable field than make it disabled
                                $disabled = "disabled='disabled'";
                            }

                            if ( $contact_id != "" && isset($layout_field_properties[$layout_name]['read_only']) && $layout_field_properties[$layout_name]['read_only'] == "true" ) {
                                $disabled = "disabled='disabled'";
                            }

                            if ( isset($layout_field_properties[$layout_name]['help_tip']) && $layout_field_properties[$layout_name]['help_tip'] != "" ) {
                                $helptip = htmlentities($layout_field_properties[$layout_name]['help_tip']);
                            }
                            if ( isset($layout_field_properties[$layout_name]['placeholder']) && $layout_field_properties[$layout_name]['placeholder'] != "" ) {
                                $placeholder = htmlentities($layout_field_properties[$layout_name]['placeholder']);
                            }
                            if ( isset($layout_field_properties[$layout_name]['validationMessage']) && $layout_field_properties[$layout_name]['validationMessage'] != "" ) {
                                $validationMessage = htmlentities($layout_field_properties[$layout_name]['validationMessage']);
                            }

                            if ( isset($layout_field_properties[$layout_name]['multiPicklistSelectionLimit']) && $layout_field_properties[$layout_name]['multiPicklistSelectionLimit'] != "" ) {
                                $multiPicklistSelectionLimit = htmlentities($layout_field_properties[$layout_name]['multiPicklistSelectionLimit']);
                            }

                            if ( isset($layout_field_properties[$layout_name]['default_picklist']) && $layout_field_properties[$layout_name]['default_picklist'] != "" ) {
                                $default_picklist = $layout_field_properties[$layout_name]['default_picklist'];
                            }

                            $data_value = ( ( $record_id && isset( $record[$name] ) ) ? $record[$name] : '' );
                            if ( !$record_id && $disabled != ""  ) {
                                $data_value = ( isset($layout_field->defaultValue) && $layout_field->defaultValue != "" ) ? $layout_field->defaultValue : "";
                            }

                            if ( $layout_field->type == "email" ) {
                                $ftype = "email";
                                $class .= " sfcp-email";
                            }
                            if ( $layout_field->type == "double" ) {
                                $ftype = "number";
                                $class .= " sfcp-double";
                            }
                            if ( $layout_field->type == "currency" ) {
                                $class .= " sfcp-currency";
                            }
                            if ( $layout_field->type == "percent" ) {
                                $class .= " sfcp-percent";
                            }
                            if ( $layout_field->type == "date" ) {
                                $class .= " sfcp-date datepicker ";
                                $data_value = ($data_value != "") ? date("Y/m/d", strtotime($data_value)) : "";
                            }
                            if ( $layout_field->type == "datetime" ) {
                                $class .= " sfcp-datetime datepicker ";
                                $data_value = $this->bcp_action->bcp_show_date($data_value);
                                $data_value = ($data_value != "") ? date("Y/m/d H:i", strtotime($data_value)) : "";
                            }
                            if ( $layout_field->type == "time" ) {
                                $class .= " sfcp-time timepicker ";
                                $data_value = ($data_value != "") ? date("H:i", strtotime($data_value)) : "";
                            }

                            if ( $data_value != "" && $module_name == 'contentnote' && $value->name == "Content") {
                                $data_value = base64_decode($data_value);
                            }

                            if( $layout_field->type == "boolean" ) {
                                $ftype = "boolean";
                                $class .= " sfcp-checkbox";
                            }

                            if ( $layout_field->type == "phone" ) {
                                $class .= " sfcp-phone";
                            }
                            if ( $layout_field->type == "url" ) {
                                $class .= " sfcp-url";
                            }
                            if ($layout_field->type == 'multipicklist') {
                                $ftype = "multi-select";
                                $class .= " sfcp-multipicklist";
                            }
                            if ($layout_field->type == 'picklist') {
                                $ftype = "select";
                                $class .= " sfcp-picklist";
                            }
                            if ( $layout_field->name == $Password__c ) {
                                $ftype = "password";
                                $class .= " sfcp-password";
                            }
                            if ( $layout_field->type == "textarea" || $layout_field->type == "base64" ) {
                                $ftype = "textarea";
                                $class .= " sfcp-textarea";
                            }
                            if ($layout_field->type == 'reference') {
                                $ftype = "reference";
                                $class .= " sfcp-picklist";
                                $referenceTo = strtolower(isset($layout_field->referenceTo[0]) ? $layout_field->referenceTo[0] : '');
                            }

                            if(in_array($value->name, $catagotyfields)){
                                $class .= " sfcp-catagotyfield";
                            }

                            if((empty($record_id)) || (($layout_field->type == 'picklist' || $layout_field->type == 'multipicklist') && !empty($record_id) && $data_value =='' ) ){
                                $data_value = $default_picklist;
                            }


                            /* hide fields tmp */
                            if(!empty($record_id)) {
                                if(array_key_exists($name, $fields_hides)){
                                    $hideFields = $fields_hides[$name]['fieldshow'];
                                }
                            } else if(in_array($name, $Fieldnames)){
                                $hideFields = 'hide';
                            }
                            if ( $layout_field->name == "FileExtension" || $layout_field->name == "Body" ) {
                                $helptip = 'Only [jpg, jpeg, png, gif, doc, docx, pdf, txt, csv, xls, xlsx, zip] file formats allowed.';
                                $ftype = "file";
                                $class .= " sfcp-file";
                                $fields_object[$i]['rows'][$m][$j]['type'] = $ftype;
                                $fields_object[$i]['rows'][$m][$j]['label'] = 'File';
                                $fields_object[$i]['rows'][$m][$j]['name'] = "Attachment";
                                $fields_object[$i]['rows'][$m][$j]['disabled'] = '';
                                $fields_object[$i]['rows'][$m][$j]['required'] = 1;
                                $fields_object[$i]['rows'][$m][$j]['value'] = $data_value;
                                $fields_object[$i]['rows'][$m][$j]['class'] = $class;
                                $fields_object[$i]['rows'][$m][$j]['helptip'] = $helptip;
                                $fields_object[$i]['rows'][$m][$j]['placeholder'] = $placeholder;
                                $fields_object[$i]['rows'][$m][$j]['validationMessage'] = $validationMessage;
                                $fields_object[$i]['rows'][$m][$j]['selectionlimit'] = $multiPicklistSelectionLimit;
                            }
                            if( $module_name == 'contentdocument' || $module_name == 'attachment' ){
                                $required = 1;
                            }

                            if( $layout_field->createable || $layout_field->updateable ) {
                                $fields_object[$i]['rows'][$m][$j]['type'] = $ftype;
                                $fields_object[$i]['rows'][$m][$j]['label'] = $label;
                                $fields_object[$i]['rows'][$m][$j]['name'] = $name;
                                $fields_object[$i]['rows'][$m][$j]['disabled'] = $disabled;
                                $fields_object[$i]['rows'][$m][$j]['required'] = $required;
                                $fields_object[$i]['rows'][$m][$j]['value'] = $data_value;
                                $fields_object[$i]['rows'][$m][$j]['class'] = $class;
                                $fields_object[$i]['rows'][$m][$j]['helptip'] = $helptip;
                                $fields_object[$i]['rows'][$m][$j]['placeholder'] = $placeholder;
                                $fields_object[$i]['rows'][$m][$j]['validationMessage'] = $validationMessage;
                                $fields_object[$i]['rows'][$m][$j]['selectionlimit'] = $multiPicklistSelectionLimit;
                                $fields_object[$i]['rows'][$m][$j]['hideFields'] = $hideFields;
                                if ($layout_field->type == 'picklist' || $layout_field->type == 'multipicklist') {
                                    if ( isset($layout_field_properties[$layout_name]['portalVisiblePicklistValues']) && $layout_field_properties[$layout_name]['portalVisiblePicklistValues'] != "" && !empty($layout_field_properties[$layout_name]['portalVisiblePicklistValues'])) {

                                        $portalVisiblePicklistValues = $layout_field_properties[$layout_name]['portalVisiblePicklistValues'];
                                        $options = array();
                                        $f=0;
                                        foreach ($portalVisiblePicklistValues as $k => $picklist_value) {  
                                            $options[$f]['value'] = $k;
                                            $options[$f]['label'] = __($picklist_value, "bcp_portal");
                                            $f++;
                                        }
                                        $fields_object[$i]['rows'][$m][$j]['option'] = $options;
                                    } else if ($layout_field->picklistValues != null) {
                                        $options = array();
                                        foreach ($layout_field->picklistValues as $k => $picklist_value) {
                                            $options[$k]['value'] = $picklist_value->value;
                                            $options[$k]['label'] = __($picklist_value->label, "bcp_portal");
                                        }
                                        if (($module_name == 'contract' || $module_name == 'order') && $name == 'Status' && !$record_id){
                                            $options = array();
                                            $options[$k]['value'] = 'Draft';
                                            $options[$k]['label'] = __('Draft', "bcp_portal");
                                        }
                                        $fields_object[$i]['rows'][$m][$j]['option'] = $options;
                                    }
                                }
                            }

                            if ($layout_field->type == 'reference') {
                                $fields_object[$i]['rows'][$m][$j]['referenceTo'] = $referenceTo;
                                $bcp_modules = isset($_SESSION['bcp_modules']) ? $_SESSION['bcp_modules'] : array();
                                if ( (isset($bcp_modules[$referenceTo]) && !empty($bcp_modules[$referenceTo])) && $module_name != $referenceTo ) {

                                    $data_fields = $this->getRelatedFieldsByModule($referenceTo);
                                    $data_field_values = array_values($data_fields);

                                    if( $_SESSION['bcp_relationship_type'] == "Account-Based"){ 
                                        $relationship_field_key = $_SESSION['bcp_module_access_data']->$referenceTo->accountRelationship;
                                        $contact_id = $_SESSION['bcp_account_id'];
                                        
                                        if($referenceTo == "account"){ 
                                            $relationship_field_key = $_SESSION['bcp_module_access_data']->$referenceTo->contactRelationship;
                                            $contact_id = $_SESSION['bcp_contact_id'];
                                        }
                                    }else{
                                        $relationship_field_key = $_SESSION['bcp_module_access_data']->$referenceTo->contactRelationship;
                                        $contact_id = $_SESSION['bcp_contact_id'];
                                    }
                                   
                                    $where_condition = "";
                                    if ($referenceTo == 'contract' && isset($_SESSION['bcp_account_id']) && $_SESSION['bcp_account_id'] != '') {
                                        $where_condition = "AccountId='". $_SESSION['bcp_account_id']."'";
                                    }
                                    $arguments = array(
                                        "cid"                   => $contact_id,
                                        "relationship_field"    => $relationship_field_key,
                                        "module_name"           => $referenceTo,
                                        "fields"                => $data_field_values,
                                        "where_condition"       => $where_condition,
                                    );
                                  
                                    $relate_records = $objCp->getPortalListData($arguments);
                                  
                                    $options = array();
                                    if ($relate_records->records != null) {

                                        $field_value = $data_fields['Value'];
                                        $field_label = $data_fields['Label'];

                                        foreach ($relate_records->records as $k => $relate_record) {
                                            $options[$k]['value'] = $relate_record->$field_value;
                                            $options[$k]['label'] = $relate_record->$field_label ? $relate_record->$field_label : "N/A";
                                        }
                                    }
                                    $fields_object[$i]['rows'][$m][$j]['option'] = $options;
                                }
                            }
                            $j++;
                        }
                    }
                    $m++; 
                }
                $i++;
            }
            foreach ($fields_object as $k => $fieldsobject) {
                if(!isset($fieldsobject['rows']) && empty($fieldsobject['rows'])) {
                    unset($fields_object[$k]);
                }
            }
            reset($fields_object);
            return $fields_object;
        }
    }
}
