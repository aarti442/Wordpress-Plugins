<?php
/**
 * Define the internationalization functionality
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Wp_Profile
 * @subpackage Wp_Profile/includes
 * @author     Aarti 
 */

class Wp_Profile_i18n {
	/**
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'wp-profile',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}