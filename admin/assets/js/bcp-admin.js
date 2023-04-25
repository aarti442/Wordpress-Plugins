/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function () {
    jQuery(".sfcp_log_view_more").click(function () {
        jQuery("#thick_sfcp_req_url").text(jQuery(this).parent().parent().find(".sfcp_req_url").text());
        jQuery("#thick_sfcp_req_method").text(jQuery(this).parent().parent().find(".sfcp_req_method").text());
        jQuery("#thick_sfcp_req_time").text(jQuery(this).parent().parent().find(".sfcp_req_time").text());
        jQuery("#thick_sfcp_req_data").text(jQuery(this).parent().parent().find(".sfcp_req_data").text());
        jQuery("#thick_sfcp_res_time").text(jQuery(this).parent().parent().find(".sfcp_res_time").text());
        jQuery("#thick_sfcp_res_data").html(jQuery(this).parent().parent().find(".sfcp_res_data").text());
        jQuery("#thick_sfcp_res_status").text(jQuery(this).parent().parent().find(".sfcp_res_status").text());
    });

    jQuery("#bcp_login_password_enable").click(function () {
        if (jQuery(this).is(":checked")) {
            jQuery("#bcp_maximum_login_attempts").removeAttr("disabled");
            jQuery("#bcp_lockout_effective_period").removeAttr("disabled");
        } else {
            jQuery("#bcp_maximum_login_attempts").attr("disabled", "disabled");
            jQuery("#bcp_lockout_effective_period").attr("disabled", "disabled");
        }
    });

    jQuery(document).on('change', '#bcp_type_selection select#bcp_page_type', function (event) {
        var bcp_type = jQuery(this).val();
        jQuery('#bcp_type_selection .module-selection').hide();
        if (['bcp_list', 'bcp_add_edit', 'bcp_detail'].includes(bcp_type)) {
            jQuery('#bcp_type_selection .module-selection').show();
        }
    });

    jQuery('button.sync-button').on('click', function () {

        jQuery(this).attr('disabled', true);
        jQuery(this).find(".dashicons-update-alt").hide();
        var bcp_updated_layout = jQuery("#bcp_layout_array").val();
        var myCheckboxes = new Array();
        jQuery("ul#module_selection input:checked").each(function() {
            myCheckboxes.push(jQuery(this).val());
        });
        var bcp_roles = jQuery("#bcp_roles").val();
        jQuery.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            async: true,
            data: {
                action: 'bcp_admin_update_layout',
                update_layout: true,
                bcp_updated_layout: bcp_updated_layout,
                selected_modules : myCheckboxes,
                bcp_roles : bcp_roles
            },
            beforeSend: function () {
                jQuery('span.sync-loader').show();
                jQuery('span.sync-loader').css('visibility', 'visible');

            },
            success: function (response) {
                jQuery("#first_time_sync").val("0");
                response = JSON.parse(response);
                jQuery('span.sync-loader').hide();
                jQuery(".dashicons-update-alt").show();
                jQuery('.nav-tab-wrapper').before(response.message);
                jQuery('button.sync-button').attr('disabled','disabled');
                jQuery(".bcp-layout-sync-ul").remove();
                jQuery(".bcp-layout-sync-note").remove();
                jQuery('ul#module_selection input[type=checkbox]').prop( "checked", false );
                jQuery(".bcp-layout-info-msg").html(jQuery(".bcp-layout-info-msg").attr("data-norecords"));
            }
        });
     
//        jQuery.ajax({
//            url: my_ajax_object.ajax_url,
//            type: 'POST',
//            async: true,
//            data: {
//                action: 'bcp_admin_update_bcp_fields',
//            },
//            success: function (response) {
//                response = JSON.parse(response);
//                console.log(response);
//            }
//        });
    });
    jQuery('body').on('click', 'ul#module_selection input.module_chk_select[type=checkbox]', function (event) {
        if(jQuery('ul#module_selection input[type=checkbox]:checked').length > 0){
            jQuery('.sync-button').removeAttr('disabled');
        }else{
            jQuery('.sync-button').attr('disabled','disabled');
        }
        console.log(jQuery('ul#module_selection input.module_chk_select[type=checkbox]:checked').length +'=='+jQuery('.module_chk_select').length);
        if (jQuery('ul#module_selection input.module_chk_select[type=checkbox]:checked').length== jQuery('.module_chk_select').length) {
            jQuery('.module_chk_selectAll').prop('checked', true);
        }else{
            jQuery('.module_chk_selectAll').prop('checked', false);
        }
    });

    jQuery('body').on('click', 'ul#module_selection input.module_chk_selectAll[type=checkbox]', function (event) {
        if(jQuery('ul#module_selection input.module_chk_selectAll[type=checkbox]:checked').length > 0){
            jQuery('ul#module_selection input.module_chk_select[type=checkbox]').prop('checked', true);
            jQuery('.sync-button').removeAttr('disabled');
        }
    });
    jQuery('body').on('change','#sfcp-menu-select-all', function () {
        if(this.checked) {
            jQuery("#sfcp-menu-items-checklist-pop input[type=checkbox]").prop('checked', true);
        } else {
            jQuery("#sfcp-menu-items-checklist-pop input[type=checkbox]").prop('checked', false);
        }
    });

    jQuery('body').on('change','#authentication_method', function () {
        var val = jQuery(this).val();
        console.log(val);
        if(val == "user_pwd_flow"){
            jQuery('#jwt_bearer_flow').find('input').removeAttr('required');
            jQuery('#jwt_bearer_flow').find('textarea').removeAttr('required');
            jQuery('#jwt_bearer_flow').hide();
            jQuery('#user_pwd_flow').show();
            if(jQuery('#user_pwd_flow').find('.bcp_client_id_hidden').val() == ""){
                jQuery('#user_pwd_flow').find('input[name=bcp_client_id]').attr('required','required');
            }
            if(jQuery('#user_pwd_flow').find('.bcp_client_secret_hidden').val() == ""){
                jQuery('#user_pwd_flow').find('input[name=bcp_client_secret]').attr('required','required');
            }
            if(jQuery('#user_pwd_flow').find('.bcp_username_hidden').val() == ""){
                jQuery('#user_pwd_flow').find('input[name=bcp_username]').attr('required','required');
            }
            if(jQuery('#user_pwd_flow').find('.bcp_password_hidden').val() == ""){
                jQuery('#user_pwd_flow').find('input[name=bcp_password]').attr('required','required');
            }
            if(jQuery('#user_pwd_flow').find('.bcp_security_token_hidden').val() == ""){
                jQuery('#user_pwd_flow').find('input[name=bcp_security_token]').attr('required','required');
            }
            jQuery('#user_pwd_flow').find('textarea').attr('required','required');
            
        }else if(val == "jwt_bearer_flow"){
            jQuery('#jwt_bearer_flow').show();
            if(jQuery('#jwt_bearer_flow').find('.bcp_client_id_jwt_hidden').val() == ""){
                jQuery('#jwt_bearer_flow').find('input[name=bcp_client_id_jwt]').attr('required','required');
            }
            if(jQuery('#jwt_bearer_flow').find('.bcp_username_jwt_hidden').val() == ""){
                jQuery('#jwt_bearer_flow').find('input[name=bcp_username_jwt]').attr('required','required');
            }
            jQuery('#jwt_bearer_flow').find('textarea').attr('required','required');
            
            jQuery('#user_pwd_flow').hide();
            jQuery('#user_pwd_flow').find('input').removeAttr('required');
            jQuery('#user_pwd_flow').find('textarea').removeAttr('required');
        }else{
            jQuery('#jwt_bearer_flow').hide();
            jQuery('#user_pwd_flow').show();
        }
    });

   
    jQuery('body').on('click','#bcp_enable_success_logs', function () {
        if (jQuery(this).prop('checked')==true){ 
            jQuery(this).val(1);
        }else{
            jQuery(this).val(0);
        }
    });

    jQuery('body').on('change','#bcp_operation_mode',function(){
        var  val = jQuery(this).val();
        jQuery('#bcp_operation_mode_jwt').val(val);

    });
    jQuery('body').on('change','#bcp_operation_mode_jwt',function(){
        var  val = jQuery(this).val();
        jQuery('#bcp_operation_mode').val(val);

    });


    
//    if (jQuery("#first_time_sync").val() == "0") {
//        jQuery('button.sync-button').click();
//    }
});
