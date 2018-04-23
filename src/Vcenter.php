<?php

namespace Diskerror\Service;

/**
 * Accessor for VCenter.
 * Some code comes from:
 *     https://geauxvirtual.wordpress.com/2010/10/07/using-php5-soap-with-vsphere-api/
 *     https://vadimcomanescu.wordpress.com/2013/03/19/vmwarephp-vsphere-api-bindings-for-php/
 *     https://github.com/vadimcomanescu/vmwarephp.
 *
 * Possible INI file contents passed to $opts and $cred
 *     vcenter.url = https://service.domain/sdk/vimService.wsdl
 *     vcenter.location = https://service.domain/sdk/
 *     vcenter.compression = SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_DEFLATE
 *     vcenter.trace = 1
 *     vcenter.cache_wsdl = WSDL_CACHE_BOTH
 *     vcenter.user_agent = "user_agent"
 *
 *     cred.username = username
 *     cred.password = password
 */
class Vcenter implements ServiceInterface
{
	/**
	 * Holds the SoapClient instance.
	 * @type \SoapClient
	 */
	protected $_client;

	/**
	 * Holds the session object.
	 * @type stdClass
	 */
	protected $_session;

	/**
	 * Holds the serviceContent object.
	 * @type stdClass
	 */
	protected $_sc;

	/**
	 * Constructor.
	 *
	 * @param array $opts
	 * @param array $creds
	 */
	public function __construct(array $opts, array $creds)
	{
		ini_set('soap.wsdl_cache_enabled', 1);

		$wsdl = $opts['url'];
		unset($opts['url']);

		$this->_client = new \SoapClient($wsdl, $opts);

		$this->_sc = $this->RetrieveServiceContent([
			'_this' => ['_' => 'ServiceInstance', 'type' => 'ServiceInstance'],
		]);

		// login
		$this->_session = $this->Login([
			'_this'    => $this->_sc->sessionManager,
			'userName' => $creds['username'],
			'password' => $creds['password'],
		]);
	}

	/**
	 * Destructor.
	 */
	public function __destruct()
	{
		$this->Logout(['_this' => $this->_sc->sessionManager]);
	}

	/**
	 * Return SOAP client instance.
	 * @return \SoapClient
	 */
	public function __getClient()
	{
		return $this->_client;
	}

	/**
	 * Return serviceContent object.
	 * @return stdClass
	 */
	public function __getServiceContent()
	{
		return $this->_sc;
	}

	/**
	 * Call SOAP built-in or vCenter method.
	 *
	 * Proper parameter object is built from simplified array type.
	 *
	 * Assumes all values associated with the first member of the array
	 *    are unnamed. They will be associated with "_" and "type" by position.
	 *    All other members must be key/value pairs.
	 *
	 * @param string $name
	 * @param string $args -OPTIONAL
	 *
	 * @return mixed
	 */
	public function __call($name, array $args = [])
	{
		//	Handle SOAPClient built-in methods.
		if (substr($name, 0, 2) === '__') {
			if (count($args) === 0) {
				return $this->_client->$name();
			}
			else {
				return call_user_func_array([$this->_client, $name], $args);
			}
		}

		//	If no parameters for vCenter function:
		if (count($args) === 0) {
			$ret = $this->_client->$name();
		}
		else {
			//	Parameters to vCenter are a single object.
			$ret = $this->_client->$name($args[0]);
		}

		if (isset($ret->returnval)) {
			return $ret->returnval;
		}

		return $ret;
	}

	/**
	 * Execute preprepared version of "RetrieveProperties" or "RetrievePropertiesEx".
	 *
	 * @param string $objType VirtualMachine|HostSystem|ClusterComputeResource
	 * @param array  $fields  -OPTIONAL
	 * @param int    $limit   -OPTIONAL
	 *
	 * @return stdClass
	 * @throws InvalidArgumentException
	 */
	public function execRetrieveProperties($objType, $fields = [], $limit = 0)
	{
		switch ($objType) {
			case 'VirtualMachine':
			case 'HostSystem':
			case 'ClusterComputeResource':
			break;

			default:
				throw new \InvalidArgumentException('must be “VirtualMachine”, “HostSystem” or “ClusterComputeResource”');
		}

		$message = [
			'_this'   => $this->_sc->propertyCollector,
			'specSet' => [
				'propSet'   => [
					'type' => $objType,
					'all'  => true,
				],
				'objectSet' => [
					'obj'       => $this->_sc->rootFolder,
					'selectSet' => VCenter\TraversalSpec::get(),
				],
			],
		];

		if (count($fields)) {
			$message['specSet']['propSet']['all'] = false;
			$message['specSet']['propSet']['pathSet'] = $fields;
		}

		if ($limit > 0) {
			$message['options'] = ['maxObjects' => $limit];

			$ret = $this->_client->RetrievePropertiesEx($message);

			return $ret->returnval->objects;
		}

		$ret = $this->_client->RetrieveProperties($message);

		return $ret->returnval;
	}
}
