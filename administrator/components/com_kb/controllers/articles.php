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
 * Short description for 'KbController'
 * 
 * Long description (if any) ...
 */
class KbControllerArticles extends Hubzero_Controller
{
	/**
	 * Short description for 'articles'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();
		
		// Get filters
		$this->view->filters['orphans']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.orphans', 
			'orphans', 
			0, 
			'int'
		);
		$this->view->filters['id']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.id', 
			'id', 
			0, 
			'int'
		);
		$this->view->filters['cid']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.cid', 
			'cid', 
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

		$a = new KbArticle($this->database);

		// Get record count
		$this->view->total = $a->getArticlesCount($this->view->filters);

		// Get records
		$this->view->rows = $a->getArticlesAll($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Get the sections
		$row = new KbCategory($this->database);
		$this->view->sections = $row->getAllSections();

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}
	
	/**
	 * Create a new category
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}

	/**
	 * Short description for 'editfaq'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
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
			$ids = JRequest::getVar('id', array(0));
			if (is_array($ids) && !empty($ids)) 
			{
				$id = $ids[0];
			}
			
			// Load category
			$this->view->row = new KbArticle($this->database);
			$this->view->row->load($id);
		}

		// Fail if checked out not by 'me'
		if ($this->view->row->checked_out && $this->view->row->checked_out != $this->juser->get('id')) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_KB_CHECKED_OUT'),
				'warning'
			);
			return;
		}

		if ($this->view->row->id) 
		{
			// Editing existing
			$this->view->row->checkout($this->juser->get('id'));
		}
		else 
		{
			$this->view->row->created_by = $this->juser->get('id');
			$this->view->row->created = date('Y-m-d H:i:s', time());
		}
		
		// Get name of creator
		$this->view->creator = JUser::getInstance($this->view->row->created_by);

		$this->view->params = new JParameter(
			$this->view->row->params, 
			JPATH_COMPONENT . DS . 'kb.xml'
		);

		// Get Tags
		$st = new KbTags($this->database);
		$this->view->tags = $st->get_tag_string($this->view->row->id, 0, 0, NULL, 0, 1);

		$c = new KbCategory($this->database);

		// Get the sections
		$this->view->sections = $c->getAllSections();

		// Get the sections
		$this->view->categories = $c->getAllCategories();

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Short description for 'savefaq'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = new KbArticle($this->database);
		if (!$row->bind($fields)) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($row);
			return;
		}

		if (!isset($fields['access']))
		{
			$row->access = JRequest::getInt('access', 0, 'post');
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
			$this->addComponentMessage($row->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($row);
			return;
		}

		$row->checkin();

		// Save the tags
		$tags = JRequest::getVar('tags', '', 'post');
		$st = new KbTags($this->database);
		$st->tag_object($this->juser->get('id'), $row->id, $tags, 0, true);
		
		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_KB_ARTICLE_SAVED')
		);
	}

	/**
	 * Short description for 'deletefaq'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Incoming
		$cid = JRequest::getInt('cid', 0);
		$ids = JRequest::getVar('id', array(0));
		if (!is_array($ids)) 
		{
			$ids = array(0);
		}

		if (count($ids) > 0) 
		{
			// Create a category object
			$article = new KbArticle($this->database);

			foreach ($ids as $id)
			{
				// Delete the category
				$article->delete(intval($id));
			}
		}
		
		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Articles Successfully Removed')
		);
	}

	/**
	 * Set the state of an article to 'public'
	 * 
	 * @return     void
	 */
	public function accesspublicTask()
	{
		return $this->accessTask(0);
	}
	
	/**
	 * Set the state of an article to 'registered'
	 * 
	 * @return     void
	 */
	public function accessregisteredTask()
	{
		return $this->accessTask(1);
	}
	
	/**
	 * Set the state of an article to 'special'
	 * 
	 * @return     void
	 */
	public function accessspecialTask()
	{
		return $this->accessTask(2);
	}

	/**
	 * Set the state of an article
	 * 
	 * @param      integer $access Access level to set
	 * @return     void
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
		$row = new KbArticle($this->database);
		$row->load($id);
		$row->access = $access;
		
		// Check and store the changes
		if (!$row->check()) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$row->getError(),
				'error'
			);
			return;
		}
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
	 * @return     void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}
	
	/**
	 * Calls stateTask to unpublish entries
	 * 
	 * @return     void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Sets the state of one or more entries
	 * 
	 * @param      integer The state to set entries to
	 * @return     void
	 */
	public function stateTask($state=0)
	{
		// Incoming
		$cid = JRequest::getInt('cid', 0);
		$ids = JRequest::getVar('id', array(0));
		if (!is_array($ids)) 
		{
			$ids = array(0);
		}

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
			$row = new KbArticle($this->database);
			$row->load($id);
			$row->state = $state;
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
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($cid ? '&id=' . $cid : ''),
			$message
		);
	}

	/**
	 * Cancels a task and redirects to listing
	 * 
	 * @return     void
	 */
	public function cancelTask()
	{
		$filters = JRequest::getVar('filters', array());
		
		if (isset($filters['id']) && $filters['id']) 
		{
			// Bind the posted data to the article object and check it in
			$article = new KbArticle($this->database);
			$article->load(intval($filters['id']));
			$article->checkin();
		}
		
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Short description for 'resethits'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
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
		$article = new KbArticle($this->database);
		$article->load($id);
		$article->hits = 0;
		if (!$article->check()) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$article->getError(),
				'error'
			);
			return;
		}
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
	 * Short description for 'resetvotes'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
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
		$article = new KbArticle($this->database);
		$article->load($id);
		$article->helpful = 0;
		$article->nothelpful = 0;
		if (!$article->check()) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$article->getError(),
				'error'
			);
			return;
		}
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
		$helpful = new KbVote($this->database);
		$helpful->deleteVote($id);

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}

