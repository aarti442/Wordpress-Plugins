<h2><?php _e('General Settings', 'bcp_portal'); ?></h2>
<!-- form-table Start -->
<table class="form-table">
    <tbody>
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Name', 'bcp_portal'); ?></label></th>
            <td>
                <input type="text" name="bcp_name" value="<?php echo fetch_data_option('bcp_name'); ?>">
            </td>
        </tr>
        <!-- TR End -->
        <?php if (!bcp_check_wpml_compatibility()) { ?>
            <!-- TR Start -->
            <tr>
                <th scope="row"><label for="bcp_enable_multilang"><?php _e('Enable Multilanguage', 'bcp_portal'); ?></label></th>
                <td>
                    <?php $bcp_enable_multilang = fetch_data_option('bcp_enable_multilang'); ?>
                    <input type="hidden" name="bcp_enable_multilang" value="0">
                    <input id="bcp_enable_multilang" value="1" name="bcp_enable_multilang" type="checkbox" <?php echo checked($bcp_enable_multilang, "1") ?>><?php _e('Enable portal in multiple language.', 'bcp_portal'); ?>
                    <script type="text/javascript">
                        jQuery('#bcp_enable_multilang').click(function () {
                            if (jQuery(this).is(':checked')) {
                                jQuery('#bcp_portal_language_row').show();
                                jQuery('#bcp_default_language_row').show();
                            } else {
                                jQuery('#bcp_portal_language_row').hide();
                                jQuery('#bcp_default_language_row').hide();
                            }
                        });
                    </script>

                </td>
            </tr>
            <!-- TR End -->
            <!-- TR Start -->
            <tr id="bcp_portal_language_row" <?php
            if (!$bcp_enable_multilang) {
                echo 'style="display:none;"';
            }
            ?>>
                <th scope="row"><label><?php _e('Portal Language', 'bcp_portal'); ?></label></th>
                <td>
                    <?php
                    $lang_output = '';
                    $all_installed_languages = $bcp_helper->get_all_installed_languages();
                    $enabled_languages = fetch_data_option('bcp_portal_language');
                    if (isset($all_installed_languages) && is_array($all_installed_languages) && count($all_installed_languages) > 0) {
                        $lang_output .= '<select multiple="multiple" name="bcp_portal_language[]" id="bcp_portal_language" onchange="bcp_set_default_language_options(jQuery(this).val())">';
                        foreach ($all_installed_languages as $lanuage_info) {
                            $selected = '';
                            if (( isset($enabled_languages) && is_array($enabled_languages) && count($enabled_languages) > 0 ) && (in_array($lanuage_info['language'], $enabled_languages))) {
                                $selected = " selected='selected'";
                            }
                            $lang_output .= '<option ' . $selected . ' value="' . $lanuage_info['language'] . '" >' . $lanuage_info['native_name'] . '</option> ';
                        }
                        $lang_output .= '</select>';
                    }
                    echo $lang_output;
                    ?>
                </td>
            </tr>
            <!-- TR End -->
            <!-- TR Start -->
            <tr id="bcp_default_language_row" <?php
            if (!$bcp_enable_multilang) {
                echo 'style="display:none;"';
            }
            ?>>
                <th scope="row"><label><?php _e('Default Portal Language', 'bcp_portal'); ?></label></th>
                <td>
                    <?php
                    $default_lang_output = '';
                    $bcp_default_language = fetch_data_option('bcp_default_language');
                    $all_installed_languages = $bcp_helper->get_all_installed_languages();
                    $enabled_languages_data = array();
                    if (isset($enabled_languages) && is_array($enabled_languages) && count($enabled_languages) > 0) {
                        $enabled_languages_data = $bcp_helper->get_language_information($enabled_languages);
                    }
                    if (isset($all_installed_languages) && is_array($all_installed_languages) && count($all_installed_languages) > 0) {
                        $default_lang_output .= '<select name="bcp_default_language" id="bcp_default_language">';
                        $default_lang_output .= '<option value="en_US">Select Default Language</option>';
                        foreach ($all_installed_languages as $lanuage_info) {
                            $selected = '';
                            if ($lanuage_info['language'] == $bcp_default_language && array_key_exists($lanuage_info['language'], $enabled_languages_data)) {
                                $selected = ' selected="selected"';
                            }
                            if (!array_key_exists($lanuage_info['language'], $enabled_languages_data)) {
                                $selected .= ' disabled=""';
                            }
                            $default_lang_output .= '<option ' . $selected . ' value="' . $lanuage_info['language'] . '" >' . $lanuage_info['native_name'] . '</option> ';
                        }
                        $default_lang_output .= '</select>';
                    }
                    echo $default_lang_output;
                    ?>
                    <script type="text/javascript">
                        function bcp_set_default_language_options(portal_languages = '') {
                            jQuery('#bcp_default_language option').prop('disabled', true);
                            if (portal_languages != '') {

                                jQuery('#bcp_default_language option').each(function () {
                                    if (jQuery.inArray(jQuery(this).val(), portal_languages) != -1) {
                                        jQuery(this).prop('disabled', false);
                                    }
                                });
                        }
                        }
                    </script>
                </td>
            </tr>
            <!-- TR End -->
        <?php } ?>
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Logo', 'bcp_portal'); ?></label></th>
            <td>
                <p class="sfcp-logo-img">
                    <?php
                    $bcp_logo = fetch_data_option('bcp_logo');
                    if ($bcp_logo != null) {
                        $bcp_logo_url = wp_get_attachment_image_src($bcp_logo);
                        $bcp_logo_url = $bcp_logo_url[0];
                        if ($bcp_logo_url) {
                            ?>
                            <img src="<?php echo $bcp_logo_url; ?>" alt="<?php echo fetch_data_option('bcp_name'); ?>" />
                            <?php
                        }
                    }
                    ?>
                </p>
                <p class="sfcp-logo-section">
                    <button type="button" class="button remove-sfcp-logo"<?php echo (!$bcp_logo ? ' style="display: none;"' : '' ); ?>><?php _e('Remove Media', 'bcp_portal'); ?></button>
                    <button type="button" class="button sfcp-logo"<?php echo ( $bcp_logo ? ' style="display: none;"' : '' ); ?>><?php _e('Add Media', 'bcp_portal'); ?></button>
                    <input id="bcp_logo" type="hidden" name="bcp_logo" value="<?php echo $bcp_logo; ?>" />
                </p>
                <script type="text/javascript">
                    jQuery(function ($) {
                        var frame;

                        $('.sfcp-logo').on('click', function (event) {
                            event.preventDefault();
                            if (frame) {
                                frame.open();
                                return;
                            }

                            frame = wp.media({
                                library: {
                                    type: 'image'
                                }
                            });

                            frame.on('select', function () {
                                var attachment = frame.state().get('selection').first().toJSON();
                                $('#bcp_logo').val(attachment.id);
                                $('.sfcp-logo-img ').html('<img src="' + attachment.sizes.thumbnail.url + '" alt="<?php echo fetch_data_option('bcp_name'); ?>" />');
                                $('.remove-sfcp-logo').show();
                                $('.sfcp-logo').hide();
                            });

                            frame.open();
                        });

                        $('.remove-sfcp-logo').on('click', function () {
                            $('.sfcp-logo-img img').remove();
                            $('#bcp_logo').val('');
                            $('.remove-sfcp-logo').hide();
                            $('.sfcp-logo').show();
                        });
                    });
                </script>
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr class="hide_class">
            <th scope="row"><label for="bcp_customer_verify_enable"><?php _e('Enable Email Verification', 'bcp_portal'); ?></label></th>
            <td>
                <?php $customer_verify_enable = fetch_data_option('bcp_customer_verify_enable'); ?>
                <input value="0" name="bcp_customer_verify_enable" type="hidden">
                <input id="bcp_customer_verify_enable" value="1" name="bcp_customer_verify_enable" type="checkbox" <?php echo checked($customer_verify_enable, "1") ?>><?php _e('This feature will enable email verification for portal users after registration.', 'bcp_portal'); ?>
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr class="hide_class">
            <th scope="row"><label for="bcp_customer_approval_enable"><?php _e('Enable User Approval', 'bcp_portal'); ?></label></th>
            <td>
                <?php $customer_approval_enable = fetch_data_option('bcp_customer_approval_enable'); ?>
                <input value="0" name="bcp_customer_approval_enable" type="hidden">
                <input id="bcp_customer_approval_enable" value="1" name="bcp_customer_approval_enable" type="checkbox" <?php echo checked($customer_approval_enable, "1") ?>><?php _e('This feature will allow CRM admin to approve customer after registration.', 'bcp_portal'); ?>
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr class="hide_class">
            <th scope="row"><label for="sfcp_customer_otp_enable"><?php _e('Two - Step Authentication', 'bcp_portal'); ?></label></th>
            <td>
                <?php
                ( fetch_data_option('sfcp_customer_otp_enable') === FALSE ? update_option('sfcp_customer_otp_enable', '1') : '' );
                $customer_verify_enable = fetch_data_option('sfcp_customer_otp_enable');
                ?>
                <input value="0" name="sfcp_customer_otp_enable" type="hidden">
                <input id="sfcp_customer_otp_enable" value="1" name="sfcp_customer_otp_enable" type="checkbox" <?php echo checked($customer_verify_enable, "1") ?>><?php _e('This feature will enable Two - Step Authentication for signin from portal.', 'bcp_portal'); ?>
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Mobile Menu Title', 'bcp_portal'); ?></label></th>
            <td>
                <input type="text" name="bcp_mobile_menu_title" value="<?php echo fetch_data_option('bcp_mobile_menu_title'); ?>">
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Custom CSS', 'bcp_portal'); ?></label></th>
            <td>
                <textarea rows="8" name="bcp_custom_css"><?php echo fetch_data_option('bcp_custom_css'); ?></textarea>
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Standalone Footer Text', 'bcp_portal'); ?></label></th>
            <td>
                <textarea name="bcp_standalone_footer_content"><?php echo fetch_data_option('bcp_standalone_footer_content'); ?></textarea>
            </td>
        </tr>
        <!-- TR End -->
    </tbody>
</table>
<!-- form-table End -->