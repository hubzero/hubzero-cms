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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Checkin\Controllers;

use Hubzero\Component\AdminController;
use Exception;
use JSubMenuHelper;
use JSession;
use JRequest;
use JText;

/**
 * Checkin Controller
 */
class Checkin extends AdminController
{
	/**
	 * Determine a task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->model = new \Components\Checkin\Models\Checkin();

		parent::execute();
	}

	/**
	 * Display admin control panel
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Load the submenu.
		$this->addSubmenu(JRequest::getWord('option', 'com_checkin'));

		$this->view->items      = $this->model->getItems();
		$this->view->pagination = $this->model->getPagination();
		$this->view->state      = $this->model->getState();

		// Check for errors.
		if (count($errors = $this->model->getErrors()))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		$this->view
			->setLayout('default')
			->display();
	}

	/**
	 * Checkin items
	 *
	 * @return  void
	 */
	public function checkinTask()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Initialise variables.
		$ids = JRequest::getVar('cid', array(), '', 'array');

		$msg = '';

		if (empty($ids))
		{
			throw new Exception(JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'), 500);
		}
		else
		{
			// Checked in the items.
			$msg = JText::plural('COM_CHECKIN_N_ITEMS_CHECKED_IN', $this->model->checkin($ids));
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			($msg ?: null)
		);
	}

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 * @return  void
	 */
	protected function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('JGLOBAL_SUBMENU_CHECKIN'),
			'index.php?option=com_checkin',
			$vName == 'com_checkin'
		);
		JSubMenuHelper::addEntry(
			JText::_('JGLOBAL_SUBMENU_CLEAR_CACHE'),
			'index.php?option=com_cache',
			$vName == 'cache'
		);
		JSubMenuHelper::addEntry(
			JText::_('JGLOBAL_SUBMENU_PURGE_EXPIRED_CACHE'),
			'index.php?option=com_cache&view=purge',
			$vName == 'purge'
		);
	}
}
