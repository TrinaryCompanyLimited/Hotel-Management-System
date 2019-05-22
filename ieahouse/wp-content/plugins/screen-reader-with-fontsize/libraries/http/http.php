<?php
/**
 * HTTP client class
 * @package SCREENREADER::plugins
 * @author JExtensions Store
 * @copyright (C) 2016 - JExtensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
require_once 'uri.php';
require_once 'httpresponse.php';
class jscrHttp {
	/**
	 *
	 * @var JRegistry Options for the HTTP client.
	 * @since 11.3
	 */
	protected $options;
	
	/**
	 *
	 * @var JHttpTransport The HTTP transport object to use in sending HTTP requests.
	 * @since 11.3
	 */
	protected $transport;
	
	/**
	 * Constructor.
	 *
	 * @param
	 *        	JRegistry &$options Client options object.
	 * @param JHttpTransport $transport
	 *        	The HTTP transport object.
	 *        	
	 * @since 11.3
	 */
	public function __construct(jscrHttpTransport $transport = null) {
		if (is_null ( $transport ) && ! class_exists ( 'jscrHttpTransportSocket' )) {
			require_once 'transport/httptransportsocket.php';
		}
		$this->transport = isset ( $transport ) ? $transport : new jscrHttpTransportSocket ( $this->options );
	}
	
	/**
	 * Method to send the OPTIONS command to the server.
	 *
	 * @param string $url
	 *        	Path to the resource.
	 * @param array $headers
	 *        	An array of name-value pairs to include in the header of the request.
	 *        	
	 * @return JHttpResponse
	 *
	 * @since 11.3
	 */
	public function options($url, array $headers = null) {
		return $this->transport->request ( 'OPTIONS', new JUri ( $url ), null, $headers );
	}
	
	/**
	 * Method to send the HEAD command to the server.
	 *
	 * @param string $url
	 *        	Path to the resource.
	 * @param array $headers
	 *        	An array of name-value pairs to include in the header of the request.
	 *        	
	 * @return JHttpResponse
	 *
	 * @since 11.3
	 */
	public function head($url, array $headers = null) {
		return $this->transport->request ( 'HEAD', new JUri ( $url ), null, $headers );
	}
	
	/**
	 * Method to send the GET command to the server.
	 *
	 * @param string $url
	 *        	Path to the resource.
	 * @param array $headers
	 *        	An array of name-value pairs to include in the header of the request.
	 *        	
	 * @return JHttpResponse
	 *
	 * @since 11.3
	 */
	public function get($url, array $headers = null) {
		return $this->transport->request ( 'GET', new JUri ( $url ), null, $headers );
	}
	
	/**
	 * Method to send the POST command to the server.
	 *
	 * @param string $url
	 *        	Path to the resource.
	 * @param mixed $data
	 *        	Either an associative array or a string to be sent with the request.
	 * @param array $headers
	 *        	An array of name-value pairs to include in the header of the request.
	 *        	
	 * @return JHttpResponse
	 *
	 * @since 11.3
	 */
	public function post($url, $data, array $headers = null) {
		return $this->transport->request ( 'POST', new JUri ( $url ), $data, $headers );
	}
	
	/**
	 * Method to send the PUT command to the server.
	 *
	 * @param string $url
	 *        	Path to the resource.
	 * @param mixed $data
	 *        	Either an associative array or a string to be sent with the request.
	 * @param array $headers
	 *        	An array of name-value pairs to include in the header of the request.
	 *        	
	 * @return JHttpResponse
	 *
	 * @since 11.3
	 */
	public function put($url, $data, array $headers = null) {
		return $this->transport->request ( 'PUT', new JUri ( $url ), $data, $headers );
	}
	
	/**
	 * Method to send the DELETE command to the server.
	 *
	 * @param string $url
	 *        	Path to the resource.
	 * @param array $headers
	 *        	An array of name-value pairs to include in the header of the request.
	 *        	
	 * @return JHttpResponse
	 *
	 * @since 11.3
	 */
	public function delete($url, array $headers = null) {
		return $this->transport->request ( 'DELETE', new JUri ( $url ), null, $headers );
	}
	
	/**
	 * Method to send the TRACE command to the server.
	 *
	 * @param string $url
	 *        	Path to the resource.
	 * @param array $headers
	 *        	An array of name-value pairs to include in the header of the request.
	 *        	
	 * @return JHttpResponse
	 *
	 * @since 11.3
	 */
	public function trace($url, array $headers = null) {
		return $this->transport->request ( 'TRACE', new JUri ( $url ), null, $headers );
	}
}
