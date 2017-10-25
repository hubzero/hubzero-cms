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

namespace Components\Resources\Models;

use Components\Resources\Helpers\Tags;
use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Hubzero\Utility\Str;
use Component;
use Date;
use Lang;
use User;

require_once __DIR__ . DS . 'association.php';
require_once __DIR__ . DS . 'type.php';
require_once __DIR__ . DS . 'author.php';
require_once __DIR__ . DS . 'license.php';

/**
 * Resource model
 *
 * NOTE: This isn't named 'Resource' because it's
 * a reserved word in PHP 7+
 *
 * @uses \Hubzero\Database\Relational
 */
class Entry extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = '';

	/**
	 * The table name, non-standard naming 
	 *
	 * @var  string
	 */
	protected $table = '#__resources';

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
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'alias',
		'modified',
		'modified_by'
	);

	/**
	 * Path to filespace
	 *
	 * @var  string
	 */
	protected $filespace = null;

	/**
	 * Params Registry
	 *
	 * @var  object
	 */
	protected $paramsRegistry = null;

	/**
	 * Attribs Registry
	 *
	 * @var  object
	 */
	protected $attribsRegistry = null;

	/**
	 * Generates automatic alias field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		if (!isset($data['alias']))
		{
			$data['alias'] = '';
		}
		$alias = str_replace(' ', '-', $data['alias']);
		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
	}

	/**
	 * Generates automatic created field value
	 *
	 * @return  string
	 */
	public function automaticModified()
	{
		return Date::of('now')->toSql();
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @return  int
	 */
	public function automaticModifiedBy()
	{
		return User::get('id');
	}

	/**
	 * Get parent type
	 *
	 * @return  object
	 */
	public function transformType()
	{
		//return $this->belongsToOne(__NAMESPACE__ . '\\Type', 'type_id')->row();
		return Type::oneOrNew($this->get('type'));
	}

	/**
	 * Get logical type
	 *
	 * @return  object
	 */
	public function transformLogicaltype()
	{
		//return $this->belongsToOne(__NAMESPACE__ . '\\Type', 'logicaltype_id')->row();
		return Type::oneOrNew($this->get('logicaltype'));
	}

	/**
	 * Get associated license
	 *
	 * @return  object
	 */
	public function license()
	{
		//return $this->oneToOne(__NAMESPACE__ . '\\License', 'license_id');
		return License::oneByName($this->params->get('license'));
	}

	/**
	 * Get owning group
	 *
	 * @return  object
	 */
	public function transformGroup()
	{
		//return $this->belongsToOne('Hubzero\User\Group', 'group_owner');
		return \Hubzero\User\Group::getInstance($this->get('group_owner'));
	}

	/**
	 * Get all the groups allowed to access a resource
	 *
	 * @return  array
	 */
	public function transformGroups()
	{
		$allowedgroups = array();

		if ($group_access = $this->get('group_access'))
		{
			$group_access = trim($group_access);
			$group_access = trim($group_access, ';');
			$group_access = explode(';', $group_access);

			$allowedgroups += $group_access;
		}

		if ($this->get('group_owner'))
		{
			$allowedgroups[] = $this->get('group_owner');
		}

		return $allowedgroups;
	}

	/**
	 * Generates a list of authors
	 *
	 * @return  object
	 */
	public function authors()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Author', 'subid')->whereEquals('subtable', 'resources');
	}

	/**
	 * Get a list of authors
	 *
	 * @return  string
	 */
	public function authorsList()
	{
		$names = array();

		foreach ($this->authors()->ordered()->rows() as $contributor)
		{
			if (strtolower($contributor->get('role')) == 'submitter')
			{
				continue;
			}

			// Build the user's name and link to their profile
			$name = htmlentities($contributor->name);
			if ($contributor->get('authorid') > 0)
			{
				$name = '<a href="' . Route::url('index.php?option=com_members&id=' . $contributor->get('authorid')) . '" data-rel="contributor" class="resource-contributor" title="View the profile of ' . $name . '">' . $name . '</a>';
			}
			if ($contributor->get('role'))
			{
				$name .= ' (' . $contributor->get('role') . ')';
			}

			$names[] = $name;
		}

		return implode(', ', $names);
	}

	/**
	 * Generates a list of parents
	 *
	 * @return  object
	 */
	public function parents()
	{
		$model = new Association();
		return $model->manyToMany(__NAMESPACE__ . '\\Entry', $model->getTableName(), 'child_id', 'parent_id');
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
		return $this->manyToMany(__NAMESPACE__ . '\\Entry', $model->getTableName(), 'parent_id', 'child_id');
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
			$this->addError($model->getError());
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
			$this->addError($model->getError());
			return false;
		}

		return true;
	}

	/**
	 * Is this a tool?
	 *
	 * @return  bool
	 */
	public function isTool()
	{
		return $this->type->isForTools();
	}

	/**
	 * Check if the resource was deleted
	 *
	 * @return  bool
	 */
	public function isDeleted()
	{
		return ($this->get('published') == 4);
	}

	/**
	 * Check if the resource is published
	 *
	 * @return  bool
	 */
	public function isPublished()
	{
		if ($this->isNew())
		{
			return false;
		}

		// Make sure the resource is published and standalone
		if (in_array($this->get('published'), array(0, 2, 4, 5)))
		{
			return false;
		}

		if ($this->get('publish_up')
		 && $this->get('publish_up') != '0000-00-00 00:00:00'
		 && $this->get('publish_up') >= Date::toSql())
		{
			return false;
		}

		if ($this->get('publish_down')
		 && $this->get('publish_down') != '0000-00-00 00:00:00'
		 && $this->get('publish_down') <= Date::toSql())
		{
			return false;
		}

		return true;
	}

	/**
	 * Check if the resource is owned by a group
	 *
	 * @param   mixed  $group
	 * @return  bool
	 */
	public function inGroup($group=null)
	{
		if ($group)
		{
			if (!is_array($group))
			{
				$group = array($group);
			}

			if (in_array($this->get('group_owner'), $group))
			{
				return true;
			}
		}
		else
		{
			if ($this->get('group_owner'))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Transform attribs
	 *
	 * @return  object
	 */
	public function transformAttribs()
	{
		if (!is_object($this->attribsRegistry))
		{
			$this->attribsRegistry = new Registry($this->get('attribs'));
		}

		return $this->attribsRegistry;
	}

	/**
	 * Transform params
	 *
	 * @return  object
	 */
	public function transformParams()
	{
		if (!is_object($this->paramsRegistry))
		{
			$params = new Registry($this->get('params'));

			$p = Component::params('com_resources');
			$p->merge($params);

			$this->paramsRegistry = $p;
		}

		return $this->paramsRegistry;
	}

	/**
	 * Transform display date
	 *
	 * @return  string
	 */
	public function transformDate()
	{
		if (!$this->get('modified') || $this->get('modified') == '0000-00-00 00:00:00')
		{
			$this->set('modified', $this->get('created'));
		}

		if (!$this->get('publish_up') || $this->get('publish_up') == '0000-00-00 00:00:00')
		{
			$this->set('publish_up', $this->get('created'));
		}

		// Set the display date
		switch ($this->params->get('show_date'))
		{
			case 1:
				$thedate = Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
				break;
			case 2:
				$thedate = Date::of($this->get('modified'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
				break;
			case 3:
				$thedate = Date::of($this->get('publish_up'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
				break;
			case 0:
			default:
				$thedate = '';
				break;
		}

		return $thedate;
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
				$this->addError($child->getError());
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
				$this->addError($parent->getError());
				return false;
			}
		}

		// Remove any associated files
		$path = $this->filespace();

		if (is_dir($path))
		{
			if (!Filesystem::deleteDirectory($path))
			{
				$this->addError('Unable to delete file(s).');

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

			if (!is_dir($this->basepath() . DS . $dir_year . DS . $dir_month . DS . Str::pad($this->get('id')))
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

		return $dir_year . DS . $dir_month . DS . Str::pad($this->get('id'));
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
		return 'index.php?option=com_resources&' . ($this->get('alias') ? 'alias=' . $this->get('alias') : 'id=' . $this->get('id'));
	}

	/**
	 * Build and return the url
	 *
	 * @param   string  $as
	 * @return  string
	 */
	public function tags($as = 'list')
	{
		require_once dirname(__DIR__) . DS . 'helpers' . DS . 'tags.php';

		$cloud = new Tags($this->get('id'));

		if ($as == 'list')
		{
			$tags = array();
			foreach ($cloud->tags() as $tag)
			{
				array_push($tags, $tag->tag);
			}

			return $tags;
		}

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

	/**
	 * Build a query based on commonly used filters
	 *
	 * @param   array  $filters
	 * @return  object
	 */
	public static function allWithFilters($filters = array())
	{
		$query = self::all();

		$r = $query->getTableName();
		$a = Author::blank()->getTableName();

		$query
			->select($r . '.*')
			->join($a, $a . '.subid', $r . '.id', 'left')
			->whereEquals($a . '.subtable', 'resources');

		if (isset($filters['standalone']))
		{
			$query->whereEquals($r . '.standalone', $filters['standalone']);
		}

		if (isset($filters['published']))
		{
			$query->whereIn($r . '.published', (array) $filters['published']);
		}

		if (isset($filters['type']))
		{
			if (!is_numeric($filters['type']))
			{
				$filters['type'] = Type::oneByAlias($filters['type'])->get('id');
			}
			$query->whereEquals($r . '.type', $filters['type']);
		}

		if (isset($filters['search']))
		{
			$query->whereLike($r . '.title', $filters['search'], 1)
				->orWhereLike($r . '.fulltxt', $filters['search'], 1)
				->resetDepth();
		}

		if (isset($filters['created_by']))
		{
			$query->whereEquals($r . '.created_by', $filters['created_by']);
		}

		if (isset($filters['author']))
		{
			$query->whereEquals($a . '.authorid', $filters['author']);

			if (isset($filters['notauthorrole']))
			{
				$query->where($a . '.role', '!=', $filters['notauthorrole']);
			}
		}

		if (isset($filters['access']) && !empty($filters['access']))
		{
			if (!is_array($filters['access']) && !is_numeric($filters['access']))
			{
				switch ($filters['access'])
				{
					case 'public':
						$filters['access'] = 0;
						break;
					case 'protected':
						$filters['access'] = 3;
						break;
					case 'private':
						$filters['access'] = 4;
						break;
					case 'all':
					default:
						$filters['access'] = array(0, 1, 2, 3, 4);
						break;
				}
			}

			if (isset($filters['usergroups']) && !empty($filters['usergroups']))
			{
				$query->whereIn($r . '.access', (array) $filters['access'], 1)
					->orWhereIn($r . '.group_owner', (array) $filters['usergroups'], 1)
					->resetDepth();
			}
			else
			{
				$query->whereIn($r . '.access', (array) $filters['access']);
			}
		}
		elseif (isset($filters['usergroups']) && !empty($filters['usergroups']))
		{
			$query->whereIn($r . '.group_owner', (array) $filters['usergroups']);
		}

		if (isset($filters['now']))
		{
			$query->whereEquals($r . '.publish_up', '0000-00-00 00:00:00', 1)
				->orWhere($r . '.publish_up', '<=', $filters['now'], 1)
				->resetDepth()
				->whereEquals($r . '.publish_down', '0000-00-00 00:00:00', 1)
				->orWhere($r . '.publish_down', '>=', $filters['now'], 1)
				->resetDepth();
		}

		if (isset($filters['startdate']) && $filters['startdate'])
		{
			$query->where($r . '.publish_up', '>', $filters['startdate']);
		}
		if (isset($filters['enddate']) && $filters['enddate'])
		{
			$query->where($r . '.publish_up', '<', $filters['enddate']);
		}

		return $query;
	}
}
