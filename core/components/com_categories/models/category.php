<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
		'modified_time',
		'path'
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
	public function automaticModifiedTime($data)
	{
		if (isset($data['id']) && $data['id'])
		{
			return Date::of('now')->toSql();
		}

		$columns = $this->getStructure()->getTableColumns($this->getTableName(), false);

		foreach ($columns as $column)
		{
			// We want to get the default values from the
			// table's schema, rather than assuming
			if ($column['name'] == 'modified_time')
			{
				return $column['default'];
			}
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
	 * Generates automatic alias field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : $data['title']);
		$alias = strip_tags($alias);
		$alias = trim($alias);

		// Remove any '-' from the string since they will be used as concatenaters
		$alias = str_replace('-', ' ', $alias);
		$alias = \Lang::transliterate($alias);

		// Trim white spaces at beginning and end of alias and make lowercase
		$alias = strtolower($alias);
		$alias = trim($alias);

		// Remove any duplicate whitespace, and ensure all characters are alphanumeric
		$alias = preg_replace('/(\s|[^A-Za-z0-9\-])+/', '-', $alias);

		// Trim dashes at beginning and end of alias
		$alias = trim($alias, '-');

		if (trim(str_replace('-', '', $alias)) == '')
		{
			$alias = Date::of('now')->format('Y-m-d-H-i-s');
		}

		return $alias;
	}

	/**
	 * Create path
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticPath($data)
	{
		$alias = $this->automaticAlias($data);

		$path  = $this->parent->get('path');
		$path  = $path ? $path . '/' : '';
		$path .= $alias;

		return $path;
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
	 * Get parent
	 *
	 * @return  object
	 */
	public function parent()
	{
		return $this->belongsToOne('Category', 'parent_id');
	}

	/**
	 * Method to rebuild the node's path field from the alias values of the
	 * nodes from the current node to the root node of the tree.
	 *
	 * @return  boolean  True on success.
	 */
	public function rebuildPath()
	{
		// Get the aliases for the path from the node to the root node.
		$db = App::get('db');
		$path = $this->parent->get('path');
		$segments = explode('/', $path);

		// Make sure to remove the root path if it exists in the list.
		if ($segments[0] == 'root')
		{
			array_shift($segments);
		}
		$segments[] = $this->get('alias');

		// Build the path.
		$path = trim(implode('/', $segments), ' /\\');

		// Update the path field for the node.
		$query = $db->getQuery()
			->update($this->getTableName())
			->set(array(
				'path' => $path
			))
			->whereEquals('id', (int) $this->get('id'));
		$db->setQuery($query->toString());

		// Check for a database error.
		if (!$db->execute())
		{
			$this->addError(Lang::txt('JLIB_DATABASE_ERROR_REBUILDPATH_FAILED', get_class($this), $db->getErrorMsg()));
			return false;
		}

		// Update the current record's path to the new one:
		$this->set('path', $path);

		return true;
	}

	/**
	 * Method to recursively rebuild the whole nested set tree.
	 *
	 * @param   integer  $parentId  The root of the tree to rebuild.
	 * @param   integer  $leftId    The left id to start with in building the tree.
	 * @param   integer  $level     The level to assign to the current nodes.
	 * @param   string   $path      The path to the current nodes.
	 * @return  integer  1 + value of root rgt on success, false on failure
	 */
	public function rebuild($parentId, $leftId = 0, $level = 0, $path = '')
	{
		$query = $this->getQuery()
			->select('id')
			->select('alias')
			->from($this->getTableName())
			->whereEquals('parent_id', (int) $parentId)
			->order('parent_id', 'asc')
			->order('lft', 'asc');

		// Assemble the query to find all children of this node.
		$db = \App::get('db');
		$db->setQuery($query->toString());
		$children = $db->loadObjectList();

		// The right value of this node is the left value + 1
		$rightId = $leftId + 1;

		// execute this function recursively over all children
		foreach ($children as $node)
		{
			// $rightId is the current right value, which is incremented on recursion return.
			// Increment the level for the children.
			// Add this item's alias to the path (but avoid a leading /)
			$rightId = $this->rebuild($node->id, $rightId, $level + 1, $path . (empty($path) ? '' : '/') . $node->alias);

			// If there is an update failure, return false to break out of the recursion.
			if ($rightId === false)
			{
				return false;
			}
		}

		// We've got the left value, and now that we've processed
		// the children of this node we also know the right value.
		$query = $this->getQuery()
			->update($this->getTableName())
			->set(array(
				'lft'   => (int) $leftId,
				'rgt'   => (int) $rightId,
				'level' => (int) $level,
				'path'  => $path
			))
			->whereEquals('id', (int) $parentId);

		// If there is an update failure, return false to break out of the recursion.
		if (!$query->execute())
		{
			return false;
		}

		// Return the right value of this node + 1.
		return $rightId + 1;
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
