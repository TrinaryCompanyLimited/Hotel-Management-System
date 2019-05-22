<?php

class MightyCall_Contact_Page_Page_Manager {

	private static $instance;

	public static function get_instance() {
		if ( null == self::$instance) {
			self::$instance = new MightyCall_Contact_Page_Page_Manager();
		}
		return self::$instance;
	}

	public function add() {
		return wp_insert_post(
			array(
				'post_name' => 'Contact',
				'post_title' => 'Contact',
				'post_type' => 'page',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'post_content' => '[' . MIGHTYCALL_CONTACT_PAGE_SHORTCODE . ']'
			)
		);
	}

	public function remove( $page_id ) {
		if (!empty( $page_id )) {
			wp_trash_post( $page_id );
		}
	}

	public function short_code( $atts ) {
		$options = MightyCall_Contact_Page_Options::get_instance();
		if ( 'false' == $options->get_inherit_styles())	{
			$snippet = $options->get_main_widget_code();
		} else {
			$snippet = $options->get_themed_widget_code();
		}		

		if ( !isset( $snippet ) || '' == trim( $snippet ) ) {
			return '<span>Can\'t render MightyCall Contact Form Widget. Please check MightyCall Contact plugin settings</span>';
		}

        return $snippet;
	}
}