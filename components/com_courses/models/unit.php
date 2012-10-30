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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'unit.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'iterator.php');

/**
 * Courses model class for a course
 */
class CoursesModelUnit extends JObject
{
	/**
	 * CoursesTableInstance
	 * 
	 * @var object
	 */
	public $unit = NULL;

	/**
	 * CoursesTableInstance
	 * 
	 * @var object
	 */
	public $group = NULL;

	/**
	 * CoursesTableInstance
	 * 
	 * @var object
	 */
	//public $params = NULL;

	/**
	 * CoursesTableInstance
	 * 
	 * @var object
	 */
	public $assetgroups = NULL;

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
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_data = array();

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($oid)
	{
		$this->_db = JFactory::getDBO();

		$this->unit = new CoursesTableUnit($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->unit->load($oid);
		}
		else if (is_object($oid))
		{
			$this->unit->bind($oid);
		}
		else if (is_array($oid))
		{
			$this->unit->bind($oid);
		}

		//$this->params = JComponentHelper::getParams('com_courses');
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
			$instances[$oid] = new CoursesModelUnit($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Check if a property is set
	 * 
	 * @param      string $property Name of property to set
	 * @return     boolean True if set
	 */
	public function __isset($property)
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
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->unit->$property)) 
		{
			return $this->unit->$property;
		}
		else if (isset($this->_data[$property])) 
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @access	public
	 * @param	string $property The name of the property
	 * @param	mixed  $default The default value
	 * @return	mixed The value of the property
	 * @see		getProperties()
	 * @since	1.5
 	 */
	public function get($property, $default=null)
	{
		if (isset($this->unit->$property)) 
		{
			return $this->unit->$property;
		}
		return $default;
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @access	public
	 * @param	string $property The name of the property
	 * @param	mixed  $value The value of the property to set
	 * @return	mixed Previous value of the property
	 * @see		setProperties()
	 * @since	1.5
	 */
	public function set($property, $value = null)
	{
		$previous = isset($this->unit->$property) ? $this->unit->$property : null;
		$this->unit->$property = $value;
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
			$this->_creator = JUser::getInstance($this->unit->created_by);
		}
		if ($property && is_a($this->_creator, 'JUser'))
		{
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Has the offering started?
	 * 
	 * @return     boolean
	 */
	public function started()
	{
		if (!$this->exists()) 
		{
			return false;
		}

		$now = date('Y-m-d H:i:s', time());

		if ($this->get('start_date') 
		 && $this->get('start_date') != '0000-00-00 00:00:00' 
		 && $this->get('start_date') > $now) 
		{
			return false;
		}

		return true;
	}

	/**
	 * Has the offering ended?
	 * 
	 * @return     boolean
	 */
	public function ended()
	{
		if (!$this->exists()) 
		{
			return true;
		}

		$now = date('Y-m-d H:i:s', time());

		if ($this->get('end_date') 
		 && $this->get('end_date') != '0000-00-00 00:00:00' 
		 && $this->get('end_date') <= $now) 
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if the offering is available
	 * 
	 * @return     boolean
	 */
	public function available()
	{
		if (!$this->exists())
		{
			return false;
		}

		// Make sure the resource is published and standalone
		if ($this->started() && !$this->ended()) 
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to set the article id
	 *
	 * @param	int	Article ID number
	 */
	public function assetgroup($id=null)
	{
		if (!isset($this->group) 
		 || ($id !== null && (int) $this->group->get('id') != $id && (string) $this->group->get('alias') != $id))
		{
			$this->group = null;

			foreach ($this->assetgroups() as $key => $group)
			{
				if ((int) $group->get('id') == $id || (string) $group->get('alias') == $id)
				{
					$this->group = $group;
					break;
				}
				else
				{
					foreach ($group->children() as $child)
					{
						if ((int) $child->get('id') == $id || (string) $child->get('alias') == $id)
						{
							$this->_assetgroups = $this->assetgroups; // back up the data
							$this->group = $child;
							$this->assetgroups = $group->children(); // set the current asset groups
							break;
						}
					}
				}
			}
		}
		return $this->group;
	}

	/**
	 * Reset the asset groups to top level
	 * 
	 * @return     void
	 */
	public function resetAssetgroups($idx=null, $filters=array())
	{
		$this->assetgroups = $this->_assetgroups;
		return $this->assetgroups($idx, $filters);
	}

	/**
	 * Get a list of assetgroups for an offering
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function assetgroups($idx=null, $filters=array())
	{
		if (!isset($this->assetgroups) || !is_a($this->assetgroups, 'CoursesModelIterator'))
		{
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.group.php');

			$filters['course_unit_id'] = (int) $this->get('id');

			$tbl = new CoursesTableAssetGroup($this->_db);
			if (($results = $tbl->getCourseAssetGroups(array('w' => $filters))))
			{
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'assetgroup.php');

				$list = array();

				// First pass - collect children
				foreach ($results as $v)
				{
					if ($v->parent == 0)
					{
						$list[$v->id] = new CoursesModelAssetgroup($v);
					}
				}
				foreach ($results as $c)
				{
					if (isset($list[$c->parent]))
					{
						$list[$c->parent]->children->add(new CoursesModelAssetgroup($c));
					}
				}

				$res = array_values($list);
			}
			else
			{
				$res = array();
			}
			$this->assetgroups = new CoursesModelIterator($res);
		}

		if ($idx !== null)
		{
			if (is_numeric($idx))
			{
				/*if (isset($this->assetgroups[$idx]))
				{
					return $this->assetgroups[$idx];
				}
				else
				{
					$this->setError(JText::_('Index not found: ') . __CLASS__ . '::' . __METHOD__ . '[' . $idx . ']');
					return false;
				}*/
				return $this->assetgroups->fetch($idx);
			}
			/*else if (is_array($idx))
			{
				$res = array();
				foreach ($this->assetgroups as $group)
				{
					$obj = new stdClass;
					foreach ($idx as $property)
					{
						$property = strtolower(trim($property));
						if (isset($group->$property))
						{
							$obj->$property = $group->$property;
						}
					}
					if ($found)
					{
						$res[] = $obj;
					}
				}
				return $res;
			}*/
			else if (is_string($idx))
			{
				$idx = strtolower(trim($idx));

				/*$res = array();
				foreach ($this->assetgroups as $group)
				{
					if (isset($group->$idx))
					{
						$res[] = $group->$idx;
					}
				}
				return $res;*/
				foreach ($this->assetgroups as $group)
				{
					if ($group->get('alias') == $idx)
					{
					/*if (!is_a($group, 'CoursesModelAssetgroup'))
					{
						$group = new CoursesModelAssetgroup($group);
						// Set the offering to the model
						$this->assetgroups($key, $group);
					}*/
						return $this->assetgroups;
						break;
					}
					else
					{
						foreach ($group->children() as $child)
						{
							if ($child->get('alias') == $idx)
							{
								/*if (!is_a($group, 'CoursesModelAssetgroup'))
								{
									$group = new CoursesModelAssetgroup($child);
									// Set the offering to the model
									$this->assetgroups($key, $group);
								}*/
								return $group->children();
								break;
							}
						}
					}
				}
			}
		}

		return $this->assetgroups;
	}

	/**
	 * Method to set the article id
	 *
	 * @param	int	Article ID number
	 */
	/*public function assetgrouptype($id=null)
	{
		if (!isset($this->grouptype) || $this->grouptype->id != $id)
		{
			$this->grouptype = null;

			foreach ($this->assetgrouptypes() as $key => $grouptype)
			{
				if ($grouptype->id == $id)
				{
					if (!is_a($grouptype, 'CoursesModelAssetgrouptype'))
					{
						$grouptype = new CoursesModelAssetgrouptype($grouptype);
						// Set the offering to the model
						$this->assetgrouptypes($key, $grouptype);
					}
					$this->grouptype = $grouptype;
					break;
				}
			}
		}
		return $this->grouptype;
	}*/

	/**
	 * Get a list of assetgroups for an offering
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	/*public function assetgrouptypes($idx=null, $model=null)
	{
		if (!$this->exists()) 
		{
			return array();
		}

		if (!isset($this->assetgrouptypes) || !is_array($this->assetgrouptypes))
		{
			$this->assetgrouptypes = array();

			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.group.php');

			$tbl = new CoursesTableAssetGroup($this->_db);

			// Get the unique asset group types (this will build our sub-headings)
			$results = $tbl->getUniqueCourseAssetGroupTypes(
				array(
					'w' => array(
						'course_unit_id' => $this->unit->id
					)
				)
			);
			if ($results)
			{
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'assetgrouptype.php');

				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesModelAssetgrouptype($result);
				}
				$this->assetgrouptypes = $results;
			}
		}

		if ($idx !== null)
		{
			if (is_numeric($idx))
			{
				if (isset($this->assetgrouptypes[$idx]))
				{
					if ($model && is_a($model, 'CoursesModelAssetgrouptype'))
					{
						$this->assetgrouptypes[$idx] = $model;
					}
					return $this->assetgrouptypes[$idx];
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
				foreach ($this->assetgrouptypes as $grouptype)
				{
					$obj = new stdClass;
					foreach ($idx as $property)
					{
						$property = strtolower(trim($property));
						if (isset($grouptype->$property))
						{
							$obj->$property = $grouptype->$property;
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
				foreach ($this->assetgrouptypes as $grouptype)
				{
					if (isset($grouptype->$idx))
					{
						$res[] = $grouptype->$idx;
					}
				}
				return $res;
			}
		}

		return $this->assetgrouptypes;
	}*/
}

