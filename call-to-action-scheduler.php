<?php
/*
	Plugin Name: Call to Action Scheduler
	Plugin URI: http://springbox.com
	Description: Schedule call-to-action links in a widget or with a shortcode
	Version: 0.0.2
	Text Domain: call_to_action_scheduler
	Domain Path: /languages
	Author: Springbox
	Author URI: http://springbox.com
	License: GPLv2
	Copyright 2013  Springbox  (email : opensource@springbox.com)
*/

if ( ! class_exists( 'SB_Call_To_Action_Scheduler' ) ) {


	/**
	 * Plugin class used to create plugin object and load both core and needed modules
	 */
	final class SB_Call_To_Action_Scheduler {

		private static $instance = null; //instantiated instance of this plugin

		public //see documentation upon instantiation 
			$core,
			$dashboard_menu_title,
			$dashboard_page_name,
			$globals,
			$menu_icon,
			$menu_name,
			$settings_menu_title,
			$settings_page,
			$settings_page_name,
			$top_level_menu;

		/**
		 * Default plugin execution used for settings defaults and loading components
		 * 
		 * @return void
		 */
		private function __construct() {

			//Set plugin defaults
			$this->globals = array(
				'plugin_build'			=> 1, //plugin build number - used to trigger updates
				'plugin_file'			=> __FILE__, //the main plugin file
				'plugin_access_lvl' 	=> 'manage_options', //Access level required to access plugin options
				'plugin_dir' 			=> plugin_dir_path( __FILE__ ), //the path of the plugin directory
				'plugin_homepage' 		=> 'http://wordpress.org/plugins/call-to-action-scheduler/', //The plugins homepage on WordPress.org
				'plugin_hook'			=> 'call_to_action_scheduler', //the hook for text calls and other areas
				'plugin_name' 			=> __( 'Call to Action Scheduler', 'call_to_action_scheduler' ), //the name of the plugin
				'plugin_url' 			=> plugin_dir_url( __FILE__ ), //the URL of the plugin directory
				'support_page' 			=> 'http://wordpress.org/support/plugin/call-to-action-scheduler', //address of the WordPress support forums for the plugin
			);

			$this->top_level_menu = false; //true if top level menu, else false
			$this->menu_name = __( 'Call to Action Scheduler', $this->globals['plugin_hook'] ); //main menu item name			

			//load core functionality for admin use
			require_once( $this->globals['plugin_dir'] . 'inc/class-call-to-action-scheduler-core.php' );
			$this->core = Call_To_Action_Scheduler_Core::start( $this );

			//load modules
			$this->load_modules();

			//builds admin menus after modules are loaded
			if ( is_admin() ) {
				$this->core->build_admin(); 
			}
			
		}

		/**
		 * Loads required plugin modules
		 *
		 * Note: Do not modify this area other than to specify modules to load. 
		 * Build all functionality into the appropriate module.
		 * 
		 * @return void
		 */
		public function load_modules() {

			//load Default module
			require_once( $this->globals['plugin_dir'] . 'modules/call-to-action-scheduler-module/class-call-to-action-scheduler-module.php' );
			Call_To_Action_Scheduler_Module::start( $this->core );

			//load Springbox module
			require_once( $this->globals['plugin_dir'] . 'modules/springbox/class-springbox.php' );
			Springbox::start( $this->core );
			
		}

		/**
		 * Start the plugin
		 * 
		 * @return SB_Call_To_Acition_Scheduler     The instance of the plugin
		 */
		public static function start() {

			if ( ! isset( self::$instance ) || self::$instance === null ) {
				self::$instance = new self;
			}

			return self::$instance;

		}

	}

}

SB_Call_To_Action_Scheduler::start();
