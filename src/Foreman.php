<?php

namespace Diskerror\Service;

use LogicException;
use Zend\Json\Json;

/**
 * Accessor for Foreman.
 *
 * Possible INI file contents passed to $opts and $creds
 *   foreman.url_context = https://
 *   foreman.url = service.domain:3000/api/v2/
 *
 *   cred.username = username
 *   cred.password = password
 */
class Foreman implements ServiceInterface
{
	/**
	 * @type string
	 */
	private $_url;

	/**
	 * Constructor.
	 *
	 * @param array $opts
	 * @param array $creds
	 */
	public function __construct(array $opts, array $creds)
	{
		$this->_url =
			$opts['url_context'] .
			$creds['username'] . ':' . $creds['password'] . '@' .
			$opts['url'];
	}

	/**
	 * Make a call to Foreman instance.
	 * Currently only GET method is supported.
	 *
	 * Method name starts with GET_, POST_, PUT_, or DELETE_
	 *   upper or lower case.
	 *
	 * Args must have the key/value parameter array in the last position.
	 *
	 * Example: GET /api/hosts?per_page=10&page=7
	 * becomes: $foreman->get_hosts( ['per_page'=>10, 'page'=>7] );
	 *
	 * @param string $method
	 * @param array  $args
	 *
	 * @return mixed
	 * @throws LogicException
	 */
	public function __call($method, array $args = [])
	{
		$m = explode('_', $method, 2);
		if (strcasecmp($m[0], 'GET') !== 0) {
			throw new LogicException('only GET is currently supported');
		}

		$url = $this->_url . $m[1];
		while ($a = array_shift($args)) {
			$url .= is_array($a) ?
				('?' . http_build_query($a)) :
				('/' . urlencode($a));
		}

		return Json::decode(file_get_contents($url), Json::TYPE_ARRAY);
	}
}
