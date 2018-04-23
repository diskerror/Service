<?php

namespace Diskerror\Service\VCenter;

/**
 * Manages TraversalSpec for Diskerror\Service\Vcenter::execRetrieveProperties.
 */
final class TraversalSpec
{
	/**
	 * Holds the TraversalSpec for preprepared Diskerror\Service\Vcenter::execRetrieveProperties.
	 * @type array
	 */
	protected static $_ts;

	/**
	 * @return array
	 */
	public static function get()
	{
		if (!isset(self::$_ts)) {
			self::$_ts = [];

			self::$_ts[] = new \SoapVar(
				[
					'name' => 'folderTraversalSpec',
					'type' => 'Folder',
					'path' => 'childEntity',
					'skip' => false,
					new \SoapVar(['name' => 'folderTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet', null),
					new \SoapVar(['name' => 'datacenterHostTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet',
						null),
					new \SoapVar(['name' => 'datacenterVmTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet',
						null),
					new \SoapVar(['name' => 'datacenterDatastoreTraversalSpec'], SOAP_ENC_OBJECT, null, null,
						'selectSet', null),
					new \SoapVar(['name' => 'datacenterNetworkTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet',
						null),
					new \SoapVar(['name' => 'computeResourceRpTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet',
						null),
					new \SoapVar(['name' => 'computeResourceHostTraversalSpec'], SOAP_ENC_OBJECT, null, null,
						'selectSet', null),
					new \SoapVar(['name' => 'hostVmTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet', null),
					new \SoapVar(['name' => 'resourcePoolVmTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet',
						null),
				],
				SOAP_ENC_OBJECT,
				'TraversalSpec'
			);

			self::$_ts[] = new \SoapVar(
				[
					'name' => 'datacenterDatastoreTraversalSpec',
					'type' => 'Datacenter',
					'path' => 'datastoreFolder',
					'skip' => false,
					new \SoapVar(['name' => 'folderTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet', null),
				],
				SOAP_ENC_OBJECT,
				'TraversalSpec'
			);

			self::$_ts[] = new \SoapVar(
				[
					'name' => 'datacenterNetworkTraversalSpec',
					'type' => 'Datacenter',
					'path' => 'networkFolder',
					'skip' => false,
					new \SoapVar(['name' => 'folderTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet', null),
				],
				SOAP_ENC_OBJECT,
				'TraversalSpec'
			);

			self::$_ts[] = new \SoapVar(
				[
					'name' => 'datacenterVmTraversalSpec',
					'type' => 'Datacenter',
					'path' => 'vmFolder',
					'skip' => false,
					new \SoapVar(['name' => 'folderTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet', null),
				],
				SOAP_ENC_OBJECT,
				'TraversalSpec'
			);

			self::$_ts[] = new \SoapVar(
				[
					'name' => 'datacenterHostTraversalSpec',
					'type' => 'Datacenter',
					'path' => 'hostFolder',
					'skip' => false,
					new \SoapVar(['name' => 'folderTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet', null),
				],
				SOAP_ENC_OBJECT,
				'TraversalSpec'
			);

			self::$_ts[] = new \SoapVar(
				[
					'name' => 'computeResourceHostTraversalSpec',
					'type' => 'ComputeResource',
					'path' => 'host',
					'skip' => false,
				],
				SOAP_ENC_OBJECT,
				'TraversalSpec'
			);

			self::$_ts[] = new \SoapVar(
				[
					'name' => 'computeResourceRpTraversalSpec',
					'type' => 'ComputeResource',
					'path' => 'resourcePool',
					'skip' => false,
					new \SoapVar(['name' => 'resourcePoolTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet',
						null),
					new \SoapVar(['name' => 'resourcePoolVmTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet',
						null),
				],
				SOAP_ENC_OBJECT,
				'TraversalSpec'
			);

			self::$_ts[] = new \SoapVar(
				[
					'name' => 'resourcePoolTraversalSpec',
					'type' => 'ResourcePool',
					'path' => 'resourcePool',
					'skip' => false,
					new \SoapVar(['name' => 'resourcePoolTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet',
						null),
					new \SoapVar(['name' => 'resourcePoolVmTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet',
						null),
				],
				SOAP_ENC_OBJECT,
				'TraversalSpec'
			);

			self::$_ts[] = new \SoapVar(
				[
					'name' => 'hostVmTraversalSpec',
					'type' => 'HostSystem',
					'path' => 'vm',
					'skip' => false,
					new \SoapVar(['name' => 'folderTraversalSpec'], SOAP_ENC_OBJECT, null, null, 'selectSet', null),
				],
				SOAP_ENC_OBJECT,
				'TraversalSpec'
			);

			self::$_ts[] = new \SoapVar(
				[
					'name' => 'resourcePoolVmTraversalSpec',
					'type' => 'ResourcePool',
					'path' => 'vm',
					'skip' => false,
				],
				SOAP_ENC_OBJECT,
				'TraversalSpec'
			);
		}

		return self::$_ts;
	}
}
