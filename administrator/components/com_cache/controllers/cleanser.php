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

namespace Components\Cache\Controllers;

use Components\Cache\Helpers\Cache as Helper;
use Components\Cache\Models\Cache as Handler;
use Hubzero\Component\AdminController;
use Exception;
use JRequest;
use JSession;
use JText;

/**
 * Cache Controller
 */
class Cleanser extends AdminController
{
	/**
	 * Determine a task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->model = new Handler();

		parent::execute();
	}

	/**
	 * Display
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		require_once JPATH_COMPONENT . '/helpers/cache.php';

		// Set the default view name and format from the Request.
		$vName = JRequest::getCmd('view', 'cache');

		// Get and render the view.
		switch ($vName)
		{
			case 'purge':
			break;

			case 'cache':
			default:
				$this->view->model      = $this->model;
				$this->view->data       = $this->model->getData();
				$this->view->client     = $this->model->getClient();
				$this->view->pagination = $this->model->getPagination();
				$this->view->state      = $this->model->getState();

				// Check for errors.
				if (count($errors = $this->model->getErrors()))
				{
					throw new Exception(implode("\n", $errors), 500);
				}
			break;
		}

		Helper::addSubmenu($vName);

		$this->view
			->setName($vName)
			->setLayout('default')
			->display();
	}

	/**
	 * Delete
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JInvalid_Token'));

		$cid = JRequest::getVar('cid', array(), 'post', 'array');

		if (empty($cid))
		{
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else
		{
			$this->model->cleanlist($cid);
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&client=' . $this->model->getClient()->id
		);
	}

	/**
	 * Purge
	 *
	 * @return  void
	 */
	public function purgeTask()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JInvalid_Token'));

		$ret = $this->model->purge();

		$msg = JText::_('COM_CACHE_EXPIRED_ITEMS_HAVE_BEEN_PURGED');
		$msgType = 'message';

		if ($ret === false)
		{
			$msg = JText::_('Error purging expired items');
			$msgType = 'error';
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&view=purge',
			$msg,
			$msgType
		);
	}
}
