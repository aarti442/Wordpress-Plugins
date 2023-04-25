<?php
/** 
 * The file used to manage reset password page style 1.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */

?>
<!-- Reset password main Start -->
<div class="bcp-part-login">
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
    <?php if (isset($_REQUEST['error']) && (isset($_SESSION['bcp_error_message']) && $_SESSION['bcp_error_message']) ) { ?>
        <div class="login-error">
            <p><?php _e($_SESSION['bcp_error_message'], 'bcp_portal'); ?> <span class="lnr lnr-cross close-msg"></span></p>
        </div>
        <?php
        unset($_SESSION['bcp_error_message']);
    }
    ?>
    <!-- Reset password form Start -->
    <form method="post" id="scpResetPwdForm" class="reset-password-form">
        <div class="bcp-form-group">
            <label for="sfcp_new_password"><?php _e($new_password_text, 'bcp_portal'); ?>:</label>
            <input class="form-control" type="password" name="sfcp_new_password" id="sfcp_new_password" required >
        </div>
        <div class="bcp-form-group">
            <label for="sfcp_cfm_password"><?php _e($confirm_password_text, 'bcp_portal'); ?>:</label>
            <input class="form-control" type="password" name="sfcp_cfm_password" id="sfcp_cfm_password" required >
        </div>

        <div class="login-btn-group bcp-flex bcp-justify-content-between bcp-align-items-center">
            <?php wp_nonce_field('bcp_portal_nonce', 'bcp_portal_nonce_field'); ?>
            <input type="hidden" class="browser_timezone" name="browser_timezone" value="" />
            <input type="hidden" name="action" value="bcp_reset_password">
            <input type="hidden" name="bcp_contact_token" value="<?php echo $bcp_token ?>" >
            <button type="submit" class="bcp-btn"><?php _e($submit_button_text, 'bcp_portal'); ?></button>
        </div>
    </form>
    <!-- Reset password form End -->
</div>
<!-- Reset password main End -->