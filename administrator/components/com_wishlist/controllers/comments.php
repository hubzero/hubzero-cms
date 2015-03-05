<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Wishlist\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Item\Comment;
use Components\Wishlist\Tables\Wishlist;
use Components\Wishlist\Tables\Wish;
use Exception;

/**
 * Cotnroller class for wishes
 */
class Comments extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');
		$this->registerTask('publicize', 'anon');
		$this->registerTask('anonymize', 'anon');

		parent::execute();
	}

	/**
	 * Display a list of entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = \JFactory::getConfig();
		$app = \JFactory::getApplication();

		// Get filters
		$this->view->filters = array(
			'search' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'wish' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.wish',
				'wish',
				0,
				'int'
			),
			// Get sorting variables
			'sort' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			// Get paging variables
			'limit' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				$config->getValue('config.list_limit'),
				'int'
			),
			'start' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			)
		);
		$this->view->filters['sortby'] = $this->view->filters['sort'];
		if (!$this->view->filters['wish'])
		{
			$this->setRedirect(
				\JRoute::_('index.php?option=' . $this->_option, false),
				\JText::_('Missing wish ID'),
				'error'
			);
			return;
		}

		$this->view->wish = new Wish($this->database);
		$this->view->wish->load($this->view->filters['wish']);

		$this->view->wishlist = new Wishlist($this->database);
		$this->view->wishlist->load($this->view->wish->wishlist);

		$obj = new Comment($this->database);

		// Get records
		//$comments1 = $obj->get_wishes($this->view->filters['wishlist'], $this->view->filters, true);

		// add the appropriate filters and apply them to the Item::Comment

		$filters = array(
			'item_type' => 'wish',
			'parent'    => 0,
			'search'    => $this->view->filters['search']
		);
		if ($this->view->filters['wish'] > 0)
		{
			$filters['item_id'] = $this->view->filters['wish'];
		}
		if (isset($this->view->filters['sort']))
		{
			$filter['sort'] = $this->view->filters['sort'];
			if (isset($this->view->filters['sort_Dir']))
			{
				$filters['sort_Dir'] = $this->view->filters['sort_Dir'];
			}
		}
		if (isset($this->view->filters['limit']))
		{
			$filters['limit'] = $this->view->filters['limit'] ;
		}
		if (isset($this->view->filters['start']))
		{
			$filters['start'] = $this->view->filters['start'];
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

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit a category
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		\JRequest::setVar('hidemainmenu', 1);

		$this->view->wish = \JRequest::getInt('wish', 0);

		if (!is_object($row))
		{
			// Incoming
			$id = \JRequest::getVar('id', array(0));

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load category
			$row = new Comment($this->database);
			$row->load($id);
		}

		$this->view->row = $row;

		if (!$this->view->row->id)
		{
			$this->view->row->item_type  = 'wish';
			$this->view->row->item_id    = $this->view->wish;
			$this->view->row->created    = \JFactory::getDate()->toSql();
			$this->view->row->created_by = $this->juser->get('id');
		}

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
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		\JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = \JRequest::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = new Comment($this->database);
		if (!$row->bind($fields))
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$row->anonymous = (isset($fields['anonymous']) && $fields['anonymous']) ? 1 : 0;

		// Check content
		if (!$row->check())
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->setRedirect(
			\JRoute::_('index.php?option='.$this->_option . '&controller=' . $this->_controller . '&wish=' . $row->item_id, false),
			\JText::_('COM_WISHLIST_COMMENT_SAVED')
		);
	}

	/**
	 * Remove one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		\JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$wish = \JRequest::getInt('wish', 0);
		$ids  = \JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (count($ids) > 0)
		{
			$tbl = new Comment($this->database);

			// Loop through each ID
			foreach ($ids as $id)
			{
				$id = intval($id);

				if (!$tbl->delete($id))
				{
					throw new Exception($tbl->getError(), 500);
				}
			}
		}

		// Redirect
		$this->setRedirect(
			\JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&wish=' . $wish, false),
			\JText::sprintf('COM_WISHLIST_ITEMS_REMOVED', count($ids))
		);
	}

	/**
	 * Set the state of an entry
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		\JRequest::checkToken('get') or \JRequest::checkToken() or jexit('Invalid Token');

		$state = $this->getTask() == 'publish' ? 1 : 0;

		// Incoming
		$wish = \JRequest::getInt('wish', 0);
		$ids  = \JRequest::getVar('id', array());
		$ids  = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			$this->setRedirect(
				\JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($wish ? '&wish=' . $wish : ''), false),
				($state == 1 ? \JText::_('COM_WISHLIST_SELECT_PUBLISH') : \JText::_('COM_WISHLIST_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating a category
			$row = new Comment($this->database);
			$row->load($id);
			$row->state = $state;
			$row->store();
		}

		// Set message
		switch ($state)
		{
			case '-1':
				$message = \JText::sprintf('COM_WISHLIST_ARCHIVED', count($ids));
			break;
			case '1':
				$message = \JText::sprintf('COM_WISHLIST_PUBLISHED', count($ids));
			break;
			case '0':
				$message = \JText::sprintf('COM_WISHLIST_UNPUBLISHED', count($ids));
			break;
		}

		// Set the redirect
		$this->setRedirect(
			\JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($wish ? '&wish=' . $wish : ''), false),
			$message
		);
	}

	/**
	 * Set the anonymous state of an entry
	 *
	 * @return  void
	 */
	public function anonTask()
	{
		// Check for request forgeries
		\JRequest::checkToken('get') or \JRequest::checkToken() or jexit('Invalid Token');

		$state = $this->getTask() == 'anonymize' ? 1 : 0;

		// Incoming
		$wish = \JRequest::getInt('wish', 0);
		$ids  = \JRequest::getVar('id', array());
		$ids  = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			$this->setRedirect(
				\JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($wish ? '&wish=' . $wish : ''), false)
			);
			return;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating a category
			$row = new Comment($this->database);
			$row->load($id);
			$row->anonymous = $state;
			$row->store();
		}

		// Set the redirect
		$this->setRedirect(
			\JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($wish ? '&wish=' . $wish : ''), false)
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$wish = \JRequest::getInt('wish', 0);

		// Set the redirect
		$this->setRedirect(
			\JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&wish=' . $wish, false)
		);
	}
}

