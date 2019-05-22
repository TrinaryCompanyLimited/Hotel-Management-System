<?php

class MightyCall_Contact_Page_Plugin {

	private static $instance;
	
	const error_conn_failure = "We can't connect to the MightyCall to verify your ID. Please, contact support.";
	const error_wrong_id = "The ID you have provided is incorrect. Please, check your MightyCall account.";


	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new MightyCall_Contact_Page_Plugin();
		}

		return self::$instance;
	}

	public function activate() {
		$this->log( 'Activating...' );

		$options = MightyCall_Contact_Page_Options::get_instance();
		$options->create();
		
		$this->log( 'Successfully activated ' );
	}
	
	public function deactivate() {
		$this->log( 'Deactivating...' );

		$options = MightyCall_Contact_Page_Options::get_instance();
		$page_manager = MightyCall_Contact_Page_Page_Manager::get_instance();

		// Remove Contact page
		$page_id = $options->get_page_id();
		if ( !empty( $page_id ) ) {
			$page_manager->remove( $page_id );
		}

		// Remove options
		$options->destroy();

		$this->log( 'Successfully deactivated' );
	}

	public function add_actions() {
		$this->log('Begin add_actions');
		
		$settings_manager = MightyCall_Contact_Page_Settings_Manager::get_instance();
		add_action( 'admin_menu', array( &$settings_manager, 'add_settings_to_admin_menu' ) );

		$page_manager = MightyCall_Contact_Page_Page_Manager::get_instance();
		add_shortcode( MIGHTYCALL_CONTACT_PAGE_SHORTCODE, array( &$page_manager, 'short_code' ) );
		
		add_action( 'widgets_init', 'mightycall_contact_page_register_widget' );
		add_action( 'widgets_init', 'mightycall_contact_page_register_c2c_widget' );

        add_action( 'wp_ajax_mightycall_contact_page', array( $this, 'submit_form_callback' ) );
        add_action( 'wp_ajax_nopriv_mightycall_contact_page', array( $this, 'submit_form_callback' ) );
		add_action( 'wp_ajax_mightycall_contact_page_check', array( $this, 'check_id_callback' ) );
        add_action( 'wp_ajax_nopriv_mightycall_contact_page_check', array( $this, 'check_id_callback' ) );
		
		$this->log('End add_actions');
	}

    //this will perform post to mightycall to post the form data
    public function submit_form_callback() {
		ob_clean();
		header( 'Content-Type: application/json' );
		$nonce = $_GET['nonce'];
        if ( !wp_verify_nonce( $nonce, 'mcnonce' ) ) {
			echo json_encode( array(
				'result' => 400,
				'message' => 'nonce verification failure'
				));
		} else {
			$basic_url = MIGHTYCALL_CONTACT_PAGE_SERVER_BASE;
            $resp = wp_remote_post( $basic_url . '/ContactAPI/API/Contact', array( 'body' => $_POST ) );
			if ( is_wp_error( $resp ) || 200 != wp_remote_retrieve_response_code( $resp ) ) {
				echo json_encode( array(
					'result' => 400,
					'message' => 'mightycall connection failure',
					'error' => $resp->get_error_message()
					));
			} else {
				echo wp_remote_retrieve_body( $resp );
			}
		}

		exit;
    }

	public function check_id_callback() {
		ob_clean();
		header( 'Content-Type: application/json' );
		$nonce = $_GET['nonce'];
        if ( !wp_verify_nonce( $nonce, 'mcnonce2' ) ) {
			echo json_encode( array(
				'result' => 400,
				'message' => 'nonce verification failure'
				));
		} else {
			$basic_url = MIGHTYCALL_CONTACT_PAGE_SERVER_BASE;
            $resp = wp_remote_post( $basic_url . '/ContactAPI/api/GetTenantId', array( 'body' => $_POST ) );
			if ( is_wp_error( $resp ) || 200 != wp_remote_retrieve_response_code( $resp ) ) {
				echo json_encode( array(
					'result' => 400,
					'message' => MightyCall_Contact_Page_Plugin::error_conn_failure,
					'error' => $resp->get_error_message()
					));
			} else {
				$options = MightyCall_Contact_Page_Options::get_instance();
				$body = wp_remote_retrieve_body( $resp );
				$result = json_decode( $body );
				$tenant_id = '';
				
				//if ( json_last_error() === JSON_ERROR_NONE ) {   			
				if ( is_object($result) ) {
					$tenant_id = $result->{'tenantId'};
				}

				if ( isset( $tenant_id ) && trim( $tenant_id ) != '' ) {
					echo $body;
				} else {
					echo json_encode( array(
					'result' => 400,
					'message' => MightyCall_Contact_Page_Plugin::error_wrong_id
					));
				}
			}
		}

		exit;
    }

	private function log( $message ) {
		MightyCall_Contact_Page_Logger::log( '[' . get_class( $this ) . '] ' . $message );
	}
}
?>