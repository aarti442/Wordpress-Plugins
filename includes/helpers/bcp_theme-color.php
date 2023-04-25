<?php
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Check portal theme color
 *
 * @since 1.0.0
 * @return void
 */
class BCPCustomerPortalThemeColor {

    public function __construct() {
        $this->theme_color = get_option('bcp_theme_color');
        if($this->theme_color){
            $ColorFormatter = new Mexitek\PHPColors\ColorFormatter($this->theme_color);
            $this->gradient_css = $ColorFormatter->lighten(45);
            $this->gradient_dark_css = $ColorFormatter->lighten(40);
        }
    }
    
    /**
     * Counter style 
     *
     * @param string $content
     * @return void
     * @author Dharti Gajera
     */
    public function theme_color_counter_style($counter_block_id, $block_color, $block_hover_color, $font_color, $font_hover_color, $view_style) {
        ?>
        <style>
            <?php if($view_style == 'style_1'){ ?>
                #<?php echo $counter_block_id; ?> .card-body .bcp-row h3,
                #<?php echo $counter_block_id; ?> .card-body .bcp-row h2,
                #<?php echo $counter_block_id; ?> .card-body .fa::before,
                #<?php echo $counter_block_id; ?> .card-body .bcp-row .bcp-col a {
                    color: <?php echo $font_color; ?> !important;
                }

                #<?php echo $counter_block_id; ?> .card-body:hover .bcp-row h3,
                #<?php echo $counter_block_id; ?> .card-body:hover .bcp-row h2,
                #<?php echo $counter_block_id; ?> .card-body:hover .fa::before,
                #<?php echo $counter_block_id; ?> .card-body:hover .bcp-row .bcp-col a {
                    color: <?php echo $font_hover_color; ?> !important;
                }
                #<?php echo $counter_block_id; ?> .card-body .fa::after,
                #<?php echo $counter_block_id; ?> .card-body:hover {
                    background-color: <?php echo $block_hover_color; ?>;
                }
                #<?php echo $counter_block_id; ?> .card-body {
                    background-color: <?php echo $block_color; ?>;
                }
            <?php } else if($view_style == 'style_2'){ ?>
                #<?php echo $counter_block_id; ?> .card-body h3,
                #<?php echo $counter_block_id; ?> .card-body h2,
                #<?php echo $counter_block_id; ?> .card-body .fa::before,
                #<?php echo $counter_block_id; ?> .card-body a {
                    color: <?php echo $font_color; ?> !important;
                }

                #<?php echo $counter_block_id; ?> .card-body {
                    background-color: <?php echo $block_color; ?>;
                }
            <?php } else if($view_style == 'style_3'){ ?>
                #<?php echo $counter_block_id; ?> .card-body h3,
                #<?php echo $counter_block_id; ?> .card-body h2,
                #<?php echo $counter_block_id; ?> .card-body .fa::before,
                #<?php echo $counter_block_id; ?> .card-body a {
                    color: <?php echo $font_color; ?> !important;
                }

                #<?php echo $counter_block_id; ?> .card-body:hover h3,
                #<?php echo $counter_block_id; ?> .card-body:hover h2,
                #<?php echo $counter_block_id; ?> .card-body:hover .fa::before,
                #<?php echo $counter_block_id; ?> .card-body:hover a {
                    color: <?php echo $font_hover_color; ?> !important;
                }
                #<?php echo $counter_block_id; ?> .card-body {
                    background-color: <?php echo $block_color; ?>;
                }
                #<?php echo $counter_block_id; ?> .card-body .icon-block,
                #<?php echo $counter_block_id; ?> .card-body:hover {
                    background: <?php echo $block_hover_color; ?>;
                }

                #<?php echo $counter_block_id; ?> .card-body:hover .icon-block{
                    background: <?php echo $block_color; ?>;
                    color: <?php echo $block_hover_color; ?> !important;
                }
            <?php } else if($view_style == 'style_4'){ ?>
                #<?php echo $counter_block_id; ?> .card-body .fa::before,
                #<?php echo $counter_block_id; ?> .card-body:hover a {
                    color: <?php echo $font_color; ?> !important;
                }

                #<?php echo $counter_block_id; ?> .card-body:hover .fa::before,
                #<?php echo $counter_block_id; ?> .card-body a {
                    color: <?php echo $font_hover_color; ?> !important;
                }
                #<?php echo $counter_block_id; ?> .card-body {
                    background-color: <?php echo $block_color; ?>;
                }
                #<?php echo $counter_block_id; ?> .card-body:hover h3,
                #<?php echo $counter_block_id; ?> .card-body:hover .icon-block {
                    background: <?php echo $block_hover_color; ?>;
                    color: <?php echo $font_hover_color; ?> !important;
                    border: 1px solid <?php echo $font_color; ?> !important;
                    box-shadow: none !important;
                }

                #<?php echo $counter_block_id; ?> .card-body h3,
                #<?php echo $counter_block_id; ?> .card-body .icon-block{
                    background: <?php echo $block_color; ?>;
                    color: <?php echo $font_color; ?> !important;
                    border: 1px solid <?php echo $font_color; ?> !important;
                }
            <?php } else if($view_style == 'style_5'){ ?>
                #<?php echo $counter_block_id; ?> h3.counter-number-h3,
                #<?php echo $counter_block_id; ?> .card-body .icon-block {
                    background-color: <?php echo $block_color; ?> !important;
                    color: <?php echo $font_color; ?> !important;
                }
            <?php } else if($view_style == 'style_6'){ ?>
                #<?php echo $counter_block_id; ?> .card-body .icon-block {
                    background: <?php echo $block_color; ?> !important;
                    color: <?php echo $font_color; ?> !important;
                    box-shadow: none !important;
                }
                #<?php echo $counter_block_id; ?> .card-body:hover .icon-block {
                    background: <?php echo $block_color; ?> !important;
                    color: <?php echo $font_color; ?> !important;
                    box-shadow: none !important;
                }
            <?php } ?>
        </style>
        <?php
    }
    
    /**
     * Detail page style 
     *
     * @param string $content
     * @return void
     * @author Dharti Gajera
     */
    public function theme_color_detail_view_style($view_style) {
        ?>
        <style>
            .case-details-view h2,
            .case-details-description .updates-details h3,
            .case-details-view .bcp-button-group button.action-btn,
            ul.tabs li.current,
            ul.tabs li.current a {
                color: <?php echo $this->theme_color; ?> !important;
            }
            .bcp-btn {
                background-color: <?php echo $this->theme_color; ?> !important;
            }
            /*.bcp-table tbody tr{
                background-color: #<?php echo $this->gradient_css; ?> !important;
            }*/
            .listing-style-1.bcp-table tr:hover{
                background-color: #<?php echo $this->gradient_dark_css; ?> !important;
            }
            .listing-data-table thead {
                background-color: #<?php echo $this->gradient_dark_css; ?> !important;
            }
            .subpanel-div nav.dcp-navigation ul li a.active {
                background-color: <?php echo $this->theme_color; ?> !important;
            }
            .subpanel-div nav.dcp-navigation ul li a:hover {
                background-color: <?php echo $this->theme_color; ?> !important;
            }
            <?php if($view_style == 'style_2'){ ?>
                ul.tabs li.current,
                ul.tabs li.current a {
                    color: <?php echo $this->theme_color; ?> !important;
                    border-color: <?php echo $this->theme_color; ?> !important;
                }
                .description-details-2 .updates-details .updates-details-main .update-listing.even .profile-pic {
                    background-color: <?php echo $this->theme_color; ?> !important;
                }
            <?php } ?>
        </style>
        <?php
    }
    
    /**
     * forgot password style 
     *
     * @param string $content
     * @return void
     * @author Dharti Gajera
     */
    public function theme_color_forgot_password_style() {
        ?>
        <style>
            .bcp-part-login .login-btn-group .bcp-btn{
                background-color: <?php echo $this->theme_color; ?> !important;
            }
            .bcp-part-login .login-btn-group .signup-now.bcp-btn{
                color: <?php echo $this->theme_color; ?> !important;
                background: transparent !important;
            }
        </style>
        <?php
    }
    
    /**
     * edit style 
     *
     * @param string $content
     * @return void
     * @author Dharti Gajera
     */
    public function theme_color_edit_style() {
        ?>
        <style>
            .add-case-block h2 { color: <?php echo $this->theme_color; ?> !important;}
            .save-btn,
            .login_btn {
                background-color: <?php echo $this->theme_color; ?> !important;
            }
        </style>
        <?php
    }
    
    /**
     * Global search style 
     *
     * @param string $content
     * @return void
     * @author Dharti Gajera
     */
    public function theme_color_global_search_style() {
        ?>
        <style>
            .bcp-btn {
                background-color: <?php echo $this->theme_color; ?> !important;
            }
            .global-search .global-result-block h3 {
                color: <?php echo $this->theme_color; ?> !important;
            }
        </style>
        <?php
    }
    
    /**
     * knowledgebase style 
     *
     * @param string $content
     * @return void
     * @author Dharti Gajera
     */
    public function theme_color_knowledgebase_style() {
        ?>
        <style>
            .search-block h2,
            .solution-info a,
            .search-block .solution-found-block h3{ color: <?php echo $this->theme_color; ?> !important; }
            .bcp-btn,
            nav.dcp-navigation ul li a.active,
            nav.dcp-navigation ul li a:hover {
                background-color: <?php echo $this->theme_color; ?> !important;
            }
           
            .search-block .bcp-form-group input { border: 1px solid <?php echo $this->theme_color; ?> !important;}
        </style>
        <?php
    }
    
    /**
     * list block style 
     *
     * @param string $content
     * @return void
     * @author Dharti Gajera
     */
    public function theme_color_list_style($view_style) {
        ?>
        <style>
            .listing-block .bcp-btn,
            nav.dcp-navigation ul li a.active,
            nav.dcp-navigation ul li a:hover {
                background-color: <?php echo $this->theme_color; ?> !important;
            }
            .tab button.active {
                color: <?php echo $this->theme_color; ?> !important;
            }
            .add-case-block.add-product-block h2,.add-case-block.add-product-block .module_title{
                color: <?php echo $this->theme_color; ?> !important;
            }
            #module_for_selected_products .bcp-btn {
                background-color: <?php echo $this->theme_color; ?> !important;
            }
           
            <?php if($view_style == 'style_1'){ ?>
                .listing-1 .listing-block span.heading-icon,
                .listing-1 .listing-block h2{ color: <?php echo $this->theme_color; ?> !important;}
                .listing-style-1 .bcp-table tr:hover{
                    background-color: #<?php echo $this->gradient_dark_css; ?> !important;
                }
            <?php } else if($view_style == 'style_2'){ ?>
                .listing-2 .listing-block span.heading-icon,
                .listing-2 .listing-style-2 h2,
                .listing-style-2 .bcp-table tbody tr:hover,
                .listing-style-2 .bcp-table tbody tr .listing-action .edit-action,
                .listing-style-2 .bcp-table tbody tr:hover .delete-action,
                .listing-style-2 .bcp-table tbody tr:hover a{
                    color: <?php echo $this->theme_color; ?> !important;
                }

                .listing-style-2 .bcp-table thead tr{
                    background-color: #<?php echo $this->gradient_dark_css; ?> !important;
                } 
            <?php } else if($view_style == 'style_3'){ ?>
                .listing-3 .listing-block span.heading-icon,
                .listing-3 .listing-block h2{ color: <?php echo $this->theme_color; ?> !important;}
                
                .listing-style-3 .bcp-table tbody tr td.listing-action a {
                    color: <?php echo $this->theme_color; ?> !important;
                    border: 1px solid <?php echo $this->theme_color; ?> !important;
                }
                .listing-style-3 .bcp-table tbody tr .listing-action a:hover{
                    background-color: <?php echo $this->theme_color; ?> !important;
                    color: #FFFFFF !important;
                }
                .listing-style-3 .bcp-table tbody tr:nth-child(odd) {
                    background-color: #<?php echo $this->gradient_css; ?> !important;
                }

                .listing-style-3 .bcp-table tbody tr:hover {
                    background-color: #<?php echo $this->gradient_dark_css; ?> !important;
                } 
            <?php } ?>
        </style>
        <?php
    }
    
    /**
     * login style 
     *
     * @param string $content
     * @return void
     * @author Dharti Gajera
     */
    public function theme_color_login_style() {
        ?>
        <style>
            .bcp-part-login .login-btn-group .bcp-btn{
                background-color: <?php echo $this->theme_color; ?> !important;
            }
            .bcp-part-login .login-btn-group .signup-now.bcp-btn{
                color: <?php echo $this->theme_color; ?> !important;
                background: transparent !important;
            }
        </style>
        <?php
    }
    
    /**
     * profile style 
     *
     * @param string $content
     * @return void
     * @author Dharti Gajera
     */
    public function theme_color_profile_style() {
        ?>
        <style>
            .add-case-block h2 { color: <?php echo $this->theme_color; ?> !important;}
            .bcp-btn {
                background-color: <?php echo $this->theme_color; ?> !important;
            }
        </style>
        <?php
    }
    
    /**
     * reset password style 
     *
     * @param string $content
     * @return void
     * @author Dharti Gajera
     */
    public function theme_color_reset_password_style() {
        ?>
        <style>
            .bcp-part-login .login-btn-group .bcp-btn{
                background-color: <?php echo $this->theme_color; ?> !important;
            }
        </style>
        <?php
    }
    
    /**
     * recent activity style 
     *
     * @param string $content
     * @return void
     * @author Dharti Gajera
     */
    public function theme_color_recent_activity_style($view_style) {
        ?>
        <style>
            <?php if($view_style == 'style_1'){ ?>
                .dcp-content-table .dcp-table tr .dcp-user-details .dcp-profile-pic{
                    background-color: <?php echo $this->theme_color; ?> !important;
                }
                .bcp-recent-activity-style-1 .dcp-content-table .dcp-table .dcp-head th {
                    background-color: #<?php echo $this->gradient_dark_css; ?> !important;
                }
            <?php } else if($view_style == 'style_2'){ ?>
                .dcp-content-table .dcp-table tr .dcp-user-details .dcp-profile-pic{
                    background-color: <?php echo $this->theme_color; ?> !important;
                }
                .recent-block-style-2 table.dcp-table tbody tr:nth-child(odd) {
                    background-color: #<?php echo $this->gradient_dark_css; ?> !important;   
                }
            <?php } else if($view_style == 'style_3'){ ?>
                .bcp-recent-activity-style-3 .dcp-content-table .dcp-table .dcp-head th {
                    background-color: #<?php echo $this->gradient_dark_css; ?> !important;
                }
            <?php } ?>
        </style>
        <?php
    }
    
    /**
     * knowledgebase style 
     *
     * @param string $content
     * @return void
     * @author Dharti Gajera
     */
    public function theme_color_standalone() {
        ?>
        <style>
            .standalone-header, .standalone-footer {
                background-color: <?php echo $this->theme_color; ?>;
            }
        </style>
        <?php
    }
}
