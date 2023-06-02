<?php

/**
 * The file that defines the core plugin class
 *
 * @link       https://#
 * @since      1.0.0
 * @package    Wp_Profile
 * @subpackage Wp_Profile/includes
 */
class Wp_Profile {

	/**  Maintains and registers all hooks for the plugin.
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Profile_Loader    $loader   
	 */
	protected $loader;

	/**
	 *  The string used to uniquely identify this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name   
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version  
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WP_PROFILE_VERSION' ) ) {
			$this->version = WP_PROFILE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-profile';

		$this->define_constants();
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Define the constant for this plugin.
	 * @since    1.0.0
	 * @access   private
	 */

	private function define_constants() {
        define('PLUGIN_DIR_PATH', plugin_dir_path(dirname(__FILE__)));
        define('TEMPLATE_PATH', plugin_dir_path(__FILE__) . 'templates/');
        define('ASSET_URL', plugins_url('/public/', dirname(__FILE__)));
        define('PROFILE', 'profile');
        define('EDUCATION', 'education');
        define('SKILL', 'skill');
        define('PROFILE_DOB', 'profile_DOB');
        define('PROFILE_HOBBIES', 'profile_Hobbies');
        define('PROFILE_INTEREST', 'profile_Interest');
        define('PROFILE_EXP', 'profile_expericence');
        define('PROFILE_RATING', 'profile_rating');
        define('PROFILE_JOBS', 'profile_jobs');
    }


	/**
	 * Load the required dependencies for this plugin.
	 * @since    1.0.0
	 * @access   private
	 */


	private function load_dependencies() {
		/**
		 * The class responsible for render the template files 
		 * of the plugin.
		 */
		require_once PLUGIN_DIR_PATH . '/includes/template-helper.php';
		/**
		 * The class responsible for defining all actions functionality
		 * of the plugin.
		 */
		require_once PLUGIN_DIR_PATH . 'includes/actions.php';
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once PLUGIN_DIR_PATH . 'includes/class-wp-profile-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once PLUGIN_DIR_PATH . 'includes/class-wp-profile-i18n.php';

		
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once PLUGIN_DIR_PATH . 'admin/class-wp-profile-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once PLUGIN_DIR_PATH . 'public/class-wp-profile-public.php';

		$this->loader = new Wp_Profile_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Wp_Profile_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */


	private function define_admin_hooks() {
		$plugin_admin = new Wp_Profile_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */


	private function define_public_hooks() {
		$plugin_public = new Wp_Profile_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_action = new Wp_Profile_Actions( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_shortcode('profile_list', $plugin_action, 'profile_list_callback');
		$this->loader->add_filter('single_template', $plugin_action, 'profile_single_callback');
		$this->loader->add_action('wp_ajax_wp_profile_filter', $plugin_action, 'wp_profile_filter_callback');
        $this->loader->add_action('wp_ajax_nopriv_wp_profile_filter', $plugin_action, 'wp_profile_filter_callback');
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */


	public function run() {
		$this->loader->run();
	}


	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */


	public function get_plugin_name() {
		return $this->plugin_name;
	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Profile_Loader    Orchestrates the hooks of the plugin.
	 */


	public function get_loader() {
		return $this->loader;
	}


	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */


	public function get_version() {
		return $this->version;
	}

}
