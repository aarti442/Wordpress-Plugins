<h2><?php _e('Password settings', 'bcp_portal'); ?></h2>
<!-- form-table Start -->
<table class="form-table">
    <tbody>
        <!-- TR Start -->
        <tr class="hide_class">
            <th scope="row"><label for="bcp_login_password_enable"><?php _e('Enable', 'bcp_portal'); ?></label></th>
            <td>
                <?php $bcp_login_password_enable = fetch_data_option('bcp_login_password_enable'); ?>
                <input value="0" name="bcp_login_password_enable" type="hidden">
                <input id="bcp_login_password_enable" value="1" name="bcp_login_password_enable" type="checkbox" <?php echo checked($bcp_login_password_enable, "1") ?>>
            </td>
        </tr>
        <!-- TR End -->
        <?php
        if (fetch_data_option('bcp_maximum_login_attempts') === FALSE) {
            add_option('bcp_maximum_login_attempts', '3');
        }

        $lockout_disabled = "disabled=''";
        if ($bcp_login_password_enable) {
            $lockout_disabled = "";
        }
        ?>
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Maximum Login Attempts', 'bcp_portal'); ?></label></th>
            <td>
                <input id="bcp_maximum_login_attempts" <?php echo $lockout_disabled; ?> type="number" name="bcp_maximum_login_attempts" value="<?php echo fetch_data_option('bcp_maximum_login_attempts'); ?>" min="1">
            </td>
        </tr>
        <!-- TR End -->
        <?php
        if (fetch_data_option('bcp_lockout_effective_period') === FALSE) {
            add_option('bcp_lockout_effective_period', '1');
        }
        ?>
        <!-- TR Start -->
        <tr>
            <th scope="row"><label><?php _e('Lockout Effective Period', 'bcp_portal'); ?></label></th>
            <td>
                <?php $lockout_effective_period = array("1", "3", "5", "10"); ?>
                <select <?php echo $lockout_disabled; ?> name="bcp_lockout_effective_period" id="bcp_lockout_effective_period">
                    <?php
                    foreach ($lockout_effective_period as $value) {
                        $effective_selected = "";
                        if ($value == fetch_data_option("bcp_lockout_effective_period")) {
                            $effective_selected = "selected='selected'";
                        }
                        ?>
                        <option value="<?php echo $value; ?>" <?php echo $effective_selected; ?> ><?php echo $value . " " . __('Minutes', 'bcp_portal'); ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <!-- TR End -->
    </tbody>
</table>
<!-- form-table Start -->