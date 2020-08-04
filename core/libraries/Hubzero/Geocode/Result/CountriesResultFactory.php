<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Geocode\Result;

use Geocoder\Result\ResultFactoryInterface;

/**
 * Countries result factory
 */
class CountriesResultFactory implements ResultFactoryInterface
{
	/**
	 * {@inheritDoc}
	 */
	final public function createFromArray(array $data)
	{
		$result = new \SplObjectStorage();
		foreach ($data as $row)
		{
			$instance = $this->newInstance();
			$instance->fromArray($row);
			$result->attach($instance);
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function newInstance()
	{
		return new Country();
	}
}
