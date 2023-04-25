<?php
global $objCp, $bcp_field_prefix, $wpdb;

$Module_Name__c = $bcp_field_prefix . 'Module_Name__c';
$View_Name__c = $bcp_field_prefix . 'View_Name__c';
$Role_Data__c = $bcp_field_prefix . 'Customer_Portal_Role__c';

$bcp_portal_module = fetch_data_option("bcp_portal_modules");
$bcp_role_objects = fetch_data_option("bcp_role_objects");

$layout_params = ['type' => 'layoutSyncStatus'];
$bcp_portal_layout = $objCp->getPortalLayoutsCall($layout_params);
$NotSyncLayoutData = array();
if ( isset($bcp_portal_layout->SyncData->Sync) && $bcp_portal_layout->SyncData->Sync != TRUE && isset($bcp_portal_layout->SyncData->NotSyncLayoutData) && !empty($bcp_portal_layout->SyncData->NotSyncLayoutData) ) {
    $NotSyncLayoutData = json_decode($bcp_portal_layout->SyncData->NotSyncLayoutData, 1);
}
$module_layouts = $wpdb->get_results("SELECT * FROM " . $wpdb->base_prefix . "bcp_layouts");
$first_time_sync = fetch_data_option('first_time_sync');
if (empty($module_layouts) || $first_time_sync == "0") {
    $first_time_sync = "0";
}
?>
<h2><?php _e('Synchronize CRM Layout', 'bcp_portal'); ?></h2>
<?php
$msg = __("Whenever there is a layout change from Salesforce; In order to get those changes in the portal, you have to sync it from here.", "bcp_portal");
$disabled = '';
/*$layout_array = array();
if (!empty($NotSyncLayoutData) || $first_time_sync == "0") { ?>
    <strong class="bcp-layout-sync-note"><?php _e("Note: Click on Sync button to synchronize below layouts."); ?></strong>
    <ul class="bcp-layout-sync-ul">
    <?php
    foreach ($NotSyncLayoutData as $key => $value) {
        $mod_name = $value[$Module_Name__c];
        $view = $value[$View_Name__c];
        $role = $value[$Role_Data__c];
        $role_name = isset($bcp_role_objects[$role]) ? $bcp_role_objects[$role] : $role;
        $singular_name = isset($bcp_portal_module[$mod_name]) ? $bcp_portal_module[$mod_name] : $mod_name;
        if (!isset($layout_array[$role][$mod_name])) {
            $layout_array[$role][$mod_name] = array($view);
        } else {
            array_push($layout_array[$role][$mod_name], $view);
        }
        ?>
        <li><?php echo __("Change in the layout for the Role: ") . "<b>" . $role_name . "</b>" . __(" and Object: ") . "<b>" . $singular_name . "</b>" . __(" and View: ") . "<b>" . $view ."</b>"; ?></li>
        <?php
    }
    ?>
    </ul>
    <?php
} else {
    $msg = __("There is no data to sync in the portal.", "bcp_portal");
    $disabled = "disabled='disabled'";
}
*/
?>
<?php 
$bcp_modules = $objCp->PortalModuleName(); 
$role_params = ['type' => 'role'];
$bcp_portal_role_result = $objCp->getPortalLayoutsCall($role_params);

$bcp_portal_role_result = $objCp->getPortalLayoutsCall($role_params);
if (isset($bcp_portal_role_result->portalRoleData) && !empty($bcp_portal_role_result->portalRoleData)) {
    $role_array = json_decode($bcp_portal_role_result->portalRoleData);
    $role_array_of_name = array();
    foreach ($role_array as $role_information) {
        $layout_role = $role_information->Id;
        array_push($role_array_of_name,$layout_role);
       
    }

}
$disabled = "disabled='disabled'";
?>
<ul id="module_selection">
<li><input type="checkbox" name="select_all" class="module_chk_selectAll" value="select_all"> <label><?php echo __("Select All", "bcp_portal"); ?></label></li>
<?php
foreach($bcp_modules as $key => $value){
     if($key == "status" || $key == "product2"){
        continue;
     }
     $value = json_decode($value); ?>
     <li><input type="checkbox" class="module_chk_select" name="<?php echo $key ?>" value="<?php echo $key ?>"> <label><?php echo $value->SingleName ?></label></li>
<?php }
?>
</ul>



<input type="hidden" id="first_time_sync" value="<?php echo $first_time_sync; ?>" />
<input type="hidden" id="bcp_layout_array" value='<?php echo json_encode($layout_array); ?>' />
<input type="hidden" id="bcp_roles" value='<?php echo json_encode($role_array_of_name); ?>' />
<!--<p class="bcp-layout-info-msg" data-norecords="<?php echo __("There is no data to sync in the portal.", "bcp_portal"); ?>"><?php echo $msg; ?></p>-->
<div>
    <button <?php echo $disabled; ?> type="button" aria-disabled="true" aria-expanded="false" class="sync-button button button-primary">
        <span class="dashicons dashicons-update-alt"></span> 
        <?php _e("Run Sync", "bcp_portal"); ?>
        <span class="spinner sync-loader" style="display: none;"></span>
    </button>
    <div class="bcp-sync-response"></div>
</div>