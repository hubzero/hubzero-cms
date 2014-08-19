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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Update controller class
 */
class UpdateControllerDatabase extends \Hubzero\Component\AdminController
{
	/**
	 * Display the database migration log
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->filters = array();

		// Paging
		$app    = JFactory::getApplication();
		$config = JFactory::getConfig();
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		$this->view->rows  = array();
		$this->view->total = 0;
		$migrations = json_decode(cli::migration(true, true));
		if ($migrations && count($migrations) > 0)
		{
			foreach ($migrations as $status => $files)
			{
				$files = array_reverse($files);
				foreach ($files as $entry)
				{
					$row = array('entry'=>$entry, 'status'=>$status);
					$this->view->rows[] = $row;
				}
			}
			$this->view->total = count($this->view->rows);
			$this->view->rows  = array_splice($this->view->rows, $this->view->filters['start'], $this->view->filters['limit']);
		}

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Perform rollback
	 *
	 * @return     void
	 */
	public function migrateTask()
	{
		$file     = JRequest::getVar('file', null);
		$response = cli::migration(false, true, $file);
		$response = json_decode($response);
		$message  = 'Migration complete!';
		$type     = 'success';

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$message,
			$type
		);
	}
}