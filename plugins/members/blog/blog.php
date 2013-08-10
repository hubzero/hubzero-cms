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

jimport('joomla.plugin.plugin');

/**
 * Members Plugin class for blog entries
 */
class plgMembersBlog extends JPlugin
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
	public function &onMembersAreas($user, $member)
	{
		$areas['blog'] = JText::_('PLG_MEMBERS_BLOG');
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

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'blog.php');

		// Get our model
		$this->model = new BlogModel('member', $member->get('uidNumber'));

		if ($returnhtml) 
		{
			$this->user    = $user;
			$this->member  = $member;
			$this->option  = $option;
			//$this->authorized = $authorized;
			$this->database = JFactory::getDBO();

			$p = new Hubzero_Plugin_Params($this->database);
			$this->params = $p->getParams($this->member->get('uidNumber'), 'members', $this->_name);

			// Push styles to the template
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('members', $this->_name);
			Hubzero_Document::addPluginScript('members', $this->_name);

			// Append to document the title
			$document =& JFactory::getDocument();
			$document->setTitle($document->getTitle() . ': ' . JText::_('PLG_MEMBERS_BLOG'));

			// Get and determine task
			$this->task = JRequest::getVar('action', '');

			if (is_numeric($this->task)) 
			{
				$this->task = 'entry';
			}

			switch ($this->task) 
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
				case 'editcomment':   $arr['html'] = $this->_editcomment();   break;
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

		// Build filters
		$filters = array(
			'scope'      => 'member',
			'group_id'   => 0,
			'created_by' => $member->get('uidNumber')
		);

		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$filters['state'] = 'public';
		} 
		// Logged-in non-owner
		else if ($juser->get('id') != $member->get('uidNumber')) 
		{
			$filters['state'] = 'registered';
		}
		// Owner of the blog
		else 
		{
			$filters['state'] = 'all';
			$filters['authorized'] = $member->get('uidNumber');
		}

		// Get an entry count
		$arr['metadata']['count'] = $this->model->entries('count', $filters);

		return $arr;
	}

	/**
	 * Display a list of latest blog entries
	 * 
	 * @return     string
	 */
	private function _browse() 
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => $this->_name,
				'name'    => 'browse'
			)
		);
		$view->option = $this->option;
		$view->member = $this->member;
		$view->config = $this->params;
		$view->model  = $this->model;

		$jconfig = JFactory::getConfig();

		// Filters for returning results
		$view->filters = array(
			'limit'      => JRequest::getInt('limit', $jconfig->getValue('config.list_limit')),
			'start'      => JRequest::getInt('limitstart', 0),
			'created_by' => $this->member->get('uidNumber'),
			'year'       => JRequest::getInt('year', 0),
			'month'      => JRequest::getInt('month', 0),
			'scope'      => 'member',
			'group_id'   => 0,
			'search'     => JRequest::getVar('search',''),
			'authorized' => false
		);

		// See what information we can get from the path
		$juri =& JURI::getInstance();
		$path = $juri->getPath();
		if (strstr($path, '/')) 
		{
			$path = str_replace('/members/' . $this->member->get('uidNumber') . '/' . $this->_name, '', $path);
			$path = ltrim($path, DS);
			$bits = explode('/', $path);
			$view->filters['year']  = (isset($bits[0])) ? $bits[0] : $view->filters['year'];
			$view->filters['month'] = (isset($bits[1])) ? $bits[1] : $view->filters['month'];
		}

		// Check logged-in status
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$view->filters['state'] = 'public';
		} 
		// Logged-in non-owner
		else if ($juser->get('id') != $this->member->get('uidNumber')) 
		{
			$view->filters['state'] = 'registered';
		}
		// Owner of the blog
		else 
		{
			$view->filters['state'] = 'all';
		}
		if ($juser->get('id') == $this->member->get('uidNumber'))
		{
			$view->filters['authorized'] = $this->member->get('uidNumber');
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
		$jdoc =& JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		$doc = new JDocumentFeed;
		$doc->link = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name);

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Filters for returning results
		$filters = array(
			'limit'      => JRequest::getInt('limit', $jconfig->getValue('config.list_limit')),
			'start'      => JRequest::getInt('limitstart', 0),
			'year'       => JRequest::getInt('year', 0),
			'month'      => JRequest::getInt('month', 0),
			'scope'      => 'member',
			'group_id'   => 0,
			'search'     => JRequest::getVar('search',''),
			'created_by' => $this->member->get('uidNumber')
		);

		$path = JURI::getInstance()->getPath();
		if (strstr($path, '/')) 
		{
			$path = str_replace('/members/' . $this->member->get('uidNumber') . '/blog', '', $path);
			$path = ltrim($path, DS);
			$bits = explode('/', $path);
			$filters['year']  = (isset($bits[0])) ? $bits[0] : $filters['year'];
			$filters['month'] = (isset($bits[1])) ? $bits[1] : $filters['month'];
		}

		// Build some basic RSS document information
		$jconfig =& JFactory::getConfig();
		$doc->title       = $jconfig->getValue('config.sitename') . ' - ' . stripslashes($this->member->get('name')) . ': ' . JText::_('Blog');
		$doc->description = JText::sprintf('PLG_MEMBERS_BLOG_RSS_DESCRIPTION',$jconfig->getValue('config.sitename'),stripslashes($this->member->get('name')));
		$doc->copyright   = JText::sprintf('PLG_MEMBERS_BLOG_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category    = JText::_('PLG_MEMBERS_BLOG_RSS_CATEGORY');

		$filters['state'] = 'public';

		$rows = $this->model->entries('list', $filters);

		// Start outputing results if any found
		if ($rows->total() > 0) 
		{
			foreach ($rows as $row)
			{
				$item = new JFeedItem();

				// Strip html from feed item description text
				$item->description = $row->content('parsed');
				$item->description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($item->description));
				if ($this->params->get('feed_entries') == 'partial') 
				{
					$item->description = Hubzero_View_Helper_Html::shortenText($item->description, 300, 0);
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
	 * Display a blog entry
	 * 
	 * @return     string
	 */
	private function _entry() 
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => $this->_name,
				'name'    => 'entry'
			)
		);
		$view->option = $this->option;
		$view->member = $this->member;
		$view->config = $this->params;
		$view->model  = $this->model;

		if (isset($this->entry) && is_object($this->entry)) 
		{
			$view->row = $this->entry;
		} 
		else 
		{
			$path = JURI::getInstance()->getPath();
			$alias = '';
			if (strstr($path, '/')) 
			{
				$path = str_replace('/members/' . $this->member->get('uidNumber') . '/blog/', '', $path);
				$bits = explode('/', $path);
				$alias = end($bits);
			}

			$view->row = $this->model->entry($alias);
		}

		if (!$view->row->exists()) 
		{
			return $this->_browse();
		}

		// Check authorization
		$juser =& JFactory::getUser();
		if (($view->row->get('state') == 2 && $juser->get('guest')) 
		 || ($view->row->get('state') == 0 && $juser->get('id') != $this->member->get('uidNumber'))) 
		{
			JError::raiseError(403, JText::_('PLG_MEMBERS_BLOG_NOT_AUTH'));
			return;
		}

		// Filters for returning results
		$view->filters = array(
			'limit'    => 10,
			'start'    => 0,
			'scope'    => 'member',
			'group_id' => 0,
			'created_by' => $this->member->get('uidNumber')
		);

		if ($juser->get('guest')) 
		{
			$view->filters['state'] = 'public';
		} 
		else 
		{
			if ($juser->get('id') != $this->member->get('uidNumber')) 
			{
				$view->filters['state'] = 'registered';
			}
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
		// Login check
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$this->setError(JText::_('MEMBERS_LOGIN_NOTICE'));
			return $this->_login();
		}

		// Instantiate view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => $this->_name,
				'name'    => 'edit'
			)
		);
		$view->option = $this->option;
		$view->member = $this->member;
		$view->task   = $this->task;
		$view->config = $this->params;

		// Load the entry
		if (is_object($row))
		{
			$view->entry = $row;
		}
		else 
		{
			$view->entry = $this->model->entry(JRequest::getInt('entry', 0));
		}

		// Does it exist?
		if (!$view->entry->exists()) 
		{
			// Set some defaults
			$view->entry->set('allow_comments', 1);
			$view->entry->set('state', 1);
			$view->entry->set('scope', 'member');
			$view->entry->set('created_by', $this->member->get('uidNumber'));
		}

		// Pass any errors on to the view
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		Hubzero_Document::addSystemScript('jquery.timepicker');
		Hubzero_Document::addSystemStylesheet('jquery.datepicker.css');
		Hubzero_Document::addSystemStylesheet('jquery.timepicker.css');

		// Render view
		return $view->loadTemplate();
	}

	/**
	 * Save an entry
	 * 
	 * @return     void
	 */
	private function _save() 
	{
		// Login check
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$this->setError(JText::_('MEMBERS_LOGIN_NOTICE'));
			return $this->_login();
		}

		$entry = JRequest::getVar('entry', array(), 'post', 'none', 2);

		// Instantiate model
		$row = $this->model->entry($entry['id']);

		// Bind data
		if (!$row->bind($entry)) 
		{
			$this->setError($row->getError());
			return $this->_edit($row);
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

		//return $this->_entry();
		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_($row->link()));
	}

	/**
	 * Delete an entry
	 * 
	 * @return     string
	 */
	private function _delete() 
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$this->setError(JText::_('MEMBERS_LOGIN_NOTICE'));
			return;
		}

		if ($this->user->get('id') != $this->member->get('uidNumber'))
		{
			$this->setError(JText::_('PLG_MEMBERS_BLOG_NOT_AUTHORIZED'));
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
		$entry = $this->model->entry($id);

		// Did they confirm delete?
		if (!$process || !$confirmdel) 
		{
			if ($process && !$confirmdel) 
			{
				$this->setError(JText::_('PLG_MEMBERS_BLOG_ERROR_CONFIRM_DELETION'));
			}

			// Output HTML
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => $this->_name,
					'name'    => 'delete'
				)
			);
			$view->option = $this->option;
			$view->member = $this->member;
			$view->task   = $this->task;
			$view->config = $this->params;
			$view->entry  = $entry;
			$view->authorized = true;

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
		//return $this->_browse();
		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=com_members&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name));
	}

	/**
	 * Save a comment
	 * 
	 * @return     string
	 */
	private function _savecomment() 
	{
		$yearFormat = '%Y';
		$monthFormat = '%m';
		$tz = 0;

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$yearFormat = 'Y';
			$monthFormat = 'm';
			$tz = null;
		}

		// Ensure the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$this->setError(JText::_('MEMBERS_LOGIN_NOTICE'));
			return $this->_login();
		}

		// Incoming
		$comment = JRequest::getVar('comment', array(), 'post');

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

		/*
		if ($row->created_by != $this->member->get('uidNumber)) {
			$this->entry = new BlogEntry($this->database);
			$this->entry->load($row->entry_id);
			
			// Get the site configuration
			$jconfig =& JFactory::getConfig();

			// Build the "from" data for the e-mail
			$from = array();
			$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_('PLG_MEMBERS_BLOG');
			$from['email'] = $jconfig->getValue('config.mailfrom');

			$subject = JText::_('PLG_MEMBERS_BLOG_SUBJECT_COMMENT_POSTED');

			// Build the SEF referenced in the message
			$juri =& JURI::getInstance();
			$sef = JRoute::_('index.php?option='.$this->option.'&id='. $this->member->get('uidNumber').'&active=blog&task='.JHTML::_('date',$this->entry->publish_up, $yearFormat, $tz).'/'.JHTML::_('date',$this->entry->publish_up, $monthFormat, $tz).'/'.$this->entry->alias.'#comments);
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef,1,strlen($sef));
			}

			// Message
			$message  = "The following comment has been posted to your blog entry:\r\n\r\n";
			$message .= stripslashes($row->content)."\r\n\r\n";
			$message .= "To view all comments on the blog entry, go to:\r\n";
			$message .= $juri->base().$sef . "\r\n";

			// Send the message
			JPluginHelper::importPlugin('xmessage');
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger('onSendMessage', array('blog_comment', $subject, $message, $from, array($this->member->get('uidNumber')), $this->option))) {
				$this->setError(JText::_('PLG_MEMBERS_BLOG_ERROR_MSG_MEMBER_FAILED'));
			}
		}
		*/

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
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$this->setError(JText::_('MEMBERS_LOGIN_NOTICE'));
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
		if (!$comment->store()) 
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
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return;
		}

		if ($this->user->get('id') != $this->member->get('uidNumber'))
		{
			$this->setError(JText::_('PLG_MEMBERS_BLOG_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		// Output HTML
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => $this->_name,
				'name'    => 'settings'
			)
		);
		$view->option   = $this->option;
		$view->member   = $this->member;
		$view->task     = $this->task;
		$view->config   = $this->params;

		$view->settings = new Hubzero_Plugin_Params($this->database);
		$view->settings->loadPlugin($this->member->get('uidNumber'), 'members', $this->_name);

		$view->message  = (isset($this->message)) ? $this->message : '';
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
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$this->setError(JText::_('MEMBERS_LOGIN_NOTICE'));
			return;
		}

		if ($this->user->get("id") != $this->member->get("uidNumber"))
		{
			$this->setError(JText::_('PLG_MEMBERS_BLOG_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		$settings = JRequest::getVar('settings', array(), 'post');

		$row = new Hubzero_Plugin_Params($this->database);
		if (!$row->bind($settings)) 
		{
			$this->setError($row->getError());
			return $this->_entry();
		}

		// Get parameters
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		$p = new $paramsClass('');
		$p->bind(JRequest::getVar('params', '', 'post'));

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

		$this->message = JText::_('Settings successfully saved!');

		return $this->_settings();
	}
}
