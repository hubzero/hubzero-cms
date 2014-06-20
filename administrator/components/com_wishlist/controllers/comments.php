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
 * Cotnroller class for wishes
 */
class WishlistControllerComments extends \Hubzero\Component\AdminController
{
	/**
	 * Display a list of entries
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		// Get filters
		$this->view->filters = array();
		$this->view->filters['search']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		));
		$this->view->filters['wish']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.wish',
			'wish',
			0,
			'int'
		));
		if (!$this->view->filters['wish'])
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option,
				JText::_('Missing wish ID'),
				'error'
			);
			return;
		}
		// Get sorting variables
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
		$this->view->filters['sortby'] = $this->view->filters['sort'];

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

		$this->view->wish = new Wish($this->database);
		$this->view->wish->load($this->view->filters['wish']);

		$this->view->wishlist = new Wishlist($this->database);
		$this->view->wishlist->load($this->view->wish->wishlist);

		$obj = new \Hubzero\Item\Comment($this->database);
		//$obj->getResults(array('id' => $wishid, 'category' => 'wish'));

		// Get record count
		//$this->view->total = $obj->get_count($this->view->filters['wishlist'], $this->view->filters, true);

		// Get records
		//$comments1 = $obj->get_wishes($this->view->filters['wishlist'], $this->view->filters, true);
		$filters = array(
			'item_type' => 'wish',
			'parent'    => 0,
			'search'    => $this->view->filters['search']
		);
		if ($this->view->filters['wish'] > 0)
		{
			$filters['item_id'] = $this->view->filters['wish'];
		}
		$comments1 = $obj->find($filters, 1);
		$comments = array();
		if (count($comments1) > 0)
		{
			$pre    = '<span class="treenode">&#8970;</span>&nbsp;';
			$spacer = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

			foreach ($comments1 as $comment1)
			{
				$comment1->prfx = '';
				$comment1->wish = $this->view->filters['wish'];
				$comments[] = $comment1;

				$comments2 = $obj->find(array('item_id' => $comment1->item_id, 'item_type' => 'wish', 'parent' => $comment1->id), 1);
				if (count($comments2) > 0)
				{
					foreach ($comments2 as $comment2)
					{
						$comment2->prfx = $spacer . $pre;
						$comment2->wish = $this->view->filters['wish'];
						$comments[] = $comment2;

						$comments3 = $obj->find(array('item_id' => $comment2->item_id, 'item_type' => 'wish', 'parent' => $comment2->id), 1);
						if (count($comments3) > 0)
						{
							foreach ($comments3 as $comment3)
							{
								$comment3->prfx = $spacer . $spacer . $pre;
								$comment3->wish = $this->view->filters['wish'];
								$comments[] = $comment3;
							}
						}
					}
				}
			}
		}
		$this->view->total = count($comments);
		$this->view->rows  = $comments;

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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
		$this->editTask();
	}

	/**
	 * Edit a category
	 *
	 * @return     void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		$this->view->wish = JRequest::getInt('wish', 0);

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
			$this->view->row = new \Hubzero\Item\Comment($this->database);
			$this->view->row->load($id);
		}

		if (!$this->view->row->id)
		{
			$this->view->row->item_type  = 'wish';
			$this->view->row->item_id    = $this->view->wish;
			$this->view->row->created    = JFactory::getDate()->toSql();
			$this->view->row->created_by = $this->juser->get('id');
		}

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save an entry and come back to the edit form
	 *
	 * @return     void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save an entry
	 *
	 * @param      integer $redirect Redirect the page after saving
	 * @return     void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = new \Hubzero\Item\Comment($this->database);
		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$row->anonymous = (isset($fields['anonymous']) && $fields['anonymous']) ? 1 : 0;

		// Check content
		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($redirect)
		{
			// Redirect
			$this->setRedirect(
				'index.php?option='.$this->_option . '&controller=' . $this->_controller . '&wish=' . $row->item_id,
				JText::_('COM_WISHLIST_COMMENT_SAVED')
			);
			return;
		}

		$this->editTask($row);
	}

	/**
	 * Remove one or more entries
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$wish = JRequest::getInt('wish', 0);
		$ids  = JRequest::getVar('id', array());

		// Do we have any IDs?
		if (count($ids) > 0)
		{
			$tbl = new \Hubzero\Item\Comment($this->database);

			// Loop through each ID
			foreach ($ids as $id)
			{
				$id = intval($id);

				if (!$tbl->delete($id))
				{
					JError::raiseError(500, $tbl->getError());
					return;
				}
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&wish=' . $wish,
			JText::sprintf('COM_WISHLIST_ITEMS_REMOVED', count($ids))
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
	 * Set the state of an entry
	 *
	 * @param      integer $state State to set
	 * @return     void
	 */
	public function stateTask($state=0)
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$wish = JRequest::getInt('wish', 0);
		$ids = JRequest::getVar('id', array());

		// Check for an ID
		if (count($ids) < 1)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($wish ? '&wish=' . $wish : ''),
				($state == 1 ? JText::_('COM_WISHLIST_SELECT_PUBLISH') : JText::_('COM_WISHLIST_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating a category
			$row = new \Hubzero\Item\Comment($this->database);
			$row->load($id);
			$row->state = $state;
			$row->store();
		}

		// Set message
		switch ($state)
		{
			case '-1':
				$message = JText::sprintf('COM_WISHLIST_ARCHIVED', count($ids));
			break;
			case '1':
				$message = JText::sprintf('COM_WISHLIST_PUBLISHED', count($ids));
			break;
			case '0':
				$message = JText::sprintf('COM_WISHLIST_UNPUBLISHED', count($ids));
			break;
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($wish ? '&wish=' . $wish : ''),
			$message
		);
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return     void
	 */
	public function publicizeTask()
	{
		$this->anonTask(0);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return     void
	 */
	public function anonymizeTask()
	{
		$this->anonTask(1);
	}

	/**
	 * Set the state of an entry
	 *
	 * @param      integer $state State to set
	 * @return     void
	 */
	public function anonTask($state=0)
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$wish = JRequest::getInt('wish', 0);
		$ids = JRequest::getVar('id', array());

		// Check for an ID
		if (count($ids) < 1)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($wish ? '&wish=' . $wish : '')
			);
			return;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating a category
			$row = new \Hubzero\Item\Comment($this->database);
			$row->load($id);
			$row->anonymous = $state;
			$row->store();
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($wish ? '&wish=' . $wish : '')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		$wish = JRequest::getInt('wish', 0);
		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&wish=' . $wish
		);
	}
}

