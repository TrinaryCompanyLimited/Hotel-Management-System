<?php
/*
Plugin Name: Contact Page with Click to Call by MightyCall
Plugin URI: http://www.mightycall.com
Description: Configurable all-in-one contact page including company address, clickable phone numbers with click to call, business hours, location map & directions, vCard, social media links and a "contact us" form including call me back and email response options. 
Version: 1.9
Author: MightyCall
Author URI: http://www.mightycall.com
*/

define( 'MIGHTYCALL_CONTACT_PAGE_UNIQUE_ID', 'contact-page-with-click-to-call-by-mightycall' );
define( 'MIGHTYCALL_CONTACT_PAGE_DIR', WP_PLUGIN_DIR . '/' . MIGHTYCALL_CONTACT_PAGE_UNIQUE_ID );
define( 'MIGHTYCALL_CONTACT_PAGE_SHORTCODE', 'MightyCallContactForm' );
define( 'MIGHTYCALL_CONTACT_PAGE_SERVER_BASE', 'http://panel.mightycall.com' );
define( 'MIGHTYCALL_CONTACT_PAGE_CDN_SERVER_BASE', 'https://mightycallstorage.blob.core.windows.net' );
define( 'MIGHTYCALL_CONTACT_PAGE_REGISTER_URL', 'https://secure.mightycall.com' );


require_once( 'class-mightycall-contact-page.php' );
require_once( 'class-mightycall-contact-page-logger.php' );
require_once( 'class-mightycall-contact-page-options.php' );
require_once( 'class-mightycall-contact-page-page-manager.php' );
require_once( 'class-mightycall-contact-page-settings-manager.php' );
require_once( 'class-mightycall-contact-page-widget.php' );
require_once( 'class-mightycall-contact-page-click-connect-widget.php' );
require_once( 'class-mightycall-contact-page-plugin.php' );

MightyCall_Contact_Page::init(__FILE__);