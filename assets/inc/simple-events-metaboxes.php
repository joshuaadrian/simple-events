<?php

function simple_groups_metaboxes( array $meta_boxes ) {

	global $se_options;

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_se_';

	if ( isset($se_options['google_cal']) && $se_options['google_cal'] ) {

		$meta_boxes[] = array(
			'id'         => 'se_event_details_metaboxes',
			'title'      => 'Extra Event Details',
			'pages'      => array( 'se_events', ), // Post type
			'context'    => 'normal',
			'priority'   => 'low',
			'show_names' => true, // Show field names on the left
			'fields'     => array(
				array(
					'name' => 'Address',
					'desc' => '',
					'id'   => $prefix . 'event_address',
					'type' => 'text',
				),
				array(
					'name' => 'Cost',
					'desc' => '',
					'id'   => $prefix . 'event_cost',
					'type' => 'text_money',
				)
			),
		);

	} else {

		$meta_boxes[] = array(
			'id'         => 'se_event_date_time_metaboxes',
			'title'      => 'Event Date &amp; Time',
			'pages'      => array( 'se_events', ), // Post type
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true, // Show field names on the left
			'fields'     => array(
				array(
					'name' => 'Start Date &amp; Time',
					'desc' => '',
					'id'   => $prefix . 'event_start_datetime',
					'type' => 'text_datetime_timestamp',
				),
				array(
					'name' => 'End Date &amp; Time',
					'desc' => '',
					'id'   => $prefix . 'event_end_datetime',
					'type' => 'text_datetime_timestamp',
				)
			),
		);

		$meta_boxes[] = array(
			'id'         => 'se_event_details_metaboxes',
			'title'      => 'Event Details',
			'pages'      => array( 'se_events', ), // Post type
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true, // Show field names on the left
			'fields'     => array(
				array(
					'name' => 'Location',
					'desc' => '',
					'id'   => $prefix . 'event_location',
					'type' => 'text',
				),
				array(
					'name' => 'Address',
					'desc' => '',
					'id'   => $prefix . 'event_address',
					'type' => 'text',
				),
				array(
					'name' => 'Cost',
					'desc' => '',
					'id'   => $prefix . 'event_cost',
					'type' => 'text_money',
				)
			),
		);

		$meta_boxes[] = array(
			'id'         => 'se_event_description_metaboxes',
			'title'      => 'Event Description',
			'pages'      => array( 'se_events', ), // Post type
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true, // Show field names on the left
			'fields'     => array(
				array(
					'name' => 'Description',
					'desc' => '',
					'id'   => $prefix . 'event_description',
					'type' => 'textarea',
				)
			),
		);

	}

	return $meta_boxes;
}

add_filter( 'cmb_meta_boxes', 'simple_groups_metaboxes' );
?>