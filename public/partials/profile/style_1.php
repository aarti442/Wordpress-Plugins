<?php
/** 
 * The file used to manage profile page style 1.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */


global $bcp_field_prefix;

if ($bcp_portal_template) {
    $first_class = 'bcp-col-lg-8';
    $secound_class = 'bcp-col-lg-4';
} else {
    $first_class = 'bcp-col-lg-12';
    $secound_class = 'bcp-col-lg-12';
}
?>
<!-- Profile style-1 main div Start -->
<div class="case-listing-block-1 profile-change-block profile-change-block-style-1">
    <!-- bcp-container-fluid Start -->
    <div class="bcp-container-fluid-">
        <!-- row Start -->
        <div class="bcp-row">
            <!-- first_class Start -->
            <div class="<?php echo $first_class; ?>">
                <div class="add-case-block">
                    <h2><?php _e($edit_profile_title, 'bcp_portal'); ?></h2>
                    <!-- module-form Start -->
                    <form method="post" enctype="multipart/form-data" class="module-form module-<?php echo $module_name; ?> ">
                        <!-- form-part Start -->
                        <div class="form-part">
                            <span id="form-success-msg" class='text-left success alert alert-success' role="alert" style="display:none;"></span>
                            <span id="form-error-msg" class='alert alert-danger text-left error' role="alert" role="alert" style="display:none;"></span>
                            <?php foreach ($fields as $k => $item) { ?>
                                <!-- section-div Start -->
                                <div class="<?php echo $item['section_name']; ?> section-div">
                                    <div class="bcp-col-md-12 bcp-col-lg-12 section-title">
                                        <h3 class="section_name">

                                            <?php _e($item['section_name'], 'bcp_portal'); ?>
                                        </h3>
                                    </div>
                                    <?php foreach ($item['rows'] as $fieldInformation) { ?>
                                        <!-- bcp-row Start -->
                                        <div class="bcp-row">
                                            <?php
                                            foreach ($fieldInformation as $key => $field_information) {

                                                $field_type = $field_information['type'];
                                                $required = "";
                                                $required_text = '';
                                                $readonly = "";
                                                $helptip = "";
                                                $disabled = ( $field_information['disabled'] && $field_information['disabled'] != "") ? "disabled='disabled'" : "";
                                                $class = ( $field_information['class'] && $field_information['class'] != "") ? $field_information['class'] : "";
                                                $data_value = isset($field_information['value']) ? $field_information['value'] : '';
                                                $label = isset($field_information['label']) ? $field_information['label'] : $value;
                                                $name = isset($field_information['name']) ? $field_information['name'] : "";
                                                if ($field_information['required']) {
                                                    $required = "required=''";
                                                    $required_text = '<span class="required">*</span>';
                                                }

                                                if ($field_information['helptip'] != "") {
                                                    $helptip = '<span data-toggle="tooltip" data-placement="top" data-title="' . __($field_information['helptip'], 'bcp_portal') . '"><em class="fa fa-info-circle"></em></span>';
                                                }

                                                if ($field_type == 'textarea') {
                                                    ?>
                                                    <!-- bcp-col-lg-6 Start -->
                                                    <div class="bcp-col-lg-6">
                                                        <div class="bcp-form-group">
                                                            <div class="bcp-flex"><label for="<?php echo $name; ?>"><?php _e($label, 'bcp_portal') . _e($helptip, 'bcp_portal') . _e($required_text); ?></label></div>
                                                            <textarea class="<?php echo $class; ?>" id="<?php echo $name; ?>" <?php echo $disabled; ?> name="<?php echo $name; ?>" <?php echo $required; ?> ><?php echo $data_value; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <!-- bcp-col-lg-6 End -->
                                                    <?php
                                                } else if (in_array($field_information['type'], array('multi-select', 'select'))) {
                                                    $data_value = explode(";", $data_value);
                                                    $option_values = $field_information['option'];
                                                    if (isset($option_values) && count($option_values) > 0) {
                                                        ?>
                                                        <!-- bcp-col-lg-6 Start -->
                                                        <div class="bcp-col-lg-6">
                                                            <div class="bcp-form-group">
                                                                <div class="bcp-flex bcp-align-items-center">
                                                                    <label for="<?php echo $name; ?>"><?php _e($label, 'bcp_portal') . _e($helptip, 'bcp_portal'); ?></label>
                                                                    <?php _e($required_text); ?>
                                                                </div>
                                                                <select class="custom-select <?php echo $class; ?>" <?php echo ($field_information['type'] == 'multipicklist') ? ' multiple' : ''; ?> id="<?php echo $name; ?>" <?php echo $disabled; ?> name="<?php echo $name; ?>" >
                                                                    <?php if ($field_information['type'] != 'multipicklist') { ?>
                                                                        <option value="" selected=""><?php _e('--None--'); ?></option>
                                                                        <?php
                                                                    }
                                                                    foreach ($option_values as $option) {
                                                                        $selected = '';
                                                                        if (in_array($option['value'], $data_value)) {
                                                                            $selected = ' selected="selected"';
                                                                        }
                                                                        ?>
                                                                        <option value="<?php echo $option['value']; ?>" <?php echo $selected; ?> ><?php echo $option['label']; ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!-- bcp-col-lg-6 End -->
                                                        <?php
                                                    }
                                                } else if ($field_type == 'boolean') {
                                                    $checked = '';
                                                    if ($data_value == TRUE) {
                                                        $checked = ' checked="checked"';
                                                    }
                                                    ?>
                                                    <!-- bcp-col-lg-6 Start -->
                                                    <div class="bcp-col-lg-6">
                                                        <div class="bcp-form-group">
                                                            <div class="form-check form-check-inline">
                                                                <input type="hidden" name="<?php echo $name; ?>" value="0" />
                                                                <input class="form-check-input <?php echo $class; ?>" name="<?php echo $name; ?>" type="checkbox" id="<?php echo $name; ?>" value="1">
                                                                <label class="form-check-label" for="inlineCheckbox1"><?php _e($label, 'bcp_portal') . _e($helptip, 'bcp_portal') . _e($required_text); ?></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- bcp-col-lg-6 End -->
                                                    <?php
                                                } else if ($field_type == 'reference') {
                                                    $relate_module_name = strtolower(isset($field_information['referenceTo']) ? $field_information['referenceTo'] : '');
                                                    if ($relate_module_name == "contact") {
                                                        continue;
                                                    }
                                                } else {
                                                    ?>
                                                    <!-- bcp-col-lg-6 Start -->
                                                    <div class="bcp-col-lg-6">
                                                        <div class="bcp-form-group">
                                                            <div class="bcp-flex bcp-align-items-center">
                                                                <label for="<?php echo $name; ?>"><?php _e($label, 'bcp_portal') . _e($helptip, 'bcp_portal'); ?></label>
                                                                <?php _e($required_text); ?>
                                                            </div>
                                                            <input id="<?php echo $name; ?>" value="<?php echo $data_value; ?>" type="<?php echo $field_type; ?>" class="bcp-form-control input-text <?php echo $class; ?>" name="<?php echo $name; ?>" <?php echo $required; ?> <?php echo $disabled; ?> >
                                                        </div>
                                                    </div>
                                                    <!-- bcp-col-lg-6 End -->
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <!-- bcp-row End -->
                                    <?php } ?>
                                </div>
                                <!-- section-div End -->
                            <?php } ?>
                            <!-- bcp-col-lg-12 Start -->
                            <div class="bcp-col-lg-12">
                                <div class="case-button-group">
                                    <?php wp_nonce_field('bcp_portal_nonce', 'bcp_portal_nonce_field'); ?>
                                    <input type="hidden" name="action" value="bcp_form_submit" />
                                    <input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>" />
                                    <input type="hidden" name="module_name" value="<?php echo $module_name; ?>" />
                                    <?php if ($record_id) { ?>
                                        <input type="hidden" name="Id" value="<?php echo $record_id; ?>" />
                                    <?php } ?>
                                    <button type="submit" class="bcp-btn"><?php _e($edit_profile_save_label, 'bcp_portal'); ?></button>
                                </div>
                            </div>
                            <!-- bcp-col-lg-12 End -->
                        </div>
                        <!-- form-part End -->
                    </form>
                    <!-- module-form End -->
                </div>
            </div>
            <!-- first_class End -->
            <!-- secound_class Start -->
            <div class="<?php echo $secound_class; ?>">
                <!-- change-password-block Start -->
                <div class="add-case-block change-password-block" id="bcp_change_pwd_div">
                    <h2><?php _e($change_password_text, 'bcp_portal'); ?></h2>
                    <div id="pwd-sucmsg" style='display:none;'>
                        <span class="col-lg-12 alert" role='alert'></span>
                    </div>
                    <!-- change_pwd_form Start -->
                    <form method="post" name="change_pwd_form" id="change_password" novalidate="novalidate">
                        <!-- row Start -->
                        <div class="bcp-row">
                            <!-- bcp-col-lg-12 Start -->
                            <div class="bcp-col-lg-12">
                                <div class="bcp-form-group">
                                    <div class="bcp-flex align-items-center">
                                        <label><?php _e('Old Password', 'bcp_portal'); ?>: </label>
                                        <em class="fa fa-info-circle" aria-hidden="true" title="<?php _e('Enter your current password.', 'bcp_portal'); ?>"></em>
                                        <span class="required">*</span>
                                    </div>
                                    <input type="Password" class="form-control bcp-required" maxlength="255" name='<?php echo $bcp_field_prefix; ?>Current_Password__c' id="old_password" required="">
                                </div>
                            </div>
                            <!-- bcp-col-lg-12 End -->
                            <!-- bcp-col-lg-12 Start -->
                            <div class="bcp-col-lg-12">
                                <div class="bcp-form-group">
                                    <div class="bcp-flex align-items-center">
                                        <label><?php _e('New Password', 'bcp_portal'); ?>: </label>
                                        <em class="fa fa-info-circle" aria-hidden="true" title="<?php _e('Your password must contain at least one uppercase letter, one lowercase letter, one special character and one numeric.', 'bcp_portal'); ?>"></em>
                                        <span class="required">*</span>
                                    </div>
                                    <input type="Password" class="form-control bcp-required" maxlength="255" name='<?php echo $bcp_field_prefix; ?>Password__c' minlength="5" id="pass1" required="">
                                </div>
                            </div>
                            <!-- bcp-col-lg-12 End -->
                            <!-- bcp-col-lg-12 Start -->
                            <div class="bcp-col-lg-12">
                                <div class="bcp-form-group">
                                    <div class="bcp-flex align-items-center">
                                        <label><?php _e('Confirm Password', 'bcp_portal'); ?>: </label>
                                        <em class="fa fa-info-circle" aria-hidden="true" title="<?php _e('Retype new password.', 'bcp_portal'); ?>"></em>
                                        <span class="required">*</span>
                                    </div>
                                    <input type="Password" class="form-control bcp-required" maxlength="255" name='<?php echo $bcp_field_prefix; ?>Retype_Password__c' minlength="5" id="pass2" required="">
                                </div>
                            </div>
                            <!-- bcp-col-lg-12 End -->
                            <!-- bcp-col-lg-12 Start -->
                            <div class="bcp-col-lg-12">
                                <div class="case-button-group">
                                    <input type='hidden' name='action' value='bcp_change_password'>
                                    <input type="hidden" name="record_id" value="<?php echo $contact_id; ?>">
                                    <button class="bcp-btn text-center" type="submit"><?php _e($change_password_save_label, 'bcp_portal'); ?></button>
                                </div>
                            </div>
                            <!-- bcp-col-lg-12 End -->
                        </div>
                        <!-- row End -->
                    </form>
                    <!-- change_pwd_form End -->
                </div>
                <!-- change-password-block End -->
            </div>
            <!-- secound_class End -->
        </div>
        <!-- row End -->
    </div>
    <!-- bcp-container-fluid End -->
</div>
<!-- Profile style-1 main div End -->