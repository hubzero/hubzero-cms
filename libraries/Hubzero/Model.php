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

namespace Hubzero;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Abstract model class
 */
abstract class Model extends Object
{
	/**
	 * Unpublished state
	 * 
	 * @var integer
	 */
	const APP_STATE_UNPUBLISHED = 0;

	/**
	 * Published state
	 * 
	 * @var integer
	 */
	const APP_STATE_PUBLISHED   = 1;

	/**
	 * Deleted state
	 * 
	 * @var integer
	 */
	const APP_STATE_DELETED     = 2;

	/**
	 * CoursesTableAsset
	 * 
	 * @var string
	 */
	protected $_tbl_name = null;

	/**
	 * CoursesTableAsset
	 * 
	 * @var object
	 */
	protected $_tbl = NULL;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	protected $_db = NULL;

	/**
	 * Constructor
	 * 
	 * @param      mixed $oid Integer (ID), string (alias), object or array
	 * @return     void
	 */
	public function __construct($oid)
	{
		$this->_db = \JFactory::getDBO();

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (is_numeric($oid) || is_string($oid))
			{
				// Make sure $oid isn't empty
				// This saves a database call
				if ($oid)
				{
					$this->_tbl->load($oid);
				}
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $default The default value
	 * @return	mixed  The value of the property
 	 */
	public function get($property, $default=null)
	{
		if (isset($this->_tbl->$property)) 
		{
			return $this->_tbl->$property;
		}
		else if (isset($this->_tbl->{'__' . $property})) 
		{
			return $this->_tbl->{'__' . $property};
		}
		return $default;
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $value The value of the property to set
	 * @return	mixed  Previous value of the property
	 */
	public function set($property, $value = null)
	{
		if (!array_key_exists($property, $this->_tbl->getProperties()))
		{
			$property = '__' . $property;
		}
		$previous = isset($this->_tbl->$property) ? $this->_tbl->$property : null;
		$this->_tbl->$property = $value;
		return $previous;
	}

	/**
	 * Check if the entry exists (i.e., has a database record)
	 * 
	 * @return     boolean True if record exists, False if not
	 */
	public function exists()
	{
		if (!in_array('id', array_keys($this->_tbl->getProperties())))
		{
			return true;
		}
		if ($this->get('id') && (int) $this->get('id') > 0) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Has the offering started?
	 * 
	 * @return     boolean
	 */
	public function isPublished()
	{
		if (!in_array('state', array_keys($this->_tbl->getProperties())))
		{
			return true;
		}
		if ($this->get('state') == self::APP_STATE_PUBLISHED) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Has the offering started?
	 * 
	 * @return     boolean
	 */
	public function isUnpublished()
	{
		if (!in_array('state', array_keys($this->_tbl->getProperties())))
		{
			return false;
		}
		if ($this->get('state') == self::APP_STATE_UNPUBLISHED) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Has the offering started?
	 * 
	 * @return     boolean
	 */
	public function isDeleted()
	{
		if (!in_array('state', array_keys($this->_tbl->getProperties())))
		{
			return false;
		}
		if ($this->get('state') == self::APP_STATE_DELETED) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Bind data to the model
	 * 
	 * @param      mixed $data Object or array
	 * @return     boolean True on success, False on error
	 */
	public function bind($data=null)
	{
		if (is_object($data))
		{
			$res = $this->_tbl->bind($data);

			if ($res)
			{
				$properties = $this->_tbl->getProperties();
				foreach (get_object_vars($data) as $key => $property)
				{
					if (!array_key_exists($key, $properties))
					{
						$this->_tbl->set('__' . $key, $property);
					}
				}
			}
		}
		else if (is_array($data))
		{
			$res = $this->_tbl->bind($data);

			if ($res)
			{
				$properties = $this->_tbl->getProperties();
				foreach (array_keys($data) as $key)
				{
					if (!array_key_exists($key, $properties))
					{
						$this->_tbl->set('__' . $key, $data[$key]);
					}
				}
			}
		}
		else
		{
			throw new \InvalidArgumentException(\JText::sprintf('Data must be of type object or array. Type given was %s', gettype($data)));
		}

		return $res;
	}

	/**
	 * Perform data validation
	 *
	 * @return    boolean False if error, True on success
	 */
	public function check()
	{
		// Is data valid?
		if (!$this->_tbl->check())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}
		return true;
	}

	/**
	 * Store changes to this database entry
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		// Ensure we have a database to work with
		if (empty($this->_db))
		{
			return false;
		}

		// Validate data?
		if ($check)
		{
			// Is data valid?
			if (!$this->check())
			{
				return false;
			}
		}

		// Attempt to store data
		if (!$this->_tbl->store())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Delete a record
	 * 
	 * @return     boolean True on success, false on error
	 */
	public function delete()
	{
		// Remove record from the database
		if (!$this->_tbl->delete())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		// Hey, no errors!
		return true;
	}
}

