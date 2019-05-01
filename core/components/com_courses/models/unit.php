<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Components\Courses\Tables;
use Date;
use User;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'unit.php');
require_once(__DIR__ . DS . 'base.php');
require_once(__DIR__ . DS . 'iterator.php');
require_once(__DIR__ . DS . 'asset.php');
require_once(__DIR__ . DS . 'assetgroup.php');
require_once(__DIR__ . DS . 'section' . DS . 'date.php');

/**
 * Courses model class for a unit
 */
class Unit extends Base
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\Unit';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'unit';

	/**
	 * \Components\Courses\Models\Assetgroup
	 *
	 * @var object
	 */
	public $group = null;

	/**
	 * \Components\Courses\Models\Iterator
	 *
	 * @var object
	 */
	public $assetgroups = null;

	/**
	 * \Components\Courses\Models\Asset
	 *
	 * @var object
	 */
	private $_asset = null;

	/**
	 * \Components\Courses\Models\Iterator
	 *
	 * @var object
	 */
	private $_assets = null;

	/**
	 * \Components\Courses\Models\Iterator
	 *
	 * @var object
	 */
	private $_siblings = null;

	/**
	 * Constructor
	 *
	 * @param   mixed   $oid         Integer, string, array, or object
	 * @param   integer $offering_id Offering the unit is linked to
	 * @return  void
	 */
	public function __construct($oid=null, $offering_id=null)
	{
		$this->_db = \App::get('db');

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
	 * @return  object  \Components\Courses\Models\Unit
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
			$tbl = new Tables\SectionDate($this->_db);
			$tbl->load($this->get('id'), 'unit', $this->get('section_id'));

			if (!$this->_tbl->get('__publish_up', null))
			{
				$this->set('publish_up', $tbl->get('publish_up', ''));
			}
			if (!$this->_tbl->get('__publish_down', null))
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

		if (!isset($this->assetgroups) || !($this->assetgroups instanceof Iterator))
		{
			$tbl = new Tables\AssetGroup($this->_db);
			if (($results = $tbl->find(array('w' => $filters))))
			{
				$list = array();

				// First pass - collect children
				foreach ($results as $v)
				{
					if ($v->parent == 0)
					{
						$list[$v->id] = new Assetgroup($v);

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
						$ag = new Assetgroup($c);

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
			$this->assetgroups = new Iterator($res);
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
		if ($siblings instanceof Iterator)
		{
			$this->_siblings = $siblings;
		}
		else
		{
			$this->_siblings = new Iterator($siblings);
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
	 * @return  object  \Components\Courses\Models\Asset
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
	 * @return  object \Components\Courses\Models\Iterator
	 */
	public function assets($filters=array())
	{
		if (!isset($this->_assets) || !($this->_assets instanceof Iterator))
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

			$tbl = new Tables\Asset($this->_db);

			if (($results = $tbl->find(array('w' => $filters))))
			{
				foreach ($results as $key => $result)
				{
					$results[$key] = new Asset($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_assets = new Iterator($results);
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
			$dt = new Tables\SectionDate($this->_db);
			$dt->load($this->get('id'), $this->_scope, $this->get('section_id'));

			$dt->set('publish_up', $this->get('publish_up'));
			$dt->set('publish_down', $this->get('publish_down'));

			if (!$dt->get('id'))
			{
				$dt->set('section_id', $this->get('section_id'));
				$dt->set('scope', $this->_scope);
				$dt->set('scope_id', $this->get('id'));
				$dt->set('created', Date::toSql());
				$dt->set('created_by', User::get('id'));
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
			$dt = new Tables\SectionDate($this->_db);
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
			$tbl = new Tables\AssetAssociation($this->_db);
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
