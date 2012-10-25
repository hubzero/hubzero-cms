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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.group.php');

/**
 * Courses model class for a course
 */
class CoursesModelAssetgroup extends JObject
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

		$this->asset = new CoursesTableAssetGroup($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->asset->load($oid);
		}
		else if (is_object($oid))
		{
			$this->asset->bind($oid);
		}
		else if (is_array($oid))
		{
			$this->asset->bind($oid);
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
			$inst = new CoursesModelUnit($oid);

			$instances[$oid] = $inst;
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
	 * Check if the resource exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function exists()
	{
		if ($this->asset->id && $this->asset->id > 0) 
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
			$this->_creator = JUser::getInstance($this->asset->created_by);
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
	public function asset($id=null)
	{
		if (!isset($this->asset) || $this->asset->id != $id)
		{
			$this->asset = null;

			foreach ($this->assets() as $key => $asset)
			{
				if ($asset->id == $id)
				{
					if (!is_a($asset, 'CoursesModelAsset'))
					{
						$asset = new CoursesModelAsset($asset);
						// Set the offering to the model
						$this->assets($key, $asset);
					}
					$this->asset = $asset;
					break;
				}
			}
		}
		return $this->asset;
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
	public function assets($idx=null, $model=null)
	{
		if (!$this->exists()) 
		{
			return array();
		}

		if (!isset($this->assets) || !is_array($this->assets))
		{
			$this->assets = array();

			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');

			$tbl = new CoursesTableAsset($this->_db);
			$results = $tbl->getCourseAssets(
				array(
					'w' => array(
						'course_asset_scope_id' => $this->group->id,
						'course_asset_scope' => 'asset_group'
					)
				)
			);
			if ($results)
			{
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php');

				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesModelAsset($result);
				}
				$this->assets = $results;
			}
		}

		if ($idx !== null)
		{
			if (is_numeric($idx))
			{
				if (isset($this->assets[$idx]))
				{
					if ($model && is_a($model, 'CoursesModelAssetgroup'))
					{
						$this->assets[$idx] = $model;
					}
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
		}

		return $this->assets;
	}
}

