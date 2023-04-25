<?php
/** 
 * The file used to manage attachment style 1.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */
global $objCp, $case_record;

$parent_module = $module_name;
?>
<br/>
<!-- updates-details attachment Start-->
<div class="updates-details">

    <div class="sfcp-validation Attachment"></div>
    <!-- sfcp_attachment-list Start-->
    <div class="updates-details-main sfcp_attachment-list">
        <?php
        if (isset($attachments) && $attachments != null) {
            $ctn = 0;
            $bgcolor = '';
            foreach ($attachments as $attachment_detail) {
                $desc = nl2br(htmlentities(stripslashes_deep($attachment_detail->description)));
                if ($ctn == 0) {
                    $ctn = 1;
                    $bgcolor = '';
                } else {
                    $ctn = 0;
                    $bgcolor = 'user-bg-gray';
                }
                ?>
                <!-- user-details-1 Start-->
                <div class="user-details-1 <?php echo $bgcolor; ?>">
                    <div class="bcp-flex bcp-justify-content-between user-details-information">
                        <div class="user d-flex align-items-center">
                            <div class="attachment-name"><?php echo $attachment_detail->title ?></div>
                        </div>
                        <div class="post-date">
                            <a title="<?php _e('Preview', 'bcp_portal'); ?>" href="<?php echo admin_url('admin-ajax.php'); ?>?action=bcp_attachment_download&record_id=<?php echo $attachment_detail->Id; ?>&title=<?php echo $attachment_detail->title; ?>&type=<?php echo $attachment_detail->fileextension; ?>&bcp_file_preview=1" class="scp-caseattachment-btn" target="_blank">
                                <em class="fa fa-eye preview-cstm" aria-hidden="true"></em>
                            </a>
                            &nbsp;&nbsp;
                            <a title="<?php _e('Download', 'bcp_portal'); ?>" href="<?php echo admin_url('admin-ajax.php'); ?>?action=bcp_attachment_download&record_id=<?php echo $attachment_detail->Id; ?>&title=<?php echo $attachment_detail->title; ?>&type=<?php echo $attachment_detail->fileextension; ?>" class="attachment-download general-link-btn scp-download-btn scp-caseattachment-btn">
                                <em class="fa fa-download" aria-hidden="true"></em>
                            </a>
                        </div>
                    </div>
                    <?php
                    if (strlen($desc) > 100) {

                        $first_half = substr($desc, 0, 100);
                        $remain_half = substr($desc, 101, strlen($desc));
                        ?>
                        <span class="quote ellipses-quote"><?php echo $first_half; ?>
                            <span class="moreellipses" style="display: inline;">...&nbsp;</span>
                            <span class="morecontent">
                                <span style="display: none;"><?php echo $remain_half; ?></span>&nbsp;&nbsp;
                                <a title="<?php _e('Read more', 'bcp_portal'); ?>" href="javascript:void(0);" onclick="read_more_less(this)" class="scp-caseattachment-font readmorelink"><?php _e('Read more', 'bcp_portal'); ?></a>
                            </span>
                        </span>
                        <input type="hidden" value="<?php _e('Read More', 'bcp_portal'); ?>" class="bcp_crm_read_more_text" />
                        <input type="hidden" value="<?php _e('Read Less', 'bcp_portal'); ?>" class="bcp_crm_read_less_text" />
                        <br />
                        <?php } else { ?>
                        <span class="description"><?php echo html_entity_decode($desc); ?></span>
                        <?php } ?>
                </div>
                <!-- user-details-1 End -->
                <?php
            }
        }else{ 
          
            if($IsClosed == 1 && isset($IsClosed)){ ?>
            <div class="no_found"><strong><?php _e('No Records Found.', 'bcp_portal'); ?></strong></div>
       <?php } 
          }
        ?>
    </div>
    <!-- sfcp_attachment-list End -->
    <?php if($IsClosed != 1) { ?>
    <!-- details-desription-block Start -->
    <div class="details-desription-block">
        <!-- module-form Start -->
        <form class="module-form scp-form" id="sfcp-attachment-form">
            <div class="form-group">
                <div class="file-field bcp-flex bcp-align-center">
                    <div class="fileUpload">
                        <input required="" id="Attachment" name="Attachment" class="upload_btn scp-form-control bcp-required" type="file">
                        <div class="overlay-layer"><span class="lnr lnr-paperclip"></span></div>
                    </div>
                    <label for="Attachment" id="uploadFile"><?php _e('No file Chosen', 'bcp_portal'); ?></label>
                </div>
                <span id="error-msg-after-flex-div"></span>
                <p class="upload-file"><?php _e('Maximum upload file size: 2MB.', 'bcp_portal'); ?></p>
            </div>
            <div class="form-group">
                <div class="comment-block">
                    <textarea id="Description" required="" class="form-control" name="Description" maxlength="500" placeholder="<?php _e('Description', 'bcp_portal'); ?>"></textarea>
                </div>
            </div>
            <div class="form-actions">
                <?php wp_nonce_field('bcp_portal_nonce', 'bcp_portal_nonce_field'); ?>
                <input type="hidden" name="action" value="bcp_form_submit" />
                <input type="hidden" name="ParentId" value="<?php echo $record_id; ?>" />
                <input type="hidden" name="module_name" value="contentdocument" />
                <input type="hidden" name="ParentModule" value="<?php echo $parent_module; ?>" />
                <div class="case-button-group">
                    <button type="submit" class="bcp-btn"><?php _e('Upload', 'bcp_portal'); ?></button>
                </div>
            </div>
        </form>
        <!-- module-form End -->
    </div>
    <!-- details-desription-block End -->
    <?php } ?>
    
</div>
<!-- updates-details attachment End -->
