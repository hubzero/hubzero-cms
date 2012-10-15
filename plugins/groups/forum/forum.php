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

ximport('Hubzero_Plugin');

/**
 * Groups Plugin class for forum entries
 */
class plgGroupsForum extends Hubzero_Plugin
{
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

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @return     array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name' => 'forum',
			'title' => JText::_('PLG_GROUPS_FORUM'),
			'default_access' => $this->params->get('plugin_access','members'),
			'display_menu_tab' => true
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
		$active = 'forum';
		$active_real = 'discussion';

		// The output array we're returning
		$arr = array(
			'html'=>'',
			'name' => $active
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

		$this->group = $group;
		$this->database = JFactory::getDBO();
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'post.php');

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html') 
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//Create user object
			$juser = JFactory::getUser();

			//get the group members
			$members = $group->get('members');

			//if set to nobody make sure cant access
			if ($group_plugin_acl == 'nobody') 
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('COM_GROUPS_PLUGIN_OFF', ucfirst($active_real)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if ($juser->get('guest') 
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members')) 
			{
				ximport('Hubzero_Module_Helper');
				$arr['html']  = '<p class="warning">' . JText::sprintf('COM_GROUPS_PLUGIN_REGISTERED', ucfirst($active_real)) . '</p>';
				$arr['html'] .= Hubzero_Module_Helper::renderModules('force_mod');
				return $arr;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array($juser->get('id'), $members) 
			 && $group_plugin_acl == 'members' 
			 && $authorized != 'admin') 
			{
				$arr['html'] = '<p class="warning">' . JText::sprintf('COM_GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active_real)) . '</p>';
				return $arr;
			}

			//user vars
			$this->juser = $juser;
			$this->authorized = $authorized;

			//group vars
			$this->members = $members;

			//option and paging vars
			$this->option = $option;
			$this->name = substr($option, 4, strlen($option));
			$this->limitstart = $limitstart;
			$this->limit = $limit;
			
			$juri = JURI::getInstance();
			$path = $juri->getPath();
			if (strstr($path, '/')) 
			{
				$path = str_replace('/groups/' . $this->group->get('cn') . '/forum', '', $path);
				$path = ltrim($path, DS);

				$bits = explode('/', $path);
				// Section name
				if (isset($bits[0]) && trim($bits[0])) 
				{
					if ($bits[0] == 'new')
					{
						$action = 'newsection';
					}
					else 
					{
						JRequest::setVar('section', $bits[0]);
					}
				}
				// Categry name
				if (isset($bits[1]) && trim($bits[1])) 
				{
					if ($bits[1] == 'edit')
					{
						$action = 'editsection';
					}
					else if ($bits[1] == 'delete')
					{
						$action = 'deletesection';
					}
					else if ($bits[1] == 'new')
					{
						$action = 'editcategory';
					}
					else 
					{
						JRequest::setVar('category', $bits[1]);
						$action = 'categories';
					}
				}
				// Thread name
				if (isset($bits[2]) && trim($bits[2])) 
				{
					if ($bits[2] == 'edit')
					{
						$action = 'editcategory';
					}
					else if ($bits[2] == 'delete')
					{
						$action = 'deletecategory';
					}
					else if ($bits[2] == 'new')
					{
						$action = 'editthread';
					}
					else 
					{
						JRequest::setVar('thread', $bits[2]);
						$action = 'threads';
					}
				}
				// Thread action
				if (isset($bits[3]) && trim($bits[3])) 
				{
					if ($bits[3] == 'edit')
					{
						$action = 'editthread';
					}
					else if ($bits[3] == 'delete')
					{
						$action = 'deletethread';
					}
					else 
					{
						JRequest::setVar('post', $bits[3]);
					}
				}
				// Thread attachment download
				if (isset($bits[4]) && trim($bits[4])) 
				{
					JRequest::setVar('file', $bits[4]);
					$action = 'download';
				}
			}
			$action = JRequest::getVar('action', $action, 'post');

			//push the stylesheet to the view
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('groups', 'forum');
			Hubzero_Document::addPluginScript('groups', 'forum');

			//include 
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'category.php');
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'section.php');
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'attachment.php');
			
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'pagination.php');
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'tags.php');

			/*$controllerName = JRequest::getCmd('cntrl', 'sections');
			if (!file_exists(JPATH_ROOT . DS . 'plugins' . DS . 'groups' . DS . 'forum' . DS . 'controllers' . DS . $controllerName . '.php'))
			{
				$controllerName = 'sections';
			}
			require_once(JPATH_ROOT . DS . 'plugins' . DS . 'groups' . DS . 'forum' . DS . 'controllers' . DS . $controllerName . '.php');
			$controllerName = 'PlgForumController' . ucfirst(strtolower($controllerName));

			// Instantiate controller
			$controller = new $controllerName();
			$controller->group = $group;
			$controller->authorized = $authorized;
			$controller->option = $option;
			$arr['html'] .= $controller->execute($action);
			$controller->redirect();*/
			
			switch ($action)
			{
				case 'sections':       $arr['html'] .= $this->sections();       break;
				case 'savesection':    $arr['html'] .= $this->savesection();    break;
				case 'deletesection':  $arr['html'] .= $this->deletesection();  break;
				
				case 'categories':     $arr['html'] .= $this->categories();     break;
				case 'savecategory':   $arr['html'] .= $this->savecategory();   break;
				case 'newcategory':    $arr['html'] .= $this->editcategory();   break;
				case 'editcategory':   $arr['html'] .= $this->editcategory();   break;
				case 'deletecategory': $arr['html'] .= $this->deletecategory(); break;
				
				case 'threads':        $arr['html'] .= $this->threads();        break;
				case 'savethread':     $arr['html'] .= $this->savethread();     break;
				case 'editthread':     $arr['html'] .= $this->editthread();     break;
				case 'deletethread':   $arr['html'] .= $this->deletethread();   break;
				
				case 'download':       $arr['html'] .= $this->download();       break;
				case 'search':         $arr['html'] .= $this->search();         break;

				default: $arr['html'] .= $this->sections(); break;
			}
		}

		$tModel = new ForumPost($this->database);
		
		$arr['metadata']['count'] = $tModel->getCount(array(
			'group' => $this->group->get('gidNumber'),
			'state' => 1,
			'parent' => 0
		));

		// Return the output
		return $arr;
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      string $url  URL to redirect to
	 * @param      string $msg  Message to send
	 * @param      string $type Message type (message, error, warning, info)
	 * @return     void
	 */
	public function setRedirect($url, $msg=null, $type='message')
	{
		if ($msg !== null)
		{
			$this->addPluginMessage($msg, $type);
		}
		$this->redirect($url);
	}
	
	/**
	 * Set permissions
	 * 
	 * @param      string  $assetType Type of asset to set permissions for (component, section, category, thread, post)
	 * @param      integer $assetId   Specific object to check permissions for
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->params->set('access-view', true);
		if (!$this->juser->get('guest')) 
		{
			$this->params->set('access-view-' . $assetType, false);
			if (in_array($this->juser->get('id'), $this->members))
			{
				$this->params->set('access-view-' . $assetType, true);
			}
			if (isset($this->model) && is_object($this->model))
			{
				if (!$this->model->state)
				{
					$this->params->set('access-view-' . $assetType, false);
				}
			}
			
			$this->params->set('access-create-' . $assetType, false);
			$this->params->set('access-delete-' . $assetType, false);
			$this->params->set('access-edit-' . $assetType, false);
			switch ($assetType)
			{
				case 'thread':
					$this->params->set('access-create-' . $assetType, true);
					if ($this->authorized == 'admin' || $this->authorized == 'manager')
					{
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
				break;
				case 'category':
					if ($this->authorized == 'admin' || $this->authorized == 'manager')
					{
						$this->params->set('access-create-' . $assetType, true);
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
				break;
				case 'section':
					if ($this->authorized == 'admin' || $this->authorized == 'manager')
					{
						$this->params->set('access-create-' . $assetType, true);
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
				break;
				case 'component':
				default:
					if ($this->authorized == 'admin' || $this->authorized == 'manager')
					{
						$this->params->set('access-create-' . $assetType, true);
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
				break;
			}
		}
	}
	
	/**
	 * Show sections in this forum
	 * 
	 * @return     string
	 */
	public function sections()
	{
		// Instantiate a vew
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'forum',
				'name'    => 'sections',
				'layout'  => 'display'
			)
		);

		// Incoming
		$view->filters = array();
		$view->filters['authorized'] = 1;
		$view->filters['group']      = $this->group->get('gidNumber');
		$view->filters['search']     = JRequest::getVar('q', '');
		$view->filters['section_id'] = 0;
		$view->filters['state']      = 1;

		$view->edit = JRequest::getVar('section', '');

		// Get Sections
		$sModel = new ForumSection($this->database);
		$view->sections = $sModel->getRecords(array(
			'state' => 1, 
			'group' => $this->group->get('gidNumber')
		));

		$model = new ForumCategory($this->database);

		// Check if there are uncategorized posts
		// This should mean legacy data
		if (($posts = $model->getPostCount(0, $this->group->get('gidNumber'))) || !$view->sections || !count($view->sections))
		{
			// Create a default section
			$dSection = new ForumSection($this->database);
			$dSection->title = JText::_('Default Section');
			$dSection->alias = str_replace(' ', '-', $dSection->title);
			$dSection->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($dSection->title));
			$dSection->group_id = $this->group->get('gidNumber');
			$dSection->state = 1;
			if ($dSection->check())
			{
				$dSection->store();
			}

			// Create a default category
			$dCategory = new ForumCategory($this->database);
			$dCategory->title = JText::_('Discussions');
			$dCategory->description = JText::_('Default category for all discussions in this forum.');
			$dCategory->alias = str_replace(' ', '-', $dCategory->title);
			$dCategory->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($dCategory->title));
			$dCategory->section_id = $dSection->id;
			$dCategory->group_id = $this->group->get('gidNumber');
			$dCategory->state = 1;
			if ($dCategory->check())
			{
				$dCategory->store();
			}

			if ($posts)
			{
				// Update all the uncategorized posts to the new default
				$tModel = new ForumPost($this->database);
				$tModel->updateCategory(0, $dCategory->id, $this->group->get('gidNumber'));
			}

			$view->sections = $sModel->getRecords(array(
				'state' => 1, 
				'group' => $this->group->get('gidNumber')
			));
		}

		$view->stats = new stdClass;
		$view->stats->categories = 0;
		$view->stats->threads = 0;
		$view->stats->posts = 0;

		foreach ($view->sections as $key => $section)
		{
			$view->filters['section_id'] = $section->id;

			$view->sections[$key]->categories = $model->getRecords($view->filters);
			/*if ($view->sections[$key]->id == 0 && !$view->sections[$key]->categories)
			{
				$default = new ForumCategory($this->database);
				$default->id = 0;
				$default->title = JText::_('Discussions');
				$default->description = JText::_('Default category for all discussions in this forum.');
				$default->alias = str_replace(' ', '-', $default->title);
				$default->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($default->title));
				$default->section_id = 0;
				$default->created_by = 0;
				$default->threads = $model->getThreadCount(0, $this->group->get('gidNumber'));
				$default->posts = $model->getPostCount(0, $this->group->get('gidNumber'));
				
				$view->sections[$key]->categories = array($default);
			}*/
	
			$view->stats->categories += count($view->sections[$key]->categories);
			if ($view->sections[$key]->categories)
			{
				foreach ($view->sections[$key]->categories as $c)
				{
					$view->stats->threads += $c->threads;
					$view->stats->posts += $c->posts;
				}
			}
		}

		$post = new ForumPost($this->database);
		$view->lastpost = $post->getLastActivity($this->group->get('gidNumber'));

		//get authorization
		$this->_authorize('section');
		$this->_authorize('category');
		$view->config = $this->params;
		$view->group = $this->group;
		$view->option = $this->option;
		$view->notifications = $this->getPluginMessage();

		// email settings data
		include_once(JPATH_ROOT . DS . 'plugins' . DS . 'groups' . DS . 'memberoptions' . DS . 'memberoption.class.php');
		$user = & JFactory::getUser();

		$database = & JFactory::getDBO();
		$recvEmailOption = new XGroups_MemberOption($database);
		$recvEmailOption->loadRecord($this->group->get('gidNumber'), $user->id, GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION);

		if ($recvEmailOption->id) 
		{
			$view->recvEmailOptionID = $recvEmailOption->id;
			$view->recvEmailOptionValue = $recvEmailOption->optionvalue;
		} 
		else 
		{
			$view->recvEmailOptionID = 0;
			$view->recvEmailOptionValue = 0;
		}

		// Set any errors
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}

	/**
	 * Saves a section and redirects to main page afterward
	 * 
	 * @return     void
	 */
	public function savesection()
	{
		// Incoming posted data
		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Instantiate a new table row and bind the incoming data
		$model = new ForumSection($this->database);
		if (!$model->bind($fields))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum')
			);
			return;
		}

		// Check content
		if ($model->check()) 
		{
			// Store new content
			$model->store();
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum')
		);
	}

	/**
	 * Deletes a section and redirects to main page afterwards
	 * 
	 * @return     void
	 */
	public function deletesection()
	{
		// Is the user logged in?
		if ($this->juser->get('guest')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'))),
				JText::_('PLG_GROUPS_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$alias = JRequest::getVar('section', '');
		
		// Load the section
		$model = new ForumSection($this->database);
		$model->loadByAlias($alias, $this->group->get('gidNumber'));
		
		// Make the sure the section exist
		if (!$model->id) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'),
				JText::_('PLG_GROUPS_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}

		// Check if user is authorized to delete entries
		$this->_authorize('section', $model->id);
		if (!$this->params->get('access-delete-section')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'),
				JText::_('PLG_GROUPS_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Get all the categories in this section
		$cModel = new ForumCategory($this->database);
		$categories = $cModel->getRecords(array(
			'section_id' => $model->id,
			'group'      => $this->group->get('gidNumber')
		));
		if ($categories)
		{
			// Build an array of category IDs
			$cats = array();
			foreach ($categories as $category)
			{
				$cats[] = $category->id;
			}

			// Set all the threads/posts in all the categories to "deleted"
			$tModel = new ForumPost($this->database);
			if (!$tModel->setStateByCategory($cats, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
			{
				$this->setError($tModel->getError());
			}

			// Set all the categories to "deleted"
			if (!$cModel->setStateBySection($model->id, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
			{
				$this->setError($cModel->getError());
			}
		}

		// Set the section to "deleted"
		$model->state = 2;  /* 0 = unpublished, 1 = published, 2 = deleted */
		if (!$model->store()) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'),
				$model->getError(),
				'error'
			);
			return;
		}

		// Redirect to main listing
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'),
			JText::_('PLG_GROUPS_FORUM_SECTION_DELETED'),
			'passed'
		);
	}

	/**
	 * Short description for 'topics'
	 * 
	 * @return     string
	 */
	public function categories()
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'forum',
				'name'    => 'categories',
				'layout'  => 'display'
			)
		);

		// Incoming
		$view->filters = array();
		$view->filters['authorized'] = 1;
		$view->filters['limit']    = JRequest::getInt('limit', 25);
		$view->filters['start']    = JRequest::getInt('limitstart', 0);
		$view->filters['section']  = JRequest::getVar('section', '');
		$view->filters['category'] = JRequest::getVar('category', '');
		$view->filters['search']   = JRequest::getVar('q', '');
		$view->filters['group']    = $this->group->get('gidNumber');
		$view->filters['state']    = 1;
		$view->filters['parent']   = 0;
		
		$view->section = new ForumSection($this->database);
		$view->section->loadByAlias($view->filters['section'], $this->group->get('gidNumber'));
		$view->filters['section_id'] = $view->section->id;

		$view->category = new ForumCategory($this->database);
		$view->category->loadByAlias($view->filters['category'], $view->section->id, $this->group->get('gidNumber'));
		$view->filters['category_id'] = $view->category->id;

		if (!$view->category->id)
		{
			$view->category->title = JText::_('Discussions');
			$view->category->alias = str_replace(' ', '-', $view->category->title);
			$view->category->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($view->category->title));
		}

		// Initiate a forum object
		$view->forum = new ForumPost($this->database);

		// Get record count
		$view->total = $view->forum->getCount($view->filters);

		// Get records
		$view->rows = $view->forum->getRecords($view->filters);

		//get authorization
		$this->_authorize('category');
		$this->_authorize('thread');
		
		$view->config = $this->params;
		$view->group = $this->group;
		$view->option = $this->option;
		$view->notifications = $this->getPluginMessage();

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			$view->total, 
			$view->filters['start'], 
			$view->filters['limit']
		);

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		return $view->loadTemplate();
	}

	/**
	 * Search forum entries and display results
	 * 
	 * @return     string
	 */
	public function search()
	{
		ximport('Hubzero_Plugin_View');
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'forum',
				'name'    => 'categories',
				'layout'  => 'search'
			)
		);

		// Incoming
		$this->view->filters = array();
		$this->view->filters['authorized'] = 1;
		$this->view->filters['limit']  = JRequest::getInt('limit', 25);
		$this->view->filters['start']  = JRequest::getInt('limitstart', 0);
		$this->view->filters['search'] = JRequest::getVar('q', '');
		$this->view->filters['group']  = $this->group->get('gidNumber');

		$this->view->section = new ForumSection($this->database);
		$this->view->section->title = JText::_('Posts');
		$this->view->section->alias = str_replace(' ', '-', $this->view->section->title);
		$this->view->section->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($this->view->section->title));

		// Get all sections
		$sections = $this->view->section->getRecords(array(
			'state' => 1, 
			'group' => $this->view->filters['group']
		));
		$s = array();
		foreach ($sections as $section)
		{
			$s[$section->id] = $section;
		}
		$this->view->sections = $s;

		$this->view->category = new ForumCategory($this->database);
		$this->view->category->title = JText::_('Search');
		$this->view->category->alias = str_replace(' ', '-', $this->view->category->title);
		$this->view->category->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($this->view->category->title));

		// Get all categories
		$categories = $this->view->category->getRecords(array(
			'state' => 1, 
			'group' => $this->view->filters['group']
		));
		$c = array();
		foreach ($categories as $category)
		{
			$c[$category->id] = $category;
		}
		$this->view->categories = $c;

		// Initiate a forum object
		$this->view->forum = new ForumPost($this->database);

		// Get record count
		$this->view->total = $this->view->forum->getCount($this->view->filters);

		// Get records
		$this->view->rows = $this->view->forum->getRecords($this->view->filters);

		//get authorization
		$this->_authorize('category');
		$this->_authorize('thread');

		$this->view->config = $this->params;
		$this->view->group = $this->group;
		$this->view->option = $this->option;

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		$this->view->notifications = $this->getPluginMessage();

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		return $this->view->loadTemplate();
	}

	/**
	 * Show a form for editing a category
	 * 
	 * @return     string
	 */
	public function editcategory($model=null)
	{
		ximport('Hubzero_Plugin_View');
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'forum',
				'name'    => 'categories',
				'layout'  => 'edit'
			)
		);

		$category = JRequest::getVar('category', '');
		$section = JRequest::getVar('section', '');
		if ($this->juser->get('guest')) 
		{
			$return = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($return))
			);
			return;
		}

		$sModel = new ForumSection($this->database);
		$sModel->loadByAlias($section, $this->group->get('gidNumber'));

		// Incoming
		if (is_object($model))
		{
			$this->view->model = $model;
		}
		else 
		{
			$this->view->model = new ForumCategory($this->database);
			$this->view->model->loadByAlias($category, $sModel->id, $this->group->get('gidNumber'));
		}

		$this->_authorize('category', $this->view->model->id);

		if (!$this->view->model->id) 
		{
			$this->view->model->created_by = $this->juser->get('id');
			$this->view->model->section_id = ($this->view->model->section_id) ? $this->view->model->section_id : $sModel->id;
		}
		elseif ($this->view->model->created_by != $this->juser->get('id') && !$this->params->get('access-create-category')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum')
			);
			return;
		}

		$this->view->sections = $sModel->getRecords(array(
			'state' => 1,
			'group' => $this->group->get('gidNumber')
		));
		if (!$this->view->sections || count($this->view->sections) <= 0)
		{
			$this->view->sections = array();

			$default = new ForumSection($this->database);
			$default->id = 0;
			$default->title = JText::_('Categories');
			$default->alias = str_replace(' ', '-', $default->title);
			$default->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($default->title));
			$this->view->sections[] = $default;
		}

		$this->view->notifications = $this->getPluginMessage();
		$this->view->config = $this->params;
		$this->view->group = $this->group;
		$this->view->option = $this->option;

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		return $this->view->loadTemplate();
	}

	/**
	 * Save a category
	 * 
	 * @return     void
	 */
	public function savecategory()
	{
		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		$model = new ForumCategory($this->database);
		if (!$model->bind($fields))
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editcategory($model);
		}

		$this->_authorize('category', $model->id);
		if (!$this->params->get('access-edit-category'))
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum')
			);
		}
		$model->closed = (isset($fields['closed']) && $fields['closed']) ? 1 : 0;
		// Check content
		if (!$model->check()) 
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editcategory($model);
		}

		// Store new content
		if (!$model->store()) 
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editcategory($model);
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum')
		);
	}

	/**
	 * Delete a category
	 * 
	 * @return     void
	 */
	public function deletecategory()
	{
		// Is the user logged in?
		if ($this->juser->get('guest')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'))),
				JText::_('PLG_GROUPS_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$category = JRequest::getVar('category', '');
		if (!$category) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'),
				JText::_('PLG_GROUPS_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}
		
		$section = JRequest::getVar('section', '');
		$sModel = new ForumSection($this->database);
		$sModel->loadByAlias($section, $this->group->get('gidNumber'));

		// Initiate a forum object
		$model = new ForumCategory($this->database);
		$model->loadByAlias($category, $sModel->id, $this->group->get('gidNumber'));

		// Check if user is authorized to delete entries
		$this->_authorize('category', $model->id);
		if (!$this->params->get('access-delete-category')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'),
				JText::_('PLG_GROUPS_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Set all the threads/posts in all the categories to "deleted"
		$tModel = new ForumPost($this->database);
		if (!$tModel->setStateByCategory($model->id, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
		{
			$this->setError($tModel->getError());
		}

		// Set the category to "deleted"
		$model->state = 2;  /* 0 = unpublished, 1 = published, 2 = deleted */
		if (!$model->store()) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'),
				$model->getError(),
				'error'
			);
			return;
		}

		// Redirect to main listing
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'),
			JText::_('PLG_GROUPS_FORUM_CATEGORY_DELETED'),
			'passed'
		);
	}

	/**
	 * Show a thread
	 * 
	 * @return     string
	 */
	public function threads()
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'forum',
				'name'    => 'threads',
				'layout'  => 'display'
			)
		);

		// Incoming
		$view->filters = array();
		$view->filters['limit']    = JRequest::getInt('limit', 25);
		$view->filters['start']    = JRequest::getInt('limitstart', 0);
		$view->filters['section']  = JRequest::getVar('section', '');
		$view->filters['category'] = JRequest::getVar('category', '');
		$view->filters['parent']   = JRequest::getInt('thread', 0);
		$view->filters['state']    = 1;
		$view->filters['group']    = $this->group->get('gidNumber');

		if ($view->filters['parent'] == 0) 
		{
			return $this->categories();
		}

		$view->section = new ForumSection($this->database);
		$view->section->loadByAlias($view->filters['section'], $this->group->get('gidNumber'));
		$view->filters['section_id'] = $view->section->id;

		$view->category = new ForumCategory($this->database);
		$view->category->loadByAlias($view->filters['category'], $view->section->id, $this->group->get('gidNumber'));
		$view->filters['category_id'] = $view->category->id;

		if (!$view->category->id)
		{
			$view->category->title = JText::_('Discussions');
			$view->category->alias = 'discussions';
		}

		// Initiate a forum object
		$view->post = new ForumPost($this->database);

		// Load the topic
		$view->post->load($view->filters['parent']);

		// Get reply count
		$view->total = $view->post->getCount($view->filters);

		// Get replies
		$view->rows = $view->post->getRecords($view->filters);

		// Record the hit
		$view->participants = $view->post->getParticipants($view->filters);
		
		// Get attachments
		$view->attach = new ForumAttachment($this->database);
		$view->attachments = $view->attach->getAttachments($view->post->id);
		
		// Get tags on this article
		$view->tModel = new ForumTags($this->database);
		$view->tags = $view->tModel->get_tag_cloud(0, 0, $view->post->id);

		// Get authorization
		$this->_authorize('category', $view->category->id);
		$this->_authorize('thread', $view->post->id);

		$view->config = $this->params;
		$view->group = $this->group;
		$view->option = $this->option;
		$view->notifications = $this->getPluginMessage();

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			$view->total, 
			$view->filters['start'], 
			$view->filters['limit']
		);

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		return $view->loadTemplate();
	}

	/**
	 * Show a form for editing a post
	 * 
	 * @return     string
	 */
	public function editthread($post=null)
	{
		ximport('Hubzero_Plugin_View');
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'forum',
				'name'    => 'threads',
				'layout'  => 'edit'
			)
		);

		$id = JRequest::getInt('thread', 0);
		$category = JRequest::getVar('category', '');
		$sectionAlias = JRequest::getVar('section', '');

		if ($this->juser->get('guest')) 
		{
			$return = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $section . '/' . $category . '/new');
			if ($id)
			{
				$return = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $section . '/' . $category . '/' . $id . '/edit');
			}
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($return))
			);
			return;
		}

		$this->view->category = new ForumCategory($this->database);
		$this->view->category->loadByAlias($category);

		// Incoming
		if (is_object($post))
		{
			$this->view->post = $post;
		}
		else 
		{
			$this->view->post = new ForumPost($this->database);
			$this->view->post->load($id);
		}

		// Get authorization
		$this->_authorize('thread', $id);

		if (!$id) 
		{
			$this->view->post->created_by = $this->juser->get('id');
		}
		elseif ($this->view->post->created_by != $this->juser->get('id') && !$this->params->get('access-edit-thread')) 
		{
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $section . '/' . $category));
			return;
		}

		$sModel = new ForumSection($this->database);
		$this->view->sections = $sModel->getRecords(array(
			'state' => 1, 
			'group' => $this->group->get('gidNumber')
		));

		if (!$this->view->sections || count($this->view->sections) <= 0)
		{
			$this->view->sections = array();
			
			$default = new stdClass;
			$default->id = 0;
			$default->title = JText::_('Categories');
			$default->alias = str_replace(' ', '-', $default->title);
			$default->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($default->title));
			$this->view->sections[] = $default;
		}

		$cModel = new ForumCategory($this->database);
		foreach ($this->view->sections as $key => $section)
		{
			$this->view->sections[$key]->categories = $cModel->getRecords(array(
				'section_id' => $section->id,
				'group'      => $this->group->get('gidNumber'),
				'state'      => 1
			));
		}

		// Get tags on this article
		$this->view->tModel = new ForumTags($this->database);
		$this->view->tags = $this->view->tModel->get_tag_string($this->view->post->id, 0, 0, $this->view->post->created_by);

		$this->view->config = $this->params;
		$this->view->group = $this->group;
		$this->view->option = $this->option;
		$this->view->section = $sectionAlias;
		$this->view->notifications = $this->getPluginMessage();

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		return $this->view->loadTemplate();
	}
	
	/**
	 * Saves posted data for a new/edited forum thread post
	 * 
	 * @return     void
	 */
	public function savethread()
	{
		if ($this->juser->get('guest')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum')))
			);
			return;
		}

		// Incoming
		$section = JRequest::getVar('section', '');

		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		$this->_authorize('thread', intval($fields['id']));
		$asset = 'thread';
		if ($fields['parent'])
		{
			//$asset = 'post';
		}
		if (($fields['id'] && !$this->params->get('access-edit-thread')) 
		 || (!$fields['id'] && !$this->params->get('access-create-thread')))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'),
				JText::_('You are not authorized to perform this action.'),
				'warning'
			);
			return;
		}

		if ($fields['id'])
		{
			$old = new ForumPost($this->database);
			$old->load(intval($fields['id']));
		}

		// Bind data
		/* @var $model ForumPost */
		$model = new ForumPost($this->database);
		if (!$model->bind($fields)) 
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editthread($model);
		}
		
		// Check content
		if (!$model->check()) 
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editthread($model);
		}

		// Store new content
		if (!$model->store()) 
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editthread($model);
		}
		
		$parent = ($model->parent) ? $model->parent : $model->id;
		
		$this->upload($parent, $model->id);

		if ($fields['id'])
		{
			if ($old->category_id != $fields['category_id'])
			{
				$model->updateReplies(array('category_id' => $fields['category_id']), $model->id);
			}
		}
		
		$category = new ForumCategory($this->database);
		$category->load(intval($model->category_id));
		
		$tags = JRequest::getVar('tags', '', 'post');
		$tagger = new ForumTags($this->database);
		$tagger->tag_object($this->juser->get('id'), $model->id, $tags, 1);
		
		// Determine post save message 
		// Also, get subject of post for outgoing email, either the title of parent post (for replies), or title of current post (for new threads)
		if (!$fields['id'])
		{
			if (!$fields['parent'])
			{
				$message = JText::_('PLG_GROUPS_FORUM_THREAD_STARTED');
				$posttitle = $model->title;
			}
			else 
			{
				$message = JText::_('PLG_GROUPS_FORUM_POST_ADDED');
				
				/* @var $parentForumPost ForumPost */
				$parentForumPost = new ForumPost($this->database);
				$parentForumPost->load(intval($fields['parent']));
				$posttitle = $parentForumPost->title;
			}
		}
		else 
		{
			$message = ($model->modified_by) ? JText::_('PLG_GROUPS_FORUM_POST_EDITED') : JText::_('PLG_GROUPS_FORUM_POST_ADDED');
		}
		
		// Determine route
		if ($model->parent) 
		{
			$thread = $model->parent;
		} 
		else 
		{
			$thread = $model->id;
		}
		
		// Build outgoing email message
		$juser =& JFactory::getUser();
		$prependtext = "~!~!~!~!~!~!~!~!~!~!\r\n";
		$prependtext .= "You can reply to this message, but be sure to include your reply text above this area.\r\n\r\n" ;
		$prependtext .= $juser->name . " (". $juser->username . ") wrote:";
		$forum_message = $prependtext . "\r\n\r\n" . $model->comment;

		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $section . '/' . $category->alias . '/' . $thread . '#c' . $model->id);
		$forum_message .= "\r\n\r\n" . rtrim($juri->base(), DS) . DS . ltrim($sef, DS) . "\r\n";

		// Translate the message wiki formatting to html
		/*
		ximport('Hubzero_Wiki_Parser');

		$p =& Hubzero_Wiki_Parser::getInstance();
		
		$wikiconfig = array(
			'option'   => $this->option,
			'scope'    => 'group' . DS . 'forum',
			'pagename' => 'group',
			'pageid'   => $this->group->get('gidNumber'),
			'filepath' => '',
			'domain'   => ''
		);
		
		$forum_message = $p->parse("\n".stripslashes($forum_message), $wikiconfig);		
		*/

		$params =& JComponentHelper::getParams('com_groups');

		// Email the group and insert email tokens to allow them to respond to group posts via email
		if ($params->get('email_comment_processing'))
		{
			ximport('Hubzero_Emailtoken');
			// Figure out who should be notified about this comment (all group members for now)
			$userIDsToEmail = array();

			foreach ($this->members as $mbr)
			{
				//Look up user info 
				$user = new JUser();

				if ($user->load($mbr))
				{
					include_once(JPATH_ROOT . DS . 'plugins' . DS . 'groups' . DS . 'memberoptions' . DS . 'memberoption.class.php');

					// Find the user's group settings, do they want to get email (0 or 1)?
					$groupMemberOption = new XGroups_MemberOption($this->database);
					$groupMemberOption->loadRecord(
						$this->group->get('gidNumber'), 
						$user->id, 
						GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION
					);

					$sendEmail = 0;
					if ($groupMemberOption->id)
					{
						$sendEmail = $groupMemberOption->optionvalue;
					}

					if ($sendEmail)
					{
						$userIDsToEmail[] = $user->id;
					}
				}
			}

			JPluginHelper::importPlugin('xmessage');
			$dispatcher =& JDispatcher::getInstance();

			// Email each group member separately, each needs a user specific token
			ximport('Hubzero_Emailtoken');
			foreach ($userIDsToEmail as $userID)
			{
				$encryptor = new Hubzero_EmailToken();
				$jconfig =& JFactory::getConfig();

				// Construct User specific Email ThreadToken
				// Version, type, userid, xforumid
				$token = $encryptor->buildEmailToken(1, 2, $userID, $parent);

				$subject = ' - ' . $this->group->get('cn') . ' - ' . $posttitle;
				
				$from = array();
				$from['name']  = $jconfig->getValue('config.sitename') . ' ';
				$from['email'] = $jconfig->getValue('config.mailfrom');
				$from['replytoemail'] = 'hgm-' . $token;

				if (!$dispatcher->trigger('onSendMessage', array('group_message', $subject, $forum_message, $from, array($userID), $this->option, null, '', $this->group->get('gidNumber')))) 
				{
					$this->setError(JText::_('COM_GROUPS_ERROR_EMAIL_MEMBERS_FAILED'));
				}
			}
		}
		
		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $section . '/' . $category->alias . '/' . $thread . '#c' . $model->id),
			$message,
			'passed'
		);
	}

	/**
	 * Remove a thread
	 * 
	 * @return     void
	 */
	public function deletethread()
	{
		$section = JRequest::getVar('section', '');
		$category = JRequest::getVar('category', '');

		// Is the user logged in?
		if ($this->juser->get('guest')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $section . '/' . $category),
				JText::_('PLG_GROUPS_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$id = JRequest::getInt('thread', 0);

		// Initiate a forum object
		$model = new ForumPost($this->database);
		$model->load($id);

		// Make the sure the category exist
		if (!$model->id) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $section . '/' . $category),
				JText::_('PLG_GROUPS_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}

		// Check if user is authorized to delete entries
		$this->_authorize('thread', $id);
		if (!$this->params->get('access-delete-thread'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $section . '/' . $category),
				JText::_('PLG_GROUPS_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Update replies if this is a parent (thread starter)
		if (!$model->parent)
		{
			if (!$model->updateReplies(array('state' => 2), $model->id))  /* 0 = unpublished, 1 = published, 2 = deleted */
			{
				$this->setError($model->getError());
			}
		}

		// Delete the topic itself
		$model->state = 2;  /* 0 = unpublished, 1 = published, 2 = deleted */
		if (!$model->store()) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $section . '/' . $category),
				$forum->getError(),
				'error'
			);
			return;
		}

		// Redirect to main listing
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $section . '/' . $category),
			JText::_('PLG_GROUPS_FORUM_THREAD_DELETED'),
			'passed'
		);
	}

	/**
	 * Uploads a file to a given directory and returns an attachment string
	 * that is appended to report/comment bodies
	 * 
	 * @param      string $listdir Directory to upload files to
	 * @return     string A string that gets appended to messages
	 */
	public function upload($listdir, $post_id)
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			return;
		}

		if (!$listdir) 
		{
			$this->setError(JText::_('PLG_GROUPS_FORUM_NO_UPLOAD_DIRECTORY'));
			return;
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name']) 
		{
			return;
		}

		// Incoming
		$description = trim(JRequest::getVar('description', ''));

		// Construct our file path
		$path = JPATH_ROOT . DS . trim($this->params->get('filepath', '/site/forum'), DS) . DS . $listdir;
		if ($post_id)
		{
			$path .= DS . $post_id;
		}

		// Build the path if it doesn't exist
		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				$this->setError(JText::_('PLG_GROUPS_FORUM_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$ext = strtolower(JFile::getExt($file['name']));

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name'])) 
		{
			$this->setError(JText::_('PLG_GROUPS_FORUM_ERROR_UPLOADING'));
			return;
		} 
		else 
		{
			// File was uploaded
			// Create database entry
			$row = new ForumAttachment($this->database);
			$row->bind(array(
				'id' => 0,
				'parent' => $listdir,
				'post_id' => $post_id,
				'filename' => $file['name'],
				'description' => $description
			));
			if (!$row->check()) 
			{
				$this->setError($row->getError());
			}
			if (!$row->store()) 
			{
				$this->setError($row->getError());
			}
		}
	}
	
	/**
	 * Serves up files only after passing access checks
	 *
	 * @return	void
	 */
	public function download()
	{
		// Incoming
		$section = JRequest::getVar('section', '');
		$category = JRequest::getVar('category', '');
		$thread = JRequest::getInt('thread', 0);
		$post = JRequest::getInt('post', 0);
		$file = JRequest::getVar('file', '');
		
		// Check logged in status
		if ($this->juser->get('guest')) 
		{
			$return = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $section . '/' . $category . '/' . $thread . '/' . $post . '/' . $file);
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($return))
			);
			return;
		}

		// Ensure we have a database object
		if (!$this->database) 
		{
			JError::raiseError(500, JText::_('PLG_GROUPS_FORUM_DATABASE_NOT_FOUND'));
			return;
		}
		
		// Instantiate an attachment object
		$attach = new ForumAttachment($this->database);
		if (!$post)
		{
			$attach->loadByThread($thread, $file);
		}
		else 
		{
			$attach->loadByPost($post);
		}

		if (!$attach->filename) 
		{
			JError::raiseError(404, JText::_('PLG_GROUPS_FORUM_FILE_NOT_FOUND'));
			return;
		}
		$file = $attach->filename;

		// Get the parent ticket the file is attached to
		$this->model = new ForumPost($this->database);
		$this->model->load($attach->post_id);

		if (!$this->model->id) 
		{
			JError::raiseError(404, JText::_('PLG_GROUPS_FORUM_POST_NOT_FOUND'));
			return;
		}

		// Load ACL
		$this->_authorize('thread', $this->model->id);

		// Ensure the user is authorized to view this file
		if (!$this->params->get('access-view-thread')) 
		{
			JError::raiseError(403, JText::_('PLG_GROUPS_FORUM_NOT_AUTH_FILE'));
			return;
		}

		// Ensure we have a path
		if (empty($file)) 
		{
			JError::raiseError(404, JText::_('PLG_GROUPS_FORUM_FILE_NOT_FOUND'));
			return;
		}
		if (preg_match("/^\s*http[s]{0,1}:/i", $file)) 
		{
			JError::raiseError(404, JText::_('PLG_GROUPS_FORUM_BAD_FILE_PATH'));
			return;
		}
		if (preg_match("/^\s*[\/]{0,1}index.php\?/i", $file)) 
		{
			JError::raiseError(404, JText::_('PLG_GROUPS_FORUM_BAD_FILE_PATH'));
			return;
		}
		// Disallow windows drive letter
		if (preg_match("/^\s*[.]:/", $file)) 
		{
			JError::raiseError(404, JText::_('PLG_GROUPS_FORUM_BAD_FILE_PATH'));
			return;
		}
		// Disallow \
		if (strpos('\\', $file)) 
		{
			JError::raiseError(404, JText::_('PLG_GROUPS_FORUM_BAD_FILE_PATH'));
			return;
		}
		// Disallow ..
		if (strpos('..', $file)) 
		{
			JError::raiseError(404, JText::_('PLG_GROUPS_FORUM_BAD_FILE_PATH'));
			return;
		}

		// Get the configured upload path
		$basePath  = DS . trim($this->params->get('filepath', '/site/forum'), DS) . DS  . $attach->parent . DS . $attach->post_id;

		// Does the path start with a slash?
		if (substr($file, 0, 1) != DS) 
		{
			$file = DS . $file;
			// Does the beginning of the $attachment->filename match the config path?
			if (substr($file, 0, strlen($basePath)) == $basePath) 
			{
				// Yes - this means the full path got saved at some point
			} 
			else 
			{
				// No - append it
				$file = $basePath . $file;
			}
		}

		// Add JPATH_ROOT
		$filename = JPATH_ROOT . $file;

		// Ensure the file exist
		if (!file_exists($filename)) 
		{
			JError::raiseError(404, JText::_('PLG_GROUPS_FORUM_FILE_NOT_FOUND'));
			return;
		}

		// Get some needed libraries
		ximport('Hubzero_Content_Server');

		// Initiate a new content server and serve up the file
		$xserver = new Hubzero_Content_Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve()) 
		{
			// Should only get here on error
			JError::raiseError(404, JText::_('PLG_GROUPS_FORUM_SERVER_ERROR'));
		} 
		else 
		{
			exit;
		}
		return;
	}

	/**
	 * Remove all items associated with the gorup being deleted
	 * 
	 * @param      object $group Group being deleted
	 * @return     string Log of items removed
	 */
	public function onGroupDelete($group)
	{
		$log = JText::_('PLG_GROUPS_FORUM') . ': ';
		
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'post.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'category.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'section.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'attachment.php');

		$this->database = JFactory::getDBO();

		$sModel = new ForumSection($this->database);
		$sections = $sModel->getRecords(array(
			'group' => $group->get('gidNumber')
		));

		// Do we have any IDs?
		if (count($sections) > 0) 
		{
			// Loop through each ID
			foreach ($sections as $section) 
			{
				// Get the categories in this section
				$cModel = new ForumCategory($this->database);
				$categories = $cModel->getRecords(array(
					'section_id' => $section->id,
					'group'      => $group->get('gidNumber')
				));
				
				if ($categories)
				{
					// Build an array of category IDs
					$cats = array();
					foreach ($categories as $category)
					{
						$cats[] = $category->id;
					}

					// Set all the threads/posts in all the categories to "deleted"
					$tModel = new ForumPost($this->database);
					if (!$tModel->setStateByCategory($cats, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
					{
						$this->setError($tModel->getError());
					}
					$log .= 'forum.section.' . $section->id . '.category.' . $category->id . '.post' . "\n";

					// Set all the categories to "deleted"
					if (!$cModel->setStateBySection($model->id, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
					{
						$this->setError($cModel->getError());
					}
					$log .= 'forum.section.' . $section->id . '.category.' . $category->id . "\n";
				}

				// Set the section to "deleted"
				$sModel->load($section->id);
				$sModel->state = 2;  /* 0 = unpublished, 1 = published, 2 = deleted */
				if (!$sModel->store()) 
				{
					$this->setError($sModel->getError());
					return '';
				}
				$log .= 'forum.section.' . $section->id . ' ' . "\n";
			}
		}
		else 
		{
			$log .= JText::_('PLG_GROUPS_FORUM_NO_RESULTS')."\n";
		}

		return $log;
	}

	/**
	 * Get a count of all items that will be deleted with this gorup
	 * 
	 * @param      object $group Group to be deleted
	 * @return     void
	 */
	public function onGroupDeleteCount($group)
	{
	}
}
