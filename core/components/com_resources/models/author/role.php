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

namespace Components\Resources\Models\Author;

use Hubzero\Database\Relational;
use Components\Resources\Models\Author\Role\Type;

include_once(__DIR__ . DS . 'role' . DS . 'type.php');

/**
 * Resource author role model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Role extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'author';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'title';

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
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'alias'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : $data['title']);
		$alias = strip_tags($alias);
		$alias = trim($alias);
		if (strlen($alias) > 100)
		{
			$alias = substr($alias . ' ', 0, 100);
			$alias = substr($alias, 0, strrpos($alias,' '));
		}
		$alias = str_replace(' ', '-', $alias);

		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
	}

	/**
	 * Get associated types
	 *
	 * @return  object
	 */
	public function types()
	{
		$tbl = Type::blank()->getTableName();

		return $this->manyToMany('Components\Resources\Models\Type', $tbl);
	}

	/**
	 * Return a formatted timestamp for created date
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('created');
			break;
		}
	}

	/**
	 * Defines a belongs to one relationship between article and user
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Set a list of types for a role
	 *
	 * @param   array  $current
	 * @return  bool
	 */
	public function setTypes($current)
	{
		// Get an array of all the previous types
		$old = array();

		$types = $this->types()
			->rows()
			->raw();

		// Run through the $current array and determine if
		// each item is new or not
		$keep = array();
		$add  = array();

		foreach ($current as $bit)
		{
			if (!isset($types[$bit]))
			{
				$add[]  = intval($bit);
			}
			else
			{
				$keep[] = intval($bit);
			}
		}

		$remove = array_diff($old, $keep);

		// Remove any types in the remove list
		if (count($remove) > 0)
		{
			foreach ($remove as $type_id)
			{
				$row = Type::oneByRoleAndType($this->get('id'), $type_id);
				if (!$row->destroy())
				{
					$this->addError($row->getError());
					return false;
				}
			}
		}

		// Add any types not in the OLD list
		if (count($add) > 0)
		{
			foreach ($add as $type_id)
			{
				$row = Type::blank();
				$row->set('role_id', $this->get('id'));
				$row->set('type_id', $type_id);
				if (!$row->save())
				{
					$this->addError($row->getError());
					return false;
				}
			}
		}

		return true;
	}
}
