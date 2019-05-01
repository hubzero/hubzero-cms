<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Categories\Models;

use Hubzero\Database\Nested;
use Hubzero\Database\Rows;
use Hubzero\Config\Registry;
use Hubzero\Form\Form;
use Component;
use Lang;
use User;
use Date;

/**
 * Model class for a category
 */
class Category extends Nested
{
	/**
	 * Default order by for model
	 *
	 * @var  string
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
		'params',
		'metadata',
		'modified_user_id',
		'asset_id',
		'modified_time'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created_time',
		'created_user_id'
	);

	/**
	 * Asset rules
	 *
	 * @var  object
	 */
	public $assetRules;

	/**
	 * Set the namespace
	 *
	 * @param   string  $name
	 * @return  void
	 */
	public function setNameSpace($name)
	{
		if (!empty($name))
		{
			$underscorePos = strpos($name, '_');
			if ($underscorePos !== false)
			{
				$name = substr($name, $underscorePos + 1);
			}

			$this->namespace = $name;
		}
	}

	/**
	 * Generate asset ID
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
	 * Generates automatic created time field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticCreatedTime($data)
	{
		if (!isset($data['created_time']) || !$data['created_time'])
		{
			$data['created_time'] = Date::toSql();
		}

		return $data['created_time'];
	}

	/**
	 * Generates userId of person logged in if no user ID provided upon creation.
	 *
	 * @param   array   $data  the data being saved
	 * @return  integer
	 */
	public function automaticCreatedUserId($data)
	{
		if (empty($data['created_user_id']))
		{
			$data['created_user_id'] = User::get('id');
		}

		return $data['created_user_id'];
	}

	/**
	 * Generates userId of person logged in if no user ID provided upon creation.
	 *
	 * @param   array   $data  the data being saved
	 * @return  integer
	 */
	public function automaticModifiedUserId($data)
	{
		if (empty($data['modified_user_id']))
		{
			return User::get('id');
		}

		return $data['modified_user_id'];
	}

	/**
	 * Generates userId of person logged in if no user ID provided upon creation.
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModifiedTime()
	{
		if (isset($data['id']) && $data['id'])
		{
			return Date::of('now')->toSql();
		}
		return null;
	}

	/**
	 * Get title
	 *
	 * @return  string
	 */
	public function transformName()
	{
		return $this->get('title');
	}

	/**
	 * Get params as Registry object
	 *
	 * @return  object
	 */
	public function transformParams()
	{
		if (!($this->paramsRegistry instanceof Registry))
		{
			$this->paramsRegistry = new Registry($this->get('params'));
		}
		return $this->paramsRegistry;
	}

	/**
	 * Make sure params are a string
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticParams($data)
	{
		if (!empty($data['params']) && !is_string($data['params']))
		{
			if (!($data['params'] instanceof Registry))
			{
				$data['params'] = new Registry($data['params']);
			}
			$data['params'] = $data['params']->toString();
		}
		return $data['params'];
	}

	/**
	 * Get metadata as Registry object
	 *
	 * @return  object
	 */
	public function transformMetadata()
	{
		if (!($this->metadataRegistry instanceof Registry))
		{
			$this->metadataRegistry = new Registry($this->get('metadata'));
		}
		return $this->metadataRegistry;
	}

	/**
	 * Get the published state as a text string
	 *
	 * @return  object
	 */
	public function transformPublished()
	{
		$states = array(
			'0' => 'Unpublished',
			'1' => 'Published',
			'2' => 'Archived',
			'-2' => 'Trashed'
		);
		$stateNum = $this->get('published', 0);
		return $states[$stateNum];
	}

	/**
	 * Ensure metadata is a string
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticMetadata($data)
	{
		if (!empty($data['metadata']) && !is_string($data['metadata']))
		{
			if (!($data['metadata'] instanceof Registry))
			{
				$data['metadata'] = new Registry($data['metadata']);
			}
			$data['metadata'] = $data['metadata']->toString();
		}
		return $data['metadata'];
	}

	/**
	 * Generate a Form object and bind data to it
	 *
	 * @return  object
	 */
	public function getForm()
	{
		$file = __DIR__ . '/forms/category.xml';
		$file = Filesystem::cleanPath($file);

		Form::addFieldPath(__DIR__ . '/fields');

		$form = new Form('categories', array('control' => 'fields'));

		if (!$form->loadFile($file, false, '//form'))
		{
			$this->addError(Lang::txt('JERROR_LOADFILE_FAILED'));
		}

		$data = $this->getAttributes();
		$data['params'] = $this->params->toArray();
		$data['metadata'] = $this->metadata->toArray();

		$form->bind($data);

		return $form;
	}

	/**
	 * Establish relationship of user to checked_out
	 *
	 * @return  object
	 */
	public function editor()
	{
		return $this->belongsToOne('\Hubzero\User\User', 'checked_out');
	}

	/**
	 * Save the ordering for multiple entries
	 *
	 * @param   array    $ordering
	 * @param   string   $extension
	 * @return  boolean
	 */
	public static function saveorder($ordering, $extension)
	{
		if (empty($ordering) || !is_array($ordering))
		{
			return false;
		}

		$storage = null;

		foreach ($ordering as $parentid => $order)
		{
			$existingOrderedRows = self::all()
				->whereEquals('parent_id', $parentid)
				->whereEquals('extension', $extension)
				->order('lft', 'asc')
				->rows();

			if (count($existingOrderedRows) <= 1)
			{
				continue;
			}

			$existingLftIds = array();
			foreach ($existingOrderedRows as $row)
			{
				$pkValue = $row->get('id');
				$existingLftIds[$pkValue] = $row->lft;
			}

			asort($order);

			if (array_keys($order) !== array_keys($existingLftIds))
			{
				$startLft = array_shift($existingLftIds);
				foreach (array_keys($order) as $pk)
				{
					$row = $existingOrderedRows->seek($pk);
					$storage = $row->updatePositionWithChildren($startLft, $storage);
					$startLft = $storage->last()->get('rgt') + 1;
				}
			}
		}

		if ($storage && !$storage->save())
		{
			return false;
		}
		return true;
	}

	/**
	 * Move an entry up or down in th ordering
	 *
	 * @param   itneger  $delta
	 * @param   string   $extension
	 * @param   string   $where
	 * @return  boolean
	 */
	public function move($delta, $extension, $where = '')
	{
		// If the change is none, do nothing.
		if (empty($delta))
		{
			return true;
		}

		// Select the primary key and ordering values from the table.
		$query = self::all()
			->whereEquals('parent_id', $this->get('parent_id'))
			->whereEquals('extension', $extension);

		// If the movement delta is negative move the row up.
		if ($delta < 0)
		{
			$query->where('lft', '<', (int) $this->get('lft'));
			$query->order('lft', 'desc');
		}
		// If the movement delta is positive move the row down.
		elseif ($delta > 0)
		{
			$query->where('lft', '>', (int) $this->get('lft'));
			$query->order('lft', 'asc');
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
			if ($delta < 0)
			{
				$thisStart = $row->get('lft');
				$storage = $this->updatePositionWithChildren($thisStart);
				$rowStart = $storage->last()->get('rgt') + 1;
				$row->updatePositionWithChildren($rowStart, $storage);
			}
			else if ($delta > 0)
			{
				$rowStart = $this->get('lft');
				$storage = $row->updatePositionWithChildren($rowStart);
				$thisStart = $storage->last()->get('rgt') + 1;
				$this->updatePositionWithChildren($thisStart, $storage);
			}
			if (!$storage->save())
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * More or copy an entry with children
	 *
	 * @param   integer  $parentId
	 * @param   string   $method
	 * @param   array    $params
	 * @return  boolean
	 */
	public function moveOrCopyWithChildren($parentId, $method = 'c', $params = array())
	{
		$children = $this->getChildren();

		if ($method == 'c')
		{
			$this->removeAttribute('id');
		}

		foreach ($params as $index => $value)
		{
			if (!empty($value))
			{
				$this->set($index, $value);
			}
		}

		if ($this->saveAsChildOf($parentId))
		{
			foreach ($children as $child)
			{
				$child->moveOrCopyWithChildren($this->get('id'), $method, $params);
			}
		}
		else
		{
			return false;
		}

		return true;
	}

	/**
	 * Update position of an entry
	 *
	 * @param   integer  $iterator
	 * @param   object   $storage
	 * @return  object
	 */
	public function updatePositionWithChildren($iterator, $storage = null)
	{
		if (!($storage instanceof Rows))
		{
			$storage = new Rows();
		}
		$children = $this->getChildren();

		$this->set('lft', $iterator);

		if ($children->count() < 1)
		{
			$iterator++;

			$this->set('rgt', $iterator);

			$storage->push($this);
		}
		else
		{
			foreach ($children as $child)
			{
				$iterator++;
				$storage  = $child->updatePositionWithChildren($iterator, $storage);
				$iterator = $storage->last()->get('rgt');
			}
			$iterator++;

			$this->set('rgt', $iterator);

			$storage->push($this);
		}

		return $storage;
	}

	/**
	 * Retrieve parents
	 *
	 * @return  object
	 */
	public function parents()
	{
		$parents = self::all()
			->whereEquals('extension', $this->get('extension'))
			->where('parent_id', '!=', $this->get('id'))
			->order('lft', 'asc');

		return $parents;
	}

	/**
	 * Get the title prefixed based on the level of nesting
	 *
	 * @return  string
	 */
	public function nestedTitle()
	{
		$nestedPad = str_repeat('- ', $this->get('level', 1));
		$title = $nestedPad . $this->get('title');
		return $title;
	}
}
