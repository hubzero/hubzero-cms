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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Groups Plugin class for assets
 */
class plgGroupsCollections extends JPlugin
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
			'name'  => $this->_name,
			'title' => JText::_('PLG_GROUPS_' . strtoupper($this->_name)),
			'default_access' => $this->params->get('plugin_access', 'members'),
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

		$this->juser    = JFactory::getUser();
		$this->database = JFactory::getDBO();

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'item.php');

		//are we returning html
		if ($return == 'html') 
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//Create user object
			//$juser =& JFactory::getUser();

			//get the group members
			$members = $group->get('members');

			//if set to nobody make sure cant access
			if ($group_plugin_acl == 'nobody') 
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if ($this->juser->get('guest') 
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members')) 
			{
				ximport('Hubzero_Module_Helper');
				$arr['html']  = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active)) . '</p>';
				$arr['html'] .= Hubzero_Module_Helper::renderModules('force_mod');
				return $arr;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array($this->juser->get('id'), $members) 
			 && $group_plugin_acl == 'members' 
			 && $authorized != 'admin') 
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
				return $arr;
			}
			
			$this->params->set('access-plugin', 0);
			if ($group_plugin_acl == 'members')
			{
				$this->params->set('access-plugin', 4);
			}

			//user vars
			$this->authorized = $authorized;

			//group vars
			$this->group      = $group;
			$this->members    = $members;

			// Set some variables so other functions have access
			$this->action     = $action;
			$this->option     = $option;
			$this->name       = substr($option, 4, strlen($option));

			//get the plugins params
			$p = new Hubzero_Plugin_Params($this->database);
			$this->params = $p->getParams($group->gidNumber, 'groups', $this->_name);

			$this->_authorize('board');
			$this->_authorize('bulletin');

			//push the css to the doc
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('groups', $this->_name);

			$this->dateFormat  = '%d %b, %Y';
			$this->timeFormat  = '%I:%M %p';
			//$this->monthFormat = '%b';
			//$this->yearFormat  = '%Y';
			//$this->dayFormat   = '%d';
			$this->tz = 0;
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$this->dateFormat  = 'd M, Y';
				$this->timeFormat  = 'h:i a';
				//$this->monthFormat = 'b';
				//$this->yearFormat  = 'Y';
				//$this->dayFormat   = 'd';
				$this->tz = true;
			}

			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'collection.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'post.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'asset.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'vote.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'helpers' . DS . 'tags.php');

			$task = '';
			$controller = 'board';
			$id = 0;

			$juri =& JURI::getInstance();
			$path = $juri->getPath();
			if (strstr($path, '/')) 
			{
				$path = str_replace('/groups/' . $this->group->get('cn') . '/' . $this->_name, '', $path);
				$path = ltrim($path, DS);
				$bits = explode('/', $path);

				$this->action = (isset($bits[0])) ? $bits[0] : $this->action;
				if (isset($bits[1]))
				{
					if (is_numeric($bits[1]))
					{
						$id = intval($bits[1]);
						$task = (isset($bits[2])) ? $bits[2] : $task;
					}
					else
					{
						$task = (isset($bits[1])) ? $bits[1] : $task;
					}
				}
			}
			if ($this->action == 'boards')
			{
				if ($id)
				{
					$this->action = 'board';
					JRequest::setVar('board', $id);
				}
				else if ($task)
				{
					$this->action = 'board';
				}
				$this->action = ($task) ? $task . $this->action : $this->action;
			}

			if ($this->action == 'posts')
			{
				$this->action = 'post';
				if (in_array($task, array('post', 'vote', 'repost', 'unpost', 'move', 'comment')))
				{
					$this->action = '';
				}
				if ($id)
				{
					JRequest::setVar('bulletin', $id);
				}
				$this->action = ($task) ? $task . $this->action : $this->action;
			}
			//$this->action = ($this->action) ? $this->action : $task . $controller;
			/*if ($controller == 'boards' && $id)
			{
				$controller = 'board';
				
			}
			
			if ($controller == 'posts' && $id)
			{
				JRequest::setVar('bulletin', $id);
			}
			
			$this->action = ($this->action) ? $this->action : $task . $controller;*/
			/*if (is_numeric($this->action)) 
			{
				$this->action = 'entry';
			}*/

			switch ($this->action)
			{
				// Comments
				case 'savecomment':   $arr['html'] = $this->_savecomment();   break;
				case 'newcomment':    $arr['html'] = $this->_newcomment();    break;
				case 'editcomment':   $arr['html'] = $this->_editcomment();   break;
				case 'deletecomment': $arr['html'] = $this->_deletecomment(); break;

				// Entries
				case 'savepost':   $arr['html'] = $this->_save();   break;
				case 'newpost':    $arr['html'] = $this->_new();    break;
				case 'editpost':   $arr['html'] = $this->_edit();   break;
				case 'deletepost': $arr['html'] = $this->_delete(); break;

				case 'comment':
				case 'post':   $arr['html'] = $this->_post();   break;
				case 'vote':   $arr['html'] = $this->_vote();   break;
				case 'repost': $arr['html'] = $this->_repost(); break;
				case 'unpost': $arr['html'] = $this->_unpost(); break;
				case 'move':   $arr['html'] = $this->_move();   break;

				case 'repostboard': $arr['html'] = $this->_repostboard(); break;
				case 'newboard':    $arr['html'] = $this->_newboard();    break;
				case 'editboard':   $arr['html'] = $this->_editboard();   break;
				case 'saveboard':   $arr['html'] = $this->_saveboard();   break;
				case 'deleteboard': $arr['html'] = $this->_deleteboard(); break;

				case 'boards': $arr['html'] = $this->_boards(); break;

				case 'board':
				default: $arr['html'] = $this->_board(); break;
			}
		}

		/*$filters = array();
		$filters['object_type'] = 'group';
		$filters['object_id']   = $group->get('gidNumber');
		$filters['state']       = 1;

		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$filters['access'] = 0;
		} 
		else 
		{
			if ($authorized != 'member' 
			 && $authorized != 'manager' 
			 && $authorized != 'admin') 
			{
				$filters['access'] = array(1, 0);
			}
		}

		$bulletin = new CollectionsTableItem($this->database);

		// Build the HTML meant for the "profile" tab's metadata overview
		$arr['metadata']['count'] = $bulletin->getCount($filters);*/

		return $arr;
	}

	/**
	 * Display a list of latest whiteboard entries
	 * 
	 * @return     string
	 */
	private function _boards()
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => $this->_name,
				'name'    => 'board',
				'layout'  => 'boards'
			)
		);
		$view->name        = $this->_name;
		$view->juser       = $this->juser;
		$view->option      = $this->option;
		$view->group       = $this->group;
		$view->params      = $this->params;

		Hubzero_Document::addPluginScript('groups', $this->_name, 'jquery.masonry');
		Hubzero_Document::addPluginScript('groups', $this->_name);

		// Filters for returning results
		$view->filters = array();
		//$view->filters['limit']       = JRequest::getInt('limit', 25);
		//$view->filters['start']       = JRequest::getInt('limitstart', 0);
		$view->filters['user_id']     = JFactory::getUser()->get('id');
		$view->filters['object_type'] = 'group';
		$view->filters['object_id']   = $this->group->get('gidNumber');
		//$view->filters['search']      = JRequest::getVar('search', '');
		$view->filters['state']       = 1;
		//$view->filters['board_id']    = JRequest::getInt('board', 0);

		$view->board = new CollectionsTableCollection($this->database);

		$filters = array(
			'object_type' => $view->filters['object_type'],
			'object_id'   => $view->filters['object_id'],
			'state'       => 1
		);
		if (!$this->params->get('access-manage-board')) 
		{
			$filters['access'] = 0;
		}
		$view->rows = $view->board->getRecords($filters);

		$bulletin = new CollectionsTableItem($this->database);
		$view->bulletins = $bulletin->getCount($view->filters);

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
	 * Display a list of latest whiteboard entries
	 * 
	 * @return     string
	 */
	private function _board()
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => $this->_name,
				'name'    => 'board'
			)
		);
		$view->name        = $this->_name;
		$view->juser       = $this->juser;
		$view->option      = $this->option;
		$view->group       = $this->group;
		$view->params      = $this->params;

		$view->dateFormat  = $this->dateFormat;
		$view->timeFormat  = $this->timeFormat;
		$view->tz          = $this->tz;

		Hubzero_Document::addPluginScript('groups', $this->_name, 'jquery.masonry');
		Hubzero_Document::addPluginScript('groups', $this->_name);

		// Filters for returning results
		$view->filters = array();
		$view->filters['limit']       = JRequest::getInt('limit', 25);
		$view->filters['start']       = JRequest::getInt('limitstart', 0);
		$view->filters['user_id']     = JFactory::getUser()->get('id');
		$view->filters['object_type'] = 'group';
		$view->filters['object_id']   = $this->group->get('gidNumber');
		$view->filters['search']      = JRequest::getVar('search', '');
		$view->filters['state']       = 1;
		$view->filters['board_id']    = JRequest::getInt('board', 0);

		$view->board = new CollectionsTableCollection($this->database);
		if (!$view->filters['board_id'])
		{
			$view->board->loadDefault($view->filters['object_id'], $view->filters['object_type']);
		}
		else
		{
			$view->board->load($view->filters['board_id']);
		}
		if (!$view->board->id)
		{
			$view->board->setup($view->filters['object_id'], $view->filters['object_type']);
		}

		$view->boards = $view->board->getCount(array(
			'object_type' => $view->filters['object_type'],
			'object_id'   => $view->filters['object_id'],
			'state'       => 1
		));
		/*$view->boards = $view->board->getRecords(array(
			'object_type' => $view->filters['object_type'],
			'object_id'   => $view->filters['object_id'],
			'state'       => 1
		));*/

		$view->filters['board_id'] = $view->board->id;

		$bulletin = new CollectionsTableItem($this->database);
		$view->rows = $bulletin->getRecords($view->filters);

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
	 * Display a whiteboard entry
	 * 
	 * @return     string
	 */
	private function _post()
	{
		/*$app =& JFactory::getApplication();

		$board = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->_name);
		if ($this->juser->get('guest')) 
		{
			$app->enqueueMessage(JText::_('GROUPS_LOGIN_NOTICE'), 'warning');
			$app->redirect('/login?return=' . base64_encode($board));
			return;
		}

		if (!$this->params->get('access-view-bulletin')) 
		{
			$app->enqueueMessage(JText::_('You are not authorized to view this bulletin board entry.'), 'error');
			$app->redirect($board);
			return;
		}*/

		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => $this->_name,
				'name'    => 'entry'
			)
		);
		$view->option = $this->option;
		$view->group = $this->group;
		$view->params = $this->params;
		$view->juser = $this->juser;
		$view->name = $this->_name;

		$view->dateFormat  = $this->dateFormat;
		$view->timeFormat  = $this->timeFormat;
		$view->tz          = $this->tz;

		/*if (isset($this->entry) && is_object($this->entry)) 
		{
			$view->row = $this->entry;
		} 
		else 
		{
			$juri =& JURI::getInstance();
			$path = $juri->getPath();
			if (strstr($path, '/')) 
			{
				$path = str_replace('/groups/' . $this->group->get('cn') . '/whiteboard/', '', $path);
				$bits = explode('/', $path);
				$alias = end($bits);
			}

			$view->row = new whiteboardEntry($this->database);
			$view->row->loadAlias($alias, 'group', 0, $this->group->get('gidNumber'));
		}*/
		$post_id = JRequest::getInt('bulletin', 0);
		//$board_id    = JRequest::getInt('board', 0);

		$view->post = new CollectionsTablePost($this->database);
		$view->post->load($post_id);

		$view->row = new CollectionsTableItem($this->database);
		$view->row->load($view->post->bulletin_id);
		$view->row->reposts = $view->row->getReposts();
		$view->row->voted   = $view->row->getVote();

		if (!$view->row->id) 
		{
			return $this->_board();
		}

		// Check authorization
		if (!$this->params->get('access-view-bulletin')) 
		{
			JError::raiseError(403, JText::_('PLG_GROUPS_BULLETINBOARD_NOT_AUTH'));
			return;
		}

		ximport('Hubzero_Item_Comment');
		$bc = new Hubzero_Item_Comment($this->database);
		$view->comments = $bc->getComments($view->row->id);

		//count($this->comments, COUNT_RECURSIVE)
		$view->comment_total = 0;
		if ($view->comments) 
		{
			foreach ($view->comments as $com)
			{
				$view->comment_total++;
				if ($com->replies) 
				{
					foreach ($com->replies as $rep)
					{
						$view->comment_total++;
						if ($rep->replies) 
						{
							$view->comment_total = $view->comment_total + count($rep->replies);
						}
					}
				}
			}
		}
		$view->board = new CollectionsTableCollection($this->database);
		$view->board->load($view->post->board_id);

		$bt = new CollectionsTableTags($this->database);
		$view->tags = $bt->get_tag_cloud(0, 0, $view->row->id);

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
	 * Display a form for creating an entry
	 * 
	 * @return     string
	 */
	private function _new()
	{
		return $this->_edit();
	}

	/**
	 * Display a form for editing an entry
	 * 
	 * @return     string
	 */
	private function _edit()
	{
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		if (!$this->params->get('access-create-bulletin') && !$this->params->get('access-edit-bulletin')) 
		{
			$board = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->_name);
			$app =& JFactory::getApplication();
			$app->enqueueMessage(JText::_('You are not authorized to edit this bulletin board entry.'), 'error');
			$app->redirect($board);
			return;
		}

		ximport('Hubzero_Plugin_View');
		$no_html = JRequest::getInt('no_html', 0);
		if ($no_html)
		{
			$type = strtolower(JRequest::getWord('type', 'file'));
			if (!in_array($type, array('file', 'image', 'text', 'link')))
			{
				$type = 'file';
			}

			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'groups',
					'element' => $this->_name,
					'name'    => 'edit',
					'layout'  => '_' . $type
				)
			);
		}
		else
		{
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'groups',
					'element' => $this->_name,
					'name'    => 'edit'
				)
			);
		}

		$view->name       = $this->_name;
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->task       = $this->action;
		$view->params     = $this->params;
		//$view->authorized = $this->authorized;

		$view->dateFormat  = $this->dateFormat;
		$view->timeFormat  = $this->timeFormat;
		//$view->yearFormat  = $this->yearFormat;
		//$view->monthFormat = $this->monthFormat;
		//$view->dayFormat   = $this->dayFormat;
		$view->tz          = $this->tz;

		$id = JRequest::getInt('bulletin', 0);

		$view->entry = new CollectionsTableItem($this->database);
		$view->entry->load($id);

		$view->board = new CollectionsTableCollection($this->database);
		/*$view->board->loadDefault($this->group->get('gidNumber'), 'group');
		if (!$view->board->id)
		{
			$view->board->setup($this->group->get('gidNumber'), 'group');
		}*/
		if ($remove = JRequest::getInt('remove', 0))
		{
			$attachment = new CollectionsTableAsset($this->database);
			$attachment->remove($remove);
		}

		if ($no_html)
		{
			$view->display();
			exit;
		}
		else
		{
			$view->boards = $view->board->getRecords(array(
				'object_type' => 'group',
				'object_id'   => $this->group->get('gidNumber'),
				'state'       => 1
			));
			if (!$view->boards)
			{
				$view->board->setup($this->group->get('gidNumber'), 'group');
				$view->boards = array($view->board);
			}
	
			Hubzero_Document::addPluginScript('groups', $this->_name);

			$bt = new CollectionsTableTags($this->database);
			$view->tags = $bt->get_tag_string($view->entry->id);

			return $view->loadTemplate();
		}
	}

	/**
	 * Save an entry
	 * 
	 * @return     void
	 */
	private function _save()
	{
		if ($this->juser->get('guest')) 
		{
			//$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return $this->_login();
		}

		if (!$this->params->get('access-edit-bulletin') || !$this->params->get('access-create-bulletin')) 
		{
			$this->setError(JText::_('PLG_GROUPS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_board();
		}

		$fields = JRequest::getVar('fields', array(), 'post');

		$row = new CollectionsTableItem($this->database);
		if (!$row->bind($fields)) 
		{
			$this->setError($row->getError());
			return $this->_edit();
		}

		$files = JRequest::getVar('fls', '', 'files', 'array');
		if ($row->type == 'image' || $row->type == 'file')
		{
			if (!$files || count($files['name']) <= 0)
			{
				$this->setError(JText::_('Please provide a file'));
				return $this->_edit();
			}
		}

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			return $this->_edit();
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			return $this->_edit();
		}

		if ($row->type == 'image' || $row->type == 'file')
		{
			$descriptions = JRequest::getVar('description', array(), 'post');
			if (!$this->_upload($files, $row->id, $row->type, $descriptions))
			{
				return $this->_edit();
			}

			$assets = JRequest::getVar('asset', array(), 'post');
			if ($assets && count($assets) > 0)
			{
				foreach ($assets as $asset)
				{
					$attachment = new CollectionsTableAsset($this->database);
					$attachment->load(intval($asset['id']));
					$attachment->description = (isset($asset['description'])) ? trim($asset['description']) : '';

					if (!$attachment->check()) 
					{
						$this->setError($attachment->getError());
						continue;
					}
					if (!$attachment->store()) 
					{
						$this->setError($attachment->getError());
					}
				}
			}
		}

		$stick = new CollectionsTablePost($this->database);
		$stick->loadByBoard($board_id, $bulletin_id);
		$stick->bulletin_id = $row->id;
		$stick->board_id    = $fields['board_id'];
		$stick->original    = 1;
		if ($stick->check()) 
		{
			// Store new content
			if (!$stick->store()) 
			{
				$this->setError($stick->getError());
			}
		}

		// Process tags
		$tags = trim(JRequest::getVar('tags', ''));
		$bt = new CollectionsTableTags($this->database);
		$bt->tag_object($juser->get('id'), $row->id, $tags, 1, 1);

		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->_name));
	}

	/**
	 * Repost an entry
	 * 
	 * @return     string
	 */
	private function _repost()
	{
		if ($this->juser->get('guest')) 
		{
			//$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return $this->_login();
		}

		if (!$this->params->get('access-create-bulletin')) 
		{
			$this->setError(JText::_('PLG_GROUPS_BULLETINBOARD_NOT_AUTHORIZED'));
			return $this->_board();
		}

		// Incoming
		$bulletin_id = JRequest::getInt('bulletin', 0);
		$board_id    = JRequest::getInt('board', 0);
		$no_html     = JRequest::getInt('no_html', 0);

		if (!$bulletin_id && $board_id)
		{
			$b = new CollectionsTableItem($this->database);
			$b->loadType($board_id, 'board');
			if (!$b->id)
			{
				$row = new CollectionsTableCollection($this->database);
				$row->load($board_id);

				$b->type        = 'board';
				$b->object_id   = $row->id;
				$b->title       = $row->title;
				$b->description = $row->description;
				if (!$b->check()) 
				{
					$this->setError($b->getError());
				}
				// Store new content
				if (!$b->store()) 
				{
					$this->setError($b->getError());
				}
			}
			$bulletin_id = $b->id;
			$board_id = 0;
		}

		// No board ID selected so present repost form
		if (!$board_id)
		{
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'groups',
					'element' => $this->_name,
					'name'    => 'edit',
					'layout'  => 'repost'
				)
			);

			$board = new CollectionsTableCollection($this->database);

			$view->myboards = $board->getRecords(array(
				'object_type' => 'member',
				'object_id'   => $this->juser->get('id'),
				'state'       => 1
			));
			if (!$view->myboards)
			{
				$board->setup($juser->get('id'), 'member');
				$view->myboards = array($board);
			}

			$view->groupboards = array();

			$member = Hubzero_User_Profile::getInstance($this->juser->get('id'));

			$usergroups = $member->getGroups('members');
			if ($usergroups)
			{
				foreach ($usergroups as $usergroup)
				{
					$groups = $board->getRecords(array(
						'object_type' => 'group',
						'object_id'   => $usergroup->gidNumber,
						'state'       => 1
					));
					if ($groups)
					{
						foreach ($groups as $s)
						{
							if (!isset($view->groupboards[$s->group_alias]))
							{
								$view->groupboards[$s->group_alias] = array();
							}
							if ($s->access == 4 && !$usergroup->manager)
							{
								continue;
							}
							$view->groupboards[$s->group_alias][] = $s;
							asort($view->groupboards[$s->group_alias]);
						}
					}
				}
			}

			asort($view->groupboards);

			$view->name        = $this->_name;
			$view->option      = $this->option;
			$view->group       = $this->group;
			//$view->task        = $this->action;
			//$view->params      = $this->params;
			//$view->authorized  = $this->authorized;

			$view->no_html     = $no_html;
			$view->bulletin_id = $bulletin_id;

			if ($no_html)
			{
				$view->display();
				exit;
			}
			else 
			{
				return $view->loadTemplate();
			}
		}

		// Try loading the current board/bulletin to see
		// if this has already been posted to the board (i.e., no duplicates)
		$stick = new CollectionsTablePost($this->database);
		$stick->loadByBoard($board_id, $bulletin_id);
		if (!$stick->id)
		{
			// No record found -- we're OK to add one
			$stick->bulletin_id = $bulletin_id;
			$stick->board_id    = $board_id;
			$stick->description = JRequest::getVar('description', '');
			if ($stick->check()) 
			{
				// Store new content
				if (!$stick->store()) 
				{
					$this->setError($stick->getError());
				}
			}
		}

		// Display updated bulletin stats if called via AJAX
		if ($no_html)
		{
			echo JText::sprintf('%s reposts', $stick->getCount(array('bulletin_id' => $stick->bulletin_id, 'original' => 0)));
			exit;
		}

		// Display the main listing
		return $this->_board();
	}

	/**
	 * Redirect to the login form
	 * 
	 * @return     void
	 */
	private function _login()
	{
		$board = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->_name);
		$app =& JFactory::getApplication();
		$app->enqueueMessage(JText::_('GROUPS_LOGIN_NOTICE'), 'warning');
		$app->redirect(JRoute::_('index.php?option=com_login&return=' . base64_encode($board)));
		return;
	}

	/**
	 * Repost an entry
	 * 
	 * @return     string
	 */
	private function _unpost()
	{
		if ($this->juser->get('guest')) 
		{
			//$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return $this->_login();
		}

		if (!$this->params->get('access-create-bulletin')) 
		{
			$this->setError(JText::_('PLG_GROUPS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_board();
		}

		// Incoming
		$post_id = JRequest::getInt('bulletin', 0);
		$no_html = JRequest::getInt('no_html', 0);

		// Try loading the current board/bulletin to see
		// if this has already been posted to the board (i.e., no duplicates)
		$post = new CollectionsTablePost($this->database);
		$post->load($post_id);

		$board_id = $post->board_id;

		// Can't unpost original posts. They must be deleted instead.
		if (!$post->original)
		{
			if (!$post->delete()) 
			{
				$this->setError($post->getError());
			}
		}

		if ($no_html)
		{
			echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=boards/' . $board_id);
			exit;
		}

		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=boards/' . $board_id));
	}

	/**
	 * Move a bulletin to another board
	 * 
	 * @return     void
	 */
	private function _move()
	{
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		if (!$this->params->get('access-create-bulletin')) 
		{
			$this->setError(JText::_('PLG_GROUPS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_board();
		}

		// Incoming
		$post_id = JRequest::getInt('post', 0);
		$no_html = JRequest::getInt('no_html', 0);

		$stick = new CollectionsTablePost($this->database);
		$stick->load($post_id);
		$stick->board_id = JRequest::getInt('board', 0);
		if ($stick->check()) 
		{
			// Store new content
			if (!$stick->store()) 
			{
				$this->setError($stick->getError());
			}
		}

		if ($no_html)
		{
			echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->_name);
			exit;
		}

		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->_name));
	}

	/**
	 * Delete an entry
	 * 
	 * @return     string
	 */
	private function _delete()
	{
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		if (!$this->params->get('access-delete-bulletin')) 
		{
			$this->setError(JText::_('PLG_GROUPS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_board();
		}

		// Incoming
		$no_html = JRequest::getInt('no_html', 0);
		$id = JRequest::getInt('bulletin', 0);
		if (!$id) 
		{
			return $this->_board();
		}

		$process = JRequest::getVar('process', '');
		$confirmdel = JRequest::getVar('confirmdel', '');

		// Initiate a whiteboard entry object
		$bulletin = new CollectionsTableItem($this->database);
		$bulletin->load($id);

		// Did they confirm delete?
		if (!$process || !$confirmdel) 
		{
			if ($process && !$confirmdel) 
			{
				$this->setError(JText::_('PLG_GROUPS_' . strtoupper($this->_name) . '_ERROR_CONFIRM_DELETION'));
			}

			// Output HTML
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'groups',
					'element' => $this->_name,
					'name'    => 'delete'
				)
			);
			$view->option   = $this->option;
			$view->group    = $this->group;
			$view->task     = $this->action;
			$view->params   = $this->params;
			$view->bulletin = $bulletin;
			$view->no_html  = $no_html;
			$view->name     = $this->_name;

			if ($this->getError()) 
			{
				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}
			}
			return $view->loadTemplate();
		}

		// Mark the entry as deleted
		$bulletin->state = 2;

		if (!$bulletin->store()) 
		{
			$this->setError($bulletin->getError());
		}

		if ($no_html)
		{
			echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->_name);
			exit;
		}

		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->_name));
	}

	/**
	 * Save a comment
	 * 
	 * @return     string
	 */
	private function _savecomment()
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		// Incoming
		$comment = JRequest::getVar('comment', array(), 'post');

		// Instantiate a new comment object and pass it the data
		ximport('Hubzero_Item_Comment');
		$row = new Hubzero_Item_Comment($this->database);
		if (!$row->bind($comment)) 
		{
			$this->setError($row->getError());
			return $this->_post();
		}

		// Set the created time
		if (!$row->id) 
		{
			$row->created = date('Y-m-d H:i:s', time());  // use gmdate() ?
		}

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			return $this->_post();
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			return $this->_post();
		}

		return $this->_post();
	}

	/**
	 * Delete a comment
	 * 
	 * @return     string
	 */
	private function _deletecomment()
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		// Incoming
		$id = JRequest::getInt('comment', 0);
		if (!$id) 
		{
			return $this->_post();
		}

		// Initiate a whiteboard comment object
		ximport('Hubzero_Item_Comment');
		$comment = new Hubzero_Item_Comment($this->database);

		// Delete the entry itself
		if (!$comment->delete($id)) 
		{
			$this->setError($comment->getError());
		}

		// Return the topics list
		return $this->_post();
	}

	/**
	 * Upload a file
	 * 
	 * @return     void
	 */
	private function _vote()
	{
		$id = JRequest::getInt('bulletin', 0);

		// Create a vote record
		$vote = new CollectionsTableVote($this->database);
		$vote->loadByBulletin($id, $this->juser->get('id'));

		$like = true;

		if (!$vote->id)
		{
			$vote->bulletin_id = $id;
			// Store the record
			if (!$vote->check())
			{
				$this->setError($vote->getError());
			}
			else
			{
				if (!$vote->store())
				{
					$this->setError(JText::_('Error occurred while saving vote'));
				}
			}
		}
		else
		{
			$like = false;
			// Load the vote record
			if (!$vote->delete())
			{
				$this->setError($vote->getError());
			}
		}

		// Load the bulletin
		$bulletin = new CollectionsTableItem($this->database);
		$bulletin->load($id);
		if (!$this->getError())
		{
			if ($like)
			{
				// Increase like count
				$bulletin->positive++;
			}
			else if ($bulletin->positive > 0) // Make sure we don't go below 0
			{
				// Decrease like count
				$bulletin->positive--;
			}
			$bulletin->store();
		}

		// Display updated bulletin stats if called via AJAX
		$no_html = JRequest::getInt('no_html', 0);
		if ($no_html)
		{
			echo JText::sprintf('%s likes', $bulletin->positive);
			exit;
		}

		// Display the main listing
		return $this->_board();
	}

	/**
	 * Upload a file
	 * 
	 * @return     void
	 */
	private function _upload($files, $listdir, $type, $descriptions)
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest')) 
		{
			$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return false;
		}

		// Ensure we have an ID to work with
		//$listdir = JRequest::getInt('listdir', 0, 'post');
		if (!$listdir) 
		{
			$this->setError(JText::_('WIKI_NO_ID'));
			return false;
		}

		// Build the upload path if it doesn't exist
		$path = JPATH_ROOT . DS . trim($this->params->get('filepath', '/site/bulletins'), DS) . DS . $listdir;

		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				$this->setError(JText::_('Error uploading. Unable to create path.'));
				return false;
			}
		}

		foreach ($files['name'] as $i => $file)
		{
			// Incoming file
			//$file = JRequest::getVar('upload', '', 'files', 'array');
			if (!$files['name'][$i]) 
			{
				$this->setError(JText::_('WIKI_NO_FILE'));
				return false;
			}

			$ext = strtolower(JFile::getExt($files['name'][$i]));
			if ($type == 'image' && !in_array($ext, array('jpg', 'jpeg', 'jpe', 'gif', 'png')))
			{
				continue;
			}

			// Make the filename safe
			jimport('joomla.filesystem.file');
			$files['name'][$i] = urldecode($files['name'][$i]);
			$files['name'][$i] = JFile::makeSafe($files['name'][$i]);
			$files['name'][$i] = str_replace(' ', '_', $files['name'][$i]);

			// Upload new files
			if (!JFile::upload($files['tmp_name'][$i], $path . DS . $files['name'][$i])) 
			{
				$this->setError(JText::_('ERROR_UPLOADING'));
				return false;
			}
			// File was uploaded 
			else 
			{
				// Create database entry
				$attachment = new CollectionsTableAsset($this->database);
				$attachment->bulletin_id = $listdir;
				$attachment->filename    = $files['name'][$i];
				$attachment->description = (isset($descriptions[$i])) ? $descriptions[$i] : '';

				if (!$attachment->check()) 
				{
					$this->setError($attachment->getError());
					return false;
				}
				if (!$attachment->store()) 
				{
					$this->setError($attachment->getError());
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Display a form for creating an entry
	 * 
	 * @return     string
	 */
	private function _newboard()
	{
		return $this->_editboard();
	}

	/**
	 * Display a form for editing an entry
	 * 
	 * @return     string
	 */
	private function _editboard($row=null)
	{
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		if (!$this->params->get('access-create-board') && !$this->params->get('access-edit-board')) 
		{
			$board = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->_name);
			$app =& JFactory::getApplication();
			$app->enqueueMessage(JText::_('You are not authorized to edit this bulletin board.'), 'error');
			$app->redirect($board);
			return;
		}

		$no_html = JRequest::getInt('no_html', 0);

		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => $this->_name,
				'name'    => 'board',
				'layout'  => 'edit'
			)
		);

		$view->name       = $this->_name;
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->task       = $this->action;
		$view->params     = $this->params;

		if (is_object($row))
		{
			$view->entry = $row;
		}
		else
		{
			$id = JRequest::getInt('board', 0);

			$view->entry = new CollectionsTableCollection($this->database);
			$view->entry->load($id);
		}

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		if ($no_html)
		{
			$view->display();
			exit;
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a form for editing an entry
	 * 
	 * @return     string
	 */
	private function _saveboard()
	{
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		if (!$this->params->get('access-edit-board') || !$this->params->get('access-create-board')) 
		{
			$this->setError(JText::_('PLG_GROUPS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_board();
		}

		$fields = JRequest::getVar('fields', array(), 'post');

		$row = new CollectionsTableCollection($this->database);
		if (!$row->bind($fields)) 
		{
			$this->setError($row->getError());
			return $this->_editboard($row);
		}

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			return $this->_editboard($row);
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			return $this->_editboard($row);
		}

		$bulletin = new CollectionsTableItem($this->database);
		$bulletin->loadType($row->id, 'board');
		if (!$bulletin->id)
		{
			$bulletin->type = 'board';
			$bulletin->object_id = $row->id;
			$bulletin->title = $row->title;
			$bulletin->description = $row->description;
			//$bulletin->url = '/' . $row->object_type . 's/' . $object_id . '/' . $this->_name;
			if (!$bulletin->check()) 
			{
				$this->setError($bulletin->getError());
			}
			// Store new content
			if (!$bulletin->store()) 
			{
				$this->setError($bulletin->getError());
			}
		}

		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=boards/' . $row->id));
	}

	/**
	 * Set permissions
	 * 
	 * @param      string  $assetType Type of asset to set permissions for (component, section, category, thread, post)
	 * @param      integer $assetId   Specific object to check permissions for
	 * @return     void
	 */
	protected function _authorize($assetType='plugin', $assetId=null)
	{
		// Everyone can view by default
		$this->params->set('access-view', true);
		if (!$this->juser->get('guest')) 
		{
			// Set asset to viewable
			$this->params->set('access-view-' . $assetType, false);
			if (in_array($this->juser->get('id'), $this->members))
			{
				$this->params->set('access-view-' . $assetType, true);
			}
			// Set asset to NOT viewable if unpublished
			if (isset($this->model) && is_object($this->model))
			{
				if (!$this->model->state)
				{
					$this->params->set('access-view-' . $assetType, false);
				}
			}
			// Can NOT create, delete, or edit by default
			$this->params->set('access-create-' . $assetType, false);
			$this->params->set('access-delete-' . $assetType, false);
			$this->params->set('access-edit-' . $assetType, false);
			switch ($assetType)
			{
				case 'board':
					// Only managers and admins can work with boards
					if ($this->authorized == 'admin' || $this->authorized == 'manager')
					{
						$this->params->set('access-manage-' . $assetType, true);
						$this->params->set('access-create-' . $assetType, true);
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
				break;
				case 'bulletin':
					// All members can post bulletins
					$this->params->set('access-create-' . $assetType, true);
					$this->params->set('access-delete-' . $assetType, true);
					$this->params->set('access-edit-' . $assetType, true);
					$this->params->set('access-view-' . $assetType, true);
					if ($this->authorized == 'admin' || $this->authorized == 'manager')
					{
						$this->params->set('access-manage-' . $assetType, true);
					}
				break;
				case 'plugin':
				default:
					// Only managers and admins
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
}
