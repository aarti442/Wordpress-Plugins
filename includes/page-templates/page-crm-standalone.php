<?php
/**
 * Template Name: CRM Standalone
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 */
ob_start();
global $post;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo get_the_title(); ?> | <?php bloginfo('name'); ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php wp_head(); ?>
    </head>
    <body  <?php body_class(); ?> >
        <?php
        if (get_option('bcp_theme_color')) {
            $themecolor = new BCPCustomerPortalThemeColor();
            $themecolor->theme_color_standalone();
        }
        ?>

<?php if (isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id'] != '') { ?>
            <!-- main Start-->
            <div class="main-home">
                <!-- Header Start-->
                <header class="standalone-header">
                    <!-- Container Start-->
                    <div class="container">
                        <!-- Row Start-->
                        <div class="bcp-row header-content">
                            <!-- sub-bcp-flex Start-->
                            <div class="sub-bcp-flex">
                                <?php
                                $bcp_logo = fetch_data_option('bcp_logo');
                                if ($bcp_logo != null) {
                                    $bcp_logo_url = wp_get_attachment_image_src($bcp_logo);
                                    $bcp_logo_url = $bcp_logo_url[0];
                                    if ($bcp_logo_url) {
                                        ?>
                                        <div class="main-logo">
                                            <a href="<?php echo home_url('/'); ?>">
                                                <img src="<?php echo $bcp_logo_url; ?>"  alt="<?php echo fetch_data_option('bcp_name'); ?>">
                                            </a>
                                        </div>
                                    <?php
                                    }
                                } else {
                                    $bcp_name = fetch_data_option('bcp_name');
                                    $bcp_mobile_menu_title = fetch_data_option('bcp_mobile_menu_title');
                                    if (wp_is_mobile()) {
                                        ?>
                                        <div class="main-title" id="scp-mobile-title">
                                            <a href="<?php echo home_url('/'); ?>">
                                                <h3><?php _e($bcp_mobile_menu_title, 'bcp_portal'); ?></h3>
                                            </a>
                                        </div>
        <?php } else { ?>
                                        <div class="main-title" id="scp-title">
                                            <a href="<?php echo home_url('/'); ?>">
                                                <h3><?php _e($bcp_name, 'bcp_portal'); ?></h3>
                                            </a>
                                        </div>

                                        <?php
                                    }
                                }
                                ?>
                                <nav>
                                    <?php
                                    $standalone_menu = fetch_data_option('bcp_portal_menuSelection');
                                    if (!wp_get_nav_menu_items($standalone_menu)) {
                                        $term = get_term_by('name', 'Customer Portal Menu', 'nav_menu');
                                        $standalone_menu = $term->term_id;
                                    }

                                    wp_nav_menu(array(
                                        'menu' => $standalone_menu,
                                        'menu_class' => 'crm-standalone-menu',
                                        'container' => 'ul',
                                        'fallback_cb' => false,
                                    ));
                                    ?>
                                </nav>
                            </div>
                            <!-- sub-bcp-flex End-->
                            <!-- header-right Start-->
                            <div class="header-right">
                                <ul class="user-details">
                                    <li class="dropdown-li">
                                        <a href="javascript:void(0)" class="toggle-submenu">
                                            <span class="lnr lnr-user"></span>
                                            <span class="bcp_contact_name"></span>
                                            <span class="lnr lnr-chevron-down"></span>
                                        </a>
                                        <ul class="dropdown">
                                            <?php
                                            if (fetch_data_option('bcp_profile_page') == get_the_ID()) {
                                                $redirect_name = __('Dashboard', 'bcp_portal');
                                                $redirect_url = get_permalink(fetch_data_option('bcp_dashboard_page'));
                                            } else {
                                                $redirect_name = __('Profile', 'bcp_portal');
                                                $redirect_url = get_permalink(fetch_data_option('bcp_profile_page'));
                                            }
                                            ?>
                                            <li><a href="<?php echo $redirect_url; ?>"><?php echo $redirect_name; ?></a></li>
                                            <li><a href="<?php echo home_url('/?bcp-logout=true'); ?>"><?php _e('Logout', 'bcp_portal'); ?></a></li>
                                        </ul>
                                    </li>
                                </ul>
                                <div class="mobile-menu">
                                    <span class="lnr lnr-menu"></span>
                                </div>
                            </div>
                            <!-- header-right End-->
                        </div>
                        <!-- Row End-->
                    </div>
                    <!-- Container End-->
                </header>
                <!-- Header End-->
            <?php } ?>

            <?php
            $bcp_authentication = fetch_data_option("bcp_authentication");  
            while (have_posts()) : the_post();
                if (!isset($bcp_authentication) || $bcp_authentication == null) {
                    ?>
                    <div id="fullwidth-wrapper" class="crm_standalone sfcp-manage-page">
                        <div class='alert alert-danger' id='setting-error-settings_updated'>
                            <p><strong><?php _e('Portal is disabled. Please contact your administrator.'); ?></strong></p>
                        </div>
                    </div>
                    <?php
                } else {
                    $pagesIds = array(
                        'bcp_login_page',
                        'bcp_forgot_password_page',
                        'bcp_reset_password_page',
                    );

                    $page_id = array();
                    foreach ($pagesIds as $val) {
                        $page_id[] = fetch_data_option($val);
                    }
                    if (in_array($post->ID, $page_id)) {
                        $featured_img = get_the_post_thumbnail_url(get_the_ID(), 'full');
                        $class = "bcp-flex crm_standalone bcp-main-login-part login-bg-color";
                        if ($featured_img) {
                            $class = "bcp-flex crm_standalone bcp-main-login-part login-bg";
                            ?>
                            <style>.login-bg { background-image: url('<?php echo $featured_img; ?>') !important; }</style>
                                <?php } ?>
                        <div class="<?php echo $class; ?>">
                            <div class="container">
                        <?php the_content(); ?>
                            </div>
                        </div>
                    <?php } else if (fetch_data_option("bcp_registration_page") == $post->ID) { ?>
                        <div class="bcp-flex crm_standalone login-bg-color bcp-main-signup-part">
            <?php the_content(); ?>
                        </div>
                                <?php } else { ?>
                        <main class="sfcp-content-main">
                            <div class="container">
                                <div id="fullwidth-wrapper" class="crm_standalone dcp_page_login">
                        <?php the_content(); ?>
                                </div>
                            </div>
                        </main>
                        <?php
                    }
                }
            endwhile;
            ?>

            <?php if (isset($_SESSION['bcp_contact_id']) && $_SESSION['bcp_contact_id'] != '') { ?>
                <footer class="standalone-footer">
                    <div class="container">
                        <p><?php echo fetch_data_option('bcp_standalone_footer_content'); ?></p>
                    </div>
                </footer>
            </div>
            <!-- main End-->
            <?php } ?>

<?php wp_footer(); ?>
    </body>
</html>
<?php
$content = ob_get_clean();
echo $content;
