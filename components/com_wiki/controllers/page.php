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

ximport('Hubzero_Controller');

/**
 * Wiki controller class for pages
 */
class WikiControllerPage extends Hubzero_Controller
{
	/**
	 * Constructor
	 * 
	 * @param      array $config Optional configurations
	 * @return     void
	 */
	public function __construct($config=array())
	{
		$this->_base_path = JPATH_ROOT . DS . 'components' . DS . 'com_wiki';

		$this->_sub = false;
		if (isset($config['sub'])) 
		{
			$this->_sub = $config['sub'];
		}

		$this->_group = false;
		if (isset($config['group'])) 
		{
			$this->_group = $config['group'];
		}

		/*$this->_access = false;
		if (isset($config['access'])) 
		{
			$this->_access = $config['access'];
		}*/

		parent::__construct($config);
	}

	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		if ($this->_sub || $this->_option != 'com_wiki')
		{
			$this->config = JComponentHelper::getParams('com_wiki');
		}
		
		define('WIKI_SUBPAGE_SEPARATOR', $this->config->get('subpage_separator', '/'));
		define('WIKI_MAX_PAGENAME_LENGTH', $this->config->get('max_pagename_length', 100));
		
		if (!is_object($this->page))
		{
			$this->page = WikiHelperPage::getPage($this->config);
		}
		$this->config = WikiHelperPage::authorize($this->config, $this->page);

		$wp = new WikiPage($this->database);
		if (!$wp->count()) 
		{
			$result = WikiSetup::initialize($this->_option);
			if ($result) 
			{
				$this->setError($result);
			}
		}

		if (substr(strtolower($this->page->pagename), 0, strlen('image:')) == 'image:'
		 || substr(strtolower($this->page->pagename), 0, strlen('file:')) == 'file:') 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=media&scope=' . $this->page->scope . '&pagename=' . $this->page->pagename . '&task=download'
			);
			return;
		}

		parent::execute();
	}

	/**
	 * Display a page
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->page = $this->page;
		$this->view->config = $this->config;
		$this->view->base_path = $this->_base_path;
		$this->view->sub = $this->_sub;

		if (!$this->_sub)
		{
			// Include any CSS
			if ($this->page->pagename == 'MainPage')
			{
				$this->_getStyles('', 'introduction.css', true); // component, stylesheet name, look in media system dir
			}
			$this->_getStyles();
		}
		// Include any Scripts
		$this->_getScripts('assets/js/wiki', 'com_wiki');

		// Prep the pagename for display 
		$this->view->title = $this->page->getTitle();

		// Set the page's <title> tag
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_(strtoupper($this->_name)) . ': ' . $this->view->title);

		// Set the pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
		}

		// Does a page exist for the given pagename?
		if (!$this->page->exist()) 
		{
			// No! Ask if they want to create a new page
			$this->view->setLayout('doesnotexist');

			if ($this->getError()) 
			{
				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}
			}
			$this->view->display();
			return;
		}
		
		if ($this->page->group_cn && !$this->_group)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_groups&scope=' . $this->page->scope . '&pagename=' . $this->page->pagename)
			);
			return;
		}

		// Check if the page is group restricted and the user is authorized
		if (!$this->config->get('access-view')) 
		{
			JError::raiseWarning(403, JText::_('WIKI_WARNING_NOT_AUTH'));
			return;
		}

		if ($this->page->scope && !$this->page->group_cn) 
		{
			$bits = explode('/', $this->page->scope);
			$s = array();
			foreach ($bits as $bit)
			{
				$bit = trim($bit);
				if ($bit != '/' && $bit != '') 
				{
					$p = new WikiPage($this->database);
					$p->load($bit, implode('/', $s));
					if ($p->id) 
					{
						$pathway->addItem($p->title, 'index.php?option=' . $this->_option . '&scope=' . $p->scope . '&pagename=' . $p->pagename);
					}
					$s[] = $bit;
				}
			}
		}
		$pathway->addItem(
			$this->view->title,
			'index.php?option=' . $this->_option . '&scope=' . $this->page->scope . '&pagename=' . $this->page->pagename
		);

		// Retrieve a specific version if given
		$this->view->version  = JRequest::getInt('version', 0);
		$this->view->revision = $this->page->getRevision($this->view->version);

		if (!$this->view->revision->id) 
		{
			$this->view->setLayout('nosuchrevision');

			if ($this->getError()) 
			{
				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}
			}
			$this->view->display();
			return;
		}

		// Up the hit counter
		//$this->page->hit();

		// Load the wiki parser
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => $this->page->scope,
			'pagename' => $this->page->pagename,
			'pageid'   => $this->page->id,
			'filepath' => '',
			'domain'   => $this->page->group_cn
		);

		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();

		// Parse the text
		$this->view->revision->pagehtml = $p->parse($this->view->revision->pagetext, $wikiconfig, true, true);

		// Create a linked Table of Contents
		//$this->view->output = (is_object($p->_parser)) ? $p->_parser->parser->formatHeadings($this->view->revision->pagehtml) : array('text'=>$this->view->revision->pagehtml,'toc'=>'');

		// Get the page's tags
		if ($this->config->get('admin')) 
		{
			$this->view->tags = $this->page->getTags(1);
		} 
		else 
		{
			$this->view->tags = $this->page->getTags();
		}

		$this->view->message = $this->_message;
		
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Show a form for creating an entry
	 * 
	 * @return     void
	 */
	public function newTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 * 
	 * @return     void
	 */
	public function editTask()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			$url = JRequest::getVar('REQUEST_URI', '', 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($url))
			);
			return;
		}

		// Check if the page is locked and the user is authorized
		if ($this->page->state == 1 && !$this->config->get('access-manage')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&scope=' . $this->page->scope . '&pagename=' . $this->page->pagename),
				JText::_('WIKI_WARNING_NOT_AUTH_EDITOR'),
				'warning'
			);
			return;
		}

		// Check if the page is group restricted and the user is authorized
		if (!$this->config->get('access-edit') && !$this->config->get('access-modify')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&scope=' . $this->page->scope . '&pagename=' . $this->page->pagename),
				JText::_('WIKI_WARNING_NOT_AUTH_EDITOR'),
				'warning'
			);
			return;
		}

		$this->view->setLayout('edit');

		// Load the page
		$ischild = false;
		if ($this->page->id && $this->_task == 'new') 
		{
			$this->page->id = 0;
			$ischild = true;
		}

		// Get the most recent version for editing
		if (!is_object($this->revision))
		{
			$this->revision = $this->page->getCurrentRevision();
			$this->revision->created_by = $this->juser->get('id');
			$this->revision->summary = '';
		}

		// If an existing page, pull its tags for editing
		$t = '';
		$a = '';
		if ($this->page->id) 
		{
			// Get the tags on this page
			$tags = $this->page->getTags();

			if (count($tags) > 0) 
			{
				$tagarray = array();
				foreach ($tags as $tag)
				{
					$tagarray[] = $tag['raw_tag'];
				}
				$t = implode(', ', $tagarray);
			}

			// Get the list of authors and find out their usernames
			$wpa = new WikiPageAuthor($this->database);
			$auths = $wpa->getAuthors($this->page->id);
			$authors = '';
			if (count($auths) > 0) 
			{
				$autharray = array();
				foreach ($auths as $auth)
				{
					$autharray[] = $auth->username;
				}
				$a = implode(', ', $autharray);
			}
		} 
		else 
		{
			$this->page->created_by = $this->juser->get('id');
			//$this->page->scope = $this->scope;
			if ($this->_group) 
			{
				$this->page->group_cn = $this->_group;
			}

			if ($ischild && $this->page->pagename) 
			{
				$this->page->scope .= ($this->page->scope) ? DS . $this->page->pagename : $this->page->pagename;
				$this->page->pagename = '';
				$this->page->title = ($this->page->title) ? $this->page->title : JText::_('New Page');
			}

			$this->page->title = $this->page->getTitle();
		}
		
		$this->view->tags = trim(JRequest::getVar('tags', $t, 'post'));
		$this->view->authors = trim(JRequest::getVar('authors', $a, 'post'));

		if (!$this->_sub)
		{
			// Include any CSS
			$this->_getStyles();
		}
		// Include any Scripts
		$this->_getScripts('assets/js/wiki', 'com_wiki');
		if (JPluginHelper::isEnabled('system', 'jquery')) 
		{
			$document =& JFactory::getDocument();
			$document->addScript("/media/system/js/jquery.fileuploader.js");
		}

		// Prep the pagename for display 
		// e.g. "MainPage" becomes "Main Page"
		$this->view->title = $this->page->getTitle();
		$this->view->title = ($this->view->title) ? $this->view->title : JText::_('NEW') . ' ' . JText::_('page');

		// Set the page's <title> tag
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_(strtoupper($this->_name)) . ': ' . $this->view->title . ': ' . JText::_(strtoupper($this->_task)));

		// Set the pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
		}
		$pathway->addItem(
			$this->view->title,
			'index.php?option=' . $this->_option . '&scope=' . $this->page->scope . '&pagename=' . $this->page->pagename
		);
		$pathway->addItem(
			JText::_(strtoupper($this->_task)),
			'index.php?option=' . $this->_option . '&scope=' . $this->page->scope . '&pagename=' . $this->page->pagename . '&task=' . $this->_task
		);

		$this->view->preview = NULL;

		// Are we previewing?
		if ($this->preview)
		{
			// Yes - get the preview so we can parse it and display
			$this->view->preview = $this->preview;

			// Parse the HTML
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => $this->page->scope,
				'pagename' => $this->page->pagename,
				'pageid'   => $this->page->id,
				'filepath' => '',
				'domain'   => $this->_group
			);
			ximport('Hubzero_Wiki_Parser');
			$p =& Hubzero_Wiki_Parser::getInstance();

			$this->revision->pagehtml = $p->parse($this->revision->pagetext, $wikiconfig, true, true);
		}

		// Process the page's params
		if (!is_object($this->page->params))
		{
			$paramClass = 'JParameter';
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$paramClass = 'JRegistry';
			}
			$this->page->params = new $paramClass($this->page->params);
		}

		// We need to recalculate permissions if this is a fall-through from saveTask()
		$this->config = WikiHelperPage::authorize($this->config, $this->page);

		$this->view->sub = $this->_sub;
		$this->view->base_path = $this->_base_path;
		$this->view->message = $this->_message;
		$this->view->page = $this->page;
		$this->view->config = $this->config;
		$this->view->revision = $this->revision;

		// Pull a tree of pages in this wiki
		$items = $this->page->getPages(array(
			'group'  => $this->_group,
			'sortby' => 'pagename ASC, scope ASC'
		));
		if ($items)
		{
			foreach ($items as $k => $branch)
			{
				// Since these will be parent pages, we need to add the item's pagename to the scope
				$branch->scope = ($branch->scope) ? $branch->scope . '/' . $branch->pagename : $branch->pagename;
				$branch->scopeName = $branch->scope;
				// Strip the group name from the beginning of the scope for display.
				if ($this->_group)
				{
					$branch->scopeName = substr($branch->scope, strlen($this->_group . '/wiki/'));
				}
				// Push the item to the tree
				$tree[$branch->scope] = $branch;
			}
			$items = $tree;
			ksort($items);
		}
		$this->view->tree = $items;

		$this->view->tplate = trim(JRequest::getVar('tplate', ''));

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	public static function treeRecurse($id, $children, $maxlevel=9999, $level=0)
	{
		$list = array();
		if (@$children[$id] && $level <= $maxlevel)
		{
			foreach ($children[$id] as $v)
			{
				$id = $v->pagename;

				$list[$id] = $v;
				$list[$id]['children'] = array();
				if (isset($children[$id]) && count($children[$id]) > 0)
				{
					$list[$id]->children = self::treeRecurse($id, $children, $maxlevel, $level+1);
				}
				//$list[$id]['children'] = @$children[$id];
				//$list = self::treeRecurse($id, $children, $maxlevel, $level+1);
			}
		}
		return $list;
	}

	/**
	 * Save a wiki page
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			$url = JRequest::getVar('REQUEST_URI', '', 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($url))
			);
			return;
		}
		
		// Incoming revision
		$rev = JRequest::getVar('revision', array(), 'post', 'none', 2);
		$rev['pageid'] = (isset($rev['pageid'])) ? intval($rev['pageid']) : 0;

		$this->revision = new WikiPageRevision($this->database);
		$this->revision->pageid     = $rev['pageid'];
		$this->revision->created    = date('Y-m-d H:i:s', time());
		$this->revision->created_by = $this->juser->get('id');
		$this->revision->version    = (isset($rev['version']))    ? intval($rev['version'])    : 0;
		$this->revision->summary    = (isset($rev['summary']))    ? preg_replace('/\s+/', ' ', trim($rev['summary'])) : '';
		$this->revision->minor_edit = (isset($rev['minor_edit'])) ? intval($rev['minor_edit']) : 0;
		$this->revision->pagetext   = (isset($rev['pagetext']))   ? rtrim($rev['pagetext'])    : '';

		// Incoming page
		$page = JRequest::getVar('page', array(), 'post', 'none', 2);

		$this->page = new WikiPage($this->database);
		$this->page->load($rev['pageid']);
		$this->page->title    = (isset($page['title']))  ? trim($page['title'])    : '';
		$this->page->pagename = trim(JRequest::getVar('pagename', '', 'post'));
		$this->page->scope    = trim(JRequest::getVar('scope', '', 'post'));
		$this->page->access   = (isset($page['access'])) ? intval($page['access']) : 0;
		$this->page->group_cn    = (isset($page['group']))  ? trim($page['group'])    : '';
		$this->page->state    = (isset($page['state']))  ? intval($page['state'])  : 0;
		
		// Get parameters
		$paramClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramClass = 'JRegistry';
		}
		$params = new $paramClass($this->page->params);
		$params->bind(JRequest::getVar('params', array(), 'post'));
		$this->page->params = $params->toString();
		
		// Get the previous version to compare against
		if (!$rev['pageid']) 
		{
			// New page - save it to the database
			$this->page->created_by = $this->juser->get('id');

			$old = new WikiPageRevision($this->database);
		} 
		else 
		{
			// Get the revision before changes
			$old = $this->page->getCurrentRevision();
		}

		// Was the preview button pushed?
		$this->preview = trim(JRequest::getVar('preview', ''));
		if ($this->preview) 
		{
			// Set the component task
			if (!$rev['pageid']) 
			{
				JRequest::setVar('task', 'new');
				$this->_task = 'new';
			}
			else
			{
				JRequest::setVar('task', 'edit');
				$this->_task = 'edit';
			}

			// Push on through to the edit form
			$this->editTask();
			return;
		}

		// Check content
		if (!$this->page->check()) 
		{
			$this->setError($this->page->getError());
			$this->editTask();
			return;
		}

		// Store new content
		if (!$this->page->store()) 
		{
			$this->setError($this->page->getError());
			$this->editTask();
			return;
		}
		// Make sure we have a page ID, we'll need it
		if (!$this->page->id) 
		{
			$this->page->getID();
		}
		
		// Get allowed authors
		if (!$this->page->updateAuthors(JRequest::getVar('authors', '', 'post'))) 
		{
			$this->setError($this->page->getError());
		}

		// Get the upload path
		$path = DS . trim($this->config->get('filepath', '/site/wiki'), DS);

		// Rename the temporary upload directory if it exist
		$lid = JRequest::getInt('lid', 0, 'post');
		if ($lid != $this->page->id) 
		{
			if (is_dir(JPATH_ROOT . $path . DS . $lid)) 
			{
				jimport('joomla.filesystem.folder');
				if (!JFolder::move(JPATH_ROOT . $path . DS . $lid, JPATH_ROOT . $path . DS . $this->page->id)) 
				{
					$this->setError(JFolder::move(JPATH_ROOT . $path . DS . $lid, JPATH_ROOT . $path . DS . $this->page->id));
				}
				$wpa = new WikiPageAttachment($this->database);
				$wpa->setPageID($lid, $this->page->id);
			}
		}

		$this->revision->pageid = $this->page->id;
		$this->revision->version++;

		if ($params->get('mode', 'wiki') == 'knol')
		{
			// Set revisions to NOT approved
			$this->revision->approved = 0;
			// If an author or the original page creator, set to approved
			if ($this->page->created_by == $this->juser->get('id')
			 || $this->page->isAuthor($this->juser->get('id'))) 
			{
				$this->revision->approved = 1;
			}
		}
		else 
		{
			// Wiki mode, approve revision
			$this->revision->approved = 1;
		}

		// Stripslashes just to make sure
		//$old->pagetext = rtrim(stripslashes($old->pagetext));
		//$this->revision->pagetext = rtrim(stripslashes($this->revision->pagetext));

		// Compare against previous revision
		// We don't want to create a whole new revision if just the tags were changed
		if (rtrim($old->pagetext) != rtrim($this->revision->pagetext)) 
		{
			// Transform the wikitext to HTML
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => $this->page->scope,
				'pagename' => $this->page->pagename,
				'pageid'   => $this->page->id,
				'filepath' => '',
				'domain'   => $this->_group
			);
			ximport('Hubzero_Wiki_Parser');
			$p =& Hubzero_Wiki_Parser::getInstance();
			$this->revision->pagehtml = $p->parse($this->revision->pagetext, $wikiconfig);

			// Parse attachments
			$a = new WikiPageAttachment($this->database);
			$a->pageid = $this->page->id;
			$a->path = $path;

			$this->revision->pagehtml = $a->parse($this->revision->pagehtml);
			if ($this->config->get('access-manage') || $this->config->get('access-edit')) 
			{
				$this->revision->approved = 1;
			}

			// Check content
			if (!$this->revision->check()) 
			{
				echo WikiHtml::alert($this->revision->getError());
				exit();
			}
			// Store content
			if (!$this->revision->store()) 
			{
				echo WikiHtml::alert($this->revision->getError());
				exit();
			}
		}

		// Process tags
		$tags = trim(JRequest::getVar('tags', ''));
		$tagging = new WikiTags($this->database);
		$tagging->tag_object($this->revision->created_by, $this->page->id, $tags, 1, 1);

		// Log the action
		$log = new WikiLog($this->database);
		$log->pid       = $this->page->id;
		$log->uid       = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action    = ($this->revision->version == 1) ? 'page_created' : 'page_edited';
		$log->actorid   = $this->juser->get('id');
		if (!$log->store()) 
		{
			$this->setError($log->getError());
		}

		// Redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&scope=' . $this->page->scope . '&pagename=' . $this->page->pagename)
		);
	}

	/**
	 * Delete a page
	 * 
	 * @return     void
	 */
	public function deleteTask()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			$url = JRequest::getVar('REQUEST_URI', '', 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($url))
			);
			return;
		}

		if (!is_object($this->page)) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&scope=' . $this->scope),
				JText::_('WIKI_ERROR_PAGE_NOT_FOUND'),
				'error'
			);
			return;
		}

		// Make sure they're authorized to delete
		if (!$this->config->get('access-delete')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&scope=' . $this->scope),
				JText::_('WIKI_ERROR_NOTAUTH'),
				'error'
			);
			return;
		}
		
		$confirmed = JRequest::getInt('confirm', 0, 'post');

		switch ($confirmed)
		{
			case 1:
				$page = new WikiPage($this->database);

				// Delete the page's history, tags, comments, etc.
				$page->deleteBits($this->page->id);

				// Finally, delete the page itself
				$page->delete($this->page->id);

				// Delete the page's files
				$path = DS . trim($this->config->get('filepath'), DS);
				if (is_dir($path . DS . $this->page->id)) 
				{
					jimport('joomla.filesystem.folder');
					if (!JFolder::delete($path . DS . $this->page->id)) 
					{
						$this->setError(JText::_('COM_WIKI_UNABLE_TO_DELETE_FOLDER'));
					}
				}
				/*$page->state = 2;
				if (!$page->store())
				{
					$this->setError(JText::_('COM_WIKI_UNABLE_TO_DELETE'));
				}*/

				// Log the action
				$log = new WikiLog($this->database);
				$log->pid       = $this->page->id;
				$log->uid       = $this->juser->get('id');
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action    = 'page_removed';
				$log->actorid   = $this->juser->get('id');
				if (!$log->store()) 
				{
					$this->setError($log->getError());
				}
			break;

			default:
				if (!$this->_sub)
				{
					// Include any CSS
					$this->_getStyles();
				}

				$this->view->page = $this->page;
				$this->view->config = $this->config;
				$this->view->base_path = $this->_base_path;
				$this->view->sub = $this->_sub;

				// Prep the pagename for display 
				// e.g. "MainPage" becomes "Main Page"
				$this->view->title = ($this->page->title) ? $this->page->title : $this->splitPagename($this->page->pagename);

				// Set the page's <title> tag
				$document =& JFactory::getDocument();
				$document->setTitle(JText::_(strtoupper($this->_name)) . ': ' . $this->view->title . ': ' . JText::_(strtoupper($this->_task)));

				// Set the pathway
				$pathway =& JFactory::getApplication()->getPathway();
				if (count($pathway->getPathWay()) <= 0) 
				{
					$pathway->addItem(
						JText::_(strtoupper($this->_name)),
						'index.php?option=' . $this->_option . '&controller=' . $this->_controller
					);
				}
				$pathway->addItem(
					$this->view->title,
					'index.php?option=' . $this->_option . '&scope=' . $this->page->scope . '&pagename=' . $this->page->pagename
				);
				$pathway->addItem(
					JText::_(strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&scope=' . $this->page->scope . '&pagename=' . $this->page->pagename . '&task=' . $this->_task
				);

				$this->view->message = $this->_message;

				if ($this->getError()) 
				{
					foreach ($this->getErrors() as $error)
					{
						$this->view->setError($error);
					}
				}

				$this->view->display();
				return;
			break;
		}

		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&scope=' . $this->page->scope)
		);
	}

	/**
	 * Show a form to rename a page
	 * 
	 * @return     void
	 */
	public function renameTask()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			$url = JRequest::getVar('REQUEST_URI', '', 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($url))
			);
			return;
		}

		// Make sure they're authorized to delete
		if (!$this->config->get('access-edit')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&scope=' . $this->scope),
				JText::_('WIKI_ERROR_NOTAUTH'),
				'error'
			);
			return;
		}

		$this->view->setLayout('rename');

		$this->view->page = $this->page;
		$this->view->config = $this->config;
		$this->view->base_path = $this->_base_path;
		$this->view->sub = $this->_sub;

		if (!$this->_sub)
		{
			// Include any CSS
			$this->_getStyles();
		}

		// Prep the pagename for display 
		// e.g. "MainPage" becomes "Main Page"
		$this->view->title = $this->page->getTitle();

		// Set the page's <title> tag
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_(strtoupper($this->_name)) . ': ' . $this->page->title . ': ' . JText::_('RENAME'));

		// Set the pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		$pathway->addItem(
			$this->view->title,
			'index.php?option=' . $this->_option . '&scope=' . $this->page->scope . '&pagename=' . $this->pagename
		);
		$pathway->addItem(
			JText::_(strtoupper('RENAME')),
			'index.php?option=' . $this->_option . '&scope=' . $this->page->scope . '&pagename=' . $this->pagename . '&task=' . $this->_task
		);

		$this->view->message = $this->_message;

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Save the new page name
	 * 
	 * @return     void
	 */
	public function saverenameTask()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			$url = JRequest::getVar('REQUEST_URI', '', 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($url))
			);
			return;
		}

		// Incoming
		$oldpagename = trim(JRequest::getVar('oldpagename', '', 'post'));
		$newpagename = trim(JRequest::getVar('newpagename', '', 'post'));
		$scope = trim(JRequest::getVar('scope', '', 'post'));

		// Remove any bad characters
		$page = new WikiPage($this->database);
		$page->load($oldpagename, $scope);

		$newpagename = $page->normalize($newpagename);

		// Are they just changing case of characters?
		if (strtolower($oldpagename) != strtolower($newpagename)) 
		{
			// Check that no other pages are using the new title
			$p = new WikiPage($this->database);
			$p->load($newpagename, $scope);
			if ($p->exist()) 
			{
				$this->setError(JText::_('WIKI_ERROR_PAGE_EXIST').' '.JText::_('CHOOSE_ANOTHER_PAGENAME'));
				$this->renameTask($page);
				return;
			}
		}

		// Load the page, reset the name, and save
		$page->pagename = $newpagename;
		$page->_tbl_key = 'id';

		if (!$page->check()) 
		{
			$this->setError($page->getError());
			$this->renameTask($page);
			return;
		}

		if (!$page->store()) 
		{
			$this->setError($page->getError());
			$this->renameTask($page);
			return;
		}

		// Log the action
		$log = new WikiLog($this->database);
		$log->pid       = $page->id;
		$log->uid       = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action    = 'page_renamed';
		$log->actorid   = $this->juser->get('id');
		if (!$log->store()) 
		{
			$this->setError($log->getError());
		}

		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&scope=' . $page->scope . '&pagename=' . $page->pagename)
		);
	}
}

