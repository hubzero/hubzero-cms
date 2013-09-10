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
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'iterator.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section' . DS . 'date.php');

/**
 * Courses model class for an asset group
 */
class CoursesModelAssetgroup extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableAssetGroup';

	/**
	 * Object scope
	 * 
	 * @var string
	 */
	protected $_scope = 'asset_group';

	/**
	 * CoursesModelIterator
	 * 
	 * @var array
	 */
	private $_asset = null;

	/**
	 * CoursesModelAsset
	 * 
	 * @var array
	 */
	private $_assets = null;

	/**
	 * CoursesModelIterator
	 * 
	 * @var array
	 */
	public $children = null;

	/**
	 * CoursesModelAssetGroup
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
		parent::__construct($oid);

		$this->children = new CoursesModelIterator(array());
	}

	/**
	 * Returns a property of the params
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $default The default value
	 * @return	mixed The value of the property
 	 */
	public function params($key, $default=null)
	{
		if (!isset($this->_params))
		{
			$paramsClass = 'JParameter';
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$paramsClass = 'JRegistry';
			}

			$this->_params = new $paramsClass($this->get('params'));
		}
		return $this->_params->get($key, $default);
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
		else if (in_array($property, self::$_section_keys))
		{
			$tbl = new CoursesTableSectionDate($this->_db);
			$tbl->load($this->get('id'), 'asset_group', $this->get('section_id'));

			$this->set('publish_up', $tbl->get('publish_up'));
			$this->set('publish_down', $tbl->get('publish_down'));

			return $tbl->get($property, $default);
		}
		return $default;
	}

	/**
	 * Method to set the article id
	 *
	 * @param	int	Article ID number
	 */
	public function children($idx=null, $populate=false)
	{
		if ($populate)
		{
			$filters = array(
				'parent'     => $this->get('id'),
				'unit_id'    => $this->get('unit_id'),
				'section_id' => $this->get('section_id')
			);

			if (($results = $this->_tbl->find(array('w' => $filters))))
			{
				foreach ($results as $c)
				{
					$this->children->add(new CoursesModelAssetgroup($c));
				}
			}
		}

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
		if (!isset($this->_asset) 
		 || ($id !== null && (int) $this->_asset->get('id') != (int) $id))
		{
			$this->_asset = null;

			foreach ($this->assets() as $key => $asset)
			{
				if ((int) $asset->get('id') == (int) $id)
				{
					$this->_asset = $asset;
					break;
				}
			}
		}
		return $this->_asset;
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
		if (!isset($this->_assets) || !is_a($this->_assets, 'CoursesModelIterator'))
		{
			if (!isset($filters['asset_scope_id']))
			{
				$filters['asset_scope_id'] = (int) $this->get('id');
			}
			if (!isset($filters['asset_scope']))
			{
				$filters['asset_scope']    = 'asset_group';
			}
			if (!isset($filters['section_id']))
			{
				$filters['section_id']     = (int) $this->get('section_id');
			}

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

			$this->_assets = new CoursesModelIterator($results);
		}

		return $this->_assets;
	}

	/**
	 * Set siblings
	 *
	 * @return     void
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
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		$value = parent::store($check);

		if ($value && $this->get('section_id'))
		{
			$dt = new CoursesTableSectionDate($this->_db);
			$dt->load($this->get('id'), $this->_scope, $this->get('section_id'));
			$dt->set('publish_up', $this->get('publish_up'));
			$dt->set('publish_down', $this->get('publish_down'));
			if (!$dt->store())
			{
				$this->setError($dt->getError());
			}
		}

		if ($value)
		{
			JPluginHelper::importPlugin('courses');
			JDispatcher::getInstance()->trigger('onAssetgroupSave', array($this));
		}

		return $value;
	}

	/**
	 * Delete an entry and associated data
	 * 
	 * @return     boolean True on success, false on error
	 */
	public function delete()
	{
		// Remove all children
		foreach ($this->children() as $child)
		{
			if (!$child->delete())
			{
				$this->setError($child->getError());
			}
		}
		// Remove all assets
		foreach ($this->assets() as $asset)
		{
			if (!$asset->delete())
			{
				$this->setError($asset->getError());
			}
		}

		if ($this->get('section_id'))
		{
			$dt = new CoursesTableSectionDate($this->_db);
			$dt->load($this->get('id'), $this->_scope, $this->get('section_id'));
			if ($dt->id)
			{
				if (!$dt->delete())
				{
					$this->setError($dt->getError());
				}
			}
		}

		JPluginHelper::importPlugin('courses');
		JDispatcher::getInstance()->trigger('onAssetgroupDelete', array($this));

		// Remove this record from the database and log the event
		return parent::delete();
	}
}

