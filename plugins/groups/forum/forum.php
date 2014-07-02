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
 * Groups Plugin class for forum entries
 */
class plgGroupsForum extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name'             => $this->_name,
			'title'            => JText::_('PLG_GROUPS_FORUM'),
			'default_access'   => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'             => 'f086'
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

		$this->group    = $group;
		$this->database = JFactory::getDBO();

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'forum.php');

		$this->model = new ForumModel('group', $group->get('gidNumber'));

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html')
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//get the group members
			$this->members = $group->get('members');

			//if set to nobody make sure cant access
			if ($group_plugin_acl == 'nobody')
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_OFF', ucfirst($active_real)) . '</p>';
				return $arr;
			}

			//Create user object
			$this->juser = JFactory::getUser();

			//check if guest and force login if plugin access is registered or members
			if ($this->juser->get('guest')
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
			{
				$return = base64_encode(JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active, false, true), 'server'));
				$this->setRedirect(
					JRoute::_('index.php?option=com_users&view=login&return=' . $return, false),
					JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active_real)),
					'warning'
				);
				return;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array($this->juser->get('id'), $this->members)
			 && $group_plugin_acl == 'members'
			 && $authorized != 'admin')
			{
				$arr['html'] = '<p class="warning">' . JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active_real)) . '</p>';
				return $arr;
			}

			//user vars
			$this->authorized = $authorized;

			//group vars
			//$this->members = $members;
			//get the plugins params
			$p = new \Hubzero\Plugin\Params($this->database);
			$this->params = $p->getParams($this->group->get('gidNumber'), 'groups', $this->_name);

			//option and paging vars
			$this->option = $option;
			//$this->name = substr($option, 4, strlen($option));
			$this->limitstart = $limitstart;
			$this->limit = $limit;
			$this->base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name;

			$juri = JURI::getInstance();
			$path = $juri->getPath();
			if (strstr($path, '/'))
			{
				$bits = $this->_parseUrl();
				// Section name
				if (isset($bits[0]) && trim($bits[0]))
				{
					if ($bits[0] == 'new')
					{
						$action = 'newsection';
					}
					else if ($bits[0] == 'settings' || $bits[0] == 'savesettings')
					{
						$action = $bits[0];
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

			switch ($action)
			{
				// Settings
				case 'savesettings':   $arr['html'] .= $this->savesettings();   break;
				case 'settings':       $arr['html'] .= $this->settings();       break;

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

		$arr['metadata']['count'] = $this->model->count('threads');

		// Return the output
		return $arr;
	}

	/**
	 * Parse an SEF URL into its component bits
	 * stripping out the path leading up to the blog plugin
	 *
	 * @return     string
	 */
	private function _parseUrl()
	{
		static $path;

		if (!$path)
		{
			$juri = JURI::getInstance();
			$path = $juri->getPath();

			$path = str_replace($juri->base(true), '', $path);
			$path = str_replace('index.php', '', $path);
			$path = DS . trim($path, DS);

			$blog = '/groups/' . $this->group->get('cn') . '/forum';

			if ($path == $blog)
			{
				$path = array();
				return $path;
			}

			$path = ltrim($path, DS);
			$path = explode('/', $path);

			/*while ($path[0] != 'members' && !empty($path));
			{
				array_shift($path);
			}*/
			$paths = array();
			$start = false;
			foreach ($path as $bit)
			{
				if ($bit == 'groups' && !$start)
				{
					$start = true;
					continue;
				}
				if ($start)
				{
					$paths[] = $bit;
				}
			}
			if (count($paths) >= 2)
			{
				array_shift($paths);  // Remove group cn
				array_shift($paths);  // Remove 'blog'
			}
			$path = $paths;
		}

		return $path;
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
			$this->params->set('access-create-' . $assetType, false);
			$this->params->set('access-delete-' . $assetType, false);
			$this->params->set('access-edit-' . $assetType, false);

			if (in_array($this->juser->get('id'), $this->members))
			{
				$this->params->set('access-view-' . $assetType, true);
			}
			if (isset($this->thread) && is_object($this->thread))
			{
				if (!$this->thread->get('state'))
				{
					$this->params->set('access-view-' . $assetType, false);
				}
			}
			if (!in_array($this->juser->get('id'), $this->members))
			{
				return;
			}

			switch ($assetType)
			{
				case 'thread':
					$this->params->set('access-create-' . $assetType, true);
					if ($this->authorized == 'admin' || $this->authorized == 'manager')
					{
						$this->params->set('access-manage-' . $assetType, true);
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
				break;
				case 'category':
					if ($this->authorized == 'admin' || $this->authorized == 'manager')
					{
						$this->params->set('access-manage-' . $assetType, true);
						$this->params->set('access-create-' . $assetType, true);
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
				break;
				case 'section':
					if ($this->authorized == 'admin' || $this->authorized == 'manager')
					{
						$this->params->set('access-manage-' . $assetType, true);
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
						$this->params->set('access-manage-' . $assetType, true);
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
		$this->view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'groups',
				'element' => $this->_name,
				'name'    => 'sections',
				'layout'  => 'display'
			)
		);

		// Incoming
		$this->view->filters = array(
			//'authorized' => 1,
			'scope'      => $this->model->get('scope'),
			'scope_id'   => $this->model->get('scope_id'),
			'search'     => JRequest::getVar('q', ''),
			//'section_id' => 0,
			'state'      => 1
		);

		$this->view->edit = JRequest::getVar('section', '');

		$this->view->sections = $this->model->sections('list', array('state' => 1));

		$this->view->model = $this->model;

		if (!$this->view->sections->total())
		{
			if (!$this->model->setup())
			{
				$this->setError($this->model->getError());
			}
			$this->view->sections = $this->model->sections('list', array('state' => 1));
		}

		//get authorization
		$this->_authorize('section');
		$this->_authorize('category');

		$this->view->model  = $this->model;
		$this->view->config = $this->params;
		$this->view->group  = $this->group;
		$this->view->option = $this->option;
		$this->view->notifications = $this->getPluginMessage();

		// email settings data
		include_once(JPATH_ROOT . DS . 'plugins' . DS . 'groups' . DS . 'memberoptions' . DS . 'memberoption.class.php');

		$recvEmailOption = new GroupsTableMemberoption($this->database);
		$recvEmailOption->loadRecord($this->group->get('gidNumber'), $this->juser->id, GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION);

		if ($recvEmailOption->id)
		{
			$this->view->recvEmailOptionID    = $recvEmailOption->id;
			$this->view->recvEmailOptionValue = $recvEmailOption->optionvalue;
		}
		else
		{
			$this->view->recvEmailOptionID    = 0;
			$this->view->recvEmailOptionValue = 0;
		}

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
	 * Saves a section and redirects to main page afterward
	 *
	 * @return     void
	 */
	public function savesection()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming posted data
		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);
		$id = (isset($fields['id'])) ? $fields['id'] : null;

		// Instantiate a new table row and bind the incoming data
		$section = new ForumModelSection($id);
		if (!$section->bind($fields))
		{
			$this->setRedirect(
				JRoute::_($this->base),
				$section->getError(),
				'error'
			);
			return;
		}

		if (in_array($section->get('alias'), array('new', 'settings', 'savesettings')))
		{
			$this->setRedirect(
				JRoute::_($this->base),
				JText::sprintf('PLG_GROUPS_FORUM_SECTION_TITLE_RESERVED', $section->get('alias')),
				'error'
			);
			return;
		}

		// Check content
		if (!$section->store(true))
		{
			$this->setRedirect(
				JRoute::_($this->base),
				$section->getError(),
				'error'
			);
			return;
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_($this->base)
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
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($this->base))),
				JText::_('PLG_GROUPS_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$alias = JRequest::getVar('section', '');

		// Load the section
		$section = $this->model->section(JRequest::getVar('section', ''));

		// Make the sure the section exist
		if (!$section->exists())
		{
			$this->setRedirect(
				JRoute::_($this->base),
				JText::_('PLG_GROUPS_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}

		// Check if user is authorized to delete entries
		$this->_authorize('section', $section->get('id'));

		if (!$this->params->get('access-delete-section'))
		{
			$this->setRedirect(
				JRoute::_($this->base),
				JText::_('PLG_GROUPS_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Set the section to "deleted"
		$section->set('state', 2);  /* 0 = unpublished, 1 = published, 2 = deleted */

		if (!$section->store())
		{
			$this->setRedirect(
				JRoute::_($this->base),
				$model->getError(),
				'error'
			);
			return;
		}

		// Redirect to main listing
		$this->setRedirect(
			JRoute::_($this->base),
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
		$this->view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'groups',
				'element' => $this->_name,
				'name'    => 'categories',
				'layout'  => 'display'
			)
		);

		// Incoming
		$this->view->filters = array(
			'authorized' => 1,
			'limit'      => JRequest::getInt('limit', 25),
			'start'      => JRequest::getInt('limitstart', 0),
			'section'    => JRequest::getVar('section', ''),
			'category'   => JRequest::getCmd('category', ''),
			'search'     => JRequest::getVar('q', ''),
			'scope'      => $this->model->get('scope'),
			'scope_id'   => $this->model->get('scope_id'),
			'state'      => 1,
			'parent'     => 0
		);

		$this->view->filters['sortby']   = JRequest::getWord('sortby', 'activity');
		switch ($this->view->filters['sortby'])
		{
			case 'title':
				$this->view->filters['sort'] = 'c.sticky DESC, c.title';
				$this->view->filters['sort_Dir'] = 'ASC';
			break;

			case 'replies':
				$this->view->filters['sort'] = 'c.sticky DESC, replies';
				$this->view->filters['sort_Dir'] = 'DESC';
			break;

			case 'created':
				$this->view->filters['sort'] = 'c.sticky DESC, c.created';
				$this->view->filters['sort_Dir'] = 'DESC';
			break;

			case 'activity':
			default:
				$this->view->filters['sort'] = 'c.sticky DESC, activity';
				$this->view->filters['sort_Dir'] = 'DESC';
			break;
		}

		$this->view->section  = $this->model->section($this->view->filters['section'], $this->model->get('scope'), $this->model->get('scope_id'));
		if (!$this->view->section->exists())
		{
			JError::raiseError(404, JText::_('Section not found.'));
			return;
		}

		$this->view->category = $this->view->section->category($this->view->filters['category']);
		if (!$this->view->category->exists())
		{
			JError::raiseError(404, JText::_('Category not found.'));
			return;
		}

		//get authorization
		$this->_authorize('category');
		$this->_authorize('thread');

		$this->view->model  = $this->model;
		$this->view->config = $this->params;
		$this->view->group  = $this->group;
		$this->view->option = $this->option;
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
	 * Search forum entries and display results
	 *
	 * @return     string
	 */
	public function search()
	{
		$this->view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'groups',
				'element' => $this->_name,
				'name'    => 'categories',
				'layout'  => 'search'
			)
		);

		// Incoming
		$this->view->filters = array(
			'authorized' => 1,
			'limit'      => JRequest::getInt('limit', 25),
			'start'      => JRequest::getInt('limitstart', 0),
			'search'     => JRequest::getVar('q', ''),
			'scope'      => $this->model->get('scope'),
			'scope_id'   => $this->model->get('scope_id'),
			'state'      => 1
		);

		$this->view->section = $this->model->section(0);
		$this->view->section->set('title', JText::_('Posts'));
		$this->view->section->set('alias', str_replace(' ', '-', $this->view->section->get('title')));
		$this->view->section->set('alias', preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($this->view->section->get('title'))));

		// Get all sections
		$sections = $this->model->sections();
		$s = array();
		foreach ($sections as $section)
		{
			$s[$section->get('id')] = $section;
		}
		$this->view->sections = $s;

		$this->view->category = $this->view->section->category(0);
		$this->view->category->set('title', JText::_('Search'));
		$this->view->category->set('alias', str_replace(' ', '-', $this->view->category->get('title')));
		$this->view->category->set('alias', preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($this->view->category->get('title'))));

		$this->view->thread = $this->view->category->thread(0);

		// Get all categories
		$categories = $this->view->section->categories('list', array('section_id' => -1));
		$c = array();
		foreach ($categories as $category)
		{
			$c[$category->get('id')] = $category;
		}
		$this->view->categories = $c;

		//get authorization
		$this->_authorize('category');
		$this->_authorize('thread');

		$this->view->model  = $this->model;
		$this->view->config = $this->params;
		$this->view->group  = $this->group;
		$this->view->option = $this->option;

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
		if ($this->juser->get('guest'))
		{
			$return = JRoute::_($this->base);
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		$this->view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'groups',
				'element' => $this->_name,
				'name'    => 'categories',
				'layout'  => 'edit'
			)
		);

		$this->view->section = $this->model->section(JRequest::getVar('section', ''));

		// Incoming
		if (is_object($model))
		{
			$this->view->category = $model;
		}
		else
		{
			$this->view->category = new ForumModelCategory(
				JRequest::getVar('category', ''),
				$this->view->section->get('id')
			);
		}

		$this->_authorize('category', $this->view->category->get('id'));

		if (!$this->view->category->exists())
		{
			$this->view->category->set('created_by', $this->juser->get('id'));
			$this->view->category->set('section_id', $this->view->section->get('id'));
		}
		elseif ($this->view->category->get('created_by') != $this->juser->get('id') && !$this->config->get('access-create-category'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}

		$this->view->model  = $this->model;
		$this->view->config = $this->params;
		$this->view->group  = $this->group;
		$this->view->option = $this->option;

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
	 * Save a category
	 *
	 * @return     void
	 */
	public function savecategory()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		$model = new ForumModelCategory($fields['id']);
		if (!$model->bind($fields))
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editcategory($model);
		}

		$this->_authorize('category', $model->get('id'));

		if (!$this->params->get('access-edit-category'))
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_($this->base)
			);
		}

		$model->set('closed', (isset($fields['closed']) && $fields['closed']) ? 1 : 0);

		// Store new content
		if (!$model->store(true))
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editcategory($model);
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_($this->base)
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
				JRoute::_($this->base),
				JText::_('PLG_GROUPS_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Load the section
		$section = $this->model->section(JRequest::getVar('section', ''));

		// Load the category
		$category = $section->category(JRequest::getVar('category', ''));

		// Incoming
		if (!$category->exists())
		{
			$this->setRedirect(
				JRoute::_($this->base),
				JText::_('PLG_GROUPS_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}

		// Check if user is authorized to delete entries
		$this->_authorize('category', $category->get('id'));
		if (!$this->params->get('access-delete-category'))
		{
			$this->setRedirect(
				JRoute::_($this->base),
				JText::_('PLG_GROUPS_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Set all the threads/posts in all the categories to "deleted"
		$tModel = new ForumTablePost($this->database);
		if (!$tModel->setStateByCategory($category->get('id'), 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
		{
			$this->setError($tModel->getError());
		}

		// Set the category to "deleted"
		$category->set('state', 2);  /* 0 = unpublished, 1 = published, 2 = deleted */
		if (!$category->store())
		{
			$this->setRedirect(
				JRoute::_($this->base),
				$category->getError(),
				'error'
			);
			return;
		}

		// Redirect to main listing
		$this->setRedirect(
			JRoute::_($this->base),
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
		$this->view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'groups',
				'element' => $this->_name,
				'name'    => 'threads',
				'layout'  => 'display'
			)
		);

		// Incoming
		$this->view->filters = array(
			'limit'    => JRequest::getInt('limit', 25),
			'start'    => JRequest::getInt('limitstart', 0),
			'section'  => JRequest::getVar('section', ''),
			'category' => JRequest::getCmd('category', ''),
			'parent'   => JRequest::getInt('thread', 0),
			'state'    => 1
		);

		$this->view->section  = $this->model->section($this->view->filters['section'], $this->model->get('scope'), $this->model->get('scope_id'));
		if (!$this->view->section->exists())
		{
			JError::raiseError(404, JText::_('PLG_GROUPS_FORUM_ERROR_SECTION_NOT_FOUND'));
			return;
		}

		$this->view->category = $this->view->section->category($this->view->filters['category']);
		if (!$this->view->category->exists())
		{
			JError::raiseError(404, JText::_('PLG_GROUPS_FORUM_ERROR_CATEGORY_NOT_FOUND'));
			return;
		}

		$this->view->filters['category_id'] = $this->view->category->get('id');

		// Load the topic
		$this->view->thread = $this->view->category->thread($this->view->filters['parent']);

		// Get authorization
		$this->_authorize('category', $this->view->category->get('id'));
		$this->_authorize('thread', $this->view->thread->get('id'));

		$this->view->filters['state'] = array(1, 3);

		$this->view->model  = $this->model;
		$this->view->config = $this->params;
		$this->view->group  = $this->group;
		$this->view->option = $this->option;
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
	 * Show a form for editing a post
	 *
	 * @return     string
	 */
	public function editthread($post=null)
	{
		$this->view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'groups',
				'element' => $this->_name,
				'name'    => 'threads',
				'layout'  => 'edit'
			)
		);

		$id           = JRequest::getInt('thread', 0);
		$category     = JRequest::getVar('category', '');
		$sectionAlias = JRequest::getVar('section', '');

		if ($this->juser->get('guest'))
		{
			$return = JRoute::_($this->base . '&scope=' . $sectionAlias . '/' . $category . '/new');
			if ($id)
			{
				$return = JRoute::_($this->base . '&scope=' . $sectionAlias . '/' . $category . '/' . $id . '/edit');
			}
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		$this->view->section  = $this->model->section($sectionAlias, $this->model->get('scope'), $this->model->get('scope_id'));
		if (!$this->view->section->exists())
		{
			JError::raiseError(404, JText::_('PLG_GROUPS_FORUM_ERROR_SECTION_NOT_FOUND'));
			return;
		}

		$this->view->category = $this->view->section->category($category);
		if (!$this->view->category->exists())
		{
			JError::raiseError(404, JText::_('PLG_GROUPS_FORUM_ERROR_CATEGORY_NOT_FOUND'));
			return;
		}

		// Incoming
		if (is_object($post))
		{
			$this->view->post = new ForumModelThread($post);
		}
		else
		{
			$this->view->post = new ForumModelThread($id);
		}

		// Get authorization
		$this->_authorize('thread', $id);

		if (!$id)
		{
			$this->view->post->set('scope', $this->model->get('scope'));
			$this->view->post->set('created_by', $this->juser->get('id'));
		}
		elseif ($this->view->post->get('created_by') != $this->juser->get('id') && !$this->params->get('access-edit-thread'))
		{
			$this->setRedirect(JRoute::_($this->base . '&scope=' . $section . '/' . $category));
			return;
		}

		$this->view->model  = $this->model;
		$this->view->config = $this->params;
		$this->view->group  = $this->group;
		$this->view->option = $this->option;

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
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($this->base)))
			);
			return;
		}

		// Incoming
		$section = JRequest::getVar('section', '');

		$fields = JRequest::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		$this->_authorize('thread', intval($fields['id']));
		$asset = 'thread';
		if ($fields['parent'])
		{
			//$asset = 'post';
		}

		if ($fields['id'])
		{
			$old = new ForumTablePost($this->database);
			$old->load(intval($fields['id']));
			if ($old->created_by == $this->juser->get('id'))
			{
				$this->params->set('access-edit-thread', true);
			}
		}

		if (($fields['id'] && !$this->params->get('access-edit-thread'))
		 || (!$fields['id'] && !$this->params->get('access-create-thread')))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum'),
				JText::_('PLG_GROUPS_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		$fields['sticky'] = (isset($fields['sticky'])) ? $fields['sticky'] : 0;
		$fields['closed'] = (isset($fields['closed'])) ? $fields['closed'] : 0;

		// Bind data
		/* @var $model ForumTablePost */
		$model = new ForumTablePost($this->database);
		if (!$model->bind($fields))
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editthread($model);
		}
		if (!$model->anonymous)
		{
			$model->anonymous = 0;
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

		$category = new ForumTableCategory($this->database);
		$category->load(intval($model->category_id));

		$tags = JRequest::getVar('tags', '', 'post');
		$tagger = new ForumModelTags($model->id);
		$tagger->setTags($tags, $this->juser->get('id'), 1);

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

				/* @var $parentForumTablePost ForumTablePost */
				$parentForumTablePost = new ForumTablePost($this->database);
				$parentForumTablePost->load(intval($fields['parent']));
				$posttitle = $parentForumTablePost->title;
			}
		}
		else
		{
			$message = ($model->modified_by) ? JText::_('PLG_GROUPS_FORUM_POST_EDITED') : JText::_('PLG_GROUPS_FORUM_POST_ADDED');
		}

		// Determine route
		if ($model->parent)
		{
			$thread = $model->thread;
		}
		else
		{
			$thread = $model->id;
		}

		// Build outgoing email message
		$juser = JFactory::getUser();
		$prependtext = "~!~!~!~!~!~!~!~!~!~!\r\n";
		$prependtext .= "You can reply to this message, but be sure to include your reply text above this area.\r\n\r\n" ;
		$prependtext .= $juser->name . " (". $juser->username . ") wrote:";
		$forum_message = $prependtext . "\r\n\r\n" . html_entity_decode(strip_tags($model->comment), ENT_COMPAT, 'UTF-8');

		$juri = JURI::getInstance();
		$sef = JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum&scope=' . $section . '/' . $category->alias . '/' . $thread . '#c' . $model->id);
		$forum_message .= "\r\n\r\n" . rtrim($juri->base(), DS) . DS . ltrim($sef, DS) . "\r\n";

		$params = JComponentHelper::getParams('com_groups');

		// Email the group and insert email tokens to allow them to respond to group posts via email
		if ($params->get('email_comment_processing'))
		{
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
					$groupMemberOption = new GroupsTableMemberoption($this->database);
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
			$dispatcher = JDispatcher::getInstance();

			// Email each group member separately, each needs a user specific token
			foreach ($userIDsToEmail as $userID)
			{
				$encryptor = new \Hubzero\Mail\Token();
				$jconfig = JFactory::getConfig();

				// Construct User specific Email ThreadToken
				// Version, type, userid, xforumid
				$token = $encryptor->buildEmailToken(1, 2, $userID, $parent);

				$subject = ' - ' . $this->group->get('cn') . ' - ' . $posttitle;

				$from = array();
				$from['name']  = $jconfig->getValue('config.sitename') . ' ';
				$from['email'] = $jconfig->getValue('config.mailfrom');
				$from['replytoemail'] = 'hgm-' . $token . '@' . $_SERVER['HTTP_HOST'];

				if (!$dispatcher->trigger('onSendMessage', array('group_message', $subject, $forum_message, $from, array($userID), $this->option, null, '', $this->group->get('gidNumber'))))
				{
					$this->setError(JText::_('GROUPS_ERROR_EMAIL_MEMBERS_FAILED'));
				}
			}
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_($this->base . '&scope=' . $section . '/' . $category->alias . '/' . $thread), // . '#c' . $model->id),
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
		$section  = JRequest::getVar('section', '');
		$category = JRequest::getVar('category', '');

		// Is the user logged in?
		if ($this->juser->get('guest'))
		{
			$this->setRedirect(
				JRoute::_($this->base . '&scope=' . $section . '/' . $category),
				JText::_('PLG_GROUPS_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$id = JRequest::getInt('thread', 0);

		// Initiate a forum object
		$model = new ForumTablePost($this->database);
		$model->load($id);

		// Make the sure the category exist
		if (!$model->id)
		{
			$this->setRedirect(
				JRoute::_($this->base . '&scope=' . $section . '/' . $category),
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
				JRoute::_($this->base . '&scope=' . $section . '/' . $category),
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
				JRoute::_($this->base . '&scope=' . $section . '/' . $category),
				$forum->getError(),
				'error'
			);
			return;
		}

		// Redirect to main listing
		$this->setRedirect(
			JRoute::_($this->base . '&scope=' . $section . '/' . $category),
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
			if (!JFolder::create($path))
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
			$row = new ForumTableAttachment($this->database);
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
			$return = JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum&scope=' . $section . '/' . $category . '/' . $thread . '/' . $post . '/' . $file);
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($return))
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
		$attach = new ForumTableAttachment($this->database);
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
		$this->model = new ForumTablePost($this->database);
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

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
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

		$sModel = new ForumTableSection($this->database);
		$sections = $sModel->getRecords(array(
			'scope'    => 'group',
			'scope_id' => $group->get('gidNumber')
		));

		// Do we have any IDs?
		if (count($sections) > 0)
		{
			// Loop through each ID
			foreach ($sections as $section)
			{
				// Get the categories in this section
				$cModel = new ForumTableCategory($this->database);
				$categories = $cModel->getRecords(array(
					'section_id' => $section->id,
					'scope'      => 'group',
					'scope_id'   => $group->get('gidNumber')
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
					$tModel = new ForumTablePost($this->database);
					if (!$tModel->setStateByCategory($cats, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
					{
						$this->setError($tModel->getError());
					}
					$log .= 'forum.section.' . $section->id . '.category.' . $category->id . '.post' . "\n";

					// Set all the categories to "deleted"
					if (!$cModel->setStateBySection($sModel->id, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
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
	 * Display blog settings
	 *
	 * @return     string
	 */
	private function settings()
	{
		$juser = JFactory::getUser();
		if ($juser->get('guest'))
		{
			$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return;
		}

		if ($this->authorized != 'manager' && $this->authorized != 'admin')
		{
			$this->setError(JText::_('PLG_GROUPS_FORUM_NOT_AUTHORIZED'));
			return $this->sections();
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'groups',
				'element' => $this->_name,
				'name'    => 'settings'
			)
		);
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->config     = $this->params;
		$view->model      = $this->model;

		$view->settings   = new \Hubzero\Plugin\Params($this->database);
		$view->settings->loadPlugin($this->group->get('gidNumber'), 'groups', $this->_name);

		$view->authorized = $this->authorized;
		$view->message    = (isset($this->message)) ? $this->message : '';

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		return $view->loadTemplate();
	}

	/**
	 * Save blog settings
	 *
	 * @return     void
	 */
	private function savesettings()
	{
		$juser = JFactory::getUser();
		if ($juser->get('guest'))
		{
			$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return;
		}

		if ($this->authorized != 'manager' && $this->authorized != 'admin')
		{
			$this->setError(JText::_('PLG_GROUPS_FORUM_NOT_AUTHORIZED'));
			return $this->sections();
		}

		$settings = JRequest::getVar('settings', array(), 'post');

		$row = new \Hubzero\Plugin\Params($this->database);
		if (!$row->bind($settings))
		{
			$this->setError($row->getError());
			return $this->settings();
		}

		// Get parameters
		$p = new JRegistry('');
		$p->loadArray(JRequest::getVar('params', '', 'post'));

		$row->params = $p->toString();

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			return $this->_settings();
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			return $this->_settings();
		}

		$this->redirect(
			JRoute::_('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&action=settings'),
			JText::_('PLG_GROUPS_FORUM_SETTINGS_SAVED')
		);
	}
}
