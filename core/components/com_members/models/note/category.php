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
		return self::blank()->whereEquals('extension', 'com_users');
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
