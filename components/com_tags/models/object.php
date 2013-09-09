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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'object.php');

/**
 * Model class for a tag/object association
 */
class TagsModelObject extends JObject
{
	/**
	 * TagsObject
	 * 
	 * @var object
	 */
	protected $_tbl = null;

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
	 * Constructor
	 * 
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct($oid, $scope_id=null, $tag_id=null)
	{
		// Set the database object
		$this->_db = JFactory::getDBO();

		// Set the table object
		$this->_tbl = new TagsObject($this->_db);

		// Load record
		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		if (is_string($oid))
		{
			$this->_tbl->loadByObjectTag($oid, $scope_id, $tag_id);
		}
		else if (is_object($oid))
		{
			$this->_tbl->bind($oid);
			$properties = $this->_tbl->getProperties();
			foreach (get_object_vars($oid) as $key => $property)
			{
				if (!array_key_exists($key, $properties))
				{
					$this->_tbl->set('__' . $key, $property);
				}
			}
		}
		else if (is_array($oid))
		{
			$this->_tbl->bind($oid);
			$properties = $this->_tbl->getProperties();
			foreach (array_keys($oid) as $key)
			{
				if (!array_key_exists($key, $properties))
				{
					$this->_tbl->set('__' . $key, $oid[$key]);
				}
			}
		}
	}

	/**
	 * Returns a reference to a tags object model
	 *
	 * @param      mixed   $oid      ID, string (scope) or object
	 * @param      integer $scope_id Object ID
	 * @param      integer $tag_id   Tag ID
	 * @return     object TagsModelObject
	 */
	static function &getInstance($oid=0, $scope_id=null, $tag_id=null)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid . $scope_id . $tag_id;
		}
		else if (is_object($oid))
		{
			$key = $oid->id . $scope_id . $tag_id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'] . $scope_id . $tag_id;
		}

		if (!isset($instances[$oid])) 
		{
			$instances[$oid] = new TagsModelObject($oid, $scope_id, $tag_id);
		}

		return $instances[$oid];
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $default The default value
	 * @return	mixed The value of the property
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
	 * @return	mixed Previous value of the property
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
	 * Check if the record exists
	 * 
	 * @return     boolean
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
			$this->_creator = JUser::getInstance($this->get('taggerid'));
		}
		if ($property && is_a($this->_creator, 'JUser'))
		{
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Return a formatted timestamp
	 * 
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function created($rtrn='')
	{
		switch (strtolower($rtrn))
		{
			case 'date':
				return JHTML::_('date', $this->get('taggedon'), TAGS_DATE_FORMAT, TAGS_DATE_TIMEZONE);
			break;

			case 'time':
				return JHTML::_('date', $this->get('taggedon'), TAGS_TIME_FORMAT, TAGS_DATE_TIMEZONE);
			break;

			default:
				return $this->get('taggedon');
			break;
		}
	}

	/**
	 * Bind data to this model
	 * 
	 * @param      mixed $idx Data to bind
	 * @return     boolean
	 */
	public function bind($data=null)
	{
		return $this->_tbl->bind($data);
	}

	/**
	 * Store changes to this offering
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
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function delete()
	{
		// Ensure we have a database to work with
		if (empty($this->_db))
		{
			$this->setError(JText::_('Database not found.'));
			return false;
		}

		// Can't delete what doesn't exist
		if (!$this->exists()) 
		{
			return true;
		}

		// Attempt to delete the record
		if (!$this->_tbl->delete())
		{
			$this->setError($this->_tbl->getErrorMsg());
			return false;
		}

		return true;
	}
}

