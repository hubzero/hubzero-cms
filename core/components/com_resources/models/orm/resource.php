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

use Components\Resources\Helpers\Tags;
use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Component;
use Date;

require_once(__DIR__ . DS . 'association.php');
require_once(dirname(__DIR__) . DS . 'type.php');
require_once(dirname(__DIR__) . DS . 'author.php');

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
	protected $filespace = null;

	/**
	 * Get parent type
	 *
	 * @return  object
	 */
	public function type()
	{
		return $this->belongsToOne('\Components\Resources\Models\Type', 'type')->row();
	}

	/**
	 * Generates a list of authors
	 *
	 * @return  object
	 */
	public function authors()
	{
		return $this->oneToMany('\Components\Resources\Models\Author', 'subid')->whereEquals('subtable', 'resources');
	}

	/**
	 * Generates a list of parents
	 *
	 * @return  object
	 */
	public function parents()
	{
		$model = new Association();
		return $model->manyToMany('Resource', $model->getTableName(), 'child_id', 'parent_id');
	}

	/**
	 * Generates a list of children
	 *
	 * @return  object
	 * @since   2.0.0
	 */
	public function children()
	{
		$model = new Association();
		return $this->manyToMany('Resource', $model->getTableName(), 'parent_id', 'child_id');
	}

	/**
	 * Check if a resource has an attachment with the specified path
	 *
	 * @param   string   $path  File path
	 * @return  boolean
	 */
	public function hasChild($path)
	{
		$row = $this->children()
			->whereEquals('standalone', 0)
			->whereEquals('path', $path, 1)
			->orWhere('path', 'LIKE', '%/' . $path, 1)
			->row();

		return $row->get('id') > 0;
	}

	/**
	 * Make this resource a child of another
	 *
	 * @param   mixed    $id  Resource object or ID
	 * @return  boolean
	 */
	public function makeChildOf($id)
	{
		if ($id instanceof Resource)
		{
			$id = $id->get('id');
		}

		if (!$id)
		{
			return false;
		}

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
	 * @param   mixed    $id  Resource object or ID
	 * @return  boolean
	 */
	public function makeParentOf($id)
	{
		if ($id instanceof Resource)
		{
			$id = $id->get('id');
		}

		if (!$id)
		{
			return false;
		}

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
	 * Build and return the base path to resource file storage
	 *
	 * @return  string
	 */
	public function basepath()
	{
		static $base;

		if (!$base)
		{
			$base = PATH_APP . DS . trim(Component::params('com_resources')->get('webpath', '/site/resources'), '/');
		}

		return $base;
	}

	/**
	 * Build and return the relative path to resource file storage
	 *
	 * @return  string
	 */
	public function relativepath()
	{
		$date = $this->get('created');

		if ($date && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs))
		{
			$date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		if ($date)
		{
			$dir_year  = Date::of($date)->format('Y');
			$dir_month = Date::of($date)->format('m');

			if (!is_dir($this->basepath() . DS . $dir_year . DS . $dir_month . DS . String::pad($this->get('id')))
			 && intval($dir_year) <= 2013
			 && intval($dir_month) <= 11)
			{
				$dir_year  = Date::of($date)->toLocal('Y');
				$dir_month = Date::of($date)->toLocal('m');
			}
		}
		else
		{
			$dir_year  = Date::of('now')->format('Y');
			$dir_month = Date::of('now')->format('m');
		}

		return $dir_year . DS . $dir_month . DS . String::pad($this->get('id'));
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
			$this->filespace = $this->basepath() . DS . $this->relativepath();
		}

		return $this->filespace;
	}

	/**
	 * Build and return the url
	 *
	 * @return  string
	 */
	public function link()
	{
		return 'index.php?option=com_resources&alias=' . ($this->get('alias') ? 'alias=' . $this->get('alias') : 'id=' . $this->get('id'));
	}

	/**
	 * Build and return the url
	 *
	 * @return  string
	 */
	public function tags()
	{
		require_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'tags.php');

		$cloud = new Tags($this->get('id'));
		return $cloud->tags();
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
