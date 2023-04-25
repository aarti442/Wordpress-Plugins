//mange page script
var ajaxurl = my_ajax_object.ajaxurl;
var wpml_current_lang = my_ajax_object.bcp_current_lang;

// To render edit form function
function bcp_render_edit_form(module_name, id = "", form_title, view_style, capcha_enable = "") {
    jQuery('#responsedataPortal').addClass('bcp-loading');
    var data = {
        "action": "bcp_edit_portal",
        "module_name": module_name,
        "record_id": id,
        "capcha_enable": capcha_enable,
        "view_style": view_style,
        "form_title": form_title,
    };
    if (wpml_current_lang) {
        data.lang = wpml_current_lang;
    }
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: data,
        beforeSend: function () {
        },
        success: function (response) {
            jQuery('#responsedataPortal').removeClass('bcp-loading');
            jQuery("html, body").animate({
                scrollTop: jQuery("#responsedataPortal").offset().top,
            }, 100);
            jQuery('#responsedataPortal').html(response);
            if (jQuery("#bcp_change_pwd_div").length > 0) {
                jQuery("#bcp_change_pwd_div").show();
            }
            if (jQuery('.browser_timezone').length) {
                var timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                jQuery('.browser_timezone').val(timeZone);
            }
            bcp_datetimepicker_formate();
        }
    });
}

/* profile edit form event */
function bcp_render_profile_edit_form(module_name, view, view_style, edit_profile_title, change_password_text, edit_profile_save_label, change_password_save_label) {
    jQuery('#responsedataPortal').addClass('bcp-loading');
    var data = {
        "action": "bcp_edit_portal",
        "module_name": module_name,
        "view": view,
        "view_style": view_style,
        "edit_profile_title": edit_profile_title,
        "change_password_text": change_password_text,
        "edit_profile_save_label": edit_profile_save_label,
        "change_password_save_label": change_password_save_label,
    };  
    if (wpml_current_lang) {
        data.lang = wpml_current_lang;
    }
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: data,
        beforeSend: function () {
            jQuery("html, body").animate({
                scrollTop: jQuery("#responsedataPortal").offset().top,
            }, 100);
        },
        success: function (response) {
            jQuery('#responsedataPortal').html(response);
            jQuery('#responsedataPortal').removeClass('bcp-loading');
            if (jQuery("#bcp_change_pwd_div").length > 0) {
                jQuery("#bcp_change_pwd_div").show();
            }
            bcp_datetimepicker_formate();
        }
    });
}

// To validate and submit edit form
jQuery(document).on('submit', '.module-form', function (e) {
    e.preventDefault();
    if (jQuery('#scp-recaptcha').length > 0 && jQuery('#scp-recaptcha').html() != "") {
        jQuery('#sfcp-recaptcha-error-msg').hide();
        var msg = grecaptcha.getResponse(widget1);
        if (jQuery.trim(msg) == "") {
            jQuery('#sfcp-recaptcha-error-msg').show();
            return false;
        }
    }
    jQuery('#responsedataPortal').addClass('bcp-loading');
    
    jQuery("button[type='submit']").attr("disabled", "disabled");
    var data = new FormData(jQuery(this)[0]);
    if (wpml_current_lang) {
        data.append('lang', wpml_current_lang);
    }
    setTimeout(function () {
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
            },
            success: function (response) {
                jQuery('#responsedataPortal').removeClass('bcp-loading');
                var res = JSON.parse(response);
                var redirect_url = res.redirect_url;
                var msg = res.msg;
                if (res.success) {
                    
//                    if (res.module_name == "account") {
//                        jQuery(".dcp-menu-account").find(".case-add").hide();
//                    }
                    if (res.module_name == "contact" && jQuery.trim(redirect_url) == "") {
                        jQuery("#form-success-msg").html(msg);
                        jQuery("#form-success-msg").append('<span class="lnr lnr-cross close-msg"></span>') ;
                        if ("bcp_contact_name" in res) {
                            jQuery(".dcp_header_contact_name").text(res.bcp_contact_name);
                        }
                        jQuery("#form-success-msg").show();
                        jQuery("html, body").animate({
                            scrollTop: jQuery("#form-error-msg").offset().top,
                        }, 100);
                        if (jQuery('#form-error-msg').length > 0 || jQuery('#form-success-msg').length > 0) {
                            /*setTimeout(function () {
                                jQuery('#form-error-msg').html("");
                                jQuery('#form-error-msg').hide();
                                jQuery('#form-success-msg').html("");
                                jQuery('#form-success-msg').hide();
                            }, 5000);  */ 
                        }
                        jQuery("button[type='submit']").removeAttr("disabled");
                    } else if (res.module_name == "CaseComment") {
                        jQuery('.sfcp-validation.casecomment-msg').html('<span class="success">' + res.msg + '<span class="lnr lnr-cross close-msg"></span></span>');
                        bcp_case_comments();
                        jQuery("button[type='submit']").removeAttr("disabled");
                    } else if (res.module_name == 'contentnote' && ("parent_module" in res) && res.parent_module == "case" && ("record" in res)) {
                        jQuery('.sfcp-validation.casenote-msg').html('<span class="success">' + res.msg + '<span class="lnr lnr-cross close-msg"></span></span>');
                        var record = res.record;
                        bcp_case_notes(record);
                        jQuery("button[type='submit']").removeAttr("disabled");
                    } else if (res.module_name == "contentdocument" && ("parent_module" in res) && res.parent_module == "case" && ("record" in res)) {
                        jQuery('.sfcp-validation.Attachment').html('<span class="success">' + res.msg + '<span class="lnr lnr-cross close-msg"></span></span>');
                        var record = res.record;
                        bcp_case_attachments(record);
                        jQuery("button[type='submit']").removeAttr("disabled");
                    } else if (jQuery.trim(redirect_url) != "") {
                        window.location.href = redirect_url;
                    }
                } else {
                    jQuery("button[type='submit']").removeAttr("disabled");
                    jQuery("label.error-line").remove();
                    if (res.msg == 1 && ("msg_fields" in res)) {
                        var msg_fields = res.msg_fields;
                        jQuery(msg_fields).each(function (key, value) {
                            jQuery("#" + value.Key).after("<label class='error-line'>" + value.Value + "</label>");
                        });
                    } else {
                        if (res.module_name == "contentdocument") {
                            jQuery('.sfcp-validation.Attachment').html('<div class="login_error"><span class="error">' + msg + '</span></div>');
                        } else if (jQuery(".scp-casecomment-form").find("input[name='module_name']").length > 0 && jQuery(".scp-casecomment-form").find("input[name='module_name']").val() == "CaseComment") {
                            jQuery('.sfcp-validation.casecomment-msg').html('<div class="login_error"><span class="error">' + msg + '</span></div>');
                        } else {
                            jQuery("#form-error-msg").text(msg) ;
                            jQuery("#form-error-msg").append('<span class="lnr lnr-cross close-msg"></span>') ;
                           
                            jQuery("#form-error-msg").show();
                            if (jQuery("#form-error-msg").length > 0) {
                                jQuery("html, body").animate({
                                    scrollTop: jQuery("#form-error-msg").offset().top,
                                }, 100);
                            }

                            if (jQuery('#form-error-msg').length > 0) {
                                /*setTimeout(function () {
                                    jQuery('#form-error-msg').html("");
                                    jQuery('#form-error-msg').hide();
                                }, 5000);   */
                            }
                        }
                    }
                }
            }
        });
    }, 1000);
    return false;
});

/* case_comments event */
function bcp_case_comments() {
    jQuery('body #casecomments').addClass('bcp-loading');
    var record_id = jQuery('#case-comments').attr('record-id');
    var style = jQuery('#case-comments').attr('data-style');
    var data = {
        'action': 'bcp_case_comments',
        'record_id': record_id,
        'view_style': style
    };
    jQuery.post(ajaxurl, data, function (response) {
        jQuery('body #casecomments').removeClass('bcp-loading');
        jQuery('#case-comments .casecomment_content').html(response);
    });
}

/* case_notes event */
function bcp_case_notes(record) {
    jQuery('body #case-notes').addClass('bcp-loading');
    //var record_id = jQuery('#case-notes').attr('record-id');
    var style = jQuery('#case-notes').attr('data-style');
    var data = {
        'action': 'bcp_case_notes',
        'record': record,
        'view_style': style
    };
    jQuery.post(ajaxurl, data, function (response) {
        jQuery("input[name='Title']").val("");
        jQuery("textarea[name='Content']").val("");
        jQuery('.bcp-notes-list').append(response);
        jQuery('body #case-notes').removeClass('bcp-loading');
       /* setTimeout(function () {
            jQuery('.sfcp-validation.casenote-msg').html("");
        }, 2000);*/
    });
}

/* case_attachments event */
function bcp_case_attachments(record) {
    jQuery('body #attachments').addClass('bcp-loading');
    var parent_module = jQuery('#module-attachments').attr('data-parent-module');
    var data = {
        'action': 'bcp_case_attachments',
        'record': record,
        'parent_module': parent_module,
    };
    jQuery.post(ajaxurl, data, function (response) {
        jQuery("input[name='Attachment']").val("");
        jQuery('label[for=Attachment]').html('No file Chosen');
        jQuery("textarea[name='Description']").val("");
        if (jQuery("#case-attachment-no-records").length > 0) {
            jQuery('.sfcp_attachment-list').html(response);
        } else {
            jQuery('.sfcp_attachment-list').append(response);
        }
        jQuery('body #attachments').removeClass('bcp-loading');
        setTimeout(function () {
            jQuery('.sfcp-validation.Attachment').html("");
        }, 2000);
    });
}


/* change_password validation event */
var pass1 = jQuery(document).find('#pass1');
var pass2 = jQuery(document).find('#pass2');
var regex_pwd = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[`~!@#$%^&*()\-_=+[\]{}|\\;:'",./<>?])/;

jQuery(document).ready(function ($) {
    
    $('body').on('change', '#pass1', function () {
        
        $('#pass1').parent('div').find('label.error-line').remove();
        $('#pass1').removeClass('error-line');
        if ($('#pass1').val().length < 8) {
            $('#pass1').addClass('error-line');
            $('<label class="error-line error"> ' + __('Password must be atleast 8 character long.', 'bcp_portal') + ' </label>').insertAfter('#pass1');
            return false;
        }
        if (!regex_pwd.test($('#pass1').val())) {
            $('#pass1').addClass('error-line');
            $('<label class="error-line error"> ' + __('Your password must contain at least one uppercase letter, one lowercase letter, one special character and one numeric.', 'bcp_portal') + ' </label>').insertAfter('#pass1');
            return false;
        }
    });

    $(document).on('keyup', '#pass2', function () {
        $('#pass2').parent('div').find('label.error-line').remove();
        $('#pass2').removeClass('error-line');
        if ($('#pass1').val() != $('#pass2').val() && $('#pass2').val() != "") {
            $('#pass2').addClass('error-line');
            $('#pass2').parent('div').find('label.error-line').remove();
            $('<label class="error-line error">' + __('Password does not match.', 'bcp_portal') + '</label>').insertAfter('#pass2');
        }
    });
});
 
/* change_password validation event */
jQuery(document).on('submit', '#change_password', function (e) {
    e.preventDefault();

    jQuery('label.error-line', '#change_password').remove();
    var bcp_required_error = 0;
    jQuery('#change_password').addClass('bcp-loading');

    jQuery('input').removeClass('error-line');
    jQuery('label.error-line.error').remove();
    var pass1 = jQuery(this).find('#pass1');
    var pass2 = jQuery(this).find('#pass2');

    jQuery('.bcp-required', this).each(function () {
        if (!jQuery(this).val().trim()) {
            jQuery(this).addClass('error-line');
            jQuery(this).parent('div').find('label.error-line').remove();
            jQuery('<label class="error-line error">' + __('This field is required.', 'bcp_portal') + '</label>').insertAfter(this);
            bcp_required_error = 1;
        }
    });
    if (pass1.val() != '') {
        if (jQuery('#pass1').val().length < 8) {
            jQuery('#pass1').addClass('error-line');
            jQuery('<label class="error-line error"> ' + __('Password must be atleast 8 character long.', 'bcp_portal') + ' </label>').insertAfter('#pass1');
            bcp_required_error = 1;
        }
        if ((!regex_pwd.test(jQuery('#pass1').val())) && (jQuery('#pass1').val().length > 7)) {
            jQuery('#pass1').addClass('error-line');
            jQuery('<label class="error-line error"> ' + __('Your password must contain at least one uppercase letter, one lowercase letter, one special character and one numeric.', 'bcp_portal') + ' </label>').insertAfter('#pass1');
            bcp_required_error = 1;
        }
    }
    
    if (pass1.val() != pass2.val()) {
        jQuery(pass2).addClass('error-line error');
        jQuery(pass2).parent('div').find('label.error-line.error').remove();
        jQuery('<label class="error-line error">' + __('Password does not match.', 'bcp_portal') + '</label>').insertAfter(pass2);        
        bcp_required_error = 1;
    }
    

    if (bcp_required_error) {
        jQuery('#change_password').removeClass('bcp-loading');
        return false;
    }
    

    var data = new FormData(jQuery(this)[0]);
    if (wpml_current_lang) {
        data.append('lang', wpml_current_lang)
    }

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: data,
        async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
        },
        success: function (response) {

            jQuery('#change_password').removeClass('bcp-loading');
            var response = jQuery.parseJSON(response);
            jQuery('#change_password .success').remove();
            jQuery('#change_password .error').remove();
            jQuery('#change_password')[0].reset();
            jQuery('#change_password:first *:input[type!=hidden]:first').focus();
            jQuery('#pwd-sucmsg').hide();
            jQuery('#pwd-sucmsg').find("span").removeClass("success alert-success");
            jQuery('#pwd-sucmsg').removeClass("pwd_danger ");
            if (response['success']) {
                jQuery('#pwd-sucmsg').find("span").addClass("success alert-success");
            } else {
                jQuery('#pwd-sucmsg').find("span").addClass("error alert-error");
                jQuery('#pwd-sucmsg').addClass("pwd_danger");
            }
            jQuery('#pwd-sucmsg').find("span").html(response['message']);
            jQuery('#pwd-sucmsg').show();
            setTimeout(function () {
                jQuery('#pwd-sucmsg').hide();
            }, 4000);
        }
    });

    return false;
});

/* Case Attachment change event */
jQuery('body').on('change', '#Attachment', function (e) {
    jQuery('.error-line.file-upload').remove();
    var selection = jQuery(this)[0];
    var allowedFiles = ['jpg', 'JPG', 'JPEG', 'PNG', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'zip'];
    for (var i = 0; i < selection.files.length; i++) {
        var ext = selection.files[i].name.split('.').pop();
        //var name = selection.files[i].name;
        var size = selection.files[i].size;

        var insertAfter = '#Attachment';
        if (jQuery(this).parent().hasClass('fileUpload')) {
            var insertAfter = '#uploadFile';
        }
        if (jQuery(this).parent().parent().hasClass('bcp-flex')) {
            var insertAfter = '#error-msg-after-flex-div'; //to handle in case > attachment file upload
        }
        if (size > 2097152) {
            jQuery(this).val('');
            jQuery('<label class="error error-line file-upload">' + __('Maximum file upload size limit is 2 MB.', 'bcp_portal') + '</label>').insertAfter(insertAfter);
            return false;
        }
        if (allowedFiles.indexOf(ext) == '-1') {
            jQuery(this).val('');
            jQuery('<label class="error error-line file-upload">' + __('Only [jpg, jpeg, png, gif, doc, docx, pdf, txt, csv, xls, xlsx, zip] file formats allowed.', 'bcp_portal') + '</label>').insertAfter(insertAfter);
            return false;
        }
    }
    if (jQuery('#uploadFile').prop('tagName') == 'LABEL') {
        jQuery('#uploadFile').text(e.target.files[0].name); //to handle in case > attachment file upload
    } else {
        jQuery('#uploadFile').val(e.target.files[0].name);
    }
});

/* File Attachment change event @author Dhari Gajera */
jQuery('body').on('change', '.bcp-attachment', function (e) {
    jQuery('.error-line.file-upload').remove();
    var selection = jQuery(this)[0];
    var allowedFiles = ['jpg', 'JPG', 'JPEG', 'PNG', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'zip'];
    for (var i = 0; i < selection.files.length; i++) {
        var ext = selection.files[i].name.split('.').pop();
        var size = selection.files[i].size;

        //var insertAfter = '.bcp-attachment';
        var insertAfter = '#error-msg-after-flex-div';
        
        if (size > 2097152) {
            jQuery(this).val('');
            jQuery('<label class="error error-line file-upload">' + __('Maximum file upload size limit is 2 MB.', 'bcp_portal') + '</label>').insertAfter(insertAfter);
            return false;
        }
        if (allowedFiles.indexOf(ext) == '-1') {
            jQuery(this).val('');
            jQuery('<label class="error error-line file-upload">' + __('Only [jpg, jpeg, png, gif, doc, docx, pdf, txt, csv, xls, xlsx, zip] file formats allowed.', 'bcp_portal') + '</label>').insertAfter(insertAfter);
            return false;
        }
    }
});

jQuery(document).ready(function () {
    /* search selected open close */
    jQuery("#bcp-gsearch-selected-module").change(function () {
        jQuery("#width_tmp_option").html(jQuery('#bcp-gsearch-selected-module option:selected').text()); 
        jQuery(this).width(jQuery("#width_tmp_select").width());
    });

    /* search selected open close */
    jQuery('#bcp-gsearch-selected-module').on('blur', function () {
       jQuery(this).removeClass('gsearch-open');
    }).on('focus', function () {
      jQuery(this).addClass('gsearch-open');
    });
        
    /* pricebook_selection changes */
    jQuery(document).on('change', "select.pricebook_selection", function (event) {
        var selected_element = jQuery(this).val();
        var module_name = jQuery("input[name=module_name]").val();
        bcp_get_priceleval_product_list(module_name, selected_element, 1, true);
    });
    
    if (jQuery('.sfcp-datetime').length > 0) {
        jQuery('.sfcp-datetime').datetimepicker();
    }
    if (jQuery('.sfcp-time').length > 0) {
        jQuery('.sfcp-time').datetimepicker({datepicker: false, format: 'H:i', formatTime: 'H:i', });
    }
    if (jQuery('.sfcp-date').length > 0) {
        jQuery('.sfcp-date').datetimepicker({timepicker: false, format: 'Y/m/d', formatDate: 'Y/m/d'});
    }
    
});

/* select_all_product checkbox click event */
function bcp_handle_select_all_product_chk() {

    var all_chk = jQuery('.select-product-chk').length;
    var selected_chk = jQuery('.select-product-chk:checked').length;
    if (all_chk > 0 && all_chk == selected_chk) {
        jQuery('#select_all_product').prop('checked', true);
    } else {
        jQuery('#select_all_product').prop('checked', false);
    }

    if (all_chk > 0) {
        jQuery('#select_all_product').prop('disabled', false);
        bcp_handle_select_all_product_chk();
    } else {
        jQuery('#select_all_product').prop('disabled', true);
    }
}
/**
 * 
 * @param {type} price_book Pricebook ID
 * @param {type} paged Page number
 * @param {type} reset To Empty the list
 * @param {type} contain_products To Check existing products
 * @returns {undefined}
 */
function bcp_get_priceleval_product_list(module_name = 'order', price_book, paged = 1, reset = false, contain_products = false) {

    jQuery(".add-product-table-block").addClass('bcp-loading');
    var data = {
        'action': 'bcp_get_products',
        'module_name': module_name,
        'price_book': price_book,
        'page': paged,
        'contain_products': contain_products,
    };

    jQuery.post(ajaxurl, data, function (response) {
        var product_response = jQuery.parseJSON(response);
        jQuery(".add-product-table-block").removeClass('bcp-loading');
        
        if (product_response.status) {
            if (reset === true) {
                jQuery('tbody#bcp_get_priceleval_product_list').empty();
            }
            jQuery("#select_all_product").removeAttr("disabled");
            jQuery('tbody#bcp_get_priceleval_product_list').append(product_response.productlist_rows);
            
            if (jQuery("#select_all_product").is(":checked")) {
                jQuery('.select-product-chk').prop('checked', true);
            }
            if (product_response.next_page && product_response.show_load_more) {                
                jQuery("#bcp_product_load_more").html('<a href="javascript:void(0);" data-current_page =' + product_response.next_page + ' class="button load_more">' + __('Load More', 'bcp_portal') + '</a>');
            } else {
                jQuery("#bcp_product_load_more").html('');
                if (jQuery('.select-product-chk:checked').length == jQuery('.select-product-chk').length) {
                    jQuery("#select_all_product").prop('checked', true);
                }
            }
            
        } else {
            jQuery('tbody#bcp_get_priceleval_product_list').empty();
            jQuery("#bcp_product_load_more").html();
            jQuery("#select_all_product").attr("disabled","disabled");
        }
    });

}

jQuery(document).on('click', '.select-product-chk', function () {
 
    if (jQuery(this).prop('checked') == true) {
        jQuery(this).parent().parent().addClass('checked');
        jQuery(this).parent().parent().find('.bcp_product_select_qty .product_qty').prop('disabled', false);
     
    } else {
        jQuery(this).parent().parent().removeClass('checked');
        jQuery('#select_all_product').prop('checked', false);
        jQuery(this).parent().parent().find('.bcp_product_select_qty .product_qty').prop('disabled', true);
  
    }

    var all_chk = jQuery('.select-product-chk').length;
    var selected_chk = jQuery('.select-product-chk:checked').length;
    if (all_chk > 0 && all_chk == selected_chk) {
        jQuery('#select_all_product').prop('checked', true);
       
    }
    
    if(selected_chk > 0){
        jQuery('.module-createby-product').show();
    }else{
        jQuery('.module-createby-product').hide();
    }
});

/* select_all_product click event */
jQuery(document).on('click', '#select_all_product', function () {

    jQuery('.select-product-chk').prop('checked', jQuery(this).prop('checked'));
    if (jQuery(this).prop('checked') == true) {
        jQuery('.select-product-chk').parent().parent().addClass('checked');
        jQuery('.product_qty').prop('disabled', false);
        jQuery('.module-createby-product').show();
    } else {
        jQuery('.select-product-chk').parent().parent().removeClass('checked');
        jQuery('.product_qty').prop('disabled', true);
        jQuery('.module-createby-product').hide();
    }
});

/* product_qty click event */
jQuery(document).on('change', '.bcp_product_select_qty .product_qty', function () {

    var total_element = jQuery(this).parent().parent().find('.bcp_product_total_price');
    var qty = jQuery(this).val();
    var price = total_element.attr('data-amount');
    var symbol = total_element.attr('data-symbol');

    if (qty < 1 || qty == '') {
        jQuery(this).val(1);
        qty = 1;
    }

    var total = parseFloat(price) * parseFloat(qty);
    total = total.toFixed(2);
    total = total.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    total = symbol + ' ' +total;
    total_element.text(total);
});

jQuery(document).on('click', "#bcp_product_load_more a.load_more", function () {
    var current_page = jQuery(this).attr('data-current_page');
    var module_name = jQuery("input[name=module_name]").val(); 
    var pricebook = jQuery("select.pricebook_selection option:selected").val();
    bcp_get_priceleval_product_list(module_name, pricebook, current_page, false);

});

/* multi-dropdown click event */
jQuery(document).on('click', ".multi-select-title", function () {
    jQuery(".multi-select-dropdown").slideUp();
    if (jQuery(this).parent().find('.multi-select-dropdown').is(":hidden")) {
        jQuery(this).parent().find('.multi-select-dropdown').slideDown();
    }
});

/* multi-dropdown click event */
jQuery(document).on('click', ".multi-select-dropdown label", function () {
    var dp_count = 0;
    if (jQuery(this).parent().parent().parent().prev().find("label").find("span").length > 0) {
        dp_count = jQuery(this).parent().parent().parent().prev().find("label").find("span").text();
    }

    if (jQuery(this).prev().is(":checked")) {
        dp_count--;
        if (dp_count == 0) {
            jQuery(this).parent().parent().parent().prev().find("label").text("Select");
        } else {
            jQuery(this).parent().parent().parent().prev().find("label").html("<span>" + dp_count + "</span> Selected");
        }
    } else {
        var selectionlimit = jQuery(this).closest('ul').attr("selectionlimit");
        if ((jQuery(this).closest('ul').find('input.sfcp-multi-dropdown:checked').length >= selectionlimit) && selectionlimit != 0) {
            alert('You can select maximum ' + selectionlimit + ' options.');
            return false;
        } else {
            dp_count++;
            jQuery(this).parent().parent().parent().prev().find("label").html("<span>" + dp_count + "</span> Selected");
        }
    }
    if (dp_count == 0) {
        jQuery(this).parent().parent().parent().next().val('');
    } else {
        jQuery(this).parent().parent().parent().next().val(dp_count);
    }
});


jQuery(document).on("click", function (event) {
    var $trigger = jQuery(".multi-select1");
    if ($trigger !== event.target && !$trigger.has(event.target).length) {
        jQuery(".multi-select-dropdown").slideUp("fast");
    }
});

/* multi-dropdown click event */
jQuery(document).on('click', '.sfcp-multi-dropdown', function () {
    var count = 0;
    var val = [];
    jQuery(this).closest('ul').find('input.sfcp-multi-dropdown').each(function () {
        if (jQuery(this).prop("checked")) {
            val.push(jQuery(this).val());
            count++;
        }
    });
    
    if (count < 4 && count != 0) {
        jQuery(this).parents('.multi-select-dropdown').prev().find("label").html(val.join());
    } else if (count == 0) {
        jQuery(this).parents('.multi-select-dropdown').prev().find("label").html("Select");
    } else {
        jQuery(this).parents('.multi-select-dropdown').prev().find("label").html("<span>" + count + "</span> Selected");
    }
    
});

jQuery(document).ready(function () {
    /* detail page first subpanel trigger event */
    if (jQuery('body').find('.tab-details-1 ul.tabs li.tab-link').hasClass("current")) {
        jQuery('body').find('.tab-details-1 ul.tabs li.tab-link.current').trigger('click');
    }

    /* note validation */
    if (jQuery('#sfcp-case-notes-form').length > 0) {
        jQuery("#sfcp-case-notes-form").validate();
        jQuery.extend(jQuery.validator.messages, {
            required: __("This field is required.", 'bcp_portal')
        });
    }

    /* case comment validation */
    if (jQuery('#sfcp-case-comment-form').length > 0) {
        jQuery("#sfcp-case-comment-form").validate();
        jQuery.extend(jQuery.validator.messages, {
            required: __("This field is required.", 'bcp_portal')
        });
    }

    /* attachment validation */
    if (jQuery('#sfcp-attachment-form').length > 0) {
        jQuery("#sfcp-attachment-form").validate({
            errorPlacement: function (error, element) {
                if (element.attr("name") == "Attachment")
                    error.insertAfter("#error-msg-after-flex-div"); //to solve design break in flex
                else
                    error.insertAfter(element);
            }
        });
        jQuery.extend(jQuery.validator.messages, {
            required: __("This field is required.", 'bcp_portal')
        });
    }


    /* required_product validation */
    if (jQuery('.bcp-product').length > 0) {
        jQuery.validator.addMethod("required_product", function (value, elem, param) {
            jQuery("#err-msg").hide();
            if (jQuery(".bcp-product:checkbox:checked").length == 0) {
                jQuery("#err-msg").show();
            }
            return jQuery(".bcp-product:checkbox:checked").length > 0;
        }, __("You must select at least one product from list.", 'bcp_portal'));

        jQuery.validator.addClassRules("bcp-product", {
            required_product: true,
        });
    }

    /* validation form */
    jQuery(".sfcp-requiredfield").each(function () {
        var name = jQuery(this).attr("name");
        var validationMessage = jQuery(this).attr("validationMessage");
        if (typeof validationMessage !== typeof undefined && validationMessage !== false) {
            var msg = '<label id="' + name + '-error" class="error" for="' + name + '">' + validationMessage + '</label>';
            jQuery(this).rules("add", {
                messages: {
                    required: msg
                },
            });
        }
    });

    jQuery(".multi-select-dropdown").each(function () {
        var name = jQuery(this).attr("name");
        var msg = '<label id="' + name + '-error" class="error" for="' + name + '">This field is required.</label>';
        //jQuery(this).next().rules("add", {
        jQuery(this).rules("add", {
            messages: {
                required: msg
            },
        });
    });
    if (jQuery('body').find('.custom-select.sfcp-picklist, .sfcp-multipicklist').hasClass("sfcp-catagotyfield")) {
        jQuery('body').find('.custom-select.sfcp-picklist.sfcp-catagotyfield, .sfcp-multipicklist.sfcp-catagotyfield').trigger('change');
    }

    if (jQuery('.sfcp-datetime').length > 0) {
        jQuery('.sfcp-datetime').datetimepicker();
    }
    if (jQuery('.sfcp-time').length > 0) {
        jQuery('.sfcp-time').datetimepicker({datepicker: false, format: 'H:i', formatTime: 'H:i', });
    }
    if (jQuery('.sfcp-date').length > 0) {
        jQuery('.sfcp-date').datetimepicker({timepicker: false, format: 'Y/m/d', formatDate: 'Y/m/d'});
    }

    if (jQuery('.sfcp-password').length > 0) {
        jQuery.validator.addMethod("check_pwd", function (value, element) {
            var password = value;
            if (password.match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[`~!@#$%^&*()\-_=+[\]{}|\\;:'",./<>?])/)) {
                return true;
            } else {
                return false;
            }
        }, __("Your password must contain at least one uppercase letter, one lowercase letter, one special character and one numeric.", 'bcp_portal'));

        jQuery.validator.addClassRules("sfcp-password", {
            minlength: 8,
            check_pwd: true,
        });
    }

    if (jQuery('.sfcp-phone').length > 0) {
        jQuery.validator.addMethod("check_phone", function (value, element) {
            var phone = value;
            if (phone.match(/\D*?(\d\D*?){10}/) || phone == "") {
                return true;
            } else {
                return false;
            }
        }, __("Please enter a valid number.", 'bcp_portal'));

        jQuery.validator.addClassRules("sfcp-phone", {
            check_phone: true,
        });
    }

    if (jQuery('.sfcp-double').length > 0) {
        jQuery.validator.addMethod("check_number", function (value, element) {
            var sfcp_number = value;
            if (sfcp_number.match(/^(\d*\.)?\d+$/) || sfcp_number == "") {
                return true;
            } else {
                return false;
            }
        }, __("Please enter a valid number.", 'bcp_portal'));

        jQuery.validator.addClassRules("sfcp-double", {
            check_number: true,
        });
    }

    if (jQuery('.sfcp-requiredfield, .required_multi_select').length > 0) {
        jQuery.validator.addMethod("check_blank", function (value, element) {
            return value == '' || value.trim().length != 0;
        }, __("This field is required.", 'bcp_portal'));

        jQuery.validator.addClassRules("sfcp-requiredfield", {
            check_blank: true,
        });

        jQuery.validator.addClassRules("required_multi_select", {
            check_blank: true,
        });
    }

    if (jQuery('.module-form').length > 0) {
        jQuery('.module-form').validate({
            errorPlacement: function (error, element) {
                if (element.attr("name") == "selected-product[]") {
                    error.appendTo("#err-msg");
                } else {
                    error.insertAfter(element);
                }
            }
        });
    }
    if (jQuery('.sfcp-requiredfield').length > 0) {
        jQuery.extend(jQuery.validator.messages, {
            required: __("This field is required.", 'bcp_portal'),
            remote: __("Please fix this field.", 'bcp_portal'),
            email: __("Please enter a valid email address.", 'bcp_portal'),
            url: __("Please enter a valid URL.", 'bcp_portal'),
            date: __("Please enter a valid date.", 'bcp_portal'),
            dateISO: __("Please enter a valid date (ISO).", 'bcp_portal'),
            number: __("Please enter a valid number.", 'bcp_portal'),
            digits: __("Please enter only digits.", 'bcp_portal'),
            check_number: __("Please enter a valid number.", 'bcp_portal'),
            check_phone: __("Please enter a valid number.", 'bcp_portal'),
            equalTo: __("Please enter the same value again.", 'bcp_portal')
        });
    }
    
   /* if (jQuery('.login-error').length > 0 || jQuery('.login_error').length > 0) {
        setInterval(function(){
            jQuery( '.login-error, .login_error' ).html("");
            jQuery( '.login-error, .login_error' ).hide();
        }, 5000);   
    }

    if (jQuery('.login-sucess').length > 0) {
        setInterval(function(){
            jQuery( '.login-sucess' ).html("");
            jQuery( '.login-sucess' ).hide();
        }, 5000);   
    }*/

});

/* datetimepicker formate */
function bcp_datetimepicker_formate() {
    if (jQuery('.sfcp-datetime').length > 0) {
        jQuery('.sfcp-datetime').datetimepicker();
    }
    if (jQuery('.sfcp-time').length > 0) {
        jQuery('.sfcp-time').datetimepicker({datepicker: false, format: 'H:i', formatTime: 'H:i', });
    }
    if (jQuery('.sfcp-date').length > 0) {
        jQuery('.sfcp-date').datetimepicker({timepicker: false, format: 'Y/m/d', formatDate: 'Y/m/d'});
    }
}
$("body").on("click", ".close-msg", function() {
    $(this).parent().animate({opacity: 0}, 500).hide('slow');
});

