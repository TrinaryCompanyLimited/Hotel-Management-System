<?php
class MightyCall_Contact_Page_Logger {
	const message_prefix = '[MightyCallContactPage] ';

	public static function log( $message ) {
		if ( WP_DEBUG === true ) {
			if( is_array( $message ) || is_object( $message ) ) {
				error_log( self::message_prefix . print_r($message, true) );
			} else {
				error_log( self::message_prefix . $message );
			}
		}
	}
}