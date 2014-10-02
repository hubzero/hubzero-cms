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
 * Manage resource types
 */
class ResourcesControllerLicenses extends \Hubzero\Component\AdminController
{
	/**
	 * List resource types
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['search']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		));
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'ordering'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));

		// Instantiate an object
		$rt = new ResourcesLicense($this->database);

		// Get a record count
		$this->view->total = $rt->getCount($this->view->filters);

		// Get records
		$this->view->rows = $rt->getRecords($this->view->filters);

		// initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		if (!$this->view->total)
		{
			$this->database->setQuery("INSERT INTO `#__resource_licenses` (`id`, `name`, `text`, `title`, `ordering`, `apps_only`, `main`, `icon`, `url`, `agreement`, `info`)
			VALUES
				(1,'cc25-by-nc-sa','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\nto Remix — to adapt the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\nNoncommercial — You may not use this work for commercial purposes.\r\n\r\nShare Alike — If you alter, transform, or build upon this work, you may distribute the resulting work only under the same or similar license to this one.\r\n\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\n- The author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.\r\n\r\nNotice — For any reuse or distribution, you must make clear to others the license terms of this work. The best way to do this is with a link to this web page.','Creative Commons BY-NC-SA 2.5',6,0,NULL,NULL,'http://creativecommons.org/licenses/by-nc-sa/2.5/',0,NULL),
				(2,'cc30-by-nc-sa','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\nto Remix — to adapt the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\n\r\nNoncommercial — You may not use this work for commercial purposes.\r\n\r\nShare Alike — If you alter, transform, or build upon this work, you may distribute the resulting work only under the same or similar license to this one.\r\n\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\n- The author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.','Creative Commons BY-NC-SA 3.0',7,0,NULL,NULL,'http://creativecommons.org/licenses/by-nc-sa/3.0/',0,NULL),
				(3,'cc','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\nto Remix — to adapt the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\nNoncommercial — You may not use this work for commercial purposes.\r\n\r\nShare Alike — If you alter, transform, or build upon this work, you may distribute the resulting work only under the same or similar license to this one.\r\n\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\n- The author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.\r\n\r\nNotice — For any reuse or distribution, you must make clear to others the license terms of this work. The best way to do this is with a link to this web page.','Creative Commons',1,0,NULL,NULL,'http://creativecommons.org/licenses/by-nc-sa/2.5/',0,NULL),
				(4,'cc30-by-nc-nd','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\n\r\nNoncommercial — You may not use this work for commercial purposes.\r\n\r\nNo Derivative Works — You may not alter, transform, or build upon this work.\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\n- The author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.','Creative Commons BY-NC-ND 3.0',8,0,NULL,NULL,'http://creativecommons.org/licenses/by-nc-nd/3.0/',0,NULL),
				(5,'cc30-by','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\nto Remix — to adapt the work\r\nto make commercial use of the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\n- The author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.','Creative Commons BY 3.0',2,0,NULL,NULL,'http://creativecommons.org/licenses/by/3.0/',0,NULL),
				(6,'cc30-by-sa','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\nto Remix — to adapt the work\r\nto make commercial use of the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\n\r\nShare Alike — If you alter, transform, or build upon this work, you may distribute the resulting work only under the same or similar license to this one.\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\n- The author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.','Creative Commons BY-SA 3.0',3,0,NULL,NULL,'http://creativecommons.org/licenses/by-sa/3.0/',0,NULL),
				(7,'cc30-by-nd','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\nto make commercial use of the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\n\r\nNo Derivative Works — You may not alter, transform, or build upon this work.\r\n\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\nThe author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.','Creative Commons BY-ND 3.0',4,0,NULL,NULL,'http://creativecommons.org/licenses/by-nd/3.0/',0,NULL),
				(8,'cc30-by-nc','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\nto Remix — to adapt the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\n\r\nNoncommercial — You may not use this work for commercial purposes.\r\n\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\n- The author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.','Creative Commons BY-NC 3.0',5,0,NULL,NULL,'http://creativecommons.org/licenses/by-nc/3.0/',0,NULL);");
			if (!$this->database->query())
			{
				echo $this->database->getError();
			}
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
	 * Add a new type
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a record
	 *
	 * @return     void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		if ($row)
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming (expecting an array)
			$id = JRequest::getVar('id', array(0));
			if (is_array($id))
			{
				$id = (!empty($id) ? $id[0] : 0);
			}

			// Load the object
			$this->view->row = new ResourcesLicense($this->database);
			$this->view->row->load($id);
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
	 * Save a record
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = new ResourcesLicense($this->database);
		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!$row->id)
		{
			$row->ordering = $row->getNextOrder();
		}

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

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_RESOURCES_ITEM_SAVED')
		);
	}

	/**
	 * Remove one or more types
	 *
	 * @return     void Redirects back to main listing
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming (expecting an array)
		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Ensure we have an ID to work with
		if (empty($ids))
		{
			// Redirect with error message
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_RESOURCES_NO_ITEM_SELECTED'),
				'error'
			);
			return;
		}

		$rt = new ResourcesLicense($this->database);

		foreach ($ids as $id)
		{
			// Delete the type
			$rt->delete($id);
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::sprintf('COM_RESOURCES_ITEMS_REMOVED', count($ids))
		);
	}

	/**
	 * Move an item down up the ordering
	 *
	 * @return     void
	 */
	public function orderupTask()
	{
		$this->reorderTask(-1);
	}

	/**
	 * Move an item down in the ordering
	 *
	 * @return     void
	 */
	public function orderdownTask()
	{
		$this->reorderTask(1);
	}

	/**
	 * Reorders a resource child
	 * Redirects to parent resource's children listing
	 *
	 * @return     void
	 */
	public function reorderTask($dir = 0)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getVar('id', array(0), '', 'array');

		// Load row
		$row = new ResourcesLicense($this->database);
		$row->load((int) $id[0]);

		// Update order
		$row->move($dir);

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect('index.php?option=' . $this->_option . '&controller=' . $this->_controller);
	}
}
