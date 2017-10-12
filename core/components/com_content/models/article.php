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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Content\Models;

use Components\Categories\Models\Category;
use Hubzero\Database\Relational;
use Hubzero\Database\Asset;
use Hubzero\Config\Registry;
use Component;
use Lang;
use User;
use Date;
use Hubzero\Form\Form;

require_once Component::path('com_categories') . '/models/category.php';

/**
 * Model class for a blog entry
 */
class Article extends Relational
{
	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'published_up';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title'    => 'notempty',
		'content'  => 'notempty',
		'scope'    => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'publish_up',
		'publish_down',
		'modified',
		'modified_by',
		'metadata',
		'attribs',
		'asset_id'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by',
		'ordering'
	);

	/**
	 * Rules array converted into JSON string.
	 *
	 * @var string
	 */
	public $assetRules;

	/**
	 * Registry
	 *
	 * @var  object
	 */
	public $params = null;

	/**
	 * Registry params object 
	 *
	 * @var  object
	 */
	protected $paramsRegistry = null;

	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'content';

	/**
	 * The table name
	 *
	 * @var  string
	 */
	protected $table = '#__content';

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('publish_down', function($data)
		{
			if (!$data['publish_down'] || $data['publish_down'] == '0000-00-00 00:00:00')
			{
				return false;
			}
			return $data['publish_down'] >= $data['publish_up'] ? false : Lang::txt('The entry cannot end before it begins');
		});
	}

	/**
	 * Generates automatic asset_id field value
	 *
	 * @return  integer
	 */
	public function automaticAssetId()
	{
		if (!empty($this->assetRules))
		{
			return parent::automaticAssetId();
		}
		return $this->get('asset_id');
	}

	/**
	 * Establish relationship to category
	 *
	 * @return  object
	 */
	public function category()
	{
		return $this->belongsToOne('Components\Categories\Models\Category', 'catid');
	}

	/**
	 * Establish relationship to author
	 *
	 * @return  object
	 */
	public function author()
	{
		return $this->belongsToOne('\Hubzero\User\User', 'created_by');
	}

	/**
	 * Establish relationship to editor
	 *
	 * @return  object
	 */
	public function editor()
	{
		return $this->belongsToOne('\Hubzero\User\User', 'checked_out');
	}

	/**
	 * Get a list of all categories
	 *
	 * @return  object
	 */
	public function categories()
	{
		$categories = Category::all()
			->whereEquals('extension', 'com_content')
			->whereIn('published', array(0, 1))
			->order('lft', 'asc');
		return $categories;
	}

	/**
	 * Display state as human readable text
	 *
	 * @return  string
	 */
	public function transformState()
	{
		$states = array(
			'0' => 'Unpublished',
			'1' => 'Published',
			'2' => 'Archived',
			'-2' => 'Trashed'
		);
		$stateNum = $this->get('state', 0);
		return $states[$stateNum];
	}

	/**
	 * Establish relationship to access level
	 *
	 * @return  object
	 */
	public function accessLevel()
	{
		return $this->belongsToOne('\Hubzero\Access\Viewlevel', 'access');
	}

	/**
	 * Establish relationship to asset
	 *
	 * @return  object
	 */
	public function asset()
	{
		return $this->belongsToOne('Hubzero\Access\Asset', 'asset_id');
	}

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticPublishUp($data)
	{
		if (!isset($data['publish_up']))
		{
			$data['publish_up'] = null;
		}

		$publish_up = $data['publish_up'];

		if (!$publish_up || $publish_up == '0000-00-00 00:00:00')
		{
			$publish_up = $this->isNew() ? Date::toSql() : $this->created;
		}

		return $publish_up;
	}

	/**
	 * Generates automatic publish_down field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticPublishDown($data)
	{
		if (!isset($data['publish_down']) || !$data['publish_down'])
		{
			$data['publish_down'] = '0000-00-00 00:00:00';
		}
		return $data['publish_down'];
	}

	/**
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		$data['modified'] = Date::of()->toSql();
		return $data['modified'];
	}

	/**
	 * Generates automatic modified by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModifiedBy($data)
	{
		$data['modified_by'] = User::getInstance()->get('id');
		return $data['modified_by'];
	}

	/**
	 * Generates automatic ordering field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticOrdering($data)
	{
		if (empty($data['ordering']) && !empty($data['catid']))
		{
			$lastOrderedRow = self::all()
				->whereEquals('catid', $data['catid'])
				->order('ordering', 'desc')
				->row();
			$lastOrderNum = $lastOrderedRow->get('ordering', 0);
			$data['ordering'] = $lastOrderNum + 1;
		}
		return $data['ordering'];
	}

	/**
	 * Get params as Registry object
	 *
	 * @return  object
	 */
	public function transformAttribs()
	{
		if (!($this->paramsRegistry instanceof Registry))
		{
			$itemRegistry = new Registry($this->get('attribs'));
			$componentRegistry = Component::params('com_content');
			$componentRegistry->merge($itemRegistry);
			$this->paramsRegistry = $componentRegistry;
		}
		return $this->paramsRegistry;
	}

	/**
	 * Transform title
	 *
	 * @return  string
	 */
	public function transformName()
	{
		return $this->get('title');
	}

	/**
	 * Get metadata as an object
	 *
	 * @return  object
	 */
	public function transformMetadata()
	{
		$metadata = $this->get('metadata');

		if (!empty($metadata) && !is_array($metadata))
		{
			return json_decode($metadata);
		}

		return new \stdclass;
	}

	/**
	 * Generates automatic attribs field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAttribs($data)
	{
		if (!empty($data['attribs']))
		{
			return json_encode($data['attribs']);
		}
		return '';
	}

	/**
	 * Generates automatic metadata field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticMetadata($data)
	{
		if (!empty($data['metadata']))
		{
			return json_encode($data['metadata']);
		}
		return '';
	}

	/**
	 * Build a Form object and bind data to it
	 *
	 * @return  object
	 */
	public function getForm()
	{
		$file = __DIR__ . '/forms/article.xml';
		$file = Filesystem::cleanPath($file);
		$form = new Form('content', array('control' => 'fields'));
		if (!$form->loadFile($file, false, '//form'))
		{
			$this->addError(Lang::txt('JERROR_LOADFILE_FAILED'));
		}
		$data = $this->getAttributes();
		$data['attribs']  = $this->attribs->toArray();
		$data['metadata'] = $this->metadata;
		if ($this->isNew())
		{
			unset($data['asset_id']);
		}
		$form->bind($data);
		return $form;
	}

	/**
	 * Save ordering
	 *
	 * @param   array  $ordering
	 * @return  bool
	 */
	public static function saveorder($ordering)
	{
		if (empty($ordering) || !is_array($ordering))
		{
			return false;
		}
		foreach ($ordering as $catid => $order)
		{
			$existingOrderedRows = self::all()
				->whereEquals('catid', $catid)
				->order('ordering', 'asc')
				->rows();
			if (count($existingOrderedRows) <= 1)
			{
				continue;
			}
			$existingOrderIds = array();
			foreach ($existingOrderedRows as $row)
			{
				$pkValue = $row->get('id');
				$existingOrderIds[$pkValue] = $row->ordering;
			}
			$newOrder = $order + $existingOrderIds;
			if ($newOrder != $existingOrderIds)
			{
				asort($newOrder);
				$iterator = 1;
				foreach ($newOrder as $pk => $orderValue)
				{
					$existingOrderedRows->seek($pk)->set('ordering', $iterator);
					$iterator++;
				}
				if (!$existingOrderedRows->save())
				{
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Move a single item up or down in ordering
	 *
	 * @param   integer  $delta
	 * @param   string   $where
	 * @return  bool
	 */
	public function move($delta, $where = '')
	{
		// If the change is none, do nothing.
		if (empty($delta))
		{
			return true;
		}

		// Select the primary key and ordering values from the table.
		$query = self::all()
			->whereEquals('catid', $this->get('catid'));

		// If the movement delta is negative move the row up.
		if ($delta < 0)
		{
			$query->where('ordering', '<', (int) $this->get('ordering'));
			$query->order('ordering', 'desc');
		}
		// If the movement delta is positive move the row down.
		elseif ($delta > 0)
		{
			$query->where('ordering', '>', (int) $this->get('ordering'));
			$query->order('ordering', 'asc');
		}

		// Add the custom WHERE clause if set.
		if ($where)
		{
			$query->whereRaw($where);
		}

		// Select the first row with the criteria.
		$row = $query->row();

		// If a row is found, move the item.
		if ($row->get($this->pk))
		{
			$prev = $this->get('ordering');

			// Update the ordering field for this instance to the row's ordering value.
			$this->set('ordering', (int) $row->get('ordering'));

			// Check for a database error.
			if (!$this->save())
			{
				return false;
			}

			// Update the ordering field for the row to this instance's ordering value.
			$row->set('ordering', (int) $prev);

			// Check for a database error.
			if (!$row->save())
			{
				return false;
			}
		}
		else
		{
			// Update the ordering field for this instance.
			$this->set('ordering', (int) $this->get('ordering'));

			// Check for a database error.
			if (!$this->save())
			{
				return false;
			}
		}

		return true;
	}
}
