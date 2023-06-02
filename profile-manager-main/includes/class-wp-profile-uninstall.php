<?php

/**
 * Fired during plugin uninstall
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Wp_Profile
 * @subpackage Wp_Profile/includes
 * @author     Aarti
 */

class Wp_Profile_Uninstall {

	/**
	 * @since    1.0.0
	 */
	public static function uninstall() {
		unregister_post_type( 'profile' );
	}
}
