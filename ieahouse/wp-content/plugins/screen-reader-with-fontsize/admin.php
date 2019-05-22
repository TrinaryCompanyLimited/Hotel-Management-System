<?php 
//namespace plugins\screenreader;  
/** 
 * Main wp admin render plugin
 * @package SCREENREADER::plugins
 * @author JExtensions Store 
 * @copyright (C) 2016 - JExtensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */

// Aggiunge il link settings
function screenreader_plugin_action_links($links, $file) {
	if ($file == plugin_basename ( dirname ( __FILE__ ) . '/screenreader.php' )) {
		$links [] = '<a href="admin.php?page=screenreader-key-config">' . __ ( 'Settings' ) . '</a>';
	}
	
	return $links;
}
add_filter ( 'plugin_action_links', 'screenreader_plugin_action_links', 10, 2 );

// Renderizza il form di configurazione e salva i dati sul DB
function screenreader_conf() {
	global $wpdb, $table_prefix;
	// Live Site
	$siteUrl = plugins_url('/', __FILE__); 
	// CONFIG LOAD DA DB OPTIONS
	$screenreaderQuery = "SELECT * FROM " . $table_prefix . "screenreader_config";
	$screenreaderConfig = $wpdb->get_row($screenreaderQuery);
 
	// Save config da POST submit
	if ( isset($_POST['submit']) ) {
		$fields = array();
		foreach ($screenreaderConfig as $paramName=>&$paramValue) {
			$paramValue = $_POST[$paramName];
			$fields[$paramName] = $paramValue;
		} 
		if($wpdb->update($table_prefix . 'screenreader_config', $fields, array('id'=>1))) {
			echo '<div id="message" class="updated"><p>Settings saved</p></div>'; 
		} else {
			echo '<div id="message" class="updated"><p>Settings up-to-date</p></div>'; 
		}
	}
	 
	// Config form generation
	if(is_object($screenreaderConfig)) {
		$config = null;
		foreach ($screenreaderConfig as $paramNameForm=>$paramValueForm) {
			switch($paramNameForm) {
				case 'volume_tts':
					$config .= "<div style='min-height:40px;'><label style='float:left;font-weight:bold;font-size:21px;text-decoration:underline'>Main settings</label></div>";
					break;
				case 'showlabel':
					$config .= "<div style='min-height:40px;'><label style='float:left;font-weight:bold;font-size:21px;text-decoration:underline'>Appearance</label></div>";
					break;				
				case 'jquery_include':
					$config .= "<div style='min-height:40px;'><label style='float:left;font-weight:bold;font-size:21px;text-decoration:underline'>Advanced settings</label></div>";
					break;
				case 'accesskey_play':
					$config .= "<div style='min-height:40px;'><label style='float:left;font-weight:bold;font-size:21px;text-decoration:underline'>Accesskeys</label></div>";
					break;
			}
			
			$labelValues = screenreaderTransformFunctionLabel($paramNameForm);
			$config .= "<div style='min-height:40px;'><label title='" . $labelValues[1] . "' style='float:left;width:240px'>" . $labelValues[0] . "</label>";
			$config .= screenreaderTransformFunctionInput($paramNameForm, $paramValueForm);
			$config .= "</div>";
		} 
	} 
	?>
<fieldset
	style="border: 1px solid #CCC; padding: 20px; margin-top: 30px;">
	<legend style="width: 580px;">
		<img src="<?php echo $siteUrl;?>config_icon.png" alt="config_icon" />
		<label style="font-weight: bold; margin-top: 50px; display: block; float: right; font-size: 24px;">Screen Reader free version configuration</label>
	</legend>
	<div style="margin-bottom:20px;" class="update-nag">Screen Reader plugin free version. The free version is limited to read only 100 characters and has only few features. Visit our website at <a href="http://storejextensions.org/extensions/screen_reader.html">http://storejextensions.org/extensions/screen_reader.html</a> to get the full and unlimited version of the plugin.</div>
	<form action="" method="post" id="screenreader-conf">
		<input style='float:right;width:150px;background-color:#23282D;color:#FFF;border:none;cursor:pointer;' type="submit" name="submit" value="<?php _e('Save settings'); ?>" /> 
			<?php echo $config;?>
		</form>
</fieldset>
<?php
}
  
function screenreader_load_menu() { 
	add_submenu_page ( 'plugins.php', __ ( 'Screen Reader settings' ), __ ( 'Screen Reader settings' ), 'manage_options', 'screenreader-key-config', 'screenreader_conf' ); 
}
add_action ( 'admin_menu', 'screenreader_load_menu' ); 

function screenreader_enqueue_color_picker( $hook_suffix ) {
	// first check that $hook_suffix is appropriate for your admin page
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'screenreader-colorpicker-handle', plugins_url('libraries/js/colorpicker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}
add_action( 'admin_enqueue_scripts', 'screenreader_enqueue_color_picker' );

/**
 * Funzione di trasformazione HTML controls, restituisce il controllo richiesto
 * @param string $control
 * @param mixed $value
 * @return string
 */
function screenreaderTransformFunctionInput($control, $value) {
	// Text translations
	static $adminLanguageStrings = null;
	if(!$adminLanguageStrings) {
		$adminLanguageFile = dirname(__FILE__) . "/languages/admin/en-GB.ini";
		if(file_exists($adminLanguageFile)) {
			$adminLanguageStrings = parse_ini_file($adminLanguageFile, false, INI_SCANNER_NORMAL);
		}
	}
	
	// Main switch
	switch ($control) {
		case 'volume_tts' :
			$options = array(20=>'20%',40=>'40%',60=>'60%',80=>'80%',100=>'100%');
			$str = '<select name="volume_tts">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
		
		case 'read_page' :
			$options = array(0=>'SELECTED_TEXT_ONLY', 1=>'MAINPAGE_PART_AND_SELECTED_TEXT');
			$str = '<select name="read_page">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'read_images_attribute' :
			$options = array('alt'=>'ALT', 'title'=>'TITLE');
			$str = '<select name="read_images_attribute">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;

		case 'read_images_ordering' :
			$options = array('before'=>'BEFORE', 'after'=>'AFTER');
			$str = '<select name="read_images_ordering">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
		
		case 'chunksize':
			$options = array (
					'20' => '20',
					'40' => '40',
					'60' => '60',
					'80' => '80',
					'90' => '90',
					'100'=>'100',
					'120' => '120',
					'140' => '140',
					'160' => '160',
					'180' => '180',
					'200' => '200',
					'240' => '240',
					'280' => '280',
					'300' => '280',
					'400' => '400',
					'500' => '500',
					'600'=>'600',
					'700' => '700',
					'800' => '800',
					'900'=>'900',
					'1000'=>'1000');
			$str = '<select name="chunksize">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'highcontrast_alternate_color_hue' :
			$options = array(45=>'FONTSIZE_VERYLOW',
							 90=>'FONTSIZE_LOW',
							 180=>'FONTSIZE_MEDIUM',
							 225=>'FONTSIZE_AVERAGE',
							 270=>'FONTSIZE_HIGH',
							 305=>'FONTSIZE_VERYHIGH');
			$str = '<select name="highcontrast_alternate_color_hue">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'highcontrast_alternate_color_brightness' :
			$options = array(2=>'FONTSIZE_VERYLOW',
							 4=>'FONTSIZE_LOW',
							 6=>'FONTSIZE_AVERAGE',
							 8=>'FONTSIZE_HIGH',
							 10=>'FONTSIZE_VERYHIGH');
			$str = '<select name="highcontrast_alternate_color_brightness">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
				
		case 'fontsize_selector_mode' :
			$options = array(0=>'APPEND', 1=>'OVERRIDE');
			$str = '<select name="fontsize_selector_mode">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
				
		case 'corner_position' :
			$options = array('topright'=>'TOP_RIGHT',
							 'bottomright'=>'BOTTOM_RIGHT',
							 'topleft'=>'TOP_LEFT',
							 'bottomleft'=>'BOTTOM_LEFT');
			$str = '<select name="corner_position">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'template' :
			$options = array('main.css'=>'MAIN_TEMPLATE');
			$str = '<select name="template">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
				
		case 'scrolling' :
			$options = array('fixed'=>'FIXED_IMAGE',
							 'absolute'=>'SCROLLING_IMAGE');
			$str = '<select name="scrolling">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
				
		case 'target_append_mode' :
			$options = array('top'=>'TARGET_APPEND_MODE_TOP',
							 'bottom'=>'TARGET_APPEND_MODE_BOTTOM');
			$str = '<select name="target_append_mode">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'reader_engine' :
			$options = array('proxy_virtual'=>'READER_ENGINE_VIRTUALSPEAKER',
							 'proxy'=>'READER_ENGINE_GOOGLE');
			$str = '<select name="reader_engine">' . screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'script_responsivevoice_loading' :
			$options = array('local'=>'SCRIPT_LOADING_LOCAL_SERVER',
							 'remote'=>'SCRIPT_LOADING_REMOTE_SERVER');
			$str = '<select name="script_responsivevoice_loading">' . screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
				
		case 'toolbar_bgcolor':
			$str = "<input type='text' value='" . $value . "' name='toolbar_bgcolor' class='my-color-field' data-default-color='#EEE'/>";
			break;
		
		case 'read_child_nodes':
		case 'exclude_scripts':
		case 'read_images':
		case 'showlabel':
		case 'screenreader':
		case 'fontsize':
		case 'highcontrast':
		case 'highcontrast_alternate':
		case 'hide_on_mobile':
		case 'preload':
		case 'ie_highcontrast':
		case 'ie_highcontrast_advanced':
		case 'sef_lang_code':
			$checked = (bool)$value ? 'checked' : '';
			$nochecked = !(bool)$value ? 'checked' : '';
			$str = "<input type='radio' name='$control' $nochecked value='0'/>&nbsp;No";
			$str .= "&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;  <input type='radio' name='$control' $checked value='1'/>&nbsp;Yes";
			break;
			
		case 'id':
			$str = "<input type='hidden' name='id' value='1'/>";
			break;
		default:
			$styles = null;
			if(stripos($control, 'selector')) {
				$styles = "style='width: 50%'";
			}
			$str = "<input type='text' name='$control' value='$value' $styles/>";	
	} 
	return $str;
}

/**
 * Funzione di trasformazione HTML controls, restituisce il controllo richiesto
 * @param string $control 
 * @return array
 */
function screenreaderTransformFunctionLabel($controlName) {
	// Text translations
	static $adminLanguageStrings = null;
	if(!$adminLanguageStrings) {
		$adminLanguageFile = dirname(__FILE__) . "/languages/admin/en-GB.ini";
		if(file_exists($adminLanguageFile)) {
			$adminLanguageStrings = parse_ini_file($adminLanguageFile, false, INI_SCANNER_NORMAL);
		}
	}
	
	// Manage title + desc
	$titleIdentifier = strtoupper($controlName);
	$descriptionIdentifier = $titleIdentifier . '_DESC';

	if(array_key_exists($titleIdentifier, $adminLanguageStrings)) {
		$titleString = $adminLanguageStrings[$titleIdentifier];
	}
	
	if(array_key_exists($descriptionIdentifier, $adminLanguageStrings)) {
		$descriptionString = htmlspecialchars($adminLanguageStrings[$descriptionIdentifier], ENT_QUOTES, 'UTF-8');
	}

	return array($titleString, $descriptionString);
}


/**
 * Renders a generic dropdown list
 * @param string $type
 * @return array
 */
function screenreaderGenericSelectLists($optionsArray, $value, $languageStrings) {
	$optionsString = null;
	foreach ($optionsArray as $optionValue=>$optionText) {
		if(array_key_exists($optionText, $languageStrings)) {
			$optionText = $languageStrings[$optionText];
		}
		$checked = $optionValue == $value ? 'selected="selected"' : '';
		$optionsString .= '<option ' . $checked . ' value="' . $optionValue . '">' . $optionText . '</option>';
	}
	
	return $optionsString;
}
?>