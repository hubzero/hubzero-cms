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
class plgGroupsBulletinboard extends JPlugin
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

		//are we returning html
		if ($return == 'html') 
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//Create user object
			$juser =& JFactory::getUser();

			//get the group members
			$members = $group->get('members');

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
				ximport('Hubzero_Module_Helper');
				$arr['html']  = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active)) . '</p>';
				$arr['html'] .= Hubzero_Module_Helper::renderModules('force_mod');
				return $arr;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array($juser->get('id'), $members) 
			 && $group_plugin_acl == 'members' 
			 && $authorized != 'admin') 
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
				return $arr;
			}

			//user vars
			$this->juser      = $juser;
			$this->authorized = $authorized;

			//group vars
			$this->group      = $group;
			$this->members    = $members;

			// Set some variables so other functions have access
			$this->action     = $action;
			$this->option     = $option;
			$this->name       = substr($option, 4, strlen($option));
			$this->database   = JFactory::getDBO();

			//get the plugins params
			$p = new Hubzero_Plugin_Params($this->database);
			$this->params = $p->getParams($group->gidNumber, 'groups', $this->_name);

			//push the css to the doc
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('groups', $this->_name);

			$this->dateFormat  = '%d %b, %Y';
			$this->timeFormat  = '%I:%M %p';
			$this->monthFormat = '%b';
			$this->yearFormat  = '%Y';
			$this->dayFormat   = '%d';
			$this->tz = 0;
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$this->dateFormat  = 'd M, Y';
				$this->timeFormat  = 'h:i a';
				$this->monthFormat = 'b';
				$this->yearFormat  = 'Y';
				$this->dayFormat   = 'd';
				$this->tz = true;
			}

			//include helpers
			//include_once(JPATH_ROOT . DS . 'components' . DS . 'com_whiteboard' . DS . 'tables' . DS . 'whiteboard.comment.php');
			//include_once(JPATH_ROOT . DS . 'components' . DS . 'com_whiteboard' . DS . 'helpers' . DS . 'whiteboard.member.php');
			//include_once(JPATH_ROOT . DS . 'components' . DS . 'com_whiteboard' . DS . 'helpers' . DS . 'whiteboard.tags.php');
			$path = dirname(__FILE__);

			include_once($path . DS . 'tables' . DS . 'board.php');
			include_once($path . DS . 'tables' . DS . 'bulletin.php');
			include_once($path . DS . 'tables' . DS . 'stick.php');
			include_once($path . DS . 'tables' . DS . 'asset.php');
			include_once($path . DS . 'tables' . DS . 'vote.php');
			include_once($path . DS . 'helpers' . DS . 'tags.php');

			if (is_numeric($this->action)) 
			{
				$this->action = 'entry';
			}

			switch ($this->action)
			{
				// Settings
				case 'savesettings': $arr['html'] = $this->_savesettings(); break;
				case 'settings':     $arr['html'] = $this->_settings();     break;

				// Comments
				case 'savecomment':   $arr['html'] = $this->_savecomment();   break;
				case 'newcomment':    $arr['html'] = $this->_newcomment();    break;
				case 'editcomment':   $arr['html'] = $this->_editcomment();   break;
				case 'deletecomment': $arr['html'] = $this->_deletecomment(); break;

				// Entries
				case 'save':   $arr['html'] = $this->_save();   break;
				case 'new':    $arr['html'] = $this->_new();    break;
				case 'edit':   $arr['html'] = $this->_edit();   break;
				case 'delete': $arr['html'] = $this->_delete(); break;
				case 'entry':  $arr['html'] = $this->_entry();  break;
				case 'vote':   $arr['html'] = $this->_vote();   break;
				case 'repost': $arr['html'] = $this->_repost(); break;

				case 'browse':
				default: $arr['html'] = $this->_browse(); break;
			}
		}

		/*$filters = array();
		$filters['scope']    = 'group';
		$filters['group_id'] = $group->get('gidNumber');

		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$filters['state'] = 'public';
		} 
		else 
		{
			if ($authorized != 'member' 
			 && $authorized != 'manager' 
			 && $authorized != 'admin') 
			{
				$filters['state'] = 'registered';
			}
		}*/

		//$be = new whiteboardEntry(JFactory::getDBO());

		// Build the HTML meant for the "profile" tab's metadata overview
		//$arr['metadata']['count'] = $be->getCount($filters);

		return $arr;
	}

	/**
	 * Display a list of latest whiteboard entries
	 * 
	 * @return     string
	 */
	private function _browse()
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => $this->_name,
				'name'    => 'browse'
			)
		);
		$view->name = $this->_name;
		$view->juser      = $this->juser;
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->config     = $this->params;
		$view->authorized = $this->authorized;

		$view->dateFormat  = $this->dateFormat;
		$view->timeFormat  = $this->timeFormat;
		$view->yearFormat  = $this->yearFormat;
		$view->monthFormat = $this->monthFormat;
		$view->dayFormat   = $this->dayFormat;
		$view->tz          = $this->tz;
		$view->canpost = true;

		Hubzero_Document::addPluginScript('groups', $this->_name, 'jquery.masonry');
		Hubzero_Document::addPluginScript('groups', $this->_name);

		// Filters for returning results
		$filters = array();
		$filters['limit']      = JRequest::getInt('limit', 25);
		$filters['start']      = JRequest::getInt('limitstart', 0);
		/*$filters['created_by'] = JRequest::getInt('author', 0);
		$filters['year']       = JRequest::getInt('year', 0);
		$filters['month']      = JRequest::getInt('month', 0);*/
		$filters['user_id']     = JFactory::getUser()->get('id');
		$filters['object_type'] = 'group';
		$filters['object_id']   = $this->group->get('gidNumber');
		$filters['search']      = JRequest::getVar('search', '');

		/*$view->canpost = $this->_getPostingPermissions();

		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$filters['state'] = 'public';
		} 
		else 
		{
			if ($this->authorized != 'member' 
			 && $this->authorized != 'manager' 
			 && $this->authorized != 'admin') 
			{
				$filters['state'] = 'registered';
			}
		}

		$be = new whiteboardEntry($this->database);

		$total = $be->getCount($filters);

		$view->rows = $be->getRecords($filters);
		if ($filters['search']) 
		{
			$view->rows = $this->_highlight($filters['search'], $view->rows);
		}

		jimport('joomla.html.pagination');
		$pageNav = new JPagination(
			$total, 
			$filters['start'], 
			$filters['limit']
		);

		$pageNav->setAdditionalUrlParam('gid', $this->group->get('cn'));
		$pageNav->setAdditionalUrlParam('active', 'whiteboard');
		if ($filters['year'])
		{
			$pageNav->setAdditionalUrlParam('year', $filters['year']);
		}
		if ($filters['month'])
		{
			$pageNav->setAdditionalUrlParam('month', $filters['month']);
		}
		if ($filters['search'])
		{
			$pageNav->setAdditionalUrlParam('search', $filters['search']);
		}

		$path = $this->params->get('uploadpath', '/site/groups/{{gid}}/whiteboard');
		$view->path = str_replace('{{gid}}', $this->group->get('gidNumber'),$path);

		$view->firstentry = $be->getDateOfFirstEntry($filters);

		$view->popular = $be->getPopularEntries($filters);
		$view->recent = $be->getRecentEntries($filters);

		$view->year = $filters['year'];
		$view->month = $filters['month'];
		$view->search = $filters['search'];
		$view->pagenavhtml = $pageNav->getListFooter();*/

		$view->board = new BulletinboardBoard($this->database);
		$view->board->loadDefault($filters['object_id'], $filters['object_type']);
		if (!$view->board->id)
		{
			$view->board->setup($filters['object_id'], $filters['object_type']);
		}

		$filters['board_id'] = $view->board->id;

		$bulletin = new BulletinboardBulletin($this->database);
		$view->rows = $bulletin->getRecords($filters);

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
	 * Determine permissions to post an entry
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	private function _getPostingPermissions()
	{
		switch ($this->params->get('posting'))
		{
			case 1:
				if ($this->authorized == 'manager' || $this->authorized == 'admin') 
				{
					return true;
				}
			break;

			case 0:
			default:
				if ($this->authorized == 'member' || $this->authorized == 'manager' || $this->authorized == 'admin') 
				{
					return true;
				} 
				else 
				{
					return false;
				}
			break;
		}

		return false;
	}

	/**
	 * Display a whiteboard entry
	 * 
	 * @return     string
	 */
	private function _entry()
	{
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
		$view->config = $this->params;
		$view->authorized = $this->authorized;
		$view->juser = $this->juser;

		$view->dateFormat  = $this->dateFormat;
		$view->timeFormat  = $this->timeFormat;
		$view->yearFormat  = $this->yearFormat;
		$view->monthFormat = $this->monthFormat;
		$view->dayFormat   = $this->dayFormat;
		$view->tz          = $this->tz;

		if (isset($this->entry) && is_object($this->entry)) 
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
		}

		if (!$view->row->id) 
		{
			return $this->_browse();
		}

		// Check authorization
		$juser =& JFactory::getUser();
		if (($view->row->state == 2 && $juser->get('guest'))
		 || ($view->row->state == 0 && $juser->get('id') != $view->row->created_by && $this->authorized != 'member' && $this->authorized != 'manager' && $this->authorized != 'admin')) 
		{
			JError::raiseError(403, JText::_('PLG_GROUPS_whiteboard_NOT_AUTH'));
			return;
		}

		//$juser =& JFactory::getUser();
		if ($juser->get('id') != $view->row->created_by) 
		{
			$view->row->hit();
		}

		if ($view->row->content) 
		{
			ximport('Hubzero_Wiki_Parser');
			$p =& Hubzero_Wiki_Parser::getInstance();

			if ($view->row->scope == 'member') 
			{
				$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
				}
				
				$plugin = JPluginHelper::getPlugin('members', 'whiteboard');
				$params = new $paramsClass($plugin->params);
				$path = $params->get('uploadpath', '/site/members/{{uid}}/whiteboard');
				$path = str_replace('{{uid}}', Hubzero_View_Helper_Html::niceidformat($view->row->created_by), $path);
			} 
			else 
			{
				$path = $this->params->get('uploadpath', '/site/groups/{{gid}}/whiteboard');
				$path = str_replace('{{gid}}', $this->group->get('gidNumber'), $path);
			}
			
			$wikiconfig = array(
				'option'   => $this->option,
				'scope'    => $this->group->get('gidNumber') . DS . 'whiteboard',
				'pagename' => $view->row->alias,
				'pageid'   => 0,
				'filepath' => $path,
				'domain'   => $this->group->get('cn')
			);
			
			//$p = new WikiParser(stripslashes($view->row->title), $this->option, 'whiteboard', $view->row->alias, 0, $path);
			$view->row->content = $p->parse("\n" . stripslashes($view->row->content), $wikiconfig, true, true);
		}

		$bc = new whiteboardComment($this->database);
		$view->comments = $bc->getAllComments($view->row->id);

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

		$r = JRequest::getInt('reply', 0);
		$view->replyto = new whiteboardComment($this->database);
		$view->replyto->load($r);

		$bt = new whiteboardTags($this->database);
		$view->tags = $bt->get_tag_cloud(0,0,$view->row->id);

		// Filters for returning results
		$filters = array();
		$filters['limit']      = 10;
		$filters['start']      = 0;
		$filters['created_by'] = 0;
		$filters['group_id']   = $this->group->get('gidNumber');
		$filters['scope']      = 'group';

		if ($juser->get('guest')) 
		{
			$filters['state'] = 'public';
		} 
		else 
		{
			//if ($juser->get('id') != $this->member->get('uidNumber')) 
			//{
				$filters['state'] = 'registered';
			//}
		}
		$view->popular = $view->row->getPopularEntries($filters);
		$view->recent = $view->row->getRecentEntries($filters);

		// Push some scripts to the template
		/*$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT . DS . 'plugins' . DS . 'groups' . DS . 'whiteboard' . DS . 'whiteboard.js')) {
			$document->addScript('plugins' . DS . 'groups' . DS . 'whiteboard' . DS . 'whiteboard.js');
		}*/
		$view->canpost = $this->_getPostingPermissions();

		$view->p = $p;
		$view->wikiconfig = $wikiconfig;

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
		$juser =& JFactory::getUser();
		$app =& JFactory::getApplication();

		$board = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=bulletinboard');
		if ($juser->get('guest')) 
		{
			//$app->enqueueMessage(JText::_('GROUPS_LOGIN_NOTICE'), 'warning');
			$app->redirect('/login?return=' . base64_encode($board));
			return;
		}

		if (!$this->authorized) 
		{
			$app->enqueueMessage(JText::_('You are not authorized to edit this bulletin board entry.'), 'error');
			$app->redirect($board);
			return;
		}

		if (!$this->_getPostingPermissions()) 
		{
			$app->enqueueMessage(JText::_('You do not have permission to post entries.'), 'error');
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
		$view->config     = $this->params;
		$view->authorized = $this->authorized;

		$view->dateFormat  = $this->dateFormat;
		$view->timeFormat  = $this->timeFormat;
		$view->yearFormat  = $this->yearFormat;
		$view->monthFormat = $this->monthFormat;
		$view->dayFormat   = $this->dayFormat;
		$view->tz          = $this->tz;

		$id = JRequest::getInt('entry', 0);

		$view->entry = new BulletinboardBulletin($this->database);
		$view->entry->load($id);

		$view->board = new BulletinboardBoard($this->database);
		/*$view->board->loadDefault($this->group->get('gidNumber'), 'group');
		if (!$view->board->id)
		{
			$view->board->setup($this->group->get('gidNumber'), 'group');
		}*/

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

			$bt = new BulletinboardTags($this->database);
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
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return $this->_login();
		}

		if (!$this->authorized) 
		{
			$this->setError(JText::_('PLG_GROUPS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		if (!$this->_getPostingPermissions()) 
		{
			$this->setError(JText::_('You do not have permission to edit/save entries.'));
			return $this->_browse();
		}

		$fields = JRequest::getVar('fields', array(), 'post');

		$row = new BulletinboardBulletin($this->database);
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

		$descriptions = JRequest::getVar('description', array(), 'post');
		if (!$this->_upload($files, $row->id, $row->type, $descriptions))
		{
			return $this->_edit();
		}

		$stick = new BulletinboardStick($this->database);
		$stick->bulletin_id = $row->id;
		$stick->board_id    = $fields['board_id'];
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
		$bt = new BulletinboardTags($this->database);
		$bt->tag_object($juser->get('id'), $row->id, $tags, 1, 1);

		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=com_groups&gid=' . $this->group->get('cn') . '&active=' . $this->_name));
	}

	/**
	 * Repost an entry
	 * 
	 * @return     string
	 */
	private function _repost()
	{
		$juser = JFactory::getUser();

		// Incoming
		$bulletin_id = JRequest::getInt('bulletin', 0);
		$board_id    = JRequest::getInt('board', 0);
		$no_html     = JRequest::getInt('no_html', 0);

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

			$board = new BulletinboardBoard($this->database);

			$view->myboards = $board->getRecords(array(
				'object_type' => 'member',
				'object_id'   => $juser->get('id'),
				'state'       => 1
			));
			if (!$view->myboards)
			{
				$board->setup($juser->get('id'), 'member');
				$view->myboards = array($board);
			}

			$view->groupboards = array();
			$groups = $board->getRecords(array(
				'object_type' => 'group',
				'object_id'   => $this->group->get('gidNumber'),
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
					$view->groupboards[$s->group_alias][] = $s;
					asort($view->groupboards[$s->group_alias]);
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
		$stick = new BulletinboardStick($this->database);
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
			echo JText::sprintf('%s reposts', $stick->getCount(array('bulletin_id' => $stick->bulletin_id)));
			exit;
		}

		// Display the main listing
		return $this->_browse();
	}

	/**
	 * Delete an entry
	 * 
	 * @return     string
	 */
	/*private function _delete()
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return;
		}

		if (!$this->authorized) 
		{
			$this->setError(JText::_('PLG_GROUPS_whiteboard_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		if (!$this->_getPostingPermissions()) 
		{
			$this->setError(JText::_('You do not have permission to delete entries.'));
			return $this->_browse();
		}

		// Incoming
		$id = JRequest::getInt('entry', 0);
		if (!$id) 
		{
			return $this->_browse();
		}

		$process = JRequest::getVar('process', '');
		$confirmdel = JRequest::getVar('confirmdel', '');

		// Initiate a whiteboard entry object
		$entry = new BulletinboardBulletin($this->database);
		$entry->load($id);

		// Did they confirm delete?
		if (!$process || !$confirmdel) 
		{
			if ($process && !$confirmdel) 
			{
				$this->setError(JText::_('PLG_GROUPS_whiteboard_ERROR_CONFIRM_DELETION'));
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
			$view->option = $this->option;
			$view->group = $this->group;
			$view->task = $this->action;
			$view->config = $this->params;
			$view->entry = $entry;
			$view->authorized = $this->authorized;
			
			$view->dateFormat  = $this->dateFormat;
			$view->timeFormat  = $this->timeFormat;
			$view->yearFormat  = $this->yearFormat;
			$view->monthFormat = $this->monthFormat;
			$view->dayFormat   = $this->dayFormat;
			$view->tz          = $this->tz;
			
			if ($this->getError()) 
			{
				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}
			}
			return $view->loadTemplate();
		}

		// Delete the entry itself
		if (!$entry->delete($id)) 
		{
			$this->setError($entry->getError());
		}

		// Return the topics list
		return $this->_browse();
	}

	/**
	 * Save a comment
	 * 
	 * @return     string
	 */
	/*private function _savecomment()
	{
		// Ensure the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return $this->_login();
		}

		// Incoming
		$comment = JRequest::getVar('comment', array(), 'post');

		// Instantiate a new comment object and pass it the data
		$row = new whiteboardComment($this->database);
		if (!$row->bind($comment)) 
		{
			$this->setError($row->getError());
			return $this->_entry();
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
			return $this->_entry();
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			return $this->_entry();
		}

		return $this->_entry();
	}

	/**
	 * Delete a comment
	 * 
	 * @return     string
	 */
	/*private function _deletecomment()
	{
		// Ensure the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return;
		}

		// Incoming
		$id = JRequest::getInt('comment', 0);
		if (!$id) 
		{
			return $this->_entry();
		}

		// Initiate a whiteboard comment object
		$comment = new whiteboardComment($this->database);

		// Delete all comments on an entry
		if (!$comment->deleteChildren($id)) 
		{
			$this->setError($comment->getError());
			return $this->_entry();
		}

		// Delete the entry itself
		if (!$comment->delete($id)) 
		{
			$this->setError($comment->getError());
		}

		// Return the topics list
		return $this->_entry();
	}

	/**
	 * Upload a file
	 * 
	 * @return     void
	 */
	private function _vote()
	{
		$juser = JFactory::getUser();

		$id = JRequest::getInt('bulletin', 0);

		// Create a vote record
		$vote = new BulletinboardVote($this->database);
		$vote->loadByBulletin($id, $juser->get('id'));

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
		$bulletin = new BulletinboardBulletin($this->database);
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
		return $this->_browse();
	}

	/**
	 * Upload a file
	 * 
	 * @return     void
	 */
	private function _upload($files, $listdir, $type, $descriptions)
	{
		// Ensure the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
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
				$attachment = new BulletinboardAsset($this->database);
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
}
