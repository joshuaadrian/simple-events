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
			'description' => __( 'Place to manage your events' ), /* Custom Type Description */
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 30, /* this is what order you want it to appear in on the left hand side menu */ 
			'menu_icon' => SE_URL_PATH . '/assets/img/cpt/events-icon.png', /* the icon for the custom post type menu */
			'rewrite'	=> array( 'slug' => 'events' ), /* you can specify its url slug */
			'has_archive' => 'events', /* you can rename the slug here */
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array( 'title','thumbnail' )
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
	      echo date( 'F jS, Y - g:i a', get_post_meta($post_ID, '_se_event_start_datetime', true) ); 
	    }
	    if ($column_name == 'event_start_time_col') {  
	      echo date( 'F jS, Y - g:i a', get_post_meta($post_ID, '_se_event_end_datetime', true) );  
	    }
	}