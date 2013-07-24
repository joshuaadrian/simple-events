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

/************************************************************************/
/* REDIRECT TO CUSTOM EVENT ARCHIVE TEMPLATE
/************************************************************************/
function se_events_archives_template( $archive_template ) {

  global $post;

  if ( is_post_type_archive ( 'se_events' ) ) {
    $archive_template = SE_PATH . 'assets/inc/simple-events-archive-template.php';
  }

  return $archive_template;

}

add_filter( 'archive_template', 'se_events_archives_template' ) ;

/************************************************************************/
/* REDIRECT TO CUSTOM EVENT SINGLE TEMPLATE
/************************************************************************/
function se_events_single_template( $single_template ) {

  global $post;

  if ( $post->post_type == 'se_events' ) {
    $single_template = SE_PATH . 'assets/inc/simple-events-single-template.php';
  }

  return $single_template;

}

add_filter( "single_template", "se_events_single_template" );

/************************************************************************/
/* ADD DO SHORTCODE FILTER
/************************************************************************/
if ( !has_filter( 'the_content', 'do_shortcode' ) ) {
  add_filter('the_content', 'do_shortcode', 11);
}

/************************************************************************/
/* SAVE END DATE IF NOT SET
/************************************************************************/
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
  // Probably a good idea to make sure your data is set
  if ( isset( $_POST['_se_event_start_datetime'] ) && !empty( $_POST['_se_event_end_datetime'] ) ) {
    _log('Here' . $_POST['_se_event_start_datetime']);
    //update_post_meta( $post_id, '_se_event_end_datetime', wp_kses( $_POST['_se_event_end_datetime'], $allowed ) );
  } else {
    //update_post_meta( $post_id, '_se_event_end_datetime', wp_kses( $_POST['_se_event_start_datetime'], $allowed ) );
  }
}

add_action('save_post', 'se_meta_box_save');

/************************************************************************/
/* UNSET ADDING EVENTS IF MANAGED VIA GOOGLE CALENDAR
/************************************************************************/
function hide_add_new_se_events_type() {

    global $submenu, $se_options;

    if ( isset($se_options['google_cal']) && $se_options['google_cal'] ) {
      unset($submenu['edit.php?post_type=se_events'][10]);
    }

}

add_action('admin_menu', 'hide_add_new_se_events_type');

?>