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
		$cache_file = SE_PATH . 'assets/cache/google_calendar_events.txt';
		$error_file = SE_PATH . 'assets/cache/error_log.txt';

		// CHECK FOR GOOGLE CALENDAR VARIABLES
		if ( isset($se_options['google_cal_id']) && !empty($se_options['google_cal_id']) && isset($se_options['google_cal_api_key']) && !empty($se_options['google_cal_api_key']) ) {

			// GOOGLE CAL API VARIABLES
			$google_cal_id      = $se_options['google_cal_id'];
			$google_cal_api_key = $se_options['google_cal_api_key'];
			$google_cal_api_url = 'https://www.googleapis.com/calendar/v3/calendars/'.$google_cal_id.'/events?key='.$google_cal_api_key;
			
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
	    $google_cal_events = json_decode($google_cal_data, true);

	    // CHECK FOR ERRORS AND WRITE JSON TO FILE
	    // $google_cal_cache_file = fopen( $cache_file, 'wb' );
	    // fwrite( $google_cal_cache_file, utf8_encode($google_cal_data) );
	    // fclose( $google_cal_cache_file );

	    if ( !isset($google_cal_events['error']) ) {
	    	$google_cal_cache_file = fopen( $cache_file, 'wb' );
		    fwrite( $google_cal_cache_file, utf8_encode($google_cal_data) );
		    fclose( $google_cal_cache_file );
	    } else {
		    $google_cal_error_file = fopen($error_file, 'a');
				$google_cal_error_message = date("F j, Y, g:i a") . ' google calendar error json => ' . $google_cal_data . ' | rest call url =>  ' . $google_cal_api_url . "\r\n\n";
				fwrite($google_cal_error_file, utf8_encode($google_cal_error_message));
				fclose($google_cal_error_file);
		  }

	  }

	}

?>