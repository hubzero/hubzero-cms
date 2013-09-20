<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

define('FORUM_STATE_UNPUBLISHED', 0);
define('FORUM_STATE_PUBLISHED',   1);
define('FORUM_STATE_DELETED',     2);

if (!defined('FORUM_DATE_FORMAT'))
{
	if (version_compare(JVERSION, '1.6', 'ge'))
	{
		define('FORUM_DATE_YEAR', "Y");
		define('FORUM_DATE_MONTH', "m");
		define('FORUM_DATE_DAY', "d");
		define('FORUM_DATE_TIMEZONE', false);
		define('FORUM_DATE_FORMAT', 'd M Y');
		define('FORUM_TIME_FORMAT', 'h:i A');
	}
	else
	{
		define('FORUM_DATE_YEAR', "%Y");
		define('FORUM_DATE_MONTH', "%m");
		define('FORUM_DATE_DAY', "%d");
		define('FORUM_DATE_TIMEZONE', 0);
		define('FORUM_DATE_FORMAT', '%d %b %Y');
		define('FORUM_TIME_FORMAT', '%I:%M %p');
	}
}

/**
 * Abstract model for forums
 */
abstract class ForumModelAbstract extends JObject
{
	/**
	 * Table class name
	 * 
	 * @var object
	 */
	protected $_tbl_name = null;

	/**
	 * JTable
	 * 
	 * @var object
	 */
	protected $_tbl = NULL;

	/**
	 * JUser
	 * 
	 * @var object
	 */
	protected $_creator = NULL;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	protected $_db = NULL;

	/**
	 * JParameter
	 * 
	 * @var object
	 */
	protected $_config = NULL;

	/**
	 * Constructor
	 * 
	 * @param      mixed $oid Integer (ID), string (alias), object or array
	 * @return     void
	 */
	public function __construct($oid)
	{
		$this->_db = JFactory::getDBO();

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (!is_a($this->_tbl, 'JTable'))
			{
				throw new RuntimeException(JText::_('Table class must be of type JTable.'));
			}

			if (is_numeric($oid) || is_string($oid))
			{
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
		if ($this->get('id') && (int) $this->get('id') > 0) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Was the entry deleted?
	 * 
	 * @return     boolean
	 */
	public function isPublished()
	{
		if ($this->get('state') == FORUM_STATE_PUBLISHED) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Was the entry deleted?
	 * 
	 * @return     boolean
	 */
	public function isDeleted()
	{
		if ($this->get('state') == FORUM_STATE_DELETED) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Return a formatted timestamp
	 * 
	 * @param      string $as What data to return
	 * @return     boolean
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return JHTML::_('date', $this->get('created'), FORUM_DATE_FORMAT, FORUM_DATE_TIMEZONE);
			break;

			case 'time':
				return JHTML::_('date', $this->get('created'), FORUM_TIME_FORMAT, FORUM_DATE_TIMEZONE);
			break;

			default:
				return $this->get('created');
			break;
		}
	}

	/**
	 * Get the creator of this entry
	 * 
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function creator($property=null)
	{
		if (!isset($this->_creator) || !is_object($this->_creator))
		{
			$this->_creator = JUser::getInstance($this->get('created_by'));
		}
		if ($property && is_a($this->_creator, 'JUser'))
		{
			return $this->_creator->get($property);
		}
		return $this->_creator;
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
			throw new InvalidArgumentException(JText::sprintf('Data must be of type object or array. Type given was %s', gettype($data)));
		}

		return $res;
	}

	/**
	 * Store changes to this database entry
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		// Validate data?
		if ($check)
		{
			// Is data valid?
			if (!$this->_tbl->check())
			{
				$this->setError($this->_tbl->getError());
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

	/**
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 * 
	 * @param      string $key Config property to retrieve
	 * @return     mixed
	 */
	public function config($key=null)
	{
		if (!isset($this->_config))
		{
			$this->_config =& JComponentHelper::getParams('com_forum');
		}
		if ($key)
		{
			return $this->_config->get($key);
		}
		return $this->_config;
	}
}

