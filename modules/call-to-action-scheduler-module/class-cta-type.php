<?php

if( ! class_exists( 'CTA_Type' ) ) {

	class CTA_Type {

		private 
			$module,
			$prefix;

		function __construct( $module ) {

			$this->module = $module;
			$this->prefix = '_sb_cta_'; // Prefix for all fields

			add_action( 'init', array( $this, 'initialize_cmb_meta_boxes' ), 9999 );
			add_action( 'init', array( $this, 'cta_taxonomies' ), 0 );
			add_action( 'init', array( $this, 'create_post_type' ) );
			add_filter( 'cmb_meta_boxes', array( $this, 'do_metaboxes' ) );
			add_filter( 'manage_call_to_action_posts_columns', array( $this, 'admin_columns_head' ) );  
			add_action( 'manage_call_to_action_posts_custom_column', array( $this, 'admin_columns_content' ), 10, 2);
			add_filter( 'manage_edit-call_to_action_sortable_columns', array( $this, 'admin_sortable_columns' ) );
			add_action( 'cmb_render_end_date_timestamp', array( $this, 'render_end_date_timestamp' ), 10, 2 );
			add_action( 'cmb_validate_end_date_timestamp', array( $this, 'save_end_date_timestamp' ) );

		} 

		function initialize_cmb_meta_boxes() {

			if ( ! class_exists( 'cmb_Meta_Box' ) ) {
				require_once( 'lib/metabox/init.php' );
			}

		}

		
		function render_end_date_timestamp( $field, $meta ) {
    		echo '<input class="cmb_text_small cmb_datepicker" type="text" name="', $field['id'], '[date]" id="', $field['id'], '_date" value="', '' !== $meta ? date( 'm\/d\/Y', $meta ) : $field['std'], '" />';
		}

		function save_end_date_timestamp( $new ) {			

			return absint( ( ( strtotime( $new['date'] ) + 86400 ) - 1 ) );

		}
		
		function create_post_type() {

			register_post_type( 'call_to_action',
				array(
					'labels'			=> array(
						'name'				=> __( 'Calls to Action', $this->module->core->plugin->globals['plugin_hook'] ),
						'all_items'			=> __( 'All Calls to Action', $this->module->core->plugin->globals['plugin_hook'] ),
						'singular_name'		=> __( 'Call to Action', $this->module->core->plugin->globals['plugin_hook'] ),
						'add_new_item'		=> __( 'Add Call to Action', $this->module->core->plugin->globals['plugin_hook'] ),
					),
					'public'			=> false,
					'show_ui'			=> true,
					'show_in_nav_menus'	=> false,
					'show_in_admin_bar'	=> true,
					'has_archive'		=> false,
					'rewrite'			=> false,
					'capability_type'	=> 'post',
					'supports'			=> array(
						'title',
						'editor',
						'revisions',
					),
					'taxonomies'		=> array( 'cta_type' )
				)
			);

		}

		function do_metaboxes( $meta_boxes ) {
			
			$meta_boxes[] = array(
				'id' 			=> 'cta_date_picker',
				'title' 		=> __( 'Display Dates', $this->module->core->plugin->globals['plugin_hook'] ),
				'pages'			=> array( 'call_to_action' ), // post type
				'context'		=> 'normal',
				'priority'		=> 'high',
				'show_names'	=> true, // Show field names on the left
				'fields'		=> array(
					array(
						'name'		=> __( 'Start Date', $this->module->core->plugin->globals['plugin_hook'] ),
						'desc'		=> __( 'Select the date the Call to Action should first appear.', $this->module->core->plugin->globals['plugin_hook'] ),
						'id'		=> $this->prefix . 'start_date',
						'type'		=> 'text_date_timestamp',
					),
					array(
						'name'		=> __( 'End Date', $this->module->core->plugin->globals['plugin_hook'] ),
						'desc'		=> __( 'Select the last date the Call to Action should appear.', $this->module->core->plugin->globals['plugin_hook'] ),
						'id'		=> $this->prefix . 'end_date',
						'type'		=> 'end_date_timestamp',
					),
				),
			);

			return $meta_boxes;
		}

		function cta_taxonomies() {

			$labels = array(
				'name'                => _x( 'Categories', 'taxonomy general name', $this->module->core->plugin->globals['plugin_hook'] ),
				'singular_name'       => _x( 'Call to Action Category', 'taxonomy singular name', $this->module->core->plugin->globals['plugin_hook'] ),
				'search_items'        => __( 'Search Call to Action Category', $this->module->core->plugin->globals['plugin_hook'] ),
				'all_items'           => __( 'All Call to Action Categories', $this->module->core->plugin->globals['plugin_hook'] ),
				'parent_item'         => __( 'Parent Call to Action Category', $this->module->core->plugin->globals['plugin_hook'] ),
				'parent_item_colon'   => __( 'Parent Call to Actin Category :', $this->module->core->plugin->globals['plugin_hook'] ),
				'edit_item'           => __( 'Edit Call to Action Category', $this->module->core->plugin->globals['plugin_hook'] ),
				'update_item'         => __( 'Update Call to Action Category', $this->module->core->plugin->globals['plugin_hook'] ),
				'add_new_item'        => __( 'Add New Call to Action Category', $this->module->core->plugin->globals['plugin_hook'] ),
				'new_item_name'       => __( 'New Call to Action Category Name', $this->module->core->plugin->globals['plugin_hook'] ),
				'menu_name'           => __( 'Category', $this->module->core->plugin->globals['plugin_hook'] ),
			  ); 	

			  $args = array(
				'hierarchical'        => true,
				'labels'              => $labels,
				'show_ui'             => true,
				'show_admin_column'   => true,
				'query_var'           => true,
			  );
	  
			register_taxonomy( 'cta_type', 'call_to_action', $args );

		}

		function admin_sortable_columns( $columns ) {

			$columns['start_date'] = 'start_date';
			$columns['end_date'] = 'end_date';

			return $columns;

		}

		function admin_columns_head( $columns ) {

			$columns = array(
				'cb' 			=> '<input type="checkbox" />',
				'title' 		=> __( 'Call to Action', $this->module->core->plugin->globals['plugin_hook'] ),
				'start_date' 	=> __( 'Start Date', $this->module->core->plugin->globals['plugin_hook'] ),
				'end_date' 		=> __( 'End Date', $this->module->core->plugin->globals['plugin_hook'] ),
			);

			return $columns; 

		}

		function admin_columns_content( $column, $post_id ) {

			switch ( $column ) {

				case 'start_date':

					$post_start_date = get_post_meta( $post_id, $this->prefix . 'start_date' );
				
					if ( $post_start_date ) {
						echo date( get_option( 'date_format' ), $post_start_date[0] );
					} 

					break;

				case 'end_date':

					$post_end_date = get_post_meta( $post_id, $this->prefix . 'end_date' );;  
				
					if ( $post_end_date ) {
						echo date( get_option( 'date_format' ), $post_end_date[0] );
					} 

					break;

				default:
					break;

			}

		}

	}

}
