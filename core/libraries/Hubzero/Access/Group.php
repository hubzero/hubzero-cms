<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Access;

use Hubzero\Database\Relational;

/**
 * User access group
 */
class Group extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = '';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__usergroups';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'lft';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title' => 'notempty'
	);

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('title', function($data)
		{
			if (!isset($data['title']) || $data['title'] == '')
			{
				return 'Title is required.';
			}

			$exist = self::all()
				->whereEquals('title', $data['title'])
				->whereEquals('parent_id', $data['parent_id'])
				->where('id', '<>', $data['id'])
				->count();

			return $exist ? 'Access group title already exists.' : false;
		});
	}

	/**
	 * Defines a relationship to the User/Group Map
	 *
	 * @return  object
	 */
	public function maps()
	{
		return $this->oneToMany('Map', 'group_id');
	}

	/**
	 * Get parent
	 *
	 * @return  object
	 */
	public function parent()
	{
		return $this->oneToOne('Group', 'parent');
	}

	/**
	 * Load a record by title
	 *
	 * @param   string  $title
	 * @return  object
	 */
	public static function oneByTitle($title)
	{
		return self::all()
			->whereEquals('title', $title)
			->row();
	}

	/**
	 * Saves the current model to the database
	 *
	 * @return  bool
	 */
	public function save()
	{
		if ($result = parent::save())
		{
			// Rebuild the nested set tree.
			$this->rebuild();
		}

		return $result;
	}

	/**
	 * Method to recursively rebuild the nested set tree.
	 *
	 * @param   integer  $parent_id  The root of the tree to rebuild.
	 * @param   integer  $left       The left id to start with in building the tree.
	 * @return  boolean  True on success
	 */
	public function rebuild($parent_id = 0, $left = 0)
	{
		// get all children of this node
		$children = self::all()
			->select('id')
			->whereEquals('parent_id', (int) $parent_id)
			->order('parent_id', 'asc')
			->rows();

		// the right value of this node is the left value + 1
		$right = $left + 1;

		// execute this function recursively over all children
		foreach ($children as $child)
		{
			// $right is the current right value, which is incremented on recursion return
			$right = $this->rebuild($child->get('id'), $right);

			// if there is an update failure, return false to break out of the recursion
			if ($right === false)
			{
				return false;
			}
		}

		// we've got the left value, and now that we've processed
		// the children of this node we also know the right value
		$query = $this->getQuery()
			->update($this->getTableName())
			->set(array(
				'lft' => (int) $left,
				'rgt' => (int) $right
			))
			->whereEquals('id', (int) $parent_id);

		// if there is an update failure, return false to break out of the recursion
		if (!$query->execute())
		{
			return false;
		}

		// return the right value of this node + 1
		return $right + 1;
	}

	/**
	 * Delete this object and its dependencies
	 *
	 * @return  boolean
	 */
	public function destroy()
	{
		if ($this->get('id') == 0)
		{
			$this->addError('JGLOBAL_CATEGORY_NOT_FOUND');
			return false;
		}

		if ($this->get('parent_id') == 0)
		{
			$this->addError('JLIB_DATABASE_ERROR_DELETE_ROOT');
			return false;
		}

		if ($this->get('lft') == 0 or $this->get('rgt') == 0)
		{
			$this->addError('JLIB_DATABASE_ERROR_DELETE_ROOT');
			return false;
		}

		// Select it's children
		$children = self::all()
			->where('lft', '>=', (int)$this->get('lft'))
			->where('rgt', '<=', (int)$this->get('rgt'))
			->rows();

		if (!$children->count())
		{
			$this->addError('JLIB_DATABASE_ERROR_DELETE_CATEGORY');
			return false;
		}

		// Delete the dependencies
		$ids = array();

		foreach ($children as $child)
		{
			$ids[] = $child->get('id');
		}

		$query = $this->getQuery()
			->delete($this->getTableName())
			->whereIn('id', $ids);

		if (!$query->execute())
		{
			$this->addError($query->getError());
			return false;
		}

		// Delete the usergroup in view levels
		$find    = array();
		$replace = array();
		foreach ($ids as $id)
		{
			$find[] = "[$id,";
			$find[] = ",$id,";
			$find[] = ",$id]";
			$find[] = "[$id]";

			$replace[] = "[";
			$replace[] = ",";
			$replace[] = "]";
			$replace[] = "[]";
		}

		$rules = Viewlevel::all()
			->rows();

		foreach ($rules as $rule)
		{
			foreach ($ids as $id)
			{
				if (strstr($rule->get('rules'), '[' . $id)
				 || strstr($rule->get('rules'), ',' . $id)
				 || strstr($rule->get('rules'), $id . ']'))
				{
					$rule->set('rules', str_replace($find, $replace, $rule->get('rules')));

					if (!$rule->save())
					{
						$this->addError($rule->getError());
						return false;
					}
				}
			}
		}

		// Delete the user to usergroup mappings for the group(s) from the database.
		try
		{
			Map::destroyByGroup($ids);
		}
		catch (\Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}

		return true;
	}
}
