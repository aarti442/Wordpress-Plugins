<h2><?php _e('reCaptcha Settings', 'bcp_portal'); ?></h2>
<!-- form-table Start -->
<table class='form-table'>
    <!--Google Recptcha-->
    <?php
    $bcp_recaptcha_site_key = fetch_data_option("bcp_recaptcha_site_key");
    $bcp_recaptcha_secret_key = fetch_data_option("bcp_recaptcha_secret_key");
    $bcp_portal_visible_reCAPTCHA = (fetch_data_option("bcp_portal_visible_reCAPTCHA") != NULL) ? fetch_data_option("bcp_portal_visible_reCAPTCHA") : array();
    ?>
    <!-- TR Start -->
    <tr>
        <th scope='row'><label><?php _e('reCaptcha Site Key', 'bcp_portal'); ?></label></th>
        <td>
            <input type="text" class="regular-text" value="<?php echo $bcp_recaptcha_site_key; ?>" name="bcp_recaptcha_site_key" />
        </td>
    </tr>
    <!-- TR End -->
    <!-- TR Start -->
    <tr class="hide_class">
        <th scope='row'><label><?php _e('reCaptcha Secret Key', 'bcp_portal'); ?></label></th>
        <td>
            <input type="text" class="regular-text" value="<?php echo $bcp_recaptcha_secret_key; ?>" name="bcp_recaptcha_secret_key" />
        </td>
    </tr>
    <!-- TR End -->
</table>
<!-- form-table End -->