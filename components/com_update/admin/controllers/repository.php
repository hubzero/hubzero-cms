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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Update\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Update\Helpers\Cli;

/**
 * Update repository controller class
 */
class Repository extends AdminController
{
	/**
	 * Display the repository details
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->total   = 0;
		$this->view->filters = array();

		// Paging
		$app = \JFactory::getApplication();
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			Config::getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['status'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.status',
			'status',
			'upcoming'
		));
		$this->view->filters['search'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		));

		$upcoming  = false;
		$installed = true;
		if ($this->view->filters['status'] == 'upcoming' || $this->view->filters['status'] == 'all')
		{
			$upcoming = true;

			if ($this->view->filters['status'] == 'upcoming')
			{
				$installed = false;
			}
		}

		$source = Component::params('com_update')->get('git_repository_source', null);

		$this->view->rows = json_decode(
			Cli::log(
				$this->view->filters['limit'],
				$this->view->filters['start'],
				$this->view->filters['search'],
				$upcoming,
				$installed,
				false,
				$source
			)
		);
		$this->view->total = json_decode(
			Cli::log(
				$this->view->filters['limit'],
				$this->view->filters['start'],
				$this->view->filters['search'],
				$upcoming,
				$installed,
				true,
				$source
			)
		);
		$this->view->total = $this->view->total[0];

		// Initiate paging
		$this->view->pageNav = new \JPagination(
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
	 * Perform update
	 *
	 * @return     void
	 */
	public function updateTask()
	{
		$env         = Config::getValue('config.application_env', 'production');
		$source      = Component::params('com_update')->get('git_repository_source', null);
		$autoPushRef = Component::params('com_update')->get('git_auto_push_ref', null);
		$allowNonFf  = ($env == 'production') ? false : true;
		$response    = Cli::update(false, $allowNonFf, $source, $autoPushRef);
		$response    = json_decode($response);
		$response    = $response[0];
		$message     = 'Update complete!';
		$type        = 'success';

		if (!empty($response) && stripos($response, 'fix conflicts and then commit the result') === false)
		{
			$type    = 'error';
			$message = ucfirst($response);
		}
		else
		{
			// Also check status again to make sure it's clean (merge conflicts will show up here)
			$status = json_decode(Cli::status());

			if (!empty($status))
			{
				foreach ($status as $type => $files)
				{
					// If anything is left over besides untracked files, something went wrong
					if ($type != 'untracked' && !empty($files))
					{
						$type    = 'error';
						$message = 'Update failed. Rolling back changes.';
						$this->rollbackTask();
						break;
					}
				}
			}
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$message,
			$type
		);
	}

	/**
	 * Perform rollback
	 *
	 * @return     void
	 */
	public function rollbackTask()
	{
		$response = Cli::rollback();
		$response = json_decode($response);
		$response = $response[0];
		$message  = 'Rollback complete!';
		$type     = 'success';

		if (!empty($response))
		{
			$type    = 'error';
			$message = ucfirst($response);
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$message,
			$type
		);
	}
}