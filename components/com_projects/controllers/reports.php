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
 * Projects Reports controller class
 */
class ProjectsControllerReports extends \Hubzero\Component\SiteController
{
	/**
	 * Display reports
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Incoming
		$period = JRequest::getVar( 'period', 'alltime');

		// Publishing enabled?
		$this->_publishing = JPluginHelper::isEnabled('projects', 'publications') ? 1 : 0;
		if ($this->_publishing)
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'publication.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'version.php');
		}

		// Instantiate a project and related classes
		$obj   = new Project( $this->database );
		$objAA = new ProjectActivity ( $this->database );

		// Is user in special admin group to view advanced stats?
		$admin = ProjectsHelper::checkReviewerAuth('general', $this->config);

		// Get all test projects
		$testProjects = $obj->getTestProjects();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle(null);
		$this->view->title = $this->_title;

		if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_projects' . DS . 'tables' . DS . 'project.stats.php'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_projects' . DS . 'tables' . DS . 'project.stats.php');

			$objStats = new ProjectStats($this->database);

			$monthly = $objStats->monthlyStats(2, true);
			$this->view->monthly = ($monthly && count($monthly) > 1) ? $monthly : NULL;
		}
		else
		{
			$this->view->monthly = NULL;
		}

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
		$data   = JRequest::getVar( 'data', array(), 'post', 'array' );
		$from   = JRequest::getVar( 'fromdate', JHTML::_('date', JFactory::getDate('-1 month')->toSql(), 'Y-m') );
		$to     = JRequest::getVar( 'todate', JHTML::_('date', JFactory::getDate()->toSql(), 'Y-m') );
		$filter = JRequest::getVar( 'searchterm', '');

		if (empty($data))
		{
			$this->setError(JText::_('Please pick at least one information field to report'));
		}

		$date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])$/';

		if (!preg_match($date_regex, $from) || !preg_match($date_regex, $to))
		{
			$this->setError(JText::_('Please use yyyy-mm format for the date'));
		}

		if (strtotime($from) > strtotime($to))
		{
			$this->setError(JText::_('The start date of report should be earlier than the end date'));
		}

		// Instantiate a project and related classes
		$obj   = new Project( $this->database );

		// Is user in special admin group to view advanced stats?
		$admin = ProjectsHelper::checkReviewerAuth('general', $this->config);

		// Check authorization
		$authorized   = $this->_authorize();

		if (!$authorized)
		{
			if ($this->juser->get('guest'))
			{
				$this->_msg = JText::_('COM_PUBLICATIONS_REPORTS_LOGIN');
				$this->_login();
				return;
			}

			JError::raiseError( 403, JText::_('COM_PUBLICATIONS_REPORTS_ERROR_UNAUTHORIZED'));
			return;
		}

		// Get stats
		if (!$this->getError())
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'logs.php');

			$objLog = new PublicationLog($this->database);

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
				$this->setError(JText::_('Nothing to report for selected date range and/or search term'));
			}
		}

		// Redirect on error
		if ($this->getError())
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option
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
		$obj   = new Project( $this->database );

		// Is user in special admin group to view advanced stats?
		$admin = ProjectsHelper::checkReviewerAuth('general', $this->config);

		// Get all test projects
		$testProjects = $obj->getTestProjects();

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle(null);
		$this->view->title = $this->_title;

		// Check authorization
		$authorized   = $this->_authorize();

		if (!$authorized)
		{
			if ($this->juser->get('guest'))
			{
				$this->_msg = JText::_('COM_PUBLICATIONS_REPORTS_LOGIN');
				$this->_login();
				return;
			}

			JError::raiseError( 403, JText::_('COM_PUBLICATIONS_REPORTS_ERROR_UNAUTHORIZED'));
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

	/**
	 * Build the "trail"
	 *
	 * @return void
	 */
	protected function _buildPathway()
	{
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		$pathway->addItem(
			JText::_('COM_PROJECTS_REPORTS'),
			'index.php?option=' . $this->_option . '&controller=reports&task=display'
		);

		if ($this->_task == 'custom')
		{
			$pathway->addItem(
				JText::_('COM_PROJECTS_REPORTS_CUSTOM'),
				'index.php?option=' . $this->_option . '&controller='
					. $this->_controller .  '&task=custom'
			);
		}
	}

	/**
	 * Build the title for this component
	 *
	 * @return void
	 */
	protected function _buildTitle()
	{
		if (!$this->_title)
		{
			$this->_title = JText::_(strtoupper($this->_option)) . ': '
				. JText::_(strtoupper($this->_option . '_' . $this->_controller));
		}

		if ($this->_task == 'custom')
		{
			$this->_title .= ' - ' . JText::_('COM_PROJECTS_CUSTOM');
		}
		$document = JFactory::getDocument();
		$document->setTitle( $this->_title );
	}

	/**
	 * Check user access
	 *
	 * @param      array $curatorgroups
	 * @return     mixed False if no access, string if has access
	 */
	protected function _authorize( $curatorgroups = array() )
	{
		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			return false;
		}

		$authorized = false;

		// Check if they're a site admin (from Joomla)
		if ($this->juser->authorize($this->_option, 'manage'))
		{
			$authorized = 'admin';
		}

		// Check if they are in reports
		$reportgroup = $this->config->get('reportgroup', '');
		if ($reportgroup && $group = \Hubzero\User\Group::getInstance($reportgroup))
		{
			// Check if they're a member of this group
			$ugs = \Hubzero\User\Helper::getGroups($this->juser->get('id'));
			if ($ugs && count($ugs) > 0)
			{
				foreach ($ugs as $ug)
				{
					if ($group && $ug->cn == $group->get('cn'))
					{
						$authorized = true;
						return $authorized;
					}
				}
			}
		}

		return $authorized;
	}

	/**
	 * Login view
	 *
	 * @return     void
	 */
	protected function _login()
	{
		$rtrn = JRequest::getVar('REQUEST_URI',
			JRoute::_('index.php?option=' . $this->_option
			. '&controller=reports&task=' . $this->_task), 'server');

		$this->setRedirect(
			JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
			$this->_msg,
			'warning'
		);
	}
}
