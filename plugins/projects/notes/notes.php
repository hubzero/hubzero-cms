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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Projects Notes (wiki) plugin
 */
class plgProjectsNotes extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var	   boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Store redirect URL
	 *
	 * @var	   string
	 */
	protected $_referer = NULL;

	/**
	 * Store output message
	 *
	 * @var	   array
	 */
	protected $_message = NULL;

	/**
	 * Name of project group
	 *
	 * @var	   array
	 */
	protected $_group = NULL;

	/**
	 * Name of master scope
	 *
	 * @var	   array
	 */
	protected $_masterScope = NULL;

	/**
	 * Name of page
	 *
	 * @var	   array
	 */
	protected $_pagename = NULL;

	/**
	 * Tool record (tool wiki)
	 *
	 * @var	   array
	 */
	protected $_tool = NULL;

	/**
	 * Controller name
	 *
	 * @var	   array
	 */
	protected $_controllerName = NULL;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @return     array   Plugin name and title
	 */
	public function &onProjectAreas()
	{
		$area = array(
			'name' => 'notes',
			'title' => JText::_('COM_PROJECTS_TAB_NOTES')
		);

		return $area;
	}

	/**
	 * Event call to return count of items
	 *
	 * @param      object  $project 		Project
	 * @param      integer &$counts
	 * @return     array   integer
	 */
	public function &onProjectCount( $project, &$counts )
	{
		$database = JFactory::getDBO();

		// Load component configs
		$this->_config = JComponentHelper::getParams('com_projects');

		$group_prefix = $this->_config->get('group_prefix', 'pr-');
		$groupname = $group_prefix . $project->alias;
		$scope = 'projects' . DS . $project->alias . DS . 'notes';

		// Include note model
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects'
			. DS . 'models' . DS . 'note.php');

		// Get our model
		$this->model = new ProjectModelNote($scope, $groupname, $project->id);

		$counts['notes'] = $this->model->getNoteCount();

		return $counts;
	}

	/**
	 * Event call to return data for a specific project
	 *
	 * @param      object  $project 		Project
	 * @param      string  $option 			Component name
	 * @param      integer $authorized 		Authorization
	 * @param      integer $uid 			User ID
	 * @param      integer $msg 			Message
	 * @param      integer $error 			Error
	 * @param      string  $action			Plugin task
	 * @param      string  $areas  			Plugins to return data
	 * @param      string  $tool			Name of tool wiki belongs to
	 * @return     array   Return array of html
	 */
	public function onProject ( $project, $option, $authorized,
		$uid, $msg = '', $error = '', $action = '', $areas = null, $tool = NULL )
	{
		$returnhtml = true;

		$arr = array(
			'html'=>'',
			'metadata'=>'',
			'msg'=>'',
			'referer'=>''
		);

		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (empty($this->_area) || !in_array($this->_area['name'], $areas)) {
				return;
			}
		}

		// Is the user logged in?
		if ( !$authorized && !$project->owner )
		{
			return $arr;
		}

		// Do we have a project ID?
		if ( !is_object($project) or !$project->id )
		{
			return $arr;
		}
		else
		{
			$this->_project = $project;
		}

		// Are we returning HTML?
		if ($returnhtml)
		{
			// Load wiki language file
			$lang = JFactory::getLanguage();
			$lang->load('plg_groups_wiki');
			$lang->load('com_wiki');

			// Get database
			$database = JFactory::getDBO();

			// Set vars
			$this->_database 	= $database;
			$this->_option 		= $option;
			$this->_authorized 	= $authorized;
			$this->_uid 		= $uid;

			if ( !$this->_uid)
			{
				$juser = JFactory::getUser();
				$this->_uid = $juser->get('id');
			}
			$this->_msg = $msg;
			if ( $error)
			{
				$this->setError($error);
			}

			// Load component configs
			$this->_config = JComponentHelper::getParams('com_projects');
			$this->_group = $this->_config->get('group_prefix', 'pr-') . $this->_project->alias;

			// Incoming
			$this->_pagename = trim(JRequest::getVar('pagename', '', 'default', 'none', 2));
			$this->_masterScope = 'projects' . DS . $this->_project->alias . DS . 'notes';

			// Include note model
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects'
				. DS . 'models' . DS . 'note.php');

			// Get our model
			$this->model = new ProjectModelNote($this->_masterScope, $this->_group, $this->_project->id);

			// What's the task?
			$this->_task = $action ? $action : JRequest::getVar('action', 'view');

			// Publishing?
			if ($this->_task == 'browser')
			{
				return $this->browser();
			}

			// Import some needed libraries
			switch ($this->_task)
			{
				case 'upload':
				case 'download':
				case 'deletefolder':
				case 'deletefile':
				case 'media':
					$this->_controllerName = 'media';
				break;

				case 'history':
				case 'compare':
				case 'approve':
				case 'deleterevision':
					$this->_controllerName = 'history';
				break;

				case 'editcomment':
				case 'addcomment':
				case 'savecomment':
				case 'reportcomment':
				case 'removecomment':
				case 'comments':
					$this->_controllerName = 'comments';
					$cid = JRequest::getVar('cid', 0);
					if ($cid)
					{
						JRequest::setVar('id', $cid);
					}
				break;

				case 'delete':
				case 'edit':
				case 'save':
				case 'rename':
				case 'saverename':
				default:
					$this->_controllerName = 'page';
				break;
			}

			if (substr(strtolower($this->_pagename), 0, strlen('image:')) == 'image:'
			 || substr(strtolower($this->_pagename), 0, strlen('file:')) == 'file:')
			{
				$this->_controllerName = 'media';
				$this->_task = 'download';
			}

			if (!file_exists(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS
				. 'controllers' . DS . $this->_controllerName . '.php'))
			{
				$this->_controllerName = 'page';
			}
			// Include controller
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS
				. 'controllers' . DS . $this->_controllerName . '.php');

			// Listing/unlisting?
			if ($this->_task == 'publist' || $this->_task == 'unlist')
			{
				$arr['html'] = $this->_list();
			}
			elseif ($this->_task == 'share')
			{
				$arr['html'] = $this->_share();
			}
			else
			{
				// Display page
				$arr['html'] = $this->page();
			}
		}

		$arr['referer'] = $this->_referer;
		$arr['msg'] = $this->_message;

		// Return data
		return $arr;
	}

	/**
	 * View of project note
	 *
	 * @return     string
	 */
	public function page()
	{
		// Incoming
		$preview 	= trim(JRequest::getVar( 'preview', '' ));
		$note 		= JRequest::getVar('page', array(), 'post', 'none', 2);
		$scope 		= trim(JRequest::getVar( 'scope', $this->_masterScope ), DS);

		$pagePrefix = '';

		// Output HTML (wrap for notes)
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'notes',
				'name'		=>'wrap',
				'layout' 	=>'wrap'
			)
		);

		// Get first project note
		$view->firstNote = $this->model->getFirstNote( $pagePrefix);

		// Default view to first available note if no page is requested
		if (!$this->_pagename && $this->_task != 'new' && $this->_task != 'save')
		{
			$this->_pagename = $view->firstNote ? $view->firstNote : '';
		}

		// Are we saving?
		$save 	= $this->_task == 'save' ? 1 : 0;
		$rename = $this->_task == 'saverename' ? 1 : 0;
		$canDelete = 1;

		// Get page
		$view->page = $this->model->page($this->_pagename, $scope);
		$view->content = NULL;
		$exists = $view->page->get('id') ? true : false;

		JRequest::setVar('pagename', $this->_pagename);
		JRequest::setVar('task', $this->_task);
		JRequest::setVar('scope', $scope);
		JRequest::setVar('group_cn', $this->_group);

		JRequest::setVar('tool', $this->_tool);
		JRequest::setVar('project', $this->_project);
		JRequest::setVar('candelete', $canDelete);

		if (!$view->page->get('id') && $this->_task == 'view' && $view->page->get('namespace') != 'special')
		{
			// Output HTML (wrap for notes)
			$nview = new \Hubzero\Plugin\View(
				array(
					'folder'	=>'projects',
					'element'	=>'notes',
					'name'		=>'page',
					'layout' 	=>'doesnotexist'
				)
			);
			$nview->scope 		= $scope;
			$nview->option 		= $this->_option;
			$nview->project 	= $this->_project;
			$view->content 		= $nview->loadTemplate();
		}

		$basePath = JPATH_ROOT . DS . 'components' . DS . 'com_wiki';
		if ($this->_task == 'edit' || $this->_task == 'new' || $this->_task == 'save')
		{
			$basePath = JPATH_ROOT . DS . 'plugins' . DS . 'projects' . DS . 'notes';
		}
		if (!$view->content)
		{
			$controllerName = 'WikiController' . ucfirst($this->_controllerName);
			// Instantiate controller
			$controller = new $controllerName(array(
				'base_path' => $basePath,
				'name'      => 'projects',
				'sub'       => 'notes',
				'group'     => $this->_group
			));

			// Catch any echoed content with ob
			ob_start();
			$controller->execute();

			// Record activity
			if ($save && !$preview && !$this->getError() && !$controller->getError())
			{
				$objAA = new ProjectActivity( $this->_database );
				$what  = $exists
					? JText::_('COM_PROJECTS_NOTE_EDITED')
					: JText::_('COM_PROJECTS_NOTE_ADDED');
				$what .= $exists ? ' "' . $controller->page->get('title') . '" ' : '';
				$what .= ' '.JText::_('COM_PROJECTS_NOTE_IN_NOTES');
				$aid = $objAA->recordActivity($this->_project->id, $this->_uid, $what,
					$controller->page->get('id'), 'notes', JRoute::_('index.php?option=' . $this->_option . a
					. 'alias=' . $this->_project->alias . a . 'active=notes') , 'notes', 0);

				// Record page order for new pages
				$lastorder = $this->model->getLastNoteOrder($scope);
				$order = intval($lastorder + 1);
				$this->model->saveNoteOrder($scope, $order);
			}

			// Make sure all scopes of subpages are valid after rename
			if ($rename)
			{
				// Incoming
				$oldpagename = trim(JRequest::getVar( 'oldpagename', '', 'post' ));
				$newpagename = trim(JRequest::getVar( 'newpagename', '', 'post' ));
				$this->model->fixScopePaths($scope, $oldpagename, $newpagename);
			}

			$controller->redirect();
			$view->content = ob_get_contents();
			ob_end_clean();
		}

		// Fix pathway (com_wiki screws it up)
		$this->fixupPathway();

		// Get messages	and errors
		$view->msg = isset($this->_msg) ? $this->_msg : NULL;
		if ( $this->getError())
		{
			$view->setError( $this->getError() );
		}

		$view->title 		= $this->_area['title'];
		$view->model 		= $this->model;
		$view->task 		= $this->_task;
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->pagename 	= $this->_pagename;
		$view->scope 		= $scope;
		$view->preview 		= $preview;
		$view->group 		= $this->_group;
		$view->params		= $this->params;

		return $view->loadTemplate();

	}

	/**
	 * List/unlist on public project page
	 *
	 *
	 * @return     string
	 */
	protected function _list()
	{
		// Incoming
		$id = trim(JRequest::getInt( 'p', 0 ));

		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;
		$url 	= JRoute::_($route . a . 'active=notes');

		// Load requested page
		$page = $this->model->page($id);
		if (!$page->get('id'))
		{
			$this->_referer = $url;
			return;
		}

		$listed = $this->_task == 'publist' ? 1 : 0;

		// Get/update public stamp for page
		if ($this->model->getPublicStamp($page->get('id'), true, $listed))
		{
			$this->_msg = $this->_task == 'publist' ? JText::_('COM_PROJECTS_NOTE_MSG_LISTED') : JText::_('COM_PROJECTS_NOTE_MSG_UNLISTED');
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
			$this->_referer = JRoute::_('index.php?option=' . $this->_option . '&scope=' . $page->get('scope') . '&pagename=' . $page->get('pagename'));
			return;
		}

		$this->_referer = $url;
		return;
	}


	/**
	 * Get public link and list/unlist
	 *
	 *
	 * @return     string
	 */
	protected function _share()
	{
		// Incoming
		$id = trim(JRequest::getInt( 'p', 0 ));

		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;
		$url 	= JRoute::_($route . a . 'active=notes');

		// Load requested page
		$page = $this->model->page($id);
		if (!$page->get('id'))
		{
			$this->_referer = $url;
			return;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'=>'projects',
				'element'=>'notes',
				'name'=>'pubsettings'
			)
		);

		// Get/update public stamp for page
		$view->publicStamp = $this->model->getPublicStamp($page->get('id'), true);

		if (!$view->publicStamp)
		{
			$this->setError(JText::_('PLG_PROJECTS_NOTES_ERROR_SHARE'));

			// Output error
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'=>'projects',
					'element'=>'files',
					'name'=>'error'
				)
			);

			$view->title  = '';
			$view->option = $this->_option;
			$view->setError( $this->getError() );
			return $view->loadTemplate();
		}

		$view->option 			= $this->_option;
		$view->project			= $this->_project;
		$view->url				= $url;
		$view->config 			= JComponentHelper::getParams( 'com_projects' );
		$view->page				= $page;
		$view->revision 		= $page->revision('current');
		$view->masterscope 		= 'projects' . DS . $this->_project->alias . DS . 'notes';
		$view->params			= $this->params;
		$view->ajax				= JRequest::getInt('ajax', 0);

		// Output HTML
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}

		return $view->loadTemplate();
	}

	/**
	 * Fix pathway
	 *
	 * @param      object  	$page
	 *
	 * @return     string
	 */
	public function fixupPathway()
	{
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		$pathway->setPathway(array());

		$group = NULL;

		if ($this->_project->owned_by_group)
		{
			$group = \Hubzero\User\Group::getInstance( $this->_project->owned_by_group );
		}

		// Add group
		if ($group && is_object($group))
		{
			$pathway->setPathway(array());
			$pathway->addItem(
				JText::_('COM_PROJECTS_GROUPS_COMPONENT'),
				JRoute::_('index.php?option=com_groups')
			);
			$pathway->addItem(
				\Hubzero\Utility\String::truncate($group->get('description'), 50),
				JRoute::_('index.php?option=com_groups' . a . 'cn=' . $group->cn)
			);
			$pathway->addItem(
				JText::_('COM_PROJECTS_PROJECTS'),
				JRoute::_('index.php?option=com_groups' . a . 'cn=' . $group->cn . a . 'active=projects')
			);
		}
		else
		{
			$pathway->addItem(
				JText::_('COMPONENT_LONG_NAME'),
				JRoute::_('index.php?option=' . $this->_option)
			);
		}

		$pathway->addItem(
			stripslashes($this->_project->title),
			JRoute::_('index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias)
		);

		if ($this->_tool && $this->_tool->id)
		{
			$pathway->addItem(
				ucfirst(JText::_('COM_PROJECTS_PANEL_TOOLS')),
				JRoute::_('index.php?option=' . $this->_option . a . 'alias='
				. $this->_project->alias . a . 'active=tools')
			);

			$pathway->addItem(
				\Hubzero\Utility\String::truncate($this->_tool->title, 50),
				JRoute::_('index.php?option=' . $this->_option . a . 'alias='
				. $this->_project->alias . a . 'active=tools' . a . 'tool=' . $this->_tool->id)
			);

			$pathway->addItem(
				ucfirst(JText::_('COM_PROJECTS_TOOLS_TAB_WIKI')),
				JRoute::_('index.php?option=' . $this->_option . a . 'alias='
				. $this->_project->alias . a . 'active=tools' . a . 'tool=' . $this->_tool->id . a . 'action=wiki')
			);
		}
		else
		{
			$pathway->addItem(
				ucfirst(JText::_('COM_PROJECTS_TAB_NOTES')),
				JRoute::_('index.php?option=' . $this->_option . a . 'alias='
				. $this->_project->alias . a . 'active=notes')
			);
		}
	}

	/**
	 * List project notes available for publishing
	 *
	 * @return     array
	 */
	public function browser()
	{
		// Incoming
		$ajax 		= JRequest::getInt('ajax', 0);
		$primary 	= JRequest::getInt('primary', 1);
		$versionid  = JRequest::getInt('versionid', 0);

		if (!$ajax)
		{
			return false;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'=>'projects',
				'element'=>'notes',
				'name'=>'browser'
			)
		);

		// Get current attachments
		$pContent = new PublicationAttachment( $this->_database );
		$role 	= $primary ? '1' : '0';
		$other 	= $primary ? '0' : '1';

		$view->attachments = $pContent->getAttachments($versionid, $filters = array('role' => $role, 'type' => 'note'));

		// Output HTML
		$view->params 		= new JParameter( $this->_project->params );
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->config 		= $this->_config;
		$view->title		= $this->_area['title'];
		$view->primary		= $primary;
		$view->versionid	= $versionid;

		// Get messages	and errors
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		$html =  $view->loadTemplate();

		$arr = array(
			'html' => $html,
			'metadata' => '',
			'msg' => '',
			'referer' => ''
		);

		return $arr;
	}

	/**
	 * Serve wiki page (usually via public link)
	 *
	 * @param   int  	$projectid
	 * @return  void
	 */
	public function serve( $projectid = 0, $query = '')
	{
		$data = json_decode($query);

		if (!isset($data->pageid) || !$projectid)
		{
			return false;
		}

		$this->loadLanguage();

		$database = JFactory::getDBO();

		// Instantiate a project
		$obj = new Project( $database );

		// Get Project
		$this->_project = $obj->getProject($projectid);
		$this->_option 	= 'com_projects';

		if (!$this->_project)
		{
			return false;
		}

		// Load component configs
		$this->_config = JComponentHelper::getParams('com_projects');

		$group_prefix = $this->_config->get('group_prefix', 'pr-');
		$groupname = $group_prefix . $this->_project->alias;
		$scope = 'projects' . DS . $this->_project->alias . DS . 'notes';

		// Include note model
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects'
			. DS . 'models' . DS . 'note.php');

		// Get our model
		$this->model = new ProjectModelNote($scope, $groupname, $projectid);

		// Fix pathway (com_wiki screws it up)
		$this->fixupPathway();

		// URL to project
		$url 	= JRoute::_('index.php?option=com_projects' . a . 'alias=' . $this->_project->alias);

		// Load requested page
		$page = $this->model->page($data->pageid);
		if (!$page->get('id'))
		{
			return false;
		}

		// Write title & build pathway
		$document = JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_option)) . ': ' . stripslashes($this->_project->title) . ' - ' . stripslashes($page->get('title')) );

		// Instantiate a new view
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'notes',
				'name'		=>'pubview'
			)
		);
		$view->option 			= $this->_option;
		$view->project			= $this->_project;
		$view->url				= $url;
		$view->config 			= JComponentHelper::getParams( 'com_projects' );
		$view->database 		= $database;
		$view->page				= $page;
		$view->revision 		= $page->revision('current');
		$view->masterscope 		= 'projects' . DS . $this->_project->alias . DS . 'notes';

		// Output HTML
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}

		$view->display();
		return true;
	}
}