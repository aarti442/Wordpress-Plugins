<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Wp_Profile
 * @subpackage Wp_Profile/public
 * @author     Aarti
 */

class Wp_Profile_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version   
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       
	 * @param      string    $version    
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is load the styles that require for list the data
		 */
		wp_enqueue_style('bootstrap-css',  plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css');
		wp_enqueue_style('jquery-select2-css',  plugin_dir_url( __FILE__ ) . 'css/select2.min.css');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-profile-public.css', array(), $this->version, 'all' );
		
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is load the js that require for list the data
		 */
		wp_enqueue_script('jquery-js', plugin_dir_url( __FILE__ ) . 'js/jquery-3.3.1.min.js',array( 'jquery' ), $this->version, true);
		wp_enqueue_script('jquery-select2-js', plugin_dir_url( __FILE__ ) . 'js/select2.min.js',array( 'jquery' ), $this->version, true);
		wp_enqueue_script('bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js',array( 'jquery' ), $this->version, true);	
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-profile-public.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'ajax_object',array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

}
