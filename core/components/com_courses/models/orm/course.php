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

namespace Components\Courses\Models\Orm;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Component;
use Event;
use Html;

require_once __DIR__ . DS . 'tags.php';
require_once __DIR__ . DS . 'offering.php';
require_once __DIR__ . DS . 'page.php';

/**
 * Model class for a course entry
 */
class Course extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = '';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'publish_up';

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
	 * Fields to be parsed
	 *
	 * @var array
	 */
	protected $parsed = array(
		'description'
	);

	/**
	 * Registry
	 *
	 * @var  object
	 */
	public $params = NULL;

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
		$alias = str_replace(' ', '_', $alias);

		return preg_replace("/[^a-zA-Z0-9_\-\.]/", '', strtolower($alias));
	}

	/**
	 * Retrieves one row loaded by an alias field
	 *
	 * @param   string  $alias  The alias to load by
	 * @return  mixed
	 */
	public static function oneByAlias($alias)
	{
		return self::blank()
			->whereEquals('alias', $alias)
			->row();
	}

	/**
	 * Has the offering started?
	 *
	 * @return  boolean
	 */
	public function isDeleted()
	{
		return ($this->get('state') == self::STATE_TRASHED);
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Get a list of offerings
	 *
	 * @return  object
	 */
	public function offerings()
	{
		return $this->oneToMany('Offering', 'course_id');
	}

	/**
	 * Get a list of pages
	 *
	 * @return  object
	 */
	public function pages()
	{
		return $this->oneToMany('Page', 'course_id');
	}

	/**
	 * Get tags on an entry
	 *
	 * @param   string   $what   Data format to return (string, array, cloud)
	 * @param   integer  $admin  Get admin tags? 0=no, 1=yes
	 * @return  mixed
	 */
	public function tags($what='cloud', $admin=0)
	{
		if (!$this->get('id'))
		{
			switch (strtolower($what))
			{
				case 'array':
					return array();
				break;

				case 'string':
				case 'cloud':
				case 'html':
				default:
					return '';
				break;
			}
		}

		$cloud = new Tags($this->get('id'));

		return $cloud->render($what, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @param   string   $tags     Tags to apply
	 * @param   integer  $user_id  ID of tagger
	 * @param   integer  $admin    Tag as admin? 0=no, 1=yes
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new Tags($this->get('id'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 *
	 * @param   string  $as  Format to return state in [text, number]
	 * @return  mixed   String or Integer
	 */
	public function state($as='text')
	{
		$as = strtolower($as);

		if ($as == 'text')
		{
			switch ($this->get('state'))
			{
				case 1:  return 'published';   break;
				case 2:  return 'trashed';     break;
				case 0:
				default: return 'unpublished'; break;
			}
		}

		return $this->get('state');
	}

	/**
	 * Get the access level of the entry as either text or numerical value
	 *
	 * @param   string  $as  Format to return state in [text, number]
	 * @return  mixed   String or Integer
	 */
	public function visibility($as='text')
	{
		static $access;

		if ($as == 'text')
		{
			if (!isset($access))
			{
				$access = \Html::access('assetgroups');
			}
			foreach ($access as $a)
			{
				if ($this->get('access') == $a->value)
				{
					return $a->text;
				}
			}
		}

		return $this->get('access');
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type    The type of link to return
	 * @param   mixed   $params  Optional string or associative array of params to append
	 * @return  string
	 */
	public function link($type='', $params=null)
	{
		if (!isset($this->base))
		{
			$this->base  = 'index.php?option=com_courses&gid=' . $this->get('alias');
		}

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link = $this->base . '&task=edit';
			break;

			case 'delete':
				$link = $this->base . '&task=delete';
			break;

			case 'permalink':
			default:
				$link = $this->base;
			break;
		}

		return $link;
	}

	/**
	 * Transform params
	 *
	 * @return  string
	 */
	public function transformParams()
	{
		if (!is_object($this->params))
		{
			$params = new Registry($this->get('params'));

			$p = Component::params('com_courses');
			$p->merge($params);

			$this->params = $p;
		}

		return $this->params;
	}

	/**
	 * Parses content string as directed
	 *
	 * @return  string
	 */
	public function transformDescription()
	{
		$field = 'description';

		$property = "_{$field}Parsed";

		if (!isset($this->$property))
		{
			$params = array(
				'option'   => 'com_courses',
				'scope'    => '',
				'pagename' => $this->get('alias'),
				'pageid'   => 0,
				'filepath' => '',
				'domain'   => ''
			);

			$this->$property = Html::content('prepare', $this->get($field, ''), $params);
		}

		return $this->$property;
	}

	/**
	 * Store the record
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function save()
	{
		$result = parent::save();

		if ($result)
		{
			Event::trigger('courses.onCourseSave', array($this));
		}

		return $result;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		Event::trigger('courses.onCourseDelete', array($this));

		// Remove offerings
		foreach ($this->offerings()->rows() as $offering)
		{
			if (!$offering->destroy())
			{
				$this->addError($offering->getError());
				return false;
			}
		}

		// Remove pages
		foreach ($this->pages()->rows() as $page)
		{
			if (!$page->destroy())
			{
				$this->addError($page->getError());
				return false;
			}
		}

		// Remove all tags
		$this->tag('');

		// Attempt to delete the record
		return parent::destroy();
	}
}
