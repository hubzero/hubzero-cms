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
 * Wiki controller class for comments
 */
class WikiControllerComments extends Hubzero_Controller
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
		if (isset($config['base_path'])) 
		{
			$this->_base_path = $config['base_path'];
		}

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

		if ($this->_sub)
		{
			JRequest::setVar('task', JRequest::getWord('action'));
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

		$this->page   = WikiHelperPage::getPage($this->config);
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

		$this->registerTask('addcomment', 'new');
		$this->registerTask('editcomment', 'edit');
		$this->registerTask('savecomment', 'save');
		$this->registerTask('removecomment', 'remove');
		$this->registerTask('reportcomment', 'report');

		parent::execute();
	}

	/**
	 * Display comments for a wiki page
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->setLayout('display');

		$this->view->page = $this->page;
		$this->view->config = $this->config;
		$this->view->base_path = $this->_base_path;
		$this->view->sub = $this->_sub;

		// Viewing comments for a specific version?
		$ver = '';
		$this->view->v = JRequest::getInt('version', 0);
		if ($this->view->v) 
		{
			$ver = 'AND version=' . $this->view->v;
		} 

		// Get comments
		$c = new WikiPageComment($this->database);
		$this->view->comments = $c->getComments(
			$this->page->id, 
			0, 
			$ver, 
			''
		);
		for ($i=0,$n=count($this->view->comments);$i<$n;$i++)
		{
			// Get replies
			$this->view->comments[$i]->children = $c->getComments(
				$this->page->id, 
				$this->view->comments[$i]->id, 
				$ver, 
				''
			);
			for ($k=0,$m=count($this->view->comments[$i]->children);$k<$m;$k++)
			{
				// Get replies to replies
				$this->view->comments[$i]->children[$k]->children = $c->getComments(
					$this->page->id, 
					$this->view->comments[$i]->children[$k]->id, 
					$ver, 
					'LIMIT 4'
				);
			}
		}

		// Do we have a comment object? If so, then we're in edit mode
		if (is_object($this->comment)) 
		{
			$this->view->mycomment = $this->comment;
		} 
		else 
		{
			$this->view->mycomment = NULL;
		}

		$revision = new WikiPageRevision($this->database);
		$this->view->versions = $revision->getRevisionNumbers($this->page->id);

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
		$document->setTitle(JText::_(strtoupper($this->_name)) . ': ' . $this->view->title . ': ' . JText::_(strtoupper($this->_task)));

		// Set the pathway
		$pathway = JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		$pathway->addItem(
			$this->view->title,
			'index.php?option=' . $this->_option . '&controller=page&scope=' . $this->page->scope . '&pagename=' . $this->pagename
		);
		$pathway->addItem(
			JText::_(strtoupper($this->_task)),
			'index.php?option=' . $this->_option . '&controller=comments&scope=' . $this->page->scope . '&pagename=' . $this->pagename . '&task=' . $this->_task
		);

		// Output content
		$this->view->juser = $this->juser;
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
	 * Create a comment
	 * 
	 * @return     void
	 */
	public function newTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a comment
	 * 
	 * @return     void
	 */
	public function editTask()
	{
		// Is the user logged in?
		// If not, then we need to stop everything else and display a login form
		if ($this->juser->get('guest')) 
		{
			$url = JRequest::getVar('REQUEST_URI', '', 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($url))
			);
			return;
		}

		// Retrieve a comment ID if we're editing
		$id = JRequest::getInt('id', 0);

		// Add the comment object to our controller's registry
		// This is how comments() knows if it needs to display a form or not
		$this->comment = new WikiPageComment($this->database);
		$this->comment->load($id);

		if (!$id) 
		{
			// No ID, so we're creating a new comment
			// In that case, we'll need to set some data...
			$revision = $this->page->getCurrentRevision();

			$this->comment->pageid     = $revision->pageid;
			$this->comment->version    = $revision->version;
			$this->comment->parent     = JRequest::getInt('parent', 0);
			$this->comment->created_by = $this->juser->get('id');
		}

		$this->displayTask();
	}

	/**
	 * Save a comment
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		$pagename = JRequest::getVar('pagename', '', 'post');
		$scope    = JRequest::getVar('scope', '', 'post');

		$fields = JRequest::getVar('comment', array(), 'post');

		// Bind the form data to our object
		$this->comment = new WikiPageComment($this->database);
		if (!$this->comment->bind($fields)) 
		{
			$this->setError($this->comment->getError());
			$this->displayTask();
			return;
		}

		// Parse the wikitext and set some values
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => $scope,
			'pagename' => $pagename,
			'pageid'   => $this->comment->pageid,
			'filepath' => '',
			'domain'   => $this->_group
		);
		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();
		$this->comment->chtml = $p->parse($this->comment->ctext, $wikiconfig);

		$this->comment->anonymous = ($this->comment->anonymous == 1 || $this->comment->anonymous == '1') ? $this->comment->anonymous : 0;
		$this->comment->created   = ($this->comment->created) ? $this->comment->created : date("Y-m-d H:i:s");

		// Check for missing (required) fields
		if (!$this->comment->check()) 
		{
			$this->setError($this->comment->getError());
			$this->displayTask();
			return;
		}

		// Save the data
		if (!$this->comment->store()) 
		{
			$this->setError($this->comment->getError());
			$this->displayTask();
			return;
		}

		// Did they rate the page? 
		// If so, update the page with the new average rating
		if ($this->comment->rating) 
		{
			$page = new WikiPage($this->database);
			$page->load(intval($this->comment->pageid));
			$page->calculateRating();
			if (!$page->store()) 
			{
				$this->setError($page->getError());
			}
		}

		// Redirect to Comments page
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&scope=' . $scope . '&pagename=' . $pagename . '&task=comments')
		);
	}

	/**
	 * Remove a comment
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		$id = JRequest::getInt('id', 0);

		$msg = null;
		$cls = 'message';

		// Make sure we have a comment to delete
		if ($id) 
		{
			// Make sure they're authorized to delete (must be an author)
			if ($this->config->get('access-comment-delete')) 
			{
				$comment = new WikiPageComment($this->database);
				$comment->load($id);
				$comment->status = 2;
				if ($comment->store())
				{
					$msg = JText::_('COM_WIKI_COMMENT_DELETED');
				}
			} 
			else 
			{
				$msg = JText::_('COM_WIKI_ERROR_NOTAUTH');
				$cls = 'error';
			}
		}

		$pagename = JRequest::getVar('pagename', '');
		$scope    = JRequest::getVar('scope', '');

		// Redirect to Comments page
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&scope=' . $scope . '&pagename=' . $pagename . '&task=comments'),
			$msg,
			$cls
		);
	}

	/**
	 * Flag a comment as abusive
	 * 
	 * @return     void
	 */
	public function reportTask()
	{
		$id = JRequest::getInt('id', 0, 'request');

		// Make sure we have a comment to report
		if ($id) 
		{
			$comment = new WikiPageComment($this->database);
			$comment->report($id);

			$this->addComponentMessage(JText::sprintf('WIKI_COMMENT_REPORTED', $id));
		}

		$this->displayTask();
	}
}

