<?php
/** 
 * The file used to manage notes style 1.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */
global $objCp;

$parent_module = $module_name;
?>
<!-- updates-details notes Start -->
<div class="updates-details">
    <div class="sfcp-validation casenote-msg"></div>
    <!-- updates-details-main bcp-notes-list Start -->
    <div class="updates-details-main bcp-notes-list">
        <?php
        if (isset($notes) && $notes != null) {
            $ctn = 0;
            $bgcolor = '';
            foreach ($notes as $record) {
                $title = htmlentities($record->Title);
                $comment_body = nl2br(htmlentities(stripslashes_deep(base64_decode($record->Content))));
                $auth_names = explode(" ", $title);
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
                <div class="user-details-1 <?php echo $bgcolor; ?>">
                    <div class="bcp-flex bcp-justify-content-between user-details-information">
                        <div class="user bcp-flex bcp-align-center">
                            <div class="profile-pic"><?php echo $Auth_letter; ?></div>
                            <div class="user-name"><?php echo $title; ?></div>
                        </div>
                        <div class="post-date">
                            <p><?php echo $action_class->bcp_show_date($record->CreatedDate); ?></p>
                        </div>
                    </div>
                    <div class="user-description">
                        <p><?php echo html_entity_decode($comment_body); ?></p>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <!-- updates-details-main bcp-notes-list End -->
    <!-- details-description-block Start -->
    <div class="details-desription-block">
        <!-- module-form Start -->
        <form class="form module-form" id="sfcp-case-notes-form">
            <div class="comment-block">
                <input type="text"  class="form-control" placeholder="<?php _e("Title", 'bcp_portal'); ?>" required="" id="Title" name="Title" />
                <textarea required="" id="Content" class="form-control" rows="10" cols="30" name="Content" placeholder="<?php _e('Comment', 'bcp_portal'); ?>"></textarea>
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
                <input type="hidden" name="module_name" value="contentnote" />
                <input type="hidden" name="ParentModule" value="<?php echo $parent_module; ?>" />
                <div class="case-button-group">
                    <button type="submit" class="bcp-btn"><?php _e('Save', 'bcp_portal'); ?></button>
                </div>
            </div>
        </form>
        <!-- module-form End -->
    </div>
    <!-- details-description-block End -->
</div>
<!-- updates-details notes End -->