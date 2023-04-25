<?php if (isset($kb_package_status->status) && $kb_package_status->status == '1') { ?>
    <h2><?php _e('Knowledge Base Settings', 'bcp_portal'); ?></h2>
    <hr />
    <!-- form-table Start -->
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><label><?php _e('Organization Prefix', 'bcp_portal'); ?></label></th>
                <td>
                    <input type="text" name="bcp_org_prefix" value="<?php echo fetch_data_option('bcp_org_prefix'); ?>" />
                    <p class="description"><?php _e('Please refer user manual to get prefix, User prefix without two underscores, "__"', 'bcp_portal'); ?></p>
                </td>
            </tr>
        </tbody>
    </table>
    <!-- form-table End -->
    <?php
}