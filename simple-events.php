<?php

/*
Plugin Name: Simple Events
Plugin URI: https://github.com/joshuaadrian/simple-events
Description: Create, manage and place events easily with this plugin. Place them with shortcodes on pages, posts, and/or widgets.
Author: Joshua Adrian
Version: 0.6.0
Author URI: http://joshuaadrian.com
*/

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
/* DEFINE PLUGIN ID AND NICK
/************************************************************************/
// DEFINE PLUGIN BASE
define( 'SE_PATH', plugin_dir_path( __FILE__ ) );
// DEFINE PLUGIN URL
define( 'SE_URL_PATH', plugins_url() . '/simple-events');
// DEFINE PLUGIN ID
define( 'SE_PLUGINOPTIONS_ID', 'simple-events' );
// DEFINE PLUGIN NICK
define( 'SE_PLUGINOPTIONS_NICK', 'Simple Events' );
// DEFINE PLUGIN NICK
register_activation_hook( __FILE__, 'se_add_defaults' );
// DEFINE PLUGIN NICK
register_uninstall_hook( __FILE__, 'se_delete_plugin_options' );
// ADD LINK TO ADMIN
add_action( 'admin_init', 'se_init' );
// ADD LINK TO ADMIN
add_action( 'admin_menu', 'se_add_options_page' );
// ADD LINK TO ADMIN
add_filter('plugin_action_links', 'se_plugin_action_links', 10, 2 );
// PLUGIN OPTIONS
$se_options = get_option('se_options');
// GET PLUGIN DATA
if ( is_admin() ) {

	if ( !function_exists( 'get_plugins' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

  $se_data = get_plugin_data( SE_PATH . plugin_basename( dirname( __FILE__ ) ) . '.php', false, false );

  if ( !function_exists('markdown') ) {
		require_once SE_PATH . 'assets/inc/libs/php-markdown/markdown.php';
	}

}

/************************************************************************/
/* ADD LOCALIZATION FOLDER
/************************************************************************/

function se_plugin_setup() {
    load_plugin_textdomain( 'simple-events', false, dirname(plugin_basename(__FILE__)) . '/lang/' );
}

add_action( 'after_setup_theme', 'se_plugin_setup' );

/************************************************************************/
/* Delete options table entries ONLY when plugin deactivated AND deleted
/************************************************************************/

function se_delete_plugin_options() {
	delete_option( 'se_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'posk_add_defaults')
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE PLUGIN IS ACTIVATED. IF THERE ARE NO THEME OPTIONS
// CURRENTLY SET, OR THE USER HAS SELECTED THE CHECKBOX TO RESET OPTIONS TO THEIR
// DEFAULTS THEN THE OPTIONS ARE SET/RESET.
//
// OTHERWISE, THE PLUGIN OPTIONS REMAIN UNCHANGED.
// ------------------------------------------------------------------------------

// Define default option settings
function se_add_defaults() {

	global $se_options;

  if ( !$se_options || !is_array( $se_options ) ) {
		
		delete_option('se_options');
		
		$defaults = array(
			"google_cal"         => "",
			"google_cal_id"      => "",
			"google_cal_api_key" => "",
			"skin"               => "none",
			"cal_view"           => "list",
			"time_zone"          => "America/Chicago"
		);
		
		update_option( 'se_options', $defaults );
	
	}

}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_init', 'posk_init' )
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_init' HOOK FIRES, AND REGISTERS YOUR PLUGIN
// SETTING WITH THE WORDPRESS SETTINGS API. YOU WON'T BE ABLE TO USE THE SETTINGS
// API UNTIL YOU DO.
// ------------------------------------------------------------------------------

// Init plugin options to white list our options
function se_init() {
	register_setting( 'se_plugin_options', 'se_options', 'se_validate_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_menu', 'posk_add_options_page');
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_menu' HOOK FIRES, AND ADDS A NEW OPTIONS
// PAGE FOR YOUR PLUGIN TO THE SETTINGS MENU.
// ------------------------------------------------------------------------------

// Add menu page
function se_add_options_page() {
	add_options_page( 'Simple Events', SE_PLUGINOPTIONS_NICK, 'manage_options', SE_PLUGINOPTIONS_ID, 'se_render_form' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION SPECIFIED IN: add_options_page()
// ------------------------------------------------------------------------------
// THIS FUNCTION IS SPECIFIED IN add_options_page() AS THE CALLBACK FUNCTION THAT
// ACTUALLY RENDER THE PLUGIN OPTIONS FORM AS A SUB-MENU UNDER THE EXISTING
// SETTINGS ADMIN MENU.
// ------------------------------------------------------------------------------

// Render the Plugin options form
function se_render_form() {

	global $se_options, $se_data; ?>

	<div id="se-options" class="wrap">

		<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'google_calendar_settings_options'; ?>
        
		<h2 class="nav-tab-wrapper">
			<a href="?page=simple-events&tab=google_calendar_settings_options" class="nav-tab <?php echo $active_tab == 'google_calendar_settings_options' ? 'nav-tab-active' : ''; ?>">Google Calendar Settings</a> 
		  <a href="?page=simple-events&tab=settings_options" class="nav-tab <?php echo $active_tab == 'settings_options' ? 'nav-tab-active' : ''; ?>">Settings</a>
		  <a href="?page=simple-events&tab=wiki_options" class="nav-tab <?php echo $active_tab == 'wiki_options' ? 'nav-tab-active' : ''; ?>">Wiki</a>  
		</h2>

		<?php if ( $active_tab == 'google_calendar_settings_options' ) : ?>

    <div class="se-options-section">

	    <form action="options.php" method="post" id="<?php echo SE_PLUGINOPTIONS_ID; ?>-options-form" name="<?php echo SE_PLUGINOPTIONS_ID; ?>-options-form">

	    	<?php settings_fields('se_plugin_options'); ?>

    		<table class="form-table">
					
					<tr>

						<th>
				    	<label for="se_google_cal_id">Enable Google Calendar API</label>
				    </th>

				    <td>
				    	<input type="checkbox" id="google_cal" name="se_options[google_cal]" value="1" <?php if ( isset($se_options['google_cal']) ) { checked( $se_options['google_cal'], 1 ); } ?> />
						</td>

					</tr>

					<tr>

						<th>
				    	<label for="se_google_cal_id">Google Calendar ID</label>
				    </th>
				    
				    <td>
				    	<input type="text" name="se_options[google_cal_id]" value="<?php echo $se_options['google_cal_id']; ?>" id="google_cal_id" />
						</td>

					</tr>

					<tr>
						
						<th>
				    	<label for="se_google_cal_api_key">Google Calendar API Key</label>
				    </th>
				    
				    <td>
				    	<input type="text" name="se_options[google_cal_api_key]" value="<?php if ( isset($se_options['google_cal_api_key']) ) { echo $se_options['google_cal_api_key']; } ?>" id="google_cal_api_key" />
						</td>

					</tr>

				</table>

				<div class="se-form-action">
          <p><input name="Submit" type="submit" value="<?php esc_attr_e('Update Settings'); ?>" class="button-primary" /></p>
        </div>

			</form>

		</div>

    <?php endif; ?>

		<?php if ( $active_tab == 'settings_options' ) : ?>

		<div class="se-options-section">

			<form action="options.php" method="post" id="<?php echo SE_PLUGINOPTIONS_ID; ?>-options-form" name="<?php echo SE_PLUGINOPTIONS_ID; ?>-options-form">

	    	<?php settings_fields('se_plugin_options'); ?>

			  <table class="form-table">
					
					<tr>

						<th>
						  <label for="se_skin">Skin</label>
						</th>
						
						<td>
							<select name='se_options[skin]'>
								<option value='none' <?php selected('none', $se_options['skin']); ?>>&mdash; None &mdash;</option>
								<?php
								if ($handle = opendir(SE_PATH . 'assets/css/skins')) {
								    while (false !== ($entry = readdir($handle))) {
								    	if ($entry != "." && $entry != "..") { ?>
								        	<option value='<?php echo $entry; ?>' <?php selected($entry, $se_options['skin']); ?>><?php echo ucfirst($entry); ?></option>
								    	<?php }
								    }
								    closedir($handle);
								}
								?>
							</select>
						</td>

					</tr>
					
					<tr>
						
						<th>
						  <label for="se_cal_view">Calendar View</label>
						</th>
						
						<td>
							
							<select id="se_cal_view" name='se_options[cal_view]'>
							  <option value='list' <?php selected('list', $se_options['cal_view']); ?>>List</option>
							  <option value='calendar' <?php selected('calendar', $se_options['cal_view']); ?>>Calendar</option>
							</select>
						
						</td>

					</tr>
					
					<tr>
						
						<th>
						  <label for="se_time_zone">Time Zone</label>
						</th>
						
						<td>
							<select id="se_time_zone" name='se_options[time_zone]'>
								<option value='none' <?php selected('none', $se_options['time_zone']); ?>>&mdash; None &mdash;</option>
			        	<option value='America/Puerto_Rico' <?php selected('America/Puerto_Rico', $se_options['time_zone']); ?>>AST</option>
			        	<option value='America/New_York' <?php selected('America/New_York', $se_options['time_zone']); ?>>EDT</option>
			        	<option value='America/Chicago' <?php selected('America/Chicago', $se_options['time_zone']); ?>>CDT</option>
			        	<option value='America/Boise' <?php selected('America/Boise', $se_options['time_zone']); ?>>MDT</option>
			        	<option value='America/Phoenix' <?php selected('America/Phoenix', $se_options['time_zone']); ?>>MST</option>
			        	<option value='America/Los_Angeles' <?php selected('America/Los_Angeles', $se_options['time_zone']); ?>>PDT</option>
			        	<option value='America/Juneau' <?php selected('America/Juneau', $se_options['time_zone']); ?>>AKDT</option>
			        	<option value='Pacific/Honolulu' <?php selected('Pacific/Honolulu', $se_options['time_zone']); ?>>HST</option>
			        	<option value='Pacific/Guam' <?php selected('Pacific/Guam', $se_options['time_zone']); ?>>ChST</option>
			        	<option value='Pacific/Samoa' <?php selected('Pacific/Samoa', $se_options['time_zone']); ?>>SST</option>
			        	<option value='Pacific/Wake' <?php selected('Pacific/Wake', $se_options['time_zone']); ?>>WAKT</option>
							</select>
						</td>

					</tr>
						
				</table>
			
				<div class="se-form-action">
	        <p><input name="Submit" type="submit" value="<?php esc_attr_e('Update Settings'); ?>" class="button-primary" /></p>
	      </div>

			</form>

		</div>

		<?php endif; ?>

		<?php if ( $active_tab == 'wiki_options' ) : ?>

		<div class="se-options-section">

	  	<div class="se-copy">

	  	<?php

    		$text = file_get_contents( SE_PATH . 'README.md' );

    		if ( $text ) {
					$html = Markdown($text);
					echo $html;
				} else {
					echo '<h1>Issue retrieving plugin information</h1>';
				}

			?>

			</div>		

		</div>

		<?php endif; ?>

		<div class="credits">
			<p><?php echo $se_data['Name']; ?> Plugin | Version <?php echo $se_data['Version']; ?> | <a href="<?php echo $se_data['PluginURI']; ?>">Plugin Website</a> | Author <a href="<?php echo $se_data['AuthorURI']; ?>"><?php echo $se_data['Author']; ?></a> | <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" style="position:relative; top:3px; margin-left:3px"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/80x15.png" /></a><a href="http://joshuaadrian.com" target="_blank" class="alignright"><img src="<?php echo plugins_url( 'assets/img/ja-logo.png' , __FILE__ ); ?>" alt="Joshua Adrian" /></a></p>
		</div>

	</div>

<?php

}

/************************************************************************/
/* Sanitize and validate input. Accepts an array, return a sanitized array.
/************************************************************************/

function se_validate_options($input) {
	// strip html from textboxes
	// $input['textarea_one'] =  wp_filter_nohtml_kses($input['textarea_one']); // Sanitize textarea input (strip html tags, and escape characters)
	return $input;
}

/************************************************************************/
/* Display a Settings link on the main Plugins page
/************************************************************************/

function se_plugin_action_links( $links, $file ) {
	$tmp_id = SE_PLUGINOPTIONS_ID . '/simple-events.php';
	if ( $file == $tmp_id ) {
		$se_links = '<a href="' . get_admin_url() . 'options-general.php?page=' . SE_PLUGINOPTIONS_ID . '">' . __('Settings') . '</a>';
		array_unshift( $links, $se_links );
	}
	return $links;
}

/************************************************************************/
/* IMPORT CSS AND JAVASCRIPT STYLES
/************************************************************************/
function se_plugin_enqueue() {
	wp_enqueue_style( 'simple_events_admin_css', plugins_url('/assets/css/simple-events-admin.css', __FILE__), false, '1.0.0' );
	wp_enqueue_script( 'simple_events_admin_js', plugins_url('/assets/js/simple-events.min.js', __FILE__), array('jquery'), '1.0.0', true );
}

add_action( 'admin_enqueue_scripts', 'se_plugin_enqueue' );

function se_plugin_skin_styles() {

	global $se_options;

	wp_enqueue_style('simple_events_admin_css', plugins_url('/assets/css/simple-events.css', __FILE__), false, '1.0.0');
	wp_enqueue_script('simple_events_js', plugins_url('/assets/js/simple-events.min.js', __FILE__), array('jquery', 'jquery_ui'), '1.0.0', true);

	if ( isset( $se_options['skin'] ) && $se_options['skin'] != 'none' && $skin_json ) {

		$skin_json = json_decode( file_get_contents( SE_PATH . 'assets/css/skins/' . $se_options['skin'] . '/' . $se_options['skin'] . '.json' ) );

		$dependencies = array();

		if ($skin_json->css) {
			wp_enqueue_style('se-skin-default', plugins_url('/assets/css/skins/'.$se_options['skin'].'/'.$se_options['skin'].'.css', __FILE__), false, '1.0.0');
		}
		if ($skin_json->js_dependencies) {
			array_push($dependencies, $skin_json->js_dependencies);
		}
		if ($skin_json->js) {
			wp_enqueue_script('se-skin-default', plugins_url('/assets/css/skins/'.$se_options['skin'].'/'.$se_options['skin'].'.min.js', __FILE__), $dependencies, '1.0.0', true);
		}
		
	}

}

add_action('wp_enqueue_scripts', 'se_plugin_skin_styles');

/************************************************************************/
/* INCLUDES
/************************************************************************/

if ( isset( $se_options['google_cal'] ) && $se_options['google_cal'] ) {
	
	require SE_PATH . 'assets/inc/simple-events-google-calendar-cron.php';

} else {

	if ( wp_next_scheduled( 'simple_events_cron' ) ) {
		
		wp_clear_scheduled_hook('simple_events_cron');
	
	}

}

require SE_PATH . 'assets/inc/simple-events-functions.php';
require SE_PATH . 'assets/inc/simple-events-custom-post-type.php';
require SE_PATH . 'assets/inc/simple-events-metaboxes.php';
require SE_PATH . 'assets/inc/simple-events-shortcodes.php';
require SE_PATH . 'assets/inc/simple-events-widgets.php';
require SE_PATH . 'assets/inc/simple-events-ical-feed.php';

?>