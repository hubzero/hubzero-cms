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

namespace Components\Projects\Controllers;

use Components\Projects\Tables;

/**
 * Projects Reports controller class
 */
class Reports extends Base
{
	/**
	 * Display reports
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Incoming
		$period = \JRequest::getVar( 'period', 'alltime');

		// Instantiate a project and related classes
		$obj   = new Tables\Project( $this->database );
		$objAA = new Tables\Activity ( $this->database );

		// Is user in special admin group to view advanced stats?
		$admin = $this->_checkReviewerAuth('general');

		// Get all test projects
		$testProjects = $obj->getTestProjects();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();
		$this->view->title = $this->title;

		require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
			.'com_projects' . DS . 'tables' . DS . 'project.stats.php');

		$objStats = new Tables\Stats($this->database);

		$monthly = $objStats->monthlyStats(2, true);
		$this->view->monthly = ($monthly && count($monthly) > 1) ? $monthly : NULL;

		// Output HTML
		$this->view->task 		= $this->_task;
		$this->view->admin 		= $admin;
		$this->view->option 	= $this->_option;
		$this->view->config 	= $this->config;
		$this->view->uid 		= $this->juser->get('id');
		$this->view->guest 		= $this->juser->get('guest');
		$this->view->stats		= $obj->getStats($period, $admin, $this->config, $testProjects, $this->_publishing);
		$this->view->publishing	= $this->_publishing;

		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}

		$this->view->msg = isset($this->_msg) ? $this->_msg : '';
		$this->view->display();
	}

	/**
	 * Generate report
	 *
	 * @return     void
	 */
	public function generateTask()
	{
		// Incoming
		$data   = \JRequest::getVar( 'data', array(), 'post', 'array' );
		$from   = \JRequest::getVar( 'fromdate', \JHTML::_('date', \JFactory::getDate('-1 month')->toSql(), 'Y-m') );
		$to     = \JRequest::getVar( 'todate', \JHTML::_('date', \JFactory::getDate()->toSql(), 'Y-m') );
		$filter = \JRequest::getVar( 'searchterm', '');

		if (empty($data))
		{
			$this->setError(Lang::txt('Please pick at least one information field to report'));
		}

		$date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])$/';

		if (!preg_match($date_regex, $from) || !preg_match($date_regex, $to))
		{
			$this->setError(Lang::txt('Please use yyyy-mm format for the date'));
		}

		if (strtotime($from) > strtotime($to))
		{
			$this->setError(Lang::txt('The start date of report should be earlier than the end date'));
		}

		// Instantiate a project and related classes
		$obj   = new Tables\Project( $this->database );

		// Is user in special admin group to view advanced stats?
		$admin = $this->_checkReviewerAuth('general');

		// Check authorization
		$groups = $this->config->get('reportgroup', '') ? array($this->config->get('reportgroup', '')) : array();
		$authorized   = $this->_authorize(0, $groups);

		if (!$authorized)
		{
			if ($this->juser->get('guest'))
			{
				$this->_msg = Lang::txt('COM_PUBLICATIONS_REPORTS_LOGIN');
				$this->_login();
				return;
			}

			\JError::raiseError( 403, Lang::txt('COM_PUBLICATIONS_REPORTS_ERROR_UNAUTHORIZED'));
			return;
		}

		// Get stats
		if (!$this->getError())
		{
			require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'logs.php');

			$objLog = new \Components\Publications\Tables\Log($this->database);

			// Get all test projects
			$exclude = $obj->getTestProjects();

			$stats = $objLog->getCustomStats($from, $to, $exclude, $filter);

			$filename = 'from_' . $from . '_to_' . $to .'_report.csv';

			if ($stats)
			{
				// Output to CSV
				header('Content-Type: text/csv; charset=utf-8');
				header('Content-Disposition: attachment; filename=' . $filename);
				$output = fopen('php://output', 'w');

				// output the column headings
				fputcsv($output, $data);

				foreach ($stats as $record)
				{
					$sorted = array();
					foreach ($data as $field)
					{
						$input = $record->$field;
						if ($field == 'doi')
						{
							$input = $input ? 'http://dx.doi.org/' . $input : 'N/A';
						}

						$sorted[] = $input;
					}

					fputcsv($output, $sorted);
				}

				fclose($output);
			}
			else
			{
				$this->setError(Lang::txt('Nothing to report for selected date range and/or search term'));
			}
		}

		// Redirect on error
		if ($this->getError())
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option
				. '&controller=reports&task=custom'
				. '&searchterm=' . $filter),
				$this->getError(),
				'error'
			);
		}

		return;
	}

	/**
	 * Custom reports
	 *
	 * @return     void
	 */
	public function customTask()
	{
		$this->view->setLayout('custom');

		// Instantiate a project and related classes
		$obj   = new Tables\Project( $this->database );

		// Is user in special admin group to view advanced stats?
		$admin = $this->_checkReviewerAuth('general');

		// Get all test projects
		$testProjects = $obj->getTestProjects();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();
		$this->view->title = $this->title;

		// Check authorization
		$groups = $this->config->get('reportgroup', '') ? array($this->config->get('reportgroup', '')) : array();
		$authorized   = $this->_authorize(0, $groups);

		if (!$authorized)
		{
			if ($this->juser->get('guest'))
			{
				$this->_msg = Lang::txt('COM_PUBLICATIONS_REPORTS_LOGIN');
				$this->_login();
				return;
			}

			\JError::raiseError( 403, Lang::txt('COM_PUBLICATIONS_REPORTS_ERROR_UNAUTHORIZED'));
			return;
		}

		// Output HTML
		$this->view->task 		= $this->_task;
		$this->view->admin 		= $admin;
		$this->view->option 	= $this->_option;
		$this->view->config 	= $this->config;

		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}

		$this->view->msg = isset($this->_msg) ? $this->_msg : '';
		$this->view->display();
	}
}
