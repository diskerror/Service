<?php

namespace Diskerror\Service;

use \Zend_XmlRpc_Client;

/**
 * Accessor for Spacewalk.
 *
 * Possible INI file contents passed to $opts and $creds
 *   spacewalk.url = https://service.domain/rpc/api
 *   spacewalk.duration = 600
 *
 *   cred.username = username
 *   cred.password = password
 */
class Spacewalk implements ServiceInterface
{
	/**
	 * Holds the Zend_XmlRpc_Client instance.
	 * @type Zend_XmlRpc_Client
	 */
	private $_rpc;

	/**
	 * Holds the session key returned by logging into Spacewalk.
	 * @type string
	 */
	private $_sKey;

	/**
	 * Constructor.
	 *
	 * @param array $opts
	 * @param array $creds
	 */
	public function __construct(array $opts, array $creds)
	{
		//	assuming "duration" is in seconds
		$this->_rpc = new Zend_XmlRpc_Client($opts['url']);
		$this->_sKey = $this->_rpc->call(
			'auth.login',
			array_merge($creds, ['duration' => (int)$opts['duration']])
		);
	}

	/**
	 * Destructor.
	 */
	public function __destruct()
	{
		$this->_rpc->call('auth.logout', $this->_sKey);
	}

	/**
	 * Make a call to Spacewalk instance.
	 * Dot (.) for namespace is replaced with underscore (_).
	 *
	 * Example, the XMLRPC call: system.listSystems()
	 * in PHP becomes: $spacewalk->system_listSystems();
	 *
	 * @param string $method
	 * @param array  $args
	 *
	 * @return mixed
	 */
	public function __call($method, array $args = [])
	{
		$m = preg_replace('/_/', '.', $method);

		return $this->_rpc->call($m, array_merge(['sessionKey' => $this->_sKey], $args));
	}
}
