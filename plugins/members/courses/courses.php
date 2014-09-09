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

/**
 * Members Plugin class for courses
 */
class plgMembersCourses extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @return     array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		$areas = array();

		if ($user->get('id') == $member->get('uidNumber'))
		{
			$areas['courses'] = JText::_('PLG_MEMBERS_COURSES');
			$areas['icon'] = 'f09c';
		}
		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @param      string  $option Component name
	 * @param      string  $areas  Plugins to return data
	 * @return     array   Return array of html
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				$returnhtml = false;
			}
		}

		$arr = array(
			'html' => '',
			'metadata' => array(
				'count' => 0
			)
		);

		$this->database = JFactory::getDBO();
		$this->member = $member;

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');
		$model = CoursesModelOffering::getInstance();
		$roles = $model->roles();

		$hasRoles = 0;
		if ($roles)
		{
			foreach ($roles as $i => $role)
			{
				$roles[$i]->total = $this->_getData('count', $role->alias);
				if ($roles[$i]->total > 0)
				{
					$hasRoles++;
				}
				$arr['metadata']['count'] += $roles[$i]->total;
			}
		}

		// Build the HTML
		if ($returnhtml)
		{
			$this->app = JFactory::getApplication();
			$this->jconfig = JFactory::getConfig();

			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'display'
				)
			);
			$view->option   = $option;
			$view->member   = $member;
			$view->roles    = $roles;
			$view->hasRoles = $hasRoles;

			$view->filters = array();
			$view->filters['limit'] = $this->app->getUserStateFromRequest(
				$option . '.plugin.courses.limit',
				'limit',
				$this->jconfig->getValue('config.list_limit'),
				'int'
			);
			$view->filters['start'] = $this->app->getUserStateFromRequest(
				$option . '.plugin.courses.limitstart',
				'limitstart',
				0,
				'int'
			);
			$view->filters['task'] = strtolower(JRequest::getVar('action', ''));
			$view->filters['sort'] = strtolower(JRequest::getWord('sort', 'enrolled'));
			if (!in_array($view->filters['sort'], array('enrolled', 'title')))
			{
				$view->filters['sort'] = 'enrolled';
			}

			$view->total   = 0;
			$view->results = null;
			$view->active  = null;
			$view->results = null;

			if ($view->hasRoles >= 1)
			{
				$found = false;
				if ($view->filters['task'])
				{
					foreach ($roles as $i => $role)
					{
						if ($view->filters['task'] == $role->alias && $role->total > 0)
						{
							$found = true;
						}
					}
				}
				if (!$found)
				{
					foreach ($roles as $i => $role)
					{
						if ($role->total > 0)
						{
							$view->filters['task'] = $role->alias;
							break;
						}
					}
				}
			}
			foreach ($view->roles as $i => $role)
			{
				if ($view->filters['task'] == $role->alias)
				{
					$view->active  = $view->roles[$i];
					$view->total   = $view->roles[$i]->total;
				}
			}

			if (!is_null($view->active))
			{
				$view->results = $this->_getData('list', $view->active->alias, $view->filters);
			}

			jimport('joomla.html.pagination');
			$view->pageNav = new JPagination(
				$view->total,
				$view->filters['start'],
				$view->filters['limit']
			);

			if ($this->getError())
			{
				foreach ($this->getError() as $error)
				{
					$view->setError($error);
				}
			}

			$arr['html'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @return     array   Return array of html
	 */
	private function _getData($what='count', $who=null, $filters=array())
	{
		if (!isset($filters['start']))
		{
			$filters['start'] = 0;
		}
		if (!isset($filters['limit']))
		{
			$filters['limit'] = 25;
		}
		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'enrolled';
		}

		$results = null;

		$who = strtolower(trim($who));

		switch ($who)
		{
			case 'student':
				$now = JFactory::getDate()->toSql();
				if ($what == 'count')
				{
					$this->database->setQuery("SELECT COUNT(*)
						FROM `#__courses` AS c
							JOIN `#__courses_members` AS m ON m.course_id=c.id
							LEFT JOIN `#__courses_offerings` AS o ON o.id=m.offering_id
							LEFT JOIN `#__courses_offering_sections` AS s on s.id=m.section_id
							LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
						WHERE c.state=1 AND m.user_id=" . (int) $this->member->get('uidNumber') . " AND m.student=1
							AND (s.publish_down='0000-00-00 00:00:00' OR s.publish_down < " . $this->database->quote($now) .") AND o.state=1");
					$results = $this->database->loadResult();
				}
				else
				{
					$this->database->setQuery("SELECT c.id, c.state, c.alias, c.title, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title, s.is_default,
						m.enrolled, s.publish_up AS starts, s.publish_down AS ends
						FROM `#__courses` AS c
							JOIN `#__courses_members` AS m ON m.course_id=c.id
							LEFT JOIN `#__courses_offerings` AS o ON o.id=m.offering_id
							LEFT JOIN `#__courses_offering_sections` AS s on s.id=m.section_id
							LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
						WHERE c.state=1 AND m.user_id=" . (int) $this->member->get('uidNumber') . " AND m.student=1
							AND (s.publish_down='0000-00-00 00:00:00' OR s.publish_down < " . $this->database->quote($now) .") AND o.state=1
						ORDER BY " . $filters['sort'] . " ASC LIMIT " . $filters['start'] . "," . $filters['limit']);
					$results = $this->database->loadObjectList();
				}
			break;

			case 'manager':
				if ($what == 'count')
				{
					$this->database->setQuery("SELECT COUNT(*)
							FROM `#__courses` AS c
							JOIN `#__courses_members` AS m ON m.course_id=c.id
							LEFT JOIN `#__courses_offerings` AS o ON o.id=m.offering_id
							LEFT JOIN `#__courses_offering_sections` AS s on s.id=m.section_id
							LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
							WHERE c.state IN (1, 3) AND m.user_id=" . (int) $this->member->get('uidNumber') . " AND m.student=0 AND r.alias='manager'");
					$results = $this->database->loadResult();
				}
				else
				{
					$this->database->setQuery("SELECT c.id, c.state, c.alias, c.title, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title, s.is_default,
							m.enrolled, r.alias AS role_alias, r.title AS role_title, s.publish_up AS starts, s.publish_down AS ends
							FROM `#__courses` AS c
							JOIN `#__courses_members` AS m ON m.course_id=c.id
							LEFT JOIN `#__courses_offerings` AS o ON o.id=m.offering_id
							LEFT JOIN `#__courses_offering_sections` AS s on s.id=m.section_id
							LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
						WHERE c.state IN (1, 3) AND m.user_id=" . (int) $this->member->get('uidNumber') . " AND m.student=0 AND r.alias='manager'
						ORDER BY " . $filters['sort'] . " ASC LIMIT " . $filters['start'] . "," . $filters['limit']);
					$results = $this->database->loadObjectList();
				}
			break;

			case 'instructor':
				if ($what == 'count')
				{
					$this->database->setQuery("SELECT COUNT(*)
						FROM `#__courses` AS c
						JOIN `#__courses_members` AS m ON m.course_id=c.id
						LEFT JOIN `#__courses_offerings` AS o ON o.id=m.offering_id
						LEFT JOIN `#__courses_offering_sections` AS s on s.id=m.section_id
						LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
						WHERE c.state IN (1, 3) AND m.user_id=" . (int) $this->member->get('uidNumber') . " AND m.student=0 AND r.alias=" . $this->database->Quote('instructor'));
					$results = $this->database->loadResult();
				}
				else
				{
					$this->database->setQuery("SELECT c.id, c.state, c.alias, c.title, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title, s.is_default,
						m.enrolled, r.alias AS role_alias, r.title AS role_title, s.publish_up AS starts, s.publish_down AS ends
						FROM `#__courses` AS c
						JOIN `#__courses_members` AS m ON m.course_id=c.id
						LEFT JOIN `#__courses_offerings` AS o ON o.id=m.offering_id
						LEFT JOIN `#__courses_offering_sections` AS s on s.id=m.section_id
						LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
						WHERE c.state IN (1, 3) AND m.user_id=" . (int) $this->member->get('uidNumber') . " AND m.student=0 AND r.alias=" . $this->database->Quote('instructor') . "
						ORDER BY " . $filters['sort'] . " ASC LIMIT " . $filters['start'] . "," . $filters['limit']);
					$results = $this->database->loadObjectList();
				}
			break;

			case 'ta':
				if ($what == 'count')
				{
					$this->database->setQuery("SELECT COUNT(*)
						FROM `#__courses` AS c
						JOIN `#__courses_members` AS m ON m.course_id=c.id
						LEFT JOIN `#__courses_offerings` AS o ON o.id=m.offering_id
						LEFT JOIN `#__courses_offering_sections` AS s on s.id=m.section_id
						LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
						WHERE c.state IN (1, 3) AND m.user_id=" . (int) $this->member->get('uidNumber') . " AND m.student=0 AND r.alias=" . $this->database->Quote('ta'));
					$results = $this->database->loadResult();
				}
				else
				{
					$this->database->setQuery("SELECT c.id, c.state, c.alias, c.title, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title, s.is_default,
						m.enrolled, r.alias AS role_alias, r.title AS role_title, s.publish_up AS starts, s.publish_down AS ends
						FROM `#__courses` AS c
						JOIN `#__courses_members` AS m ON m.course_id=c.id
						LEFT JOIN `#__courses_offerings` AS o ON o.id=m.offering_id
						LEFT JOIN `#__courses_offering_sections` AS s on s.id=m.section_id
						LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
						WHERE c.state IN (1, 3) AND m.user_id=" . (int) $this->member->get('uidNumber') . " AND m.student=0 AND r.alias=" . $this->database->Quote('ta') . "
						ORDER BY " . $filters['sort'] . " ASC LIMIT " . $filters['start'] . "," . $filters['limit']);
					$results = $this->database->loadObjectList();
				}
			break;

			default:
				if ($what == 'count')
				{
					$this->database->setQuery("SELECT COUNT(*)
						FROM `#__courses` AS c
						JOIN `#__courses_members` AS m ON m.course_id=c.id
						LEFT JOIN `#__courses_offerings` AS o ON o.id=m.offering_id
						LEFT JOIN `#__courses_offering_sections` AS s on s.id=m.section_id
						LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
						WHERE c.state IN (1, 3) AND m.user_id=" . (int) $this->member->get('uidNumber') . " AND m.student=0 AND r.alias=" . $this->database->Quote($who));
					$results = $this->database->loadResult();
				}
				else
				{
					$this->database->setQuery("SELECT c.id, c.state, c.alias, c.title, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title, s.is_default,
						m.enrolled, r.alias AS role_alias, r.title AS role_title, s.publish_up AS starts, s.publish_down AS ends
						FROM `#__courses` AS c
						JOIN `#__courses_members` AS m ON m.course_id=c.id
						LEFT JOIN `#__courses_offerings` AS o ON o.id=m.offering_id
						LEFT JOIN `#__courses_offering_sections` AS s on s.id=m.section_id
						LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
						WHERE c.state IN (1, 3) AND m.user_id=" . (int) $this->member->get('uidNumber') . " AND r.alias=" . $this->database->Quote($who) . "
						ORDER BY " . $filters['sort'] . " ASC LIMIT " . $filters['start'] . "," . $filters['limit']);
					$results = $this->database->loadObjectList();
				}
			break;
		}
		return $results;
	}

	/**
	 * Return a list of categories
	 *
	 * @return     array
	 */
	public function onMembersContributionsAreas()
	{
		return array(
			'courses' => JText::_('PLG_MEMBERS_COURSES')
		);
	}

	/**
	 * Build SQL for returning the count of the number of contributions
	 *
	 * @param      string $user_id  Field to join on user ID
	 * @param      string $username Field to join on username
	 * @return     string
	 */
	public function onMembersContributionsCount($user_id='m.uidNumber', $username='m.username')
	{
		$query = "SELECT COUNT(*)
				FROM `#__courses` AS c
				JOIN `#__courses_members` AS m ON m.course_id=c.id
				LEFT JOIN `#__courses_offerings` AS o ON o.id=m.offering_id
				LEFT JOIN `#__courses_offering_sections` AS s on s.id=m.section_id
				LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
				WHERE m.user_id=" . (int) $user_id . " AND m.student=0 AND r.alias='instructor' AND c.state=1";

		return $query;
	}

	/**
	 * Return either a count or an array of the member's contributions
	 *
	 * @param      object  $member     Current member
	 * @param      string  $option     Component name
	 * @param      string  $authorized Authorization level
	 * @param      integer $limit      Number of record to return
	 * @param      integer $limitstart Record return start
	 * @param      string  $sort       Field to sort records on
	 * @param      array   $areas      Areas to return data for
	 * @return     array
	 */
	public function onMembersContributions($member, $option, $limit=0, $limitstart=0, $sort, $areas=null)
	{
		$database = JFactory::getDBO();

		if (is_array($areas) && $limit)
		{
			if (!isset($areas[$this->_name])
			  && !in_array($this->_name, $areas)
			  && !array_intersect($areas, array_keys($this->onMembersContributionsAreas())))
			{
				return array();
			}
		}

		// Do we have a member ID?
		if ($member instanceof \Hubzero\User\Profile)
		{
			if (!$member->get('uidNumber'))
			{
				return array();
			}
			else
			{
				$uidNumber = $member->get('uidNumber');
				$username  = $member->get('username');
			}
		}
		else
		{
			if (!$member->uidNumber)
			{
				return array();
			}
			else
			{
				$uidNumber = $member->uidNumber;
				$username  = $member->username;
			}
		}

		include_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'course.php');

		// Instantiate some needed objects
		$tbl = new CoursesTableCourse($database);

		// Build query
		if (!$limit)
		{
			$database->setQuery($this->onMembersContributionsCount($uidNumber));
			return $database->loadResult();
		}
		else
		{
			//$rows = $tbl->getUserCourses($uidNumber, $type='instructor', $limit, $limitstart);
			$query = "SELECT c.id, c.alias, c.title, c.blurb, m.enrolled, s.publish_up AS starts, s.publish_down AS ends, r.alias AS role, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title, s.is_default
					FROM `#__courses` AS c
					JOIN `#__courses_members` AS m ON m.course_id=c.id
					LEFT JOIN `#__courses_offerings` AS o ON o.id=m.offering_id
					LEFT JOIN `#__courses_offering_sections` AS s on s.id=m.section_id
					LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
					WHERE m.user_id=" . $database->Quote($uidNumber) . " AND m.student=0 AND r.alias='instructor' AND c.state=1";
			$database->setQuery($query);
			$rows = $database->loadObjectList();

			if ($rows)
			{
				foreach ($rows as $key => $row)
				{
					$rows[$key]->href = JRoute::_('index.php?option=com_courses&gid=' . $row->alias);
					$rows[$key]->text = $row->blurb;
					$rows[$key]->section = 'courses';
				}
			}

			return $rows;
		}
	}
}
