<?php
/** 
 * The file used to manage case comments style 2.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */
global $objCp;
$IsClosed = isset($IsClosed) ? $IsClosed : "";
?>
<div class="sfcp-validation casecomment-msg"></div>
<div class="casecomment_content">
<!-- updates-details case comments Start -->
<div class="updates-details">
    
    <?php if (isset($comments) && $comments != null) { ?>
        <!-- updates-details-main Start -->
        <div class="updates-details-main">
            <?php
            $ctn = 0;
            $bgcolor = '';

            foreach ($comments as $record) {
                if (!$record->IsPublished) {
                    continue;
                }

                $createby = '';
                $comment_body = htmlentities($record->CommentBody);

                if (strpos($comment_body, '###') !== false) {

                    $comment_body = htmlentities($record->CommentBody);
                    $comment_data = explode('###', $comment_body);
                    $createby = end($comment_data);
                    $createby = str_replace("By ", "", $createby);
                    array_pop($comment_data);
                    $comment_body = implode(' ', $comment_data);
                    $class = "even";
                } else {
                    $createby = ( isset($record->CreatedBy->Name) ? $record->CreatedBy->Name : $record->CreatedBy );
                    $class = "odd";
                }
                $comment_body = trim($comment_body);
                $comment_body = stripslashes($comment_body);
                $comment_body = htmlentities($comment_body);
                $comment_body = nl2br($comment_body);
                $auth_names = explode(" ", $createby);
                $Auth_letter = strtoupper($auth_names[0])[0];
                $Auth_letter .= (isset($auth_names[1])) ? strtoupper($auth_names[1])[0] : "";
                if ($ctn == 0) {
                    $ctn = 1;
                    $bgcolor = '';
                } else {
                    $ctn = 0;
                    $bgcolor = 'user-bg-gray';
                }
                ?>
                <div class="update-listing <?php echo $class; ?>">
                    <div class="user-details-1 <?php echo $bgcolor; ?>">
                        <div class="bcp-flex bcp-justify-content-between user-details-information">
                            <div class="user bcp-flex bcp-align-center">
                                <div class="profile-pic"><?php echo $Auth_letter; ?></div>
                            </div>
                        </div>
                        <div class="user-description">
                            <p><?php echo html_entity_decode($comment_body); ?></p>
                        </div>
                    </div>
                    <div class="post-date">
                        <p><?php echo $action_class->bcp_show_date($record->CreatedDate); ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
        <!-- updates-details-main End -->
        <?php
    }else{ 
          
        if($IsClosed == 1 && isset($IsClosed)){ ?>
        <div class="no_found"><strong><?php _e('No Records Found.', 'bcp_portal'); ?></strong></div>
    <?php } 
      } 
    ?>
    <?php if ($IsClosed != 1) { ?>
        <!-- details-description-block Start -->
        <div class="details-desription-block">
            <!-- module-form Start -->
            <form class="form module-form" id="sfcp-case-comment-form">
                <div class="comment-block">
                    <textarea required="" id="CommentBody" class="form-control" rows="10" cols="30" name="CommentBody" placeholder="<?php _e('Comment', 'bcp_portal'); ?>"></textarea>
                </div>
                <div class="form-group">
                    <?php
                    $module_fields = array(
                        'CommentBody' => 'textarea',
                    );
                    ?>
                    <?php wp_nonce_field('bcp_portal_nonce', 'bcp_portal_nonce_field'); ?>
                    <input type="hidden" name="module_fields" value="<?php echo urlencode(serialize($module_fields)); ?>" />
                    <input type="hidden" name="action" value="bcp_form_submit" />
                    <input type="hidden" name="ParentId" value="<?php echo $record_id; ?>" />
                    <input type="hidden" name="module_name" value="CaseComment" />
                    <div class="case-button-group">
                        <button type="submit" class="bcp-btn"><?php _e('Save', 'bcp_portal'); ?></button>
                    </div>
                </div>
            </form>
            <!-- module-form End -->
        </div>
        <!-- details-description-block End -->
    <?php } ?>
</div>
<!-- updates-details case comments End -->
</div>