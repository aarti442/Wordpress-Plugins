<?php
/** 
 * The file used to manage forgot password page style 1.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */
?>
<!-- forgot password main Start -->
<div class="bcp-part-login <?php echo ($enable_reCaptcha) ? 'sfcp-recaptcha-class' : ''; ?>">
    <?php if ($bcp_logo_url) { ?>
        <h2 class="bcp-login-logo">
            <img title="<?php _e($bcp_name, 'bcp_portal'); ?>" src="<?php echo $bcp_logo_url; ?>" alt="<?php _e($bcp_name, 'bcp_portal'); ?>">
        </h2>
        <?php
    }
    if ($bcp_name) {
        ?>
        <h3><?php _e($bcp_name, 'bcp_portal'); ?></h3>
    <?php } ?>
    <?php if (isset($_REQUEST['error']) && isset($_SESSION['bcp_error_message']) && $_SESSION['bcp_error_message']) { ?>
        <div class="login-error">
            <?php _e($_SESSION['bcp_error_message'], 'bcp_portal'); ?><span class="lnr lnr-cross close-msg"></span>              
        </div>
        <?php
        unset($_SESSION['bcp_error_message']);
    }
    ?>
    <!-- forgot-password-form Start -->
    <form action="" class="forgot-password-form" method="post" id="commentForm">
        <?php echo $bcp_helper->get_language_dropdown(); ?>
        <div class="bcp-form-group">
            <label for="forgot-password-email-address"><?php _e($email_text, 'bcp_portal'); ?>:</label>
            <input class="form-control" type="email" name="sfcp_email" id="forgot-password-email-address" required="" >
        </div>
        <?php if ($enable_reCaptcha) { ?>
            <div class="bcp-form-group">
                <div id="scp-recaptcha"></div>
                <label id="sfcp-recaptcha-error-msg" style="display:none;">
                    <?php _e("Please select reCaptcha.", 'bcp_portal'); ?>
                </label>
            </div>
        <?php } ?>
        <div class="login-btn-group bcp-flex bcp-justify-content-between bcp-align-items-center">
            <?php wp_nonce_field('bcp_portal_nonce', 'bcp_portal_nonce_field'); ?>
            <input type="hidden" name="action" value="bcp_forgot_password">
            <?php if ($bcp_login_page_link) { ?>
                <a href="<?php echo get_permalink($bcp_login_page_link); ?>" class="signup-now bcp-btn" title="<?php _e("Back to Login", 'bcp_portal'); ?>"><?php _e('Back to Login', 'bcp_portal'); ?></a>
            <?php } ?>
            <button type="submit" name="submit" class="bcp-btn" ><?php _e($submit_button_text, 'bcp_portal'); ?></button>
        </div>
    </form>
    <!-- forgot-password-form End -->
</div>
<!-- forgot password main End -->