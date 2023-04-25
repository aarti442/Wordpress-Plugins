<h2><?php _e('Layout settings', 'bcp_portal'); ?></h2>
<!-- form-table Start -->
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><label><?php _e('Portal Template', 'bcp_portal'); ?></label></th>
            <td>
                <?php $crmtemplate = fetch_data_option('bcp_portal_template'); ?>
                <select name="bcp_portal_template" id="bcp_portal_template">
                    <option value="1" <?php echo $crmtemplate == '1' ? 'selected="selected"' : '' ?>><?php _e('CRM Standalone Template', 'bcp_portal'); ?></option>
                    <option value="0" <?php echo $crmtemplate == '0' ? 'selected="selected"' : '' ?>><?php _e('Default Template', 'bcp_portal'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php _e('Theme Color', 'bcp_portal'); ?></label></th>
            <td>
                <input type="text" name="bcp_theme_color" value="<?php echo fetch_data_option('bcp_theme_color'); ?>" class="sfcp-color-field" />
            </td>
        </tr>

    </tbody>
</table>
<!-- form-table End -->