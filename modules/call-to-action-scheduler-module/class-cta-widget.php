<?php

class SB_Call_To_Action_Widget extends WP_Widget {

	private $hook = 'call_to_action_scheduler';
 
 	/**
 	 * Initialize Call to Action Widget
 	 * 
 	 * @return void
 	 */
	function SB_Call_To_Action_Widget() {

		$widget_ops = array(
			'classname' => 'SB_Call_To_Action_Widget', 
			'description' => __( 'Displays Call To Action from Call to Action Scheduler plugin.', $this->hook ) 
		);

		$this->WP_Widget( 'SB_Call_To_Action_Widget', __( 'Call to Action Scheduler', $this->hook ), $widget_ops );

	}
 
 	/**
 	 * Set up the settings form
 	 * 
 	 * @param array $instance array of widget settings
 	 * 
 	 * @return void
 	 */
	function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'Call to Action', $this->hook );
		}
		
		?>

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->hook ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

		<?php

		$taxonomy = 'cta_type';
		$categories = get_terms( $taxonomy, array( 'hide_empty' => false ) );

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category:', $this->hook ); ?>:</label><br />
			<select name="<?php echo $this->get_field_name( 'category' ); ?>" id="<?php echo $this->get_field_id( 'category' ); ?>">
				<option value="0" <?php selected( 0, $instance['category'], true ); ?>><?php _e( 'Show All', $this->hook ); ?></option>
				<?php foreach( $categories as $cat ) { ?>
					<option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( $cat->term_id, $instance['category'], true ); ?>><?php echo $cat->name; ?></option>
				<?php } ?>
			</select>
		</p>

		<?php

	}
 
 	/**
 	 * Update widget settings
 	 * 
 	 * @param array $new_instance new widget settings
 	 * @param array $old_instance old widget settings
 	 * 
 	 * @return array
 	 */
	function update( $new_instance, $old_instance ) {
		
		$instance = array();

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['category'] = absint( $new_instance['category'] );

		return $instance;

	}
 
 	/**
 	 * Output the actual widget
 	 * 
 	 * @param array $args widget arguments
 	 * @param array $instance widget settings
 	 * 
 	 * @return void
 	 */
	function widget( $args, $instance ) {
	
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		$args = array(
			'post_type' 	=> 'call_to_action',
			'post_status' 	=> 'publish',
			'meta_query' => array(
				array( 
					'key' => '_sb_cta_start_date', 
					'value' => time(),
					'type' => 'numeric',
					'compare' => '<=',
				),
				array( 
					'key' => '_sb_cta_end_date', 
					'value' => time(), 
					'type' => 'numeric',
					'compare' => '>=',
				),
			),
		);

		if ( isset( $instance['category'] ) && $instance['category'] !== 0 ) {

			$args['tax_query'] = array(
				array(
					'taxonomy' 		=> 'cta_type',
					'field' 		=> 'id',
					'terms' 		=> absint( $instance['category'] ),
					'operator' 		=> 'IN',
				),
			);
			
		}

		$ctas = new WP_Query( $args );

		if ( $ctas->have_posts() ) {

  			while ( $ctas->have_posts() ) {

  				$ctas->the_post();

				the_content();

			}

		}			
		
		echo $after_widget;
	
	}
 
}