<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Antispam;

use Hubzero\Antispam\Adapter\AdapterInterface;
use Hubzero\Antispam\Exception\AdapterNotFoundException;
use InvalidArgumentException;

/**
 * Antispam service class
 */
class Service
{
	/**
	 * Antispam adapter
	 *
	 * @var  object
	 */
	protected $_adapter = null;

	/**
	 * Map of characters to be replaced through strtr
	 *
	 * @var  array
	 */
	protected $_canonicalNamesReplacements = array(
		'-'  => '',
		'_'  => '',
		' '  => '',
		'\\' => '',
		'/'  => ''
	);

	/**
	 * Default set of adapters
	 *
	 * @var  array
	 */
	protected $_invokableClasses = array();

	/**
	 * Constructor
	 *
	 * @param   mixed  $adapter
	 * @return  void
	 */
	public function __construct($adapter = null)
	{
		if (null !== $adapter)
		{
			$this->setAdapter($adapter);
		}
	}

	/**
	 * Returns the authentication adapter
	 *
	 * The adapter does not have a default if the storage adapter has not been set.
	 *
	 * @return  object
	 */
	public function getAdapter()
	{
		return $this->_adapter;
	}

	/**
	 * Sets the authentication adapter
	 *
	 * @param   mixed   $adapter  String or AdapterInterface
	 * @return  object
	 */
	public function setAdapter($adapter)
	{
		if (is_string($adapter))
		{
			$cName = $this->canonicalizeName($adapter);

			if (isset($this->_invokableClasses[$cName]))
			{
				$invokable = $this->_invokableClasses[$cName];

				if (!class_exists($invokable))
				{
					throw new AdapterNotFoundException(sprintf(
						'%s: failed retrieving adapter via invokable class "%s"; class does not exist',
						get_class($this) . '::' . __FUNCTION__,
						$invokable
					));
				}
				$adapter = new $invokable;
			}
		}

		if (!($adapter instanceof AdapterInterface))
		{
			throw new InvalidArgumentException(sprintf(
				'%s was unable to fetch adapter or adapter was not an instance of %s',
				get_class($this) . '::' . __FUNCTION__,
				__NAMESPACE__ . '\AdapterInterface'
			));
		}

		$this->_adapter = $adapter;

		return $this;
	}

	/**
	 * Set a property
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  object
	 */
	public function set($key, $value)
	{
		$this->getAdapter()->set($key, $value);
		return $this;
	}

	/**
	 * Get a property
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	public function get($key)
	{
		return $this->getAdapter()->get($key);
	}

	/**
	 * Set properties
	 *
	 * @param   mixed   $data  Array or object
	 * @return  object
	 */
	public function setProperties($data)
	{
		$this->getAdapter()->setProperties($data);
		return $this;
	}

	/**
	 * Get properties
	 *
	 * @return  array
	 */
	public function getProperties()
	{
		return $this->getAdapter()->getProperties();
	}

	/**
	 * Validate against the supplied adapter
	 *
	 * @param   string   $value
	 * @return  boolean
	 * @throws  AdapterNotFoundException
	 */
	public function isSpam($value)
	{
		if (!$adapter = $this->getAdapter())
		{
			throw new AdapterNotFoundException('An adapter must be set or passed prior to calling isSpam()');
		}

		$adapter->setValue($value);

		return $adapter->isSpam($value);
	}

	/**
	 * Train the service
	 *
	 * @param   string   $value
	 * @param   boolean  $isSpam
	 * @return  boolean
	 * @throws  AdapterNotFoundException
	 */
	public function learn($value, $isSpam)
	{
		if (!$adapter = $this->getAdapter())
		{
			throw new AdapterNotFoundException('An adapter must be set or passed prior to calling isSpam()');
		}

		$adapter->setValue($value);

		return $adapter->learn($value, $isSpam);
	}

	/**
	 * Forget a trained value
	 *
	 * @param   string   $value
	 * @param   boolean  $isSpam
	 * @return  boolean
	 * @throws  AdapterNotFoundException
	 */
	public function forget($value, $isSpam)
	{
		if (!$adapter = $this->getAdapter())
		{
			throw new AdapterNotFoundException('An adapter must be set or passed prior to calling isSpam()');
		}

		$adapter->setValue($value);

		return $adapter->forget($value, $isSpam);
	}

	/**
	 * Canonicalize name
	 *
	 * @param   string  $name
	 * @return  string
	 */
	protected function canonicalizeName($name)
	{
		if (isset($this->canonicalNames[$name]))
		{
			return $this->canonicalNames[$name];
		}

		// this is just for performance instead of using str_replace
		return strtolower(strtr($name, $this->_canonicalNamesReplacements));
	}
}
