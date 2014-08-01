<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'section.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section' . DS . 'code.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section' . DS . 'date.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section' . DS . 'badge.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'member.php');

/**
 * Courses model class for a course
 */
class CoursesModelSection extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableSection';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'section';

	/**
	 * Flag for if authorization checks have been run
	 *
	 * @var mixed
	 */
	private $_authorized = false;

	/**
	 * JUser
	 *
	 * @var object
	 */
	private $_roles = NULL;

	/**
	 * JUser
	 *
	 * @var object
	 */
	private $_members = NULL;

	/**
	 * JUser
	 *
	 * @var object
	 */
	private $_member = NULL;

	/**
	 * CoursesModelIterator
	 *
	 * @var object
	 */
	private $_codes = NULL;

	/**
	 * CoursesModelSectionCode
	 *
	 * @var object
	 */
	private $_code = NULL;

	/**
	 * CoursesModelIterator
	 *
	 * @var object
	 */
	private $_dates = NULL;

	/**
	 * CoursesModelSectionDate
	 *
	 * @var object
	 */
	private $_date = NULL;

	/**
	 * CoursesModelSectionBadge
	 *
	 * @var object
	 */
	private $_badge = NULL;

	/**
	 * JRegistry
	 *
	 * @var object
	 */
	private $_params = NULL;

	/**
	 * Constructor
	 *
	 * @param      integer $id Course offering ID or alias
	 * @return     void
	 */
	public function __construct($oid=null, $offering_id=null)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CoursesTableSection($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			if ($oid)
			{
				$this->_tbl->load($oid, $offering_id);
			}
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}

		if (!$this->exists() && $offering_id)
		{
			$this->set('id', 0);
			$this->set('offering_id', $offering_id);
		}
	}

	/**
	 * Returns a reference to a course offering model
	 *
	 * This method must be invoked as:
	 *     $offering = CoursesModelOffering::getInstance($alias);
	 *
	 * @param      mixed $oid ID (int) or alias (string)
	 * @return     object CoursesModelOffering
	 */
	static function &getInstance($oid=null, $offering_id=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$key = 0;

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid . '_' . $offering_id;
		}
		else if (is_object($oid))
		{
			$key = $oid->get('id') . '_' . $offering_id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'] . '_' . $offering_id;
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $offering_id);
		}

		return $instances[$key];
	}

	/**
	 * Has the offering started?
	 *
	 * @return     boolean
	 */
	public function started()
	{
		if (!$this->exists() || !$this->isPublished())
		{
			return false;
		}

		$now = JFactory::getDate()->toSql();

		if ($this->get('start_date')
		 && $this->get('start_date') != $this->_db->getNullDate()
		 && $this->get('start_date') > $now)
		{
			return false;
		}

		return true;
	}

	/**
	 * Has the offering ended?
	 *
	 * @return     boolean
	 */
	public function ended()
	{
		if (!$this->exists() || !$this->isPublished())
		{
			return true;
		}

		$now = JFactory::getDate()->toSql();

		if ($this->get('end_date')
		 && $this->get('end_date') != $this->_db->getNullDate()
		 && $this->get('end_date') <= $now)
		{
			return true;
		}

		return false;
	}

	/**
	 * Has the section ended?
	 *
	 * @return     boolean
	 */
	public function expired()
	{
		// If it doesn't exist or isn't published
		if (!$this->exists() || !$this->isPublished())
		{
			return true;
		}

		$now = JFactory::getDate()->toSql();

		if ($this->get('publish_down')
		 && $this->get('publish_down') != $this->_db->getNullDate()
		 && $this->get('publish_down') <= $now)
		{
			return true;
		}

		return false;
	}

	/**
	 * Has the offering ended?
	 *
	 * @return     boolean
	 */
	public function canEnroll()
	{
		// If it doesn't exist or isn't published
		if (!$this->exists() || !$this->isPublished())
		{
			return false;
		}
		// Is enrollment closed?
		if ($this->get('enrollment') == 2)
		{
			return false;
		}
		return true;
	}

	/**
	 * Check if the current user has manager access
	 * This is just a shortcut for the access check
	 *
	 * @return     boolean
	 */
	public function isMember($id=null)
	{
		if (!$id)
		{
			$juser = JFactory::getUser();
			$id = $juser->get('id');
		}
		return $this->member($id)->exists();
	}

	/**
	 * Check if the current user has manager access
	 * This is just a shortcut for the access check
	 *
	 * @return     boolean
	 */
	public function isManager()
	{
		return $this->access('manage');
	}

	/**
	 * Check a user's authorization
	 *
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='view', $item='section')
	{
		if (!isset($this->_permissions))
		{
			$this->_permissions = CoursesModelPermissions::getInstance();
			$this->_permissions->set('offering_id', $this->get('id'));
			$this->_permissions->set('section_id', $this->get('id'));
		}
		return $this->_permissions->access($action, $item);
	}

	/**
	 * Check if the current user is enrolled
	 *
	 * @return     boolean
	 */
	public function member($user_id=null)
	{
		if (!isset($this->_member)
		 || ($user_id !== null && (int) $this->_member->get('user_id') != $user_id))
		{
			$this->_member = null;

			if (isset($this->_members) && isset($this->_members[$user_id]))
			{
				$this->_member = $this->_members[$user_id];
			}
		}

		if (!$this->_member)
		{
			$this->_member = CoursesModelMember::getInstance($user_id, null, null, $this->get('id'));
		}

		return $this->_member;
	}

	/**
	 * Get a list of units for an offering
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 *
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $clear   Force a new dataset?
	 * @return     mixed
	 */
	public function members($filters=array(), $clear=false)
	{
		if (!isset($filters['section_id']))
		{
			$filters['section_id'] = (int) $this->get('id');
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableMember($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_members) || !is_array($this->_members) || $clear)
		{
			//require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'iterator.php');

			$tbl = new CoursesTableMember($this->_db);

			$results = array();

			if (($data = $tbl->find($filters)))
			{
				foreach ($data as $key => $result)
				{
					$results[$result->user_id] = new CoursesModelMember($result, $this->get('id'));
				}
			}

			$this->_members = $results; //new CoursesModelIterator($results);
		}

		return $this->_members;
	}

	/**
	 * Check if the current user is enrolled
	 *
	 * @return     boolean
	 */
	public function date($scope=null, $scope_id=null)
	{
		if (!isset($this->_date)
		 || ((int) $this->_date->get('scope_id') != (int) $scope_id || (string) $this->_date->get('scope') != (string) $scope))
		{
			$this->_date = new CoursesModelSectionDate(null);

			foreach ($this->dates() as $dt)
			{
				if ($dt->get('scope') == $scope
				 && $dt->get('scope_id') == $scope_id)
				{
					$this->_date = $dt;
				}
			}
		}
		return $this->_date;
	}

	/**
	 * Get a list of units for an offering
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 *
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $clear   Force a new dataset?
	 * @return     mixed
	 */
	public function dates($filters=array(), $clear=false)
	{
		if (!isset($filters['section_id']))
		{
			$filters['section_id'] = (int) $this->get('id');
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableSectionDate($this->_db);

			return $tbl->count($filters);
		}

		if (!($this->_dates instanceof CoursesModelIterator) || $clear)
		{
			$tbl = new CoursesTableSectionDate($this->_db);

			if (($results = $tbl->find($filters)))
			{
				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesModelSectionDate($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_dates = new CoursesModelIterator($results);
		}

		return $this->_dates;
	}

	/**
	 * Add one or more user IDs or usernames to the managers list
	 *
	 * @param     array $value List of IDs or usernames
	 * @return    void
	 */
	public function add($data = array(), $role_id='student')
	{
		if (!is_array($data))
		{
			$data = array($data);
		}
		$role = new CoursesTableRole($this->_db);
		$role->load($role_id);
		if (is_string($role_id))
		{
			$role_id = $role->get('id');
		}
		if (!$this->get('course_id'))
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');
			$offering = CoursesModelOffering::getInstance($this->get('offering_id'));
			$this->set('course_id', $offering->get('course_id'));
		}
		foreach ($data as $result)
		{
			$user_id = (int) $this->_userId($result);

			// Create the entry
			$model = CoursesModelMember::getInstance($user_id, $this->get('course_id'), $this->get('offering_id'), $this->get('id'));
			$model->set('user_id', $user_id);
			$model->set('course_id', $this->get('course_id'));
			$model->set('offering_id', $this->get('offering_id'));
			$model->set('section_id', $this->get('id'));
			$model->set('role_id', $role_id);
			if ($role->get('alias') == 'student')
			{
				$model->set('student', 1);
			}
			if (!$model->store())
			{
				$this->setError($model->getError());
				continue;
			}

			// Append to the members list
			if (isset($this->_members) && is_array($this->_members))
			{
				$this->_members[$user_id] = $model;
			}
		}
	}

	/**
	 * Remove one or more user IDs or usernames to the managers list
	 *
	 * @param     array $value List of IDs or usernames
	 * @return    void
	 */
	public function remove($data = array())
	{
		if (!is_array($data))
		{
			$data = array($data);
		}
		if (!$this->get('course_id'))
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');
			$offering = CoursesModelOffering::getInstance($this->get('offering_id'));
			$this->set('course_id', $offering->get('course_id'));
		}
		foreach ($data as $result)
		{
			$user_id = $this->_userId($result);

			$model = CoursesModelMember::getInstance($user_id, $this->get('course_id'), $this->get('offering_id'), $this->get('id'));
			if (!$model->exists())
			{
				$this->setError(JText::sprintf('Entry for user #%s, course #%s, offering #%s, section #%s not found.', $user_id, $this->get('course_id'), $this->get('offering_id'), $this->get('id')));
				continue;
			}
			if (!$model->delete())
			{
				$this->setError($model->getError());
				continue;
			}

			if (isset($this->_members[$user_id]))
			{
				unset($this->_members[$user_id]);
			}
		}
	}

	/**
	 * Return an ID for a user
	 *
	 * @param     mixed $user User ID or username
	 * @return    integer
	 */
	private function _userId($user)
	{
		if (is_numeric($user))
		{
			return $user;
		}

		$this->_db->setQuery("SELECT id FROM #__users WHERE username=" . $this->_db->Quote($user));

		if (($result = $this->_db->loadResult()))
		{
			return $result;
		}

		return 0;
	}

	/**
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		$isNew = ($this->get('id') ? false : true);

		$value = parent::store($check);

		$this->importPlugin('courses')
		     ->trigger('onAfterSaveSection', array($this, $isNew));

		if ($isNew)
		{
			$this->log($this->get('id'), $this->_scope, 'create');
		}

		return $value;
	}

	/**
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function delete()
	{
		// Remove associated date data
		$sd = new CoursesTableSectionDate($this->_db);
		if (!$sd->deleteBySection($this->get('id')))
		{
			$this->setError($sd->getError());
			return false;
		}

		// Remove associated member data
		$sm = new CoursesTableMember($this->_db);
		if (!$sm->deleteBySection($this->get('id')))
		{
			$this->setError($sm->getError());
			return false;
		}

		$value = parent::delete();

		$this->importPlugin('courses')
		     ->trigger('onAfterDeleteSection', array($this));

		return $value;
	}

	/**
	 * Check if the current user is enrolled
	 *
	 * @return     boolean
	 */
	public function code($code=null)
	{
		if (!isset($this->_code)
		 || ($code !== null && (string) $this->_code->get('code') != $code))
		{
			$this->_code = null;

			if (isset($this->_codes))
			{
				foreach ($this->_codes as $c)
				{
					if ($c->get('code') == $code)
					{
						$this->_code = $c;
					}
				}
			}
		}

		if (!$this->_code)
		{
			$this->_code = new CoursesModelSectionCode($code, $this->get('id'));
		}

		return $this->_code;
	}

	/**
	 * Get a list of units for an offering
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 *
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $clear   Force a new dataset?
	 * @return     mixed
	 */
	public function codes($filters=array(), $clear=false)
	{
		if (!isset($filters['section_id']))
		{
			$filters['section_id'] = (int) $this->get('id');
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableSectionCode($this->_db);

			return $tbl->count($filters);
		}

		if (!($this->_codes instanceof CoursesModelIterator) || $clear)
		{
			$tbl = new CoursesTableSectionCode($this->_db);

			if (($results = $tbl->find($filters)))
			{
				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesModelSectionCode($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_codes = new CoursesModelIterator($results);
		}

		return $this->_codes;
	}

	/**
	 * Generate a coupon code
	 *
	 * @return    string
	 */
	public function generateCode()
	{
		$chars = '023456789ABCDEFGHJKLMNOPQRSTUVWXYZ'; // no 1 or I
		$res = '';
		for ($i = 0; $i < 10; $i++)
		{
			$res .= $chars[mt_rand(0, strlen($chars)-1)];
		}
		return $res;
	}

	/**
	 * Generate coupon codes
	 *
	 * @param     integer $num Number of codes to generate
	 * @return    array
	 */
	public function generateCodes($num=1)
	{
		$codes = array();
		for ($i = 0; $i < $num; $i++)
		{
			$codes[] = $this->generateCode();
		}
		return $codes;
	}

	/**
	 * Get section badge
	 *
	 * @return     obj
	*/
	public function badge()
	{
		if (!isset($this->_badge))
		{
			$this->_badge = CoursesModelSectionBadge::loadBySectionId($this->get('id'));
		}

		return $this->_badge;
	}

	/**
	 * Mark this section as the default for this offering
	 *
	 * @return  boolean
	 */
	public function makeDefault()
	{
		if (!$this->exists() || !$this->get('offering_id'))
		{
			return true;
		}

		$sections = $this->_tbl->find(array('offering_id' => $this->get('offering_id')));
		foreach ($sections as $section)
		{
			$section = new CoursesModelSection($section);
			$section->set('is_default', 0);
			$section->store(false);
		}

		$this->set('is_default', 1);

		return $this->store(false);
	}

	/**
	 * Get a param value
	 *
	 * @param	   string $key     Property to return
	 * @param	   mixed  $default Default value to return
	 * @return     mixed
	 */
	public function params($key='', $default=null)
	{
		if (!($this->_params instanceof JRegistry))
		{
			$this->_params = new JRegistry($this->get('params'));
		}
		if ($key)
		{
			return $this->_params->get((string) $key, $default);
		}
		return $this->_params;
	}

	/**
	 * Get the section logo
	 *
	 * @param      string $rtrn Property to return
	 * @return     string
	 */
	public function logo($rtrn='')
	{
		$rtrn = strtolower(trim($rtrn));

		// Return just the file name
		if ($rtrn == 'file')
		{
			return $this->params('logo');
		}

		// We need the course ID
		if (!$this->get('course_id'))
		{
			$offering = CoursesModelOffering::getInstance($this->get('offering_id'));
			$this->set('course_id', $offering->get('course_id'));
		}

		// Build the path
		$path = '/' . trim($this->config('uploadpath', '/site/courses'), '/') . '/' . $this->get('course_id') . '/sections/' . $this->get('id');

		// Return just the upload path?
		if ($rtrn == 'path')
		{
			return $path;
		}

		// Do we have a logo set?
		if ($file = $this->params('logo'))
		{
			// Return the web path to the image
			$path .= '/' . $file;
			if (file_exists(JPATH_ROOT . $path))
			{
				$path = str_replace('/administrator', '', \JURI::base(true)) . $path;
			}
			return $path;
		}

		return '';
	}
}