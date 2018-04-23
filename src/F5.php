<?php

namespace Diskerror\Service;

use Diskerror\Utilities;
use Xml2Array;

/**
 * Accessor for an F5 BigIP iControl.
 *
 * Possible INI file contents passed to $opts and $creds
 *   f5.url = https://service.domain/iControl/iControlPortal.cgi
 *   f5.compression = SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_DEFLATE
 *   f5.trace = 1
 *   f5.cache_wsdl = WSDL_CACHE_BOTH
 *   f5.user_agent = "user_agent"
 *
 *   cred.username = username
 *   cred.password = password
 */
class F5 implements ServiceInterface
{
	/**
	 * URL set by the child class.
	 * @type array
	 */
	protected $_url;

	/**
	 * Array (Diskerror\Utilities\Registry) of clients for reuse.
	 * @type Diskerror\Utilities\Registry
	 */
	protected $_client;

	/**
	 * Session ID from F5 wrapped prepped in \SoapHeader.
	 * @type \SoapHeader
	 */
	protected $_sessionId = null;

	/**
	 * Array of options for reuse.
	 * @type array
	 */
	protected $_soapOpts;

	/**
	 * Array of CURL options for reuse.
	 * @type array
	 */
	protected $_curlOpts;

	/**
	 * Constructor.
	 *
	 * @param array $opts
	 * @param array $creds
	 */
	public function __construct(array $opts, array $creds)
	{
		if (in_array('location', $opts)) {
			unset($opts['location']);
		}
		$this->_soapOpts = $opts;
		unset($this->_soapOpts['url']);    //	everything but url in _soapOpts
		$this->_url = $opts['url'];
		$this->_soapOpts['login'] = $creds['username'];
		$this->_soapOpts['password'] = $creds['password'];
		$this->_soapOpts['stream_context'] = stream_context_create([
			'ssl' => [
				'verify_peer'       => false,
				'verify_peer_name'  => false,
				'allow_self_signed' => true,
			],
		]);

		$this->_curlOpts = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => false,
			CURLOPT_USERPWD        => $creds['username'] . ':' . $creds['password'],
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => false,
		];

		$this->_client = new Utilities\Registry();

		$this->_client['System.Session'] = new \SoapClient(
			$this->_url . '?WSDL=System.Session',
			$this->_soapOpts
		);

		$this->_sessionId = new \SoapHeader(
			'urn:iControl:System/Session',
			'session',
			$this->_client['System.Session']->__call('get_session_identifier', [])
		);

		$this->_client['System.Session']->__setSoapHeaders([$this->_sessionId]);
	}

	/**
	 * Destructor.
	 */
	public function __destruct()
	{
		unset($this->_client);
	}

	/**
	 * Make a call to F5 instance.
	 *
	 * Use an underscore (_) to delineate namespace.
	 *
	 * @param string $method
	 * @param array  $args
	 *
	 * @return mixed
	 * @throws LogicException
	 */
	public function __call($method, array $args = [])
	{
		$m = explode('_', $method, 3);
		$namespace = $m[0] . '.' . $m[1];
		$function = $m[2];

		//	build unique client for this namespace
		if (!isset($this->_client[$namespace])) {
			$this->_client[$namespace] = new \SoapClient(
				$this->_url . '?WSDL=' . $namespace,
				$this->_soapOpts
			);

			//	set session ID if not set
// 			if ( $this->_sessionId === null ) {
// 			    $doSysHeader = false;
// 				//	if no session ID the check to be sure System.Session is
// 				if ( !isset($this->_client['System.Session']) ) {
// 				    $this->_client['System.Session'] = new \SoapClient(
// 						$this->_url . '?WSDL=System.Session',
// 						$this->_soapOpts
// 					);
//
// 				    $doSysHeader = true;
// 				}
//
// 			    $this->_sessionId = new \SoapHeader(
// 					'urn:iControl:System/Session',
// 					'session',
// 					$this->_client['System.Session']->__call('get_session_identifier', [])
// 				);
//
// 			    if ( $doSysHeader ) {
// 			        $this->_client['System.Session']->__setSoapHeaders( [$this->_sessionId] );
// 			    }
// 			}

			$this->_client[$namespace]->__setSoapHeaders([$this->_sessionId]);
		}

		//	check to see if this is a local SOAP function
		if (substr($function, 0, 2) === '__') {
			if (count($args) === 0) {
				return $this->_client[$namespace]->$function();
			}

			return $this->_client[$namespace]->$function($args);
		}

		return call_user_func_array([$this->_client[$namespace], $function], $args);
	}

	/**
	 * Get all iControl SOAP namespaces.
	 *
	 * @return array
	 */
	public function getNamespaces()
	{
		$curl = new Curl($this->_url, $this->_curlOpts);

		$ul = [];
		preg_match('#<ul>(.*)<ul>#si', $curl->exec(), $ul);
		unset($curl);

		$dom = new \DOMDocument();
		$dom->loadHTML($ul[1]);

		return explode("\n", $dom->textContent);
	}

	/**
	 * Get WSDL for namespaces.
	 *
	 * @param string $ns
	 */
	public function getWsdl($ns)
	{
		$curl = new Curl(
			$this->_url . '?WSDL=' . $ns,
			$this->_curlOpts
		);

		return Xml2Array::process($curl->exec());
	}
}
