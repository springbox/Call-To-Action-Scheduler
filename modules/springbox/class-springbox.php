<?php
/**
 * Brand plugins with Springbox sidebar items in the admin
 * 
 * @version 1.0
 */

if ( ! class_exists( 'Springbox') ) {

	class Springbox {

		private static $instance = null;

		private 
			$core;

		private function __construct( $core ) {

			$this->core = $core;

			//add sharing reminder
			add_action( 'admin_init', array( $this, 'share_reminder' ) );

			add_action( $this->core->plugin->globals['plugin_hook'] . '_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) );

		}

		/**
		 * Add meta boxes to primary options pages
		 * 
		 * @param array $available_pages array of available page_hooks
		 */
		function add_admin_meta_boxes( $available_pages ) {

			foreach ( $available_pages as $page ) {
				
				//add metaboxes
				add_meta_box( 
					'sb_publicize', 
					__( 'Like this plugin? Spread the word', $this->core->plugin->globals['plugin_hook'] ),
					array( $this, 'metabox_sidebar_publicize' ),
					$page,
					'side',
					'core'
				);

				add_meta_box( 
					'sb_contact_info', 
					__( 'Springbox on the Web', $this->core->plugin->globals['plugin_hook'] ),
					array( $this, 'metabox_sidebar_contact' ),
					$page,
					'side',
					'core'
				);

				add_meta_box( 
					'sb_plugin_support', 
					__( 'Need help?', $this->core->plugin->globals['plugin_hook'] ),
					array( $this, 'metabox_sidebar_support' ),
					$page,
					'side',
					'core'
				);

			}

		}

		/**
		 * Display (and hide) donation reminder
		 *
		 * Adds reminder to donate or otherwise support on dashboard
		 *
		 * @return void
		 **/
		function share_reminder() {
		
			global $blog_id; //get the current blog id
			
			$options = get_option( $this->core->plugin->globals['plugin_hook'] . '_data' );

			//Gotta make sure this is available when needed
			global $plugname;
			global $plughook;
			global $plugopts;
			$plugname = $this->core->plugin->globals['plugin_name'];
			$plughook = $this->core->plugin->globals['plugin_hook'];
			$plugopts = admin_url( 'options-general.php?page=' . $this->core->plugin->globals['plugin_hook'] );
			
			//display the notifcation if they haven't turned it off and they've been using the plugin at least 30 days
			if ( ! isset( $options['no-nag'] ) && isset( $options['activatestamp'] ) && $options['activatestamp'] < ( current_time( 'timestamp' ) - 2952000 ) ) {
			
				if ( ! function_exists( 'sb_share_notice' ) ) {
			
					function sb_share_notice() {
				
						global $plugname;
						global $plughook;
						global $plugopts;
					
					    echo '<div class="updated">' . PHP_EOL .
							'<p>' . __( 'It looks like you\'ve been enjoying', $plughook ) . ' ' . $plugname . ' ' . __( 'for at least 30 days. Would you please consider telling your friends about it?', $plughook ) . '</p> <p><input type="button" class="button " value="' . __( 'Rate it 5★\'s', $plughook ) . '" onclick="document.location.href=\'?' . $plughook . '_lets_rate=yes&_wpnonce=' .  wp_create_nonce( $plughook . '-reminder' ) . '\';">  <input type="button" class="button " value="' . __( 'Tell Your Followers', $plughook ) . '" onclick="document.location.href=\'?' . $plughook . '_lets_tweet=yes&_wpnonce=' .  wp_create_nonce( $plughook . '-reminder' ) . '\';">  <input type="button" class="button " value="' . __( 'Don\'t Bug Me Again', $plughook ) . '" onclick="document.location.href=\'?' . $plughook . '_share_nag=off&_wpnonce=' .  wp_create_nonce( $plughook . '-reminder' ) . '\';"></p>' . PHP_EOL .
					    	'</div>';
				    
					}
				
				}
				
				add_action( 'admin_notices', 'sb_share_notice' ); //register notification
				
			}
			
			//if they've clicked a button hide the notice
			if ( ( isset( $_GET[$this->core->plugin->globals['plugin_hook'] . '_share_nag'] ) || isset( $_GET[$this->core->plugin->globals['plugin_hook'] . '_lets_rate'] ) || isset( $_GET[$this->core->plugin->globals['plugin_hook'] . '_lets_tweet'] ) ) && wp_verify_nonce( $_REQUEST['_wpnonce'], $this->core->plugin->globals['plugin_hook'] . '-reminder' ) ) {

				$options = get_option( $this->core->plugin->globals['plugin_hook'] . '_data' );
				$options['no-nag'] = 1;
				update_option( $this->core->plugin->globals['plugin_hook'] . '_data', $options );
				remove_action( 'admin_notices', 'sb_share_notice' );
				
				//Go to the WordPress page to let them rate it.
				if ( isset( $_GET[$this->core->plugin->globals['plugin_hook'] . '_lets_rate'] ) ) {
					wp_redirect( $this->core->plugin->globals['plugin_homepage'], '302' );
				}
				
				//Compose a Tweet
				if ( isset( $_GET[$this->core->plugin->globals['plugin_hook'] . '_lets_tweet'] ) ) {
					wp_redirect( 'http://twitter.com/home?status=' . urlencode( 'I use ' . $this->core->plugin->globals['plugin_name'] . ' for WordPress by @Springbox and you should too - ' . $this->core->plugin->globals['plugin_homepage'] ) , '302' );
				}
				
			}
			
		}

		/**
		 * Build and echo the content sidebar metabox
		 * 
		 * @return void
		 */
		public function metabox_sidebar_contact() {

			$content = '<ul>';
			$content .= '<li class="sbicon"><a href="http://www.springbox.com" target="_blank">' . __( 'Visit Springbox\'s homepage', $this->core->plugin->globals['plugin_hook'] ) . '</a></li>';
			$content .= '<li class="facebook"><a href="https://www.facebook.com/Springbox" target="_blank">' . __( 'Like Springbox on Facebook', $this->core->plugin->globals['plugin_hook'] ) . '</a></li>';
			$content .= '<li class="twitter"><a href="https://twitter.com/springbox" target="_blank">' . __( 'Follow Springbox on Twitter', $this->core->plugin->globals['plugin_hook'] ) . '</a></li>';
			$content .= '<li class="linkedin"><a href="https://www.linkedin.com/company/springbox" target="_blank">' . __( 'Follow Springbox on LinkedIn', $this->core->plugin->globals['plugin_hook'] ) . '</a></li>';
			$content .= '</ul>';

			echo $content;

		}

		/**
		 * Build and echo the "share this" sidebar metabox
		 * 
		 * @return void
		 */
		public function metabox_sidebar_publicize() {

			$content = __( 'Have you found this plugin useful? Please take a minute to tell others about it.', $this->core->plugin->globals['plugin_hook'] );
			$content .= '<ul>';
			$content .= '<li><a href="' . $this->core->plugin->globals['plugin_homepage'] . '" target="_blank">' . __( 'Rate it 5★\'s on WordPress.org', $this->core->plugin->globals['plugin_hook'] ) . '</a></li>';
			$content .= '<li>' . __( 'Talk about it on your site and link back to the ', $this->core->plugin->globals['plugin_hook'] ) . '<a href="' . $this->core->plugin->globals['plugin_homepage'] . '" target="_blank">' . __( 'plugin page.', $this->core->plugin->globals['plugin_hook'] ) . '</a></li>';
			$content .= '<li><a href="http://twitter.com/home?status=' . urlencode( 'I use ' . $this->core->plugin->globals['plugin_name'] . ' for WordPress by @Springbox and you should too - ' . $this->core->plugin->globals['plugin_homepage'] ) . '" target="_blank">' . __( 'Tweet about it. ', $this->core->plugin->globals['plugin_hook'] ) . '</a></li>';
			$content .= '</ul>';

			echo $content;

		}

		/**
		 * Build and echo the support sidebar metabox
		 * 
		 * @return void
		 */
		public function metabox_sidebar_support() {

			$content = __( 'If you need help getting this plugin working or have found a bug please visit the', $this->core->plugin->globals['plugin_hook'] ) . ' <a href="' . $this->core->plugin->globals['support_page'] . '" target="_blank">' . __( 'support forums', $this->core->plugin->globals['plugin_hook'] ) . '</a>.';

			echo $content;

		}

		/**
		 * Start the Springbox module
		 * 
		 * @param  SB_Core    $core     Instance of core plugin class
		 * @return Springbox 			The instance of the Springbox class
		 */
		public static function start( $core ) {

			if ( ! isset( self::$instance ) || self::$instance === null ) {
				self::$instance = new self( $core );
			}

			return self::$instance;

		}

	}

}