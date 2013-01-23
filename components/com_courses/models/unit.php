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
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'assetgroup.php');

/**
 * Courses model class for a unit
 */
class CoursesModelUnit extends JObject
{
	/**
	 * CoursesTableUnit
	 * 
	 * @var object
	 */
	private $_tbl = NULL;

	/**
	 * CoursesModelAssetgroup
	 * 
	 * @var object
	 */
	public $group = NULL;

	/**
	 * CoursesTableIterator
	 * 
	 * @var object
	 */
	public $assetgroups = NULL;

	/**
	 * JUser
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
	private $_siblings = NULL;

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

		$this->_tbl = new CoursesTableUnit($this->_db);

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
		if (isset($this->_tbl->$property)) 
		{
			return $this->_tbl->$property;
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
							$this->group->siblings($group->children());
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
		if (!isset($filters['unit_id']))
		{
			$filters['unit_id'] = (int) $this->get('id');
		}

		if (!isset($this->assetgroups) || !is_a($this->assetgroups, 'CoursesModelIterator'))
		{
			$tbl = new CoursesTableAssetGroup($this->_db);
			if (($results = $tbl->find(array('w' => $filters))))
			{
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
				return $this->assetgroups->fetch($idx);
			}
			else if (is_string($idx))
			{
				$idx = strtolower(trim($idx));

				foreach ($this->assetgroups as $group)
				{
					if ($group->get('alias') == $idx)
					{
						return $this->assetgroups;
						break;
					}
					else
					{
						foreach ($group->children() as $child)
						{
							if ($child->get('alias') == $idx)
							{
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
	 * Set seblings
	 *
	 * @return     boolean
	 */
	public function siblings(&$siblings) 
	{
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
	public function key() 
	{
		return $this->_siblings->key();
	}

	/**
	 * Set cursor position to previous position and return array value
	 *
	 * @return     mixed
	 */
	public function sibling($dir='next') 
	{
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
	 * Set cursor position to next position and return array value
	 *
	 * @return     mixed
	 */
	/*public function next() 
	{
		return $this->_siblings->fetch('next');
	}*/

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
			$filters['asset_scope_id'] = (int) $this->get('id');
			$filters['asset_scope']    = 'unit';

			$tbl = new CoursesTableAsset($this->_db);

			if (($results = $tbl->find(array('w' => $filters))))
			{
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

		return $this->assets;
	}

	/**
	 * Bind data to the this model
	 * 
	 * @param      mixed $data Data to bind (array or object)
	 * @return     boolean
	 */
	public function bind($data=null)
	{
		return $this->_tbl->bind($data);
	}

	/**
	 * Save data to the database
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if errors, True on success
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

