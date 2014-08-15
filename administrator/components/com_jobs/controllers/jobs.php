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
 * Controller class for job postings
 */
class JobsControllerJobs extends \Hubzero\Component\AdminController
{
	/**
	 * Jobs List
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		$this->view->filters = array();

		// Get paging variables
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

		// Get sorting variables
		$this->view->filters['sortby']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortby',
			'filter_order',
			'added'
		));
		$this->view->filters['sortdir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'DESC'
		));

		// Filters
		$this->view->filters['category'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . 'category',
			'category',
			'all'
		));
		$this->view->filters['filterby'] = '';
		$this->view->filters['search']   = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . 'search',
			'search',
			''
		)));

		// Get data
		$obj = new Job($this->database);
		$this->view->rows = $obj->get_openings($this->view->filters, $this->juser->get('id'), 1);

		$this->view->total = $obj->get_openings($this->view->filters, $this->juser->get('id'), 1, '', 1);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		$this->view->config = $this->config;

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
	 * Create a job posting
	 * Displays the edit form
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit Job Posting
	 *
	 * @param      integer $isnew Parameter description (if any) ...
	 * @return     void
	 */
	public function editTask($isnew=0)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		$jconfig = JFactory::getConfig();
		$live_site = rtrim(JURI::base(),'/');

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'jobs.css');

		// Incoming job ID
		$id = JRequest::getVar('id', array(0));
		$id = is_array($id) ? $id[0] : $id;

		// Grab some filters for returning to place after editing
		$this->view->return = array();
		$this->view->return['sortby'] = JRequest::getVar('sortby', 'added');

		$this->view->row = new Job($this->database);

		$this->view->jobadmin = new JobAdmin($this->database);
		$this->view->employer = new Employer($this->database);
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_services' . DS . 'tables' . DS . 'subscription.php');

		// Is this a new job?
		if (!$id)
		{
			$this->view->row->created      = JFactory::getDate()->toSql();
			$this->view->row->created_by   = $this->juser->get('id');
			$this->view->row->modified     = '0000-00-00 00:00:00';
			$this->view->row->modified_by  = 0;
			$this->view->row->publish_up   = JFactory::getDate()->toSql();
			$this->view->row->employerid   = 1; // admin
		}
		else if (!$this->view->row->load($id))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_JOBS_ERROR_MISSING_JOB'),
				'error'
			);
			return;
		}

		$this->view->job = $this->view->row->get_opening($id, $this->juser->get('id'), 1);

		// Get employer information
		if ($this->view->row->employerid != 1)
		{
			if (!$this->view->employer->loadEmployer($this->view->row->employerid))
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('COM_JOBS_ERROR_MISSING_EMPLOYER_INFO'),
					'error'
				);
				return;
			}
		}
		else
		{
			// site admin
			$this->view->employer->uid = 1;
			$this->view->employer->subscriptionid = 1;
			$this->view->employer->companyName 		= $jconfig->getValue('config.sitename');
			$this->view->employer->companyLocation  = '';
			$this->view->employer->companyWebsite   = $live_site;
		}

		// Get subscription info
		$this->view->subscription = new Subscription($this->database);
		$this->view->subscription->loadSubscription($this->view->employer->subscriptionid, '', '', $status=array(0, 1));

		// Get job types and categories
		$jt = new JobType($this->database);
		$jc = new JobCategory($this->database);

		// get job types
		$this->view->types = $jt->getTypes();
		$this->view->types[0] = JText::_('COM_JOBS_TYPE_ANY');

		// get job categories
		$this->view->cats = $jc->getCats();
		$this->view->cats[0] = JText::_('COM_JOBS_CATEGORY_UNSPECIFIED');

		$this->view->config = $this->config;
		$this->view->isnew = $isnew;

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
	 * Save Job Posting
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$data 		= array_map('trim',$_POST);
		$action	 	= JRequest::getVar('action', '');
		$message	= JRequest::getVar('message', '');
		$id 		= JRequest::getInt('id', 0);
		$employerid = JRequest::getInt('employerid', 0);
		$emailbody 	= '';
		$statusmsg	= '';

		$job = new Job($this->database);
		$employer = new Employer($this->database);

		if ($id)
		{
			if (!$job->load($id))
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('COM_JOBS_ERROR_MISSING_JOB'),
					'error'
				);
				return;
			}
		}
		else
		{ // saving new job
			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_services' . DS . 'tables' . DS . 'subscription.php');
			$subscription = new Subscription($this->database);
			$code = $subscription->generateCode(8, 8, 0, 1, 0);
			$job->code = $code;

			$job->added = JFactory::getDate()->toSql();
			$job->addedBy = $this->juser->get('id');
		}

		$subject = $id ? JText::sprintf('COM_JOBS_MESSAGE_SUBJECT', $job->code) : '';

		// save any new info
		$job->bind($_POST);

		// some clean-up
		$job->description     = rtrim(stripslashes($job->description));
		$job->title           = rtrim(stripslashes($job->title));
		$job->companyName     = rtrim(stripslashes($job->companyName));
		$job->companyLocation = rtrim(stripslashes($job->companyLocation));

		// admin actions
		if ($id)
		{
			switch ($action)
			{
				case 'publish':
					// make sure we aren't over quota
					$allowed_ads = $employerid==1 ? 1 : $this->_checkQuota($job, $employerid, $this->database);

					if ($allowed_ads <= 0)
					{
						$statusmsg .= JobsHtml::error(JText::_('COM_JOBS_ERROR_OVER_LIMIT'));
						$action = '';
					}
					else
					{
						$job->status   = 1;
						$job->opendate = JFactory::getDate()->toSql();
						$statusmsg .= JText::_('COM_JOBS_MESSAGE_JOB_APPROVED');
					}
				break;

				case 'unpublish':
					$job->status 	= 3;
					$statusmsg .= JText::_('COM_JOBS_MESSAGE_JOB_UNPUBLISHED');
				break;

				case 'message':

				break;

				case 'delete':
					$job->status 	= 2;
					$statusmsg .= JText::_('COM_JOBS_MESSAGE_JOB_DELETED');
				break;
			}

			$job->editedBy = $this->juser->get('id');
			$job->edited = JFactory::getDate()->toSql();
		}

		if (!$job->store()) {
			echo JobsHtml::alert($job->getError());
			exit();
		}

		if (!$job->id)
		{
			$job->checkin();
		}

		if (($message && $action == 'message' && $id) || ($action && $action != 'message'))
		{
			// Email all the contributors
			$jconfig = JFactory::getConfig();

			// E-mail "from" info
			$from = array();
			$from['email'] = $jconfig->getValue('config.mailfrom');
			$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_('COM_JOBS_JOBS');

			$juri    = JURI::getInstance();
			$jconfig = JFactory::getConfig();

			$base 	 = rtrim($juri->base(), DS);
			if (substr($base, -13) == 'administrator')
			{
				$base = substr($base, 0, strlen($base)-13);
			}
			$sef  = 'jobs/job/' . $job->code;
			$link = rtrim($base, DS) . DS . trim($sef, DS);

			// start email message
			$emailbody .= $subject . ':' . "\r\n";
			$emailbody .= $statusmsg . "\r\n";
			$emailbody  .= JText::_('COM_JOBS_MESSAGE_JOB') . ': ' . $link . "\r\n";
			if ($message)
			{
				$emailbody .= "\n";
				$emailbody .= '----------------------------------------------------------' . "\r\n";
				$emailbody .= "\n" . JText::_('COM_JOBS_MESSAGE_FROM_ADMIN:') . "\n";
				$emailbody .= $message;
			}

			JPluginHelper::importPlugin('xmessage');
			$dispatcher = JDispatcher::getInstance();
			if (!$dispatcher->trigger('onSendMessage', array('jobs_ad_status_changed', $subject,
				$emailbody, $from, array($job->addedBy), $this->_option)))
			{
				$this->setError(JText::_('COM_JOBS_ERROR_FAILED_TO_MESSAGE_USERS'));
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_JOBS_ITEM_SAVED') . ($statusmsg ? ' ' . $statusmsg : '')
		);
	}

	/**
	 * Check job ad quota depending on subscription
	 *
	 * @param      object $job Parameter description (if any) ...
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      unknown $database Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function _checkQuota($job, $uid, $database)
	{
		// make sure we aren't over quota
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_services' . DS . 'tables' . DS . 'service.php');
		$objS = new Service($database);
		$maxads = isset($this->config->parameters['maxads']) && intval($this->config->parameters['maxads']) > 0  ? $this->config->parameters['maxads'] : 3;
		$service = $objS->getUserService($uid);
		$activejobs = $job->countMyActiveOpenings($uid, 1);
		$allowed_ads = $service == 'employer_basic' ? 1 - $activejobs : $maxads - $activejobs;

		return $allowed_ads;
	}

	/**
	 * Remove Job Posting
	 *
	 * @return     unknown Return description (if any) ...
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
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_JOBS_ERROR_NO_ITEM_SELECTED'),
				'error'
			);
			return;
		}

		$row = new Job($this->database);

		foreach ($ids as $id)
		{
			// Delete the type
			$row->delete($id);
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::sprintf('COM_JOBS_ITEMS_REMOVED', count($ids))
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}
