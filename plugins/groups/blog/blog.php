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

/**
 * Groups Plugin class for blog entries
 */
class plgGroupsBlog extends \Hubzero\Plugin\Plugin
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
			'name' => $this->_name,
			'title' => JText::_('PLG_GROUPS_BLOG'),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon' => 'f075'
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

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'blog.php');

		$this->model = new BlogModel('group', $group->get('gidNumber'));

		//are we returning html
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
				$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if ($juser->get('guest')
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
			{
				$url = JRoute::_('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active, false, true);

				$this->redirect(
					JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($url)),
					JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
					'warning'
				);
				return;
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
			$this->database   = JFactory::getDBO();

			//get the plugins params
			$p = new \Hubzero\Plugin\Params($this->database);
			$this->params = $p->getParams($group->gidNumber, 'groups', $this->_name);

			if ($authorized == 'manager' || $authorized == 'admin')
			{
				$this->params->set('access-edit-comment', true);
				$this->params->set('access-delete-comment', true);
			}

			// Append to document the title
			$document = JFactory::getDocument();
			$document->setTitle($document->getTitle() . ': ' . JText::_('PLG_GROUPS_BLOG'));

			switch ($this->action)
			{
				// Feeds
				case 'feed.rss': $this->_feed();   break;
				case 'feed':     $this->_feed();   break;
				//case 'comments.rss': $this->_commentsFeed();   break;
				//case 'comments':     $this->_commentsFeed();   break;

				// Settings
				case 'savesettings': $arr['html'] = $this->_savesettings(); break;
				case 'settings':     $arr['html'] = $this->_settings();     break;

				// Comments
				case 'savecomment':   $arr['html'] = $this->_savecomment();   break;
				case 'newcomment':    $arr['html'] = $this->_newcomment();    break;
				case 'editcomment':   $arr['html'] = $this->_entry();         break;
				case 'deletecomment': $arr['html'] = $this->_deletecomment(); break;

				// Entries
				case 'save':   $arr['html'] = $this->_save();   break;
				case 'new':    $arr['html'] = $this->_new();    break;
				case 'edit':   $arr['html'] = $this->_edit();   break;
				case 'delete': $arr['html'] = $this->_delete(); break;
				case 'entry':  $arr['html'] = $this->_entry();  break;

				case 'archive':
				case 'browse':
				default: $arr['html'] = $this->_browse(); break;
			}
		}

		$filters = array(
			'scope'    => 'group',
			'group_id' => $group->get('gidNumber'),
			'state'    => 'public'
		);

		$juser = JFactory::getUser();
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
		}

		// Build the HTML meant for the "profile" tab's metadata overview
		$arr['metadata']['count'] = $this->model->entries('count', $filters);

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

			$blog = '/groups/' . $this->group->get('cn') . '/blog';

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
	 * Display a list of latest blog entries
	 *
	 * @return     string
	 */
	private function _browse()
	{
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'browse'
			)
		);
		$view->juser      = $this->juser;
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->config     = $this->params;
		$view->authorized = $this->authorized;
		$view->model      = $this->model;

		$jconfig = JFactory::getConfig();

		// Filters for returning results
		$view->filters = array(
			'limit'      => JRequest::getInt('limit', $jconfig->getValue('config.list_limit')),
			'start'      => JRequest::getInt('limitstart', 0),
			'created_by' => JRequest::getInt('author', 0),
			'year'       => JRequest::getInt('year', 0),
			'month'      => JRequest::getInt('month', 0),
			'scope'      => 'group',
			'group_id'   => $this->group->get('gidNumber'),
			'search'     => JRequest::getVar('search',''),
			'authorized' => false,
			'state'      => 'public'
		);

		// See what information we can get from the path
		$juri = JURI::getInstance();
		$path = $juri->getPath();
		if (strstr($path, '/'))
		{
			$bits = $this->_parseUrl();

			// if we have 3 pieces, then there is year/month/entry
			// display entry
			if (count($bits) > 2)
			{
				return $this->_entry();
			}

			$view->filters['year']  = (isset($bits[0])) ? $bits[0] : $view->filters['year'];
			$view->filters['month'] = (isset($bits[1])) ? $bits[1] : $view->filters['month'];
		}

		$view->canpost = $this->_getPostingPermissions();

		$juser = JFactory::getUser();
		if ($juser->get('guest'))
		{
			$view->filters['state'] = 'public';
		}
		else
		{
			if ($this->authorized != 'member'
			 && $this->authorized != 'manager'
			 && $this->authorized != 'admin')
			{
				$view->filters['state'] = 'registered';
			}
			else
			{
				if ($this->authorized == 'member'
				 || $this->authorized == 'manager'
				 || $this->authorized == 'admin')
				{
					$view->filters['authorized'] = true;
					$view->filters['state'] = 'all';
				}
				else
				{
					$view->filters['authorized'] = $juser->get('id');
				}
			}
		}

		$view->year   = $view->filters['year'];
		$view->month  = $view->filters['month'];
		$view->search = $view->filters['search'];

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
	 * Display an RSS feed of latest entries
	 *
	 * @return     string
	 */
	private function _feed()
	{
		if (!$this->params->get('feeds_enabled', 1))
		{
			$this->_browse();
			return;
		}

		include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');

		// Set the mime encoding for the document
		$jdoc = JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		$doc = new JDocumentFeed;
		$doc->link = JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name);

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Filters for returning results
		$filters = array(
			'limit'      => JRequest::getInt('limit', $jconfig->getValue('config.list_limit')),
			'start'      => JRequest::getInt('limitstart', 0),
			'year'       => JRequest::getInt('year', 0),
			'month'      => JRequest::getInt('month', 0),
			'scope'      => 'group',
			'group_id'   => $this->group->get('gidNumber'),
			'search'     => JRequest::getVar('search',''),
			'created_by' => JRequest::getInt('author', 0),
			'state'      => 'public'
		);

		$path = JURI::getInstance()->getPath();
		if (strstr($path, '/'))
		{
			$bits = $this->_parseUrl();

			$filters['year']  = (isset($bits[0])) ? $bits[0] : $filters['year'];
			$filters['month'] = (isset($bits[1])) ? $bits[1] : $filters['month'];
		}

		// Build some basic RSS document information
		$jconfig = JFactory::getConfig();
		$doc->title       = $jconfig->getValue('config.sitename') . ': ' . JText::_('Groups') . ': ' . stripslashes($this->group->get('description')) . ': ' . JText::_('Blog');
		$doc->description = JText::sprintf('PLG_GROUPS_BLOG_RSS_DESCRIPTION', $this->group->get('cn'), $jconfig->getValue('config.sitename'));
		$doc->copyright   = JText::sprintf('PLG_GROUPS_BLOG_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category    = JText::_('PLG_GROUPS_BLOG_RSS_CATEGORY');

		$rows = $this->model->entries('list', $filters);

		// Start outputing results if any found
		if ($rows->total() > 0)
		{
			foreach ($rows as $row)
			{
				$item = new JFeedItem();

				// Strip html from feed item description text
				$item->description = $row->content('parsed');
				$item->description = html_entity_decode(\Hubzero\Utility\Sanitize::stripAll($item->description));
				if ($this->params->get('feed_entries') == 'partial')
				{
					$item->description = \Hubzero\Utility\String::truncate($item->description, 300);
				}

				// Load individual item creator class
				$item->title       = html_entity_decode(strip_tags($row->get('title')));
				$item->link        = JRoute::_($row->link());
				$item->date        = date('r', strtotime($row->published()));
				$item->category    = '';
				$item->author      = $row->creator('name');

				// Loads item info into rss array
				$doc->addItem($item);
			}
		}

		// Output the feed
		echo $doc->render();
	}

	/**
	 * Determine permissions to post an entry
	 *
	 * @return     boolean True if user cna post, false if not
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
	 * Display a blog entry
	 *
	 * @return     string
	 */
	private function _entry()
	{
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'entry'
			)
		);
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->config     = $this->params;
		$view->authorized = $this->authorized;
		$view->juser      = $this->juser;
		$view->model      = $this->model;

		if (isset($this->entry) && is_object($this->entry))
		{
			$view->row = $this->entry;
		}
		else
		{
			$path = JURI::getInstance()->getPath();
			if (strstr($path, '/'))
			{
				$bits = $this->_parseUrl();

				$alias = end($bits);
			}

			$view->row = $this->model->entry($alias);
		}

		if (!$view->row->exists())
		{
			return $this->_browse();
		}

		// Check authorization
		$juser = JFactory::getUser();
		if (($view->row->get('state') == 2 && $juser->get('guest'))
		 || ($view->row->get('state') == 0 && $juser->get('id') != $view->row->get('created_by') && $this->authorized != 'member' && $this->authorized != 'manager' && $this->authorized != 'admin'))
		{
			JError::raiseError(403, JText::_('PLG_GROUPS_BLOG_NOT_AUTH'));
			return;
		}

		// make sure the group owns this
		if ($view->row->get('group_id') != $this->group->get('gidNumber'))
		{
			JError::raiseError(403, JText::_('PLG_GROUPS_BLOG_NOT_AUTH'));
			return;
		}

		// Filters for returning results
		$view->filters = array(
			'limit'      => 10,
			'start'      => 0,
			'scope'      => 'group',
			'group_id'   => $this->group->get('gidNumber'),
			'created_by' => 0
		);

		if ($juser->get('guest'))
		{
			$view->filters['state'] = 'public';
		}
		else
		{
			$view->filters['state'] = 'registered';
		}

		$view->canpost = $this->_getPostingPermissions();

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
	private function _edit($row=null)
	{
		$juser = JFactory::getUser();
		$blog = JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name);

		if ($juser->get('guest'))
		{
			$this->redirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($blog))
			);
			return;
		}

		if (!$this->authorized || !$this->_getPostingPermissions())
		{
			$this->redirect(
				$blog,
				JText::_('PLG_GROUPS_BLOG_ERROR_PERMISSION_DENIED'),
				'error'
			);
			return;
		}

		// Instantiate view
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'edit'
			)
		);
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->task       = $this->action;
		$view->config     = $this->params;
		$view->authorized = $this->authorized;
		$view->model      = $this->model;

		if (is_object($row))
		{
			$view->entry = $row;
		}
		else
		{
			$id = JRequest::getInt('entry', 0);
			$view->entry = new BlogModelEntry($id);
		}

		// Does it exist?
		if (!$view->entry->exists())
		{
			// Set some defaults
			$view->entry->set('allow_comments', 1);
			$view->entry->set('state', 1);
			$view->entry->set('scope', 'group');
			$view->entry->set('group_id', $this->group->get('gidNumber'));
		}

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
	 * Save an entry
	 *
	 * @return     void
	 */
	private function _save()
	{
		$juser = JFactory::getUser();
		if ($juser->get('guest'))
		{
			$blog = JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name, false, true);

			$this->redirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($blog)),
				JText::_('GROUPS_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		if (!$this->authorized)
		{
			$this->setError(JText::_('PLG_GROUPS_BLOG_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		if (!$this->_getPostingPermissions())
		{
			$this->setError(JText::_('PLG_GROUPS_BLOG_ERROR_PERMISSION_DENIED'));
			return $this->_browse();
		}

		$entry = JRequest::getVar('entry', array(), 'post', 'none', 2);

		if (isset($entry['publish_up']) && $entry['publish_up'] != '')
		{
			$entry['publish_up']   = JFactory::getDate($entry['publish_up'], JFactory::getConfig()->get('offset'))->toSql();
		}

		if (isset($entry['publish_down']) && $entry['publish_down'] != '')
		{
			$entry['publish_down'] = JFactory::getDate($entry['publish_down'], JFactory::getConfig()->get('offset'))->toSql();
		}

		// make sure we dont want to turn off comments
		$entry['allow_comments'] = (isset($entry['allow_comments'])) ? : 0;

		// Instantiate model
		$row = $this->model->entry($entry['id']);

		// Bind data
		if (!$row->bind($entry))
		{
			$this->setError($row->getError());
			return $this->_edit($row);
		}

		if (!$row->get('id'))
		{
			$item = $this->model->entry($row->get('alias'));
			if ($item->get('id'))
			{
				$this->setError(JText::_('PLG_GROUPS_BLOG_ERROR_ALIAS_EXISTS'));
				return $this->_edit($row);
			}
		}

		// Store new content
		if (!$row->store(true))
		{
			$this->setError($row->getError());
			return $this->_edit($row);
		}

		// Process tags
		if (!$row->tag(JRequest::getVar('tags', '')))
		{
			$this->setError($row->getError());
			return $this->_edit($row);
		}

		$this->redirect(
			JRoute::_($row->link())
		);
	}

	/**
	 * Delete an entry
	 *
	 * @return     string
	 */
	private function _delete()
	{
		$juser = JFactory::getUser();
		if ($juser->get('guest'))
		{
			$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return;
		}

		if (!$this->authorized)
		{
			$this->setError(JText::_('PLG_GROUPS_BLOG_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		if (!$this->_getPostingPermissions())
		{
			$this->setError(JText::_('PLG_GROUPS_BLOG_ERROR_PERMISSION_DENIED'));
			return $this->_browse();
		}

		// Incoming
		$id = JRequest::getInt('entry', 0);
		if (!$id)
		{
			return $this->_browse();
		}

		$process    = JRequest::getVar('process', '');
		$confirmdel = JRequest::getVar('confirmdel', '');

		// Initiate a blog entry object
		$entry = new BlogModelEntry($id);

		// Did they confirm delete?
		if (!$process || !$confirmdel)
		{
			if ($process && !$confirmdel)
			{
				$this->setError(JText::_('PLG_GROUPS_BLOG_ERROR_CONFIRM_DELETION'));
			}

			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'delete'
				)
			);
			$view->option     = $this->option;
			$view->group      = $this->group;
			$view->task       = $this->action;
			$view->config     = $this->params;
			$view->entry      = $entry;
			$view->authorized = $this->authorized;
			$view->model      = $this->model;

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
		$entry->set('state', -1);
		if (!$entry->store())
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
	private function _savecomment()
	{
		// Ensure the user is logged in
		$juser = JFactory::getUser();
		if ($juser->get('guest'))
		{
			$blog = JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name, false, true);

			$this->redirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($blog)),
				JText::_('GROUPS_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$comment = JRequest::getVar('comment', array(), 'post', 'none', 2);

		// Instantiate a new comment object and pass it the data
		$row = new BlogModelComment($comment['id']);
		if (!$row->bind($comment))
		{
			$this->setError($row->getError());
			return $this->_entry();
		}

		// Store new content
		if (!$row->store(true))
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
	private function _deletecomment()
	{
		// Ensure the user is logged in
		$juser = JFactory::getUser();
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

		// Initiate a blog comment object
		$comment = new BlogModelComment($id);

		// Delete all comments on an entry
		$comment->set('state', 2);

		// Delete the entry itself
		if (!$comment->store(false))
		{
			$this->setError($comment->getError());
		}

		// Return the topics list
		return $this->_entry();
	}

	/**
	 * Display blog settings
	 *
	 * @return     string
	 */
	private function _settings()
	{
		$juser = JFactory::getUser();
		if ($juser->get('guest'))
		{
			$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return;
		}

		if ($this->authorized != 'manager' && $this->authorized != 'admin')
		{
			$this->setError(JText::_('PLG_GROUPS_BLOG_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'settings'
			)
		);
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->task       = $this->action;
		$view->config     = $this->params;
		$view->model      = $this->model;

		$view->settings   = new \Hubzero\Plugin\Params($this->database);
		$view->settings->loadPlugin($this->group->gidNumber, $this->_type, $this->_name);

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
	private function _savesettings()
	{
		$juser = JFactory::getUser();
		if ($juser->get('guest'))
		{
			$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return;
		}

		if ($this->authorized != 'manager' && $this->authorized != 'admin')
		{
			$this->setError(JText::_('PLG_GROUPS_BLOG_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		$settings = JRequest::getVar('settings', array(), 'post');

		$row = new \Hubzero\Plugin\Params($this->database);
		if (!$row->bind($settings))
		{
			$this->setError($row->getError());
			return $this->_entry();
		}

		// Get parameters
		$p = new JRegistry('');
		$p->loadArray(JRequest::getVar('params', array(), 'post'));

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
			JText::_('PLG_GROUPS_BLOG_SETTINGS_SAVED'),
			'passed'
		);
	}
}
