<?php
/**
 * The file used to manage list page style 2.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */
$cls = '';
if ($view == '') {
    $cls = 'subpanel-div';
}
?>
<!-- listing-2 style-2 Start -->
<div class="listing-2 <?php echo $cls; ?>">
    <!-- listing-style-2 Start -->
    <div class="listing-block listing-style-2">
        <!-- case-listing-block Start -->
        <div class="case-listing-block bg-white shadow">
            <!-- bcp-justify-content-between Start -->
            <div class="bcp-flex bcp-justify-content-between">
<?php if ($view == '') { ?>
                    <?php if (isset($module_icon) && !empty($module_icon)) { echo '<span class="' . $module_icon . ' heading-icon"></span>'; } ?>
                    <!-- heading-part Start -->
                    <div class="heading-part">
                        <h2 class="" id="dcp-head-incident">
    <?php _e($heading_modulename, 'bcp_portal'); ?>
                        </h2>
                        <h6><?php _e($module_sub_title, 'bcp_portal'); ?></h6>
                    </div>
                    <!-- heading-part End -->
                    <!-- listing-search Start -->
                    <div class="bcp-form-group listing-search">
    <?php
    $class = '';
    if (isset($search) && $search != '') {
        $class = 'active';
    }
    ?>
                        <input class="module_search_name <?php echo $class; ?>" module-name="<?php echo $module_name; ?>" placeholder="<?php _e('Search'); ?>" value="<?php echo (isset($search) && $search != '') ? $search : ''; ?>"><a href="JavaScript:void(0);" id="btn_clear" module-name="<?php echo $module_name; ?>" title="<?php _e("Clear"); ?>"><span class="lnr lnr-cross clear-btn"></span></a>
                        <button class="module_search_btn bcp-btn"><span class="lnr lnr-magnifier"></span></button>
                    </div>
                    <!-- listing-search End -->
<?php } ?>
                <!-- bcp_filter_header_action Start -->
                <div class="bcp_filter_header_action">
<?php if ($add_module) { ?>
                        <a href="<?php echo $add_module_href; ?>" class="" title="<?php _e('Add', 'bcp_portal'); ?>">
                            <button class="bcp-btn">
                                <span class="lnr lnr-plus-circle"></span>
                                <p><?php echo __('Add', 'bcp_portal'); ?></p>
                            </button>
                        </a>
<?php } ?>
                    <?php if ($display_advance_filter && $view == '') { ?>
                        <a href="javascript:void(0)" class="btn-filter" title="<?php _e('Advance Filter', 'bcp_portal'); ?>">
                            <button class="bcp-btn-filter bcp-btn">
                                <span class="lnr lnr-funnel"></span>
                            </button>
                        </a>
<?php } ?>
                </div>
                <!-- bcp_filter_header_action End -->
            </div>
            <!-- bcp-justify-content-between End -->
        </div>
        <!-- case-listing-block End -->
<?php
if ($display_advance_filter && $view == '') {
    $filter_display = "style='display:none;'";
    if (!empty($where_cond_array)) {
        $filter_display = "";
    }
    ?>
            <!-- filter-list-form Start -->
            <form id="filter-list-form" class="module-form module-case" <?php echo $filter_display; ?> >
                <!-- bcp-row Start -->
                <div class="bcp-row">
                    <?php
                    $layout = json_decode(json_encode($layout), 1);
                    foreach ($fields as $key => $value) {
                        if (isset($fields_meta[$key]['searchable']) && $fields_meta[$key]['searchable'] == "true") {
                            //$k = array_search($key, array_column($layout, 'name'));
                            $field_information = isset($layout[$key]) ? $layout[$key] : array();
                            if (!empty($field_information)) {
                                $field_type = $field_information['type'];
                                $helptip = "";
                                $label = isset($fields_meta[$key]['portal_lable']) ? $fields_meta[$key]['portal_lable'] : $value;
                                $name = isset($field_information['name']) ? $field_information['name'] : "";
                                $helptip_content = isset($field_information['helptip']) ? $field_information['helptip'] : "";
                                if ($helptip_content) {
                                    $helptip = '<span data-toggle="tooltip" data-placement="top" data-title="' . $helptip_content . '"><em class="fa fa-info-circle"></em></span>';
                                }
                                $ftype = "text";
                                $class = "where_field ";
                                $input_icon = "";

                                if ($field_type == "email") {
                                    $ftype = "email";
                                    $class .= " sfcp-email";
                                }
                                if ($field_type == "double") {
                                    $ftype = "number";
                                    $class .= " sfcp-double";
                                }
                                if ($field_type == "currency") {
                                    $class .= " sfcp-currency";
                                }
                                if ($field_type == "percent") {
                                    $class .= " sfcp-percent";
                                }
                                if ($field_type == "date") {
                                    $class .= " sfcp-date";
                                    $input_icon = "fa fa-calendar";
                                }
                                if ($field_type == "datetime") {
                                    $class .= " sfcp-datetime";
                                    $input_icon = "fa fa-calendar";
                                }
                                if ($field_type == "time") {
                                    $class .= " sfcp-time";
                                    $input_icon = "fa fa-clock-o";
                                }

                                if ($field_type == "boolean") {
                                    $ftype = "boolean";
                                    $class .= " sfcp-checkbox";
                                }

                                if ($field_type == "phone") {
                                    $class .= " sfcp-phone";
                                }
                                if ($field_type == "url") {
                                    $class .= " sfcp-url";
                                }
                                if ($field_type == 'multipicklist' || $field_type == 'picklist') {
                                    $ftype = "multi-select";
                                    $class .= " sfcp-multipicklist custom-multi-select";
                                }
                                if ($field_type == "textarea" || $field_type == "base64") {
                                    $ftype = "textarea";
                                    $class .= " sfcp-textarea";
                                }
                                if ($field_type == 'reference') {
                                    $ftype = "reference";
                                    $class .= " sfcp-picklist";
                                }

                                $data_value = (isset($where_cond_array[$key]) && !empty($where_cond_array[$key])) ? $where_cond_array[$key] : "";

                                if (is_array($data_value) && array_key_exists('date-range', $data_value)) {
                                    $data_value = $data_value['date-range'][0] . ' - ' . $data_value['date-range'][1];
                                }

                                if ($ftype == 'textarea') {
                                    ?>
                                                    <!-- bcp-col-lg-4 Start -->
                                                    <div class="bcp-col-lg-4">
                                                        <div class="bcp-form-group">
                                                            <div class="bcp-flex bcp-align-items-center">
                                                                <label for="<?php echo $name; ?>"><?php _e($label . $helptip, 'bcp_portal'); ?></label>
                                                            </div>
                                                            <textarea class="<?php echo $class; ?>" id="<?php echo $name; ?>" name="<?php echo $name; ?>" ><?php echo $data_value; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <!-- bcp-col-lg-4 End -->
                                    <?php
                                } else if ($field_type == "percent" || in_array($ftype, array('multi-select', 'select'))) {
                                    if ($data_value == '') {
                                        $data_value = array();
                                    }
                                    $option_values = array();
                                    if ($field_type == "percent") {
                                        $option_values = $percentage_option_values;
                                    } else if ($field_information['picklistValues'] != null) {
                                        foreach ($field_information['picklistValues'] as $k => $picklist_value) {
                                            $option_values[$k]['value'] = $picklist_value['value'];
                                            $option_values[$k]['label'] = $picklist_value['label'];
                                        }
                                    }
                                    $field_name = $name . '[]';
                                    if (isset($option_values) && count($option_values) > 0) {
                                        ?>
                                                        <!-- bcp-col-lg-4 Start -->
                                                        <div class="bcp-col-lg-4">
                                                            <div class="bcp-form-group">
                                                                <div class="bcp-flex bcp-align-items-center">
                                                                    <label for="<?php echo $name; ?>"><?php _e($label . $helptip, 'bcp_portal'); ?></label>
                                                                </div>
                                                                <div class="multi-select">
                                                                    <div class="multi-select-title">
                                                                        <label class="bcp-picklist-title" for=""><?php echo (!empty($data_value) && is_array($data_value)) ? "<span>" . count($data_value) . "</span> Selected." : "Select"; ?></label>
                                                                        <em class="fa fa-sort-down arrow-down"> </em>
                                                                    </div>
                                                                    <div class="multi-select-dropdown">
                                                                        <ul>
                                        <?php
                                        foreach ($option_values as $option) {
                                            $checked = '';
                                            if (in_array($option['value'], $data_value)) {
                                                $checked = 'checked="checked"';
                                            }
                                            ?>
                                                                                <li><input <?php echo $checked; ?> class="where_field sfcp-multi-dropdown" type="checkbox" name="<?php echo $field_name; ?>" value="<?php echo $option['value']; ?>" id="<?php echo $option['value']; ?>"> <label for="<?php echo $option['value']; ?>"><?php echo $option['label']; ?></label></li>
                                                                            <?php } ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- bcp-col-lg-4 End -->
                                        <?php
                                    }
                                } else if ($ftype == 'boolean') {
                                    ?>
                                                    <!-- bcp-col-lg-4 Start -->
                                                    <div class="bcp-col-lg-4">
                                                        <div class="bcp-form-group">
                                                            <div class="form-check form-check-inline bcp-flex bcp-align-items-center">
                                                                <input type="hidden" name="<?php echo $name; ?>" value="0" />
                                                                <input class="form-check-input <?php echo $class; ?>" name="<?php echo $name; ?>" type="checkbox" id="<?php echo $name; ?>" value="1">
                                                                <label class="form-check-label" for="<?php echo $name; ?>"><?php _e($label . $helptip, 'bcp_portal'); ?></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- bcp-col-lg-4 End -->
                                <?php } else { ?>
                                                    <!-- bcp-col-lg-4 Start -->
                                                    <div class="bcp-col-lg-4">
                                                        <div class="bcp-form-group">
                                                            <div class="bcp-flex bcp-align-items-center">
                                                                <label for="<?php echo $name; ?>"><?php _e($label . $helptip, 'bcp_portal'); ?></label>
                                                            </div>
                                                            <input value="<?php echo $data_value; ?>" id="<?php echo $name; ?>" type="<?php echo $ftype; ?>" class="bcp-form-control input-text <?php echo $class; ?>" name="<?php echo $name; ?>" >
                                    <?php if (!empty($input_icon)) { ?>
                                                                &nbsp;<em class="<?php echo $input_icon; ?>"></em>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <!-- bcp-col-lg-4 End -->
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                    <!-- filter-modal-footer Start -->
                    <div class="bcp-col-lg-4 text-center filter-modal-footer">
                        <div class="case-button-group bcp-filter">
                            <?php $where_cond_array_data =  ( !empty($where_cond_array) ? json_encode($where_cond_array) : "" ); ?>
                            <input type="hidden" id="filter_current_where" name="current_where" value='<?php echo $where_cond_array_data; ?>'>
                            <input type="hidden" id="filter_module_name" name="module_name" value="<?php echo $module_name; ?>">
                            <input type="hidden" id="filter_action" name="action" value="<?php echo 'bcp_list_portal'; ?>">
                            <input type="hidden" id="filter_limit" name="limit" value="<?php echo $limit; ?>">
                            <input type="hidden" id="filter_view_style" name="view_style" value="<?php echo $style; ?>">
                            <button type="submit" class="bcp-btn save-btn"><?php _e("Save", 'bcp_portal'); ?></button>
                            <button type="reset" id="bcp-filter-reset" class="bcp-btn bcp-gray filter-close"><?php _e("Reset", 'bcp_portal'); ?></button>
                        </div>
                    </div>
                    <!-- filter-modal-footer End -->
                </div>
                <!-- bcp-row End -->
            </form>
            <!-- filter-list-form End -->
            <?php } ?>


        <?php if(isset($showtabs) && $showtabs == true){ ?>          
        <div class="tab">
            <?php
                $class = "";
                $myrecordactive = "";
                $allrecordactive = "";
            
                if($selectRecordType == 'allrecord'){
                    $allrecordactive = "active";
                }else if($selectRecordType == 'myrecord'){
                    $myrecordactive = "active";
                }else{
                    $myrecordactive = "active";
                }
            ?>
            <button class="tablinks myrecordlink <?php echo $myrecordactive;?>" data-record="myrecord" module-name="<?php echo $module_name; ?>" limit="<?php echo $limit; ?>" view_style="<?php echo $style;?>" onclick="openTab(event, 'myrecord')"><?php echo __('My '.$heading_modulename, 'bcp_portal'); ?></button>
            <button class="tablinks allrecordlink <?php echo $allrecordactive;?>" data-record="allrecord" module-name="<?php echo $module_name; ?>" limit="<?php echo $limit; ?>" view_style="<?php echo $style;?>" onclick="openTab(event, 'allrecord')"><?php echo __('All '.$heading_modulename, 'bcp_portal'); ?></button>

        </div>
        <?php } ?>

        <!-- listing-data-table Start -->
        <div class="listing-data-table">
        <?php if (isset($_SESSION['record_msg']) && $_SESSION['record_msg'] != "") { ?>
                <span class="success"><?php echo __($_SESSION['record_msg'], 'bcp_portal'); ?></span>
                <?php
                unset($_SESSION['record_msg']);
            }
            ?>
            <?php if ($list_records != '') { ?>

                <?php 
                foreach ($list_records['records'] as $k => $records) {
                    $contact_id = $records['ContactId'];
                }

                ?>
                <!-- bcp-table Start -->
                <table class="bcp-table" id="bcp_module_table">
                    <thead>
                        <tr>
                        <?php
                        $fields_meta = $list_records['field_meta'];
                        foreach ($list_records['layout'] as $key => $value) {
                            $label_value = ( isset($fields_meta[$key]['portal_lable']) && !empty($fields_meta[$key]['portal_lable']) ) ? $fields_meta[$key]['portal_lable'] : $value;
                            if ($orderby && $orderby == $key) {
                                $orders = 'ASC';
                                $icon = '<span class="lnr lnr-chevron-down"></span>';
                                if ($order == 'ASC') {
                                    $orders = 'DESC';
                                    $icon = '<span class="lnr lnr-chevron-up"></span>';
                                }
                            } else {
                                $orders = 'ASC';
                                $icon = '<span class="lnr lnr-chevron-down"></span>';
                            }
                            if ($label_value == 'Closed' || $label_value == 'FileExtension') {
                                continue;
                            }
                            ?>
                                <th>
                                    <a href="javascript:void(0);" class="bcp-action " module-name="<?php echo $module_name; ?>" module-action="list" order-by="<?php echo $key . ' ' . $orders; ?>">
        <?php echo __($label_value, 'bcp_portal'); ?><?php echo $icon; ?>
                                    </a>
                                </th>

                            <?php } ?>
                            <?php if (!empty($list_records['Action'])) { ?>
                                <th class="text-center">
                                    <?php _e("Actions", 'bcp_portal'); ?>
                                </th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <!-- tbody Start -->
                    <tbody>
                        <?php
                        foreach ($list_records['records'] as $k => $records) {
                            $record_id = $records['record_id'];
                            $RecordCreatedByContact = $records['RecordCreatedByContact'];
                            $detail_page_url = home_url('/customer-portal/' . $module_name . '/detail/' . $record_id . '/');
                            $solution_url = home_url('/customer-portal/' . $module_name . '/detail/' . $record_id . '/solutions/');

                            if ($custom_detail_page && $custom_detail_page_url != '') {
                                $detail_page_url = $custom_detail_page_url . $record_id;
                                $solution_url = $custom_detail_page_url . $record_id . '/solutions/';
                            }

                            $edit_url = home_url('/customer-portal/' . $module_name . '/edit/' . $record_id);
                            if (!empty($relate_id) && !empty($relationshipname)) {
                                $url = 'customer-portal/' . $module_name . '/edit/' . $record_id . '/Relate/' . $relate_module . '/Relation/' . $relationshipname . '/' . $relate_id;
                                $edit_url = home_url($url);
                            }
                            if ($custom_edit_page && $custom_edit_page_url != '') {
                                $edit_url = $custom_edit_page_url . $record_id;
                                if (!empty($relate_id) && !empty($relationshipname)) {
                                    $edit_url = $custom_edit_page_url . $record_id . '/Relate/' . $relate_module . '/Relation/' . $relationshipname . '/' . $relate_id;
                                }
                            }

                            unset($records['record_id']);
                            unset($records['RecordCreatedByContact']);
                            $FileExtension = isset($records['FileExtension']) ? $records['FileExtension'] : "";
                            if ($FileExtension) {
                                $FileExtension = $records['FileExtension'];
                                unset($records['FileExtension']);
                            }
                            if (isset($records['IsClosed'])) {
                                $IsClosed = $records['IsClosed'];
                            }
                            ?>
                            <tr>
                            <?php
                            foreach ($records as $key => $record) {
                                if ($key == 'IsClosed') {
                                    continue;
                                }
                                ?>
                                    <td class="dcp_hover_td"><a href="<?php echo $detail_page_url; ?>"><?php echo wp_trim_words(htmlentities($record), 15); ?></a></td>
                                <?php } ?>
                                <?php if (!empty($list_records['Action'])) { ?>
                                    <td class="dcp_hover_td listing-action text-center">
                                    <?php if ($module_name == 'order' && (isset($records['Status']) && $records['Status'] == 'Activated')) { ?>
                                            <a class="bcp-action" href="javascript:void(0);">
                                                <span><?php _e('N/A', 'bcp_portal'); ?></span>
                                            </a>
                                            <?php
                                            continue;
                                        }
                                        $actionFlag = 0;
                                        foreach ($list_records['Action'] as $action) {
                                            if ($action == 'delete' && (($RecordCreatedByContact != "" && $RecordCreatedByContact == $_SESSION['bcp_contact_id'])|| $module_name == 'account')) {
                                                $actionFlag = 1;
                                                ?>
                                                <a class="delete-action bcp-action" href="javascript:void(0);" module-action="delete" module-name="<?php echo $module_name; ?>" record-id="<?php echo $record_id; ?>" title="<?php _e("Delete", 'bcp_portal'); ?>">
                                                    <span class="lnr lnr-trash"></span>
                                                </a>
                                                <?php } else if ($action == 'solution') {  $actionFlag = 1;?>
                                                    <a class="solutions-action bcp-action" href="<?php echo $solution_url; ?>" title="<?php _e("Solutions", 'bcp_portal'); ?>">
                                                        <span class="lnr lnr-eye"></span>
                                                    </a>
                                                <?php } else if (($action == 'edit' && $module_name != 'case' && (($RecordCreatedByContact != "" && $RecordCreatedByContact == $_SESSION['bcp_contact_id'])|| $module_name == 'account') ) || ($action == 'edit' && $IsClosed != 1 && $module_name == 'case'  && $RecordCreatedByContact != ""  && (($RecordCreatedByContact != "" && $RecordCreatedByContact == $_SESSION['bcp_contact_id'])|| $module_name == 'account')) ) { $actionFlag = 1; ?>
                                                <a class="edit-action bcp-action" href="<?php echo $edit_url; ?>" title="<?php _e("Edit", 'bcp_portal'); ?>"><span class="lnr lnr-pencil"></span></a>
                                                <?php
                                            }
                                            if ($action == 'download') {
                                                $actionFlag = 1;
                                                $url = admin_url('admin-ajax.php') . '?action=bcp_attachment_download&record_id=' . $record_id . '&title=' . $records['Title'] . '&type=' . $FileExtension;
                                                ?>
                                                <a href="<?php echo $url; ?>" class="dropdown-item" title="<?php _e("Download", 'bcp_portal'); ?>">
                                                    <span class="lnr lnr-download"></span>
                                                </a>
                                                <?php
                                            }
                                            if ($action == 'preview') {
                                                $actionFlag = 1;
                                                $url = admin_url('admin-ajax.php') . '?action=bcp_attachment_download&record_id=' . $record_id . '&title=' . $records['Title'] . '&type=' . $FileExtension . '&bcp_file_preview=1';
                                                ?>
                                                <a href="<?php echo $url; ?>" class="dropdown-item" target="_blank" title="<?php _e("Preview", 'bcp_portal'); ?>">
                                                    <span class="lnr lnr-eye"></span>
                                                </a>  
                                                <?php
                                            }
                                            if ($action == 'close' && $IsClosed != 1 && (($RecordCreatedByContact != "" && $RecordCreatedByContact == $_SESSION['bcp_contact_id'])|| $module_name == 'account')) {
                                                $actionFlag = 1;
                                                ?>
                                                <a class="case-close-action" href="javascript:void(0);" module-action="close" record-id="<?php echo $record_id; ?>" title="<?php _e("Close", 'bcp_portal'); ?>">
                                                    <span class="lnr lnr-exit"></span>
                                                </a>  
                                                <?php
                                            }
                                            if ($action == 're-open' && $IsClosed == 1 && (($RecordCreatedByContact != "" && $RecordCreatedByContact == $_SESSION['bcp_contact_id'])|| $module_name == 'account')) {
                                                $actionFlag = 1;
                                                ?>
                                                <a class="case-close-action" href="javascript:void(0);" module-action="re-open" record-id="<?php echo $record_id; ?>" title="<?php _e("Re-open", 'bcp_portal'); ?>">
                                                    <span class="lnr lnr-enter"></span>
                                                </a>  
                                            <?php
                                        }
                                    }
                                    if($actionFlag == 0){
                                        echo '-';
                                    }
                                    ?>
                                    </td>
                                    <?php } ?>
                            </tr>
                            <?php } ?>
                    </tbody>
                    <!-- tbody End -->
                </table>
                <!-- bcp-table End -->
            <?php } else { ?>
                <strong><?php _e('No Records Found.', 'bcp_portal'); ?></strong>
            <?php } ?>
        </div>
        <!-- listing-data-table End -->
    </div>
    <!-- listing-style-2 End -->
<?php
if ($list_records != null) {
    $action = 'list';
    if ($view) {
        $action = $view;
    }
    echo $action_class->bcp_pagination($total_records, $limit, $paged, $module_name, $action, $order_by, $search, $style = 'style_2');
}
?>
</div>
<!-- listing-2 style-2 End -->