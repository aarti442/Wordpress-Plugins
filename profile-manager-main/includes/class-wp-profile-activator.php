<?php

/**
 * Fired during plugin activation
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Wp_Profile
 * @subpackage Wp_Profile/includes
 * @author     Aarti
 */

class Wp_Profile_Activator {

	/**
	 * @since    1.0.0
	 */
	public static function activate() {
		profile_init();
		flush_rewrite_rules();
	}
}
