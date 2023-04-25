<?php
defined('ABSPATH') or die('No script kiddies please!');

class BCPCustomerPortalLogSettings {

    private $plugin_name;
    private $version;
    private $bcp_helper;

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->bcp_helper = new BCPCustomerPortalHelper($this->plugin_name, $this->version);
    }

    /**
     * Create backend admin menus
     *
     * @return void
     */
    public function bcp_create_sub_menu() {
        add_submenu_page('bcp-settings', __('Manage Logs', 'bcp_portal'), __('Manage Logs', 'bcp_portal'), 'manage_options', 'bcp-manage-logs', array($this, 'bcp_manage_logs_callback'));
    }

    /*
     * Log Page settings callback
     */

    public function bcp_manage_logs_callback() {

        if (isset($_REQUEST['submit'])) {
            if (fetch_data_option("bcp_enable_logs") === FALSE) {
                add_option("bcp_enable_logs", $_REQUEST['bcp_enable_logs']);
            } else {
                update_option("bcp_enable_logs", $_REQUEST['bcp_enable_logs']);
            }
            
            if (fetch_data_option("bcp_enable_success_logs") === FALSE) {
                add_option("bcp_enable_success_logs", $_REQUEST['bcp_enable_success_logs']);
            } else {
                update_option("bcp_enable_success_logs", $_REQUEST['bcp_enable_success_logs']);
            }
        }
        wp_enqueue_style($this->plugin_name . "bcp-admin-style");
        wp_enqueue_script($this->plugin_name . "bcp-admin");
        add_thickbox();

        $licence_key = fetch_data_option('bcp_licence_key');
        ?>
        <!-- wrap Start -->
        <div class="wrap">
            <?php if ($licence_key != '') { ?>
                <h1><?php _e('Salesforce Customer Portal Logs', 'bcp_portal'); ?></h1>
                <!-- bcp_portal_log_form Start -->
                <form method="post" class="form-wrap" id="bcp_portal_log_form">
                    <h2><?php _e('Enable Log Settings', 'bcp_portal'); ?></h2>
                    <hr />
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><label><?php _e('Enable Logs', 'bcp_portal'); ?> <?php echo "<sup>*</sup>"; ?></label></th>
                                <td>
                                    <?php $bcp_enable_logs = ( fetch_data_option('bcp_enable_logs') == 1 ) ? 1 : 0; ?>  
                                    <input value="0" name="bcp_enable_logs" type="hidden">
                                    <input id="bcp_enable_logs" value="1" name="bcp_enable_logs" type="checkbox" <?php echo checked($bcp_enable_logs, "1") ?> >
                                   
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label><?php _e('Enable Success Logs', 'bcp_portal'); ?> <?php echo "<sup>*</sup>"; ?></label></th>
                                <td>
                                    <?php $bcp_enable_success_logs = ( fetch_data_option('bcp_enable_success_logs') == 1 ) ? 1 : 0; ?>
                                    <input value="0" name="bcp_enable_success_logs" type="hidden">
                                    <input id="bcp_enable_success_logs" value="1" name="bcp_enable_success_logs" type="checkbox" <?php echo checked($bcp_enable_success_logs, "1") ?> >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p><input type="submit" class="button-primary" name="submit" value="<?php _e('Save Changes', 'bcp_portal'); ?>" /></p>
                </form>
                <!-- bcp_portal_log_form End -->
                <?php
                if (fetch_data_option('bcp_enable_logs') == 1) {
                    $file_dir = BCP_DEBUG_PATH;
                    $files_array = glob($file_dir . "debug_*.log");
                    $total_files = count($files_array);
                    $paged = ( isset($_REQUEST['bcp_page']) && is_numeric($_REQUEST['bcp_page']) ) ? $_REQUEST['bcp_page'] : 1;

                    $current_page = $total_files - $paged + 1;
                    $file_path = $file_dir . "debug_" . $current_page . ".log";

                    $next_page = $paged + 1;
                    $prev_page = $paged - 1;
                    ?>
                    <h2><?php _e('Log Console', 'bcp_portal'); ?></h2>
                    <p>
                        <?php
                        if (file_exists($file_path) && !in_array(substr(sprintf('%o', fileperms($file_dir)), -4), array('0777', '0755'))) {
                            _e('To save debug log file permission require to be "0777" (read, write, & execute for owner, group and others). Current file permission is ', 'bcp_portal');
                            echo substr(sprintf('%o', fileperms($file_dir)), -4);
                        }
                        ?>
                    </p>  
                    <hr />
                    <?php if (isset($_REQUEST['success']) && isset($_SESSION['bcp_success_clear_logs'])) { ?>
                        <div class="notice notice-success is-dismissible"><p><?php echo $_SESSION['bcp_success_clear_logs']; ?></p></div>
                        <?php
                        unset($_SESSION['bcp_success_clear_logs']);
                    }
                    ?>
                    <ul class="subsubsub">
                        <?php if ($total_files > 0) { ?>
                            <li><a href="<?php echo site_url(); ?>/wp-admin/admin-post.php?action=bcp_clear_logs"><?php _e("Clear Logs", 'bcp_portal'); ?></a></li>
                            <?php
                        }
                        if ($prev_page > 0 && $prev_page < $total_files) {
                            ?>
                            <li><a href="<?php echo site_url() . "/wp-admin/admin.php?page=bcp-manage-logs&bcp_page=" . $prev_page; ?>"><?php _e("Prev", 'bcp_portal'); ?></a></li>
                            <?php
                        }
                        if ($next_page <= $total_files) {
                            ?>
                            <li><a href="<?php echo site_url() . "/wp-admin/admin.php?page=bcp-manage-logs&bcp_page=" . $next_page; ?>"><?php _e("Next", 'bcp_portal'); ?></a></li>
                        <?php } ?>
                    </ul>
                    <!-- sfcp-log Start -->
                    <div class="sfcp-log">
                        <?php
                        if (file_exists($file_path) && filesize($file_path) > 0) {
                            $file1 = fopen($file_path, "r");
                            ?>
                            <!-- table widefat Start -->
                            <table class="widefat fixed">
                                <thead>
                                    <tr>
                                        <th class="manage-column"><?php _e("No.", 'bcp_portal'); ?></th>
                                        <th class="manage-column"><?php _e("URL", 'bcp_portal'); ?></th>
                                        <th class="manage-column"><?php _e("Method", 'bcp_portal'); ?></th>
                                        <th class="manage-column"><?php _e("Request Time", 'bcp_portal'); ?></th>
                                        <th class="manage-column"><?php _e("Request", 'bcp_portal'); ?></th>
                                        <th class="manage-column"><?php _e("Response Time", 'bcp_portal'); ?></th>
                                        <th class="manage-column"><?php _e("Response", 'bcp_portal'); ?></th>
                                        <th class="manage-column"><?php _e("Status", 'bcp_portal'); ?></th>
                                        <th class="manage-column"><?php _e("Action", 'bcp_portal'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    while ($content = fgets($file1)) {
                                        $i++;
                                        $data = json_decode($content);
                                        $alternate = ( $i % 2 == 0 ) ? "alternate" : "";
                                        ?>
                                        <tr  class="<?php echo $alternate; ?>">
                                            <td><?php echo $i; ?></td>
                                            <td><span class="sfcp-long-content sfcp_req_url"><?php echo $data->request_url; ?></span></td>
                                            <td class="sfcp_req_method"><?php echo $data->method; ?></td>
                                            <td class="sfcp_req_time"><?php echo date("M d, Y H:i:s", strtotime($data->request_time)); ?></td>
                                            <td><span class="sfcp-long-content sfcp_req_data"><?php echo sanitize_text_field($data->request); ?></span></td>
                                            <td class="sfcp_res_time"><?php echo date("M d, Y H:i:s", strtotime($data->response_time)); ?></td>
                                            <td><span class="sfcp-long-content sfcp_res_data"><?php echo sanitize_text_field($data->response); ?></span></td>
                                            <td class="sfcp_res_status">
                                                <?php
                                                if ($data->status) {
                                                    echo "<span class='sfcp_log_success_label'>" . __("Success", 'bcp_portal') . "</span>";
                                                } else {
                                                    echo "<span class='sfcp_log_failed_label'>" . __("Failed", 'bcp_portal') . "</span>";
                                                }
                                                echo "<p> HTTP/1.1 " . $data->status_code . "</p>";
                                                ?>
                                            </td>
                                            <td>  
                                                <a href='/#TB_inline?width=800&height=215&inlineId=SFCP_FULL_LOG' class="thickbox sfcp_log_view_more"><?php _e("View", 'bcp_portal'); ?></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    fclose($file1);
                                    ?>
                                </tbody>
                            </table>
                            <!-- table widefat End -->
                            <!-- SFCP_FULL_LOG Start -->
                            <div id="SFCP_FULL_LOG" style="display:none;">
                                <table class="widefat fixed sfcp-table-full-log">
                                    <tbody>
                                        <tr>
                                            <th class="check-columnname"><?php _e("URL", 'bcp_portal'); ?></th>
                                            <td id="thick_sfcp_req_url"></td>
                                        </tr>
                                        <tr class="alternate">
                                            <th class="check-columnname"><?php _e("Method", 'bcp_portal'); ?></th>
                                            <td id="thick_sfcp_req_method"></td>
                                        </tr>
                                        <tr>
                                            <th class="check-columnname"><?php _e("Request Time", 'bcp_portal'); ?></th>
                                            <td id="thick_sfcp_req_time"></td>
                                        </tr>
                                        <tr class="alternate">
                                            <th class="check-columnname"><?php _e("Request", 'bcp_portal'); ?></th>
                                            <td id="thick_sfcp_req_data"></td>
                                        </tr>
                                        <tr>
                                            <th class="check-columnname"><?php _e("Response Time", 'bcp_portal'); ?></th>
                                            <td id="thick_sfcp_res_time"></td>
                                        </tr>
                                        <tr class="alternate">
                                            <th class="check-columnname"><?php _e("Response", 'bcp_portal'); ?></th>
                                            <td id="thick_sfcp_res_data"></td>
                                        </tr>
                                        <tr>
                                            <th class="check-columnname"><?php _e("Status", 'bcp_portal'); ?></th>
                                            <td id="thick_sfcp_res_status"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- SFCP_FULL_LOG End -->
                        <?php } else { ?>
                            <p><?php _e("No record(s) found.", 'bcp_portal'); ?></p>
                        <?php } ?>
                    </div>
                    <!-- sfcp-log End -->
                <?php } ?>
            <?php } else { ?>
                <div class="notice notice-error is-dismissible"><p><?php _e("Please check license key is configured or not.", 'bcp_portal'); ?></p></div>
                    <?php } ?>
        </div>
        <!-- wrap End -->
        <?php
    }
}