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
 * Groups Plugin class for blog entries
 */
class plgGroupsBlog extends JPlugin
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
			'name' => 'blog',
			'title' => JText::_('PLG_GROUPS_BLOG'),
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
		$active = 'blog';

		// The output array we're returning
		$arr = array(
			'html' => ''
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit) 
		{
			if (!in_array($this_area['name'], $areas)) 
			{
				return;
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
			$this->params = $p->getParams($group->gidNumber, 'groups', 'blog');

			//push the css to the doc
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('groups', 'blog');

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
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'tables' . DS . 'blog.entry.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'tables' . DS . 'blog.comment.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'helpers' . DS . 'blog.member.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'helpers' . DS . 'blog.tags.php');

			if (is_numeric($this->action)) 
			{
				$this->action = 'entry';
			}

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
				'folder'  => 'groups',
				'element' => 'blog',
				'name'    => 'browse'
			)
		);
		$view->juser      = $this->juser;
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->config     = $this->params;
		$view->authorized = $this->authorized;

		// Filters for returning results
		$filters = array();
		$filters['limit']      = JRequest::getInt('limit', 25);
		$filters['start']      = JRequest::getInt('limitstart', 0);
		$filters['created_by'] = JRequest::getInt('author', 0);
		$filters['year']       = JRequest::getInt('year', 0);
		$filters['month']      = JRequest::getInt('month', 0);
		$filters['scope']      = 'group';
		$filters['group_id']   = $this->group->get('gidNumber');
		$filters['search']     = JRequest::getVar('search','');

		$juri =& JURI::getInstance();
		$path = $juri->getPath();
		if (strstr($path, '/')) 
		{
			$path = str_replace('/groups/' . $this->group->get('cn') . '/blog', '', $path);
			$path = ltrim($path, DS);
			$bits = explode('/', $path);
			$filters['year']  = (isset($bits[0])) ? $bits[0] : $filters['year'];
			$filters['month'] = (isset($bits[1])) ? $bits[1] : $filters['month'];
		}

		$view->canpost = $this->_getPostingPermissions();

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

		$be = new BlogEntry($this->database);

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

		$pagenavhtml = $pageNav->getListFooter();
		$pagenavhtml = str_replace('groups/?', 'groups/' . $this->group->get('cn') . '/blog/?', $pagenavhtml);
		$pagenavhtml = str_replace('action=browse', '', $pagenavhtml);
		$pagenavhtml = str_replace('&amp;&amp;', '&amp;', $pagenavhtml);
		$pagenavhtml = str_replace('?&amp;', '?', $pagenavhtml);

		$path = $this->params->get('uploadpath');
		$view->path = str_replace('{{gid}}', $this->group->get('gidNumber'),$path);

		$view->firstentry = $be->getDateOfFirstEntry($filters);

		$view->popular = $be->getPopularEntries($filters);
		$view->recent = $be->getRecentEntries($filters);

		$view->year = $filters['year'];
		$view->month = $filters['month'];
		$view->search = $filters['search'];
		$view->pagenavhtml = $pagenavhtml;
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
		if (!$this->params->get('feeds_enabled')) 
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
		//$params =& $mainframe->getParams();
		$app =& JFactory::getApplication();
		$params =& $app->getParams();
		$doc->link = JRoute::_('index.php?option=' . $this->option . '&gid='.$this->group->cn . '&active=blog');

		// Filters for returning results
		$filters = array();
		$filters['limit'] = JRequest::getInt('limit', 25);
		$filters['start'] = JRequest::getInt('limitstart', 0);
		$filters['created_by'] = JRequest::getInt('author', 0);
		$filters['year'] = JRequest::getInt('year', 0);
		$filters['month'] = JRequest::getInt('month', 0);
		$filters['scope'] = 'group';
		$filters['group_id'] = $this->group->get('gidNumber');
		$filters['search'] = JRequest::getVar('search','');

		$juri =& JURI::getInstance();
		$path = $juri->getPath();
		if (strstr($path, '/')) 
		{
			$path = str_replace('/groups/' . $this->group->get('cn') . '/blog', '', $path);
			$path = trim($path, DS);
			$bits = explode('/', $path);
			$filters['year']  = (isset($bits[0])) ? $bits[0] : $filters['year'];
			$filters['month'] = (isset($bits[1])) ? $bits[1] : $filters['month'];
		}

		// Build some basic RSS document information
		$jconfig =& JFactory::getConfig();
		$doc->title  = $jconfig->getValue('config.sitename').': '.JText::_('Groups').': '.stripslashes($this->group->description).': '.JText::_('Blog');
		//$doc->title .= ($filters['year']) ? ': '.$filters['year'] : '';
		//$doc->title .= ($filters['month']) ? ': '.sprintf("%02d",$filters['month']) : '';

		$doc->description = JText::sprintf('PLG_GROUPS_BLOG_RSS_DESCRIPTION',$jconfig->getValue('config.sitename'));
		$doc->copyright = JText::sprintf('PLG_GROUPS_BLOG_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category = JText::_('PLG_GROUPS_BLOG_RSS_CATEGORY');

		//$view->canpost = $this->_getPostingPermissions();

		//$juser =& JFactory::getUser();
		//if ($juser->get('guest')) {
			$filters['state'] = 'public';
		//} else {
			//if ($this->authorized != 'member' && $this->authorized != 'manager' && $this->authorized != 'admin') {
				//$filters['state'] = 'registered';
			//}
		//}

		$be = new BlogEntry($this->database);

		$rows = $be->getRecords($filters);

		// Start outputing results if any found
		if (count($rows) > 0) 
		{
			ximport('wiki.parser');
			//$p = new WikiParser(JText::_(strtoupper($this->_option)), $this->_option, 'blog', '', 0, $this->config->get('uploadpath'));
			$path = $this->params->get('uploadpath');
			$path = str_replace('{{gid}}', $this->group->get('gidNumber'), $path);

			foreach ($rows as $row)
			{
				$p = new WikiParser(stripslashes($row->title), $this->option, 'blog', $row->alias, 0, $path);

				// Prepare the title
				$title = strip_tags($row->title);
				$title = html_entity_decode($title);

				// URL link to article
				$link = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->cn . '&active=blog&scope=' . JHTML::_('date', $row->publish_up, $this->yearFormat, 0) . '/' . JHTML::_('date', $row->publish_up, $this->monthFormat, 0) . '/' . $row->alias);

				$cuser =& JUser::getInstance($row->created_by);
				$author = $cuser->get('name');

				// Strip html from feed item description text
				$description = $p->parse("\n" . stripslashes($row->content), 0, 0);
				$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($description));
				if ($this->params->get('feed_entries') == 'partial') 
				{
					$description = Hubzero_View_Helper_Html::shortenText($description, 300, 0);
				}

				@$date = ($row->publish_up ? date('r', strtotime($row->publish_up)) : '');

				// Load individual item creator class
				$item = new JFeedItem();
				$item->title       = $title;
				$item->link        = $link;
				$item->description = $description;
				$item->date        = $date;
				$item->category    = '';
				$item->author      = $author;

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
	 * Highlight some text in a string
	 * 
	 * @param      string $searchquery Text to highlight
	 * @param      array  $results     Array of results to highlight text in
	 * @return     array
	 */
	private function _highlight($searchquery, $results)
	{
		$toks = array($searchquery);

		$resultback = 60;
		$resultlen  = 300;

		// Loop through all results
		for ($i = 0, $n = count($results); $i < $n; $i++)
		{
			$row =& $results[$i];

			// Clean the text up a bit first
			$lowerrow = strtolower($row->content);

			// Find first occurrence of a search word
			$pos = 0;
			foreach ($toks as $tok)
			{
				$pos = strpos($lowerrow, $tok);
				if ($pos !== false) 
				{
					break;
				}
			}

			if ($pos > $resultback) 
			{
				$row->content = substr($row->content, ($pos - $resultback), $resultlen);
			} 
			else 
			{
				$row->content = substr($row->content, 0, $resultlen);
			}

			// Highlight each word/phrase found
			foreach ($toks as $tok)
			{
				if ($tok == 'class' 
				 || $tok == 'span' 
				 || $tok == 'highlight') 
				{
					continue;
				}
				$row->content = preg_replace('#' . $tok . '#i' , "<span class=\"highlight\">\\0</span>", $row->content);
				$row->title   = preg_replace('#' . $tok . '#i', "<span class=\"highlight\">\\0</span>", $row->title);
			}

			$row->content = trim($row->content) . ' &#8230;';
		}

		return $results;
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
				'folder'  => 'groups',
				'element' => 'blog',
				'name'    => 'entry'
			)
		);
		$view->option = $this->option;
		$view->group = $this->group;
		$view->config = $this->params;
		$view->authorized = $this->authorized;
		$view->juser = $this->juser;

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
				$path = str_replace('/groups/' . $this->group->get('cn') . '/blog/', '', $path);
				$bits = explode('/', $path);
				$alias = end($bits);
			}

			$view->row = new BlogEntry($this->database);
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
			JError::raiseError(403, JText::_('PLG_GROUPS_BLOG_NOT_AUTH'));
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
				
				$plugin = JPluginHelper::getPlugin('members', 'blog');
				$params = new $paramsClass($plugin->params);
				$path = $params->get('uploadpath');
				$path = str_replace('{{uid}}', BlogHelperMember::niceidformat($view->row->created_by), $path);
			} 
			else 
			{
				$path = $this->params->get('uploadpath');
				$path = str_replace('{{gid}}', $this->group->get('gidNumber'), $path);
			}
			
			$wikiconfig = array(
				'option'   => $this->option,
				'scope'    => $this->group->get('gidNumber') . DS . 'blog',
				'pagename' => $view->row->alias,
				'pageid'   => 0,
				'filepath' => $path,
				'domain'   => $this->group->get('cn')
			);
			
			//$p = new WikiParser(stripslashes($view->row->title), $this->option, 'blog', $view->row->alias, 0, $path);
			$view->row->content = $p->parse("\n" . stripslashes($view->row->content), $wikiconfig, true, true);
		}

		$bc = new BlogComment($this->database);
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
		$view->replyto = new BlogComment($this->database);
		$view->replyto->load($r);

		$bt = new BlogTags($this->database);
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
		if (is_file(JPATH_ROOT . DS . 'plugins' . DS . 'groups' . DS . 'blog' . DS . 'blog.js')) {
			$document->addScript('plugins' . DS . 'groups' . DS . 'blog' . DS . 'blog.js');
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
		$blog = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=blog');

		if ($juser->get('guest')) 
		{
			//$app->enqueueMessage(JText::_('GROUPS_LOGIN_NOTICE'), 'warning');
			$app->redirect('/login?return=' . base64_encode($blog));
		}

		if (!$this->authorized) 
		{
			$app->enqueueMessage(JText::_('You are not authorized to edit this blog entry.'), 'error');
			$app->redirect($blog);
		}

		if (!$this->_getPostingPermissions()) 
		{
			$app->enqueueMessage(JText::_('You do not have permission to post entries.'), 'error');
			$app->redirect($blog);
		}

		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'blog',
				'name'    => 'edit'
			)
		);

		$view->option = $this->option;
		$view->group = $this->group;
		$view->task = $this->action;
		$view->config = $this->params;
		$view->authorized = $this->authorized;

		$id = JRequest::getInt('entry', 0);

		$view->entry = new BlogEntry($this->database);
		$view->entry->load($id, 'member');
		if (!$view->entry->id) 
		{
			$view->entry->allow_comments = 1;
			$view->entry->state = 0;
			$view->entry->scope = 'group';
			$view->entry->created_by = $juser->get('id');
			$view->entry->group_id = $this->group->get('gidNumber');
		}

		$bt = new BlogTags($this->database);
		$view->tags = $bt->get_tag_string($view->entry->id);

		return $view->loadTemplate();
	}

	/**
	 * Strip invalid characters from title to make an alias
	 * 
	 * @param      string $title Title to normalize
	 * @return     string
	 */
	private function _normalizeTitle($title)
	{
		$title = str_replace(' ', '-', $this->_shortenTitle($title));
		$title = preg_replace("/[^a-zA-Z0-9\-]/", '', $title);
		return strtolower($title);
	}

	/**
	 * Shorten a title
	 * 
	 * @param      string  $text  Text to shorten
	 * @param      integer $chars Length to shorten to
	 * @return     string
	 */
	public function _shortenTitle($text, $chars=100)
	{
		$text = strip_tags($text);
		$text = trim($text);
		if (strlen($text) > $chars) 
		{
			$text = $text . ' ';
			$text = substr($text, 0, $chars);
			$text = substr($text, 0, strrpos($text, ' '));
		}
		return $text;
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
			$this->setError(JText::_('PLG_GROUPS_BLOG_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		if (!$this->_getPostingPermissions()) 
		{
			$this->setError(JText::_('You do not have permission to edit/save entries.'));
			return $this->_browse();
		}

		$entry = JRequest::getVar('entry', array(), 'post');

		$row = new BlogEntry($this->database);
		if (!$row->bind($entry)) 
		{
			$this->setError($row->getError());
			return $this->_edit();
		}

		//$row->id = JRequest::getInt('entry_id', 0);

		if (!$row->id) 
		{
			$row->alias = $this->_normalizeTitle($row->title);
			$row->created = date('Y-m-d H:i:s', time());  // use gmdate() ?
			$row->publish_up = date('Y-m-d H:i:s', time());
		}

		if (!$row->publish_up || $row->publish_up == '0000-00-00 00:00:00') 
		{
			$row->publish_up = $row->created;
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

		// Process tags
		$tags = trim(JRequest::getVar('tags', ''));
		$bt = new BlogTags($this->database);
		$bt->tag_object($juser->get('id'), $row->id, $tags, 1, 1);

		//$this->entry = $row;

		//return $this->_entry();
		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=com_groups&gid=' . $this->group->get('cn') . '&active=blog&scope=' . JHTML::_('date', $row->publish_up, $this->yearFormat, 0) . '/' . JHTML::_('date', $row->publish_up, $this->monthFormat, 0) . '/' . $row->alias));
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

		// Initiate a blog entry object
		$entry = new BlogEntry($this->database);
		$entry->load($id);

		// Did they confirm delete?
		if (!$process || !$confirmdel) 
		{
			if ($process && !$confirmdel) 
			{
				$this->setError(JText::_('PLG_GROUPS_BLOG_ERROR_CONFIRM_DELETION'));
			}

			// Output HTML
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'groups',
					'element' => 'blog',
					'name'    => 'delete'
				)
			);
			$view->option = $this->option;
			$view->group = $this->group;
			$view->task = $this->action;
			$view->config = $this->params;
			$view->entry = $entry;
			$view->authorized = $this->authorized;
			if ($this->getError()) 
			{
				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}
			}
			return $view->loadTemplate();
		}

		// Delete all comments on an entry
		if (!$entry->deleteComments($id)) 
		{
			$this->setError($entry->getError());
			return $this->_browse();
		}

		// Delete all associated content
		if (!$entry->deleteTags($id)) 
		{
			$this->setError($entry->getError());
			return $this->_browse();
		}

		// Delete all associated content
		if (!$entry->deleteFiles($id)) 
		{
			$this->setError($entry->getError());
			return $this->_browse();
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
	private function _savecomment()
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
		$row = new BlogComment($this->database);
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
	private function _deletecomment()
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

		// Initiate a blog comment object
		$comment = new BlogComment($this->database);

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

		if ($this->authorized != 'manager' && $this->authorized != 'admin') 
		{
			$this->setError(JText::_('PLG_GROUPS_BLOG_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		// Output HTML
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'blog',
				'name'    => 'settings'
			)
		);
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->task       = $this->action;
		$view->config     = $this->params;
		$view->settings   = new Hubzero_Plugin_Params($this->database);
		$view->settings->loadPlugin($this->group->gidNumber, 'groups', 'blog');
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
		$juser =& JFactory::getUser();
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

		$row = new Hubzero_Plugin_Params($this->database);
		if (!$row->bind($settings)) 
		{
			$this->setError($row->getError());
			return $this->_entry();
		}

		// Get parameters
		$params = JRequest::getVar('params', '', 'post');
		if (is_array($params)) 
		{
			$txt = array();
			foreach ($params as $k=>$v)
			{
				$txt[] = "$k=$v";
			}
			$row->params = implode("\n", $txt);
		}

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

		//$this->message = JText::_('Settings successfully saved!');
		//return $this->_settings();
		$app =& JFactory::getApplication();
		$app->enqueueMessage('Settings successfully saved!', 'passed');
		$app->redirect(JRoute::_('index.php?option=com_groups&gid=' . $this->group->get('cn') . '&active=blog&task=settings'));
	}

	/**
	 * Strip wiki markup from text
	 * 
	 * @param      string $text
	 * @return     string
	 */
	public static function stripWiki($text)
	{
		$wiki = array(
			"'''",   // <strong>
			"''",    // <em>
			"'''''", // <strong><em>
			"__",    // <u>
			"{{{",   // <pre>
			"}}}",   // </pre>
			"~~",    // <strike>
			"^",     // <superscript>
			",,",    // <subscript>
			"==",    // <h2>
			"===",   // <h3>
			"====",  // <h4>
			"||",    // <td>
			"----"   // <hr />
		);

		$stripped_text = preg_replace('/\[\[\S{1,}\]\]/', '', $text);
		$stripped_text = str_replace($wiki, '', $stripped_text);

		return nl2br($stripped_text);
	}
}
