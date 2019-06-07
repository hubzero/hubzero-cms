<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models\Note;

use Hubzero\Database\Relational;

/**
 * Member notes model for a category
 */
class Category extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = '';

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
	 * Returns all rows (unless otherwise limited)
	 *
	 * @param   string|array  $columns  The columns to select
	 * @return  \Hubzero\Database\Relational|static
	 */
	public static function all($columns = null)
	{
		return self::blank()->whereEquals('extension', 'com_members');
	}

	/**
	 * Get a list of responses
	 *
	 * @param   array    $filters  Filters to apply to query
	 * @return  object
	 */
	public function children($filters = array())
	{
		$categories = self::blank()
			->whereEquals('parent_id', $this->get('id'));

		if (isset($filters['state']))
		{
			$categories->whereEquals('published', $filters['state']);
		}

		if (isset($filters['access']))
		{
			$categories->whereEquals('access', $filters['access']);
		}

		return $categories;
	}

	/**
	 * Get parent section
	 *
	 * @return  object
	 */
	public function parent()
	{
		return self::oneOrFail($this->get('parent_id', 0));
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Can't delete what doesn't exist
		if (!$this->get('id'))
		{
			return true;
		}

		// Remove children
		foreach ($this->children()->rows() as $category)
		{
			if (!$category->destroy())
			{
				$this->setError($category->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}
}
