<?php

	/************************************************************************/
	/* CUSTOM POST TYPE
	/************************************************************************/

	function se_build_taxonomies() {
		register_taxonomy( 'event_types', 'se_events', array( 'hierarchical' => true, 'label' => 'Event Types', 'query_var' => true, 'rewrite' => true ) );
	}

	add_action( 'init', 'se_build_taxonomies', 0 );

	function se_post_types() {
		$labels = array(
		  'name' => _x('Events', 'post type general name'),
		  'singular_name' => _x('Event', 'post type singular name'),
		  'add_new' => _x('Add Event', 'Event'),
		  'add_new_item' => __('Add Event'),
		  'edit_item' => __('Edit Event'),
		  'new_item' => __('New Event'),
		  'view_item' => __('View Event'),
		  'search_items' => __('Search Events'),
		  'not_found' =>  __('No Events found'),
		  'not_found_in_trash' => __('No Events found in Trash'),
		  'parent_item_colon' => ''
		);
		$args = array(
		  'labels' => $labels,
		  'public' => true,
		  'publicly_queryable' => true,
		  'show_ui' => true,
		  'exclude_from_search' => false,
		  'query_var' => true,
		  'rewrite' => array( 'slug' => 'event' ),
		  'capability_type' => 'post',
		  'hierarchical' => false,
		  'menu_position' => 20,
		  'supports' => array('title','thumbnail'),
	   	  'taxonomies' => array( 'event_types' )
		);
		register_post_type('se_events',$args);
	}

	add_action( 'init', 'se_post_types' );

	/************************************************************************/
	/* CUSTOM POST TYPE COLUMN
	/************************************************************************/

	function my_manage_columns( $columns ) {
		unset($columns['date']);
		return $columns;
	}

	function my_column_init() {
		add_filter( 'manage_se_events_posts_columns' , 'my_manage_columns' );
	}
	add_action( 'admin_init' , 'my_column_init' );

	add_filter('manage_se_events_posts_columns', 'se_columns_head_only_events', 10);  
	add_action('manage_se_events_posts_custom_column', 'se_columns_content_only_events', 10, 2);  
	// CREATE TWO FUNCTIONS TO HANDLE THE COLUMN  
	function se_columns_head_only_events($defaults) {  
	    $defaults['event_start_date_col'] = 'Event Start Date';
	    $defaults['event_start_time_col'] = 'Event Start Time';  
	    return $defaults;  
	}  
	function se_columns_content_only_events($column_name, $post_ID) {  
	    if ($column_name == 'event_start_date_col') {  
	        echo get_post_meta($post_ID, 'se-start-date', true); 
	    }
	    if ($column_name == 'event_start_time_col') {  
	        echo get_post_meta($post_ID, 'se-start-time', true);  
	    }
	}