<?php

/************************************************************************/
/* ERROR LOGGING
/************************************************************************/

/**
 *  Simple logging function that outputs to debug.log if enabled
 *  _log('Testing the error message logging');
 *	_log(array('it' => 'works'));
 */

if (!function_exists('_log')) {
  function _log( $message ) {
    if( WP_DEBUG === true ){
      if( is_array( $message ) || is_object( $message ) ){
        error_log( print_r( $message, true ) );
      } else {
        error_log( $message );
      }
    }
  }
}

/************************************************************************/
/* VARIABLES
/************************************************************************/
global $se_options;

/************************************************************************/
/* INCLUDE CMB LIB
/************************************************************************/
function se_functions_init() {
	include SE_PATH . 'assets/inc/libs/cmb/init.php';
}

add_action('init','se_functions_init');

/************************************************************************/
/* ADD GOOGLE CALENDAR EVENT DETAILS
/************************************************************************/
if ( isset($se_options['google_cal']) && $se_options['google_cal'] ) {
  add_action( 'add_meta_boxes', 'se_google_cal_event_details' );
}

/* Adds a box to the main column on the Post and Page edit screens */
function se_google_cal_event_details() {
  add_meta_box('myplugin_sectionid', 'Google Calendar Event Details', 'se_google_cal_event_details_box', 'se_events', 'normal', 'high');
}

/* Prints the box content */
function se_google_cal_event_details_box( $post ) {
  $meta = get_post_meta( $post->ID );
  echo '<h4>Start Date &amp; Time</h4>';
  echo '<p>'.date( 'M j Y g:i A', $meta['_se_event_start_datetime'][0]).'</p>';
  echo '<h4>End Date &amp; Time</h4>';
  echo '<p>'.date( 'M j Y g:i A', $meta['_se_event_end_datetime'][0]).'</p>';
  echo '<h4>Location</h4>';
  echo '<p>'.$meta['_se_event_location'][0].'</p>';
  echo '<h4>Description</h4>';
  echo '<p>'.$meta['_se_event_description'][0].'</p>';
}


?>