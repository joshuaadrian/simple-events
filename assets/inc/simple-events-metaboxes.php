<?php

function simple_groups_metaboxes( array $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_sg_';

	$meta_boxes[] = array(
		'id'         => 'group_metaboxes',
		'title'      => 'Meeting Details',
		'pages'      => array( 'sg_group_meetings', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => 'Meeting Location',
				'desc' => '*optional',
				'id'   => $prefix . 'group_meeting_location',
				'type' => 'text',
			),
			array(
				'name' => 'Meeting Address',
				'desc' => '*optional',
				'id'   => $prefix . 'group_meeting_address',
				'type' => 'text',
			),
			array(
				'name' => 'Meeting Date/Time',
				'desc' => '*optional',
				'id'   => $prefix . 'group_meeting_date_time',
				'type' => 'text_datetime_timestamp',
			),
			array(
				'name' => 'Groups',
				'desc' => '*optional',
				'id'   => $prefix . 'group_select',
				'type' => 'group_select'
			)
		),
	);

	$meta_boxes[] = array(
		'id'         => 'group_metaboxes',
		'title'      => 'Material Details',
		'pages'      => array( 'sg_group_materials', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => 'Document',
				'desc' => '*optional',
				'id'   => $prefix . 'group_material_document',
				'type' => 'file',
			),
			array(
				'name' => 'Video URL',
				'desc' => 'Example: for a vimeo video with the url of vimeo.com/53937936, you would enter the id being 53937936',
				'id'   => $prefix . 'group_material_video_url',
				'type' => 'text',
			),
			array(
				'name' => 'Groups',
				'desc' => '*optional',
				'id'   => $prefix . 'group_select',
				'type' => 'group_select'
			)
		),
	);

	$meta_boxes[] = array(
		'id'         => 'group_email_metaboxe',
		'title'      => 'Group Welcome Email',
		'pages'      => array( 'sg_groups', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name'    => '',
				'desc'    => '',
				'id'      => $prefix . 'group_welcome_email',
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 8, ),
			),
			array(
				'name' => 'Place these dynamic elements in the email copy with these tags {{name}}, {{username}}, {{password}}',
				'desc' => '',
				'id'   => $prefix . 'group_email_title',
				'type' => 'title',
			),
			array(
				'name' => '',
				'desc' => '*optional',
				'id'   => $prefix . 'group_email_notification',
				'type' => 'group_email_notification'
			)
		),
	);

	$meta_boxes[] = array(
		'id'         => 'video_metaboxes',
		'title'      => 'Video Details',
		'pages'      => array( 'videos', ), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => 'Vimeo Video ID',
				'desc' => 'ie: for this video, vimeo.com/53937936, you would enter the id being 53937936',
				'id'   => $prefix . 'video_id',
				'type' => 'text'
			),
			array(
				'name' => 'Groups',
				'desc' => '*optional',
				'id'   => $prefix . 'group_select',
				'type' => 'group_select'
			)
		),
	);
	
	$meta_boxes[] = array(
		'id'         => 'photo_metaboxes',
		'title'      => 'Photo Details',
		'pages'      => array( 'photos', ), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => 'Photo Caption (optional)',
				'desc' => 'You may include links in the caption.',
				'id'   => $prefix . 'photo_caption',
				'type' => 'text_html'
			),
			array(
				'name' => 'Groups',
				'desc' => '*optional',
				'id'   => $prefix . 'group_select',
				'type' => 'group_select'
			)
		),
	);

	return $meta_boxes;
}

add_filter( 'cmb_meta_boxes', 'simple_groups_metaboxes' );
?>