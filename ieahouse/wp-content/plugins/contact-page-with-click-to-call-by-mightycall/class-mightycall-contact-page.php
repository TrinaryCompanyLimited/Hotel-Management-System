<?php

class MightyCall_Contact_Page {

	public static function init($main_file) {
		self::log( 'Initializing plugin' );

		$plugin = MightyCall_Contact_Page_Plugin::get_instance();

		register_activation_hook( $main_file, array( &$plugin, 'activate' ) );
		register_deactivation_hook( $main_file, array( &$plugin, 'deactivate' ) );

		$plugin->add_actions();

		self::log( 'Successfully initialized' );
	}

	private static function log( $message ) {
		MightyCall_Contact_Page_Logger::log( $message );
	}
}