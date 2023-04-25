<?php
/** 
 * The file used to manage global search.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */
?>
<!-- global-search-section Start -->
<div class="global-search-section">
    <h2><?php _e('How can I Help you?', 'bcp_portal'); ?></h2>
    <!-- bcp-row Start -->
    <div class="bcp-row">
        <!-- global-input Start -->
        <div class="global-input">
            <!-- bcp-col-lg-3 Start -->
            <div class="bcp-col-lg-3">
                <div class="bcp-form-group">
                    <select class="sfcp-selected-module custom-select" id="bcp-gsearch-selected-module">
                        <option value="0" selected ><?php _e('All', 'bcp_portal'); ?></option>
                        <?php
                        $bcp_modules = isset($_SESSION['bcp_modules']) ? $_SESSION['bcp_modules'] : "";
                        foreach ($bcp_modules as $key => $value) {
                            $is_selected = '';
                            if( $_SESSION['bcp_relationship_type'] == "Account-Based"){
                                $relationship_field_key = $_SESSION['bcp_module_access_data']->$key->accountRelationship;                             
                                if($module_name == "account"){
                                    $relationship_field_key = $_SESSION['bcp_module_access_data']->$module_name->contactRelationship;
                                }
                            }else{
                                $relationship_field_key = $_SESSION['bcp_module_access_data']->$key->contactRelationship;
                            }
                            if ($key == 'account' && $relationship_field_key != "") {
                                continue;
                            }
                            if (isset($search_term) && $search_term != '' && isset($module_name) && $key == $module_name) {
                                $is_selected = 'selected';
                            }

                            echo '<option value="' . $key . '" ' . $is_selected . '>' . __($value['plural'], 'bcp_portal') . '</option>';
                        }
                        ?>
                    </select>
                    <select id="width_tmp_select">
                        <option id="width_tmp_option"></option>
                    </select>
                </div>
            </div>
            <!-- bcp-col-lg-3 End -->
            <!-- bcp-col-lg-7 Start -->
            <div class="bcp-col-lg-7">
                <div class="bcp-form-group">
                    <div class="bcp-form-group">
                        <?php
                        $class = '';
                        if (isset($search_term) && $search_term != '') {
                            $class = 'active';
                        }
                        $bcp_dashboard_page = fetch_data_option('bcp_dashboard_page');
                        $url = get_permalink($bcp_dashboard_page);
                        ?>
                        <input type="hidden" name="url" class="current-url" value="<?php echo $url; ?>" />
                        <input type="text" id="g_search_name" class="form-control <?php echo $class; ?>" placeholder="<?php _e('Search', 'bcp_portal'); ?>" value="<?php echo (isset($search_term) && $search_term != '') ? $search_term : ''; ?>"><a href="JavaScript:void(0);" id="btn_clear" module-name="0" title="<?php _e("Clear", 'bcp_portal'); ?>"><span class="lnr lnr-cross clear-btn"></span></a>
                        <button id="btn_global_search" class="module_search_btn bcp-btn"><span class="lnr lnr-magnifier"></span></button>
                    </div>
                </div>
            </div>
            <!-- bcp-col-lg-7 End -->
        </div>
        <!-- global-input End -->
    </div>
    <!-- bcp-row End -->
</div>
<!-- global-search-section End -->
<?php if (isset($show_result) && $show_result) { ?> 
    <!-- global-search Start -->
    <div class="global-search">
        <h2><?php echo __('Search result for') . ' "' . stripslashes($search_term) . '"'; ?></h2>
        <?php
        $flag = 0;
        if (isset($search_result) && !empty($search_result)) {

            foreach ($search_result as $key => $values) {
                $segregate_detail_page_exists = false;
                if ($key == "attachment") {
                    $key = "contentdocument";
                }
                if ($key == "kbarticle") {
                    $key = "KBDocuments";
                }

                $view_all_url = home_url('/customer-portal/' . $key . '/list/' . $search_term . '/');
                $segregate_list_page = $helper_class->get_segregate_url($key, 'bcp_list');
                if ($segregate_list_page['page_exists'] && $segregate_list_page['page_url']) {
                    $view_all_url = $segregate_list_page['page_url'] . $search_term;
                }

                $segregate_detail_page = $helper_class->get_segregate_url($key, 'bcp_detail');
                if ($segregate_detail_page['page_exists'] && $segregate_detail_page['page_url']) {
                    $segregate_detail_page_exists = true;
                }

                if (!empty($values->records)) {
                    $flag = 1;
                    ?>
                    <!-- global-result-block Start -->
                    <div class="global-result-block">
                        <h3><?php _e($_SESSION['bcp_modules'][$key]['plural'], 'bcp_portal'); ?></h3>
                        <div class="bcp-table-responsive">
                            <table>
                                <?php
                                foreach ($values->records as $value) {
                                    $detail_page_url = home_url('/customer-portal/' . $key . '/detail/' . $value->Id . '/');
                                    if ($segregate_detail_page_exists) {
                                        $detail_page_url = $segregate_detail_page['page_url'] . $value->Id . '/';
                                    }
                                    $label = "";
                                    if (isset($value->Subject) && $value->Subject != "") {
                                        $label = $value->Subject;
                                    } elseif (isset($value->CaseNumber) && $value->CaseNumber != "") {
                                        $label = $value->CaseNumber;
                                    } elseif (isset($value->ContractNumber) && $value->ContractNumber != "") {
                                        $label = $value->ContractNumber;
                                    } elseif (isset($value->OrderNumber) && $value->OrderNumber != "") {
                                        $label = $value->OrderNumber;
                                    } elseif (isset($value->Title) && $value->Title != "") {
                                        $label = $value->Title;
                                    } elseif (isset($value->Name) && $value->Name != "") {
                                        $label = $value->Name;
                                    } else {
                                        $label = $value->Id;
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <a class="comment-subject" href="<?php echo $detail_page_url; ?>">
                                                <?php echo htmlentities($label); ?>
                                            </a>
                                        </td>
                                        <td class="text-right"><p><?php echo $action_class->bcp_show_date($value->CreatedDate); ?></p></td>
                                    </tr>
                                    <?php
                                }

                                if (count($values->records) == 5) {
                                    ?>
                                    <tr>
                                        <td><a class="more" href="<?php echo home_url('/customer-portal/' . $key . '/list/' . $search_term . '/'); ?>" title="<?php _e('View More'); ?>"><?php _e('View More', 'bcp_portal'); ?></a></td>
                                        <td class="text-right"></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                    <!-- global-result-block End -->
                    <?php
                }
            }
        }
        if ($flag == 0 && (!isset($KBLists->records) || empty($KBLists->records))) {
            ?>
            <div class="global-result-block">
                <div class="scp-col-12 container">
                    <p><strong><?php _e('No records found.', 'bcp_portal'); ?></strong></p>
                </div>
            </div>
        <?php } ?>
    </div>
    <!-- global-search End -->
    <?php
    if (isset($KBLists->records) && !empty($KBLists->records)) {
        $flag = 1;
        $key = "KBDocuments";
        ?>
        <!-- global-search-kb-result Start -->
        <div class="search-block global-search-kb-result">
            <div class="not-found-block solution-found-block">
                <h3><?php _e($_SESSION['bcp_modules'][$key]['plural'], 'bcp_portal') ?></h3>
                <?php
                $cnt = 0;
                foreach ($KBLists->records as $KBValue) {

                    $kb_title = $KBValue->Title;
                    $kb_desc_full = isset($KBValue->Summary) ? nl2br($KBValue->Summary) : "";
                    $kb_date = $action_class->bcp_show_date($KBValue->LastPublishedDate);
                    ?>
                    <div class="acc__card">
                        <div class="acc__title"><?php echo $kb_title ?></div>
                        <div class="acc__panel">
                            <?php echo $kb_desc_full ?>
                        </div>
                    </div>
                    <?php
                    $cnt++;
                }
                if (count($KBLists->records) == 5) {
                    ?>
                    <div>
                        <div class="more-td">
                            <a class="more" href="#data/<?php echo $key; ?>/list/<?php echo $search_term; ?>/" title="<?php _e('View More'); ?>"><?php _e('View More', 'bcp_portal'); ?></a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <!-- global-search-kb-result End -->
        <?php
    }
}