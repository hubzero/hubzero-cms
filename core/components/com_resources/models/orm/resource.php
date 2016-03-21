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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 1.3.2
 */

namespace Components\Resources\Models\Orm;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Component;
use Date;

require_once(__DIR__ . DS . 'association.php');

/**
 * Resource model
 *
 * @uses \Hubzero\Database\Relational
 */
class Resource extends Relational
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
	public $orderBy = 'id';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title' => 'notempty'
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
	 * Path to filespace
	 *
	 * @var  string
	 */
	public $filespace = null;

	/**
	 * Generates a list of children
	 *
	 * @return  array  Array of children, since you can't relate thru 
	 * @since   1.3.2
	 */
	public function children()
	{
		//return $this->oneToMany('Association', 'parent_id', 'id')->rows()->toArray();
		$model = new Association();
		return $this->manyToMany('Resource', $model->getTableName(), 'parent_id', 'child_id');
	}

	/**
	 * Make this resource a child of another
	 *
	 * @param   integer  $id  Resource ID
	 * @return  boolean
	 */
	public function makeChildOf($id)
	{
		$model = new Association();
		$model->set('parent_id', (int)$id);
		$model->set('child_id', $this->get('id'));
		$model->set('grouping', 0);

		if (!$model->save())
		{
			$this->setError($model->getError());
			return false;
		}

		return true;
	}

	/**
	 * Make this resource a parent of another
	 *
	 * @param   integer  $id  Resource ID
	 * @return  boolean
	 */
	public function makeParentOf($id)
	{
		$model = new Association();
		$model->set('parent_id', $this->get('id'));
		$model->set('child_id', (int)$id);
		$model->set('grouping', 0);

		if (!$model->save())
		{
			$this->setError($model->getError());
			return false;
		}

		return true;
	}

	/**
	 * Delete a record and any associated data
	 *
	 * @return  bool
	 */
	public function destroy()
	{
		// Remove children
		foreach ($this->children()->rows() as $child)
		{
			if ($child->get('standalone'))
			{
				continue;
			}

			if (!$child->destroy())
			{
				$this->setError($child->getError());
				return false;
			}
		}

		// Remove parent associations
		$parents = Association::all()
			->whereEquals('child_id', $this->get('id'))
			->rows();

		foreach ($parents as $parent)
		{
			if (!$parent->destroy())
			{
				$this->setError($parent->getError());
				return false;
			}
		}

		return parent::destroy();
	}

	/**
	 * Build and return the path to resource file storage
	 *
	 * @return  string
	 */
	public function filespace()
	{
		if (!$this->filespace)
		{
			$base = PATH_APP . DS . trim(Component::params('com_resources')->get('webpath', '/site/resources'), '/');

			$date = $this->get('created');
			if ($date && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs))
			{
				$date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
			}
			if (!$date)
			{
				$date = 'now';
			}
			$dir_year  = Date::of($date)->format('Y');
			$dir_month = Date::of($date)->format('m');

			$this->filespace = $base . DS . $dir_year . DS . $dir_month . DS . String::pad($id);
		}

		return $this->filespace;
	}

	/**
	 * Generates a list the most recent entries
	 *
	 * @param   integer  $limit
	 * @param   string   $dateField
	 * @param   string   $sort
	 * @return  object
	 */
	public static function getLatest($limit = 10, $dateField = 'created', $sort = 'DESC')
	{
		$rows = self::all()
			->whereEquals('standalone', 1)
			->order($dateField, $sort)
			->limit($limit);

		return $rows;
	}
}
