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
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'iterator.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'assetgroup.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section' . DS . 'date.php');

/**
 * Courses model class for a unit
 */
class CoursesModelUnit extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableUnit';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'unit';

	/**
	 * CoursesModelAssetgroup
	 *
	 * @var object
	 */
	public $group = NULL;

	/**
	 * CoursesModelIterator
	 *
	 * @var object
	 */
	public $assetgroups = NULL;

	/**
	 * CoursesModelAsset
	 *
	 * @var object
	 */
	private $_asset = NULL;

	/**
	 * CoursesModelIterator
	 *
	 * @var object
	 */
	private $_assets = NULL;

	/**
	 * CoursesModelIterator
	 *
	 * @var object
	 */
	private $_siblings = NULL;

	/**
	 * Constructor
	 *
	 * @param   mixed   $oid         Integer, string, array, or object
	 * @param   integer $offering_id Offering the unit is linked to
	 * @return  void
	 */
	public function __construct($oid=null, $offering_id=null)
	{
		$this->_db = JFactory::getDBO();

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (is_numeric($oid) || is_string($oid))
			{
				if ($oid)
				{
					$this->_tbl->load($oid, $offering_id);
				}
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
	}

	/**
	 * Returns a reference to a unit model
	 *
	 * @param   mixed   $oid         Integer, string, array, or object
	 * @param   integer $offering_id Offering the unit is linked to
	 * @return  object  CoursesModelUnit
	 */
	static function &getInstance($oid=null, $offering_id=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid . '_' . $offering_id]))
		{
			$instances[$oid . '_' . $offering_id] = new self($oid, $offering_id);
		}

		return $instances[$oid . '_' . $offering_id];
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param     string $property The name of the property
	 * @param     mixed  $default  The default value
	 * @return    mixed  The value of the property
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
			$tbl->load($this->get('id'), 'unit', $this->get('section_id'));

			if (!$this->_tbl->{'__publish_up'})
			{
				$this->set('publish_up', $tbl->get('publish_up', ''));
			}
			if (!$this->_tbl->{'__publish_down'})
			{
				$this->set('publish_down', $tbl->get('publish_down', ''));
			}

			return $this->_tbl->{'__' . $property};
		}
		return $default;
	}

	/**
	 * Get a specific asset group
	 *
	 * @param   integer $id Asset group ID
	 * @return  object
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
							$children = $group->children();
							$this->group->siblings($children);
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
	 * @param   mixed $idx     Index value
	 * @param   array $filters Filters to apply to results query
	 * @return  void
	 */
	public function resetAssetgroups($idx=null, $filters=array())
	{
		$this->assetgroups = $this->_assetgroups;
		return $this->assetgroups($idx, $filters);
	}

	/**
	 * Get a list of assetgroups for this entry
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 *
	 * @param   mixed $idx     Index value
	 * @param   array $filters Filters to apply to results query
	 * @return  array
	 */
	public function assetgroups($idx=null, $filters=array())
	{
		if (!isset($filters['unit_id']))
		{
			$filters['unit_id'] = (int) $this->get('id');
		}
		if (!isset($filters['section_id']))
		{
			$filters['section_id'] = (int) $this->get('section_id');
		}

		if (!isset($this->assetgroups) || !($this->assetgroups instanceof CoursesModelIterator))
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

						if ($this->get('section_id'))
						{
							$list[$v->id]->set('section_id', $this->get('section_id'));
						}
					}
				}
				foreach ($results as $c)
				{
					if (isset($list[$c->parent]))
					{
						$ag = new CoursesModelAssetgroup($c);

						if ($this->get('section_id'))
						{
							$ag->set('section_id', $this->get('section_id'));
						}

						$list[$c->parent]->children->add($ag);
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
	 * @param   mixed $siblings Array or Iterator object
	 * @return  void
	 */
	public function siblings(&$siblings)
	{
		if ($siblings instanceof CoursesModelIterator)
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
	 * @return  boolean
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
	 * @return  boolean
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
	 * @return  integer
	 */
	public function key()
	{
		return $this->_siblings->key();
	}

	/**
	 * Set cursor position to previous position and return array value
	 *
	 * @param   string $dir
	 * @return  mixed
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
	 * Get a specific asset
	 *
	 * @param   integer $id Asset ID
	 * @return  object  CoursesModelAsset
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
	 * @param   array $filters Filters to apply
	 * @return  object CoursesModelIterator
	 */
	public function assets($filters=array())
	{
		if (!isset($this->_assets) || !($this->_assets instanceof CoursesModelIterator))
		{
			if (!isset($filters['asset_scope_id']))
			{
				$filters['asset_scope_id'] = (int) $this->get('id');
			}
			if (!isset($filters['asset_scope']))
			{
				$filters['asset_scope']    = 'unit';
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
	 * Store changes to this entry
	 *
	 * @param   boolean $check Perform data validation check?
	 * @return  boolean False if error, True on success
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

			if (!$dt->get('id'))
			{
				$dt->set('section_id', $this->get('section_id'));
				$dt->set('scope', $this->_scope);
				$dt->set('scope_id', $this->get('id'));
				$dt->set('created', JFactory::getDate()->toSql());
				$dt->set('created_by', JFactory::getUser()->get('id'));
			}

			if (!$dt->store())
			{
				$this->setError($dt->getError());
			}
		}

		if ($value)
		{
			$this->importPlugin('courses')
			     ->trigger('onUnitSave', array($this));
		}

		return $value;
	}

	/**
	 * Delete an entry and associated data
	 *
	 * @return  boolean True on success, false on error
	 */
	public function delete()
	{
		// Remove all children
		foreach ($this->assetgroups() as $group)
		{
			if (!$group->delete())
			{
				$this->setError($group->getError());
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

		// Remove dates
		if ($this->get('section_id'))
		{
			$dt = new CoursesTableSectionDate($this->_db);
			$dt->load($this->get('id'), $this->_scope, $this->get('section_id'));
			if (!$dt->delete())
			{
				$this->setError($dt->getError());
			}
		}

		$this->importPlugin('courses')
		     ->trigger('onUnitDelete', array($this));

		// Remove this record from the database and log the event
		return parent::delete();
	}

	/**
	 * Copy an entry and associated data
	 *
	 * @param   integer $offering_id New offering to copy to
	 * @param   boolean $deep        Copy associated data?
	 * @return  boolean True on success, false on error
	 */
	public function copy($offering_id=null, $deep=true)
	{
		// Get some old info we may need
		//  - Unit ID
		//  - Offering ID
		$u_id = $this->get('id');
		$o_id = $this->get('offering_id');

		// Reset the ID. This will force store() to create a new record.
		$this->set('id', 0);
		// Are we copying to a new offering?
		if ($offering_id)
		{
			$this->set('offering_id', $offering_id);
		}
		else
		{
			// Copying to the same offering so we want to distinguish
			// this unit from the one we copied from
			$this->set('title', $this->get('title') . ' (copy)');
			$this->set('alias', $this->get('alias') . '_copy');
		}
		if (!$this->store())
		{
			return false;
		}

		if ($deep)
		{
			// Copy assets
			$tbl = new CoursesTableAssetAssociation($this->_db);
			//foreach ($this->assets(array('asset_scope_id' => $u_id)) as $asset)
			foreach ($tbl->find(array('scope_id' => $u_id, 'scope' => 'unit')) as $asset)
			{
				$tbl->bind($asset);
				$tbl->id = 0;
				$tbl->scope_id = $this->get('id');
				//if (!$asset->copy($this->get('id')))
				if (!$tbl->store())
				{
					$this->setError($tbl->getError());
				}
			}

			// Copy asset groups
			foreach ($this->assetgroups(null, array('unit_id' => $u_id, 'parent' => 0)) as $assetgroup)
			{
				if (!$assetgroup->copy($this->get('id'), $deep))
				{
					$this->setError($assetgroup->getError());
				}
			}
		}

		return true;
	}
}

