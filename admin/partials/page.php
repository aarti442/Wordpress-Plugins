<h2><?php _e('Page Settings'); ?></h2>
<!-- form-table Start -->
<table class="form-table">
    <tbody>
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Log In Page', 'bcp_portal'); ?></label></th>
            <td>
                <?php
                wp_dropdown_pages(
                        array(
                            'show_option_none' => __('Select a page', 'bcp_portal'),
                            'option_none_value' => '',
                            'name' => 'bcp_login_page',
                            'selected' => fetch_data_option('bcp_login_page'),
                        )
                );
                ?>
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Registration Page', 'bcp_portal'); ?></label></th>
            <td>
                <?php
                wp_dropdown_pages(
                        array(
                            'show_option_none' => __('Select a page', 'bcp_portal'),
                            'option_none_value' => '',
                            'name' => 'bcp_registration_page',
                            'selected' => fetch_data_option('bcp_registration_page'),
                        )
                );
                ?>
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Profile Page', 'bcp_portal'); ?></label></th>
            <td>
                <?php
                wp_dropdown_pages(
                        array(
                            'show_option_none' => __('Select a page', 'bcp_portal'),
                            'option_none_value' => '',
                            'name' => 'bcp_profile_page',
                            'selected' => fetch_data_option('bcp_profile_page'),
                        )
                );
                ?>
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Forgot Password Page', 'bcp_portal'); ?></label></th>
            <td>
                <?php
                wp_dropdown_pages(
                        array(
                            'show_option_none' => __('Select a page', 'bcp_portal'),
                            'option_none_value' => '',
                            'name' => 'bcp_forgot_password_page',
                            'selected' => fetch_data_option('bcp_forgot_password_page'),
                        )
                );
                ?>
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Reset Password Page', 'bcp_portal'); ?></label></th>
            <td>
                <?php
                wp_dropdown_pages(
                        array(
                            'show_option_none' => __('Select a page', 'bcp_portal'),
                            'option_none_value' => '',
                            'name' => 'bcp_reset_password_page',
                            'selected' => fetch_data_option('bcp_reset_password_page'),
                        )
                );
                ?>
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Manage Page', 'bcp_portal'); ?></label></th>
            <td>
                <?php
                wp_dropdown_pages(
                        array(
                            'show_option_none' => __('Select a page', 'bcp_portal'),
                            'option_none_value' => '',
                            'name' => 'bcp_manage_page',
                            'selected' => fetch_data_option('bcp_manage_page'),
                        )
                );
                ?>
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('List Page', 'bcp_portal'); ?></label></th>
            <td>
                <?php
                wp_dropdown_pages(
                        array(
                            'show_option_none' => __('Select a page', 'bcp_portal'),
                            'option_none_value' => '',
                            'name' => 'bcp_list_page',
                            'selected' => fetch_data_option('bcp_list_page'),
                        )
                );
                ?>
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Detail Page', 'bcp_portal'); ?></label></th>
            <td>
                <?php
                wp_dropdown_pages(
                        array(
                            'show_option_none' => __('Select a page', 'bcp_portal'),
                            'option_none_value' => '',
                            'name' => 'bcp_detail_page',
                            'selected' => fetch_data_option('bcp_detail_page'),
                        )
                );
                ?>
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Dashboard Page', 'bcp_portal'); ?></label></th>
            <td>
                <?php
                wp_dropdown_pages(
                        array(
                            'show_option_none' => __('Select a page', 'bcp_portal'),
                            'option_none_value' => '',
                            'name' => 'bcp_dashboard_page',
                            'selected' => fetch_data_option('bcp_dashboard_page'),
                        )
                );
                ?>
            </td>
        </tr>
        <!-- TR End -->
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Add/Edit Page', 'bcp_portal'); ?></label></th>
            <td>
                <?php
                wp_dropdown_pages(
                        array(
                            'show_option_none' => __('Select a page', 'bcp_portal'),
                            'option_none_value' => '',
                            'name' => 'bcp_add_edit_page',
                            'selected' => fetch_data_option('bcp_add_edit_page'),
                        )
                );
                ?>
            </td>
        </tr>
        <!-- TR End -->
    </tbody>
</table>
<!-- form-table End -->