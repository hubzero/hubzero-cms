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
 * Wiki controller class for comments
 */
class WikiControllerComments extends \Hubzero\Component\SiteController
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

		$this->book = new WikiModelBook(($this->_group ? $this->_group : '__site__'));

		parent::__construct($config);
	}

	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		if (!$this->book->pages('count'))
		{
			if ($result = $this->book->scribe($this->_option))
			{
				$this->setError($result);
			}

			JDEBUG ? JProfiler::getInstance('Application')->mark('afterWikiSetup') : null;
		}

		$this->page = $this->book->page();

		if (in_array($this->page->get('namespace'), array('image', 'file')))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=media&scope=' . $this->page->get('scope') . '&pagename=' . $this->page->get('pagename') . '&task=download'
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

		$this->view->page      = $this->page;
		$this->view->config    = $this->config;
		$this->view->base_path = $this->_base_path;
		$this->view->sub       = $this->_sub;

		// Viewing comments for a specific version?
		$this->view->v = JRequest::getInt('version', 0);

		if (!isset($this->view->mycomment) && !$this->juser->get('guest'))
		{
			$this->view->mycomment = new WikiModelComment(0);
			// No ID, so we're creating a new comment
			// In that case, we'll need to set some data...
			$revision = $this->page->revision('current');

			$this->view->mycomment->set('pageid', $revision->get('pageid'));
			$this->view->mycomment->set('version', $revision->get('version'));
			$this->view->mycomment->set('parent', JRequest::getInt('parent', 0));
			$this->view->mycomment->set('created_by', $this->juser->get('id'));
		}

		// Prep the pagename for display
		// e.g. "MainPage" becomes "Main Page"
		$this->view->title = $this->page->get('title');

		// Set the page's <title> tag
		$document = JFactory::getDocument();
		$document->setTitle(JText::_(strtoupper($this->_option)) . ': ' . $this->view->title . ': ' . JText::_(strtoupper($this->_option . '_' . $this->_task)));

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
			$this->page->link()
		);
		$pathway->addItem(
			JText::_(strtoupper($this->_task)),
			$this->page->link('comments')
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
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($url))
			);
			return;
		}

		// Retrieve a comment ID if we're editing
		$id = JRequest::getInt('id', 0);

		// Add the comment object to our controller's registry
		// This is how comments() knows if it needs to display a form or not
		$this->view->mycomment = new WikiModelComment($id);

		if (!$id)
		{
			// No ID, so we're creating a new comment
			// In that case, we'll need to set some data...
			$revision = $this->page->revision('current');

			$this->view->mycomment->set('pageid', $revision->get('pageid'));
			$this->view->mycomment->set('version', $revision->get('version'));
			$this->view->mycomment->set('parent', JRequest::getInt('parent', 0));
			$this->view->mycomment->set('created_by', $this->juser->get('id'));
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
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$fields = JRequest::getVar('comment', array(), 'post');

		// Bind the form data to our object
		$comment = new WikiModelComment($fields['id']);
		if (!$comment->bind($fields))
		{
			$this->setError($comment->getError());
			$this->displayTask();
			return;
		}

		// Parse the wikitext and set some values
		$comment->set('chtml', $comment->content('parsed'));
		$comment->set('anonymous', ($comment->get('anonymous') ? 1 : 0));
		$comment->set('created', ($comment->get('created') ? $comment->get('created') : JFactory::getDate()->toSql()));

		// Save the data
		if (!$comment->store(true))
		{
			$this->setError($comment->getError());
			$this->displayTask();
			return;
		}

		// Did they rate the page?
		// If so, update the page with the new average rating
		if ($comment->get('rating'))
		{
			$this->page->calculateRating();
			if (!$this->page->store())
			{
				$this->setError($this->page->getError());
			}
		}

		// Redirect to Comments page
		$this->setRedirect(
			JRoute::_($this->page->link('comments'))
		);
	}

	/**
	 * Remove a comment
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		$msg = null;
		$cls = 'message';

		// Make sure we have a comment to delete
		if (($id = JRequest::getInt('id', 0)))
		{
			// Make sure they're authorized to delete (must be an author)
			if ($this->page->access('delete', 'comment'))
			{
				$comment = new WikiModelComment($id);
				$comment->set('status', 2);
				if ($comment->store(false))
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

		// Redirect to Comments page
		$this->setRedirect(
			$this->page->link('comments'),
			$msg,
			$cls
		);
	}
}

