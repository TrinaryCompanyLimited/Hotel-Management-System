<?php
/**
 * HTTP response data object class.
 * @package SCREENREADER::plugins
 * @author JExtensions Store 
 * @copyright (C) 2016 - JExtensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
class jscrHttpResponse {
	/**
	 *
	 * @var integer The server response code.
	 * @since 11.3
	 */
	public $code;
	
	/**
	 *
	 * @var array Response headers.
	 * @since 11.3
	 */
	public $headers = array ();
	
	/**
	 *
	 * @var string Server response body.
	 * @since 11.3
	 */
	public $body;
}
