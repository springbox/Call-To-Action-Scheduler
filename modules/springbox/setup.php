<?php

if ( ! class_exists( 'Springbox_Setup' ) ) {

	class Springbox_Setup {

		private 
			$hook;

		function __construct() {
			global $sb_setup_action;

			//Important, this must be manually set in each module
			$this->hook = 'springbox_wordpress_plugin_framework';

			if ( isset( $sb_setup_action ) ) {
				
				switch ( $sb_setup_action ) {

					case 'activate':
						$this->execute_activate();
						break;
					case 'upgrade':
						$this->execute_upgrade();
						break;
					case 'deactivate':
						$this->execute_deactivate();
						break;
					case 'uninstall':
						$this->execute_uninstall();
						break;

				}

			} else {
				wp_die( 'error' );
			}

		}

		/**
		 * Execute module activation
		 * 
		 * @return void
		 */
		function execute_activate() {
			global $sb_hook;

		}

		/**
		 * Execute module deactivation
		 * 
		 * @return void
		 */
		function execute_deactivate() {
			global $sb_hook;

		}

		/**
		 * Execute module uninstall
		 * 
		 * @return void
		 */
		function execute_uninstall() {
			global $sb_hook;

			$this->execute_deactivate();

		}

		/**
		 * Execute module upgrade
		 * 
		 * @return void
		 */
		function execute_upgrade() {
			global $sb_hook;

		}

	}

}

new Springbox_Setup();