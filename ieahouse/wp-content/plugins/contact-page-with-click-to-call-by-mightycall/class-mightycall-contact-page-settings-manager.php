<?php
class MightyCall_Contact_Page_Settings_Manager {

	const settings_group = 'mightycall-settings-group';

	private static $instance;

	private function __construct() {
	}

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new MightyCall_Contact_Page_Settings_Manager();
		}
		return self::$instance;
	}

	public function add_settings_to_admin_menu() {
		add_menu_page( 'MightyCall Contact Page', 'MightyCall', 'manage_options',
		get_class( $this ), array( &$this, 'settings_page' ), plugins_url( 'images/menu_icon.png' , __FILE__ ) );
	}

	public function settings_page() {
		if ( isset( $_POST['action'] ) && 'update' == $_POST['action']  &&
			 isset( $_POST['option_page'] ) && self::settings_group == $_POST['option_page'] ) {
			$this->update_settings();
		}

		$this->page_content();
	}
	
	private function update_settings() {
		$options = MightyCall_Contact_Page_Options::get_instance();

		$options->set_contact_id( $_POST['ContactId'] );
		$options->set_add_account_page( isset( $_POST['AddContactPage'] ) ? 'true' : 'false' );
		$options->set_inherit_styles( isset( $_POST['InheritStyles'] ) ? 'true' : 'false' );

		$page_id = $options->get_page_id();

		$page_manager = MightyCall_Contact_Page_Page_Manager::get_instance();

		if ( 'true' == $options->get_add_account_page() ) {
			if ( empty( $page_id ) ) {
				$page_id = $page_manager->add();
			}
		} else {
			if ( !empty( $page_id ) ) {
				$page_manager->remove( $page_id );
				$page_id = '';
			}
		}
		
		$options->set_page_id( $page_id );

		$contact_id = $options->get_contact_id();
		$tenant_id = '';
		$main_widget_code = '';
		$themed_widget_code = '';
		$cc_widget_code = '';
		$cc_mobile_widget_code = '';

		if ( isset( $contact_id ) && trim( $contact_id ) != '') {
			$tenant_id = $this->retrieve_tenant_id( $contact_id );
		}
		
		if ( isset( $tenant_id ) && trim( $tenant_id ) != '' ) {
			$codes = $this->retrieve_widget_code( $tenant_id );
			
			if ( isset( $codes ) ) {
				$main_widget_code = $codes->{'widget'};
				$themed_widget_code = $codes->{'themedWidget'};
				$cc_widget_code = $codes->{'sidebarClickConnect'};
				$cc_mobile_widget_code = $codes->{'sidebarMobileClickConnect'};
			}
		}
		
		$options->set_tenant_id( $tenant_id );
		$options->set_main_widget_code( $main_widget_code );
		$options->set_themed_widget_code( $themed_widget_code );
		$options->set_cc_widget_code( $cc_widget_code );
		$options->set_cc_mobile_widget_code( $cc_mobile_widget_code );
	}

	private function retrieve_tenant_id( $contact_id ) {
		$resp = wp_remote_get( MIGHTYCALL_CONTACT_PAGE_SERVER_BASE . '/ContactAPI/API/GetTenantId?wordPressId=' . $contact_id );
		if ( is_wp_error( $resp ) || 200 != wp_remote_retrieve_response_code( $resp ) ) {
			return '';
		} else {
			$result = json_decode( wp_remote_retrieve_body( $resp ) );
			//if ( json_last_error() === JSON_ERROR_NONE ) {
			if ( is_object($result) ) {
				$tenant_id = $result->{'tenantId'};
				return $tenant_id;
			}
			return '';
		}
	}

	private function retrieve_widget_code( $tenant_id ) {
		$resp = wp_remote_get( MIGHTYCALL_CONTACT_PAGE_SERVER_BASE . '/ContactAPI/api/GetWordPressWidgets?tenantId=' . $tenant_id );
		if ( is_wp_error( $resp ) || 200 != wp_remote_retrieve_response_code( $resp ) ) {
			return null;
		} else {
			$result = json_decode( wp_remote_retrieve_body( $resp ) );			
			//if ( json_last_error() === JSON_ERROR_NONE ) {
			if ( is_object($result) ) {			
				return $result;
			}
			return null;
		}
	}

	public function page_content() {
		$options = MightyCall_Contact_Page_Options::get_instance();
		$tenant_id = $options->get_tenant_id();
		$nonce = wp_create_nonce( 'mcnonce2' );
		$ajax_url = admin_url( 'admin-ajax.php?action=mightycall_contact_page_check&nonce=' . $nonce );
		$widgets_menu = admin_url('widgets.php');
		?>
			<style type="text/css">
				.mightycall-contact-page-logo {
					display: inline-block;
					width: 201px;
					height: 49px;					
					background-image: url(<?php echo plugins_url( 'images/mightycall_contact_page_logo.png' , __FILE__ ); ?>);
					background-size: 100%;
					background-repeat:no-repeat;
					vertical-align: bottom;
				}
				
				.mightycall-contact-page-headline {
					display: inline-block !important;
					margin-bottom: 13px !important;
					margin-left: 10px !important;
					font-size: 22px !important;
				}
				
				.mightycall-contact-page-id-status-icon {
					display: inline-block;
					width: 16px;
					height: 16px;
					margin: 2px;
					background-image: url(<?php echo plugins_url( 'images/error_icon.png' , __FILE__ ); ?>);
					vertical-align: top;					
				}
				.upd{
					background-image: url(<?php echo plugins_url( 'images/apply_icon.png' , __FILE__ ); ?>);
				}
				
				.mightycall-contact-page-contact-page-icon {
					display: inline-block;
					width: 41px;
					height: 41px;
					margin: 2px;
					background-image: url(<?php echo plugins_url( 'images/contact_page_icon.png' , __FILE__ ); ?>);
					background-size: 100%;
					background-repeat:no-repeat;
					vertical-align: middle;
				}
				
				.mightycall-contact-page-sidebar-widget-icon {
					display: inline-block;
					width: 41px;
					height: 41px;
					margin: 2px;					
					margin-left: 16px;
					background-image: url(<?php echo plugins_url( 'images/side_bar_icon.png' , __FILE__ ); ?>);
					background-size: 100%;
					background-repeat:no-repeat;
					vertical-align: middle;
				}
				
				.mightycall-contact-page-support-icon {
					display: inline-block;
					width: 42px;
					height: 42px;
					margin: 2px;
					background-image: url(<?php echo plugins_url( 'images/support_icon.png' , __FILE__ ); ?>);
					background-size: 100%;
					background-repeat:no-repeat;
					vertical-align: middle;
				}
				
				table.mightycall-contact-page-settings td {
					vertical-align: top;
				}
				
				.mightycall-contact-page-shortcode-popup {
					position:fixed;
					_position:absolute;
					height:120px;
					width:20%;
					background:#FFFFFF;
					left: 300px;
					top: 150px;
					z-index:100;
					margin-left: 15px;
					border:2px solid #000000;
					padding:15px;
					font-size:15px;
					-moz-box-shadow: 0 0 5px #000000;
					-webkit-box-shadow: 0 0 5px #000000;
				}
				
				.mightycall-contact-page-signup-link {
					font-family:Arial;
					font-size:16px;
					font-weight:bold;
					font-style:normal;
					text-decoration:underline;
					color:#009900;
				}
				
				.mightycall-contact-page-vert-line {
					position: absolute;
					left: 4px;
					top: 0px;
					width: 3px;
					height: 136px;					
					background-image: url(<?php echo plugins_url( 'images/vert_line.png' , __FILE__ ); ?>);
					background-repeat: no-repeat;
				}
				
				.mightycall-contact-page-info-tooltip {				
					display: inline-block;
					width: 16px;
					height: 16px;
					margin: 2px;
					background-image: url(<?php echo plugins_url( 'images/info_icon.png' , __FILE__ ); ?>);
					background-size: 100%;
					background-repeat:no-repeat;
					vertical-align: middle;
				}
				
				
				.mightycall-contact-page-settings {
					margin-right: auto;
				}
				
				.mightycall-contact-page-error {
					height: 1em;
					margin-top: 0;
					margin-bottom:  0;
					color: red;
				}
			</style>
			<div class="wrap">
				<div class="mightycall-contact-page-logo"></div>&nbsp;
				<h2 class="mightycall-contact-page-headline">Contact Page with Click to Call by MightyCall</h2>
				<form method="post" action="">
					<?php settings_fields( self::settings_group ); ?>													
						<table class="mightycall-contact-page-settings">
							<colgroup>
								<col width="25%">
								<col width="25%">
								<col width="25%">
								<col width="25%">
							</colgroup>
							<tbody>
							<tr>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
								<tr>
									<td colspan="4">
										<div>
											<h3>Specify your MightyCall Account</h3>
											<hr>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="3">
										<div>
											<label for="ContactId">MightyCall ID</label>
											<input id="ContactId" type="text" name="ContactId" style="width:25em" value="<?php echo $options->get_contact_id(); ?>"/>&nbsp;
											<i class="mightycall-contact-page-id-status-icon"></i>
										</div>
										
									</td>
									<td style="text-align:center;">
										<div>
											<a id="MightyCallContactIdLink" class="mightycall-contact-page-signup-link" href="<?php echo MIGHTYCALL_CONTACT_PAGE_REGISTER_URL; ?>" target="_blank">Get one for free</a>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="3">
										<div>
											<p id="MightyCallContactIdError" class="mightycall-contact-page-error" ></p>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="4">
										<div>
											<h3 style=" margin-top: 0.5em; ">How do I get a MightyCall ID?</h3>
											<hr>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="4">
										<div>
											<ol>
												<li>Sign-up for MightyCall account at <a href="<?php echo MIGHTYCALL_CONTACT_PAGE_REGISTER_URL; ?>" target="_blank"><?php echo MIGHTYCALL_CONTACT_PAGE_REGISTER_URL; ?></a>. Registration is free.</li>
												<li>Complete basic configuration of the MightyCall Contact form.</li>
												<li>Request an integration code, MightyCall ID will be available under Wordpress tab.</li>
											</ol>
											<br>
											<div class="mightycall-contact-page-support-icon"></div>&nbsp;
											<div style="margin-left:1em;display: inline-block;">
											<span>
											If you have any questions, please visit <a href="http://support.mightycall.com">MightyCall Support Website</a> or contact the <a href="mailto:cloudsupport@infratel.com">MightyCall team</a>.
											</span>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="4">
										<div>
											<h3>How would you like to add MightyCall to your website?</h3>
											<hr>
										</div>
									</td>
								</tr>								
								<tr>									
									<td colspan="2" style="border-right-width: 1px;border-right-color: gray;border-right-style: solid;">
										<div>
											<div class="mightycall-contact-page-contact-page-icon"></div>											
											<span style="font-weight:bold;margin-left:18px;">As a separate contact form:</span>
										</div>
										<div style="margin-left: 64px;">
											<input id="AddContactPage" type="checkbox" value="" name="AddContactPage" <?php echo ('false' == $options->get_add_account_page()) ? '' : 'checked'; ?>/>
											<label for="AddContactPage">Add as a new contact page</label><br> Or <a id="mightycall-contact-page-grab-shortcode">grab a short code for any other page</a><br>											
										</div>
										<div style="margin-left: 64px;margin-top:16px">
											<input id="InheritStyles" type="checkbox" value="" name="InheritStyles" <?php echo ('false' == $options->get_inherit_styles()) ? '' : 'checked'; ?>/>
											<label for="InheritStyles">Apply website theme styles to widget</label>
											<div class="mightycall-contact-page-info-tooltip" title="By default MightyCall Contact Page plugin uses its own stylesheet when showing a contact form on your website. You can apply your website theme styles to the form so it looks like a seamless part of your web page. Please note, that this feature may not work correctly for some of the Wordpress themes. If it is not looking good in your theme after you have switched it on - just revert to the original stylesheet"></div>
										</div>										
									</td>
									<td colspan="2">
										<div>
											<div class="mightycall-contact-page-sidebar-widget-icon"></div>											
											<span style="font-weight:bold;margin-left:18px;">As sidebar widgets:</span>
										</div>
										<div style="margin-left: 80px;">
											<span>Visit Wordpress <a href="<?php echo $widgets_menu; ?>">widgets menu</a></span>
										</div>
									</td>																
								</tr>

								<tr>
									<td colspan="4" style="text-align:center;">
										<div>
											<?php submit_button(); ?>
										</div>
									</td>
								</tr>								
							</tbody>
						</table>
				</form>
			</div>
			<div class="mightycall-contact-page-shortcode-popup" style="display:none">
				<p style="text-align:left;padding-left:10px;">To embed the contact form to individual web pages copy and paste the following short code</p>
				<p style="text-align:left;padding-left:10px;">[<?php echo MIGHTYCALL_CONTACT_PAGE_SHORTCODE; ?>]</p>
				<p style="text-align:left;padding-left:10px;"><input type="button" id="mightycall-contact-page-close-popup" class="button button-primary" value="Close" /></p>
			</div>
			<script type="text/javascript">
				function mightyCallContactShowError(errorMessage) {
					jQuery('#MightyCallContactIdLink').show();
					jQuery('.mightycall-contact-page-id-status-icon').removeClass('upd');
					
					if (typeof errorMessage !== 'undefined')
					{
						jQuery('#MightyCallContactIdError').text(errorMessage);
					}
					else
					{
						jQuery('#MightyCallContactIdError').text("");
					}
				}
				
				function mightyCallContactShowSuccess()	{
					jQuery('#MightyCallContactIdLink').hide();
					jQuery('.mightycall-contact-page-id-status-icon').addClass('upd');
					jQuery('#MightyCallContactIdError').text("");					
				}
				
				function checkMightyCallId()
				{				
					var id = jQuery('#ContactId').val();
					var tid = '<?php echo $tenant_id; ?>';
					if (typeof id === 'undefined' || !id)
					{
						mightyCallContactShowError();
					}
					else
					{
						if ('' == tid)	{
							mightyCallContactShowError("<?php echo MightyCall_Contact_Page_Plugin::error_wrong_id; ?>");
						}
						jQuery.ajax({
							type: 'POST',
							url: '<?php echo $ajax_url; ?>',
							data: 'wordpressId=' + id,
							dataType: 'json',
							timeout: 10000,
							success: function (data) {
								if (typeof data.result === 'undefined' || data.result != 200) {																			
									mightyCallContactShowError(data.message);
								} else {
									mightyCallContactShowSuccess();
								}
							},
							error: function (request, status, error) {
								mightyCallContactShowError("<?php echo MightyCall_Contact_Page_Plugin::error_conn_failure; ?>");
							}
						});						
					}
				}

				jQuery(document).ready(function () {
					jQuery('#mightycall-contact-page-grab-shortcode').click(function() {
						jQuery('.mightycall-contact-page-shortcode-popup').show();
					});
				
					jQuery('#mightycall-contact-page-close-popup').click(function() {
						jQuery('.mightycall-contact-page-shortcode-popup').hide();
					});
					
					var delay = (function(){
					  var timer = 0;
					  return function(callback, ms){
						clearTimeout (timer);
						timer = setTimeout(callback, ms);
					  };
					})();

					jQuery('#ContactId').keyup(function() {
											 delay(function(){
												checkMightyCallId();
											}, 500 );
										}).change(checkMightyCallId);
					checkMightyCallId();
				});
			</script>
		<?php
	}

	private function log( $message ) {
		MightyCall_Contact_Page_Logger::log( '[' . get_class( $this ) . '] ' . $message );
	}
}