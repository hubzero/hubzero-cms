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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Groups controller class
 */
class GroupsControllerCategories extends \Hubzero\Component\AdminController
{
	/**
	 * Override Execute Method
	 *
	 * @return 	void
	 */
	public function execute()
	{
		// Incoming
		$this->gid = JRequest::getVar('gid', '');

		// Ensure we have a group ID
		if (!$this->gid)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=manage',
				JText::_('COM_GROUPS_MISSING_ID'),
				'error'
			);
			return;
		}

		$this->group = \Hubzero\User\Group::getInstance( $this->gid );

		parent::execute();
	}

	/**
	 * Display Page Categories
	 *
	 * @return void
	 */
	public function displayTask()
	{
		// get page categories
		$categoryArchive = new GroupsModelPageCategoryArchive();
		$this->view->categories = $categoryArchive->categories('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'orderby'   => 'title'
		));

		// pass group to view
		$this->view->group = $this->group;

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
	 * Add Page Category
	 *
	 * @return void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit Page Category
	 *
	 * @return void
	 */
	public function editTask()
	{
		//set to edit layout
		$this->view->setLayout('edit');

		// get request vars
		$ids = JRequest::getVar('id', array());
		$id  = (isset($ids[0])) ? $ids[0] : null;

		// get the category object
		$this->view->category = new GroupsModelPageCategory( $id );

		// are we passing a category object
		if ($this->category)
		{
			$this->view->category = $this->category;
		}

		// pass group to view
		$this->view->group = $this->group;

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
	 * Save Page Category
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// get request vars
		$category = JRequest::getVar('category', array(), 'post');

		// add group id to category
		$category['gidNumber'] = $this->group->get('gidNumber');

		// load category object
		$this->category = new GroupsModelPageCategory( $category['id'] );

		// bind to our new results
		if (!$this->category->bind($category))
		{
			$this->setNotification($this->category->getError(), 'error');
			$this->editTask();
			return;
		}

		// Store new content
		if (!$this->category->store(true))
		{
			$this->setNotification($this->category->getError(), 'error');
			$this->editTask();
			return;
		}

		// log change
		GroupsModelLog::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => (isset($category['id']) && $category['id'] != '') ? 'group_pagecategory_updated' : 'group_pagecategory_created',
			'comments'  => array(
				'id'    => $this->category->get('id'),
				'title' => $this->category->get('title'),
				'color' => $this->category->get('color')
			)
		));

		//inform user & redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
			JText::_('COM_GROUPS_PAGES_CATEGORY_SAVED'),
			'passed'
		);
	}

	/**
	 * Delete Page Category
	 *
	 * @return void
	 */
	public function deleteTask()
	{
		// get request vars
		$ids = JRequest::getVar('id', array());
		$deleted = array();

		// delete each category
		foreach ($ids as $categoryid)
		{
			// load category object
			$category = new GroupsModelPageCategory( $categoryid );

			// make sure this is our groups cat
			if ($category->get('gidNumber') != $this->group->get('gidNumber'))
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
					JText::_('COM_GROUPS_PAGES_CATEGORY_DELETE_FAILED'),
					'error'
				);
				return;
			}

			// delete row
			if (!$category->delete())
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
					$category->getError(),
					'error'
				);
				return;
			}
			$deleted[] = $category->get('id');
		}

		// log change
		GroupsModelLog::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_pagecategory_deleted',
			'comments'  => $deleted
		));

		//inform user & redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
			JText::_('COM_GROUPS_PAGES_CATEGORY_DELETE_SUCCESS'),
			'passed'
		);
	}

	/**
	 * Cancel a group page task
	 *
	 * @return void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . JRequest::getVar('gid', '')
		);
	}

	/**
	 * Manage group
	 *
	 * @return void
	 */
	public function manageTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=manage&task=edit&id[]=' . JRequest::getVar('gid', '')
		);
	}
}