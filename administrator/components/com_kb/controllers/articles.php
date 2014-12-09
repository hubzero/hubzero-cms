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
 * Controller class for knowledge base articles
 */
class KbControllerArticles extends \Hubzero\Component\AdminController
{
	/**
	 * Display a list of articles
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		// Get filters
		$this->view->filters['orphans']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.orphans',
			'orphans',
			0,
			'int'
		);
		$this->view->filters['category']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.category',
			'category',
			0,
			'int'
		);
		$this->view->filters['section']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.section',
			'section',
			0,
			'int'
		);
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'title'
		));
		$this->view->filters['sort_Dir']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));
		$this->view->filters['filterby']       = $this->view->filters['sort'] . ' ' . $this->view->filters['sort_Dir'];

		// Get paging variables
		$this->view->filters['limit']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['state'] = -1;
		$this->view->filters['access'] = -1;

		$a = new KbModelArchive();

		// Get record count
		$this->view->total = $a->articles('count', $this->view->filters);

		// Get records
		$this->view->rows  = $a->articles('list', $this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Get the sections
		$this->view->sections = $a->categories('list', array(
			'access' => -1,
			'state' => -1,
			'empty' => true
		));
		if ($this->view->filters['section'] && $this->view->filters['section'] >= 0)
		{
			$this->view->categories = $a->categories('list', array(
				'section' => $this->view->filters['section'],
				'access' => -1,
				'state' => -1,
				'empty' => true
			), true);
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new article
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming
			$id = JRequest::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load category
			$this->view->row = new KbModelArticle($id);
		}

		// Fail if checked out not by 'me'
		if ($this->view->row->get('checked_out') && $this->view->row->get('checked_out')  != $this->juser->get('id'))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_KB_CHECKED_OUT'),
				'warning'
			);
			return;
		}

		if (!$this->view->row->exists())
		{
			$this->view->row->set('created_by', $this->juser->get('id'));
			$this->view->row->set('created', JFactory::getDate()->toSql());
		}

		$this->view->params = new JParameter(
			$this->view->row->get('params'),
			JPATH_COMPONENT . DS . 'kb.xml'
		);

		$c = new KbModelArchive();

		// Get the sections
		$this->view->sections = $c->categories('list', array('section' => 0, 'empty' => 1));

		/*
		$m = new KbModelAdminArticle();
		$this->view->form = $m->getForm();
		*/

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry and return to edit form
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save an entry
	 *
	 * @param   boolean  $redirect  Redirect after saving?
	 * @return  void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post', 'none', 2);

		// Initiate extended database class
		$row = new KbModelArticle($fields['id']);
		if (!$row->bind($fields))
		{
			JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!isset($fields['access']))
		{
			$row->set('access', JRequest::getInt('access', 0, 'post'));
		}

		// Get parameters
		$params = JRequest::getVar('params', array(), 'post');

		$p = $row->param();

		if (is_array($params))
		{
			$txt = array();
			foreach ($params as $k => $v)
			{
				$p->set($k, $v);
			}
			$row->set('params', $p->toString());
		}

		// Store new content
		if (!$row->store(true))
		{
			JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		//$row->checkin();

		// Save the tags
		$row->tag(
			JRequest::getVar('tags', '', 'post'),
			$this->juser->get('id')
		);

		if ($redirect)
		{
			// Set the redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_KB_ARTICLE_SAVED')
			);
			return;
		}

		$this->editTask($row);
	}

	/**
	 * Remove one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$cid = JRequest::getInt('cid', 0);

		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) > 0)
		{
			foreach ($ids as $id)
			{
				// Delete the category
				$article = new KbModelArticle(intval($id));
				$article->delete();
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::sprintf('COM_KB_ITEMS_REMOVED', count($ids))
		);
	}

	/**
	 * Set the access level of an article to 'public'
	 *
	 * @return  void
	 */
	public function accesspublicTask()
	{
		return $this->accessTask(0);
	}

	/**
	 * Set the access level of an article to 'registered'
	 *
	 * @return  void
	 */
	public function accessregisteredTask()
	{
		return $this->accessTask(1);
	}

	/**
	 * Set the access level of an article to 'special'
	 *
	 * @return  void
	 */
	public function accessspecialTask()
	{
		return $this->accessTask(2);
	}

	/**
	 * Set the access level of an article
	 *
	 * @param   integer  $access  Access level to set
	 * @return  void
	 */
	public function accessTask($access=0)
	{
		// Incoming
		$id = JRequest::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_KB_NO_ID'),
				'error'
			);
			return;
		}

		// Load the article
		$row = new KbModelArticle($id);
		$row->set('access', $access);

		// Check and store the changes
		if (!$row->store())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$row->getError(),
				'error'
			);
			return;
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return  void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return  void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @param   integer  $state  The state to set entries to
	 * @return  void
	 */
	public function stateTask($state=0)
	{
		// Incoming
		$cid = JRequest::getInt('cid', 0);

		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				($state == 1 ? JText::_('COM_KB_SELECT_PUBLISH') : JText::_('COM_KB_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating an article
			$row = new KbModelArticle(intval($id));
			$row->set('state', $state);
			$row->store();
		}

		// Set message
		switch ($state)
		{
			case '-1':
				$message = JText::sprintf('COM_KB_ARCHIVED', count($ids));
			break;
			case '1':
				$message = JText::sprintf('COM_KB_PUBLISHED', count($ids));
			break;
			case '0':
				$message = JText::sprintf('COM_KB_UNPUBLISHED', count($ids));
			break;
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($cid ? '&section=' . $cid : ''),
			$message
		);
	}

	/**
	 * Cancels a task and redirects to listing
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$filters = JRequest::getVar('filters', array());

		if (isset($filters['id']) && $filters['id'])
		{
			// Bind the posted data to the article object and check it in
			$article = new KbTableArticle($this->database);
			$article->load(intval($filters['id']));
			$article->checkin();
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Reset the hit count on an entry
	 *
	 * @return  void
	 */
	public function resethitsTask()
	{
		// Incoming
		$cid = JRequest::getInt('cid', 0);
		$id  = JRequest::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_KB_NO_ID'),
				'error'
			);
		}

		// Load and reset the article's hits
		$article = new KbModelArticle($id);
		$article->set('hits', 0);

		if (!$article->store())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$article->getError(),
				'error'
			);
			return;
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Reset the vote count on an entry
	 *
	 * @return  void
	 */
	public function resetvotesTask()
	{
		// Incoming
		$cid = JRequest::getInt('cid', 0);
		$id  = JRequest::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_KB_NO_ID'),
				'error'
			);
		}

		// Load and reset the article's ratings
		$article = new KbModelArticle($id);
		$article->set('helpful', 0);
		$article->set('nothelpful', 0);

		if (!$article->store())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$article->getError(),
				'error'
			);
			return;
		}

		// Delete all the entries associated with this article
		$helpful = new KbTableVote($this->database);
		$helpful->deleteVote($id);

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}

