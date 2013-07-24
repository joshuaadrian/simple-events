<?php

/************************************************************************/
/* REGISTER SE EVENTS WIDGET
/************************************************************************/
function se_events_widget() {
	register_widget( 'SE_EVENTS_Widget' );
}

add_action( 'widgets_init', 'se_events_widget' );

/************************************************************************/
/* CREATE SE EVENTS WIDGET CLASS
/************************************************************************/
class SE_EVENTS_Widget extends WP_Widget {

	function SE_EVENTS_Widget() {
		$widget_ops = array( 'classname' => 'se_events', 'description' => __('Displays events from your \'Simple Events\' plugin.', 'se_events') );
		
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'se-events-widget' );
		
		$this->WP_Widget( 'se-events-widget', __('Simple Events', 'se_events'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );

		//Our variables from the widget settings.
		$title = apply_filters('widget_title', $instance['title'] );
		$name = $instance['name'];
		$show_info = isset( $instance['show_info'] ) ? $instance['show_info'] : false;

		echo $before_widget;

		// Display the widget title 
		if ( $title ) {
			//echo $before_title . $title . $after_title;
		}
		//Display the name 
		if ( $name ) {
			//printf( '<p>' . __('Hey their Sailor! My name is %1$s.', 'inquire') . '</p>', $name );
		}
		
		if ( $show_info ) {
			//printf( $name );
		}

		echo '<p><a class="btn btn-primary" href="#">Events</a></p>';

		
		echo $after_widget;
	}

	//Update the widget 
	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['name'] = strip_tags( $new_instance['name'] );
		$instance['show_info'] = $new_instance['show_info'];

		return $instance;
	}

	
	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('SE Events', 'se_events'), 'name' => __('GoKart Labs', 'se_events'), 'show_info' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Informational Text:', 'se_events'); ?></label>
			<textarea id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" style="width:100%;"><?php echo $instance['title']; ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php _e('Button Text:', 'se_events'); ?></label>
			<input id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" style="width:100%;" />
		</p>

		<!-- <p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_info'], true ); ?> id="<?php echo $this->get_field_id( 'show_info' ); ?>" name="<?php echo $this->get_field_name( 'show_info' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_info' ); ?>"><?php _e('Display info publicly?', 'se_events'); ?></label>
		</p> -->

	<?php
	}

}

?>