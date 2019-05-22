<?php

class MightyCall_Contact_Page_Options {
	const options_prefix = 'MightyCallContactPage_';

	// Settings options
	private static $name_contact_id = 'ContactId';
	private static $name_add_contact_page = 'AddContactPage';
	private static $name_inherit_styles = 'InheritStyles';
	
	// Internal plugin options
	private static $name_page_id = 'PageId';
	private static $name_tenant_id = 'TenantId';
	private static $name_main_widget_code = 'MainWidgetCode';
	private static $name_themed_widget_code = 'ThemedWidgetCode';
	private static $name_cc_widget_code = 'CCWidgetCode';
	private static $name_cc_mobile_widget_code = 'CCMobileWidgetCode';

	private static $instance;

	private function __construct() {
	}

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new MightyCall_Contact_Page_Options();
		}
		return self::$instance;
	}

	public function create() {
		$this->log( 'creating options ...' );
		$this->add_option( self::$name_contact_id, '' );
		$this->add_option( self::$name_add_contact_page, 'false' );
		$this->add_option( self::$name_inherit_styles, 'false' );
		$this->add_option( self::$name_page_id, '' );
		$this->add_option( self::$name_tenant_id, '' );
		$this->add_option( self::$name_main_widget_code, '' );
		$this->add_option( self::$name_themed_widget_code, '' );
		$this->add_option( self::$name_cc_widget_code, '' );
		$this->add_option( self::$name_cc_mobile_widget_code, '' );
		$this->log( 'successfully created options' );
	}

	public function destroy() {
		$this->log( 'deleting options ...' );
		$this->delete_option( self::$name_contact_id );
		$this->delete_option( self::$name_add_contact_page );
		$this->delete_option( self::$name_inherit_styles );
		$this->delete_option( self::$name_page_id );
		$this->delete_option( self::$name_tenant_id );
		$this->delete_option( self::$name_main_widget_code );
		$this->delete_option( self::$name_themed_widget_code );
		$this->delete_option( self::$name_cc_widget_code );
		$this->delete_option( self::$name_cc_mobile_widget_code );
		$this->log( 'successfully deleted options' );
	}

	//////////////////////////////////////////////////////////
	// Setters/Getters
	//////////////////////////////////////////////////////////
	public function get_contact_id() {
		return $this->get_option( self::$name_contact_id );
	}

	public function set_contact_id( $value ) {
		return $this->update_option( self::$name_contact_id, $value );
	}

	public function get_add_account_page() {
		return $this->get_option( self::$name_add_contact_page );
	}

	public function set_add_account_page( $value ) {
		return $this->update_option( self::$name_add_contact_page, $value );
	}

	public function get_inherit_styles() {
		return $this->get_option( self::$name_inherit_styles );
	}

	public function set_inherit_styles( $value ) {
		return $this->update_option( self::$name_inherit_styles, $value );
	}

	public function get_page_id() {
		return $this->get_option( self::$name_page_id );
	}

	public function set_page_id( $value ) {
		return $this->update_option( self::$name_page_id, $value );
	}

	public function get_tenant_id() {
		return $this->get_option( self::$name_tenant_id );
	}

	public function set_tenant_id( $value ) {
		return $this->update_option( self::$name_tenant_id, $value );
	}

	public function get_main_widget_code() {
		return $this->get_option( self::$name_main_widget_code );
	}

	public function set_main_widget_code( $value ) {
		return $this->update_option( self::$name_main_widget_code, $value );
	}
	
	public function get_themed_widget_code() {
		return $this->get_option( self::$name_themed_widget_code );
	}

	public function set_themed_widget_code( $value ) {
		return $this->update_option( self::$name_themed_widget_code, $value );
	}

	public function get_cc_widget_code() {
		return $this->get_option( self::$name_cc_widget_code );
	}

	public function set_cc_widget_code( $value ) {
		return $this->update_option( self::$name_cc_widget_code, $value );
	}

	public function get_cc_mobile_widget_code() {
		return $this->get_option( self::$name_cc_mobile_widget_code );
	}

	public function set_cc_mobile_widget_code( $value ) {
		return $this->update_option( self::$name_cc_mobile_widget_code, $value );
	}

	//////////////////////////////////////////////////////////
	// Internal methods
	//////////////////////////////////////////////////////////
	private function get_prefix() {
		return self::options_prefix;
	}

	private function get_prefixed_name( $name ) {
		return self::options_prefix . $name;
	}

	private function add_option( $name, $value ) {
		return add_option( $this->get_prefixed_name( $name ), $value );
	}

	private function get_option( $name ) {
		return get_option( $this->get_prefixed_name( $name ) );
	}

	private function update_option( $name, $value ) {
		return update_option( $this->get_prefixed_name( $name ), $value );
	}

	private function delete_option( $name ) {
		return delete_option( $this->get_prefixed_name( $name ) );
	}

	private function log( $message ) {
		MightyCall_Contact_Page_Logger::log( '[' . get_class( $this ) . '] ' . $message );
	}
}