<?php
/**
 * Creates "Call to action" Content type along with associated widget and shortcode
 */

if ( ! class_exists( 'Call_To_Action_Scheduler_Module') ) {

	class Call_To_Action_Scheduler_Module {

		private static $instance = null;

		public 
			$core;

		/**
		 * Initiate all necessary functions
		 * 
		 * @param [plugin_core] $core 
		 * @return void
		 */
		private function __construct( $core ) {

			$this->core = $core;

			//Load the content type
			require_once( plugin_dir_path( __FILE__ ) . 'class-cta-type.php' );
			new CTA_Type( $this );

			//register the widget
			require_once( plugin_dir_path( __FILE__ ) . 'class-cta-widget.php' );
			add_action( 'widgets_init', array( $this, 'register_call_to_action_widget' ) );

			//load admin metabox information
			add_action( $this->core->plugin->globals['plugin_hook'] . '_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) );

			//Register the shortcode
			add_shortcode( 'call_to_action', array( $this, 'call_to_action_shortcode' ) );

		}

		/**
		 * Registers Call to Action Widget
		 * 
		 * @return WP_Widget
		 */
		function register_call_to_action_widget() {
			return register_widget(	'SB_Call_To_Action_Widget' );
		}

		/**
		 * Add meta boxes to primary options pages
		 * 
		 * @param array $available_pages array of available page_hooks
		 */
		function add_admin_meta_boxes( $available_pages ) {

			//add metaboxes
			add_meta_box( 
				'call_to_action_scheduler_module_intro', 
				__( 'Welcome!', $this->core->plugin->globals['plugin_hook'] ),
				array( $this, 'metabox_normal_intro' ),
				'settings_page_call_to_action_scheduler',
				'normal',
				'core'
			);

			//add metaboxes
			add_meta_box( 
				'call_to_action_scheduler_module_widget_instructions', 
				__( 'Using The Widget', $this->core->plugin->globals['plugin_hook'] ),
				array( $this, 'metabox_advanced_using_widget' ),
				'settings_page_call_to_action_scheduler',
				'advanced',
				'core'
			);

			//add metaboxes
			add_meta_box( 
				'call_to_action_scheduler_module_shortcode_instructions', 
				__( 'Using The Shortcode', $this->core->plugin->globals['plugin_hook'] ),
				array( $this, 'metabox_advanced_using_shortcode' ),
				'settings_page_call_to_action_scheduler',
				'advanced',
				'core'
			);

		}

		/**
		 * Introduction to the plugin and it's features
		 * 
		 * @return void
		 */
		public function metabox_normal_intro() {

			$content = '<p>' . __( 'While there are no global options to configure please read the instructions below to add your calls to action to your site. Before any calls to action appear you will need to add them using the "Calls to Action" menu to the left. Additionally you can limit calls to action to a specified category by setting up categories under the "Calls to Action" menu to the left.', $this->core->plugin->globals['plugin_hook'] ) . '</p>';

			echo $content;

		}

		/**
		 * Metabox with instructions for using the plugin widget
		 * 
		 * @return type
		 */
		public function metabox_advanced_using_widget() {

			$content = '<p>' . __( 'You can add the "Call to Action Scheduler" widget to any widgetized area of your site. Simply add the widget in the widgets panel and select a Call to Action category if you do not wish to show all available calls to action for the pertinent date.', $this->core->plugin->globals['plugin_hook'] ) . '</p>';

			echo $content;

		}

		/**
		 * Metabox with instructions for using the plugin shortcode
		 * 
		 * @return type
		 */
		public function metabox_advanced_using_shortcode() {

			$content = '<p>' . __( 'If you would like to show your call to action within a post, page or other content simply add the shortcode <em>[call_to_action]</em> where you would like it to appear. You can limit the shortcode to a specific call to action category by addint <em>cat=[category id]</em> to the shortcode. For example, <em>[call_to_action cat="6"]</em> would list every available call to action in category 6.', $this->core->plugin->globals['plugin_hook'] ) . '</p>';

			echo $content;

		}

		/**
		 * Create call to action shortcode
		 * 
		 * @param array $atts array parameters
		 * 
		 * @return string
		 */
		public function call_to_action_shortcode( $atts ) {

			$args = array(
				'post_type' 	=> 'call_to_action',
				'post_status' 	=> 'publish',
				'meta_query' 		=> array(
					array( 
						'key' 			=> '_sb_cta_start_date', 
						'value' 		=> time(),
						'type' 			=> 'numeric',
						'compare' 		=> '<=',
					),
					array( 
						'key' 			=> '_sb_cta_end_date', 
						'value' 		=> time(), 
						'type' 			=> 'numeric',
						'compare' 		=> '>=',
					),
				),
			);

			if ( isset( $atts['cat'] ) ) {

				$args['tax_query'] = array(
					array(
						'taxonomy' 		=> 'cta_type',
						'field' 		=> 'id',
						'terms' 		=> absint( $atts['cat'] ),
						'operator' 		=> 'IN',
					),
				);
				
			}

			$ctas = new WP_Query( $args );

			$content = '';

			if ( $ctas->have_posts() ) {

	  			while ( $ctas->have_posts() ) {

	  				$ctas->the_post();

					$content .= the_content();

				}

			}

			return $content;

		}

		/**
		 * Start the Springbox module
		 * 
		 * @param  [plugin_core]    $core   Instance of core plugin class
		 * @return [this] 			The instance of the module class
		 */
		public static function start( $core ) {

			if ( ! isset( self::$instance ) || self::$instance === null ) {
				self::$instance = new self( $core );
			}

			return self::$instance;

		}

	}

}