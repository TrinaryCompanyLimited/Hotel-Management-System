<?php

class MightyCall_Contact_Page_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'mightycall_contact_page_widget', // Base ID
			'MightyCall Contact Form Widget', // Name
			array( 'description' => 'MightyCall Contact Form Widget' ) // Args
		);

		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
	}

	public function widget( $args, $instance ) {
		ob_clean();
		$options = MightyCall_Contact_Page_Options::get_instance();
		$tenant_id = $options->get_tenant_id();

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

		if ( !isset( $tenant_id ) || '' == trim( $tenant_id ) ) {
			echo '<span>Can\'t render MightyCall Contact Form Widget. Please check MightyCall Contact plugin settings</span>';
		} else {
			$nonce = wp_create_nonce( 'mcnonce' );
			$ajax_url = admin_url( 'admin-ajax.php?action=mightycall_contact_page&nonce=' . $nonce );
			echo '<div id="InfratelContactWidget" data-tenantid="' . $tenant_id . '" data-url="' . $ajax_url . '"></div>';
			wp_enqueue_script( 'mightycall-tenantid.js', MIGHTYCALL_CONTACT_PAGE_CDN_SERVER_BASE . '/cqjs/' . $tenant_id . '.js', array('jquery') );
			wp_enqueue_script( 'mightycall-wordpress.js', MIGHTYCALL_CONTACT_PAGE_CDN_SERVER_BASE . '/cqjs/wordpress.js', array('jquery') );
		}
		echo $args['after_widget'];
		return;
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = 'New Title';
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}

	function scripts() {
		if ( is_active_widget( false, false, $this->id_base, true ) ) {
			//still this will put scripts on every site page if the widget is activated on some sidebar
			wp_enqueue_script( 'jquery' );
		}
	}
}

function mightycall_contact_page_register_widget() {
	register_widget( 'MightyCall_Contact_Page_Widget' );
}