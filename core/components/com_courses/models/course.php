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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Components\Courses\Tables;
use Hubzero\Config\Registry;
use Filesystem;
use Lang;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'course.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'page.php');
require_once(__DIR__ . DS . 'base.php');
require_once(__DIR__ . DS . 'permissions.php');
require_once(__DIR__ . DS . 'offering.php');
require_once(__DIR__ . DS . 'iterator.php');
require_once(__DIR__ . DS . 'tags.php');

/**
 * Courses model class for a course
 */
class Course extends Base
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\Course';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'course';

	/**
	 * \Components\Courses\Models\Offering
	 *
	 * @var object
	 */
	private $_offering = NULL;

	/**
	 * \Components\Courses\Models\Iterator
	 *
	 * @var object
	 */
	private $_offerings = NULL;

	/**
	 * \Components\Courses\Models\Permissions
	 *
	 * @var object
	 */
	private $_permissions = NULL;

	/**
	 * \Components\Courses\Models\Offering
	 *
	 * @var object
	 */
	private $_manager = NULL;

	/**
	 * List of managers
	 *
	 * @var array
	 */
	private $_managers = NULL;

	/**
	 * List of students
	 *
	 * @var array
	 */
	private $_students = NULL;

	/**
	 * \Components\Courses\Models\Offering
	 *
	 * @var object
	 */
	private $_page = NULL;

	/**
	 * \Components\Courses\Models\Iterator
	 *
	 * @var object
	 */
	private $_pages = NULL;

	/**
	 * List of plugins available for a given event
	 *
	 * @var array
	 */
	private $_plugins = array();

	/**
	 * URL to this object
	 *
	 * @var string
	 */
	private $_base = NULL;

	/**
	 * Certificate
	 *
	 * @var string
	 */
	private $_certificate = NULL;

	/**
	 * Constructor
	 *
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct($oid)
	{
		parent::__construct($oid);

		$this->config()->merge(new Registry($this->get('params')));

		if (!isset($this->_permissions))
		{
			$this->_permissions = Permissions::getInstance();
			$this->_permissions->set('course_id', $this->get('id'));
		}
	}

	/**
	 * Returns a reference to a course model
	 *
	 * This method must be invoked as:
	 *     $offering = \Components\Courses\Models\Course::getInstance($alias);
	 *
	 * @param      mixed $oid Course ID (int) or alias (string)
	 * @return     object \Components\Courses\Models\Course
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new self($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Check if the current user has manager access
	 * This is just a shortcut for the access check
	 *
	 * @return     boolean
	 */
	public function isManager($user_id=0)
	{
		if ((int) $user_id)
		{
			$this->_permissions->isManager($user_id);
		}
		return $this->_permissions->isManager();
	}

	/**
	 * Check if the current user has manager access
	 * This is just a shortcut for the access check
	 *
	 * @return     boolean
	 */
	public function isStudent($user_id=0)
	{
		if ((int) $user_id)
		{
			$this->_permissions->isStudent($user_id);
		}
		return $this->_permissions->isStudent();
	}

	/**
	 * Set and get a specific offering
	 *
	 * @return     void
	 */
	public function offering($id=null)
	{
		// If the current offering isn't set
		//    OR the ID passed doesn't equal the current offering's ID or alias
		if (!isset($this->_offering)
		 || ($id !== null && (int) $this->_offering->get('id') != $id && (string) $this->_offering->get('alias') != $id))
		{
			// Reset current offering
			$this->_offering = null;

			// If the list of all offerings is available ...
			if (isset($this->_offerings))
			{
				// Find an offering in the list that matches the ID passed
				foreach ($this->offerings() as $key => $offering)
				{
					if ((int) $offering->get('id') == $id || (string) $offering->get('alias') == $id)
					{
						// Set current offering
						$this->_offering = $offering;
						break;
					}
				}
			}

			if (is_null($this->_offering))
			{
				// Get current offering
				$this->_offering = Offering::getInstance($id, $this->get('id'));
			}
		}
		// Return current offering
		return $this->_offering;
	}

	/**
	 * Get a list of offerings for a course
	 *   Accepts an array of filters to build query from
	 *
	 * @param      array $filters Filters to build query from
	 * @return     mixed
	 */
	public function offerings($filters=array(), $clear=false)
	{
		if (!isset($filters['course_id']))
		{
			$filters['course_id'] = (int) $this->get('id');
		}

		// Perform a record count?
		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new Tables\Offering($this->_db);

			return $tbl->count($filters);
		}

		// Is the data is not set OR is it not the right type?
		if (!($this->_offerings instanceof Iterator) || $clear)
		{
			$tbl = new Tables\Offering($this->_db);

			// Attempt to get database results
			if (($results = $tbl->find($filters)))
			{
				// Loop through each result and turn into a model object
				foreach ($results as $key => $result)
				{
					$results[$key] = new Offering($result);
				}
			}
			else
			{
				// No results found
				// We need an empty array for the Iterator model
				$results = array();
			}

			// Set the results
			$this->_offerings = new Iterator($results);
		}

		// Return results
		return $this->_offerings;
	}

	/**
	 * Check a user's authorization
	 *
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='view', $item='course')
	{
		return $this->_permissions->access($action, $item);
	}

	/**
	 * Retrieve a specific manager record by user ID
	 *
	 * @return     boolean
	 */
	public function manager($user_id=null)
	{
		if (!isset($this->_manager)
		 || ($user_id !== null && (int) $this->_manager->get('user_id') != $user_id))
		{
			$this->_manager = null;

			if (isset($this->_managers) && isset($this->_managers[$user_id]))
			{
				$this->_manager = $this->_managers[$user_id];
			}

			if (!$this->_manager)
			{
				$this->_manager = \Components\Courses\Models\Manager::getInstance($user_id, $this->get('course_id'), 0, 0);
			}
		}

		return $this->_manager;
	}

	/**
	 * Get a list of managers for a course
	 *   If a manager has multiple entries, it will set
	 *   the entry int he array with the record that has
	 *   the highest permission levels
	 *
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $clear   Force a new dataset?
	 * @return     mixed
	 */
	public function managers($filters=array(), $clear=false)
	{
		if (!isset($filters['course_id']))
		{
			$filters['course_id'] = (int) $this->get('id');
		}
		if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = 0;
		}
		if (!isset($filters['section_id']))
		{
			$filters['section_id'] = 0;
		}
		$filters['student'] = 0;

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new Tables\Member($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_managers) || !is_array($this->_managers) || $clear)
		{
			$tbl = new Tables\Member($this->_db);

			$results = array();

			if (($data = $tbl->find($filters)))
			{
				foreach ($data as $key => $result)
				{
					if (!isset($results[$result->user_id]))
					{
						$results[$result->user_id] = new Manager($result, $this->get('id'));
					}
					else
					{
						// Course manager takes precedence over offering manager
						if ($result->course_id && !$result->offering_id && !$result->section_id)
						{
							$results[$result->user_id] = new Manager($result, $this->get('id'));
						}
						// Course offering takes precedence over section manager
						else if ($result->course_id && $result->offering_id && !$result->section_id)
						{
							$results[$result->user_id] = new Manager($result, $this->get('id'));
						}
					}
				}
			}

			$this->_managers = $results;
		}

		return $this->_managers;
	}

	/**
	 * Get a list of students for a course
	 *
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $clear   Force a new dataset?
	 * @return     mixed
	 */
	public function students($filters=array(), $clear=false)
	{
		if (!isset($filters['course_id']))
		{
			$filters['course_id'] = (int) $this->get('id');
		}
		$filters['student'] = 1;

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new Tables\Member($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_students) || !is_array($this->_students) || $clear)
		{
			$tbl = new Tables\Member($this->_db);

			$results = array();

			if (($data = $tbl->find($filters)))
			{
				foreach ($data as $key => $result)
				{
					$results[$key] = new Student($result, $this->get('id'));
				}
			}

			$this->_students = $results;
		}

		return $this->_students;
	}

	/**
	 * Get a list of instructors for a course
	 *
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $clear   Force a new dataset?
	 * @return     mixed
	 */
	public function instructors($filters=array(), $clear=true)
	{
		$filters['role'] = 'instructor';
		return $this->managers($filters, $clear);
	}

	/**
	 * Add one or more user IDs or usernames to the managers list
	 *
	 * @param     array $value List of IDs or usernames
	 * @return    void
	 */
	public function add($data = null, $role_id=0)
	{
		$user_ids = $this->_userIds($data);

		$tbl = new Tables\Member($this->_db);

		$filters = array(
			'course_id' => (int) $this->get('id')
		);

		foreach ($user_ids as $user_id)
		{
			$filters['user_id'] = $user_id;

			if (($data = $tbl->find($filters)))
			{
				$this->_managers[$user_id] = new Manager(array_shift($data), $this->get('id'));

				if (count($data) > 0)
				{
					foreach ($data as $key => $result)
					{
						$tbl->delete($result->id);
						//$data[$key] = new Manager($result, $this->get('id'));
						//$data[$key]->delete();
					}
				}
			}

			if (!isset($this->_managers[$user_id]))
			{
				$this->_managers[$user_id] = new Manager($user_id, $this->get('id'));
			}
			$this->_managers[$user_id]->set('user_id', $user_id);
			$this->_managers[$user_id]->set('course_id', $this->get('id'));
			$this->_managers[$user_id]->set('role_id', $role_id);
			$this->_managers[$user_id]->set('section_id', 0);
			$this->_managers[$user_id]->set('student', 0);
			$this->_managers[$user_id]->set('offering_id', 0);
			$this->_managers[$user_id]->store();
		}
	}

	/**
	 * Remove one or more user IDs or usernames to the managers list
	 *
	 * @param     array $value List of IDs or usernames
	 * @return    void
	 */
	public function remove($data = null)
	{
		$user_ids = $this->_userIds($data);

		if (count($user_ids) > 0)
		{
			$this->managers();

			foreach ($user_ids as $user_id)
			{
				if (isset($this->_managers[$user_id]))
				{
					$this->_managers[$user_id]->delete();
					unset($this->_managers[$user_id]);
				}
			}
		}
	}

	/**
	 * Return a list of user IDs for a list of usernames
	 *
	 * @param     array $users List of user IDs or usernames
	 * @return    array List of user IDs
	 */
	private function _userIds($users)
	{
		if (empty($this->_db))
		{
			return false;
		}

		$usernames = array();
		$userids = array();

		if (!is_array($users))
		{
			$users = array($users);
		}

		foreach ($users as $u)
		{
			if (is_numeric($u))
			{
				$userids[] = $u;
			}
			else
			{
				$usernames[] = $this->_db->quote($u);
			}
		}

		if (empty($usernames))
		{
			return $userids;
		}

		$this->_db->setQuery("SELECT id FROM `#__users` WHERE username IN (" . implode($usernames, ",") . ");");

		if (!($result = $this->_db->loadColumn()))
		{
			$result = array();
		}

		return array_merge($result, $userids);
	}

	/**
	 * Short title for 'update'
	 * Long title (if any) ...
	 *
	 * @param unknown $course_id Parameter title (if any) ...
	 * @param array $data Parameter title (if any) ...
	 * @return boolean Return title (if any) ...
	 */
	public function store($check=true)
	{
		if (empty($this->_db))
		{
			return false;
		}

		$isNew = ($this->get('id') ? false : true);

		$affected = 0;

		if ($check)
		{
			if (!$this->_tbl->check())
			{
				$this->setError($this->_tbl->getError());
				return false;
			}

			$this->importPlugin('content')->trigger('onContentBeforeSave', array(
				'com_courses.course.description',
				&$this,
				$this->exists()
			));
		}

		if (!$this->_tbl->store())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		$affected = $this->_db->getAffectedRows();

		// After SQL is done and has no errors, fire off onCourseUserEnrolledEvents
		// for every user added to this course
		$this->importPlugin('courses')
		     ->trigger('onCourseSave', array($this));

		if ($affected > 0)
		{
			$this->importPlugin('user')
			     ->trigger('onAfterStoreCourse', array($this));
		}

		if ($isNew)
		{
			$this->log($this->get('id'), $this->_scope, 'create');
		}

		return true;
	}

	/**
	 * Delete an entry and associated data
	 *
	 * @return     boolean True on success, false on error
	 */
	public function delete()
	{
		$this->importPlugin('courses')
		     ->trigger('onCourseDelete', array($this));

		return parent::delete();
	}

	/**
	 * Check if the current user is enrolled
	 *
	 * @return     boolean
	 */
	public function page($url=null)
	{
		if (!isset($this->_page)
		 || ($url !== null && (string) $this->_page->get('url') != $url))
		{
			$this->_page = null;

			if (isset($this->_pages) && is_array($this->_pages) && isset($this->_pages[$url]))
			{
				$this->_page = $this->_pages[$url];
			}

			if (!$this->_page)
			{
				$this->_page = new Page(0);
			}
		}

		return $this->_page;
	}

	/**
	 * Get a list of pages for a course
	 *
	 * @param      array $filters Filters to apply
	 * @return     array
	 */
	public function pages($filters=array())
	{
		if (!isset($filters['course_id']))
		{
			$filters['course_id'] = (int) $this->get('id');
		}
		if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = 0;
		}
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'ordering';
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new Tables\Page($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_pages) || !is_array($this->_pages))
		{
			$tbl = new Tables\Page($this->_db);

			$results = array();

			if (($data = $tbl->find($filters)))
			{
				foreach ($data as $key => $result)
				{
					$results[$result->url] = new Page($result);
				}
			}

			$this->_pages = $results;
		}

		return $this->_pages;
	}

	/**
	 * Check if the current user is enrolled
	 *
	 * @return     boolean
	 */
	public function tags($as='cloud', $admin=0)
	{
		if (!$this->exists())
		{
			switch (strtolower($as))
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

		return $cloud->render($as, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new Tags($this->get('id'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type The type of link to return
	 * @return     boolean
	 */
	public function link($type='')
	{
		if (!isset($this->_base))
		{
			$this->_base  = 'index.php?option=com_courses&gid=' . $this->get('alias');
		}

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link = $this->_base . '&task=edit';
			break;

			case 'delete':
				$link = $this->_base . '&task=delete';
			break;

			case 'permalink':
			default:
				$link = $this->_base;
			break;
		}

		return $link;
	}

	/**
	 * Get a course logo
	 *
	 * @param   string $rtrn Data type to return
	 * @return  mixed
	 */
	public function logo($rtrn='')
	{
		$rtrn = strtolower(trim($rtrn));

		// Return just the file name
		if ($rtrn == 'file')
		{
			return $this->get('logo');
		}

		$path = '';
		$size = array(
			'width'  => 0,
			'height' => 0
		);

		if ($file = $this->get('logo'))
		{
			$path = '/' . trim($this->config('uploadpath', '/site/courses'), '/') . '/' . $this->get('id') . '/' . $file;
			if (file_exists(PATH_APP . $path))
			{
				list($width, $height) = getimagesize(PATH_APP . $path);
				$size['width']  = $width;
				$size['height'] = $height;
				//$path = \Request::base(true) . substr(PATH_APP, strlen(PATH_ROOT)) . $path;

				if ($rtrn == 'url')
				{
					return $this->link() . '&active=logo';
				}
			}
			else
			{
				return null;
			}
		}

		// Return just the upload path?
		if ($rtrn == 'size')
		{
			return $size;
		}

		return $path;
	}

	/**
	 * Get the content of the entry
	 *
	 * @param      string  $as      Format to return state in [text, number]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     string
	 */
	public function description($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('description.parsed', null);
				if ($content === null)
				{
					$config = array(
						'option'   => 'com_courses',
						'scope'    => '',
						'pagename' => $this->get('alias'),
						'pageid'   => 0, //$this->get('id'),
						'filepath' => DS . ltrim($this->config()->get('uploadpath', '/site/courses'), DS),
						'domain'   => $this->get('alias')
					);

					$content = (string) $this->get('description', '');
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						'com_courses.course.description',
						&$this,
						&$config
					));

					$this->set('description.parsed', (string) $this->get('description', ''));
					$this->set('description', $content);

					return $this->description($as, $shorten);
				}
				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = $this->get('description');
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
				//$content = html_entity_decode($content);
				//$content = str_replace("\xC2\xA0", ' ', $content);
				$content = str_replace(array('&lt;', '&gt;', '&amp;'), array('<', '>', '&'), $content);
			break;
		}

		if ($shorten)
		{
			$content = \Hubzero\Utility\String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Copy an entry and associated data
	 *
	 * @param   boolean $deep Copy associated data?
	 * @return  boolean True on success, false on error
	 */
	public function copy($deep=true)
	{
		// Get some old info we may need
		//  - Course ID
		$c_id = $this->get('id');

		// Reset the ID. This will force store() to create a new record.
		$this->set('id', 0);
		// We want to distinguish this course from the one we copied from
		$this->set('title', $this->get('title') . ' (copy)');
		$this->set('alias', $this->get('alias') . '_copy');

		if (!$this->store())
		{
			return false;
		}

		if ($deep)
		{
			// Copy pages
			foreach ($this->pages(array('course_id' => $c_id, 'active' => array(0, 1)), true) as $page)
			{
				if (!$page->copy($this->get('course_id')))
				{
					$this->setError($page->getError());
				}
			}

			// Copy units
			foreach ($this->offerings(array('course_id' => $c_id), true) as $offering)
			{
				if (!$offering->copy($this->get('id')))
				{
					$this->setError($offering->getError());
				}
			}

			// Copy managers
			foreach ($this->managers(array('course_id' => $c_id), true) as $manager)
			{
				$manager->set('id', 0);
				$manager->set('course_id', $this->get('id'));
				if (!$manager->store())
				{
					$this->setError($manager->getError());
				}
			}

			// Copy logo
			if ($file = $this->get('logo'))
			{
				$src  = '/' . trim($this->config('uploadpath', '/site/courses'), '/') . '/' . $c_id . '/' . $file;
				if (file_exists(PATH_APP . $src))
				{
					$dest = '/' . trim($this->config('uploadpath', '/site/courses'), '/') . '/' . $this->get('id');

					if (!is_dir(PATH_APP . $dest))
					{
						if (!Filesystem::makeDirectory(PATH_APP . $dest))
						{
							$this->setError(Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH'));
						}
					}

					$dest .= '/' . $file;

					if (!Filesystem::copy(PATH_APP . $src, PATH_APP . $dest))
					{
						$this->setError(Lang::txt('Failed to copy course logo.'));
					}
				}
			}

			// Copy tags
			$tagger = new Tags($c_id);
			$this->tag($tagger->render('string', array('admin' => 1)), \User::get('id'), 1);
		}

		return true;
	}

	/**
	 * Get a course logo
	 *
	 * @return     string
	 */
	public function certificate()
	{
		if (!$this->_certificate)
		{
			include_once(__DIR__ . DS . 'certificate.php');

			$this->_certificate = Certificate::getInstance(0, $this->get('id'));
		}

		return $this->_certificate;
	}
}

