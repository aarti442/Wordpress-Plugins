<?php 
/** 
 * The file used to manage login page style 1.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */
?>
<!-- bcp-part-login Start -->
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
    <?php if (isset($_SESSION['bcp_error_message']) && $_SESSION['bcp_error_message']) { ?>
        <div class="login-error">
            <?php _e($_SESSION['bcp_error_message'], 'bcp_portal'); ?> <span class="lnr lnr-cross close-msg"></span>
        </div>
        <?php
        unset($_SESSION['bcp_error_message']);
    }

    if (!empty($disabled)) {
        ?>
        <div class="">
            <p>
                <?php _e("Too many login attempts. Please try again after: ", 'bcp_portal'); ?>
                <strong id="sfcp-login-attempt-min"><?php echo $min_diff; ?></strong>:
                <strong id="sfcp-login-attempt-second"><?php echo $sec_diff; ?></strong>
            </p>
        </div>
        <?php
    }

    if (isset($_REQUEST['success']) || isset($_REQUEST['otp'])) {
        if (isset($_SESSION['bcp_success_message']) && $_SESSION['bcp_success_message']) {
            ?>
            <div class='login-sucess'>
                <p>
                    <?php echo $_SESSION['bcp_success_message']; ?>
                </p>
            </div>
            <?php
            unset($_SESSION['bcp_success_message']);
        }
    }

    $class = 'scp-login-form';
    if (isset($_SESSION['bcp_otp_contact_id']) && $_SESSION['bcp_otp_contact_id'] != "") {
        $class = 'scp-verify-otp';
    }
    ?>
    <!-- scp-login-form1 Start -->
    <form name="scp-login-form1" id="<?php echo $class; ?>" method="post">
        <?php
        $action = "bcp_login";
        if (isset($_SESSION['bcp_otp_contact_id']) && $_SESSION['bcp_otp_contact_id'] != "") {
            $action = "bcp_verify_otp";
            ?>
            <div class="bcp-form-group">
                <label for="sfcp_otp"><?php _e("Enter OTP", 'bcp_portal'); ?>:</label>
                <input type="text" name="sfcp_otp" id="sfcp_otp" class="bcp-required form-control" />
                <a href="javascript:void(0);" class="forgot-psw resend-otp float-right" title="<?php _e("Resend OTP", 'bcp_portal'); ?>"><?php _e("Resend OTP", 'bcp_portal'); ?></a>
            </div>
            <?php
        } else {
            echo $bcp_helper->get_language_dropdown();
            ?>
            <!-- bcp-form-group Start -->
            <div class="bcp-form-group">
                <label for="bcp_username"><?php _e($username_text, 'bcp_portal'); ?>:</label>
                <input type="text" name="bcp_username" id="bcp_username" class="bcp-required form-control" tabindex="2" autofocus="" required="" <?php echo $disabled; ?> />
            </div>
            <!-- bcp-form-group End -->
            <!-- bcp-form-group Start -->
            <div class="bcp-form-group">
                <div class="bcp-flex bcp-justify-content-between bcp-align-items-center">
                    <label for="bcp_password"><?php _e($password_text, 'bcp_portal'); ?>:</label>
                    <?php if ($enable_forgot_pwd_link) { ?>
                        <a href="<?php echo get_permalink($bcp_forgot_password_page); ?>" class="forgot-psw float-right" tabindex="5" title="<?php _e("Forgot password", 'bcp_portal'); ?>"><?php _e('Forgot password?', 'bcp_portal'); ?></a>
                    <?php } ?>
                </div>
                <input type="password" name="bcp_password" id="bcp_password" class="bcp-required form-control" tabindex="2" required="" <?php echo $disabled; ?> />
            </div>
            <!-- bcp-form-group End -->
            <?php if ($enable_reCaptcha) { ?>
                <!-- bcp-form-group Start -->
                <div class="bcp-form-group">
                    <div id="scp-recaptcha"></div>
                    <label id="sfcp-recaptcha-error-msg" style="display:none;">
                        <?php _e("Please select reCaptcha.", 'bcp_portal'); ?>
                    </label>
                </div>
                <!-- bcp-form-group End -->
                <?php
            }
        }
        ?>
        <!-- login-btn-group Start -->
        <div class="login-btn-group bcp-flex bcp-justify-content-between bcp-align-items-center">
            <?php wp_nonce_field('bcp_portal_nonce', 'bcp_portal_nonce_field'); ?>
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <input type="hidden" class="browser_timezone" name="browser_timezone" value="" />
            <?php if (isset($_SESSION['bcp_otp_contact_id']) && $_SESSION['bcp_otp_contact_id'] != "") { ?>
                <a href="?bcp-login=1" class="signup-now bcp-btn" title="<?php _e("Back To Login", 'bcp_portal'); ?>"><?php _e("Back To Login", 'bcp_portal'); ?></a>
                <?php
            } else {
                if ($enable_signup_link) {
                    ?>
                    <a href="<?php echo get_permalink($bcp_registration_page); ?>" class="signup-now bcp-btn" tabindex="4" title="<?php _e("Sign Up Now!", 'bcp_portal'); ?>"><?php _e("Sign Up Now!", 'bcp_portal'); ?></a>
                    <?php
                }
            }
            ?>
            <button <?php echo $disabled; ?> type="submit" id="scp-login-form-submit" class="bcp-btn" tabindex="3"><?php _e($login_button_text, 'bcp_portal'); ?></button>
        </div>
        <!-- login-btn-group End -->
    </form>
    <!-- scp-login-form1 End -->
</div>
<!-- bcp-part-login End -->