<?php

namespace Diskerror\Service;

use Zend\Ldap as Lp;
use Zend\Stdlib;

/**
 * Extends the ZF2 LDAP class to gather all results when paging is required.
 * Code borrowed from Zend\Ldap\Ldap::search 2.4.2 and the PHP manual.
 */
class Ldap extends Lp\Ldap
{
	const PAGE_SIZE = 100;

	/**
	 * Search LDAP registry for entries matching filter and optional attributes
	 * and return ALL values, including those beyond the usual 1000 entries, as an array.
	 *
	 * @param  string	$filter
	 * @param  array	$attributes -OPTIONAL
	 * @return array
	 * @throws Exception\LdapException
	 */
	public function searchAll($filter, array $attributes = [])
	{
		$ldap = $this->getResource();

		// $ds is a valid link identifier (see ldap_connect)
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

		$cookie = '';
		$result = [];
		do {
			ldap_control_paged_result($ldap, self::PAGE_SIZE, true, $cookie);

			Stdlib\ErrorHandler::start(E_WARNING);
			$search = ldap_search($ldap, $this->getBaseDn(), $filter, $attributes);
			Stdlib\ErrorHandler::stop();

			if ($search === false) {
				throw new Lp\Exception\LdapException($this, 'searching: ' . $filter);
			}

			$entries = $this->createCollection(new Lp\Collection\DefaultIterator($this, $search), null);
			foreach ( $entries as $es ) {
				$result[] = $es;
			}

			ldap_control_paged_result_response($ldap, $search, $cookie);
		} while ($cookie !== null && $cookie != '');

		return $result;
	}
}
