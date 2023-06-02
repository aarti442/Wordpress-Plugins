<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Wp_Profile
 * @subpackage Wp_Profile/includes
 * @author     Aarti
 */

class Wp_Profile_Loader {

	/**
	 * The actions registered with WordPress to fire when the plugin l
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    
	 */
	protected $actions;

	/**
	 * The filters registered with WordPress to fire when the plugin loads.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    
	 */
	protected $filters;

	/**
	 * The shortcodes registered with WordPress to fire when the plugin loads.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $shortcodes    
	 */
	protected $shortcodes;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();
		$this->shortcodes = array();

	}

	/**
     * Add a new Shortcode to the collection to be registered with WordPress. 
     */
	public function add_shortcode($shortcode_name, $component, $callback) {
        $this->shortcodes = $this->add_shortcode_queue($this->shortcodes, $shortcode_name, $component, $callback);
		
    }

	/**
     * Add a new Shortcode to the queue to be registered with WordPress. 
	 * 
	 * @since    1.0.0
	 * @param    $shortcodes               
	 * @param    $shortcode_name              
	 * @param    $component                             
     */
    
    private function add_shortcode_queue($shortcodes, $shortcode_name, $component, $callback) {
        $shortcodes[] = array(
            'shortcode' => $shortcode_name,
            'component' => $component,
            'callback' => $callback
        );
	
        return $shortcodes;
    }
	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    $hook               
	 * @param    $component              
	 * @param    $callback             
	 * @param    $priority                
	 * @param    $accepted_args                  
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    $hook             
	 * @param    $component        
	 * @param    $callback         
	 * @param    $priority        
	 * @param    $accepted_args    
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    $hooks              
	 * @param    $hook             
	 * @param    $component        
	 * @param    $callback         
	 * @param    $priority        
	 * @param    $accepted_args    
	 */

	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);
		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */


	public function run() {

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ($this->shortcodes as $shortcode) {
            add_shortcode($shortcode['shortcode'], array($shortcode['component'], $shortcode['callback']));
        }

	}

}