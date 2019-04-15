<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Components\Courses\Tables;
use Hubzero\Config\Registry;
use Lang;


require_once(dirname(__DIR__) . DS . 'tables' . DS . 'asset.group.php');
require_once(__DIR__ . DS . 'base.php');
require_once(__DIR__ . DS . 'asset.php');
require_once(__DIR__ . DS . 'iterator.php');
require_once(__DIR__ . DS . 'section' . DS . 'date.php');

/**
 * Courses model class for an asset group
 */
class Assetgroup extends Base
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\AssetGroup';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'asset_group';

	/**
	 * Curent asset
	 *
	 * @var array
	 */
	private $_asset = null;

	/**
	 * \Components\Courses\Models\Asset
	 *
	 * @var array
	 */
	private $_assets = null;

	/**
	 * \Components\Courses\Models\Iterator
	 *
	 * @var array
	 */
	public $children = null;

	/**
	 * \Components\Courses\Models\AssetGroup
	 *
	 * @var object
	 */
	private $_parent = null;

	/**
	 * Container for properties
	 *
	 * @var array
	 */
	public $_siblings = null;

	/**
	 * Registry
	 *
	 * @var object
	 */
	private $_params = null;

	/**
	 * Constructor
	 *
	 * @param   mixed $oid Integer, array, or object
	 * @return  void
	 */
	public function __construct($oid)
	{
		parent::__construct($oid);

		$this->children = new Iterator(array());
	}

	/**
	 * Returns a property of the params
	 *
	 * @param   string $property The name of the property
	 * @param   mixed  $default  The default value
	 * @return  mixed  The value of the property
  */
	public function params($key, $default=null)
	{
		if (!($this->_params instanceof Registry))
		{
			$this->_params = new Registry($this->get('params'));
		}
		return $this->_params->get($key, $default);
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string $property The name of the property
	 * @param   mixed  $default  The default value
	 * @return  mixed  The value of the property
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
			$tbl->load($this->get('id'), 'asset_group', $this->get('section_id'));

			$this->set('publish_up', $tbl->get('publish_up', ''));
			$this->set('publish_down', $tbl->get('publish_down', ''));

			return $tbl->get($property, $default);
		}
		return $default;
	}

	/**
	 * Get a list of child asset groups
	 *
	 * @param   mixed   $idx
	 * @param   boolean $populate
	 * @param   array   $filters
	 * @return  array
	 */
	public function children($idx=null, $populate=false, $filters=array())
	{
		if ($populate)
		{
			if (!isset($filters['parent']))
			{
				$filters['parent'] = $this->get('id');
			}
			if (!isset($filters['unit_id']))
			{
				$filters['unit_id'] = $this->get('unit_id');
			}
			if (!isset($filters['section_id']))
			{
				$filters['section_id'] = $this->get('section_id');
			}

			if (($results = $this->_tbl->find(array('w' => $filters))))
			{
				foreach ($results as $c)
				{
					$this->children->add(new Assetgroup($c));
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
					$this->setError(Lang::txt('Index not found: ') . __CLASS__ . '::' . __METHOD__ . '[' . $idx . ']');
					return false;
				}
			}
			else if (is_array($idx))
			{
				$found = false;
				$res = array();
				foreach ($this->children as $child)
				{
					$obj = new \stdClass;
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
	 * Get a list of assets
	 *   Accepts an array of filters to apply to the list of assets
	 *
	 * @param   array  $filters Filters to apply
	 * @return  object \Components\Courses\Models\Iterator
	 */
	public function assets($filters=array())
	{
		if (!($this->_assets instanceof Iterator))
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

			$tbl = new Tables\Asset($this->_db);

			if (($results = $tbl->find(array('w' => $filters))))
			{
				foreach ($results as $key => $result)
				{
					$results[$key] = new Asset($result);

					if ($this->get('section_id'))
					{
						$results[$key]->set('section_id', $this->get('section_id'));
					}
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
	 * Set siblings
	 *
	 * @param   mixed $siblings Array or Iterator object
	 * @return  void
	 */
	public function siblings(&$siblings)
	{
		if (!($siblings instanceof Iterator))
		{
			$siblings = new Iterator($siblings);
		}
		$this->_siblings = $siblings;
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
	 * @param   integer $idx
	 * @return  mixed
	 */
	public function key($idx=null)
	{
		return $this->_siblings->key($idx);
	}

	/**
	 * Set cursor position to previous position and return array value
	 *
	 * @param   string $dir
	 * @return  mixed
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
			if (!$dt->store())
			{
				$this->setError($dt->getError());
			}
		}

		if ($value)
		{
			$this->importPlugin('courses')
			     ->trigger('onAssetgroupSave', array($this));
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
			$dt = new Tables\SectionDate($this->_db);
			$dt->load($this->get('id'), $this->_scope, $this->get('section_id'));
			if ($dt->id)
			{
				if (!$dt->delete())
				{
					$this->setError($dt->getError());
				}
			}
		}

		$this->importPlugin('courses')
		     ->trigger('onAssetgroupDelete', array($this));

		// Remove this record from the database and log the event
		return parent::delete();
	}

	/**
	 * Copy an entry and associated data
	 *
	 * @param   integer $unit_id New unit to copy to
	 * @param   boolean $deep    Copy associated data?
	 * @return  boolean True on success, false on error
	 */
	public function copy($unit_id=null, $deep=true)
	{
		// Keep a copy of the original asset group for later
		$oldAssetGroupId     = $this->get('id');
		$oldAssetGroupAssets = $this->assets();

		// Reset the ID. This will force store() to create a new record.
		$this->set('id', 0);
		// Are we copying to a new unit?
		if ($unit_id)
		{
			$this->set('unit_id', $unit_id);
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
			// Copy assets (grab the assets from the original asset group)
			if ($oldAssetGroupAssets)
			{
				foreach ($oldAssetGroupAssets as $asset)
				{
					$oldAssetId = $asset->get('id');
					if (!$asset->copy())
					{
						$this->setError($asset->getError());
					}
					else
					{
						// Copy asset associations
						$tbl = new Tables\AssetAssociation($this->_db);
						foreach ($tbl->find(array('scope_id' => $oldAssetGroupId, 'scope' => 'asset_group', 'asset_id' => $oldAssetId)) as $aa)
						{
							$tbl->bind($aa);
							$tbl->id = 0;
							$tbl->scope_id = $this->get('id');
							$tbl->asset_id = $asset->get('id');
							if (!$tbl->store())
							{
								$this->setError($tbl->getError());
							}
						}
					}
				}
			}

			// Copy asset groups (child asset groups)
			if ($children = $this->_tbl->find(array('w' => array('parent' => $oldAssetGroupId))))
			{
				$found = array();

				foreach ($children as $c)
				{
					if (in_array($c->id, $found))
					{
						continue;
					}
					$assetgroup = new Assetgroup($c);
					$assetgroup->set('parent', $this->get('id'));

					$found[] = $c->id;

					if (!$assetgroup->copy($unit_id, $deep))
					{
						$this->setError($assetgroup->getError());
					}
				}
			}
		}

		return true;
	}
}
