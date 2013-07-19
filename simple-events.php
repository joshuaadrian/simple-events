<?php

/*
Plugin Name: Simple Events
Plugin URI: https://github.com/joshuaadrian/simple-events
Description: Create, manage and place events easily with this plugin. Place them with shortcodes on pages, posts, and/or widgets.
Author: Joshua Adrian
Version: 0.5.0
Author URI: http://joshuaadrian.com
*/

/************************************************************************/
/* DEFINE PLUGIN ID AND NICK
/************************************************************************/
$se_data;
// DEFINE PLUGIN BASE
define( 'SE_PATH', plugin_dir_path(__FILE__) );
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
add_filter( 'plugin_action_links', 'se_plugin_action_links', 10, 2 );
// GET PLUGIN DATA
if ( !function_exists( 'get_plugins' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
if ( is_admin() ) {
  $se_data = get_plugin_data( SE_PATH . plugin_basename( dirname( __FILE__ ) ) . '.php', false, false );
}
// PLUGIN OPTIONS
$se_options = get_option('se_options');


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
	$tmp = get_option('se_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('se_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(
			"google"                 => false,
			"google_cal_id"          => "",
			"skin"                   => "none",
			"cal_view"               => "list",
			"time_zone"              => "America/Chicago"
		);
		update_option('se_options', $arr);
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
	add_options_page( 'Simple Events', '<img class="menu_se" src="' . plugins_url( 'assets/img/simple-events.gif' , __FILE__ ) . '" alt="" />'.SE_PLUGINOPTIONS_NICK, 'manage_options', SE_PLUGINOPTIONS_ID, 'se_render_form' );
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
	global $se_data;
	
	?>
	
	<div class="wrap">

	    <?php screen_icon(); ?>

		    <h2>Simple Events Settings</h2>

		    <ul class="se_pagination group">
		    	<li id="se-pagination-settings" class="se-active">
		    		<a href="#se-settings">Settings</a>
		    	</li>
		    	<li id="se-pagination-help">
		    		<a href="#se-help">Help</a>
		    	</li>
		    </ul>
		    <form action="options.php" method="post" id="<?php echo SE_PLUGINOPTIONS_ID; ?>-options-form" name="<?php echo SE_PLUGINOPTIONS_ID; ?>-options-form">

	    	<?php
				settings_fields('se_plugin_options');
				$options = get_option('se_options');
			?>

		    <ul class="se_content">
		    	<li id="se-settings" class="se-active">
					<h3>Google Calendar Settings</h3>
					<table class="form-table">
				    	<tr>
							<th>
					    		<label for="se_google">Use Google Calendar To Handle Events</label>
					    	</th>
					    	<td>

							</td>
						</tr>
						<tr>
							<th>
					    		<label for="se_google_cal_id">Google Calendar ID</label>
					    	</th>
					    	<td>
					    		<input type="text" name="se_options[google_cal_id]" value="<?php echo $options['google_cal_id']; ?>" id="google_cal_id" />
							</td>
						</tr>
					</table>
					<h3>Style Settings</h3>
		    		<table class="form-table">
				    	<tr>
							<th>
					    		<label for="se_skin">Skin</label>
					    	</th>
					    	<td>
								<select name='se_options[skin]'>
									<option value='none' <?php selected('none', $options['skin']); ?>>&mdash; None &mdash;</option>
									<?php
									if ($handle = opendir(SE_PATH . 'assets/css/skins')) {
									    while (false !== ($entry = readdir($handle))) {
									    	if ($entry != "." && $entry != "..") { ?>
									        	<option value='<?php echo $entry; ?>' <?php selected($entry, $options['skin']); ?>><?php echo ucfirst($entry); ?></option>
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
						        	<option value='list' <?php selected('list', $options['cal_view']); ?>>List</option>
						        	<option value='calendar' <?php selected('calendar', $options['cal_view']); ?>>Calendar</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>
					    		<label for="se_time_zone">Time Zone</label>
					    	</th>
					    	<td>
								<select id="se_time_zone" name='se_options[time_zone]'>
									<option value='none' <?php selected('none', $options['time_zone']); ?>>&mdash; None &mdash;</option>
						        	<option value='America/Puerto_Rico' <?php selected('America/Puerto_Rico', $options['time_zone']); ?>>AST</option>
						        	<option value='America/New_York' <?php selected('America/New_York', $options['time_zone']); ?>>EDT</option>
						        	<option value='America/Chicago' <?php selected('America/Chicago', $options['time_zone']); ?>>CDT</option>
						        	<option value='America/Boise' <?php selected('America/Boise', $options['time_zone']); ?>>MDT</option>
						        	<option value='America/Phoenix' <?php selected('America/Phoenix', $options['time_zone']); ?>>MST</option>
						        	<option value='America/Los_Angeles' <?php selected('America/Los_Angeles', $options['time_zone']); ?>>PDT</option>
						        	<option value='America/Juneau' <?php selected('America/Juneau', $options['time_zone']); ?>>AKDT</option>
						        	<option value='Pacific/Honolulu' <?php selected('Pacific/Honolulu', $options['time_zone']); ?>>HST</option>
						        	<option value='Pacific/Guam' <?php selected('Pacific/Guam', $options['time_zone']); ?>>ChST</option>
						        	<option value='Pacific/Samoa' <?php selected('Pacific/Samoa', $options['time_zone']); ?>>SST</option>
						        	<option value='Pacific/Wake' <?php selected('Pacific/Wake', $options['time_zone']); ?>>WAKT</option>
								</select>
							</td>
						</tr>
					</table>
		    	</li>
		    	<li id="se-help">
		    		<div class="se-copy">
						<h2>Using the ShortCodes and Their Options</h2>

						<h3>Usage</h3>
						
						<p>You may place the shortcodes in pages, posts, and/or widgets.</p>

						<h3>Twitter</h3>
						<p>
							This is the basic usage it will return the tweets in an unordered list.
							<pre><code>[twitter_feed]</code></pre>
						</p>

						<p>
							The Twitter shortcode has one option, <strong>count</strong>.
							<pre><code>[twitter_feed count="2"]</code></pre>
							The count number must be less than the global twitter count you have set on the twitter tab.
						</p>

						<h3>Instagram</h3>
						<p>
							This is the basic usage it will return the tweets in an unordered list.
							<pre><code>[instagram_feed]</code></pre>
						</p>

						<p>
							The Twitter shortcode has one option, <strong>count</strong>.
							<pre><code>[instagram_feed count="2"]</code></pre>
							The count number must be less than the global instagram count you have set on the instagram tab.
						</p>

						<h2>Using and Creating Skins</h2>

						<p>The default skin is placed in the plugins/simple-events/css/skins/ folder. You may create or add a new skin by simply adding your skin folder to this folder.</p>

						<p>I've included a clean, simple skin called 'Fresh' that you are free to modify for your needs but would back it up since new versions of this plugin will overwrite everything in the simple Events folder.</p>
						
					</div>
					<?php
					echo 'PLUGIN PATH => ' . SE_PATH . '<br />';
					var_dump($options);
					?>			
		    	</li>
		    </ul>
			
		    <p class="submit"><input name="Submit" type="submit" value="<?php esc_attr_e('Update Settings'); ?>" class="button-primary" /></p>
		</form>
		<div class="credits">
			<p><?php echo $se_data['Name']; ?> Plugin | Version <?php echo $se_data['Version']; ?> | <a href="<?php echo $se_data['PluginURI']; ?>">Plugin Website</a> | Author <a href="<?php echo $se_data['AuthorURI']; ?>"><?php echo $se_data['Author']; ?></a> | <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" style="position:relative; top:3px; margin-left:3px"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/80x15.png" /></a><a href="http://joshuaadrian.com" target="_blank" class="alignright"><img src="<?php echo plugins_url( 'assets/img/ja-logo.gif' , __FILE__ ); ?>" alt="Joshua Adrian" /></a></p>
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
	$tmp_id = SE_PLUGINOPTIONS_ID . '/index.php';
	if ($file == $tmp_id) {
		$se_links = '<a href="'.get_admin_url().'options-general.php?page='.SE_PLUGINOPTIONS_ID.'">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $se_links );
	}
	return $links;
}

/************************************************************************/
/* IMPORT CSS AND JAVASCRIPT STYLES
/************************************************************************/
function se_plugin_enqueue() {
	wp_enqueue_style('simple_events_admin_css', plugins_url('/assets/css/simple-events-admin.css', __FILE__), false, '1.0.0');
	wp_enqueue_script('simple_events_admin_js', plugins_url('/assets/js/simple-events-admin.min.js', __FILE__), array('jquery'), '1.0.0', true);
}

add_action('admin_enqueue_scripts', 'se_plugin_enqueue');

function se_plugin_skin_styles() {
	$skin = get_option('se_options');
	$skin = $skin['skin'];
	$skin_json = json_decode(file_get_contents(SE_PATH . 'assets/css/skins/'.$skin.'/'.$skin.'.json'));

	wp_enqueue_style('simple_events_admin_css', plugins_url('/assets/css/simple-events.css', __FILE__), false, '1.0.0');
	wp_enqueue_script('simple_events_js', plugins_url('/assets/js/simple-events.min.js', __FILE__), array('jquery', 'jquery_ui'), '1.0.0', true);

	if ( $skin != 'none' && $skin_json ) {

		$dependencies = array();

		if ($skin_json->css) {
			wp_enqueue_style('se-skin-default', plugins_url('/assets/css/skins/'.$skin.'/'.$skin.'.css', __FILE__), false, '1.0.0');
		}
		if ($skin_json->js_dependencies) {
			array_push($dependencies, $skin_json->js_dependencies);
		}
		if ($skin_json->js) {
			wp_enqueue_script('se-skin-default', plugins_url('/assets/css/skins/'.$skin.'/'.$skin.'.min.js', __FILE__), $dependencies, '1.0.0', true);
		}
		
	}
}

add_action('wp_enqueue_scripts', 'se_plugin_skin_styles');

/************************************************************************/
/* INCLUDES
/************************************************************************/

if ( !isset($se_options['google_cal_id']) || empty($se_options['google_cal_id']) ) {
	require SE_PATH . 'assets/inc/simple-events-custom-post-type.php';
	require SE_PATH . 'assets/inc/simple-events-metaboxes.php';
}
require SE_PATH . 'assets/inc/simple-events-shortcodes.php';
require SE_PATH . 'assets/inc/simple-events-widgets.php';
require SE_PATH . 'assets/inc/simple-events-functions.php';

?>