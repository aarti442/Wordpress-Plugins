<?php
/** 
 * The file used to manage detail page style 2.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */


$show_edit_button = true;
if ($bcp_portal_template && ($product_show || $solutions_show || $attachments_show || $casecomments_show || $notes_show  || ($subpanel_option && !empty($subpanels)) )) {
    $class_row = 'bcp-row';
} else {
    $class_row = '';
}
?>
<!--Detail Block Start-->
<div class="bcp-container-fluid description-details-2">
    <?php if (isset($_SESSION['record_msg']) && $_SESSION['record_msg'] != "") { ?>
        <span class="success"><?php echo __($_SESSION['record_msg'], 'bcp_portal'); ?><span class="lnr lnr-cross close-msg"></span></span>
        <?php
        unset($_SESSION['record_msg']);
        setcookie("record_msg", "", time() - 3600, "/", "", '', true);
    }
    ?>
    <!--bcp-col-lg-12 Start-->
    <div class="bcp-col-lg-12">
        <!--case-details-view Start-->
        <div class="case-details-view bg-white ">
            <!-- Title Start-->
            <div class="bcp-flex bcp-justify-content-between bcp-align-center">
                <h2 class="d-none d-md-block">
                    <?php if (isset($module_icon) && !empty($module_icon)) { 
                        echo '<span class="' . $module_icon . '"></span>'; 
                        }
                        _e($heading_modulename, 'bcp_portal'); 
                    ?>
                </h2>
                <?php
                $field_values = array_column($fields, 'value', 'lable');
                if ($module_name == 'order' && array_key_exists('Status', $field_values)) {
                    if ($field_values['Status'] == 'Activated') {
                        $show_edit_button = false;
                    }
                }
                ?>
                <!-- bcp-button-group Start -->
                <div class="bcp-button-group bcp-flex">
                    <?php if ($show_edit_button && $edit_option && $edit_module_url && (($RecordCreatedByContact != "" && $RecordCreatedByContact == $_SESSION['bcp_contact_id'])|| $module_name == 'account')) {
                        $url = 'customer-portal/' . $module_name . '/edit/' . $record_id; ?>
                        <a href="<?php echo home_url($url); ?>" title="<?php _e('Edit', 'bcp_portal'); ?>">
                            <button class="action-btn edit">
                                <span class="lnr lnr-pencil"></span>
                            </button>
                        </a>
                    <?php } ?>
                    <?php if ($show_edit_button && $delete_option && (($RecordCreatedByContact != "" && $RecordCreatedByContact == $_SESSION['bcp_contact_id'])|| $module_name == 'account')) { ?>
                        <a href="javascript:void(0);" class="bcp-action" module-name="<?php echo $module_name; ?>" module-action="delete" record-id="<?php echo $record_id; ?>"  title="<?php _e('Delete', 'bcp_portal'); ?>">
                            <button class="action-btn delete">
                                <span class="lnr lnr-trash"></span>
                            </button>
                        </a>
                    <?php } ?>
                </div>
                <!-- bcp-button-group End -->
            </div>
            <!-- Title End-->
            <!-- Edit /Delete Start-->
            <div class="bcp-flex bcp-justify-content-between title-case-block">
                <?php if ($module_sub_title) { ?>
                    <h3><?php _e($module_sub_title, 'bcp_portal'); ?></h3>
                    <?php
                }
                ?>
            </div>
            <!-- Edit /Delete End -->
        </div>
        <!--case-details-view End -->
    </div>
    <!--bcp-col-lg-12 End -->
    <!--Row Start -->
    <div class="<?php echo $class_row; ?>">
        <!--Class div Start -->
        <div class="bcp-col-lg-12">
            <!-- bg-white div Start -->
            <div class="bg-white">
                <div class="case-details-description">
                    <!-- product-case-details div Start -->
                    <div class="product-case-details">
                        <?php foreach ($fields as $k => $item) { ?>
                            <div class="<?php echo $item['section_name']; ?> section-div">
                                <div class="section-title">
                                    <h3 class="section_name">
                                        <?php _e($item['section_name'], 'bcp_portal'); ?>
                                    </h3>
                                </div>
                                <?php foreach ($item['rows'] as $Field) { ?>
                                <div class="bcp-row">
                                <?php foreach ($Field as $key => $field) { ?>
                                <div class="bcp-flex">
                                    <label><?php _e($field['lable'], 'bcp_portal') ?>:</label>
                                    <p><?php echo $field['value'] ?></p>
                                </div>
                                <?php } ?>
                                </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <!-- product-case-details div End -->
                </div>
            </div>
            <!-- bg-white div End -->
        </div>
        <!--Class div End -->
<?php if ($product_show || $solutions_show || $attachments_show || $casecomments_show || $notes_show || ($subpanel_option && !empty($subpanels)) ) { ?>
            <!-- Tab Start -->
            <div class="bcp-col-lg-12 tab-details-1">
                <div class="bg-white">
                    <!-- Case Details Description Start -->
                    <div class="case-details-description">
                        <!-- Tabs Start -->
                        <ul class="tabs">
                            <?php if ($product_show) { ?>
                                <li class="tab-link <?php if ($subtab == '') { echo 'current'; } ?>" data-tab="product"><?php _e("Products", 'bcp_portal'); ?></li>
                            <?php } ?>
                            <?php if ($casecomments_show) { ?>
                                <li class="tab-link <?php if ($subtab == '') { echo 'current'; } ?>" data-tab="casecomments"><?php _e("Case Comments", 'bcp_portal'); ?></li>
                            <?php } ?>
                            <?php if ($attachments_show) { ?>
                                <li class="tab-link" data-tab="contentdocument"><?php _e("File", 'bcp_portal'); ?></li>
                            <?php } ?>
                            <?php if ($solutions_show) { ?>
                                <li class="tab-link <?php if ($subtab == '') { echo 'current'; } ?>" data-tab="solution"><?php _e("Solutions", 'bcp_portal'); ?></li>
                            <?php } ?>
                            <?php if ($notes_show) { ?>
                                <li class="tab-link" data-tab="notes"><?php _e("Case Notes", 'bcp_portal'); ?></li>
                            <?php } ?>
                            <?php 
                                $count = 0;
                                foreach($subpanels as $subpanel) {
                                    if($module_name == strtolower($subpanel['moduleName'])) {
                                        continue;
                                    }
                                    $count++;
                                }
                                
                                $i = 0;
                                if($count_show_data){
                                    $i = $count_show_data;
                                    $count = $count_show_data + $count;
                                }
                                foreach($subpanels as $k => $subpanel) {
                                    if($module_name == strtolower($subpanel['moduleName'])) {
                                        continue;
                                    }
                                    $current = '';
                                    if( $current == "" && $subtab == ucfirst($subpanel['moduleName']).'-'.$subpanel['relationshipId'] ) {
                                        $current = 'current';
                                    }
                                    if( $current == "" && $i == 0 && $subtab == "" ) {
                                        $current = 'current';
                                    }
                                    if ($i > 4 || ($count > 4 && $i >= 3)) {
                                        break;
                                    }
                                ?>
                                <li class="tab-link subpaneltab <?php echo $current; ?>" data-tab="<?php echo $subpanel['moduleName'].'-'.$subpanel['relationshipId']; ?>" data-module="<?php echo strtolower($subpanel['moduleName']); ?>"  data-relationship="<?php echo $subpanel['relationshipId']; ?>" record-id="<?php echo $record_id; ?>" data-parent-module="<?php echo $module_name; ?>" data-style="<?php echo $view_style; ?>" data-response="responsedataSubpanel">
                                    <?php _e($subpanel['childRelationshipName'], 'bcp_portal'); ?>
                                   
                                </li>
                            <?php 
                                $i++;
                                }
                            if ($count > 4) {
                                    $l = $count_show_data;
                                ?>
                                <li class='dropdown sub-menu-div'>
                                    <a href='javascript:void(0);' class='dropdown-toggle' data-toggle='dropdown'>View more</a>
                                    <ul class='dropdown'>
                                        <?php
                                            foreach($subpanels as $subpanel) {
                                            $current = '';
                                            if( $current == "" && $subtab == ucfirst($subpanel['moduleName']).'-'.$subpanel['relationshipId'] ) {
                                                $current = 'current';
                                            }
                                                if($module_name == strtolower($subpanel['moduleName'])) {
                                                    continue;
                                                }
                                                $l++;
                                            if ($l <= 3) {
                                                    continue;
                                                }
                                            ?>
                                        <li data-toggle='tab' class="tab-link subpaneltab <?php echo $current; ?>" data-tab="<?php echo $subpanel['moduleName'].'-'.$subpanel['relationshipId']; ?>" data-module="<?php echo strtolower($subpanel['moduleName']); ?>"  data-relationship="<?php echo $subpanel['relationshipId']; ?>" record-id="<?php echo $record_id; ?>" data-parent-module="<?php echo $module_name; ?>" data-style="<?php echo $view_style; ?>" data-response="responsedataSubpanel"> 
                                                <?php _e($subpanel['childRelationshipName'], 'bcp_portal'); ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                                <?php } ?>
                        </ul>
                        <!-- Tabs End -->
                        <?php if ($product_show) { ?>
                            <div id="product" class="tab-content <?php if ($subtab == '') { echo 'current'; } ?>">
                                <div id="order-items" data-id="<?php echo $record_id; ?>">
                                    <?php require plugin_dir_path(__FILE__) . '/order_products_1.php'; ?>
                                </div>
                            </div>
                            <?php
                        }

                        if ($solutions_show) {
                            ?>
                            <div id="solution" class="tab-content <?php if ($subtab == '') { echo 'current'; } ?>">
                                <div id="solutionlist-items" class="sfcp-case-solution" data-id="<?php echo $record_id; ?>">
                                    <?php echo $action_class->bcp_solutions_list($record_id, 0, 0, ""); ?>
                                </div>
                            </div>
                            <?php
                        }
                        if ($casecomments_show) {
                            ?>
                            <div id="casecomments" class="tab-content <?php if ($subtab == '') { echo 'current'; } ?>">
                                <div id="case-comments" record-id="<?php echo $record_id; ?>" data-parent-module="<?php echo $module_name; ?>" data-style="<?php echo $view_style; ?>">
                                    <?php require plugin_dir_path(__FILE__) . '/casecomments_1.php'; ?>
                                </div>
                            </div>
                            <?php
                        }
                        if ($attachments_show) {
                            ?>
                            <div id="contentdocument" class="tab-content">
                                <div id="module-attachments" record-id="<?php echo $record_id; ?>" data-parent-module="<?php echo $module_name; ?>">
                                    <?php require plugin_dir_path(__FILE__) . '/attachment_1.php'; ?>
                                </div>
                            </div>
                            <?php
                        }
                        if ($notes_show) {
                            ?>
                            <div id="notes" class="tab-content">
                                <div id="case-notes" record-id="<?php echo $record_id; ?>" data-parent-module="<?php echo $module_name; ?>" data-style="<?php echo $view_style; ?>">
                                    <?php require plugin_dir_path(__FILE__) . '/notes_1.php'; ?>
                                </div>
                            </div>
                            <?php
                        }
                        if($subpanel_option && !empty($subpanels)) {
                            $current = '';
                            if(!$product_show || !$solutions_show || !$attachments_show || !$casecomments_show || !$notes_show){
                                $current = 'current';
                            }
                            if ( $current == "" && $subtab != "" ) {
                                $current = 'current';
                            }
                            ?>
                            <div class="tab-content subpanelpart <?php echo $current; ?>">
                                <div class="subpanel-div">
                                    <div id="responsedataSubpanel"></div>
                                </div>
                            </div>
                            <?php } ?>
                    </div>
                    <!-- Case Details Description End -->
                </div>
            </div>
            <!-- Tab End -->
        <?php } ?>
    </div>
    <!--Row End -->
</div>
<!--Detail Block End -->