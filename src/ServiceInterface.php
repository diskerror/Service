<?php

namespace Diskerror\Service;

/**
 * Diskerror\Service interface.
 */
interface ServiceInterface
{
	/**
	 * Constructor.
	 *
	 * @param array $opts
	 * @param array $creds
	 */
	public function __construct(array $opts, array $creds);
}
