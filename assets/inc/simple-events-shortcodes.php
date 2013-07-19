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

		// GET SHORTCODE OPTIONS
		extract( shortcode_atts( array(
			'count' => -1
		), $atts ));

		// SET VARIABLES
		$events_output = '';
		$i = 0;

		if ( isset($se_options['google_cal_id']) && !empty($se_options['google_cal_id']) ) {

			$feedURL = 'http://www.google.com/calendar/feeds/' . $se_options['google_cal_id'] . '/public/basic';
    	$calFeed = simplexml_load_file($feedURL);

    	foreach ($calFeed->entry as $entry) {
	      $title = stripslashes($entry->title);
	      $summary = stripslashes($entry->summary);
	      $events_output .= '<li id="se-event-'.$i.'>' . $title . $summary . '</li>';
	      $i++;
	    }

		} else {
		
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
					'key'     => 'se-end-date',
					'value'   => date('Y-m-d', time()),
					'compare' => '>=',
					'type'    => 'DATETIME'
        )
      );

			$args = array(
				'posts_per_page' => $count,
				'post_type'      => 'se_events',
				'sort'           => 'post_title',
				'order'          => 'ASC',
				'orderby'        => 'meta_value',
				'meta_key'       => 'se-start-date',
				'meta_query'     => $compare
			);

			$se_events = get_posts($args);

			foreach ($se_events as $se_event) {

				$id					= get_the_id();
				$values             = get_post_custom();
				$se_start_date      = trim($values['se-start-date'][0]);
				$se_start_date_conv = trim(strtotime($se_start_date));
				$se_end_date        = trim($values['se-end-date'][0]);
				$se_end_date_conv   = trim(strtotime($se_end_date));
				$se_start_time      = trim($values['se-start-time'][0]);
				$se_start_time_conv = trim(str_replace(':', '', $se_start_time));
				$se_end_time        = trim($values['se-end-time'][0]);
				$se_end_time_conv   = trim(str_replace(':', '', $se_end_time));
				$se_location        = trim($values['se-location'][0]);
				$se_address         = trim($values['se-address'][0]);
				$se_address_link    = str_replace(' ', '%20', $values['se-address'][0]);
				$se_cost            = trim(str_replace('$', '', $values['se-cost'][0]));
				$se_cost_dollar		= substr($se_cost, 0, strrpos($se_cost, '.'));
				$se_cost_decimal 	= substr(substr($se_cost, strrpos($se_cost, '.') + 1), 0, 2);
				$se_description     = trim($values['se-description'][0]);
				$slug               = basename(get_permalink());
				$permalink          = get_permalink();
				$title              = get_the_title();
				$se_thumbnail		= wp_get_attachment_image_src( get_post_thumbnail_id($id), 'thumbnail');

				$events_output .= '<li id="se-event-'.$i.'" class="se-event"><div class="se-event-date-time-wrapper group"><div class="se-event-date-wrapper"><span class="se-start-date">'.date('m/d/Y', $se_start_date_conv).'</span>';
				if (!empty($se_end_date) && $se_end_date_conv > $se_start_date_conv) {
					$events_output .= '-<span class="se-end-date">'.date('m/d/Y', $se_end_date_conv).'</span>';
				}
				$events_output .= '</div><div class="se-event-time-wrapper"><span class="se-start-time">Time: '.$se_start_time.'</span>';
				if (!empty($se_end_time) && $se_end_time_conv > $se_start_time_conv || !empty($se_end_time) && $se_end_date_conv > $se_start_date_conv) {
					$events_output .= '-<span class="se-end-time">'.$se_end_time.'</span>';
				}
				if (!empty($se_cost)) {
					if (strrpos($se_cost, '.')) {
						$events_output .= '/ <span class="se-cost">Cost: $'.$se_cost_dollar.'.'.$se_cost_decimal.'</span>';
					} else {
						$events_output .= '/ <span class="se-cost">Cost: $'.$se_cost.'.00</span>';
					}
				}
				$events_output .= '</div></div><a href="'.$permalink.'" title="'.$title.'" class="se-event-title">'.$title.'</a>';
				$events_output .= '<div class="se-event-details group"><p class="se-event-location"><span>Location:</span> '.$se_location;
				if (!empty($se_address)) {
					$events_output .= ' <a href="http://maps.google.com/?q='.$se_address_link.'" target="_blank" class="se-address se-external">'.$se_address.'</a>';
				}
				$events_output .= '</p>';
				if (!empty($se_thumbnail[0])) {
					$events_output .= '<img src="'.$se_thumbnail[0].'" alt="'.$title.'" />';
				}
				if (!empty($se_description)) {
					$events_output .= '<p>'.$se_description.'</p>';
				}

				$events_output .= '</div></li>';

				$i++;

			}

		}

		return '<ul class="se-events group">' . $events_output . '</ul>';

		die();
		
	}

	add_shortcode('events_list', 'se_events_list');