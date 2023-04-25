<?php
defined('ABSPATH') or die('No script kiddies please!');

if (!function_exists('sfcp_solutions_searchpage')) {
    /**
     * solutions search page
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    function sfcp_solutions_searchpage() {
        global $objCp;
        $content = '';
        $bcp_authentication = fetch_data_option("bcp_authentication");  
        ob_start();
        if (isset($bcp_authentication) && $bcp_authentication != null) {

            if (isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id'] != null) {
                $capsModule = $_SESSION['bcp_modules']['case']['singular'];
                $theme_color = fetch_data_option('bcp_theme_color');
                if ($theme_color) {
                    ?>
                    <style>
                        .solution-info a {color: '<?php echo $theme_color; ?> !important';}
                        .bcp-btn{
                            background-color: '<?php echo $theme_color; ?> !important';
                        }
                    </style>
                <?php } ?>
                <!-- search-block Start -->
                <div class="search-block">
                    <h2><?php _e(ucwords($_SESSION['bcp_modules']['case']['singular']), 'bcp_portal'); ?></h2>
                    <div class="scp-page-action-title"></div>
                    <p class="solution-info"><?php _e("You may find probable solution you're looking, just search it or ", 'bcp_portal'); ?>
                        <a href="<?php echo home_url("customer-portal/case/edit/"); ?>" title="<?php echo __("Add") . " " . $capsModule; ?>"><?php echo __("Add") . " " . $capsModule; ?></a>
                    </p>
                    <!-- search_solutions_form Start -->
                    <form id="search_solutions_form">
                        <div class="bcp-form-group search_solutions_input">
                            <input type="text" class="form-control" required="" id="bcp_solution_keyword" name="bcp_solution_keyword" placeholder="<?php _e("Search solution", 'bcp_portal'); ?>" >
                            <button class="search_solutions_btn scp-case bcp-btn" type="submit" title="<?php _e('Search'); ?>">
                                <span class="lnr lnr-magnifier"></span>
                            </button>
                        </div>
                    </form>
                    <!-- search_solutions_form End -->
                    <div id="solutionlist-items"></div>
                </div>
                <!-- search-block End -->
                <script>
                    jQuery("#search_solutions_form").submit(function (e) {
                        e.preventDefault();
                        var search_term, error_message, bcp_required_error;
                        bcp_required_error = 0;
                        jQuery('.bcp-required', this).each(function () {

                            // If next element has class error-line, than remove it
                            if (jQuery(this).next().hasClass('error-line')) {
                                jQuery(this).next('').remove();
                            }

                            this.value = jQuery.trim(jQuery(this).val());
                            search_term = jQuery(this).val();
                            if (!search_term || search_term.length < 3) {

                                jQuery(this).addClass('error-line');
                                if (!search_term) {
                                    error_message = '<?php _e('This field is required.', 'bcp_portal'); ?>';
                                } else {
                                    error_message = '<?php _e('Enter atleast 3 characters to start search.', 'bcp_portal'); ?>';
                                }
                                jQuery('<label class="error-line">' + error_message + '</label>').insertAfter(this);
                                bcp_required_error = 1;
                            }

                        });

                        if (bcp_required_error) {
                            return false;
                        }

                        var search_term = jQuery("#bcp_solution_keyword").val();
                        if (jQuery.trim(search_term) == "") {
                            return false;
                        }
                        var data = {
                            'action': 'bcp_solutions',
                            'module_name': 'solutions',
                            'view': 'list',
                            'search_term': search_term
                        };
                        jQuery('#solutionlist-items').addClass('bcp-loading');
                        jQuery.post(ajaxurl, data, function (response) {
                            jQuery('#solutionlist-items').html(response);
                            jQuery('#solutionlist-items').removeClass('bcp-loading');
                        });
                    });
                </script>
            <?php } else { ?>
                <span class='error error-line error-msg settings-error'><?php _e('Please login to portal and access this page.'); ?></span>
                <?php
            }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Portal is disabled. Please contact your administrator.'); ?></span>
            <?php
        }
        $content .= ob_get_clean();

        return $content;
    }
}

/**
* solutions list data
*
* @since 4.0.0
* @return void
* @author Akshay Kungiri 
*/
if (!function_exists('sfcp_solutions_list')) {

    function sfcp_solutions_list($case_id = "", $paged, $offset, $search_term = "") {

        global $objCp, $case_record, $bcp_field_prefix;

        $content = '';
        ob_start();
        if (isset($bcp_authentication) && $bcp_authentication != null) {

            if (isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id'] != null) {

                $limit = (int) ( fetch_data_option('bcp_records_per_page') ? fetch_data_option('bcp_records_per_page') : 10 );
                $offset = ( $offset < 0 ) ? 0 : $offset;
                $total_records = 0;
                $solutions = "";
                $rating_data = array();
                $cid = $_SESSION['bcp_contact_id'];
                $like_field = $slike_field = $bcp_field_prefix . "Like__c";
                $dislike_field = $sdislike_field = $bcp_field_prefix . "Dislike__c";
                $sol_id_field = $bcp_field_prefix . "SolutionId__c";

                if ($search_term != "") {

                    $data = array(
                        'title' => $search_term,
                        'limit' => $limit,
                        'offset' => $offset,
                        'cid' => $cid,
                    );
                    $responce = $objCp->search($data);
                    $total_records = $responce->totalcount;
                    $solutions = $responce->records[0];
                    if (isset($responce->ratingdata->data)) {
                        $rat_array = $responce->ratingdata->data;
                        foreach ($rat_array as $value) {
                            if ($value->$like_field != "" || $value->$dislike_field != "") {
                                $sol_id = $value->$sol_id_field;
                                $rating_data[$sol_id] = 0;
                                if ($value->$like_field == 1) {
                                    $rating_data[$sol_id] = 1;
                                }
                            }
                        }
                    }
                } else {

                    $total_records = 0;
                    if (isset($case_record->solutions) && $case_record->solutions != "") {
                        $case_solutions = $case_record->solutions;
                        $like_field = "Like__c";
                        $dislike_field = "Dislike__c";
                        $solutions = $case_solutions->data;
                        $total_records = $case_solutions->count;
                    } else {
                        $cid = $_SESSION['bcp_contact_id'];
                        $data_fields = array('id');
                        $others = array();
                        $others['showcasesolution'] = TRUE;
                        $others['solution_limit'] = $limit;
                        $others['solution_offset'] = $offset;
                        $case_record_solutions = $objCp->PortalGetDetailData($cid, "case", $case_id, $data_fields, $others);
                        $case_solutions = $case_record_solutions->solutions;
                        $solutions = $case_solutions->data;
                        $total_records = $case_solutions->count;
                    }
                }

                if (count($solutions) == 0) {
                    $solution_message = __("No solution found.");
                } else {
                    $solution_message = __("Didn't find solution?");
                    ?>
                    <!-- solution-found-block Start -->
                    <div class="not-found-block solution-found-block">
                        <h3><?php _e('Solution', 'bcp_portal'); ?></h3>
                        <div class="sfcp-validation casecomment-msg"></div>
                        <?php
                        $cnt = 0;
                        foreach ($solutions as $value) {

                            $title = $value->SolutionName;
                            $id = $value->Id;
                            $sort_desc = htmlspecialchars($value->SolutionNote);
                            $sort_desc = nl2br($sort_desc);
                            $total_like = (isset($value->$like_field) ? $value->$like_field : $value->Like__c);
                            $total_dislike = (isset($value->$dislike_field) ? $value->$dislike_field : $value->Dislike__c);
                            $liked_class = "";
                            $disliked_class = "";
                            if (isset($value->ratingdata->data)) {
                                $rat_array = $value->ratingdata->data;
                                if (isset($rat_array[0])) {
                                    $value_sol = $rat_array[0];
                                    if ($value_sol->$slike_field == 1) {
                                        $liked_class = "sfcp_liked";
                                    }
                                    if ($value_sol->$sdislike_field == 1) {
                                        $disliked_class = "sfcp_disliked";
                                    }
                                }
                            }
                            $like_title = __("Like");
                            $dislike_title = __("Dislike");
                            if (isset($rating_data[$id]) && $rating_data[$id] == 1) {
                                $liked_class = "sfcp_liked";
                                $like_title = __("Unlike");
                            }
                            if (isset($rating_data[$id]) && $rating_data[$id] == 0) {
                                $disliked_class = "sfcp_disliked";
                            }
                            ?>
                            <!-- acc__card Start -->
                            <div class="acc__card">
                                <div class="acc__title">
                                    <?php echo $title ?>
                                    <div class="like-block">
                                        <div class="sfcp_likes_loading"></div>
                                        <p class="sfcp_dislike_div">
                                            <span title="<?php echo $dislike_title; ?>" class="lnr lnr-thumbs-down sfcp_solution_like_btn <?php echo $disliked_class; ?>" data-id="<?php echo $id; ?>" id="sfcp_solution_dislike_btn"></span>
                                            <small><?php echo $total_dislike; ?></small>
                                        </p>
                                        <p class="sfcp_like_div">
                                            <span title="<?php echo $like_title; ?>" class="lnr lnr-thumbs-up sfcp_solution_like_btn <?php echo $liked_class; ?>" data-id="<?php echo $id; ?>" id="sfcp_solution_like_btn"></span>
                                            <small><?php echo $total_like; ?></small>
                                        </p>
                                    </div>
                                </div>
                                <div class="acc__panel">
                                    <?php echo $sort_desc; ?>
                                </div>
                            </div>
                            <!-- acc__card End -->
                            <?php
                            $cnt++;
                        }
                        echo sfcp_pagination($total_records, $limit, $paged, 'solutions', 'list', "", $search_term);
                        ?>
                    </div>
                    <!-- solution-found-block End -->
                    <script>
                        jQuery(document).ready(function () {

                            jQuery(".sfcp_solution_like_btn").on("click", function (e) {
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
                                    'action': 'sfcp_like_dislike_solutions',
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
                                                dom_liked.attr("title", "<?php _e("Like"); ?>");
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
                                                dom_liked.attr("title", "<?php _e("Unlike"); ?>");
                                                dom_liked.addClass("lnr-thumbs-up");
                                                like_count = parseInt(like_count) + 1;
                                                like_count_dom.text(like_count);
                                            }
                                        }
                                    }
                                });
                            });
                        });
                    </script>
                    <?php
                }

                if ($search_term != "") {
                    ?>
                    <!-- not-found-block Start -->
                    <div class="not-found-block">
                        <div class="dcp-flex">
                            <p><?php echo $solution_message; ?></p>
                            <a href="<?php echo home_url("customer-portal/case/edit/"); ?>" class="bcp-btn" title="<?php _e("Add"); ?>">
                                <span class="icon">+</span>&nbsp;&nbsp;
                                <?php echo __('Add') . ' ' . $_SESSION['bcp_modules']['case']['singular']; ?>
                            </a>
                        </div>
                    </div>
                    <!-- not-found-block End -->
                    <?php
                } else {
                    if (count($solutions) == 0 && $search_term != "") {
                        ?>
                        <!-- not-found-block Start -->
                        <div class="not-found-block">
                            <div class="dcp-flex">
                                <p><?php echo $solution_message; ?></p>
                                <a href="<?php echo home_url("customer-portal/case/edit/"); ?>" class="bcp-btn" title="<?php _e("Add"); ?>">
                                    <span class="icon">+</span>&nbsp;&nbsp;
                        <?php echo __('Add') . ' ' . $_SESSION['bcp_modules']['case']['singular']; ?>
                                </a>
                            </div>
                        </div>
                        <!-- not-found-block End -->
                    <?php } else if (count($solutions) == 0) { ?>
                        <!-- not-found-block Start -->
                        <div class="not-found-block">
                            <div class="dcp-flex">
                                <p><?php echo $solution_message; ?></p>
                            </div>
                        </div>
                        <!-- not-found-block End -->
                        <?php
                    }
                }
            } else {
                ?>
                <span class='error error-line error-msg settings-error'><?php _e('Please login to portal and access this page.'); ?></span>
            <?php
            }
        } else {
            ?>
            <span class='error error-line error-msg settings-error'><?php _e('Portal is disabled. Please contact your administrator.'); ?></span>
            <?php
        }
        $content .= ob_get_clean();

        return $content;
    }

}