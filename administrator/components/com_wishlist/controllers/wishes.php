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
class WishlistControllerWishes extends \Hubzero\Component\AdminController
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
		$this->view->filters['wishlist']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.wishlist',
			'wishlist',
			0,
			'int'
		));
		if (!$this->view->filters['wishlist'])
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=lists',
				JText::_('Missing list ID'),
				'error'
			);
			return;
		}
		$this->view->filters['filterby']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.filterby',
			'filterby',
			'all'
		));
		$this->view->filters['tag']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.tag',
			'tag',
			''
		));
		// Get sorting variables
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'subject'
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

		$this->view->wishlist = new Wishlist($this->database);
		$this->view->wishlist->load($this->view->filters['wishlist']);

		$obj = new Wish($this->database);

		// Get record count
		$this->view->total = $obj->get_count($this->view->filters['wishlist'], $this->view->filters, true);

		// Get records
		$this->view->rows = $obj->get_wishes($this->view->filters['wishlist'], $this->view->filters, true);

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

		$this->view->wishlist = JRequest::getInt('wishlist', 0);

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
			$this->view->row = new Wish($this->database);
			$this->view->row->load($id);
		}
		if (!$this->view->row->id)
		{
			$this->view->row->wishlist = $this->view->wishlist;
		}
		else if (!$this->view->wishlist)
		{
			$this->view->wishlist = $this->view->row->wishlist;
		}

		/*
		$m = new WishlistModelWish();
		$this->view->form = $m->getForm();
		*/

		$obj = new Wishlist($this->database);
		$filters = array();
		$filters['sort'] = 'title';
		$filters['sort_Dir'] = 'ASC';
		$this->view->lists = $obj->getRecords($filters);

		// who are list owners?
		$this->admingroup = $this->config->get('group', 'hubadmin');

		$objOwner = new WishlistOwner($this->database);
		$objG     = new WishlistOwnerGroup($this->database);
		//$owners   = $objOwner->get_owners($wishlist->id, $this->admingroup, $wishlist);

		$this->view->ownerassignees = array();
		$this->view->ownerassignees[-1] = array();
		$none = new stdClass;
		$none->id = '-1';
		$none->name = JText::_('COM_WISHLIST_SELECT');
		$this->view->ownerassignees[-1][] = $none;//JHTML::_('select.option', '-1', JText::_( 'Select Category' ), 'id', 'title');

		$this->view->assignees = null;

		if ($this->view->lists)
		{
			foreach ($this->view->lists as $k => $list)
			{
				if ($list->category == 'resource')
				{
					include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
					$list->resource = new ResourcesResource($this->database);
					$list->resource->load($list->referenceid);
				}
				$this->view->ownerassignees[$list->id] = array();

				$none = new stdClass;
				$none->id = '0';
				$none->name = JText::_('COM_WISHLIST_NONE');

				$this->view->ownerassignees[$list->id][] = $none;

				$owners = $objOwner->get_owners($list->id, $this->admingroup, $list);
				if (count($owners['individuals']) > 0)
				{
					$query = "SELECT a.id, a.name FROM `#__users` AS a WHERE a.block = '0' AND a.id IN (" . implode(',', $owners['individuals']) . ") ORDER BY a.name";
					$this->database->setQuery($query);

					$users = $this->database->loadObjectList();

					foreach ($users as $row2)
					{
						$this->view->ownerassignees[$list->id][] = $row2;//JHTML::_('select.option', $row2->id, $row2->name, 'id', 'title');
					}
					//$this->view->ownerassignees[$list->id] = $this->database->loadObjectList();

					if ($list->id == $this->view->row->wishlist)
					{
						$this->view->assignees = $this->view->ownerassignees[$list->id];
					}
				}
			}
		}

		//$wishlist->owners   = $owners['individuals'];
		//$wishlist->advisory = $owners['advisory'];
		//$wishlist->groups   = $owners['groups'];

		// Get the plan for this wish
		$objPlan = new WishlistPlan($this->database);
		$plan = $objPlan->getPlan($this->view->row->id);
		$this->view->plan = $plan ? $plan[0] : $objPlan;

		// Get tags on this wish
		include_once(JPATH_ROOT . DS . 'components' . DS . $this->_option . DS . 'helpers' . DS . 'tags.php');
		$tagging = new WishTags($this->database);
		$this->view->tags = $tagging->get_tag_string($this->view->row->id);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->messages = $this->getComponentMessage();

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
		$row = new Wish($this->database);
		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$row->anonymous = (isset($fields['anonymous']) && $fields['anonymous']) ? 1 : 0;
		$row->private   = (isset($fields['private']) && $fields['private']) ? 1 : 0;

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

		$plan = JRequest::getVar('plan', array(), 'post', 'none', 2);
		$plan['create_revision'] = isset($plan['create_revision']) ? $plan['create_revision'] : 0;
		$plan['wishid'] = ($plan['wishid'] ? $plan['wishid'] : $row->id);

		// Initiate extended database class
		$page = new WishlistPlan($this->database);
		if (!$fields['id'])
		{
			// New page - save it to the database
			$old = new WishlistPlan($this->database);
		}
		else
		{
			// Existing page - load it up
			$page->load($plan['id']);

			// Get the revision before changes
			$old = $page;
		}

		$page->bind($plan);

		if ($plan['create_revision'] && rtrim(stripslashes($old->pagetext)) != rtrim(stripslashes($page->pagetext)))
		{
			$page->version = $page->version + 1;
			$page->id = 0;
		}

		if ($page->pagetext)
		{
			$page->version = ($page->version ? $page->version : $page->version + 1);

			if (!$page->check())
			{
				$this->addComponentMessage($page->getError(), 'error');
				$this->editTask($row);
				return;
			}

			if (!$page->store())
			{
				$this->addComponentMessage($page->getError(), 'error');
				$this->editTask($row);
				return;
			}
		}

		if ($redirect)
		{
			// Redirect
			$this->setRedirect(
				'index.php?option='.$this->_option . '&controller=' . $this->_controller . '&wishlist=' . $row->wishlist,
				JText::_('COM_WISHLIST_WISH_SAVED')
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
		$wishlist = JRequest::getInt('wishlist', 0);

		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (count($ids) > 0)
		{
			$tbl = new Wish($this->database);

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
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&wishlist=' . $wishlist,
			JText::sprintf('COM_WISHLIST_ITEMS_REMOVED', count($ids))
		);
	}

	/**
	 * Set the access level of an article to 'public'
	 *
	 * @return     void
	 */
	public function accesspublicTask()
	{
		return $this->accessTask(0);
	}

	/**
	 * Set the access level of an article to 'registered'
	 *
	 * @return     void
	 */
	public function accessregisteredTask()
	{
		return $this->accessTask(1);
	}

	/**
	 * Set the access level of an article to 'special'
	 *
	 * @return     void
	 */
	public function accessspecialTask()
	{
		return $this->accessTask(2);
	}

	/**
	 * Set the access level of an article
	 *
	 * @param      integer $access Access level to set
	 * @return     void
	 */
	public function accessTask($access=0)
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_WISHLIST_NO_ID'),
				'error'
			);
			return;
		}

		// Load the article
		$row = new Wish($this->database);
		$row->load($id);
		$row->private = $access;

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
		$cid = JRequest::getInt('cid', 0);

		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				($state == 1 ? JText::_('COM_WISHLIST_SELECT_PUBLISH') : JText::_('COM_WISHLIST_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating a category
			$row = new Wish($this->database);
			$row->load($id);
			$row->status = $state;
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
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($cid ? '&id=' . $cid : ''),
			$message
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		$wishlist = JRequest::getInt('wishlist', 0);

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&wishlist=' . $wishlist
		);
	}
}

