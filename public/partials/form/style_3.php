<?php
/** 
 * The file used to manage edit form style 3.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */
?>
<!-- case-listing-block-3 Start -->
<div class="case-listing-block case-listing-block-3 sfcp-form-block">
    <!-- bcp-container-fluid Start -->
    <div class="bcp-container-fluid">
        <!-- bcp-row Start -->
        <div class="bcp-row">
            <!-- bcp-col-lg-12 Start -->
            <div class="bcp-col-lg-12">
                <!-- add-case-block Start -->
                <div class="add-case-block">
                    <div class="heading-part <?php echo ($module_icon == "") ? 'bcp-without-icon' : ''; ?>">
                        <h2 class="" id="dcp-head-incident">
                            <span class="<?php echo $module_icon; ?>"></span>
                            <?php _e($heading_module, 'bcp_portal'); ?>
                        </h2>
                        <?php if ($module_name != "contact") { ?>
                            <h6><?php _e($module_sub_title, 'bcp_portal'); ?></h6>
                        <?php } ?>
                    </div>
                    <?php if ($record_id == "" && $module_name == "contact") { ?>
                        <div class="pull-right bcp-lang-wrapper">
                            <?php echo $bcp_helper->get_language_dropdown(); ?>
                            <div class="userinfo">
                                <a class="bcp-btn login_btn" title="<?php _e("Login", 'bcp_portal'); ?>" href="<?php echo $bcp_login_page; ?>"><?php _e('Login', 'bcp_portal'); ?></a>
                            </div>
                        </div>
                    <?php } ?>
                    <span id="form-success-msg" class='text-left success alert alert-success' role="alert" style="display:none;"><span class="lnr lnr-cross close-msg"></span></span>
                    <span id="form-error-msg" class='alert alert-danger text-left error' role="alert" role="alert" style="display:none;"><span class="lnr lnr-cross close-msg"></span></span>
                    <!-- module-form Start -->
                    <form method="post" enctype="multipart/form-data" class="module-form module-<?php echo $module_name; ?> ">
                        <!-- form-part Start -->
                        <div class="form-part">
                        <?php foreach ($fields as $k => $item) { ?>
                            <!-- section-div Start -->
                            <div class="<?php echo $item['section_name']; ?> section-div">
                                <div class="bcp-col-md-12 bcp-col-lg-12 section-title">
                                    <h3 class="section_name">
                                        <?php _e($item['section_name'], 'bcp_portal'); ?>
                                    </h3>
                                </div>
                                <?php
                                if ($module_name == 'contentdocument') {
                                    $item['rows'] = array_reverse($item['rows']);
                                }
                                foreach ($item['rows'] as $fieldInformation) {
                                    ?>
                                    <!-- bcp-row Start -->
                                    <div class="bcp-row">
                                        <?php
                                        foreach ($fieldInformation as $key => $field_information) {
                                            $field_type = $field_information['type'];
                                            $required = "";
                                            $required_text = '';
                                            $readonly = "";
                                            $helptip = "";
                                            $validationMessage = "";
                                            $placeholder = "";
                                            $selectionlimit = "";
                                            $hideFields = "";

                                            $disabled = ( $field_information['disabled'] && $field_information['disabled'] != "") ? "disabled='disabled'" : "";
                                            $class = ( $field_information['class'] && $field_information['class'] != "") ? $field_information['class'] : "";
                                            $data_value = isset($field_information['value']) ? $field_information['value'] : '';
                                            $label = isset($field_information['label']) ? $field_information['label'] : $value;
                                            $name = isset($field_information['name']) ? $field_information['name'] : "";

                                            $additional_attributes = "";
                                            if ($field_information['required']) {
                                                $required = "required=''";
                                                $required_text = '<span class="required">*</span>';
                                            }

                                            if ($field_information['helptip'] != "") {
                                                $helptip = '<span data-toggle="tooltip" data-placement="top" data-title="' . __($field_information['helptip'], 'bcp_portal') . '"><em class="fa fa-info-circle"></em></span>';
                                            }

                                            if ($field_information['placeholder'] != "" && $data_value == '') {
                                                $placeholder = 'placeholder="' . __($field_information['placeholder'], 'bcp_portal') . '"';
                                            }

                                            if ($field_information['validationMessage'] != "") {
                                                $validationMessage = 'validationMessage="' . __($field_information['validationMessage'], 'bcp_portal') . '"';
                                            }

                                            if ($field_information['selectionlimit'] != "") {
                                                $selectionlimit = 'selectionlimit="' . __($field_information['selectionlimit'], 'bcp_portal') . '"';
                                            }

                                            $hideFieldsdata = isset($field_information['hideFields']) ? $field_information['hideFields'] : "";
                                            if ($hideFieldsdata == "hide") {
                                                $hideFields = 'style="display:none;"';
                                            }

                                            if ($field_type == "number") {
                                                $additional_attributes .= " min='0'";
                                            }

                                            if ($field_type == 'textarea') {
                                                ?>
                                                <!-- bcp-col-lg-4 Start -->
                                                <div class="bcp-col-lg-4" <?php echo $hideFields; ?>>
                                                    <div class="bcp-form-group">
                                                        <div class="bcp-flex"><label for="<?php echo $name; ?>"><?php _e($label, 'bcp_portal') . _e($helptip, 'bcp_portal') . _e($required_text); ?></label></div>
                                                        <textarea class="<?php echo $class; ?>" id="<?php echo $name; ?>" <?php echo $disabled; ?> name="<?php echo $name; ?>" <?php echo $validationMessage; ?> <?php echo $required; ?> <?php echo $validationMessage; ?> <?php echo $placeholder; ?>><?php echo $data_value; ?></textarea>
                                                    </div>
                                                </div>
                                                <!-- bcp-col-lg-4 End -->
                                                <?php
                                            } else if (in_array($field_information['type'], array('multi-select'))) {
                                                $option_values = $field_information['option'];
                                                $field_name = $name . '[]';
                                                $data_value = (!empty($data_value) ? explode(";", $data_value) : array() );
                                                if (!empty($data_value)) {
                                                    $count = count($data_value);
                                                    if ($count < 3) {
                                                        $data_value_count = implode(',', $data_value);
                                                    } else {
                                                        $data_value_count = "<span>" . $count . "</span> Selected";
                                                    }
                                                } else {
                                                    $data_value_count = 'Select';
                                                }
                                                if (isset($option_values) && count($option_values) > 0) {
                                                    ?>
                                                    <!-- bcp-col-lg-4 Start -->
                                                    <div class="bcp-col-lg-4">
                                                        <div class="bcp-form-group">
                                                            <div class="bcp-flex bcp-align-items-center">
                                                                <label for="<?php echo $name; ?>"><?php _e($label, 'bcp_portal') . _e($helptip, 'bcp_portal'); ?></label>
                                                                <?php _e($required_text); ?>
                                                            </div>
                                                            <div class="multi-select1 bcp-form-control input-text">
                                                                <div class="multi-select-title">
                                                                    <label class="bcp-picklist-title" for=""><?php echo $data_value_count; ?></label>
                                                                    <em class="fa fa-sort-down arrow-down"> </em>
                                                                </div>
                                                                <div class="multi-select-dropdown"  >
                                                                    <ul <?php echo $selectionlimit; ?>>
                                                                        <?php
                                                                        foreach ($option_values as $option) {
                                                                            $checked = '';
                                                                            if (in_array($option['value'], $data_value)) {
                                                                                $checked = 'checked="checked"';
                                                                            }
                                                                            ?>
                                                                            <li>
                                                                                <input <?php echo $checked; ?> class="where_field sfcp-multi-dropdown <?php echo $class; ?>" type="checkbox" name="<?php echo $field_name; ?>" value="<?php echo $option['value']; ?>" id="<?php echo $option['value']; ?>" field-name="<?php echo $name; ?>"  form-type="multi-select" module_name="<?php echo $module_name; ?>"> 
                                                                                <label for="<?php echo $option['value']; ?>"><?php echo $option['label']; ?></label>
                                                                            </li>
                                                                            <?php } ?>
                                                                    </ul>

                                                                </div>
                                                                <input type="text" value="<?php echo $data_value_count; ?>" name="<?php echo $name; ?>_hidden" class="required_multi_select input-hidden" <?php echo $required; ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- bcp-col-lg-4 End -->
                                                    <?php
                                                }
                                            } else if (in_array($field_information['type'], array('select'))) {
                                                $data_value = explode(";", $data_value);
                                                $option_values = isset($field_information['option']) ? $field_information['option'] : array();
                                                $field_name = ( $field_information['type'] == 'multi-select' ? $name . '[]' : $name );
                                                if (isset($option_values) && count($option_values) > 0) {
                                                    ?>
                                                    <!-- bcp-col-lg-4 Start -->
                                                    <div class="bcp-col-lg-4" <?php echo $hideFields; ?>>
                                                        <div class="bcp-form-group">
                                                            <div class="bcp-flex bcp-align-items-center">
                                                                <label for="<?php echo $name; ?>"><?php _e($label, 'bcp_portal') . _e($helptip, 'bcp_portal'); ?></label>
                                                                <?php _e($required_text); ?>
                                                            </div>
                                                            <select class="custom-select <?php echo $class; ?>" <?php echo ($field_information['type'] == 'multi-select') ? ' multiple' : ''; ?> id="<?php echo $name; ?>" <?php echo $disabled; ?> name="<?php echo $name; ?>" <?php echo $validationMessage; ?> <?php echo $selectionlimit; ?> <?php echo $required; ?> form-type="select" module_name="<?php echo $module_name; ?>">
                                                                <?php if ($field_information['type'] != 'multi-select') { ?>
                                                                    <option value="" selected=""><?php _e('--None--', 'bcp_portal'); ?></option>
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
                                                    <!-- bcp-col-lg-4 End -->
                                                    <?php
                                                }
                                            } else if ($field_type == 'boolean') {
                                                $checked = '';
                                                if ($data_value == TRUE) {
                                                    $checked = ' checked="checked"';
                                                }
                                                ?>
                                                <!-- bcp-col-lg-4 Start -->
                                                <div class="bcp-col-lg-4" <?php echo $hideFields; ?>>
                                                    <div class="bcp-form-group">
                                                        <div class="form-check form-check-inline">
                                                            <input type="hidden" name="<?php echo $name; ?>" value="0" />
                                                            <input class="form-check-input <?php echo $class; ?>" name="<?php echo $name; ?>" type="checkbox" id="<?php echo $name; ?>" <?php echo $checked; ?> <?php echo $required; ?> value="1" <?php echo $validationMessage; ?> form-type="checkbox" module_name="<?php echo $module_name; ?>">
                                                            <label class="form-check-label" for="<?php echo $name; ?>"><?php _e($label, 'bcp_portal') . _e($helptip, 'bcp_portal') . _e($required_text); ?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- bcp-col-lg-4 End -->
                                                <?php
                                            } else if ($field_type == 'reference') {
                                                $bcp_modules = isset($_SESSION['bcp_modules']) ? $_SESSION['bcp_modules'] : "";
                                                $relate_module_name = $field_information['referenceTo'];
                                                $option_values = isset($field_information['option']) ? $field_information['option'] : "";
                                                if ($relate_module_name == "contact") {
                                                    ?>
                                                    <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $_SESSION['bcp_contact_id']; ?>" >
                                                    <?php
                                                }

                                                if ((isset($bcp_modules[$relate_module_name]) && !empty($bcp_modules[$relate_module_name])) && $module_name != $relate_module_name) {
                                                    if (isset($option_values) && count($option_values) > 0) {
                                                        ?>
                                                        <!-- bcp-col-lg-4 Start -->
                                                        <div class="bcp-col-lg-4" <?php echo $hideFields; ?>>
                                                            <div class="bcp-form-group">
                                                                <div class="bcp-flex bcp-align-items-center">
                                                                    <label><?php _e($label, 'bcp_portal') . _e($helptip, 'bcp_portal') . _e($required_text); ?></label>
                                                                </div>
                                                                <select id="<?php echo $name; ?>" <?php echo $disabled; ?> name="<?php echo $name; ?>" class="custom-select <?php echo $class; ?>" <?php echo $required; ?> <?php echo $validationMessage; ?> <?php echo $selectionlimit; ?>>
                                                                    <option value="" disabled="" selected=""><?php _e('--None--', 'bcp_portal'); ?></option>
                                                                    <?php
                                                                    foreach ($option_values as $option) {
                                                                        $selected = '';
                                                                        if ($option['value'] == $data_value) {
                                                                            $selected = ' selected="selected"';
                                                                        }
                                                                        ?>
                                                                        <option value="<?php echo $option['value']; ?>" <?php echo $selected; ?> ><?php echo $option['label']; ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!-- bcp-col-lg-4 End -->
                                                        <?php
                                                    }
                                                }
                                            } else if ($field_type == 'file') {
                                            /* File Attachment @author Dhari Gajera */
                                            ?>
                                               <!-- bcp-col-lg-4 Start -->
                                                <div class="bcp-col-lg-4" <?php echo $hideFields; ?>>
                                                    <div class="bcp-form-group">
                                                        <div class="bcp-flex bcp-align-items-center">
                                                            <label for="<?php echo $name; ?>"><?php _e($label, 'bcp_portal') . _e($helptip, 'bcp_portal'); ?></label>
                                                            <?php _e($required_text); ?>
                                                        </div>
                                                        <input <?php echo $additional_attributes; ?> id="<?php echo $name; ?>" value="<?php echo $data_value; ?>" type="<?php echo $field_type; ?>" class="bcp-form-control input-text <?php echo $class; ?> bcp-attachment" name="<?php echo $name; ?>" <?php echo $validationMessage; ?> <?php echo $required; ?> <?php echo $disabled; ?> >
                                                        <span id="error-msg-after-flex-div"></span>
                                                        <p class="upload-file"><?php _e('Maximum upload file size: 2MB.', 'bcp_portal'); ?></p>
                                                    </div>
                                                </div>
                                                <!-- bcp-col-lg-4 End -->
                                            <?php } else { ?>
                                                <!-- bcp-col-lg-4 Start -->
                                                <div class="bcp-col-lg-4" <?php echo $hideFields; ?>>
                                                    <div class="bcp-form-group">
                                                        <div class="bcp-flex bcp-align-items-center">
                                                            <label for="<?php echo $name; ?>"><?php _e($label, 'bcp_portal') . _e($helptip, 'bcp_portal'); ?></label>
                                                            <?php _e($required_text); ?>
                                                        </div>
                                                        <input <?php echo $additional_attributes; ?> id="<?php echo $name; ?>" value="<?php echo $data_value; ?>" type="<?php echo $field_type; ?>" class="bcp-form-control input-text <?php echo $class; ?>" name="<?php echo $name; ?>" <?php echo $validationMessage; ?> <?php echo $placeholder; ?> <?php echo $required; ?> <?php echo $disabled; ?> >
                                                    </div>
                                                </div>
                                                <!-- bcp-col-lg-4 End -->
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div> <!-- bcp-row End -->
                                <?php } ?>
                            </div> <!-- section-div End -->
                            <?php
                        }

                        if ($module_name == "case" && empty($record_id)) {
                            ?>
                            <!-- section-div Start -->
                            <div class="section-div bcp-row">
                                <div class="bcp-col-md-12 bcp-col-lg-12 section-title">
                                    <h3 class="section_name">
                                        <?php echo __("Case Attachment"); ?>
                                    </h3>
                                </div>
                                <!-- bcp-col-lg-4 Start -->
                                <div class="bcp-col-lg-4">
                                    <div class="bcp-form-group">
                                        <div class="bcp-flex"><label><?php _e("Attachment", 'bcp_portal'); ?></label></div>
                                        <div class="bcp-form-group">
                                            <input id="uploadFile" placeholder="<?php _e("Choose File", 'bcp_portal'); ?>" disabled="disabled" />
                                            <div class="fileUpload btn btn-primary">
                                                <span><?php _e("Upload", 'bcp_portal'); ?></span>
                                                <input id="Attachment" type="file" class="upload" name="Attachment" />
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- bcp-col-lg-4 Start -->
                            </div> <!-- section-div End -->
                        <?php } ?>
                        </div>
                        <!-- form-part End -->
                        <?php
                        if (isset($show_product_selection) && ( isset($available_pricebooks) && is_array($available_pricebooks) && count($available_pricebooks) > 0)) {
                            include("product_selection.php");
                        }
                        ?>
                        <div class="bcp-col-lg-12 text-center">
                            <div class="case-button-group">
                                <?php wp_nonce_field('bcp_portal_nonce', 'bcp_portal_nonce_field'); ?>
                                <input type="hidden" name="action" value="bcp_form_submit" />
                                <input type="hidden" name="contact_id" value="<?php echo $_SESSION['bcp_contact_id']; ?>" />
                                <input type="hidden" name="module_name" value="<?php echo $module_name; ?>" />
                                <?php if ($record_id == "" && $module_name == "contact") { ?>
                                    <input type="hidden" class="browser_timezone" name="browser_timezone" value="" />
                                    <?php
                                }
                                if ($record_id) {
                                    ?>
                                    <input type="hidden" name="Id" value="<?php echo $record_id; ?>" />
                                    <?php
                                }
                                if ($relationship) {
                                    ?>
                                    <input type="hidden" name="relationship" value="<?php echo $relationship; ?>" />
                                    <?php
                                }
                                if ($relate_modulename) {
                                    ?>
                                    <input type="hidden" name="relate_module_name" value="<?php echo $relate_modulename; ?>" />
                                    <?php
                                }
                                if ($relate_module_id) {
                                    ?>
                                    <input type="hidden" name="relate_module_id" value="<?php echo $relate_module_id; ?>" />
                                <?php } ?>
                                <button type="submit" class="bcp-btn save-btn"><?php _e('Save', 'bcp_portal'); ?></button>
                                <?php if ($record_id != "" && $module_name != "contact") { ?>
                                    <button type="button" class="bcp-btn bcp-gray" onclick="window.location = '<?php echo home_url('/customer-portal/' . $module_name . '/list/'); ?>'"><?php _e('Cancel', 'bcp_portal'); ?></button>
                                <?php } ?>
                            </div>
                        </div>
                    </form> <!-- module-form End -->
                </div> <!-- add-case-block End -->
            </div> <!-- bcp-col-lg-12 End -->
        </div> <!-- bcp-row End -->
    </div> <!-- bcp-container-fluid End -->
</div>
<!-- case-listing-block-3 End -->