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
 * Wiki controller class for page history
 */
class WikiControllerHistory extends \Hubzero\Component\SiteController
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
		/*if ($this->_sub || $this->_option != 'com_wiki')
		{
			$this->config = JComponentHelper::getParams('com_wiki');
		}*/

		if (!$this->book->pages('count'))
		{
			if ($result = $this->book->scribe($this->_option)) 
			{
				$this->setError($result);
			}

			JPROFILE ? JProfiler::getInstance('Application')->mark('afterWikiSetup') : null;
		}

		$this->page = $this->book->page();

		if (in_array($this->page->get('namespace'), array('image', 'file')))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=media&scope=' . $this->page->get('scope') . '&pagename=' . $this->page->get('pagename') . '&task=download'
			);
			return;
		}

		$this->registerTask('deleterevision', 'delete');

		parent::execute();
	}

	/**
	 * Display a history of the current wiki page
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

		if (!$this->_sub)
		{
			// Include any CSS
			$this->_getStyles();
		}

		// Prep the pagename for display 
		// e.g. "MainPage" becomes "Main Page"
		$this->view->title = $this->page->get('title');

		// Set the page's <title> tag
		$document = JFactory::getDocument();
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
			$this->page->link()
		);
		$pathway->addItem(
			JText::_(strtoupper($this->_task)),
			$this->page->link() . '&' . ($this->_sub ? 'action' : 'task') . '=' . $this->_task
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
	 * Compare two versions of a wiki page
	 * 
	 * @return     void
	 */
	public function compareTask()
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'differenceengine.php');

		$this->view->page      = $this->page;
		$this->view->config    = $this->config;
		$this->view->base_path = $this->_base_path;
		$this->view->sub       = $this->_sub;

		// Incoming
		$oldid = JRequest::getInt('oldid', 0);
		$diff  = JRequest::getInt('diff', 0);

		// Do some error checking
		if (!$diff) 
		{
			$this->setError(JText::_('COM_WIKI_ERROR_MISSING_VERSION'));
			$this->displayTask();
			return;
		}
		if ($diff == $oldid) 
		{
			$this->setError(JText::_('COM_WIKI_ERROR_SAME_VERSIONS'));
			$this->displayTask();
			return;
		}

		// If no initial page is given, compare to the current revision
		$this->view->revision = $this->page->revision('current'); //$this->page->getRevision(0);

		$this->view->or = $this->page->revision($oldid);
		$this->view->dr = $this->page->revision($diff);

		// Diff the two versions
		$ota = explode("\n", $this->view->or->get('pagetext'));
		$nta = explode("\n", $this->view->dr->get('pagetext'));

		//$diffs = new Diff($ota, $nta);
		$formatter = new TableDiffFormatter();
		$this->view->content = $formatter->format(new Diff($ota, $nta));

		if (!$this->_sub)
		{
			// Include any CSS
			$this->_getStyles();
		}

		// Prep the pagename for display 
		// e.g. "MainPage" becomes "Main Page"
		$this->view->title = $this->page->get('title');

		// Set the page's <title> tag
		$document = JFactory::getDocument();
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
			$this->page->link()
		);
		$pathway->addItem(
			JText::_(strtoupper($this->_task)),
			$this->page->link() . '&' . ($this->_sub ? 'action' : 'task') . '=' . $this->_task
		);

		$this->view->sub     = $this->_sub;
		$this->view->message = $this->_message;
		$this->view->name    = JText::_(strtoupper($this->_name));

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
	 * Delete a revision
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

		// Incoming
		$id = JRequest::getInt('oldid', 0);

		if (!$id || !$this->page->access('delete')) 
		{
			$this->setRedirect(
				JRoute::_($this->page->link('history'))
			);
			return;
		}

		$revision = new WikiModelRevision($id);
		//$revision->load($id);

		// Get a count of all approved revisions
		if ($this->page->revisions('count', array('approved' => 1)) <= 1)
		{
			// Can't delete - it's the only approved version!
			$this->setRedirect(
				JRoute::_($this->page->link('history'))
			);
			return;
		}

		// Delete it
		$revision->set('approved', 2);

		//if (!$revision->delete($id))
		if (!$revision->store())
		{
			$this->setRedirect(
				JRoute::_($this->page->link('history')),
				JText::_('Error occurred while removing revision.'), 
				'error'
			);
			return;
		}

		// If we're deleting the current revision, set the current 
		// revision number to the previous available revision
		//if ($id == $this->page->version_id)
		//{
			//$this->page->setRevisionId();
		//}
		$this->page->revisions('list', array(), true)->last();
		$this->page->set('version_id', $this->page->revisions()->current()->get('id'));
		$this->page->store(false, 'revision_removed');

		// Log the action
		/*$log = new WikiLog($this->database);
		$log->pid       = $this->page->id;
		$log->uid       = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action    = 'revision_removed';
		$log->actorid   = $this->juser->get('id');
		if (!$log->store()) 
		{
			$this->setError($log->getError());
		}*/

		$this->setRedirect(
			JRoute::_($this->page->link('history'))
		);
	}

	/**
	 * Approve a revision
	 * 
	 * @return     void
	 */
	public function approveTask()
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
		$id = JRequest::getInt('oldid', 0);

		if (!$id || !$this->page->access('manage')) 
		{
			$this->setRedirect(
				JRoute::_($this->page->link())
			);
			return;
		}

		// Load the revision, approve it, and save
		$revision = new WikiModelRevision($id);
		$revision->set('approved', 1);
		if (!$revision->store()) 
		{
			JError::raiseWarning(500, $revision->getError());
			return;
		}

		// Get the most recent revision and compare to the set "current" version
		$this->page->revisions('list', array(), true)->last();
		if ($this->page->revisions()->current()->get('id') == $revision->get('id'))
		{
			// The newly approved revision is now the most current
			// So, we need to update the page's version_id
			$this->page->set('version_id', $this->page->revisions()->current()->get('id'));
			$this->page->store(false, 'revision_approved');
		}
		else
		{
			$this->page->log('revision_approved');
		}

		// Log the action
		/*$log = new WikiLog($this->database);
		$log->pid       = $this->page->id;
		$log->uid       = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action    = 'revision_approved';
		$log->actorid   = $this->juser->get('id');
		if (!$log->store()) 
		{
			$this->setError($log->getError());
		}*/

		$this->setRedirect(
			JRoute::_($this->page->link())
		);
	}
}

