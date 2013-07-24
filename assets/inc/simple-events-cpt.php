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
	/* CUSTOM META DATA
	/************************************************************************/

	add_action('add_meta_boxes', 'se_meta_box_add');

	function se_meta_box_add() {
	  add_meta_box( 'meta-box-se-date-time', 'Event Date &amp; Time', 'se_meta_box_date_time', 'se_events', 'normal', 'core' );
	  //add_meta_box( 'meta-box-se-recurrence', 'Event Recurrence', 'se_meta_box_recurrence', 'se_events', 'normal', 'core' );
	  add_meta_box( 'meta-box-se-details', 'Event Details', 'se_meta_box_details', 'se_events', 'normal', 'core' );
	  add_meta_box( 'meta-box-se-description', 'Event Description', 'se_meta_box_description', 'se_events', 'normal', 'core' );
	}

	function se_meta_box_date_time($post) {
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		$values = get_post_custom($post->ID);
		$se_start_date = $values['se-start-date'][0];
		$se_end_date = $values['se-end-date'][0];
		$se_start_time = $values['se-start-time'][0];
		$se_end_time = $values['se-end-time'][0];
	  ?>
	  	<div class="group">

		  	<div class="se-control four-col">

		  		<label for="se-start-date"><span>*</span> Start Date</label>
		  		<input type="text" id="se-start-date" class="datepicker required" name="se-start-date" value="<?php if (isset($se_start_date)) { echo $se_start_date; } ?>" />

		  	</div>
		  	<div class="se-control four-col">

		  		<label for="se-end-date">End Date</label>
		  		<input type="text" id="se-end-date" class="datepicker" name="se-end-date" value="<?php if (isset($se_end_date)) { echo $se_end_date; } ?>" />

		  	</div>
		  	<div class="se-control four-col">

		  		<label for="se-start-time"><span>*</span> Start Time</label>
		  		<input type="text" id="se-start-time" class="timepicker required" name="se-start-time" value="<?php if (isset($se_start_time)) { echo $se_start_time; } ?>" />

		  	</div>
		  	<div class="se-control four-col">

		  		<label for="se-end-time">End Time</label>
		  		<input type="text" id="se-end-time" class="timepicker" name="se-end-time" value="<?php if (isset($se_end_time)) { echo $se_end_time; } ?>" />

		  	</div>
		</div>
	  <?php 
	}

	function se_meta_box_recurrence($post) {
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		$values = get_post_custom($post->ID);
		$se_start_date = $values['se-start-date'][0];
		$se_end_date = $values['se-end-date'][0];
		$se_start_time = $values['se-start-time'][0];
		$se_end_time = $values['se-end-time'][0];
	  ?>
	  <div class="group">

		<div class="se-control four-col">

	  		<label for="se-end-time">Recurring?</label>
	  		<input type="text" id="se-end-time" class="timepicker" name="se-end-time" value="<?php if (isset($se_end_time)) { echo $se_end_time; } ?>" />

	  	</div>

	  	<div class="se-control four-col">

	  		<label for="se-end-time">Frequency</label>
	  		<input type="text" id="se-end-time" class="timepicker" name="se-end-time" value="<?php if (isset($se_end_time)) { echo $se_end_time; } ?>" />

	  	</div>

	  	<div class="se-control four-col">

	  		<label for="se-end-time">Day</label>
	  		<input type="text" id="se-end-time" class="timepicker" name="se-end-time" value="<?php if (isset($se_end_time)) { echo $se_end_time; } ?>" />

	  	</div>

	  	<div class="se-control four-col">

	  		<label for="se-end-time">Time</label>
	  		<input type="text" id="se-end-time" class="timepicker" name="se-end-time" value="<?php if (isset($se_end_time)) { echo $se_end_time; } ?>" />

	  	</div>

	  </div>

	  <?php 
	}

	function se_meta_box_details($post) {
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		$values = get_post_custom($post->ID);
		$se_location = $values['se-location'][0];
		$se_address = $values['se-address'][0];
		$se_cost = $values['se-cost'][0];
	  ?>
	  	<div class="group">
		  	<div class="se-control two-col">

		  		<label for="se-location"><span>*</span> Location</label>
		  		<input type="text" class="se-location required" name="se-location" value="<?php if (isset($se_location)) { echo $se_location; } ?>" />
		  		<span>Example: The Metrodome</span>

		  	</div>
		  	<div class="se-control two-col">

		  		<label for="se-address">Address</label>
		  		<input type="text" class="se-address" name="se-address" value="<?php if (isset($se_address)) { echo $se_address; } ?>" />
		  		<span>Example: 100 4th St S Minneapolis, MN 55401</span>

		  	</div>
		  	<div class="se-control two-col">

		  		<label for="se-cost">Cost</label>
		  		<input type="text" class="se-cost" name="se-cost" id="se-cost" value="<?php if (isset($se_cost)) { echo $se_cost; } ?>" />
		  		<span>Example: $5.00 or 5.00 or 5</span>

		  	</div>
	  	</div>
	  <?php 
	}

	function se_meta_box_description($post) {
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		$values = get_post_custom($post->ID);
		$se_description = $values['se-description'][0];
	  	?>
	  	<div class="group">
		  	<div class="se-control">

		  		<label for="se-description">Description</label>
		  		<textarea class="se-description" name="se-description" /><?php if (isset($se_description)) { echo $se_description; } ?></textarea>
		  		<span>Enter a short description here, no HTML is accepted.</span>

		  	</div>
	  	</div>
	  <?php 
	}

	/************************************************************************/
	/* SAVE CUSTOM POST TYPE DATA
	/************************************************************************/

	add_action('save_post', 'se_meta_box_save');


	function se_meta_box_save($post_id) {
		// Bail if we're doing an auto save
		if(defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) return;
		// if our nonce isn't there, or we can't verify it, bail
		if(!isset($_POST['meta_box_nonce']) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' )) return;
	  	// if our current user can't edit this post, bail
	  	if(!current_user_can('edit_post')) return;
		// now we can actually save the data
		$allowed = array( 
		'a' => array( // on allow a tags
		  'href' => array() // and those anchords can only have href attribute
		),
		'strong' => array()
		);
	 //  	// Probably a good idea to make sure your data is set
	 //  	if( isset( $_POST['se-start-date'] ) )
	 //    	update_post_meta( $post_id, 'se-start-date', wp_kses( $_POST['se-start-date'], $allowed ) );
		// if ( isset( $_POST['se-end-date'] ) && !empty( $_POST['se-end-date'] ) ) {
		// 	update_post_meta( $post_id, 'se-end-date', wp_kses( $_POST['se-end-date'], $allowed ) );
		// } else {
		// 	update_post_meta( $post_id, 'se-end-date', wp_kses( $_POST['se-start-date'], $allowed ) );
		// }
		// if( isset( $_POST['se-start-time'] ) )
		// 	update_post_meta( $post_id, 'se-start-time', wp_kses( $_POST['se-start-time'], $allowed ) );
		// if( isset( $_POST['se-end-time'] ) )
		// 	update_post_meta( $post_id, 'se-end-time', wp_kses( $_POST['se-end-time'], $allowed ) );
		// if( isset( $_POST['se-location'] ) )
		// 	update_post_meta( $post_id, 'se-location', wp_kses( $_POST['se-location'], $allowed ) );
		// if( isset( $_POST['se-address'] ) )
		// 	update_post_meta( $post_id, 'se-address', wp_kses( $_POST['se-address'], $allowed ) );
		// if( isset( $_POST['se-cost'] ) )
		// 	update_post_meta( $post_id, 'se-cost', wp_kses( $_POST['se-cost'], $allowed ) );
		// if( isset( $_POST['se-description'] ) )
		// 	update_post_meta( $post_id, 'se-description', wp_kses( $_POST['se-description'], $allowed ) );
	}

	/************************************************************************/
	/* CUSTOM POST TYPE ICON
	/************************************************************************/

	add_action( 'admin_head', 'cpt_icons' );

	function cpt_icons() { ?>
	    <style type="text/css" media="screen">
	        #menu-posts-se_events .wp-menu-image {
	            background: url(<?php echo SE_URL_PATH . '/assets/img/calendar-list.png'; ?>) no-repeat 6px -17px !important;
	        }
			#menu-posts-se_events:hover .wp-menu-image, #menu-posts-se_events.wp-has-current-submenu .wp-menu-image {
	            background-position:6px 7px!important;
	        }
	    </style>
	<?php }

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

	/************************************************************************/
	/* FLUSH CUSTOM POST TYPE
	/************************************************************************/

	function my_rewrite_flush() {
	  create_post_type();
	  flush_rewrite_rules();
	}

	register_activation_hook(__FILE__, 'my_rewrite_flush');