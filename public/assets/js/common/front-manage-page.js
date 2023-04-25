var ajaxurl = my_ajax_object.ajaxurl;
var homeurl = my_ajax_object.homeurl;
var wpml_current_lang = my_ajax_object.bcp_current_lang;
const {__, _x, _n, sprintf} = wp.i18n;

/* list page  */
function bcp_list_module_portal(module_name, pagination, view_style, search = '', where_condition = '', view = '', relate_id = '', relate_module = '', relationship = '', responsediv = '',record_type = '') {
    var browser_height = jQuery(window).height();
    var div_top = jQuery('#responsedataPortal').offset().top;
    if (responsediv) {
        jQuery('#' + responsediv).addClass('bcp-loading');
    } else {
        jQuery('#responsedataPortal').css({'min-height': browser_height - div_top});
        jQuery('#responsedataPortal').addClass('bcp-loading');
    }

    var data = {
        'action': 'bcp_list_portal',
        'module_name': module_name,
        'limit': pagination,
        'view_style': view_style
    };
    if (search != '') {
        data.search = search;
    }
    if (where_condition != '') {
        data.where_condition = where_condition;
    }
    if (wpml_current_lang) {
        data.lang = wpml_current_lang;
    }
    if (relationship != '') {
        data.relationship = relationship;
    }
    if (view != '') {
        data.view = view;
    }
    if (relate_id != '') {
        data.relate_id = relate_id;
    }
    if (relate_module != '') {
        data.relate_module = relate_module;
    }

    if(record_type != ""){
        data.selectRecordType = record_type;
    }

    jQuery.post(ajaxurl, data, function (response) {

        if (responsediv) {
            jQuery('#' + responsediv).html(response);
          
            jQuery('#' + responsediv).removeClass('bcp-loading');
        } else {
            jQuery('#responsedataPortal').html(response);
            if (jQuery('.success').length > 0) {
                /*setInterval(function () {
                    jQuery('.success').html("");
                    jQuery('.success').hide();
                }, 5000);*/
            }
            jQuery('#responsedataPortal').removeClass('bcp-loading');
        }
        deleteEmptyCellAction();
        bcp_filter_js();
    });
}

/* add/edit page  */
function bcp_edit_module_portal(module_name, module_action, record_id, view_style, relate_module_name = '', relationship = '', relate_module_id = '') {
    var browser_height = jQuery(window).height();
    var div_top = jQuery('#responsedataPortal').offset().top;
    jQuery('#responsedataPortal').css({'min-height': browser_height - div_top});

    if (module_action) {
        var data = {
            'action': 'bcp_' + module_action + '_portal',
            'module_name': module_name,
            'module_action': module_action,
            'record_id': record_id,
            'view_style': view_style,
        };

        if (relationship != '') {
            data.relationship = relationship;
        }
        if (relate_module_name != '') {
            data.relate_module_name = relate_module_name;
        }
        if (relate_module_id != '') {
            data.relate_module_id = relate_module_id;
        }

        if (wpml_current_lang) {
            data.lang = wpml_current_lang;
        }
        jQuery('#responsedataPortal').addClass('bcp-loading');

        jQuery.post(ajaxurl, data, function (response) {
            jQuery('#responsedataPortal').html(response);
            jQuery('#responsedataPortal').removeClass('bcp-loading');
            bcp_datetimepicker_formate();
            jQuery('.module-form').validate();
        });
    }
}



/* detail page get data */
function bcp_detail_module_portal(module_name, module_action, record_id, view_style, submodule, subtab = "") {

    var browser_height = jQuery(window).height();
    var div_top = jQuery('#responsedataPortal').offset().top;
    jQuery('#responsedataPortal').css({'min-height': browser_height - div_top});


    var tmp_module = module_name;

    if (module_action) {
        var data = {
            'action': 'bcp_' + module_action + '_portal',
            'view_style': view_style,
            'module_name': module_name
        };

        if (record_id && module_action == 'detail') {
            data.record_id = record_id;
        }
        if (submodule != '') {
            data.submodule = submodule;
        }
        if (subtab != '') {
            data.subtab = subtab;
        }

        if (tmp_module == "solutions") {
            tmp_module = "case";
        }
        if (wpml_current_lang) {
            data.lang = wpml_current_lang;
        }

        jQuery('#responsedataPortal').addClass('bcp-loading');
        jQuery.post(ajaxurl, data, function (response) {
            jQuery('#responsedataPortal').html(response);
            if (jQuery('.success').length > 0) {
               /* setInterval(function () {
                    jQuery('.success').html("");
                    jQuery('.success').hide();
                }, 5000);*/
            }
            jQuery('#responsedataPortal').removeClass('bcp-loading');
            if (jQuery('#responsedataSubpanel').length > 0 && jQuery('#responsedataSubpanel').html() == "") {
                jQuery(".subpaneltab.current").click();
            }
        });
    }
}

var temp_view = '';
jQuery(document).ready(function ($) {

    /* bcp-action page  */
    $('#responsedataPortal').on('click', '.bcp-action', function (e) {
        if ($(e.target).is("select") || $(e.target).is("option")) {
            return false;
        }
        var status = "";
        var page_created = false;
        if ($('#caseStatusBox').length > 0) {
            if ($('#caseStatusBox').val() != 'All') {
                status = $('#caseStatusBox').val();
            }
        }
        var module_name = $(this).attr('module-name');
        var view = $(this).attr('module-action');
        var paged = $(this).attr('paged');
        var order_by = $(this).attr('order-by');
        var search = $(this).attr('search');
        var record_id = $(this).attr('record-id');
        var url = $('.module-url').val();
        var view_style = $('.view_style').val();
        var relationship = $('.relationship').val();
        var relate_module = $('.relate_module').val();
        var relate_id = $('.relate_id').val();
        var limit = '';
        if (jQuery(".limit").length) {
            limit = jQuery(".limit").val();
        }
        if (view) {
            temp_view = view;
            if (view == 'delete') {
                if (module_name == "account") {
                    $('#responsedataPortal').html("<span class='error error-line error-msg settings-error'> <?php _e( 'You don\'t have sufficient permission to perform this action.' ); ?> </span>");
                    return false;
                }
                if (confirm(__('Are you sure you want to delete this record?', 'bcp_portal'))) {
                    //$(this).closest('tr').fadeOut('slow');
                } else {
                    return false;
                }
            }
            if (view == 'subpanel') {
                view = 'list';
            }
            var eloader = $('#responsedataPortal');
            var data = {
                'action': 'bcp_' + view + '_portal',
                'status': status,
                'module_name': module_name,
                'view_style': view_style
            };
            if (module_name == 'solutions') {
                data.action = "bcp_solutions";
                data.view = 'list';
                if (jQuery('#solutionlist-items').length > 0) {
                    data.case_id = jQuery('#solutionlist-items').attr('data-id');
                }
                data.search_term = search;
                eloader = $("#solutionlist-items");
            }

            if (module_name == "KBDocuments") {
                data.action = "bcp_KBDocuments";
            }

            if (module_name == "KBDocuments") {
                data.action = "bcp_KBDocuments";
            }

            data.paged = paged;
            data.order_by = order_by;
            data.search = search;
            data.record_id = record_id;
            if (view == 'list' && module_name != 'solutions' && module_name != 'KBDocuments') {
                data.limit = limit;
                if (jQuery('#filter_current_where').length != 0) {
                    data.where_condition = jQuery('#filter_current_where').val();
                }
            }

            if (module_name == 'KBDocuments' || module_name == 'solutions') {
                data.limit = limit;
            }

            if (wpml_current_lang) {
                data.lang = wpml_current_lang;
            }
            if (temp_view == 'subpanel') {
                data.view = temp_view;
            }
            if (relationship) {
                data.relationship = relationship;
            }
            if (relate_id) {
                data.relate_id = relate_id;
            }
            if (relate_module) {
                data.relate_module = relate_module;
            }

            eloader.addClass('bcp-loading');
            $.post(ajaxurl, data, function (response) {
                if (temp_view == 'delete') {
                    if (response == '-1') {
                        $('#responsedataPortal').html("<span class='error error-line error-msg settings-error'>" + __('You don\'t have sufficient permission to access this data.', 'bcp_portal') + "</span>");
                    } else {
                        var response = jQuery.parseJSON(response);
                        if (response['success']) {
                            eloader.addClass('bcp-loading');
                            if (response['relate_module'] != '') {
                                url = '';
                                page_created = checkPortalPageCreated('bcp_detail', response['relate_module']);
                                if (page_created.page_exists && page_created.page_url) {
                                    url = page_created.page_url + response['relate_id'];
                                }
                                if (url == '') {
                                    url = homeurl + 'customer-portal/' + response['relate_module'] + '/detail/' + response['relate_id'];
                                }
                            } else {
                                page_created = checkPortalPageCreated('bcp_list', module_name);
                                if (page_created.page_exists && page_created.page_url) {
                                    url = page_created.page_url;
                                }
                                if (typeof url === 'undefined') {
                                    url = homeurl + 'customer-portal/' + module_name + '/list/';
                                }
                            }
                            window.location.assign(url);
                        } else {
                            $('.sfcp-validation').html('<div class="login_error"><span class="error">' + response['message'] + '</span></div>');
                        }
                    }
                } else {
                    if (module_name == 'solutions') {
                        $("#solutionlist-items").html(response);
                    } else {
                        if (temp_view == 'subpanel') {
                            $('#responsedataSubpanel').html(response);
                        } else {
                            $('#responsedataPortal').html(response);
                        }
                    }
                }

                eloader.removeClass('bcp-loading');
            });
        }
    });

    /* case close action  */
    $('#responsedataPortal').on('click', '.case-close-action', function (e) {
        var view = $(this).attr('module-action');
        var record_id = $(this).attr('record-id');

        if (view) {
            if (view == 'close') {
                if (confirm(__('Are you sure you want to close this record?', 'bcp_portal'))) {
                } else {
                    return false;
                }
            }
            if (view == 're-open') {
                if (confirm(__('Are you sure you want to re-open this record?', 'bcp_portal'))) {
                } else {
                    return false;
                }
            }
            var data = {
                'action': 'bcp_case_close',
                'view': view,
                'record_id': record_id,
            };
            if (wpml_current_lang) {
                data.lang = wpml_current_lang;
            }
            $('#responsedataPortal').addClass('bcp-loading');
            $.post(ajaxurl, data, function (response) {
                $('#responsedataPortal').removeClass('bcp-loading');
                if (response) {
                    location.reload(true);
                }
            });
        }
    });


    // submit login page
    $(document).on('submit', '#scp-login-form', function (e) {
        e.preventDefault();
        if (jQuery('#scp-recaptcha').length > 0 && jQuery('#scp-recaptcha').html() != "") {
            jQuery('#sfcp-recaptcha-error-msg').hide();
            var msg = grecaptcha.getResponse(widget1);
            if (jQuery.trim(msg) == "") {
                jQuery('#sfcp-recaptcha-error-msg').show();
                return false;
            }
        }
        jQuery('.bcp-part-login').addClass('bcp-loading');
        var data = new FormData(jQuery(this)[0]);
        if (wpml_current_lang) {
            data.append('lang', wpml_current_lang)
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
                    var res = JSON.parse(response);
                    window.location.href = res.url;
                    jQuery('.bcp-part-login').removeClass('bcp-loading');
                }
            });
        }, 1000);
    });

    // submit forgot password page
    $(document).on('submit', '.forgot-password-form', function (e) {
        e.preventDefault();
        if (jQuery('#scp-recaptcha').length > 0 && jQuery('#scp-recaptcha').html() != "") {
            jQuery('#sfcp-recaptcha-error-msg').hide();
            var msg = grecaptcha.getResponse(widget1);
            if (jQuery.trim(msg) == "") {
                jQuery('#sfcp-recaptcha-error-msg').show();
                return false;
            }
        }
        jQuery('.forgot-password-form').addClass('bcp-loading');
        var data = new FormData(jQuery(this)[0]);

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
                    var res = JSON.parse(response);
                    window.location.href = res.url;
                    jQuery('.forgot-password-form').removeClass('bcp-loading');
                }
            });
        }, 1000);
    });


    // submit reset password page
    $(document).on('submit', '.reset-password-form', function (e) {
        e.preventDefault();

        jQuery('.reset-password-form').addClass('bcp-loading');
        var data = new FormData(jQuery(this)[0]);

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
                    var res = JSON.parse(response);
                    window.location.href = res.url;
                    jQuery('.reset-password-form').removeClass('bcp-loading');
                }
            });
        }, 1000);
    });

    // submit resend otp
    $(document).on('click', '.resend-otp', function (e) {
        e.preventDefault();

        jQuery('#scp-login-form').addClass('bcp-loading');
        var data = {
            'action': 'bcp_resend_otp',
        };

        setTimeout(function () {
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
                async: false,
                beforeSend: function () {
                },
                success: function (response) {
                    var res = JSON.parse(response);
                    window.location.href = res.url;
                    jQuery('scp-login-form').removeClass('bcp-loading');
                }
            });
        }, 1000);
    });

    // submit verify otp
    $(document).on('submit', '#scp-verify-otp', function (e) {
        e.preventDefault();

        jQuery('.bcp-part-login').addClass('bcp-loading');
        var data = new FormData(jQuery(this)[0]);

        setTimeout(function () {
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    var res = JSON.parse(response);
                    window.location.href = res.url;
                    jQuery('.bcp-part-login').removeClass('bcp-loading');
                }
            });
        }, 1000);
    });

    /* show hide field action  */
    $('#responsedataPortal').on('change', '.sfcp-catagotyfield', function (e) {
        var module_name = jQuery(this).attr('module_name');
        var form_type = jQuery(this).attr('form-type');
        var selectvalue = '';
        var fieldname = '';

        if (form_type == 'multi-select') {
            fieldname = jQuery(this).attr('field-name');
        } else {
            fieldname = jQuery(this).attr('id');
        }

        if (form_type == 'multi-select') {
            var val = [];
            $(this).closest('ul').parent().find('.sfcp-multi-dropdown:checked').each(function (i) {
                val[i] = $(this).val();
            });
            selectvalue = val;
        } else if (form_type == 'checkbox') {
            if ($(this).prop("checked") == true) {
                selectvalue = 1;
            } else if ($(this).prop("checked") == false) {
                selectvalue = 0;
            }
        } else {
            selectvalue = jQuery(this).val();
        }

        var data = {
            'action': 'bcp_show_hide_fields',
            'selectvalue': selectvalue,
            'module_name': module_name,
            'fieldname': fieldname,
        };
        if (wpml_current_lang) {
            data.lang = wpml_current_lang;
        }
        if (form_type == 'multi-select') {
            data.type = "multi-select";
        }
        $('#responsedataPortal').addClass('bcp-loading');
        $.post(ajaxurl, data, function (datas) {
            var response = jQuery.parseJSON(datas);

            $.each(response, function (i, member) {
                if (member.Visibility == 'show') {
                    jQuery(".bcp-form-group").find('#' + member.FieldName).closest('.bcp-col-lg-6, .bcp-col-lg-4').show();
                } else if (member.Visibility == 'hide') {
                    jQuery(".bcp-form-group").find('#' + member.FieldName).closest('.bcp-col-lg-6, .bcp-col-lg-4').hide();
                }
            });
            jQuery('.multi-select-dropdown').hide();
            $('#responsedataPortal').removeClass('bcp-loading');
        });
    });
});


/* gloabal search action  */
function bcp_search_gloabal() {
    var search_term, redirect_url, module_name, page_created;
    search_term = document.getElementById('g_search_name').value.trim();
    if (search_term != '' && search_term.length >= 3) {

        module_name = jQuery('#bcp-gsearch-selected-module').val();
        if (module_name == '0') {
            redirect_url = homeurl + 'customer-portal/searchresult/' + search_term + '/';
        } else {
            redirect_url = homeurl + 'customer-portal/' + module_name + '/list/' + search_term + '/';
            page_created = checkPortalPageCreated('bcp_list', module_name);
            if (page_created.page_exists && page_created.page_url) {
                redirect_url = page_created.page_url + '/' + search_term;
            }
        }
        if (wpml_current_lang != undefined) {
            redirect_url = wpml_get_filtered_url(redirect_url);
        }
        window.location.href = redirect_url;
    } else {
        window.alert(__('Enter atleast 3 characters to start search.', 'bcp_portal'));
        document.getElementById('g_search_name').style.border = '1px solid red';
        if (search_term.length == '') {
            document.getElementById('g_search_name').value = '';
        }
    }

    return false;
}


/* gloabal search action  */
jQuery(document).on('click', '#btn_global_search', function () {
    bcp_search_gloabal();
});

/* search clear action  */
jQuery(document).on('click', '#btn_clear', function () {
    var search_term = '', redirect_url, module_name;
    if (jQuery('#g_search_name.active').length) {
        search_term = jQuery('#g_search_name.active').val();
    } else {
        search_term = jQuery('.module_search_name.active').val();
    }

    if (search_term != '' && search_term != undefined) {
        module_name = jQuery('#btn_clear').attr('module-name');
        if (module_name == '0') {
            var current_url = jQuery('.current-url').val();
            redirect_url = current_url;
        } else {
            var searchurl = jQuery('.search-url').val();
            redirect_url = homeurl + 'customer-portal/' + module_name + '/list/';
            if (searchurl != '' && searchurl != undefined) {
                redirect_url = searchurl;
            }
        }
        if (wpml_current_lang != undefined) {
            redirect_url = wpml_get_filtered_url(redirect_url);
        }
        window.location.href = redirect_url;
    } else {
        if (jQuery('#g_search_name').length) {
            jQuery('#g_search_name').val('');
        } else {
            jQuery('.module_search_name').val('');
        }
    }

    return false;
});
jQuery(document).on('keypress', '#g_search_name', function (e) {
    if (e.which == 13) {
        bcp_search_gloabal();
    }
});


/* module wise search action  */
function bcp_search_in_module(el_search_input) {
    var search_term, redirect_url, module_name;

    module_name = el_search_input.attr('module-name');
    search_term = el_search_input.val();

    if (search_term != '' && search_term != undefined) {
        if (search_term.length >= 3) {
            var searchurl = jQuery('.search-url').val();
            if (module_name == '0' || module_name == '') {
                redirect_url = homeurl + 'customer-portal/searchresult/' + search_term + '/';
            } else {
                redirect_url = homeurl + 'customer-portal/' + module_name + '/list/' + search_term + '/';
                if (searchurl != '' && searchurl != undefined) {
                    redirect_url = searchurl + '/' + search_term;
                }
            }
            if (wpml_current_lang != undefined) {
                redirect_url = wpml_get_filtered_url(redirect_url);
            }
            window.location.href = redirect_url;
        } else {
            window.alert(__('Enter atleast 3 characters to start search.', 'bcp_portal'));
            if (search_term.length != '') {
                el_search_input.value = '';
            }
        }
    } else {
        window.alert(__('Enter atleast 3 characters to start search.', 'bcp_portal'));
    }

    return false;
}

/* remove block action  */
function bcp_remove_block_portal(current_class) {
    jQuery('.' + current_class).parent().remove();
}

/* content read more action  */
function read_more_less(param) {

    var moretext, lesstext;
    moretext = jQuery('.bcp_crm_read_more_text').val();
    lesstext = jQuery('.bcp_crm_read_less_text').val();

    if (jQuery(param).hasClass("less")) {

        jQuery(param).removeClass("less");
        jQuery(param).html(moretext);
        jQuery(param).attr({
            'title': moretext
        });
    } else {

        jQuery(param).addClass("less");
        jQuery(param).html(lesstext);
        jQuery(param).attr({
            'title': lesstext
        });
    }

    jQuery(param).parent().prev().toggle();
    jQuery(param).prev().toggle();
}

/* module wise search action  */
jQuery(document).on('click', '.listing-search .module_search_btn', function () {
    var el_search_input = jQuery(this).parent().children('.module_search_name');
    bcp_search_in_module(el_search_input);
});
jQuery(document).on('keypress', '.module_search_name', function (e) {
    if (e.which == 13) {
        var el_search_input = jQuery(this);
        bcp_search_in_module(el_search_input);
    }
});

//handle submenu in standalone
jQuery(document).ready(function () {

    //removing logout & profile from stanalone menu
    jQuery('ul.crm-standalone-menu li > a').each(function () {
        var this_url = jQuery(this).attr('href');
        var this_item_class = jQuery(this).attr('class');
        if (this_url.includes('portal-profile') || this_url.includes('bcp-logout')) {
            if (this_item_class && this_item_class.includes('wpml-menu-link')) {
            } else {
                jQuery(this).parent().remove();
            }
        }
    });

    //removing empty ul.sub-menu.dropdown
    jQuery('ul.crm-standalone-menu ul.sub-menu').each(function () {
        var this_childs = jQuery(this).children();
        if (this_childs.length < 1) {
            jQuery(this).parent().removeClass('menu-item-has-children');
            jQuery(this).parent().removeClass('dropdown-li');
            jQuery(this).parent().children('.toggle-submenu').remove();
            jQuery(this).remove();
        }
    });

    //removing menu item with only '#' (only from main row of menu)
    jQuery('ul.crm-standalone-menu > li > a').each(function () {
        var this_url = jQuery(this).attr('href');
        if (this_url == '#') {
            var this_childs = jQuery(this).parent().children('ul.sub-menu');
            if (this_childs.length < 1) {
                jQuery(this).parent().remove();
            }
        }
    });

    /* menu item add class dropdown  */
    jQuery('ul.crm-standalone-menu .menu-item-has-children').addClass('dropdown-li');
    jQuery('ul.crm-standalone-menu .sub-menu').addClass('dropdown');

    jQuery('ul.crm-standalone-menu .menu-item-has-children > a').each(function () {
        jQuery(this).after('<span class="lnr lnr-chevron-down toggle-submenu"></span>');
    });

    /* menu item add class active  */
    jQuery('ul .menu-item-has-children > .toggle-submenu').click(function () {
        jQuery(this).parent().toggleClass('active');
    });

    /* menu item remove class active  */
    jQuery('ul .menu-item-has-children > .toggle-submenu').focusout(function () {
        jQuery(this).parent().removeClass('active');
    });

    jQuery('.mobile-menu .lnr-menu').click(function () {
        jQuery('.header-content nav').toggleClass('active');
        jQuery(this).toggleClass('close');
    });
});

/* equal height function  */
jQuery(window).on('load', function () {
    equal_height_row();
});

jQuery(window).resize(function () {
    equal_height_row();
});

function equal_height_row() {
    if (jQuery('.wp-block-column .dcp-content-table').length > 0) {
        jQuery('.wp-block-column .dcp-content-table').matchHeight();
    }
    if (jQuery('.wp-block-column .number-incident-block').length > 0) {
        jQuery('.wp-block-column .number-incident-block').matchHeight();
    }
    if (jQuery('.wp-block-column .card-body h2').length > 0) {
        jQuery('.wp-block-column .card-body h2').matchHeight();
    }
    if (jQuery('.wp-block-columns .wp-block-column .bcp-block-div').length > 0) {
        jQuery('.wp-block-columns .wp-block-column .bcp-block-div').matchHeight();
    }
    if (jQuery('.wp-block-column .card-body a.view-more').length > 0) {
        jQuery('.wp-block-column .card-body a.view-more').matchHeight();
    }
}

jQuery(document).ready(function () {
    /* subpanel click event  */
    jQuery('body').on('click', '.tab-details-1 ul.tabs li.tab-link', function (e) {
        var tab_id = jQuery(this).attr('data-tab');

        jQuery('.tab-details-1 ul.tabs li').removeClass('current');
        jQuery('.tab-details-1  .tab-content').removeClass('current');
        jQuery(this).addClass('current');

        if (jQuery(this).parent().parent().hasClass('dropdown sub-menu-div')) {
            jQuery(this).parent().parent().addClass('current');
        }
        if (jQuery(this).hasClass("subpaneltab")) {
            jQuery('.subpanelpart').addClass('current');
            var module_name = jQuery(this).attr("data-module");
            var relationship = jQuery(this).attr("data-relationship");
            var relate_id = jQuery(this).attr("record-id");
            var relate_module = jQuery(this).attr("data-parent-module");
            var style = jQuery(this).attr("data-style");
            var responsediv = jQuery(this).attr("data-response");
            bcp_list_module_portal(module_name, '5', style, '', '', 'subpanel', relate_id, relate_module, relationship, responsediv);
        } else {
            jQuery("#" + tab_id).addClass('current');
        }
    });

    /* block columns count  */
    jQuery('.wp-block-columns').each(function (index, object) {
        var length = jQuery(object).children('.wp-block-column').find('.number-incident-block').length;
        if (length) {
            var count = jQuery(object).children('.wp-block-column').length;
            if (count == '2' || count == '4') {
                jQuery(this).addClass('bcp-custom-block-2');
            } else if (count == '3' || count == '5' || count == '6') {
                jQuery(this).addClass('bcp-custom-block-3');
            } else if (count == '1') {
                jQuery(this).addClass('bcp-custom-block-1');
            }
            jQuery(this).addClass('bcp-custom-block');
        }
    });
});

/* filter modal click  */
jQuery(document).ready(function () {
    var modal = document.getElementById("filter-modal");
    window.onclick = function (event) {
        if (event.target == modal) {
            jQuery('#filter-modal').removeClass("modal-open");
        }
    }
    jQuery(document).on("click", '.bcp-btn-filter', function () {
        jQuery('#filter-modal').addClass("modal-open");
    });
    jQuery(document).on("click", '.filter-close', function () {
        jQuery('#filter-modal').removeClass("modal-open");
    });
});

/* filter list form click  */
jQuery(document).on('submit', '#filter-list-form', function (e) {
    e.preventDefault();

    jQuery('label.error', '#filter-list-form').remove();
    jQuery('label.error-line', '#filter-list-form').remove();
    jQuery('input', '#filter-list-form').removeClass('error');
    jQuery('input', '#filter-list-form').removeClass('error-line');
    jQuery('#filter-list-form').addClass('bcp-loading');

    var module_name = jQuery('#filter_module_name', '#filter-list-form').val();
    var limit = jQuery('#filter_limit', '#filter-list-form').val();
    var view_style = jQuery('#filter_view_style', '#filter-list-form').val();
    var search = '';
    var where_condition = {}; //{'CaseNumber':'00001349','Type':'Other'};

    jQuery(".where_field").each(function () {
        var key = jQuery(this).attr("name");
        var where_field_val = jQuery(this).val();
        if (where_field_val != '') {
            if (jQuery(this).hasClass("sfcp-multi-dropdown")) {
                if (jQuery(this).is(":checked")) {
                    key = key.replace('[]', '');
                    if (where_condition[key] !== undefined) {
                        where_condition[key].push(where_field_val);
                    } else {
                        where_field_val = [where_field_val];
                        where_condition[key] = where_field_val;
                    }
                }
            } else if (typeof where_field_val == 'object') {
                key = key.replace('[]', '');
                where_condition[key] = where_field_val;
            } else {
                if (jQuery(this).hasClass("sfcp-date") || jQuery(this).hasClass("sfcp-datetime") || jQuery(this).hasClass("sfcp-time")) {
                    where_field_val = where_field_val.split(' - ');
                    where_condition[key] = {'date-range': where_field_val};
                } else {
                    where_condition[key] = where_field_val;
                }
            }
        }
    });
    if (jQuery.isEmptyObject(where_condition)) {
        window.alert(__('Please add any one filter.', 'bcp_portal'));
        jQuery('#filter-list-form').removeClass('bcp-loading');
    } else {
        bcp_list_module_portal(module_name, limit, view_style, search, where_condition);
        jQuery('#filter-list-form').removeClass('bcp-loading');
        jQuery('#filter-modal').removeClass("modal-open");
    }

    return false;
});

/* filter reset form */
jQuery(document).on("click", "#bcp-filter-reset", function () {
    var module_name = jQuery('#filter_module_name', '#filter-list-form').val();
    var limit = jQuery('#filter_limit', '#filter-list-form').val();
    var view_style = jQuery('#filter_view_style', '#filter-list-form').val();
    var search = '';
    var where_condition = {};
    bcp_list_module_portal(module_name, limit, view_style, search, where_condition);
});

/* check portal pages */
function checkPortalPageCreated(page_type, module_name) {
    var PageExistsResponse = {
        page_exists: false
    };
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        async: false,
        data: {'action': 'bcp_check_module', 'module_name': module_name, 'page_type': page_type},
        dataType: 'json',
        success: function (response) {
            PageExistsResponse = response;
        }
    });
    return PageExistsResponse;
}

/* filter btn toggle event */
jQuery(document).on("click", ".bcp-btn-filter", function () {
    jQuery("#filter-list-form").slideToggle();
});

jQuery(document).ready(function () {
    /* language dropdown */
    jQuery(document).on('keydown click', '.language-dropdown-toggle', function (e) {
        if (e.type === 'keydown') {
            jQuery(".language-dropdown-menu").slideDown(500);
        } else {
            e.stopPropagation();
            jQuery(".language-dropdown-menu").slideToggle(500);
        }
    });

    /* language selector */
    jQuery(document).on('click', ".sfcp-language-selector li", function () {
        var lang_code = jQuery('a', this).attr('lang_code');
        if (lang_code === "0") {
            alert("please select language.");
            return false;
        }
        jQuery.ajax({
            url: ajaxurl + '?action=bcp_change_language',
            type: 'POST',
            data: {
                'lang_code': lang_code,
            },
            success: function (response) {
                window.location.reload();
            }
        });
    });

});

/* wpml get filtered url */
function wpml_get_filtered_url(url) {
    var filtered_url;
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        async: false,
        data: {
            'action': 'bcp_get_filtered_url',
            'page_url': url,
        },
        success: function (response_url) {
            filtered_url = response_url;
        }
    });
    return filtered_url;
}

jQuery(document).on('click', function (e) {
    jQuery('.dropdown.sub-menu-div > ul').hide();
    if (jQuery(e.target).parent().hasClass('sub-menu-div')) {
        jQuery(e.target).siblings('ul').toggle();
    }
});

/* filter form js */
function bcp_filter_js() {
    jQuery('.sfcp-datetime').daterangepicker({
        timePicker: true,
        autoUpdateInput: false,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });
    jQuery('.sfcp-date').daterangepicker({
        autoUpdateInput: false,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    jQuery('.sfcp-datetime').on('apply.daterangepicker', function (ev, picker) {
        jQuery(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    jQuery('.sfcp-datetime').on('cancel.daterangepicker', function (ev, picker) {
        jQuery(this).val('');
    });
    jQuery('.sfcp-date').on('apply.daterangepicker', function (ev, picker) {
        jQuery(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    jQuery('.sfcp-date').on('cancel.daterangepicker', function (ev, picker) {
        jQuery(this).val('');
    });

    jQuery(".multi-select-title").click(function () {
        jQuery(".multi-select-dropdown").slideUp();
        if (jQuery(this).parent().find('.multi-select-dropdown').is(":hidden")) {
            jQuery(this).parent().find('.multi-select-dropdown').slideDown();
        }
    });

    jQuery(".multi-select-dropdown label").click(function () {
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
            dp_count++;
            jQuery(this).parent().parent().parent().prev().find("label").html("<span>" + dp_count + "</span> Selected");
        }
    });

    jQuery(document).on("click", function (event) {
        var $trigger = jQuery(".multi-select");
        if ($trigger !== event.target && !$trigger.has(event.target).length) {
            jQuery(".multi-select-dropdown").slideUp("fast");
        }
    });
}

jQuery(document).ready(function () {
    /* sfcp solution like dlike click event */
    jQuery('body').on('click', '.sfcp_solution_like_btn', function (e) {
        e.stopPropagation();
        var id = jQuery(this).attr("data-id");
        var like_count_dom = 0;
        var dislike_count_dom = 0;
        var dislike_count = 0;
        var like_count = 0;
        var like = '0';
        var tmp_like = 0;
        if (jQuery(this).hasClass('lnr-thumbs-up')) {
            tmp_like = 1;
            like = '1';
            like_count_dom = jQuery(this).next('small');
            like_count = like_count_dom.text();
            dislike_count_dom = jQuery(this).parent().parent().find(".sfcp_dislike_div").find("small");
            dislike_count_dom.prev("span").removeClass("sfcp_disliked");
            dislike_count = dislike_count_dom.text();
        } else {
            like_count_dom = jQuery(this).parent().parent().find(".sfcp_like_div").find("small");
            like_count_dom.prev("span").removeClass("sfcp_liked");
            like_count = like_count_dom.text();
            dislike_count_dom = jQuery(this).next('small');
            dislike_count = dislike_count_dom.text();
        }
        if (jQuery(this).hasClass("sfcp_liked") || jQuery(this).hasClass("sfcp_disliked")) {
            like = "-1";
            jQuery(this).removeClass('sfcp_liked');
            jQuery(this).removeClass('sfcp_disliked');
            jQuery(this).removeClass('lnr-thumbs-up');
            jQuery(this).removeClass('lnr-thumbs-down');
        } else {
            if (jQuery(this).hasClass('lnr-thumbs-up')) {
                jQuery(this).addClass('sfcp_liked');
                jQuery(this).removeClass('lnr-thumbs-up');
            } else {
                jQuery(this).addClass('sfcp_disliked');
                jQuery(this).removeClass('lnr-thumbs-down');
            }
        }
        var data = {
            'action': 'bcp_like_dislike_solutions',
            'module_name': 'solutions',
            'is_like': like,
            'id': id,
        };
        var dom_liked = jQuery(this);
        dom_liked.addClass('sfcp-like-loading');
        like_count_dom.prev().addClass('like_disabled');
        dislike_count_dom.prev().addClass('like_disabled');

        jQuery.post(ajaxurl, data, function (response) {
            if (response == 1) {
                dom_liked.removeClass("sfcp-like-loading");
                like_count_dom.prev().removeClass('like_disabled');
                dislike_count_dom.prev().removeClass('like_disabled');
                if (like == '-1') {
                    if (tmp_like) {
                        like_count = parseInt(like_count) - 1;
                        like_count_dom.text(like_count);
                        dom_liked.addClass("lnr-thumbs-up");
                        dom_liked.attr("title", __("Like", 'bcp_portal'));
                    } else {
                        dislike_count = parseInt(dislike_count) - 1;
                        dislike_count_dom.text(dislike_count);
                        dom_liked.addClass("lnr-thumbs-down");
                    }
                } else {
                    if (like == '0') {
                        if (like_count != '0') {
                            like_count = parseInt(like_count) - 1;
                            like_count_dom.text(like_count);
                        }
                        dom_liked.addClass("lnr-thumbs-down");
                        dislike_count = parseInt(dislike_count) + 1;
                        dislike_count_dom.text(dislike_count);
                    } else {
                        if (dislike_count != '0') {
                            dislike_count = parseInt(dislike_count) - 1;
                            dislike_count_dom.text(dislike_count);
                        }
                        dom_liked.attr("title", __("Unlike", 'bcp_portal'));
                        dom_liked.addClass("lnr-thumbs-up");
                        like_count = parseInt(like_count) + 1;
                        like_count_dom.text(like_count);
                    }
                }
            }
        });
    });
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

function openTab(evt, request) {
    var i, tabcontent, tablinks;
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    // document.getElementById(request).style.display = "block";
    evt.currentTarget.className += " active";
    var module_name = jQuery('.tablinks.active').attr('module-name');
    var record_type = request;
    var limit = jQuery('.tablinks.active').attr('limit');
    var view_style = jQuery('.tablinks.active').attr('view_style');
    var search = jQuery('.module_search_name').val();
    console.log(module_name + '-' +record_type +'-'+limit+'-'+view_style);
     bcp_list_module_portal(module_name, limit, view_style, search, '', '', '', '', '', '',record_type);
}

function deleteEmptyCellAction(){
    if(jQuery('#bcp_module_table').length == 0){
        return;
    }
    var tble = document.getElementById('bcp_module_table');
    var row = tble.rows; // Getting the rows
    var a = 0;
    var total_cell = row[0].cells.length;
    var action_cell =  jQuery.trim(row[0].cells[total_cell-1].innerHTML);
    if(action_cell == "Actions"){     
      for (var i = 1; i < row.length; i++) {
          var val = jQuery.trim(jQuery(row[i]).find('.listing-action').html());
          if(val == "-"){
          }else{
             a = 1;
          }                  
       }
        if( a == 0){
        for (var i = 1; i < row.length; i++) {
            row[i].deleteCell(total_cell-1);
        }
        row[0].deleteCell(total_cell-1);
        }
    }
}


jQuery(document).on('click', '#open_id01_product_module_selection', function (e) {
    jQuery('#id01_product_module_selection').show();
    var yourArray = [];
    var productArray = [];
    var qtyArray = [];
    $(".select-product-chk:checked").each(function(){
        yourArray.push($(this).val());
    });
    console.log(yourArray);
    for (i= 0; i < yourArray.length;i++){
        var qtyArray = [];
        var qty = jQuery('input[name="productLineItems['+yourArray[i]+'][Quantity]"]').val();
        qtyArray.push(yourArray[i]);
        qtyArray.push(qty);
        console.log(qtyArray);
        productArray.push(qtyArray);     
    }
    console.log(jQuery('.selected_products').length);
    jQuery.extend({}, productArray);
    jQuery('.selected_products').val(JSON.stringify(productArray));
    jQuery('.selected_pricebook').val(jQuery('#pricebook').val());
    console.log(productArray);
});


jQuery(document).on('submit', '#module_for_selected_products', function (event) {
    event.preventDefault();
    var selected_pricebook = jQuery('.selected_pricebook').val();
    var selected_products = jQuery('.selected_products').val();
    var module = jQuery('input[name="module_radio"]:checked').val();
   
    document.cookie = "pselected_pricebook="+selected_pricebook+" ;expires=; path=/";
    document.cookie = "pselected_products="+selected_products+" ;expires=; path=/";
    document.cookie = "pselected_module="+module+" ;expires=; path=/";
    console.log('selected_pricebook '+ selected_pricebook);
    console.log('selected_products '+ selected_products);
    console.log('module '+ module);
    window.location.href = homeurl + "customer-portal/"+module+"/edit/";
});