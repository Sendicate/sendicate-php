<?php
/**
 * Sendicate Wrapper
 *
 * @category   Emeich
 * @package    Emeich_Sendicate
 * @copyright  Copyright (c) 2013 Emeich (http://emeich.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  OpenSoftware License (OSL 3.0)
 * @version    0.1.0
 */

class Sendicate_Wrapper
{

	protected $token;				// Sendicate Customer API Token
	protected $api_base_url;		// Base API url
	protected $api_version = 'v1';	// API version
	protected $call_url = '';		// Final url to call
	protected $_request;			// Params to send to the API
	protected $_response;			// API response
	protected $_http_code;

	protected $debug = false;

	
	public function __construct($_token='')
	{
		$this->api_base_url = 'https://api.sendicate.net/' . $this->api_version . '/';
		$this->token = $_token;
	}

	/**
     * Logs every url requested with the http status code.
     * This works if the Log main option is enabled.
     *
     * @return void
     */
	private function _logCall()
	{
		if($this->debug) {
			error_log('URL: ' . $this->getUrl() . ' - STATUS: ' . $this->getHttpCodeText($this->getHttpCode()));   
		}
	}

	
	/**
     * Log API data
     * @return void
     */
	public function _log($message)
	{
		if($this->debug) {
			error_log($message);
		}
	}


	/**
	 * ********************************* *
	 */
	function callServer($method, $url, $params)
	{

		$this->setMethod($method);
		$this->setRequest($params);
		$this->setUrl($url);
		
		$curl = curl_init();
		try {
		switch (strtoupper($this->getMethod())) {
				case 'GET':
					$this->executeGet($curl);
					break;
				case 'POST':
					$this->executePost($curl);
					break;
				case 'PUT':
					$this->executePut($curl);
					break;
				case 'DELETE':
					$this->executeDelete($curl);
					break;
				default:
					throw new InvalidArgumentException('Current method (' . $this->getMethod() . ') is an invalid REST verb.');
			}
		} catch (InvalidArgumentException $e) {
			curl_close($curl);
			throw $e;
		} catch (Exception $e) {
			curl_close($curl);
			throw $e;
		}
		@curl_close($curl);
		$this->_logCall();
	}


	protected function setCurlOptions($curl)
	{
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLINFO_HTTP_CODE, true);
		curl_setopt($curl, CURLOPT_URL, $this->getUrl());
	}


	/**
     * Execute the API call through CURL.
     *
     * @param object $curl CURL instance.
     * @return void
     */
	public function doExecute($curl)
	{
		$response = null;
		$this->setCurlOptions($curl);
		$this->setResponse(curl_exec($curl));
		$this->setHttpCode(curl_getinfo($curl, CURLINFO_HTTP_CODE));
		curl_close($curl);
	}


	/**
     * Options for GET method.
     *
     * @param object $curl CURL instance.
     * @return void
     */
	protected function executeGet($curl)
	{
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
		$this->doExecute($curl);
	}


	/**
     * Options for POST method.
     *
     * @param object $curl CURL instance.
     * @return void
     */
	protected function executePost($curl)
	{
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_POSTFIELDS, $this->getRequest());
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
		$this->doExecute($curl);
	}


	/**
     * Options for PUT method.
     *
     * @param object $curl CURL instance.
     * @return void
     */
	protected function executePut($curl)
	{
		$request_length = strlen($this->getRequest());
		$handler = fopen('php://memory', 'rw');
		fwrite($handler, $this->getRequest());
		rewind($handler);
		curl_setopt($curl, CURLOPT_INFILE, $handler);
		curl_setopt($curl, CURLOPT_INFILESIZE, $request_length);
		curl_setopt($curl, CURLOPT_PUT, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
		$this->doExecute($curl);
		fclose($handler);
	}



	/**
     * Options for DELETE method.
     *
     * @param object $curl CURL instance.
     * @return void
     */
	protected function executeDelete($curl)
	{
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
		$this->doExecute($curl);
	}


	/**
	 * Set wrapper debug mode on/off
	 * @param boolean $boolean
	 * @return void
	 */
	public function setDebug($boolean)
	{
		if(is_bool($boolean)) {
			$this->debug = $boolean;
		}
	}


	/**
     *
     * @return string API token.
     */
	public function getApiToken()
	{
		return $this->token;
	}


	/**
     *
     * @return string API call method.
     */
	public function getMethod()
	{
		return $this->_method;
	}
	
	/**
     *
     * @param string $url API call method.
     * @return void
     */
	public function setMethod($method)
	{
		$this->_method = $method;
	}

	/**
     *
     * @return string API url.
     */
	public function getUrl()
	{
		return $this->call_url;
	}

	/**
     * Defines the API url
     *
     * @param string $url API url.
     * @return void
     */
	public function setUrl($url) {
		$url = $this->getApiBaseUrl() . $url;
		
		if ( strpos($url, "?") ) {
			$url .= '&token=' . $this->getApiToken();
		} else {
			$url .= '?token=' . $this->getApiToken();
		}

		$this->call_url = $url;
	}

	/**
	 *
	 * @return <type>
	 */
	public function getApiBaseUrl()
	{
		return $this->api_base_url;
	}

	/**
     * API request values
     *
     * @return string API request values.
     */
	public function getRequest()
	{
		return $this->_request;
	}

	/**
     *
     * @param string $request API request values.
     * @return void
     */
	public function setRequest($request)
	{
		$request = json_encode($request);
		$this->_log( '- - - -' );
		$this->_log('[setRequest] ' . $request);
		
		$this->_request = $request;
	}

	/**
	 * 
     * @return array JSON decoded object using json_decode().
     */
	public function getResponse()
	{
		$this->_log('[getResponse] ' . $this->_response);
		$response = json_decode( $this->_response );

		/** @todo Improve validation */
		if ( !$response ) {
			return false;
		}

		return $response;
	}

	/**
     *
     * @param string $response API response.
     * @return void
     */
	protected function setResponse($response)
	{
		$this->_log('[setResponse] ' . $response);

		$this->_response = $response;
	}

	/**
     *
     * @return string Http code response
     */
	public function getHttpCode()
	{
		return $this->_http_code;
	}


	/**
     *
     * @param string $http_code Http code response from the CURL call.
     * @return void
     */
	protected function setHttpCode($http_code)
	{
		$this->_http_code = $http_code;
	}
	

	/**
     *
     * @param string $http_code Http code.
     * @return string Http code and description.
     */
	public function getHttpCodeText($http_code)
	{
		$codes = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',  // 1.1
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			507 => 'Insufficient storage',
			509 => 'Bandwidth Limit Exceeded'
		);

		return (isset($codes[$http_code])) ? $http_code . ' ' . $codes[$http_code] : $http_code . ' Unknow';
	}

}

