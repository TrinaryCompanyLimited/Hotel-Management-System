<?php
//namespace plugins\screenreader;  
/** 
 * Main wp install and render plugin
 * @package SCREENREADER::plugins
 * @author JExtensions Store 
 * @copyright (C) 2016 - JExtensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */

/*
 Plugin Name: Screen Reader Free
 Plugin URI: http://storejextensions.org/extensions/screen_reader.html
 Description: Screen Reader plugin free version. The free version is limited to read only 100 characters and has only few features. Visit our website at <a href="http://storejextensions.org/extensions/screen_reader.html">http://storejextensions.org/extensions/screen_reader.html</a> to get the full and unlimited version of the plugin. 
 Author: JExtensions Store
 Version: 3.7
 Author URI: http://storejextensions.org
*/

// SITE SECTION OUTPUT SCRIPTS
function screenreader() { 
	global $wpdb, $table_prefix;
	// CONFIG LOAD DA DB OPTIONS
	$screenreaderQuery = "SELECT * FROM " . $table_prefix . "screenreader_config";
	$pparams = $wpdb->get_row($screenreaderQuery);
	
	$siteUrl = plugins_url('/', __FILE__);
	$langTag = get_bloginfo ( 'language' );
	
	if($pparams->sef_lang_code) {
		$explodedLangTag = explode ( '-', $langTag );
		$langCode = array_shift ( $explodedLangTag );
	} else {
		$langCode = $langTag;
	}
		
	$token = md5 ( $_SERVER ["HTTP_HOST"] );
		
	// Security safe
	if (! file_exists( dirname(__FILE__) . '/languages/' . $langTag . '.js' )) {
		$langTag = 'en-GB';
	}
	
	// Ensure that the chunk length is correct for Google
	if($pparams->reader_engine == 'proxy' && $pparams->chunksize > 100) {
		$pparams->chunksize = 90;
	}
		
	// Ensure that the chunk length is correct for Virtual Readers
	if($pparams->reader_engine == 'proxy_virtual' && $pparams->chunksize >= 300) {
		$pparams->chunksize = 290;
	}
	
	wp_enqueue_style('screenreader-styles-handle', plugins_url('libraries/controller/css/' . $pparams->template, __FILE__ ));
	
	// Load jQuery in safe way
	wp_enqueue_script('jquery');
	
	$soundManagerConfig = array('url' => $siteUrl . 'libraries/tts/soundmanager/swf/',
								'volume' => $pparams->volume_tts );
	wp_register_script( 'screenreader-soundmanagerparams-handle', plugins_url('libraries/js/params.js', __FILE__ ));
	wp_localize_script( 'screenreader-soundmanagerparams-handle', 'screenReaderSoundManagerOptions', $soundManagerConfig ); //pass 'object_name' to script.js
	wp_enqueue_script( 'screenreader-soundmanagerparams-handle' );
	
	wp_enqueue_script( 'screenreader-language-handle', plugins_url('languages/' . $langTag . '.js', __FILE__ ));
	wp_enqueue_script( 'screenreader-soundmanager-handle', plugins_url('libraries/tts/soundmanager/soundmanager2.js', __FILE__ ));
	wp_enqueue_script( 'screenreader-ttsengine-handle', plugins_url('libraries/tts/tts.js', __FILE__ ));
	wp_enqueue_script( 'screenreader-controller-handle', plugins_url('libraries/controller/controller.js', __FILE__ ));

	$paramsArray = array(	'baseURI' => $siteUrl,
							'token' => $token,
							'langCode' => $langCode,
							'chunkLength' => 100,
							'screenReaderVolume' => $pparams->volume_tts,
							'position' => 'bottomright',
							'scrolling' => 'fixed',
							'targetAppendto' => 'body',
							'targetAppendMode' => 'bottom',
							'preload' => 0,
							'readPage' => $pparams->read_page,
							'readChildNodes' => $pparams->read_child_nodes,
							'ieHighContrast' => 0,
							'ieHighContrastAdvanced' => 0,
							'excludeScripts' => $pparams->exclude_scripts,
							'readImages' => 0,
							'readImagesAttribute' => 'alt',
							'readImagesOrdering' => 'before',
							'mainpageSelector' => $pparams->mainpage_selector,
							'showlabel' => $pparams->showlabel,
							'screenreader' => $pparams->screenreader,
							'highcontrast' => 0,
							'highcontrastAlternate' => 0,
							'colorHue' => 180,
							'colorBrightness' => 6,
							'fontsize' => $pparams->fontsize,
							'fontsizeDefault' => $pparams->font_size_default,
							'fontsizeMin' => $pparams->font_size_min,
							'fontsizeMax' => $pparams->font_size_max,
							'fontsizeSelector' => $pparams->fontsize_selector,
							'fontSizeOverride' => $pparams->fontsize_selector_mode,
							'fontSizeHeadersIncrement' => $pparams->fontsize_headers_increment,
							'toolbarBgcolor' => $pparams->toolbar_bgcolor,
							'template' => $pparams->template,
							'accesskey_play' => 'P',
							'accesskey_pause' => 'E',
							'accesskey_stop' => 'S',
							'accesskey_increase' => 'O',
							'accesskey_decrease' => 'U',
							'accesskey_reset' => 'R',
							'accesskey_highcontrast' => 'H',
							'accesskey_highcontrast2' => 'J',
							'accesskey_highcontrast3' => 'K',
							'readerEngine' => $pparams->reader_engine,
							'hideOnMobile' =>  $pparams->hide_on_mobile);
	
	
	wp_register_script( 'screenreader-params-handle', plugins_url('libraries/js/params.js', __FILE__ ));
	wp_localize_script( 'screenreader-params-handle', 'screenReaderConfigOptions', $paramsArray ); //pass 'object_name' to script.js
	wp_enqueue_script( 'screenreader-params-handle' );
}

// Now we set that function up to execute when the admin_notices action is called 
add_action( 'wp_head', 'screenreader' );  

function screenreader_install() {
	global $wpdb, $table_prefix;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$sqlCreateConfigTable = "DROP TABLE IF EXISTS `" . $table_prefix . "screenreader_config`;
	 CREATE TABLE IF NOT EXISTS `" . $table_prefix . "screenreader_config` (
		`id` int(11) NOT NULL DEFAULT 1 PRIMARY KEY,
		`volume_tts` int(11) NOT NULL DEFAULT 80,
		`read_page` tinyint(4) NOT NULL DEFAULT 1,
		`read_child_nodes` tinyint(4) NOT NULL DEFAULT 1,
		`exclude_scripts` tinyint(4) NOT NULL DEFAULT 1,
		`mainpage_selector` varchar(255) NOT NULL DEFAULT '*[name*=main], *[class*=main], *[id*=main]',
		`showlabel` tinyint(4) NOT NULL DEFAULT 1,
		`screenreader` tinyint(4) NOT NULL DEFAULT 1,
		`fontsize` tinyint(4) NOT NULL DEFAULT 1,
		`font_size_default` int(11) NOT NULL DEFAULT 100,
		`font_size_min` int(11) NOT NULL DEFAULT 50,
		`font_size_max` int(11) NOT NULL DEFAULT 200,
		`fontsize_selector` varchar(255) NOT NULL DEFAULT '',
		`fontsize_selector_mode` tinyint(4) NOT NULL DEFAULT 1,
		`fontsize_headers_increment` int(11) NOT NULL DEFAULT 20,
		`template` varchar(255) NOT NULL DEFAULT 'main.css',
		`toolbar_bgcolor` varchar(255) NOT NULL DEFAULT '#EEE',
		`hide_on_mobile` tinyint(4) NOT NULL DEFAULT 0,
		`sef_lang_code` tinyint(4) NOT NULL DEFAULT 1,
		`reader_engine` varchar(255) NOT NULL DEFAULT 'proxy'
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	dbDelta($sqlCreateConfigTable);
} 
register_activation_hook(__FILE__,'screenreader_install');
 
function screenreader_install_data() {
	global $wpdb, $table_prefix;  
	$table_name = 'screenreader_config';
	$wpdb->insert( $table_prefix . $table_name, array( 'id' => 1 ));
}
register_activation_hook(__FILE__,'screenreader_install_data'); 

function screenreader_uninstall() {
	global $wpdb, $table_prefix;
	$table_name = 'screenreader_config';
	$wpdb->query( "DROP TABLE " . $table_prefix . $table_name);
}
register_uninstall_hook( __FILE__, 'screenreader_uninstall' );

// ADMIN SECTION
if ( is_admin() )
	require_once dirname( __FILE__ ) . '/admin.php';