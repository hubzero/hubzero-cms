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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Jobs\Admin\Controllers;

use Components\Jobs\Tables\Job;
use Components\Jobs\Tables\JobAdmin;
use Components\Jobs\Tables\JobCategory;
use Components\Jobs\Tables\JobType;
use Components\Jobs\Tables\Employer;
use Hubzero\Component\AdminController;
use Exception;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use User;
use Date;
use App;

/**
 * Controller class for job postings
 */
class Jobs extends AdminController
{
	/**
	 * Jobs List
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view->filters = array(
			// Get paging variables
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			// Get sorting variables
			'sortby' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortby',
				'filter_order',
				'added'
			),
			'sortdir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			// Filters
			'category' => Request::getState(
				$this->_option . '.' . $this->_controller . 'category',
				'category',
				'all'
			),
			'filterby' => '',
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . 'search',
				'search',
				''
			))
		);

		// Get data
		$obj = new Job($this->database);

		$this->view->rows  = $obj->get_openings($this->view->filters, User::get('id'), 1);
		$this->view->total = $obj->get_openings($this->view->filters, User::get('id'), 1, '', 1);

		$this->view->config = $this->config;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a job posting
	 * Displays the edit form
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit Job Posting
	 *
	 * @param   integer  $isnew  Is this a new entry?
	 * @return  void
	 */
	public function editTask($isnew=0)
	{
		Request::setVar('hidemainmenu', 1);

		$live_site = rtrim(Request::base(),'/');

		// Push some styles to the template
		$this->css();

		// Incoming job ID
		$id = Request::getVar('id', array(0));
		$id = is_array($id) ? $id[0] : $id;

		// Grab some filters for returning to place after editing
		$this->view->return = array();
		$this->view->return['sortby'] = Request::getVar('sortby', 'added');

		$this->view->row = new Job($this->database);

		$this->view->jobadmin = new JobAdmin($this->database);
		$this->view->employer = new Employer($this->database);

		// Is this a new job?
		if (!$id)
		{
			$this->view->row->created      = Date::toSql();
			$this->view->row->created_by   = User::get('id');
			$this->view->row->modified     = '0000-00-00 00:00:00';
			$this->view->row->modified_by  = 0;
			$this->view->row->publish_up   = Date::toSql();
			$this->view->row->employerid   = 1; // admin
		}
		else if (!$this->view->row->load($id))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_JOBS_ERROR_MISSING_JOB'),
				'error'
			);
			return;
		}

		$this->view->job = $this->view->row->get_opening($id, User::get('id'), 1);

		// Get employer information
		if ($this->view->row->employerid != 1)
		{
			if (!$this->view->employer->loadEmployer($this->view->row->employerid))
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					Lang::txt('COM_JOBS_ERROR_MISSING_EMPLOYER_INFO'),
					'error'
				);
				return;
			}
		}
		else
		{
			// site admin
			$this->view->employer->uid             = 1;
			$this->view->employer->subscriptionid  = 1;
			$this->view->employer->companyName     = Config::get('sitename');
			$this->view->employer->companyLocation = '';
			$this->view->employer->companyWebsite  = $live_site;
		}

		// Get subscription info
		include_once(PATH_CORE . DS . 'components' . DS . 'com_services' . DS . 'tables' . DS . 'subscription.php');

		$this->view->subscription = new \Components\Services\Tables\Subscription($this->database);
		$this->view->subscription->loadSubscription($this->view->employer->subscriptionid, '', '', $status=array(0, 1));

		// Get job types and categories
		$jt = new JobType($this->database);
		$jc = new JobCategory($this->database);

		// get job types
		$this->view->types = $jt->getTypes();
		$this->view->types[0] = Lang::txt('COM_JOBS_TYPE_ANY');

		// get job categories
		$this->view->cats = $jc->getCats();
		$this->view->cats[0] = Lang::txt('COM_JOBS_CATEGORY_UNSPECIFIED');

		$this->view->config = $this->config;
		$this->view->isnew = $isnew;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save Job Posting
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$data       = array_map('trim',$_POST);
		$action     = Request::getVar('action', '');
		$message    = Request::getVar('message', '');
		$id         = Request::getInt('id', 0);
		$employerid = Request::getInt('employerid', 0);
		$emailbody  = '';
		$statusmsg  = '';

		$job = new Job($this->database);
		$employer = new Employer($this->database);

		if ($id)
		{
			if (!$job->load($id))
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					Lang::txt('COM_JOBS_ERROR_MISSING_JOB'),
					'error'
				);
				return;
			}
		}
		else
		{ // saving new job
			include_once(PATH_CORE . DS . 'components' . DS . 'com_services' . DS . 'tables' . DS . 'subscription.php');
			$subscription = new \Components\Services\Tables\Subscription($this->database);
			$code = $subscription->generateCode(8, 8, 0, 1, 0);
			$job->code = $code;

			$job->added   = Date::toSql();
			$job->addedBy = User::get('id');
		}

		$subject = $id ? Lang::txt('COM_JOBS_MESSAGE_SUBJECT', $job->code) : '';

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
					$allowed_ads = $employerid == 1 ? 1 : $this->_checkQuota($job, $employerid, $this->database);

					if ($allowed_ads <= 0)
					{
						$statusmsg .= Lang::txt('COM_JOBS_ERROR_OVER_LIMIT');
						$action = '';
					}
					else
					{
						$job->status   = 1;
						$job->opendate = Date::toSql();
						$statusmsg .= Lang::txt('COM_JOBS_MESSAGE_JOB_APPROVED');
					}
				break;

				case 'unpublish':
					$job->status = 3;
					$statusmsg .= Lang::txt('COM_JOBS_MESSAGE_JOB_UNPUBLISHED');
				break;

				case 'message':

				break;

				case 'delete':
					$job->status = 2;
					$statusmsg .= Lang::txt('COM_JOBS_MESSAGE_JOB_DELETED');
				break;
			}

			$job->editedBy = User::get('id');
			$job->edited   = Date::toSql();
		}

		if (!$job->store())
		{
			throw new Exception($job->getError(), 500);
		}

		if (!$job->id)
		{
			$job->checkin();
		}

		if (($message && $action == 'message' && $id) || ($action && $action != 'message'))
		{
			// E-mail "from" info
			$from = array(
				'email' => Config::get('mailfrom'),
				'name'  => Config::get('sitename') . ' ' . Lang::txt('COM_JOBS_JOBS')
			);

			$base = rtrim(Request::base(), '/');
			if (substr($base, -13) == 'administrator')
			{
				$base = substr($base, 0, strlen($base)-13);
			}
			$sef  = 'jobs/job/' . $job->code;
			$link = rtrim($base, '/') . '/' . trim($sef, '/');

			// start email message
			$emailbody .= $subject . ':' . "\r\n";
			$emailbody .= $statusmsg . "\r\n";
			$emailbody .= Lang::txt('COM_JOBS_MESSAGE_JOB') . ': ' . $link . "\r\n";
			if ($message)
			{
				$emailbody .= "\n";
				$emailbody .= '----------------------------------------------------------' . "\r\n";
				$emailbody .= "\n" . Lang::txt('COM_JOBS_MESSAGE_FROM_ADMIN:') . "\n";
				$emailbody .= $message;
			}

			if (!Event::trigger('xmessage.onSendMessage', array('jobs_ad_status_changed', $subject, $emailbody, $from, array($job->addedBy), $this->_option)))
			{
				Notify::error(Lang::txt('COM_JOBS_ERROR_FAILED_TO_MESSAGE_USERS'));
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_JOBS_ITEM_SAVED') . ($statusmsg ? ' ' . $statusmsg : '')
		);
	}

	/**
	 * Check job ad quota depending on subscription
	 *
	 * @param    object  $job       Job entry
	 * @param   integer  $uid       User ID
	 * @param   object   $database  JDatabase
	 * @return  integer
	 */
	private function _checkQuota($job, $uid, $database)
	{
		// make sure we aren't over quota
		include_once(PATH_CORE . DS . 'components' . DS . 'com_services' . DS . 'tables' . DS . 'service.php');
		$objS = new \Components\Services\Tables\Service($database);
		$maxads = isset($this->config->parameters['maxads']) && intval($this->config->parameters['maxads']) > 0  ? $this->config->parameters['maxads'] : 3;
		$service = $objS->getUserService($uid);
		$activejobs = $job->countMyActiveOpenings($uid, 1);

		return ($service == 'employer_basic' ? 1 - $activejobs : $maxads - $activejobs);
	}

	/**
	 * Remove Job Posting
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming (expecting an array)
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Ensure we have an ID to work with
		if (empty($ids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_JOBS_ERROR_NO_ITEM_SELECTED'),
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
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_JOBS_ITEMS_REMOVED', count($ids))
		);
	}
}
