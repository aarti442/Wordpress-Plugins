<?php
/** 
 * The file used to manage recent activity style 2.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */

?>
<!-- Recent activity style-2 Start -->
<div class="bcp-recent-activity-style-2 recent-block-style-2 bcp-block-div">
    <!-- dcp-content-table Start -->
    <div class="dcp-content-table">
        <div class="dcp-flex">
            <h2><?php echo $module_title; ?></h2> 
        </div>
        <?php if ($field_values) { ?>
            <!-- table-responsive Start -->
            <div class="table-responsive">
                <table class="dcp-table">
                    <thead class="dcp-head">
                        <tr>
                            <th><?php echo $field1_label; ?></th>
                            <th class="text-right"><?php echo preg_replace('/(?<!\ )[A-Z]/', ' $0', $field3_label); ?></th>
                        </tr>
                    </thead>
                    <!-- tbody Start -->
                    <tbody>
                        <?php
                        foreach ($field_values as $item) {
                            $item = (array) $item;
                            if (array_key_exists($field3, $item)) {
                                $dateDate = $action_class->bcp_show_date($item[$field3], "Date");
                                $dateTime = $action_class->bcp_show_date($item[$field3], "Time");
                            } else {
                                $dateDate = '';
                                $dateTime = '';
                            }
                            if (array_key_exists('Id', $item)) {
                                $rec_id = $item['Id'];
                            }
                            if (array_key_exists('Status', $item)) {
                                $status = $item['Status'];
                            } else {
                                $status = '';
                            }
                            $url = home_url('customer-portal/' . $module_name . '/detail/' . $rec_id);
                            if ($segregate_detail_page_exists && $segregate_detail_page_url != '') {
                                $url = $segregate_detail_page_url . $rec_id;
                            }
                            ?>
                            <tr>
                                <!-- td Start -->
                                <td class="text-title">
                                    <div class="dcp-user-details">
                                        <?php
                                        $auth_names = explode(" ", $item[$field1]);
                                        $Auth_letter = strtoupper($auth_names[0])[0];
                                        $Auth_letter .= (isset($auth_names[1])) ? strtoupper($auth_names[1])[0] : "";
                                        ?>
                                        <div class="dcp-profile-pic">
                                            <a href="<?php echo $url; ?>">
                                                <?php echo $Auth_letter; ?>
                                            </a>
                                        </div>
                                        <div class="dcp-account-description">
                                            <a href="<?php echo $url; ?>">
                                                <p><strong><?php echo $item[$field1]; ?></strong></p>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <!-- td End -->
                                <td class="text-right">
                                    <p class="light"><?php echo $dateDate; ?></p>
                                    <span><?php echo $dateTime; ?></span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <!-- tbody End -->
                </table>
            </div>
            <!-- table-responsive End -->
            <?php if (count($field_values) >= 5) { ?>
                <div class="view-all-records"><a class="lnr lnr-arrow-right view-all" href="<?php echo $view_all_url; ?>" title="<?php _e("View All", 'bcp_portal'); ?>"></a></div>
            <?php } ?>
        <?php } else { ?>
        <div class="scp-recent-data">
            <span class="no-records"><?php _e("No Records.", 'bcp_portal'); ?></span>
        </div>
        <?php } ?>
    </div>
    <!-- dcp-content-table End -->
</div>
<!-- Recent activity style-2 End -->