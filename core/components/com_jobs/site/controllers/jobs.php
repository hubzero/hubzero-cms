<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Site\Controllers;

use Components\Jobs\Tables\JobAdmin;
use Components\Jobs\Tables\JobApplication;
use Components\Jobs\Tables\JobCategory;
use Components\Jobs\Tables\Employer;
use Components\Jobs\Tables\Job;
use Components\Jobs\Tables\Prefs;
use Components\Jobs\Tables\Resume;
use Components\Jobs\Tables\JobSeeker;
use Components\Jobs\Tables\Shortlist;
use Components\Jobs\Tables\JobStats;
use Components\Jobs\Tables\JobType;
use Components\Services\Tables\Subscription;
use Components\Services\Tables\Service;
use Hubzero\Component\SiteController;
use Hubzero\Component\View;
use Exception;
use Request;
use Pathway;
use Event;
use Route;
use Lang;
use Date;
use User;
use ZipArchive;

/**
 * Jobs controller class for postings
 */
class Jobs extends SiteController
{
	/**
	 * Method to set a property of the class
	 *
	 * @param     string $property Name of property
	 * @param     mixed  $value    Value of the property
	 * @return    void
	 */
	public function setVar($property, $value)
	{
		$this->$property = $value;
	}

	/**
	 * Method to get a property of the class
	 *
	 * @param      string $property Name of property
	 * @return     mixed
	 */
	public function getVar($property)
	{
		return $this->$property;
	}

	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		// Set configs
		$this->_banking            = $this->config->get('banking', 0);
		$this->_industry           = $this->config->get('industry', '');
		$this->_allowSubscriptions = $this->config->get('allowsubscriptions', 0);

		// Get admin priviliges
		self::_authorizeAdmin();

		// Get employer priviliges
		if ($this->_allowSubscriptions)
		{
			self::_authorizeEmployer($this->_admin);
		}
		else
		{
			$this->_emp = 0;
		}

		// Set component administrator priviliges
		$this->_masterAdmin = $this->_admin && !$this->_emp ? 1 : 0;

		// Incoming
		$this->_task    = Request::getVar('task', '');
		$this->_jobCode = Request::getVar('code', '');

		$this->registerTask('addjob', 'editjob');
		$this->registerTask('confirmjob', 'savejob');
		$this->registerTask('unpublish', 'savejob');
		$this->registerTask('reopen', 'savejob');
		$this->registerTask('remove', 'savejob');
		$this->registerTask('view', 'display');
		$this->registerTask('browse', 'display');
		$this->registerTask('withdraw', 'saveapp');
		parent::execute();
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return	void
	 */
	protected function _buildTitle()
	{
		$this->_title  = Lang::txt(strtoupper($this->_option));
		$this->_title .= $this->_industry ? ' ' . Lang::txt('COM_JOBS_IN') . ' ' . $this->_industry : '';
		if ($this->_subtitle)
		{
			$this->_title .= ': ' . $this->_subtitle;
		}
		else if ($this->_task && $this->_task != 'all' && $this->_task != 'view')
		{
			$this->_title .= ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		\Document::setTitle($this->_title);
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		$title  = Lang::txt(strtoupper($this->_option));
		$title .= $this->_industry ? ' ' . Lang::txt('COM_JOBS_IN') . ' ' . $this->_industry : '';

		if (Pathway::count() <= 0)
		{
			Pathway::append(
				$title,
				Route::url('index.php?option=' . $this->_option)
			);
		}
		if ($this->_task)
		{
			switch ($this->_task)
			{
				case 'browse':
					Pathway::append(
						Lang::txt(strtoupper($this->_option) . '_BROWSE'),
						Route::url('index.php?option=' . $this->_option . '&task=browse')
					);
				break;
				case 'all':
					if (!$this->_allowSubscriptions)
					{
						Pathway::append(
							Lang::txt(strtoupper($this->_option) . '_BROWSE'),
							Route::url('index.php?option=' . $this->_option . '&task=browse')
						);
					}
				break;
				case 'view':
				case 'cancel':
				case 'saveapp':
				case 'savejob':
				case 'confirmjob':
				case 'reopen':
				case 'remove':
				case 'confirm':
				case 'withdraw':
					// nothing
				break;
				case 'apply':
					Pathway::append(
						$this->_jobtitle,
						Route::url('index.php?option=' . $this->_option . '&task=job&code=' . $this->_jobCode)
					);
					Pathway::append(
						Lang::txt(strtoupper($this->_option) . '_APPLY'),
						Route::url('index.php?option=' . $this->_option . '&task=apply&code=' . $this->_jobCode)
					);
				break;
				case 'editapp':
					Pathway::append(
						$this->_jobtitle,
						Route::url('index.php?option=' . $this->_option . '&task=job&code=' . $this->_jobCode)
					);
					Pathway::append(
						Lang::txt(strtoupper($this->_option) . '_EDITAPP'),
						Route::url('index.php?option=' . $this->_option . '&task=apply&code=' . $this->_jobCode)
					);
				break;
				case 'job':
					Pathway::append(
						$this->_jobtitle,
						Route::url('index.php?option=' . $this->_option . '&task=job&code=' . $this->_jobCode)
					);
				break;
				case 'editjob':
					Pathway::append(
						$this->_jobtitle,
						Route::url('index.php?option=' . $this->_option . '&task=job&code=' . $this->_jobCode)
					);
					Pathway::append(
						Lang::txt(strtoupper($this->_option) . '_EDITJOB'),
						Route::url('index.php?option=' . $this->_option . '&task=editjob&code=' . $this->_jobCode)
					);
				break;
				default:
					Pathway::append(
						Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
						Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task)
					);
				break;
			}
		}
	}

	/**
	 * Redirect to login form with the return set
	 *
	 * @return     void
	 */
	public function login()
	{
		$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task, false, true), 'server');

		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
		);
	}

	/**
	 * Call a plugin
	 * NOTE: This view should only be called through AJAX
	 *
	 * @return     string HTML
	 */
	public function pluginTask()
	{
		// Incoming
		$trigger = trim(Request::getVar('trigger', ''));

		// Ensure we have a trigger
		if (!$trigger)
		{
			echo '<p class="error">' . Lang::txt('COM_JOBS_ERROR_NO_TRIGGER_FOUND') . '</p>';
			return;
		}

		// Call the trigger
		$results = Event::trigger('members.' . $trigger, array());
		if (is_array($results) && isset($results[0]) && isset($results[0]['html']))
		{
			$html = $results[0]['html'];
		}

		// Output HTML
		echo isset($html) ? $html : NULL;
	}

	/**
	 * Display a shortlist
	 *
	 * @return  void
	 */
	public function shortlistTask()
	{
		// Shortlisted user
		$oid  = Request::getInt('oid', 0);

		// Call the trigger
		$results = Event::trigger('members.shortlist', array($oid, $ajax=0));

		// Go back to the page
		App::redirect(
			Request::getVar('HTTP_REFERER', NULL, 'server')  // What page they came from
		);
	}

	/**
	 * Introductory page/ Jobs list
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$subscriptionCode = Request::getVar('employer', '');
		$action = Request::getVar('action', '');

		// Push some styles to the template
		$this->css('introduction.css', 'system');
		$this->css();

		// Push some scripts to the template
		$this->js();

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$model    = new Employer($this->database);
		$employer = $subscriptionCode ? $model->getEmployer(0, $subscriptionCode) : '';

		// Login?
		if ($action == 'login' && User::isGuest())
		{
			$this->_msg = Lang::txt('COM_JOBS_MSG_PLEASE_LOGIN_OPTIONS');
			$this->login();
			return;
		}

		if (!User::isGuest() && ($this->_task == 'browse' or !$this->_allowSubscriptions))
		{
			// save incoming prefs
			$this->_updatePrefs('job');

			// get stored preferences
			$this->_getPrefs('job');
		}

		// Get filters
		$filters = $this->_getFilters($this->_admin, 0 , 1 , 1);
		$filters['active'] = 1; // only show jobs that have open/unexpired search close date

		// Get data
		$obj = new Job($this->database);

		// Get jobs
		$adminoptions = ($this->_task != 'browse' && $this->_allowSubscriptions)  ? 0 : $this->_admin;
		$jobs = $obj->get_openings($filters, User::get('id'), $adminoptions, $subscriptionCode);

		$total = $obj->get_openings($filters, User::get('id'), $adminoptions, $subscriptionCode, 1);

		// Initiate paging
		$jtotal = ($this->_task != 'browse' && $this->_allowSubscriptions) ? count($jobs) : $total;
		$pageNav = new \Hubzero\Pagination\Paginator(
			$jtotal,
			$filters['start'],
			$filters['limit']
		);

		// Output HTML
		if ($this->_task != 'browse' && $this->_allowSubscriptions)
		{
			// Component introduction
			$view = new View(array('name' => 'intro'));
			$view->title       = $this->_title;
			$view->config      = $this->config;
			$view->option      = $this->_option;
			$view->emp         = $this->_emp;
			$view->admin       = $this->_admin;
			$view->pageNav     = $pageNav;
			$view->msg         = $this->_msg;
			$view->display();

			// Show latest jobs
			$view = new View(array('name' => 'jobs', 'layout' => 'latest'));
			$view->set('option', $this->_option)
		     ->set('filters', $filters)
		     ->set('config', $this->config)
		     ->set('task', $this->_task)
		     ->set('emp', $this->_emp)
			 ->set('jobs', $jobs)
		     ->set('admin', $this->admin)
		     ->display();
		}
		else
		{
			// Jobs list
			$view = new View(array(
				'base_path' => PATH_CORE . DS . 'components' . DS . 'com_jobs' . DS . 'site',
				'name'      => 'jobs',
				'layout'    => 'default'
				)
			);
			$view->title            = $this->_title;
			$view->config           = $this->config;
			$view->option           = $this->_option;
			$view->emp              = $this->_emp;
			$view->admin            = $this->_admin;
			$view->total            = $jtotal;
			$view->pageNav          = $pageNav;
			$view->jobs             = $jobs;
			$view->mini             = 0;
			$view->filters          = $filters;
			$view->subscriptionCode = $subscriptionCode;
			$view->employer         = $employer;
			$view->task             = $this->_task;

			$view->display();
		}

		return;
	}

	/**
	 * List of candidates
	 *
	 * @return     void
	 */
	public function resumesTask()
	{
		// Push some styles to the template
		$this->css();

		// Push some scripts to the template
		$this->js();

		// Login required
		if (User::isGuest())
		{
			\Notify::warning(Lang::txt('COM_JOBS_PLEASE_LOGIN_ACCESS_EMPLOYER'));
			$this->login();
			return;
		}

		if ($this->_admin or $this->_emp)
		{
			// Set page title
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway();

			// save incoming prefs
			$this->_updatePrefs();

			// get stored preferences
			$this->_getPrefs();

			// get filters
			$filters = self::_getFilters($this->_admin, $this->_emp);

			// get job types
			$jt       = new JobType($this->database);
			$types    = $jt->getTypes();
			$types[0] = Lang::txt('COM_JOBS_TYPE_ANY');

			// get job categories
			$jc      = new JobCategory($this->database);
			$cats    = $jc->getCats();
			$cats[0] = Lang::txt('COM_JOBS_CATEGORY_ANY');

			// get users with resumes
			$js      = new JobSeeker($this->database);
			$seekers = $js->getSeekers($filters, User::get('id'), 0, $this->_masterAdmin);
			$total   = $js->countSeekers($filters, User::get('id'), 0, $this->_masterAdmin);

			// Initiate paging
			$pageNav = new \Hubzero\Pagination\Paginator(
				$total,
				$filters['start'],
				$filters['limit']
			);

			// Output HTML
			$this->view->config      = $this->config;
			$this->view->admin       = $this->_admin;
			$this->view->masterAdmin = $this->_masterAdmin;
			$this->view->title       = $this->_title;
			$this->view->seekers     = $seekers;
			$this->view->pageNav     = $pageNav;
			$this->view->cats        = $cats;
			$this->view->types       = $types;
			$this->view->filters     = $filters;
			$this->view->emp         = $this->_emp;
			$this->view->option      = $this->_option;
			$this->view->setName('resumes')
						->setLayout('default')
						->display();
		}
		else if ($this->_allowSubscriptions)
		{
			// need to subscribe first
			$employer = new Employer($this->database);
			if ($employer->loadEmployer(User::get('id')))
			{
				//do we have a pending subscription?
				$subscription = new Subscription($this->database);
				if ($subscription->loadSubscription($employer->subscriptionid, User::get('id'), '', $status=array(0)))
				{
					App::redirect(
						Route::url('index.php?option=com_jobs&task=dashboard'),
						Lang::txt('COM_JOBS_WARNING_SUBSCRIPTION_PENDING'), 'warning'
					);
					return;
				}
			}

			// send to subscription page
			App::redirect(
				Route::url('index.php?option=com_jobs&task=subscribe')
			);
		}
		else
		{
			App::redirect(
				Route::url('index.php?option=com_jobs')
			);
		}
	}

	/**
	 * Subscription form
	 *
	 * @return     void
	 */
	public function subscribeTask()
	{
		// Login required
		if (User::isGuest())
		{
			\Notify::warning(Lang::txt('COM_JOBS_PLEASE_LOGIN_ACCESS_EMPLOYER'));
			$this->login();
			return;
		}

		// are we viewing other person's subscription? (admins only)
		$uid = Request::getInt('uid', 0);

		if ($uid && User::get('id') != $uid && !$this->_admin)
		{
			// not authorized
			App::abort(403, Lang::txt('COM_JOBS_ALERTNOTAUTH'));
		}

		$uid = $uid ? $uid : User::get('id');

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->css();

		// Push some scripts to the template
		$this->js();

		// Get the member's info
		$profile = User::getInstance($uid);

		// load Employer
		$employer = new Employer($this->database);
		if (!$employer->loadEmployer($uid))
		{
			$employer = new Employer($this->database);
			$employer->uid             = $uid;
			$employer->subscriptionid  = 0;
			$employer->companyName     = $profile->get('organization');
			$employer->companyLocation = $profile->get('countryresident');
			$employer->companyWebsite  = $profile->get('url');
		}

		// do we have an active subscription already?
		$subscription = new Subscription($this->database);
		if (!$subscription->loadSubscription ($employer->subscriptionid, '', '', $status=array(0, 1)))
		{
			$subscription = new Subscription($this->database);
			$subscription->uid = $uid;
			$subscription->serviceid = 0;
		}

		// get subscription options
		$objS = new Service($this->database);
		$specialgroup = $this->config->get('specialgroup', '');
		if ($specialgroup)
		{
			$sgroup = \Hubzero\User\Group::getInstance($specialgroup);
			if (!$sgroup)
			{
				$specialgroup = '';
			}
		}

		$services = $objS->getServices('jobs', 1, 1, 'ordering', 'ASC', $specialgroup);

		if (!$services)
		{
			// setup with default info
			$this->_setupServices();
		}

		// check available user funds (if paying with points)
		$BTL = new \Hubzero\Bank\Teller($subscription->uid);
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance;
		$funds   = ($funds > 0) ? $funds : '0';

		// Output HTML
		$this->view->title        = $this->_title;
		$this->view->config       = $this->config;
		$this->view->subscription = $subscription;
		$this->view->employer     = $employer;
		$this->view->services     = $services;
		$this->view->funds        = $funds;
		$this->view->uid          = $uid;
		$this->view->emp          = $this->_emp;
		$this->view->admin        = $this->_admin;
		$this->view->task         = $this->_task;
		$this->view->option       = $this->_option;

		// Set any errors
		if ($this->getError())
		{
			\Notify::error($this->getError());
		}

		$this->view->setName('subscribe')
					->setLayout('default')
					->display();
	}

	/**
	 * Subscription confirmation
	 *
	 * @return     void
	 */
	public function confirmTask()
	{
		// Login required
		if (User::isGuest())
		{
			\Notify::warning(Lang::txt('COM_JOBS_PLEASE_LOGIN_ACCESS_EMPLOYER'));
			$this->login();
			return;
		}

		$uid = Request::getInt('uid', User::get('id'));
		if ($uid && User::get('id') != $uid && !$this->_admin)
		{
			// not authorized
			App::abort(403, Lang::txt('COM_JOBS_ALERTNOTAUTH'));
		}

		$uid = $uid ? $uid : User::get('id');

		// Get the member's info
		$profile = User::getInstance($uid);

		// are we renewing?
		$subid       = Request::getInt('subid', 0);
		$sconfig     = Component::params('com_services');
		$autoapprove = $sconfig->get('autoapprove');

		// load Employer
		$employer = new Employer($this->database);
		if (!$employer->loadEmployer($uid))
		{
			$employer = new Employer($this->database);
			$employer->uid = $uid;
		}

		$subid = $employer->subscriptionid ? $employer->subscriptionid : $subid;

		$employer->companyName     = Request::getVar('companyName', $profile->get('organization'));
		$employer->companyLocation = Request::getVar('companyLocation', $profile->get('countryresident'));
		$employer->companyWebsite  = Request::getVar('companyWebsite', $profile->get('url'));

		if (!$employer->companyName || !$employer->companyLocation)
		{
			$this->setError(Lang::txt('Please make sure all required fields are filled in'));

			// send to subscription page
			App::redirect(
				Route::url('index.php?option=com_jobs&task=subscribe')
			);
			return;
		}

		// do we have a subscription already?
		$subscription = new Subscription($this->database);
		if (!$subscription->load($subid))
		{
			$subscription = new Subscription($this->database);
		}

		$serviceid = Request::getInt('serviceid', 0);

		// get service
		$service = new Service($this->database);

		if (!$serviceid or !$service->loadService('', $serviceid))
		{
			throw new Exception(Lang::txt('COM_JOBS_ERROR_SUBSCRIPTION_CHOOSE_SERVICE'), 500);
		}

		$units 		= Request::getInt('units_' . $serviceid, 0);
		$contact 	= Request::getVar('contact', '');
		$total 		= $service->unitprice * $units;
		$now 		= Date::toSql();
		$new 		= 0;
		$credit 	= 0;
		$months 	= $units * $service->unitsize;
		$newexprire = Date::of(strtotime('+' . $months . ' month'))->toSql();

		// we got an order
		if ($units)
		{
			if ($total && !$contact)
			{
				// need contact info with payment
				throw new Exception(Lang::txt('COM_JOBS_ERROR_SUBSCRIPTION_NO_PHONE'), 500);
			}

			$newunitcost  = $service->unitprice;

			if ($subid)
			{
				// get cost per unit (to compute required refund)
				$prevunitcost = $serviceid != $subscription->serviceid ? $service->getServiceCost($subscription->serviceid) : $newunitcost;
				$unitsleft 	  = 0;
				$refund		  = 0;

				// we are upgrading / downgrading - or replacing cancelled subscription
				if ($serviceid != $subscription->serviceid or $subscription->status==2)
				{
					if ($prevunitcost > 0 && $subscription->status != 2)
					{
						$unitsleft = $subscription->getRemaining('unit', $subscription, $service->maxunits, $service->unitsize);
						$refund = ($subscription->totalpaid > 0 && ($subscription->totalpaid - $unitsleft * $prevunitcost) < 0)
								? $unitsleft * $prevunitcost : 0;

						// calculate available credit - if upgrading
						if ($newunitcost > $prevunitcost)
						{
							$credit = 0; // TBD
						}
					}

					// cancel previous subscription & issue a refund if applicable
					if ($subscription->status != 2)
					{
						$subscription->cancelSubscription($subid, $refund, $unitsleft);
					}

					// enroll in new service
					$subscription = new Subscription($this->database);
					$new = 1;
				}
				else if ($subscription->expires > $now)
				{
					// extending?
					$subscription->status = $autoapprove && !$total ? 1 : $subscription->status;
					$subscription->status = $subscription->status == 2 ? 1 : $subscription->status;
					$subscription->units = $autoapprove && !$total ? $subscription->units + $units : $subscription->units;
					$subscription->pendingunits = $autoapprove && !$total ? 0 : $units;
					$subscription->pendingpayment = $autoapprove && !$total ? 0 : $units * $newunitcost;
					$newexprire = Date::of(strtotime('+' . $subscription->units * $service->unitsize . ' month'))->toSql();
					$subscription->expires = $newexprire;
					$subscription->updated = $now;
				}
				else
				{
					// expired - treat like new
					$new = 1;
					$subscription->updated = $now;
				}
			}
			else
			{
				// this is a new subscription
				$new = 1;
			}
		}

		// this is a new subscription
		if ($new)
		{
			$subscription->added          = $now;
			$subscription->status         = $autoapprove && !$total ? 1 : 0;
			$subscription->units          = $autoapprove && !$total ? $units : 0;
			$subscription->pendingunits   = $autoapprove && !$total ? 0 : $units;
			$subscription->pendingpayment = $autoapprove && !$total ? 0 : $units * $newunitcost;
			$subscription->pendingpayment = $credit ? $subscription->pendingpayment < $credit : $subscription->pendingpayment;
			$subscription->pendingpayment = $subscription->pendingpayment < 0 ? 0 : $subscription->pendingpayment;
			$subscription->expires        = $newexprire;
		}

		// save subscription information
		if ($units or $contact != $subscription->contact or !$subid or $serviceid != $subscription->serviceid)
		{
			$subscription->contact = $contact;
			$subscription->uid = $uid;
			$subscription->serviceid = $serviceid;

			if (!$subscription->id)
			{
				// get unique code
				$subscription->code = $subscription->generateCode();
			}
			if (!$subscription->check())
			{
				throw new Exception($subscription->getError(), 500);
			}
			if (!$subscription->store())
			{
				throw new Exception($subscription->getError(), 500);
			}
			if (!$subscription->id)
			{
				$subscription->checkin();
			}
		}

		// save employer information
		$employer->subscriptionid = $subscription->id;
		if (!$employer->store())
		{
			throw new Exception($employer->getError(), 500);
		}

		$this->_msg = $subid ? Lang::txt('COM_JOBS_MSG_SUBSCRIPTION_PROCESSED') : Lang::txt('COM_JOBS_MSG_SUBSCRIPTION_ACCEPTED');
		if ($units)
		{
			$this->_msg .= $autoapprove && !$total
					? ' ' . Lang::txt('COM_JOBS_MSG_SUBSCRIPTION_YOU_HAVE_ACCESS') . ' ' . $subscription->units . ' ' . Lang::txt('COM_JOBS_MONTHS')
					: ' ' . Lang::txt('COM_JOBS_MSG_SUBSCRIPTION_WE_WILL_CONTACT');
		}

		App::redirect(
			Route::url('index.php?option=com_jobs&task=dashboard'),
			$this->_msg
		);
		return;
	}

	/**
	 * Subscription cancellation
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Login required
		if (User::isGuest())
		{
			\Notify::warning(Lang::txt('COM_JOBS_PLEASE_LOGIN_ACCESS_EMPLOYER'));
			$this->login();
			return;
		}

		$uid = Request::getInt('uid', User::get('id'));

		// non-admins can only cancel their own subscription
		if ($uid && User::get('id') != $uid && !$this->_admin)
		{
			// not authorized
			App::abort(403, Lang::txt('COM_JOBS_ALERTNOTAUTH'));
		}
		$uid = $uid ? $uid : User::get('id');

		// load Employer
		$employer = new Employer($this->database);
		if (!$employer->loadEmployer($uid))
		{
			App::abort(404, Lang::txt('COM_JOBS_ERROR_EMPLOYER_NOT_FOUND'));
		}

		// load subscription to cancel
		$subscription = new Subscription($this->database);
		if (!$subscription->load($employer->subscriptionid))
		{
			App::abort(404, Lang::txt('COM_JOBS_ERROR_SUBSCRIPTION_NOT_FOUND'));
		}

		// get service
		$service = new Service($this->database);
		if (!$service->loadService('', $subscription->serviceid))
		{
			App::abort(404, Lang::txt('COM_JOBS_ERROR_SERVICE_NOT_FOUND'));
		}

		$refund    = 0;
		$unitsleft = $subscription->getRemaining('unit', $subscription, $service->maxunits, $service->unitsize);

		// get cost per unit (to compute required refund)
		$refund = ($subscription->totalpaid > 0 && $unitsleft > 0 && ($subscription->totalpaid - $unitsleft * $unitcost) > 0) ? $unitsleft * $prevunitcost : 0;

		// cancel previous subscription & issue a refund if applicable
		if ($subscription->cancelSubscription($employer->subscriptionid, $refund, $unitsleft))
		{
			\Notify::success(Lang::txt('COM_JOBS_MSG_SUBSCRIPTION_CANCELLED'));
		}

		App::redirect(
			Route::url('index.php?option=com_jobs')
		);
	}

	/**
	 * Dashboard
	 *
	 * @return     void
	 */
	public function dashboardTask()
	{
		// Login required
		if (User::isGuest())
		{
			\Notify::warning(Lang::txt('COM_JOBS_PLEASE_LOGIN_ACCESS_EMPLOYER'));
			$this->login();
			return;
		}

		// Incoming message
		$this->_msg_passed = $this->_msg_passed ? $this->_msg_passed : Request::getVar('msg', '');

		$uid = Request::getInt('uid', User::get('id'));
		if ($uid && User::get('id') != $uid && !$this->_admin)
		{
			// not authorized
			App::abort(403, Lang::txt('ALERTNOTAUTH'));
		}
		$uid = $uid ? $uid : User::get('id');
		$admin = $this->_admin && !$this->_emp && User::get('id') == $uid ? 1 : 0;

		// Make sure we have special admin subscription
		if ($admin)
		{
			$this->_authorizeEmployer(1);
		}

		// Get the member's info
		$profile = User::getInstance($uid);

		// load Employer
		$employer = new Employer($this->database);

		if (!$employer->loadEmployer($uid) && !$this->_admin)
		{
			// send to subscription page
			App::redirect(
				Route::url('index.php?option=com_jobs&task=subscribe')
			);
			return;
		}
		else if ($admin)
		{
			$employer->id = 1;
		}
		else if (!isset($employer->id))
		{
			App::abort(404, Lang::txt('COM_JOBS_ERROR_EMPLOYER_NOT_FOUND'));
		}

		// do we have a subscription already?
		$subscription = new Subscription($this->database);
		if (!$subscription->load($employer->subscriptionid) && !$this->_admin)
		{
			// send to subscription page
			App::redirect(
				Route::url('index.php?option=com_jobs&task=subscribe')
			);
			return;
		}

		$service = new Service($this->database);

		if (!$service->loadService('', $subscription->serviceid) && !$this->_admin)
		{
			App::abort(404, Lang::txt('COM_JOBS_ERROR_SERVICE_NOT_FOUND'));
		}
		else
		{
			// get service params like maxads
			$this->_getServiceParams($service);
		}

		// Get current stats for dashboard
		$jobstats = new JobStats($this->database);
		$stats = $jobstats->getStats($uid, 'employer', $admin);

		// Get job postings
		$job = new Job($this->database);
		$myjobs     = $job->get_my_openings($uid, 0, $admin);
		$activejobs = $job->countMyActiveOpenings($uid, 1, $admin);

		// Push some styles to the template
		$this->css();

		// Push some scripts to the template
		$this->js();

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view->title          = $this->_title;
		$this->view->config         = $this->config;
		$this->view->admin          = $this->_admin;
		$this->view->masterAdmin    = $this->_masterAdmin;
		$this->view->emp            = 1;
		$this->view->task           = $this->_task;
		$this->view->option         = $this->_option;
		$this->view->updated        = 0;
		$this->view->myjobs         = $myjobs;
		$this->view->activejobs     = $activejobs;
		$this->view->subscription   = $subscription;
		$this->view->employer       = $employer;
		$this->view->service        = $service;
		$this->view->login          = $profile->get('username');
		$this->view->uid            = $uid;
		$this->view->stats          = $stats;

		// Set any errors
		if ($this->getError())
		{
			\Notify::error($this->getError());
		}

		$this->view->setName('dashboard')
					->setLayout('default')
					->display();
	}

	/**
	 * Link to Add Resume (goes to profile "Resume" tab)
	 *
	 * @return     void
	 */
	public function addresumeTask()
	{
		// Login required
		if (User::isGuest())
		{
			$this->_msg = Lang::txt('COM_JOBS_MSG_LOGIN_RESUME');
			$this->login();
		}
		else
		{
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=resume')
			);
		}
	}

	/**
	 * Apply to a job
	 *
	 * @return     void
	 */
	public function applyTask()
	{
		// Incoming
		$code = Request::getVar('code', '');

		// Set page title
		$this->_buildTitle();

		$job = new Job($this->database);
		if (!$job->loadJob($code))
		{
			// Set the pathway
			if (Pathway::count() <= 0)
			{
				Pathway::append(
					Lang::txt(strtoupper($this->_name)),
					'index.php?option=' . $this->_option
				);
			}

			// Error view
			$this->setError(Lang::txt('COM_JOBS_ERROR_JOB_INACTIVE'));
			$view = new View(array('name' => 'error'));
			$view->title = Lang::txt(strtoupper($this->_name));
			if ($this->getError())
			{
				$view->setError($this->getError());
			}
			$view->display();
			return;
		}

		// Set the pathway
		$this->_jobCode  = $job->code;
		$this->_jobtitle = $job->title;
		$this->_buildPathway();

		// Push some styles to the template
		$this->css();

		// Login required
		if (User::isGuest())
		{
			$this->_msg = Lang::txt('COM_JOBS_MSG_LOGIN_APPLY');
			$this->login();
			return;
		}

		$ja = new JobApplication($this->database);

		// if application already exists, load it to edit
		if ($ja->loadApplication (User::get('id'), 0, $code) && $ja->status != 2)
		{
			$this->_task = 'editapp';
		}

		if ($this->_task != 'editapp')
		{
			$ja->cover = '';
		}

		$js = new JobSeeker($this->database);
		$seeker = $js->getSeeker(User::get('id'), User::get('id'));
		$seeker = count($seeker) > 0 ? $seeker[0] : NULL;

		// Output HTML
		$view = new View(array('name' => 'apply'));
		$view->title       = $this->_title;
		$view->config      = $this->config;
		$view->emp         = $this->_emp;
		$view->job         = $job;
		$view->seeker      = $seeker;
		$view->admin       = $this->_admin;
		$view->application = $ja;
		$view->task        = $this->_task;
		$view->option      = $this->_option;

		// Set any errors
		if ($this->getError())
		{
			\Notify::error($this->getError());
		}

		$view->display();
	}

	/**
	 * Save job application
	 *
	 * @return     void
	 */
	public function saveappTask()
	{
		// Incoming job id
		$code  = Request::getVar('code', '');
		$appid = Request::getInt('appid', 0, 'post');

		if (!$code)
		{
			$this->display();
			return;
		}

		// Login required
		if (User::isGuest())
		{
			$this->_msg = Lang::txt('COM_JOBS_MSG_LOGIN_SAVE_APPLICATION');
			$this->login();
			return;
		}

		$job = new Job($this->database);
		$ja  = new JobApplication($this->database);
		$now = Date::toSql();

		if (!$job->loadJob($code))
		{
			$this->setError(Lang::txt('COM_JOBS_ERROR_APPLICATION_ERROR'));
		}

		// Load application if exists
		if (!$ja->loadApplication(User::get('id'), 0, $code))
		{
			$ja = new JobApplication($this->database);
		}

		if ($this->_task == 'withdraw' && !$ja->id)
		{
			$this->setError(Lang::txt('COM_JOBS_ERROR_WITHDRAW_ERROR'));
		}

		// Save
		if (!$this->getError())
		{
			if ($this->_task == 'withdraw')
			{
				$ja->withdrawn = $now;
				$ja->status    = 2;
				$ja->reason    = Request::getVar('reason', '');
			}
			else
			{
				// Save new information
				$ja->bind($_POST);
				$ja->applied = $appid ? $ja->applied : $now;
				$ja->status  = 1;
			}

			if (!$ja->store())
			{
				throw new \Exception($ja->getError(), 500);
				return;
			}
			else
			{
				$this->_msg = $this->_task == 'withdraw' ? Lang::txt('COM_JOBS_MSG_APPLICATION_WITHDRAWN') : Lang::txt('COM_JOBS_MSG_APPLICATION_ACCEPTED');
				$this->_msg = $appid ? Lang::txt('COM_JOBS_MSG_APPLICATION_EDITS_ACCEPTED') : $this->_msg_passed;
				\Notify::success($this->_msg);
			}
		}

		// Set any errors
		if ($this->getError())
		{
			\Notify::error($this->getError());
		}

		// return to the job posting
		App::redirect(
			Route::url('index.php?option=com_jobs&task=job&code=' . $job->code)
		);
		return;
	}

	/**
	 * Job posting
	 *
	 * @return     void
	 */
	public function jobTask()
	{
		// Incoming
		$code = Request::getVar('code', '');
		$code = !$code && $this->_jobCode ? $this->_jobCode : $code;

		$obj = new Job($this->database);
		$job = $obj->get_opening (0, User::get('id'), $this->_masterAdmin, $code);

		// Push some styles to the template
		$this->css();

		// Push some scripts to the template
		$this->js();

		if (!$job)
		{
			$this->setError(Lang::txt('COM_JOBS_ERROR_JOB_INACTIVE'));

			// Set the pathway
			if (Pathway::count() <= 0)
			{
				Pathway::append(
					Lang::txt(strtoupper($this->_name)),
					'index.php?option=' . $this->_option
				);
			}

			// Output HTML
			$view = new View(array('name'=>'error'));
			$view->title = Lang::txt(strtoupper($this->_name));
			if ($this->getError())
			{
				$view->setError($this->getError());
			}
			$view->display();
			return;
		}

		if (User::get('id') == $job->employerid && !$this->_emp && !$this->_masterAdmin)
		{
			// check validity of subscription
			App::redirect(
				Route::url('index.php?option=com_jobs&task=dashboard'),
				Lang::txt('COM_JOBS_WARNING_SUBSCRIPTION_INVALID'),
				'warning'
			);
			return;
		}

		// Set the pathway
		$this->_jobid = $job->id;
		$this->_jobtitle = $job->title;
		$this->_buildPathway();

		if (User::isGuest() && $job->status != 1)
		{
			// Not authorized
			$error  = Lang::txt('COM_JOBS_ERROR_NOT_AUTHORIZED_JOB_VIEW');
			$error .= User::isGuest() ? ' ' . Lang::txt('COM_JOBS_WARNING_LOGIN_REQUIRED') : '';
			$this->setError($error);

			// Output HTML
			$view = new View(array('name' => 'error'));
			$view->title = Lang::txt(strtoupper($this->_name));
			if ($this->getError())
			{
				$view->setError($this->getError());
			}
			$view->display();
			return;
		}
		if ($job->status != 1 && !$this->_admin && (!$this->_emp && User::get('id') != $job->employerid))
		{
			// Not authorized
			App::abort(403, Lang::txt('COM_JOBS_ERROR_NOT_AUTHORIZED_JOB_VIEW'));
		}

		// Set page title
		$this->_subtitle = $job->status==4 ? Lang::txt('COM_JOBS_ACTION_PREVIEW_AD') . ' ' . $job->code : $job->title;
		$this->_buildTitle();

		// Get category & type names
		$jt = new JobType($this->database);
		$jc = new JobCategory($this->database);
		$job->type = $jt->getType($job->type);
		$job->cat = $jc->getCat($job->cid);

		// Get applications
		$ja = new JobApplication($this->database);
		$job->applications = ($this->_admin or ($this->_emp && User::get('id') == $job->employerid)) ? $ja->getApplications ($job->id) : array();

		// Get profile info of applicants
		$job->withdrawnlist = array();
		if (count($job->applications) > 0)
		{
			$js = new JobSeeker($this->database);
			foreach ($job->applications as $ap)
			{
				$seeker = $js->getSeeker($ap->uid, $job->employerid);
				$ap->seeker = (!$seeker or count($seeker)==0) ? NULL : $seeker[0];

				if ($ap->status == 2)
				{
					$job->withdrawnlist[] = $ap;
				}
			}
		}

		// Output HTML
		$this->view->title          = $this->_title;
		$this->view->config         = $this->config;
		$this->view->emp            = $this->_emp;
		$this->view->job            = $job;
		$this->view->admin          = $this->_admin;
		$this->view->task           = $this->_task;
		$this->view->option         = $this->_option;

		// Set any errors
		if ($this->getError())
		{
			\Notify::error($this->getError());
		}

		$this->view->setName('job')
					->setLayout('default')
					->display();
	}

	/**
	 * Save job
	 *
	 * @return     void
	 */
	public function savejobTask()
	{
		// Incoming
		$employerid = Request::getInt('employerid', 0);
		$min = ($this->_task == 'confirmjob'
				or $this->_task == 'unpublish'
				or $this->_task == 'reopen'
				or $this->_task == 'remove') ? 1 : 0;
		$code = $this->_jobCode ? $this->_jobCode : Request::getVar('code', '');

		// Login required
		if (User::isGuest())
		{
			\Notify::warning(Lang::txt('COM_JOBS_PLEASE_LOGIN_ACCESS_EMPLOYER'));
			$this->login();
			return;
		}

		// Do we need admin approval for job publications?
		$autoapprove = $this->config->get('autoapprove', 1);

		$job      = new Job($this->database);
		$jobadmin = new JobAdmin($this->database);
		$employer = new Employer($this->database);

		if ($code)
		{
			if (!$job->loadJob($code))
			{
				App::abort(404, Lang::txt('COM_JOBS_ERROR_JOB_NOT_FOUND'));
			}

			// check if user is authorized to edit
			if ($this->_admin
			 or $jobadmin->isAdmin(User::get('id'), $job->id)
			 or User::get('id') == $job->employerid)
			{
				// we are editing
				$code = $job->code;
			}
			else
			{
				App::abort(403, Lang::txt('COM_JOBS_ALERTNOTAUTH'));
			}

			$job->editedBy = User::get('id');
			$job->edited   = Date::toSql();
		}
		else
		{
			$job->added   = Date::toSql();
			$job->addedBy = User::get('id');
		}

		$employerid = $code ? $job->employerid : $employerid;
		$job->employerid = $employerid;

		// load Employer
		if (!$employer->loadEmployer($employerid))
		{
			App::abort(404, Lang::txt('COM_JOBS_ERROR_EMPLOYER_NOT_FOUND'));
		}

		// check validity of subscription
		if (User::get('id') == $job->employerid && !$this->_emp && !$this->_masterAdmin)
		{
			App::redirect(
				Route::url('index.php?option=com_jobs&task=dashboard'),
				Lang::txt('COM_JOBS_WARNING_SUBSCRIPTION_INVALID'),
				'warning'
			);
			return;
		}

		if (!$min)
		{
			$job->description     = rtrim(stripslashes($_POST['description']));
			$job->title           = rtrim(stripslashes($_POST['title']));
			$job->companyName     = rtrim(stripslashes($_POST['companyName']));
			$job->companyLocation = rtrim(stripslashes($_POST['companyLocation']));
			$applyInternal        = Request::getInt('applyInternal', 0);
			$applyExternalUrl     = Request::getVar('applyExternalUrl', '');

			// missing required information
			if (!$job->description or !$job->title or !$job->companyName or !$job->companyLocation)
			{
				$job->bind($_POST);
				$this->_job     = $job;
				$this->_jobCode = $code;
				$this->setError(Lang::txt('COM_JOBS_ERROR_MISSING_INFORMATION'));
				$this->editjobTask();
				return;
			}
		}

		$job->companyLocationCountry = $job->companyLocationCountry ? $job->companyLocationCountry : NULL;

		// Save new information
		if (!$min)
		{
			$job->bind($_POST);
			$job->description   	= rtrim(stripslashes($_POST['description']));
			$job->title   			= rtrim(stripslashes($_POST['title']));
			$job->companyName   	= rtrim(stripslashes($_POST['companyName']));
			$job->companyLocation   = rtrim(stripslashes($_POST['companyLocation']));
			$job->applyInternal		= Request::getInt('applyInternal', 0);
			$job->applyExternalUrl	= Request::getVar('applyExternalUrl', '');

		}
		else if ($job->status==4 && $this->_task == 'confirmjob')
		{
			// make sure we aren't over quota
			$allowedAds = $this->_masterAdmin && $employerid==1 ? 1 : $this->_checkQuota($job);

			if ($allowedAds <=0)
			{
				$this->setError(Lang::txt('COM_JOBS_ERROR_JOB_CANT_PUBLISH_OVER_LIMIT'));
			}
			else
			{
				// confirm
				$job->status       = !$autoapprove && !$this->_masterAdmin ? 0 : 1;
				$job->opendate     = !$autoapprove && !$this->_masterAdmin ? '' : Date::toSql(); // set open date as of now, if confirming new ad publication
				$this->_msg        = !$autoapprove && !$this->_masterAdmin ? Lang::txt('COM_JOBS_MSG_SUCCESS_JOB_PENDING_APPROVAL') : Lang::txt('COM_JOBS_MSG_SUCCESS_JOB_POSTED');
				\Notify::success($this->_msg);
			}
		}
		elseif ($job->status==1 && $this->_task == 'unpublish')
		{
			$job->status = 3;
			\Notify::warning(Lang::txt('COM_JOBS_MSG_JOB_UNPUBLISHED'));
		}
		elseif ($job->status==3 && $this->_task == 'reopen')
		{
			// make sure we aren't over quota
			$allowedAds = $this->_masterAdmin && $employerid==1 ? 1 : $this->_checkQuota($job);

			if ($allowedAds <= 0)
			{
				$this->setError(Lang::txt('COM_JOBS_ERROR_JOB_CANT_REOPEN_OVER_LIMIT'));
			}
			else
			{
				$job->status = 1;
				\Notify::success(Lang::txt('COM_JOBS_MSG_JOB_REOPENED'));
			}
		}
		elseif ($this->_task == 'remove')
		{
			$job->status = 2;
		}

		// get unique number code for this new job posting
		if (!$code)
		{
			$subscription = new Subscription($this->database);
			$code         = $subscription->generateCode(8, 8, 0, 1, 0);
			$job->code    = $code;
		}

		if (!$job->store())
		{
			throw new Exception($job->getError(), 500);
		}
		if (!$job->id)
		{
			$job->checkin();
		}

		if ($this->_task == 'remove')
		{
			App::redirect(
				Route::url('index.php?option=com_jobs&task=dashboard'),
				Lang::txt('COM_JOBS_MSG_JOB_REMOVED')
			);
			return;
		}

		// Set any errors
		if ($this->getError())
		{
			\Notify::error($this->getError());
		}

		App::redirect(
			Route::url('index.php?option=com_jobs&task=job&code=' . $job->code)
		);
	}

	/**
	 * Add/edit job form
	 *
	 * @return     void
	 */
	public function editjobTask()
	{
		$live_site = rtrim(Request::base(), '/');

		// Incoming
		$code  = Request::getVar('code', '');
		$empid = $this->_admin ? 1 : User::get('id');
		$code  = !$code && $this->_jobCode ? $this->_jobCode : $code;

		// Login required
		if (User::isGuest())
		{
			\Notify::warning(Lang::txt('COM_JOBS_PLEASE_LOGIN_ACCESS_EMPLOYER'));
			$this->login();
			return;
		}

		$job = new Job($this->database);
		$jobadmin = new JobAdmin($this->database);
		$employer = new Employer($this->database);

		if (!$this->_emp && !$this->_admin)
		{
			// need to subscribe first
			$employer = new Employer($this->database);
			if ($employer->loadEmployer($empid))
			{
				//do we have a pending subscription?
				$subscription = new Subscription($this->database);
				if ($subscription->loadSubscription($employer->subscriptionid, User::get('id'), '', $status=array(0)))
				{
					App::redirect(
						Route::url('index.php?option=com_jobs&task=dashboard'),
						Lang::txt('COM_JOBS_WARNING_SUBSCRIPTION_PENDING'),
						'warning'
					);
					return;
				}
			}

			// send to subscription page
			App::redirect(
				Route::url('index.php?option=com_jobs&task=subscribe')
			);
			return;
		}

		if ($code)
		{
			if (!$job->loadJob($code))
			{
				App::abort(404, Lang::txt('COM_JOBS_ERROR_JOB_NOT_FOUND'));
			}
			// check if user is authorized to edit
			if ($this->_admin
			 or $jobadmin->isAdmin(User::get('id'), $job->id)
			 or User::get('id') == $job->employerid)
			{
				// we are editing
				$code = $job->code;
			}
			else
			{
				App::abort(403, Lang::txt('COM_JOBS_ALERTNOTAUTH'));
			}
		}

		// display with errors
		if ($this->_job)
		{
			$job = $this->_job;
		}

		$uid = $code ? $job->employerid : User::get('id');
		$job->admins = $code ? $jobadmin->getAdmins($job->id) : array(User::get('id'));

		// Get the member's info
		$profile = User::getInstance($uid);

		// load Employer
		if (!$employer->loadEmployer($uid) && !$this->_admin)
		{
			App::abort(404, Lang::txt('COM_JOBS_ERROR_EMPLOYER_NOT_FOUND'));
		}
		else if (!$employer->id && $this->_admin)
		{
			$employer->uid = 1;
			$employer->subscriptionid  = 1;
			$employer->companyName     = Config::get('sitename');
			$employer->companyLocation = '';
			$employer->companyWebsite  = $live_site;
			$uid = 1; // site admin
		}

		// Push some styles to the template
		$this->css();

		// Push some scripts to the template
		$this->js();

		// Push some styles to the tmeplate
		$this->css('calendar.css');

		$jt = new JobType($this->database);
		$jc = new JobCategory($this->database);

		// get job types
		$types = $jt->getTypes();
		$types[0] = Lang::txt('COM_JOBS_TYPE_ANY');

		// get job categories
		$cats = $jc->getCats();
		$cats[0] = Lang::txt('COM_JOBS_CATEGORY_NO_SPECIFIC');

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_jobid = $job->id;
		$this->_jobtitle = $job->title;
		$this->_buildPathway();

		// Output HTML
		$this->view->title    = $this->_title;
		$this->view->config   = $this->config;
		$this->view->uid      = $uid;
		$this->view->profile  = $profile;
		$this->view->emp      = $this->_emp;
		$this->view->job      = $job;
		$this->view->jobid    = $job->id;
		$this->view->types    = $types;
		$this->view->cats     = $cats;
		$this->view->employer = $employer;
		$this->view->admin    = $this->_admin;
		$this->view->task     = $this->_task;
		$this->view->option   = $this->_option;

		// Set any errors
		if ($this->getError())
		{
			\Notify::error($this->getError());
		}

		$this->view->setName('editjob')
					->setLayout('default')
					->display();
	}

	/**
	 * Check if a user has employer authorization
	 *
	 * @param      integer $admin If user is admin or not
	 * @return     void
	 */
	protected function _authorizeEmployer($admin = 0)
	{
		$emp = 0;

		$employer = new Employer($this->database);
		if ($admin)
		{
			$adminemp = $employer->isEmployer(User::get('id'), 1);
			if (!$adminemp)
			{
				// will require setup only once
				$subscription = new Subscription($this->database);
				$subscription->status    = 1;
				$subscription->uid       = 1;
				$subscription->units     = 72;
				$subscription->serviceid = 1;
				$subscription->expires   = Date::of(strtotime("+ 72 months"))->toSql();
				$subscription->added     = Date::toSql();

				if (!$subscription->store())
				{
					throw new Exception($subscription->getError(), 500);
				}

				if (!$subscription->id)
				{
					$subscription->checkin();
				}

				// make sure we have dummy admin employer account
				$live_site = rtrim(Request::base(), '/');

				$employer->uid = 1;
				$employer->subscriptionid  = $subscription->id;
				$employer->companyName     = Config::get('sitename');
				$employer->companyLocation = '';
				$employer->companyWebsite  = $live_site;

				// save employer information
				if (!$employer->store())
				{
					throw new Exception($employer->getError(), 500);
				}
			}
		}
		else
		{
			$emp = $employer->isEmployer(User::get('id'));
		}

		$this->_emp = $emp;
	}

	/**
	 * Check if a user is an administrator
	 *
	 * @param      integer $admin Optional default value to pass
	 * @return     void
	 */
	protected function _authorizeAdmin($admin = 0)
	{
		if (!User::isGuest())
		{
			// Check if they're a site admin (from Joomla)
			$this->config->set('access-admin-component', User::authorise('core.admin', null));
			$this->config->set('access-manage-component', User::authorise('core.manage', null));
			if ($this->config->get('access-admin-component') || $this->config->get('access-manage-component'))
			{
				$admin = 1;
			}

			// check if they belong to a dedicated admin group
			$admingroup = $this->config->get('admingroup', '');
			if ($admingroup)
			{
				$ugs = \Hubzero\User\Helper::getGroups(User::get('id'));
				if ($ugs && count($ugs) > 0)
				{
					foreach ($ugs as $ug)
					{
						if ($ug->cn == $admingroup)
						{
							$admin = 1;
						}
					}
				}
			}
		}
		$this->_admin = $admin;
	}

	/**
	 * Search Preferences
	 *
	 * @param      string $category Preferences category
	 * @return     void
	 */
	protected function _updatePrefs($category = 'resume')
	{
		// Incoming
		$saveprefs  = Request::getInt('saveprefs', 0, 'post');

		$p = new Prefs($this->database);

		$filters = $this->_getFilters (0, 0, 0);
		if ($category == 'job')
		{
			$filters['sortby'] = trim(Request::getVar('sortby', 'title'));
		}

		$text = 'filterby=' . $filters['filterby'] . '&amp;match=1&amp;search='
				. $filters['search'] . '&amp;category=' . $filters['category']
				. '&amp;type=' . $filters['type'] . '&amp;sortby=';

		if ($category == 'job' && isset($_GET['performsearch']))
		{
			$text .= $filters['sortby'];
			if (!$p->loadPrefs(User::get('id'), $category))
			{
				$p = new Prefs($this->database);
				$p->uid = User::get('id');
				$p->category = $category;
			}
			$p->filters = $text;

			// Store content
			if (!$p->store())
			{
				throw new Exception($p->getError(), 500);
			}
		}
		else
		{
			if ($saveprefs && isset($_POST['performsearch']))
			{
				if (!$p->loadPrefs(User::get('id'), $category))
				{
					$p = new Prefs($this->database);
					$p->uid      = User::get('id');
					$p->category = $category;
					$text       .= 'bestmatch';
				}
				else
				{
					$text .= $filters['sortby'];
				}

				$p->filters = $text;

				// Store content
				if (!$p->store())
				{
					throw new Exception($p->getError(), 500);
				}
			}
			elseif ($p->loadPrefs(User::get('id'), $category) && isset($_POST["performsearch"]))
			{
				// delete prefs
				$p->delete();
			}
		}
	}

	/**
	 * Get a user's preferences
	 *
	 * @param      string $category Preferences category
	 * @return     void
	 */
	protected function _getPrefs($category = 'resume')
	{
		$p = new Prefs($this->database);
		if ($p->loadPrefs(User::get('id'), $category))
		{
			if (isset($p->filters) && $p->filters)
			{
				// get individual filters
				$col = explode('&amp;', $p->filters);

				if (count($col > 0))
				{
					foreach ($col as $c)
					{
						$nuk = explode('=', $c);

						// set filter variables
						$this->setVar($nuk[0], $nuk[1]);
					}
				}
			}
		}
	}

	/**
	 * Search filters
	 *
	 * @param      integer $admin       Administrator?
	 * @param      integer $emp         Employer ID
	 * @param      integer $checkstored Check internal property?
	 * @param      integer $jobs        Are filters for jobs?
	 * @return     array
	 */
	protected function _getFilters($admin = 0, $emp = 0, $checkstored = 1, $jobs = 0)
	{
		// Query filters defaults
		$filters = array();

		// jobs filters
		if ($jobs)
		{
			$filters['sortby']   = $this->getVar('sortby') && $checkstored
								 ? $this->getVar('sortby', 'title') : trim(Request::getVar('sortby', 'title'));
			$filters['category'] = $this->getVar('category') && $checkstored
								 ? $this->getVar('category') : Request::getInt('category',  'all');
		}
		else
		{
			$filters['sortby']   = $this->getVar('sortby') && $checkstored
								 ? $this->getVar('sortby') : trim(Request::getVar('sortby', 'lastupdate'));
			$filters['category'] = $this->getVar('category') && $checkstored
								 ? $this->getVar('category') : Request::getInt('category',  0);
		}

		$filters['type']     = $this->getVar('type') && $checkstored ? $this->getVar('type') : Request::getInt('type',  0);
		$filters['search']   = $this->getVar('search') && $checkstored ? $this->getVar('search') : trim(Request::getVar('q', ''));
		$filters['filterby'] = trim(Request::getVar('filterby', 'all'));
		$filters['sortdir']  = Request::getVar('sortdir', 'ASC');

		// did we get stored prefs?
		$filters['match']    = $this->getVar('match') && $checkstored ? $this->getVar("match") : Request::getInt('match', 0);

		// Paging vars
		$filters['limit']    = Request::getInt('limit', $this->config->get('jobslimit'));
		$filters['start']    = Request::getInt('limitstart', 0, 'get');

		// admins and employers
		$filters['admin']   = $admin;
		$filters['emp']     = $emp;

		// Return the array
		return $filters;
	}

	/**
	 * Batch resume download
	 *
	 * @return     void
	 */
	public function batchTask()
	{
		// Login required
		if (User::isGuest())
		{
			\Notify::warning(Lang::txt('COM_JOBS_PLEASE_LOGIN_ACCESS_EMPLOYER'));
			$this->login();
			return;
		}

		// Check authorization
		if (!$this->_admin && !$this->_emp)
		{
			App::redirect(
				Route::url('index.php?option=com_jobs&task=subscribe')
			);
		}

		// Incoming
		$pile = Request::getVar('pile', 'all');

		// Zip the requested resumes
		$archive = $this->_archiveResumes($pile);

		if ($archive)
		{
			// Initiate a new content server and serve up the file
			$xserver = new \Hubzero\Content\Server();
			$xserver->filename($archive['path']);

			$xserver->disposition('attachment');
			$xserver->acceptranges(false);
			$xserver->saveas(Lang::txt('JOBS_RESUME_BATCH=Resume Batch'));
			$result = $xserver->serve_attachment($archive['path'], $archive['name'], false);

			// Delete downloaded zip
			\Filesystem::delete($archive['path']);

			if (!$result)
			{
				throw new Exception(Lang::txt('COM_JOBS_ERROR_ARCHIVE_FAILED'), 500);
			}
			else
			{
				exit;
			}
		}
		else
		{
			App::redirect(
				Route::url('index.php?option=com_jobs&task=dashboard'),
				Lang::txt('COM_JOBS_ERROR_ARCHIVE_FAILED'), 'error'
			);
		}
	}

	/**
	 * Create resume archive
	 *
	 * @param      string $pile Resumes to return
	 * @return     mixed File path if successful, false if not
	 */
	private function _archiveResumes($pile = 'all')
	{
		// Get available resume files
		$resume = new Resume($this->database);
		$files  = $resume->getResumeFiles($pile, User::get('id'), $this->_masterAdmin);
		$batch  = array();

		if (count($files) > 0)
		{
			if (!extension_loaded('zip'))
			{
				throw new Exception(Lang::txt('COM_JOBS_ERROR_MISSING_PHP_LIBRARY'), 500);
			}

			$pile .= $pile != 'all' ? '_' . User::get('id') : '';
			$zipname = Lang::txt('Resumes') . '_' . $pile . '.zip';

			$mconfig = Component::params('com_members');
			$base_path = $mconfig->get('webpath', '/site/members');

			if ($base_path)
			{
				$base_path = DS . trim($base_path, DS);
			}

			$base_path .= DS . \Hubzero\Utility\String::pad(User::get('id'));

			$i = 0;

			$zip = new ZipArchive;
			if ($zip->open(PATH_APP . $base_path . DS . $zipname, ZipArchive::OVERWRITE) === TRUE)
			{
				foreach ($files as $avalue => $alabel)
				{
					$apath = Event::trigger('members.build_path', array($avalue));
					$path  = is_array($apath) ? $apath[0] : '';
					$file = $path ? PATH_APP . $path . DS . $alabel : '';

					if (!is_file($file))
					{
						continue;
					}

					$zip->addFile($file, basename($file));
					$i++;
				}

				$zip->close();
			}
			else
			{
				App::redirect(
					Route::url('index.php?option=com_jobs&task=dashboard'),
					Lang::txt('COM_JOBS_ERROR_ARCHIVE_FAILED'), 'error'
				);
			}

			if ($i == 0)
			{
				App::redirect(
					Route::url('index.php?option=com_jobs&task=dashboard'),
					Lang::txt('COM_JOBS_ERROR_ARCHIVE_FAILED'), 'error'
				);
			}
			else
			{
				$archive = array();
				$archive['path'] = PATH_APP . $base_path . DS . $zipname;
				$archive['name'] = $zipname;
				return $archive;
			}
		}

		return false;
	}

	/**
	 * Check job ad quota depending on subscription
	 *
	 * @param      object $job      Job
	 * @return     integer
	 */
	protected function _checkQuota($job)
	{
		// make sure we aren't over quota
		$service = new Service($this->database);
		$servicename = $service->getUserService(User::get('id'));
		if (!$service->loadService($servicename))
		{
			return 0;
		}
		else
		{
			$this->_getServiceParams($service);
			$maxads      = $service->maxads > 0 ? $service->maxads : 1;
			$activejobs  = $job->countMyActiveOpenings(User::get('id'), 1);
			$allowedAds = $maxads - $activejobs;
			return $allowedAds;
		}
	}

	/**
	 * Initial setup of default services
	 *
	 * @return     boolean False if errors, true otherwise
	 */
	protected function _setupServices()
	{
		$objS = new Service($this->database);
		$now = Date::toSql();

		$default1 = array(
			'id'          => 0,
			'title'       => Lang::txt('Employer Service, Basic'),
			'category'    => strtolower(Lang::txt('jobs')),
			'alias'       => Lang::txt('employer_basic'),
			'status'      => 1,
			'description' => Lang::txt('Allows to search member resumes and post one job ad'),
			'unitprice'   => '0.00',
			'pointprice'  => 0,
			'currency'    => '$',
			'maxunits'    => 6,
			'minunits'    => 1,
			'unitsize'    => 1,
			'unitmeasure' => strtolower(Lang::txt('month')),
			'changed'     => $now,
			'params'      => "promo=First 3 months FREE\npromomaxunits=3\nmaxads=1"
		);
		$default2 = array(
			'id'          => 0,
			'title'       => Lang::txt('Employer Service, Premium'),
			'category'    => strtolower(Lang::txt('jobs')),
			'alias'       => Lang::txt('employer_premium'),
			'status'      => 0,
			'description' => Lang::txt('Allows to search member resumes and post up to 3 job ads'),
			'unitprice'   => '500.00',
			'pointprice'  => 0,
			'currency'    => '$',
			'maxunits'    => 6,
			'minunits'    => 1,
			'unitsize'    => 1,
			'unitmeasure' => strtolower(Lang::txt('month')),
			'changed'     => $now,
			'params'      => "promo=\npromomaxunits=\nmaxads=3"
		);

		if (!$objS->bind($default1))
		{
			App::redirect(
				Route::url('index.php?option=com_jobs'),
				$objS->getError(), 'error'
			);
		}

		if (!$objS->store())
		{
			App::redirect(
				Route::url('index.php?option=com_jobs'),
				$objS->getError(), 'error'
			);
		}

		if (!$objS->bind($default2))
		{
			App::redirect(
				Route::url('index.php?option=com_jobs'),
				$objS->getError(), 'error'
			);
		}

		if (!$objS->store())
		{
			App::redirect(
				Route::url('index.php?option=com_jobs'),
				$objS->getError(), 'error'
			);
		}

		return true;
	}

	/**
	 * Get service params
	 *
	 * @param      object &$service Service
	 * @return     void
	 */
	protected function _getServiceParams(&$service)
	{
		$params = new \Hubzero\Config\Registry($service->params);
		$service->maxads = $params->get('maxads', '');
		$service->maxads = intval(str_replace(' ', '', $service->maxads));
	}
}

