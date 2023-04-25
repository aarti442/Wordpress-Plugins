<?php
/** 
 * The file used to manage knowledgebase style 2.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */

defined('ABSPATH') or die('No script kiddies please!');

global $objCp;
$content = '';
$bcp_authentication = fetch_data_option("bcp_authentication");  
ob_start();
if (isset($bcp_authentication) && $bcp_authentication != null) {

    if (isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id'] != null) {
            ?>
        <!-- search-block Start -->
        <div class="search-block">
            <!-- solution-found-block Start -->
            <div class="not-found-block solution-found-block">
                <h3><?php _e($module_display_name, 'bcp_portal'); ?></h3>
                <?php
                $data_fields = array(
                    'Id',
                    'title',
                    'UrlName',
                    'ArticleNumber',
                    'KnowledgeArticleId',
                    'Summary',
                    'LastModifiedById'
                );
                $search_field = "title";
                $org_prefix = fetch_data_option('bcp_org_prefix');
                $arguments = array(
                    "org_prefix" => $org_prefix,
                    "cid" => $cid,
                    "module_name" => "kbarticle",
                    "fields" => $data_fields,
                    "orderby" => "",
                    "order" => "",
                    "limit" => $limit,
                    "offset" => $offset,
                    "search" => $search,
                    "search_field" => $search_field,
                );
                $KBListsTotal = $objCp->getKBListCall($arguments);
                // If get and kb list
                if (isset($KBListsTotal->records) && !empty($KBListsTotal->records)) {
                    ?>
                    <!-- accordian-wrapper Start -->
                    <div class="accordian-wrapper">
                        <?php
                        $total_records = isset($KBListsTotal->totalSize) ? $KBListsTotal->totalSize : 0;
                        $cnt = 0;
                        foreach ($KBListsTotal->records as $KBKey => $KBValue) {
                            $kb_title = $KBValue->Title;
                            $kb_desc_full = isset($KBValue->Summary) ? nl2br($KBValue->Summary) : "";
                            $kb_date = $action_class->bcp_show_date($KBValue->LastPublishedDate);
                            ?>
                            <!-- accordian Start -->
                            <div class="accordian">
                                <div class="acc__card">
                                    <div class="acc__title">
                                        <?php echo $kb_title ?>
                                        <div class="date-block">
                                            <?php echo $kb_date; ?>
                                        </div>
                                    </div>
                                    <div class="acc__panel">
                                        <?php echo $kb_desc_full ?>
                                    </div>
                                </div>
                            </div>
                            <!-- accordian End -->
                            <?php
                            $cnt++;
                        }
                        ?>
                    </div>
                    <!-- accordian-wrapper End -->
                    <?php
                    echo $action_class->bcp_pagination($total_records, $limit, $paged, 'KBDocuments', 'list', '', $search);
                } else {
                    ?>
                    <strong><?php _e('No Records Found.', 'bcp_portal'); ?></strong>
                <?php } ?>
            </div>
        </div>
    <?php } else { ?>
        <span class='error error-line error-msg settings-error'><?php _e('Please login to portal and access this page.', 'bcp_portal'); ?></span>
        <?php
    }
} else {
    ?>
    <span class='error error-line error-msg settings-error'><?php _e('Portal is disabled. Please contact your administrator.', 'bcp_portal'); ?></span>
    <?php
}
$content .= ob_get_clean();

echo $content;
