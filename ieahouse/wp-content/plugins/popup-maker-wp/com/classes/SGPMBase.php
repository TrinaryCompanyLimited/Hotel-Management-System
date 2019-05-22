<?php

class SGPMBase
{
	/**
	 * Holds the class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = SGPM_VERSION;

	/**
	 * The name of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $pluginName = 'Popup Maker WP';

	/**
	 * Unique plugin slug identifier.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $pluginSlug = 'sgpmpopupmaker';

	/**
	 * Loads the plugin into WordPress.
	 *
	 * @since 1.0.0
	 */
	public function init()
	{
		// Autoload the class files.
		spl_autoload_register('SGPMBase::autoload');
		register_activation_hook(SGPM_PATH.'popup-maker-api.php', array('SGPMBase', 'activate'));
		register_uninstall_hook(SGPM_PATH.'popup-maker-api.php', array('SGPMBase', 'deactivate'));
		// update data of old user
		add_action('plugins_loaded', array($this, 'overallInit'));

		$this->helper = new SGPMHelper();
		$this->menu = new SGPMMenu();
		$this->api = new SGPMApi();
		$this->output = new SGPMOutput();
		add_action('init', array($this, 'registerDataConfig'), 99999);
	}

	public function registerDataConfig()
	{
		if (file_exists(SGPM_CLASSES.'SGPMDataConfig.php')) {
			require_once(SGPM_CLASSES.'SGPMDataConfig.php');
			SGPMDataConfig::init();
		}
	}

	public function overallInit()
	{
		$options = get_option('sgpm_popup_maker_api_option');
		if (empty($options)) {
			$options = array();
		}
		if (isset($options['pluginVersion']) && $options['pluginVersion'] >= '1.13') return;

		$options['pluginVersion'] = SGPM_VERSION;
	 	if (!isset($options['popups'])) return;

	 	foreach ($options['popups'] as $popupId => $popup) {
	  		if (!isset($options['popupsSettings'][$popupId])) continue;
	  		$popupSettings = $options['popupsSettings'][$popupId];

			if (!isset($popupSettings['displayTarget'])) {
				$popupSettings['displayTarget'] = $this->getUpdatedSettingsForOldUser($popupSettings);
				$options['popupsSettings'][$popupId] = $popupSettings;
			}
	 	}

		update_option('sgpm_popup_maker_api_option', $options);
	}

	public function getUpdatedSettingsForOldUser($popupSettings)
	{
		$updatedSettings = array();

		if (isset($popupSettings['showOnAllPosts']) && $popupSettings['showOnAllPosts'] == 'on') {
			$updatedSettings[] = array(
				'param' => 'post_all',
				'operator' => '=='
			);
		}
		if (isset($popupSettings['showOnSomePosts']) && $popupSettings['showOnSomePosts'] == 'on') {
			$updatedSettings[] = array(
				'param' => 'post_selected',
				'operator' => '==',
				'value' => $this->getSelectedPostAssocArray($popupSettings['selectedPosts'])
			);
		}
		if (isset($popupSettings['showOnAllPages']) && $popupSettings['showOnAllPages'] == 'on') {
			$updatedSettings[] = array(
				'param' => 'page_all',
				'operator' => '=='
			);
			$updatedSettings[] = array(
				'param' => 'page_type',
				'operator' => '==',
				'value' => array('is_home_page')
			);
		}
		if (isset($popupSettings['showOnSomePages']) && $popupSettings['showOnSomePages'] == 'on') {
			$updatedSettings[] = array(
				'param' => 'page_selected',
				'operator' => '==',
				'value' => $this->getSelectedPostAssocArray($popupSettings['selectedPages'])
			);

			if (in_array('-1', $popupSettings['selectedPages'])) {
				$updatedSettings[] = array(
					'param' => 'page_type',
					'operator' => '==',
					'value' => array('is_home_page')
				);
			}

		}
		return $updatedSettings;
	}

	public function getSelectedPostAssocArray($selectedPost)
	{
		$newSelectedPost = array();
		foreach ($selectedPost as $key => $selectedPostId) {
			if ($selectedPostId == '-1') continue;
			$newSelectedPost[$selectedPostId] = get_the_title($selectedPostId);
		}
		return $newSelectedPost;
	}

	public static function autoload($classname)
	{
		// Return early if not the proper classname.
		if ('SGPM' !== substr($classname, 0, 4)) {
			return;
		}
		// Check if the file exists. If so, load the file.
		$filename = SGPM_CLASSES.$classname.'.php';
		if (file_exists($filename)) {
			require_once($filename);
		}
	}

	public static function activate()
	{

	}

	public static function deactivate()
	{
		delete_option('sgpm_popup_maker_api_option');
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return SGPMBase
	 */
	public static function getInstance()
	{
		if (!isset( self::$instance ) && !(self::$instance instanceof SGPMBase)) {
			self::$instance = new SGPMBase();
		}

		return self::$instance;
	}
}
