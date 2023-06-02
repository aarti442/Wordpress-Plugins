<?php
 /**
 * The plugin bootstrap file
 *
 * @link              #
 * @since             1.0.0
 * @package           Wp_Profile
 *
 * @wordpress-plugin
 * Plugin Name:       wpprofile
 * Plugin URI:        #
 * Description:       This is Profile manager Plugin as test challenge from multidots. Please usr this Shortcode to show the data [profile_list per_page="5"]
 * Version:           1.0.0
 * Author:            Aarti
 * Author URI:        #
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-profile
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WP_PROFILE_VERSION', '1.0.0' );
define('PLUGIN_FILE', __FILE__);
define('PLUGIN_DIR', __DIR__);
/**
 * The code that runs during plugin activation.
 * includes/class-wp-profile-activator.php
 */
function activate_wp_profile() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-profile-activator.php';
	Wp_Profile_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * includes/class-wp-profile-deactivator.php
 */
function deactivate_wp_profile() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-profile-deactivator.php';
	Wp_Profile_Deactivator::deactivate();
}

/**
 * The code that runs during plugin uninstall.
 * This action is documented in includes/class-wp-profile-deactivator.php
 */
function uninstall_wp_profile() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-profile-uninstall.php';
	Wp_Profile_Uninstall::uninstall();
}

register_activation_hook( __FILE__, 'activate_wp_profile' );
register_deactivation_hook( __FILE__, 'deactivate_wp_profile' );
register_uninstall_hook( __FILE__, 'uninstall_wp_profile' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-profile.php';
require plugin_dir_path( __FILE__ ) . 'includes/post-types/profile.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_wp_profile() {

	$plugin = new Wp_Profile();
	$plugin->run();

}
run_wp_profile();
