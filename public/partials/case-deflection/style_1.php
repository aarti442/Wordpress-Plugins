<?php
/** 
 * The file used to manage case deflection style 1.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */

defined('ABSPATH') or die('No script kiddies please!');

global $objCp;
$content = '';
ob_start();
$bcp_authentication = fetch_data_option("bcp_authentication");  
if (isset($bcp_authentication) && $bcp_authentication != null) {

    if (isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id'] != null) {
            ?>
        <!--Search Block Start-->
        <div class="search-block">
            <h2><?php _e($page_heading, 'bcp_portal'); ?></h2>
            <div class="scp-page-action-title"></div>
            <p class="solution-info"><?php _e($module_description, 'bcp_portal'); ?>
                <a href="<?php echo $add_url; ?>" title="<?php _e($add_case_title, 'bcp_portal'); ?>">
                    <?php _e($add_case_title, 'bcp_portal'); ?>
                </a>
            </p>
            
            <form id="search_solutions_form">
                <div class="bcp-form-group search_solutions_input">                    
                    <input type="text" class="form-control" required="" id="bcp_solution_keyword" name="bcp_solution_keyword" placeholder="<?php _e("Search solution", 'bcp_portal'); ?>" >
                    <button class="search_solutions_btn scp-case bcp-btn" type="submit" title="<?php _e('Search', 'bcp_portal'); ?>"><span class="lnr lnr-magnifier"></span></button>
                </div>
            </form>
            <div id="solutionlist-items"></div>
        </div>
        <!--Search Block End-->
        <?php } else { ?>
        <span class='error error-line error-msg settings-error'><?php _e('Please login to portal and access this page.', 'bcp_portal'); ?></span>
        <?php
    }
} else {
    ?>
    <span class='error error-line error-msg settings-error'><?php _e('Portal is disabled. Please contact your administrator.', 'bcp_portal'); ?></span>
    <?php
}
$content .= ob_get_clean();
echo $content;
?>