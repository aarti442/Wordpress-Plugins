<h2><?php _e('Menu Settings', 'bcp_portal'); ?></h2>
<?php
$locations = get_nav_menu_locations();
$locations = array_filter($locations);
if (!empty($locations)) {
    ?>
    <!-- form-table Start -->
    <table class="form-table">
        <tbody>
            <!-- TR Start -->
            <tr>
                <th scope='row'><label><?php _e('Menu Location', 'bcp_portal'); ?></label></th>
                <td>
                    <?php
                    if (fetch_data_option('bcp_portal_menuLocation') != NULL) {
                        $crm_menuLocation = fetch_data_option('bcp_portal_menuLocation');
                    } else {
                        $crm_menuLocation = '';
                    }
                    $portal_menus_locations = get_registered_nav_menus();
                    ?>
                    <select name="bcp_portal_menuLocation" id="bcp_portal_menuLocation">
                        <option value="" ><?php _e('select', 'bcp_portal'); ?></option>
                        <?php
                        foreach ($portal_menus_locations as $key => $value) {
                            if (!isset($locations[$key])) {
                                continue;
                            }
                            if ($key == $crm_menuLocation) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                            ?>
                            <option value="<?php echo $key; ?>" <?php echo $selected; ?> ><?php echo $value; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <!-- TR End -->
            <!-- TR Start -->
            <tr>
                <th scope='row'><label><?php _e('Menu Selection', 'bcp_portal'); ?></label></th>
                <td>
                    <?php
                    if (fetch_data_option('bcp_portal_menuSelection') != NULL) {
                        $crm_menuSelection = fetch_data_option('bcp_portal_menuSelection');
                    } else {
                        $crm_menuSelection = '';
                    }
                    ?>
                    <select name="bcp_portal_menuSelection" id="bcp_portal_menuSelection">
                        <option value="" ><?php _e('select', 'bcp_portal'); ?></option>
                        <?php
                        $portal_menus_selection = wp_get_nav_menus();
                        foreach ($portal_menus_selection as $objMenu) {
                            if ($objMenu->term_id == $crm_menuSelection) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                            ?>
                            <option value="<?php echo $objMenu->term_id; ?>" <?php echo $selected; ?> ><?php echo $objMenu->name; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <!-- TR End -->
        </tbody>
    </table>
    <!-- form-table End -->
    <?php
} else {
    echo "<p>" . sprintf(__("Please select at least one menu location in menu %s here.", 'bcp_portal'), home_url('wp-admin/nav-menus.php?action=locations')) . "</p>";
}