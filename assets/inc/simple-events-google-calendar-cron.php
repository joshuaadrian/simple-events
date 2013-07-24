<?php

	/************************************************************************/
	/* SET CUSTOM EVENT SCHEDULES
	/************************************************************************/
	function se_cron_schedules() {
		return array(
			'every_minute' => array(
				'interval' => 60 * 1,
				'display' => 'Every minute'
			),
			'every_five_minutes' => array(
				'interval' => 60 * 5,
				'display' => 'Every five minutes'
			),
			'every_fifteen_minutes' => array(
				'interval' => 60 * 15,
				'display' => 'Every fifteen minutes'
			),
			'every_half_hour' => array(
				'interval' => 60 * 30,
				'display' => 'Every half hour'
			)
		);
	}

	add_filter('cron_schedules', 'se_cron_schedules');

	/************************************************************************/
	/* SET EVENT SCHEDULE
	/************************************************************************/
	add_action('simple_events_cron', 'simple_events_calls');

	function simple_events_activation() {
		if ( !wp_next_scheduled('simple_events_cron') ) {
			wp_schedule_event(current_time('timestamp'), 'every_fifteen_minutes', 'simple_events_cron');
		}
	}

	add_action('wp', 'simple_events_activation');

	/************************************************************************/
	/* CURL CALLS
	/************************************************************************/
	function simple_events_calls() {

		// GET PLUGIN OPTIONS
		global $se_options;
		// SET CACHE FILES
		$cache_file = SE_PATH . 'assets/cache/google_calendar_events.json';
		$error_file = SE_PATH . 'assets/cache/error_log.json';

		// CHECK FOR GOOGLE CALENDAR VARIABLES
		if ( isset($se_options['google_cal_id']) && !empty($se_options['google_cal_id']) && isset($se_options['google_cal_api_key']) && !empty($se_options['google_cal_api_key']) ) {

			// GOOGLE CAL API VARIABLES
			$google_cal_id      = $se_options['google_cal_id'];
			$google_cal_api_key = $se_options['google_cal_api_key'];
			$google_cal_api_url = 'https://www.googleapis.com/calendar/v3/calendars/'.$google_cal_id.'/events?key='.$google_cal_api_key.'&timeMin=' . substr( date(DATE_ATOM, time()), 0, 19) . '.000Z';
			//_log( substr( date(DATE_ATOM, time()), 0, 19) . '.000Z');
			//_log($google_cal_api_url);

			$google_cal_ch = curl_init();
		    curl_setopt ($google_cal_ch, CURLOPT_URL, $google_cal_api_url);
		    curl_setopt ($google_cal_ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt ($google_cal_ch, CURLOPT_TIMEOUT, 20);
		    curl_setopt ($google_cal_ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		    curl_setopt ($google_cal_ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		    $google_cal_data = curl_exec($google_cal_ch);
		    $google_cal_error = curl_errno($google_cal_ch);
		    curl_close($google_cal_ch);
		    $google_cal_events = json_decode($google_cal_data);

		    if ( !isset($google_cal_events->error) ) {

		    	$google_cal_cache_file = fopen( $cache_file, 'wb' );
			    fwrite( $google_cal_cache_file, utf8_encode($google_cal_data) );
			    fclose( $google_cal_cache_file );

			    $event_titles = array();

			    $args = array(
			    	'posts_per_page' => -1,
			    	'post_type' => 'se_events'
			    );

				$event_posts = get_posts( $args );

				foreach( $event_posts as $event_post ) :
					$event_titles[] = $event_post->post_title;
				endforeach;

				_log('EVENT TITLES => '.implode(', ', $event_titles) .' GOOGLE CAL TITLE => '.$google_cal_event->summary);
				_log(in_array($google_cal_event->summary, $event_titles));

		    	foreach ($google_cal_events->items as $google_cal_event) {

		    		if ( in_array($google_cal_event->summary, $event_titles) ) {

		    		

		    		} else {

			    		_log($google_cal_event->summary);

			    		// Create post object
						$google_cal_post = array(
							'post_title'  => wp_strip_all_tags( $google_cal_event->summary ),
							'post_status' => 'publish',
							'post_author' => 1,
							'post_type'   => 'se_events'
						);

						$google_cal_post_id = wp_insert_post( $google_cal_post );

						if ( $google_cal_post_id ) {

							$id = $google_cal_post_id;
							$start = isset($google_cal_event->start->dateTime) ? strtotime($google_cal_event->start->dateTime) : strtotime($google_cal_event->start->date);
							$end = isset($google_cal_event->end->dateTime) ? strtotime($google_cal_event->end->dateTime) : strtotime($google_cal_event->end->date);
							add_post_meta( $id, '_se_google_cal_id', $google_cal_event->id );
							add_post_meta( $id, '_se_event_start_datetime', $start );
							add_post_meta( $id, '_se_event_end_datetime', $end );

							if ( isset($google_cal_event->location) ) {
								add_post_meta( $id, '_se_event_location', $google_cal_event->location );
							}

							if ( isset($google_cal_event->description) ) {
								add_post_meta( $id, '_se_event_description', $google_cal_event->description );
							}

						}

			    	}

			    }

		    } else {

			    $google_cal_error_file = fopen($error_file, 'a');
				$google_cal_error_message = date("F j, Y, g:i a") . ' google calendar error json => ' . $google_cal_data . ' | rest call url =>  ' . $google_cal_api_url . "\r\n\n";
				fwrite($google_cal_error_file, utf8_encode($google_cal_error_message));
				fclose($google_cal_error_file);
				
			}

		}

	}

?>