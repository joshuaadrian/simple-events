<?php

	/************************************************************************/
	/* Remove the 2 main auto-formatters when wrapped in RAW tags
	/************************************************************************/

	remove_filter('the_content', 'wpautop');
	remove_filter('the_content', 'wptexturize');

	/************************************************************************/
	/* ENABLE SHORTCODES FOR TEXT WIDGETS
	/************************************************************************/

	add_filter('widget_text', 'shortcode_unautop');
	add_filter('widget_text', 'do_shortcode', 11);


	/************************************************************************/
	/* SIMPLE EVENTS SHORTCODE
	/************************************************************************/

	function se_events_list( $atts, $content = null ) {

		global $se_options;
		//date_default_timezone_set('America/Chicago');

		// GET SHORTCODE OPTIONS
		extract( shortcode_atts( array(
			'count' => -1
		), $atts ));

		// SET VARIABLES
		$events_output = '';
		$i = 0;
		
		$compare = array(
			'relation' => 'OR',
			array(
				'key'     => 'se-start-date',
				'value'   => date('Y-m-d', time()),
				'compare' => '>=',
				'type'    => 'DATETIME'
			),
			array(
				'key'     => 'se-end-date',
				'value'   => date('Y-m-d', time()),
				'compare' => '>=',
				'type'    => 'DATETIME'
			)
		);

		$compare = array(
			array(
				'key'     => '_se_event_start_datetime',
				'value'   => time(),
				'compare' => '>=',
				'type'    => 'NUMERIC'
			)
		);

		$args = array(
			'posts_per_page' => $count,
			'post_type'      => 'se_events',
			'sort'           => 'post_title',
			'order'          => 'ASC',
			'orderby'        => 'meta_value',
			'meta_key'       => '_se_event_start_datetime',
			'meta_query'     => $compare
		);

		$se_events = get_posts( $args );

		//_log($se_events);

		if ( $se_events ) {

			foreach ($se_events as $se_event) {

				$id                 = $se_event->ID;
				$meta               = get_post_meta( $id );
				$se_start_date      = isset($meta['_se_event_start_datetime'][0]) ? trim($meta['_se_event_start_datetime'][0]) : '';
				$se_end_date        = isset($meta['_se_event_end_datetime'][0]) ? trim($meta['_se_event_end_datetime'][0]) : '';
				$se_start_time      = date('Hi', $se_start_date);
				$se_start_time_conv = date('g:i a', $se_start_date);
				$se_end_time        = date('Hi', $se_end_date);
				$se_end_time_conv   = date('g:i a', $se_end_date);
				$se_location        = isset($meta['_se_event_location'][0]) ? trim($meta['_se_event_location'][0]) : false;
				$se_address         = isset($meta['_se_event_address'][0]) ? trim($meta['_se_event_address'][0]) : false;
				$se_address_link    = isset($meta['_se_event_address'][0]) ? str_replace(' ', '%20', $meta['_se_event_address'][0]) : false;
				$se_cost            = isset($meta['_se_event_cost'][0]) ? trim(str_replace('$', '', $meta['_se_event_cost'][0])) : false;
				$se_cost_dollar     = substr($se_cost, 0, strrpos($se_cost, '.'));
				$se_cost_decimal    = substr(substr($se_cost, strrpos($se_cost, '.') + 1), 0, 2);
				$se_description     = isset($meta['_se_event_description'][0]) ? trim($meta['_se_event_description'][0]) : false;
				$slug               = $se_event->post_name;
				$permalink          = get_bloginfo('url') . '/events/' . $se_event->post_name;
				$title              = $se_event->post_title;
				$se_thumbnail       = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'thumbnail');

				//_log($meta);

				$events_output .= '<li id="se-event-'.$i.'" class="se-event"><div class="se-event-date-time-wrapper group"><div class="se-event-date-wrapper"><span class="se-start-date">'.date('m/d/Y', $se_start_date).'</span>';
				
				if ( !empty($se_end_date) && $se_end_date > $se_start_date ) {
					$events_output .= '-<span class="se-end-date">'.date('m/d/Y', $se_end_date).'</span>';
				}

				$events_output .= '</div><div class="se-event-time-wrapper"><span class="se-start-time">Time: '.$se_start_time_conv.'</span>';
				
				if ( !empty($se_end_time) && $se_end_time > $se_start_time || !empty($se_end_time) && $se_end_date > $se_start_date ) {
					$events_output .= '-<span class="se-end-time">'.$se_end_time_conv.'</span>';
				}

				if (!empty($se_cost)) {
					if (strrpos($se_cost, '.')) {
						$events_output .= '/ <span class="se-cost">Cost: $'.$se_cost_dollar.'.'.$se_cost_decimal.'</span>';
					} else {
						$events_output .= '/ <span class="se-cost">Cost: $'.$se_cost.'.00</span>';
					}
				}

				$events_output .= '</div></div><a href="'.$permalink.'" title="'.$title.'" class="se-event-title">'.$title.'</a>';

				$events_output .= '<div class="se-event-details group">';

				if ( $se_location || $se_address) {
					$events_output .= '<p class="se-event-location">';
				}

				if ( $se_location ) {
					$events_output .= '<span>Location:</span> '.$se_location;
				}

				if (!empty($se_address)) {
					$events_output .= ' <a href="http://maps.google.com/?q='.$se_address_link.'" target="_blank" class="se-address se-external">'.$se_address.'</a>';
				}

				if ( $se_location || $se_address) {
					$events_output .= '</p>';
				}

				if (!empty($se_thumbnail[0])) {
					$events_output .= '<img src="'.$se_thumbnail[0].'" alt="'.$title.'" />';
				}

				if (!empty($se_description)) {
					$events_output .= '<p>'.$se_description.'</p>';
				}

				if ( !$se_location && !$se_address && empty($se_thumbnail[0]) && !$se_description ) {
					$events_output .= '<p>No more event details given.</p>';
				}

				$events_output .= '</div></li>';

				$i++;

			}

			return '<ul class="se-events group">' . $events_output . '</ul>';

		} else {

			return false;

		}

		die();
		
	}

	add_shortcode('events_list', 'se_events_list');