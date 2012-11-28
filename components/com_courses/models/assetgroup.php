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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'iterator.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.group.php');

/**
 * Courses model class for a course
 */
class CoursesModelAssetgroup extends JObject
{
	/**
	 * CoursesTableInstance
	 * 
	 * @var array
	 */
	public $assets = null;

	/**
	 * CoursesTableInstance
	 * 
	 * @var array
	 */
	public $children = null;

	/**
	 * CoursesTableInstance
	 * 
	 * @var object
	 */
	private $_tbl = NULL;

	/**
	 * CoursesTableInstance
	 * 
	 * @var object
	 */
	private $_creator = NULL;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_parent = NULL;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	public $_siblings = null;

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($oid)
	{
		$this->children = new CoursesModelIterator(array());

		$this->_db = JFactory::getDBO();

		$this->_tbl = new CoursesTableAssetGroup($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid))
		{
			$this->_tbl->bind($oid);
		}
		else if (is_array($oid))
		{
			$this->_tbl->bind($oid);
		}

		/*if (is_a($parent, 'CoursesModelUnit'))
		{
			$this->_parent = $parent;
		}*/
	}

	/**
	 * Returns a reference to a wiki page object
	 *
	 * This method must be invoked as:
	 *     $inst = CoursesInstance::getInstance($alias);
	 *
	 * @param      string $pagename The page to load
	 * @param      string $scope    The page scope
	 * @return     object WikiPage
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$oid])) 
		{
			$instances[$oid] = new CoursesModelAssetgroup($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Check if a property is set
	 * 
	 * @param      string $property Name of property to set
	 * @return     boolean True if set
	 */
	/*public function __isset($property)
	{
		return isset($this->_data[$property]);
	}

	/**
	 * Set a property
	 * 
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	/*public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	/*public function __get($property)
	{
		if (isset($this->asset->$property)) 
		{
			return $this->asset->$property;
		}
		else if (isset($this->_data[$property])) 
		{
			return $this->_data[$property];
		}
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
		$previous = isset($this->_tbl->$property) ? $this->_tbl->$property : null;
		$this->_tbl->$property = $value;
		return $previous;
	}

	/**
	 * Check if the resource exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
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
			$this->_creator = JUser::getInstance($this->get('created_by'));
		}
		if ($property && is_a($this->_creator, 'JUser'))
		{
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Method to set the article id
	 *
	 * @param	int	Article ID number
	 */
	public function children($idx=null)
	{
		if ($idx !== null)
		{
			if (is_numeric($idx))
			{
				if (isset($this->children[$idx]))
				{
					return $this->children[$idx];
				}
				else
				{
					$this->setError(JText::_('Index not found: ') . __CLASS__ . '::' . __METHOD__ . '[' . $idx . ']');
					return false;
				}
			}
			else if (is_array($idx))
			{
				$found = false;
				$res = array();
				foreach ($this->children as $child)
				{
					$obj = new stdClass;
					foreach ($idx as $property)
					{
						$property = strtolower(trim($property));
						if (isset($child->$property))
						{
							$obj->$property = $child->$property;
							$found = true;
						}
					}
					if ($found)
					{
						$res[] = $obj;
					}
				}
				return $res;
			}
			else if (is_string($idx))
			{
				$idx = strtolower(trim($idx));

				$res = array();
				foreach ($this->children as $child)
				{
					if (isset($child->$idx))
					{
						$res[] = $child->$idx;
					}
				}
				return $res;
			}
		}
		return $this->children;
	}

	/**
	 * Get a specific asset
	 *
	 * @param     integer $id Asset ID
	 * @return    object CoursesModelAsset
	 */
	public function asset($id=null)
	{
		if (!isset($this->asset) 
		 || ($id !== null && (int) $this->asset->get('id') != $id))
		{
			$this->asset = null;

			foreach ($this->assets() as $key => $asset)
			{
				if ((int) $asset->get('id') == $id)
				{
					$this->asset = $asset;
					break;
				}
			}
		}
		return $this->asset;
	}

	/**
	 * Get a list of assets for a unit
	 *   Accepts an array of filters to apply to the list of assets
	 * 
	 * @param      array $filters Filters to apply
	 * @return     object CoursesModelIterator
	 */
	public function assets($filters=array())
	{
		if (!isset($this->assets) || !is_a($this->assets, 'CoursesModelIterator'))
		{
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');

			$filters['asset_scope_id'] = (int) $this->get('id');
			$filters['asset_scope']    = 'asset_group';

			$tbl = new CoursesTableAsset($this->_db);

			if (($results = $tbl->find(array('w' => $filters))))
			{
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php');

				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesModelAsset($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->assets = new CoursesModelIterator($results);
		}

		/*if ($idx !== null)
		{
			if (is_numeric($idx))
			{
				if (isset($this->assets[$idx]))
				{
					return $this->assets[$idx];
				}
				else
				{
					$this->setError(JText::_('Index not found: ') . __CLASS__ . '::' . __METHOD__ . '[' . $idx . ']');
					return false;
				}
			}
			else if (is_array($idx))
			{
				$res = array();
				foreach ($this->assets as $asset)
				{
					$obj = new stdClass;
					foreach ($idx as $property)
					{
						$property = strtolower(trim($property));
						if (isset($asset->$property))
						{
							$obj->$property = $asset->$property;
						}
					}
					if ($found)
					{
						$res[] = $obj;
					}
				}
				return $res;
			}
			else if (is_string($idx))
			{
				$idx = strtolower(trim($idx));

				$res = array();
				foreach ($this->assets as $asset)
				{
					if (isset($asset->$idx))
					{
						$res[] = $asset->$idx;
					}
				}
				return $res;
			}
		}*/

		return $this->assets;
	}

	public function siblings(&$siblings) 
	{
		/*if (is_array($siblings) && !is_a($siblings, 'CoursesModelIterator'))
		{
			$siblings = new CoursesModelIterator($siblings);
		}*/
		if (is_a($siblings, 'CoursesModelIterator'))
		{
			$this->_siblings = $siblings;
		}
		else
		{
			$this->_siblings = new CoursesModelIterator($siblings);
		}
	}

	/**
	 * Is the current position the first one?
	 *
	 * @return     boolean
	 */
	public function isFirst() 
	{
		if (!$this->_siblings)
		{
			return true;
		}
		return $this->_siblings->isFirst();
	} 

	/**
	 * Is the current position the last one?
	 *
	 * @return     boolean
	 */
	public function isLast() 
	{
		if (!$this->_siblings)
		{
			return true;
		}
		return $this->_siblings->isLast();
	}

	/**
	 * Return the key for the current cursor position
	 *
	 * @return     mixed
	 */
	public function key($idx=null) 
	{
		return $this->_siblings->key($idx);
	}

	/**
	 * Set cursor position to previous position and return array value
	 *
	 * @return     mixed
	 */
	public function sibling($dir='next') 
	{
		if (!$this->_siblings)
		{
			return null;
		}
		$dir = strtolower(trim($dir));
		switch ($dir)
		{
			case 'prev':
			case 'next':
				return $this->_siblings->fetch($dir);
			break;

			default:
				
			break;
		}
		return null;
	}

	/**
	 * Check if the course exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function bind($data=null)
	{
		return $this->_tbl->bind($data);
	}

	/**
	 * Short title for 'update'
	 * Long title (if any) ...
	 *
	 * @param unknown $course_id Parameter title (if any) ...
	 * @param array $data Parameter title (if any) ...
	 * @return boolean Return title (if any) ...
	 */
	public function store($check=true)
	{
		if (empty($this->_db))
		{
			return false;
		}

		if ($check)
		{
			if (!$this->_tbl->check())
			{
				$this->setError($this->_tbl->getError());
				return false;
			}
		}

		if (!$this->_tbl->store())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
	}
}

