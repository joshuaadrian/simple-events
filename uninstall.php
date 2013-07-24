<?php

// If uninstall not called from WordPress exit
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Delete option from options table
delete_option( 'se_options' );

// Get all se posts
$se_args = array(
	'post_type' => 'se_events',
	'posts_per_page' => -1
);

$se_posts = get_posts( $se_args );

// Delete all posts and meta data
foreach( $se_posts as $se_post ) {
	delete_post_meta( $se_post->ID, '_se_event_start_datetime' );
	delete_post_meta( $se_post->ID, '_se_event_end_datetime' );
	delete_post_meta( $se_post->ID, '_se_event_address' );
	delete_post_meta( $se_post->ID, '_se_event_location' );
	delete_post_meta( $se_post->ID, '_se_event_cost' );
	delete_post_meta( $se_post->ID, '_se_event_description' );
	wp_delete_post( $se_post->ID, true);
}

?>