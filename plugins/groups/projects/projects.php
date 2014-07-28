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
 * Groups Plugin class for projects
 */
class plgGroupsProjects extends \Hubzero\Plugin\Plugin
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

		$this->_config = JComponentHelper::getParams('com_projects');
		$this->_database = JFactory::getDBO();
		$this->_setup_complete = $this->_config->get('confirm_step', 0) ? 3 : 2;
		$this->_juser = JFactory::getUser();
		$this->_total = 0;
		$this->_projects = array();
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name'             => $this->_name,
			'title'            => JText::_('PLG_GROUPS_PROJECTS'),
			'default_access'   => $this->params->get('plugin_access','members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'             => 'f03f'
		);
		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param      object  $group      Current group
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
	 */
	public function onGroup($group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		$return = 'html';
		$active = $this->_name;

		// The output array we're returning
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			if (!in_array($this_area['name'], $areas))
			{
				$return = 'metadata';
			}
		}

		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.php');

		// Set filters
		$filters = array();
		$filters['group']  = $group->get('gidNumber');
		$filters['mine']     = 1;

		// Get a record count
		$obj = new Project($this->_database);
		$this->_projects = $obj->getGroupProjectIds($group->get('gidNumber'), $this->_juser->get('id'));

		//if we want to return content
		if ($return == 'html')
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//Create user object
			$juser = JFactory::getUser();

			//get the group members
			$members = $group->get('members');

			// Set some variables so other functions have access
			$this->juser      = $juser;
			$this->authorized = $authorized;
			$this->members    = $members;
			$this->group      = $group;
			$this->option     = $option;
			$this->action     = $action;

			//if set to nobody make sure cant access
			if ($group_plugin_acl == 'nobody')
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if ($juser->get('guest')
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
			{
				$url = JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&active='.$active, false, true);

				$this->redirect(
					JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($url)),
					JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
					'warning'
				);
				return;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array($juser->get('id'), $members) && $group_plugin_acl == 'members' && $authorized != 'admin')
			{
				$arr['html'] = '<p class="info">'
					. JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
				return $arr;
			}

			// Load classes
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'html.php');
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'imghandler.php');

			// Which view
			$task = $action ? strtolower(trim($action)) : JRequest::getVar('action', '');

			switch ($task)
			{
				case 'all':     $arr['html'] = $this->_view('all');   break;
				case 'owned':   $arr['html'] = $this->_view('owned'); break;
				case 'updates': $arr['html'] = $this->_updates();     break;
				default:        $arr['html'] = $this->_view('all');   break;
			}
		}

		//get meta
		$arr['metadata'] = array();

		//return total message count
		$arr['metadata']['count'] = count($this->_projects);

		// Return the output
		return $arr;
	}

	/**
	 * View entries
	 *
	 * @param      string $which The type of entries to display
	 * @return     string
	 */
	protected function _view($which = 'owned')
	{
		// Set filters
		$filters = array();
		$filters['mine']     = 1;
		$filters['updates']  = 1;
		$filters['sortby']   = JRequest::getVar('sortby', 'status');
		$filters['getowner'] = 1;
		$filters['sortdir']  = JRequest::getVar('sortdir', 'DESC');

		// Build the final HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'groups',
				'element' => 'projects',
				'name'    => 'browse'
			)
		);

		$obj = new Project($this->_database);
		if ($which == 'all')
		{
			$filters['which'] = 'owned';
			$view->owned = $obj->getGroupProjects($this->group->get('gidNumber'),
				$this->_juser->get('id'), $filters, $this->_setup_complete);

			$filters['which'] = 'other';
			$view->rows = $obj->getGroupProjects($this->group->get('gidNumber'),
				$this->_juser->get('id'), $filters, $this->_setup_complete);
		}
		else
		{
			// Get records
			$options = array('all', 'owned', 'other');
			if (!in_array($which, $options))
			{
				$which = 'owned';
			}
			$filters['which'] = $which;
			$view->rows = $obj->getGroupProjects($this->group->get('gidNumber'),
				$this->_juser->get('id'), $filters, $this->_setup_complete);
		}

		// Get counts
		$view->projectcount = count($this->_projects);
		$view->newcount = $obj->getUpdateCount ($this->_projects, $this->_juser->get('id'));

		$view->which   = $which;
		$view->juser   = $this->_juser;
		$view->filters = $filters;
		$view->config  = $this->_config;
		$view->option  = 'com_projects';
		$view->group   = $this->group;
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
				'folder'  => 'groups',
				'element' => 'projects',
				'name'    => 'updates'
			)
		);

		// Set filters
		$filters = array();
		$filters['mine']     = 1;
		$filters['updates']  = 1;
		$filters['sortby']   = JRequest::getVar('sortby', 'title');
		$filters['getowner'] = 1;
		$filters['sortdir']  = JRequest::getVar('sortdir', 'ASC');

		// Get all projects group has access to
		$obj = new Project($this->_database);
		$projects = $obj->getGroupProjectIds($this->group->get('gidNumber'), $this->_juser->get('id'));
		$view->projectcount = count($projects);

		$projects = $obj->getGroupProjectIds($this->group->get('gidNumber'),
			$this->_juser->get('id'), 1); // active only
		$view->newcount = $obj->getUpdateCount($projects, $this->_juser->get('id'));

		// Get activity class
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.activity.php');
		$objAC = new ProjectActivity($this->_database);

		$afilters = array();
		$view->total = $objAC->getActivities(0, $afilters, 1, $this->_juser->get('id'), $projects);
		$view->limit = 25;

		$afilters['limit'] = JRequest::getVar('limit', 25, 'request');
		$view->filters = $afilters;

		$activities = $objAC->getActivities(0, $afilters, 0, $this->_juser->get('id'), $projects);
		$view->activities = $this->prepActivities($activities, 'com_projects',
			$this->_juser->get('id'), $view->filters, $view->limit);

		$view->uid      = $this->_juser->get('id');
		$view->config   = $this->_config;
		$view->option   = 'com_projects';
		$view->database = $this->_database;
		$view->group    = $this->group;
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
						$pa = $objAC->getActivities($a->projectid, $filters, 0, $uid);
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

	/**
	 * Browse entries
	 *
	 * @return     string
	 */
	private function browse()
	{
		//include a project helper file for handing project picture
		$include = JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'imghandler.php';
		if (is_file($include))
		{
			include_once($include);
		}

		//create plugin view
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'groups',
				'element' => 'projects',
				'name'    => 'browse'
			)
		);

		//instatiate db
		$db = JFactory::getDBO();

		//select all projects either belonging to the group or the group is collaborating on
		$sql = "SELECT DISTINCT
					p.id as id,
					p.alias as alias,
					p.title as title,
					p.picture as picture,
					p.type as type,
					p.private as private,
					p.created as created,
					p.owned_by_user as owned_by_user,
					p.owned_by_group as owned_by_group
				FROM #__projects as p, #__project_owners as po
				WHERE p.id=po.projectid
				AND p.state=1
				AND po.groupid=" . $db->quote($this->group->get('gidNumber')) . " ORDER BY p.owned_by_group DESC";

		//set the query
		$db->setQuery($sql);

		//get the results
		$projects = $db->loadAssocList();

		// Get the component parameters
		$view->project_params = JComponentHelper::getParams('com_projects');

		//push vars to the view
		$view->projects = $projects;
		$view->group    = $this->group;
		$view->user     = $this->juser;

		//return the view
		return $view->loadTemplate();
	}
}

