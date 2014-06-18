<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Members Plugin class for projects
 */
class plgMembersProjects extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_config = JComponentHelper::getParams('com_projects');
		$this->_database = JFactory::getDBO();
		$this->_setup_complete = $this->_config->get('confirm_step', 0) ? 3 : 2;
		$this->_juser = JFactory::getUser();
		$this->_filters = array();
		$this->_total = 0;
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onMembersAreas($user, $member)
	{
		//default areas returned to nothing
		$areas = array();

		//if this is the logged in user show them
		if ($user->get('id') == $member->get('uidNumber'))
		{
			$areas['projects'] = JText::_('PLG_MEMBERS_PROJECTS');
			$areas['icon'] = 'f03f';
		}
		return $areas;
	}

	/**
	 * Perform actions when viewing a member profile
	 *
	 * @param      object $user   Current user
	 * @param      object $member Current member page
	 * @param      string $option Start of records to pull
	 * @param      array  $areas  Active area(s)
	 * @return     array
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;
		$returnmeta = true;

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
			'html'     => '',
			'metadata' => ''
		);

		// Load classes
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'html.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'imghandler.php');

		// Set filters
		$filters = array();
		$filters['mine']     = 1;
		$filters['updates']  = 1;
		$filters['sortby']   = JRequest::getVar('sortby', 'title');
		$filters['getowner'] = 1;
		$filters['sortdir']  = JRequest::getVar('sortdir', 'ASC');

		// Get a record count
		$obj = new Project($this->_database);
		$total = $obj->getCount($filters, false, $user->get('id'), 0, $this->_setup_complete);
		$this->_filters = $filters;
		$this->_total = $total;
		$this->_juser = $user;

		// Add stylesheet
		\Hubzero\Document\Assets::addPluginStylesheet('members', 'projects');

		if ($returnhtml)
		{
			// Which view
			$task = JRequest::getVar('action', '');

			switch ($task)
			{
				case 'all':     $arr['html'] = $this->_view('all');   break;
				case 'group':   $arr['html'] = $this->_view('group'); break;
				case 'owned':   $arr['html'] = $this->_view('owned'); break;
				case 'updates': $arr['html'] = $this->_updates();     break;
				default:        $arr['html'] = $this->_view('all');   break;
			}
		}

		//get meta
		$arr['metadata'] = array();

		$prefix = ($user->get('id') == $member->get("uidNumber")) ? 'I have' : $member->get('name') . ' has';
		$title = $prefix . ' ' . $this->_total . ' active projects.';

		//return total message count
		$arr['metadata']['count'] = $this->_total;

		return $arr;
	}

	/**
	 * View entries
	 *
	 * @param      string $which The type of entries to display
	 * @return     string
	 */
	protected function _view($which = 'all')
	{
		// Build the final HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'members',
				'element' => 'projects',
				'name'    => 'browse'
			)
		);

		$obj = new Project($this->_database);
		$projects = $obj->getUserProjectIds($this->_juser->get('id'));
		$view->newcount = $obj->getUpdateCount($projects, $this->_juser->get('id'));

		if ($which == 'all')
		{
			$this->_filters['which'] = 'owned';
			$view->owned = $obj->getRecords($this->_filters, false, $this->_juser->get('id'), 0, $this->_setup_complete);

			$this->_filters['which'] = 'other';
			$view->rows = $obj->getRecords($this->_filters, false, $this->_juser->get('id'), 0, $this->_setup_complete);
		}
		else
		{
			// Get records
			$options = array('owned', 'other', 'group');
			if (!in_array($which, $options))
			{
				$which = 'owned';
			}
			$this->_filters['which'] = $which;
			$view->rows = $obj->getRecords($this->_filters, false, $this->_juser->get('id'), 0, $this->_setup_complete);
		}

		$view->which   = $which;
		$view->total   = $this->_total;
		$view->juser   = $this->_juser;
		$view->filters = $this->_filters;
		$view->config  = $this->_config;
		$view->option  = 'com_projects';
		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}

	/**
	 * Display updates
	 *
	 * @return     string
	 */
	protected function _updates()
	{
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.comment.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.todo.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.microblog.php');

		// Build the final HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'members',
				'element' => 'projects',
				'name'    => 'updates'
			)
		);

		// Get all projects user has access to
		$obj = new Project($this->_database);
		$projects = $obj->getUserProjectIds($this->_juser->get('id'));
		$view->newcount = $obj->getUpdateCount ($projects, $this->_juser->get('id'));

		// Get activity class
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.activity.php');
		$objAC = new ProjectActivity($this->_database);

		$afilters = array();
		$view->total = $objAC->getActivities (0, $afilters, 1, $this->_juser->get('id'), $projects);
		$view->limit = 25;
		$afilters['limit'] = JRequest::getVar('limit', 25, 'request');
		$view->filters = $afilters;
		$activities = $objAC->getActivities (0, $afilters, 0, $this->_juser->get('id'), $projects);
		$view->activities = $this->prepActivities ($activities, 'com_projects', $this->_juser->get('id'), $view->filters, $view->limit);

		$view->projectcount = $this->_total;
		$view->uid          = $this->_juser->get('id');
		$view->config       = $this->_config;
		$view->option       = 'com_projects';
		$view->database     = $this->_database;
		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}

	/**
	 * View entries
	 *
	 * @param      string  $activities The type of entries to display
	 * @param      string  $option     Component name
	 * @param      integer $uid        User ID
	 * @param      array   $filters    Filters to apply
	 * @param      integer $limit      Number of records to display
	 * @return     string
	 */
	protected function prepActivities($activities, $option, $uid, $filters, $limit)
	{
		// Get latest activity
		$objAC = new ProjectActivity($this->_database);
		$objM  = new ProjectMicroblog($this->_database);
		$objC  = new ProjectComment($this->_database);
		$objTD = new ProjectTodo($this->_database);

		// Collectors
		$shown   = array();
		$newc    = array();
		$skipped = array();
		$prep    = array();

		// Loop through activities
		if ($activities && count($activities) > 0)
		{
			foreach ($activities as $a)
			{
				// Is this a comment?
				if ($a->class == 'quote')
				{
					// Get comment
					$c = $objC->getComments(NULL, NULL, $a->id);

					// Bring up commented item
					$needle  = array('id' => $c->parent_activity);
					$key     = ProjectsHTML::myArraySearch($needle, $activities);
					$shown[] = $a->id;
					if (!$key)
					{
						// get and add parent activity
						$filters['id'] = $c->parent_activity;
						$pa = $objAC->getActivities ($a->projectid, $filters, 0, $uid);
						if ($pa && count($pa) > 0)
						{
							$a = $pa[0];
						}
					}
					else
					{
						$a = $activities[$key];
					}
					$a->new = isset($c->newcount) ? $c->newcount : 0;
				}

				if (!in_array($a->id, $shown))
				{
					$shown[]   = $a->id;
					$class     = $a->class ? $a->class : 'activity';
					$timeclass = '';

					// Display hyperlink
					if ($a->highlighted && $a->url)
					{
						$a->activity = str_replace($a->highlighted, '<a href="' . $a->url . '">' . $a->highlighted . '</a>', $a->activity);
					}

					// Set vars
					$body = '';
					$eid  = $a->id;
					$etbl = 'activity';
					$deletable = 0;

					// Get blog entry
					if ($class == 'blog')
					{
						$blog = $objM->getEntries($a->projectid, $bfilters = array('activityid' => $a->id), $a->referenceid);
						if (!$blog)
						{
							continue;
						}
						$body = $blog ? $blog[0]->blogentry : '';
						$eid  = $blog[0]->id;
						$etbl = 'blog';
						$deletable = 1;
					}

					// Get todo item
					if ($class == 'todo')
					{
						$todo = $objTD->getTodos($a->projectid, $tfilters = array('activityid' => $a->id), $a->referenceid);
						if (!$todo)
						{
							continue;
						}
						$body = $todo ? $todo[0]->content : '';
						$eid  = $todo[0]->id;
						$etbl = 'todo';
						$deletable = 0; // Cannot delete to-do related activity
					}

					// Embed links
					if ($body)
					{
						$body = ProjectsHTML::replaceUrls($body, 'external');
					}

					// Style body text
					$ebody  = $body ? '&#58; <span class="body' : '';
					$ebody .= $body && strlen($body) > 50 ? ' newline' : '';
					$ebody .= $body ? '">' . $body . '</span>' : '';

					// Get comments
					if ($a->commentable)
					{
						$comments = $objC->getComments($eid, $etbl);
					}
					else
					{
						$comments = null;
					}

					// Is user allowed to delete item?
					$deletable = 0;

					$prep[] = array(
						'activity' => $a, 'eid' => $eid, 'etbl' => $etbl, 'body' => $ebody, 'deletable' => $deletable, 'comments' => $comments,
						'class' => $class, 'timeclass' => $timeclass, 'projectid' => $a->projectid, 'recorded' => $a->recorded
					);
				}
			}
		}

		return $prep;
	}
}
