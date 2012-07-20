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

// No direct access
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Short description for 'JobsController'
 * 
 * Long description (if any) ...
 */
class JobsController extends Hubzero_Controller
{

	/**
	 * Short description for 'setVar'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function setVar($property, $value)
	{
		$this->$property = $value;
	}

	/**
	 * Short description for 'getVar'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function getVar($property)
	{
		return $this->$property;
	}

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function execute()
	{
		// Load the component config
		if (!$this->config->get('component_enabled')) {
			// Redirect to home page
			$this->_redirect = '/';
			return;
		}

		$this->_banking = $this->config->get('banking', 0);
		$this->_industry = $this->config->get('industry', '');
		$this->_allowsubscriptions 	= $this->config->get('allowsubscriptions', 0);
		// Get admin priviliges
		JobsController::authorize_admin();

		// Get employer priviliges
		if ($this->_allowsubscriptions) {
			JobsController::authorize_employer();
		} else {
			$this->_emp = 0;
		}

		// Set component administrator priviliges
		$this->_masteradmin = $this->_admin && !$this->_emp ? 1 : 0;

		$this->_task    = JRequest::getVar('task', '');
		$this->_jobcode = JRequest::getVar('code', '');

		switch ($this->_task)
		{
			case 'browse':    		$this->view();    		break;
			case 'job':    			$this->job();    		break;
			case 'resumes':   		$this->resumes();    	break;
			case 'view':    		$this->view();  		break;

			// job seekers
			case 'addresume':     	$this->addresume();    	break;
			case 'apply':  			$this->apply();    		break;
			case 'saveapp':  		$this->saveapp();    	break;
			case 'withdraw':  		$this->saveapp();    	break;
			case 'editapp':  		$this->apply();    		break;

			//employers
			case 'addjob':     		$this->editjob();    	break;
			case 'savejob':     	$this->savejob();    	break;
			case 'confirmjob':     	$this->savejob();    	break;
			case 'unpublish':     	$this->savejob();    	break;
			case 'reopen':     		$this->savejob();    	break;
			case 'remove':     		$this->savejob();    	break;
			case 'editjob':     	$this->editjob();    	break;
			case 'shortlist':  		$this->shortlist();    	break;
			case 'dashboard':  		$this->dashboard();    	break;
			case 'batch':  			$this->batch();    		break;

			// subscription management
			case 'subscribe':  		$this->subscribe();    	break;
			case 'confirm':  		$this->confirm();    	break;
			case 'cancel':  		$this->cancel();    	break;

			// Should only be called via AJAX
			case 'plugin':     		$this->plugin();     	break;

			default: $this->_task = 'view';  $this->view();  break;
		}
	}

	//----------------------------------------------------------
	// Private functions
	//----------------------------------------------------------

	/**
	 * Short description for '_buildTitle'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function _buildTitle()
	{
		$this->_title = JText::_(strtoupper($this->_option));
		$this->_title.= $this->_industry ? ' '.JText::_('IN').' '.$this->_industry : '';
		if ($this->_subtitle) {
			$this->_title .= ': '.$this->_subtitle;
		} else if ($this->_task && $this->_task != 'all' && $this->_task != 'view') {
			$this->_title .= ': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		}
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Short description for '_buildPathway'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function _buildPathway()
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();

		$comtitle = JText::_(strtoupper($this->_option));
		$comtitle.= $this->_industry ? ' '.JText::_('IN').' '.$this->_industry : '';

		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				$comtitle,
				'index.php?option='.$this->_option
			);
		}
		if ($this->_task) {
			switch ($this->_task)
			{
				case 'browse':
					$pathway->addItem(
						JText::_(strtoupper($this->_option).'_BROWSE'),
						'index.php?option=' . $this->_option . '&task=browse'
					);
				break;
				case 'all':
					if (!$this->_allowsubscriptions) {
						$pathway->addItem(
							JText::_(strtoupper($this->_option).'_BROWSE'),
							'index.php?option=' . $this->_option . '&task=browse'
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
					$pathway->addItem($this->_jobtitle, 'index.php?option=' . $this->_option . '&task=job&code='.$this->_jobcode );
					$pathway->addItem(JText::_(strtoupper($this->_option).'_APPLY'), 'index.php?option=' . $this->_option . '&task=apply&code='.$this->_jobcode);
				break;
				case 'editapp':
					$pathway->addItem($this->_jobtitle, 'index.php?option=' . $this->_option . '&task=job&code='.$this->_jobcode );
					$pathway->addItem(JText::_(strtoupper($this->_option).'_EDITAPP'), 'index.php?option=' . $this->_option . '&task=apply&code='.$this->_jobcode);
				break;
				case 'job':
					$pathway->addItem($this->_jobtitle, 'index.php?option=' . $this->_option . '&task=job&code='.$this->_jobcode );
				break;
				case 'editjob':
					$pathway->addItem($this->_jobtitle, 'index.php?option=' . $this->_option . '&task=job&code='.$this->_jobcode );
					$pathway->addItem(JText::_(strtoupper($this->_option).'_EDITJOB'), 'index.php?option=' . $this->_option . '&task=editjob&code='.$this->_jobcode);
				break;
				default:
					$pathway->addItem(
						JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
						'index.php?option=' . $this->_option . '&task='.$this->_task
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
		$rtrn = jRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');
		$this->setRedirect(
			JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn))
		);
	}

	/**
	 * Call a plugin
	 * NOTE: This view should only be called through AJAX
	 * 
	 * @return     string HTML
	 */
	protected function plugin()
	{
		// Incoming
		$trigger = trim(JRequest::getVar('trigger', ''));

		// Ensure we have a trigger
		if (!$trigger) 
		{
			echo Hubzero_View_Helper_Html::error(JText::_('ERROR_NO_TRIGGER_FOUND'));
			return;
		}

		// Get Members plugins
		JPluginHelper::importPlugin('members', 'resume');
		$dispatcher =& JDispatcher::getInstance();

		// Call the trigger
		$results = $dispatcher->trigger($trigger, array());
		if (is_array($results)) 
		{
			$html = $results[0]['html'];
		}

		// Output HTML
		echo $html;
	}

	/**
	 * Short description for 'shortlist'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function shortlist()
	{
		// Shortlisted user
		$oid  = JRequest::getInt('oid', 0);

		// Get Members plugins
		JPluginHelper::importPlugin('members', 'resume');
		$dispatcher =& JDispatcher::getInstance();

		// Call the trigger
		$results = $dispatcher->trigger('shortlist', array($oid, $ajax=0));

		// Go back to the page
		$referer = JRequest::getVar('HTTP_REFERER', NULL, 'server'); // What page they came from
		$this->_redirect = $referer;
	}

	/**
	 * Introductory page/ Jobs list
	 * 
	 * @return     void
	 */
	public function view()
	{
		// Push some styles to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts();

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();

		// Do we need to list only jobs from a specified employer?
		$subscriptioncode = JRequest::getVar('employer', '');
		$employer = new Employer ($database);
		$thisemployer = $subscriptioncode ? $employer->getEmployer(0, $subscriptioncode) : '';

		// get action
		$action = JRequest::getVar('action', '');
		if ($action == 'login' && $juser->get('guest')) {
			$this->_msg = JText::_('MSG_PLEASE_LOGIN_OPTIONS');
			$this->login();
			return;
		}

		if (!$juser->get('guest') && ($this->_task == 'browse' or !$this->_allowsubscriptions)) {
			// save incoming prefs
			$this->updatePrefs($database, $juser, 'job');

			// get stored preferences
			$this->getPrefs($database, $juser, 'job');
		}

		// Get filters
		$filters = $this->getFilters($this->_admin, 0 , 1 , 1);
		$filters['active'] = 1; // only show jobs that have open/unexpired search close date

		// Get data
		$obj = new Job($database);

		// Get jobs
		$adminoptions = ($this->_task != 'browse' && $this->_allowsubscriptions)  ? 0 : $this->_admin;
		$jobs = $obj->get_openings ($filters, $juser->get('id'), $adminoptions, $subscriptioncode);
		
		$total = $obj->get_openings ($filters, $juser->get('id'), $adminoptions, $subscriptioncode, 1);

		// Initiate paging 
		jimport('joomla.html.pagination');
		$jtotal = ($this->_task != 'browse' && $this->_allowsubscriptions) ? count($jobs) : $total;
		$pageNav = new JPagination($jtotal, $filters['start'], $filters['limit']);

		// Output HTML
		if ($this->_task != 'browse' && $this->_allowsubscriptions) {
			// Component introduction
			$view = new JView(array('name'=>'intro'));
			$view->title = $this->_title;
			$view->config = $this->config;
			$view->option = $this->_option;
			$view->emp = $this->_emp;
			$view->guest = $juser->get('guest');
			$view->admin = $this->_admin;
			$view->masteradmin = $this->_masteradmin;
			$view->pageNav = $pageNav;
			$view->allowsubscriptions = $this->_allowsubscriptions;
			$view->msg = $this->_msg;
			$view->display();
		}
		// Jobs list
		$view = new JView(array('name'=>'jobs'));
		$view->title = $this->_title;
		$view->config = $this->config;
		$view->option = $this->_option;
		$view->emp = $this->_emp;
		$view->guest = $juser->get('guest');
		$view->admin = $this->_admin;
		$view->masteradmin = $this->_masteradmin;
		$view->total = $jtotal;
		$view->pageNav = $pageNav;
		$view->allowsubscriptions = $this->_allowsubscriptions;
		$view->jobs = $jobs;
		$view->mini = ($this->_task == 'browse' or !$this->_allowsubscriptions) ? 0 : 1;
		$view->database = $database;
		$view->filters = $filters;
		$view->subscriptioncode = $subscriptioncode;
		$view->thisemployer = $thisemployer;
		$view->task = $this->_task;
		$view->display();
		return;
	}

	/**
	 * List of candidates
	 * 
	 * @return     void
	 */
	public function resumes()
	{
		// Push some styles to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts();

		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();

		// Login required
		if ($juser->get('guest')) {
			if ($this->_allowsubscriptions) {
				$this->intro_employer();
			} else {
				$this->login();
			}
			return;
		}

		if ($this->_admin or $this->_emp) {
			// Set page title
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway();

			// save incoming prefs
			$this->updatePrefs($database, $juser);

			// get stored preferences
			$this->getPrefs($database, $juser);

			// get filters
			$filters = JobsController::getFilters($this->_admin, $this->_emp);

			// get job types
			$jt = new JobType($database);
			$types = $jt->getTypes();
			$types[0] = JText::_('TYPE_ANY');

			// get job categories
			$jc = new JobCategory($database);
			$cats = $jc->getCats();
			$cats[0] = JText::_('CATEGORY_ANY');

			// get users with resumes
			$js = new JobSeeker($database);
			$seekers = $js->getSeekers($filters, $juser->get('id'), 0, $this->_masteradmin);
			$total   = $js->countSeekers($filters, $juser->get('id'), 0, $this->_masteradmin);

			// Initiate paging 
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($total, $filters['start'], $filters['limit']);

			// Output HTML
			$view 				= new JView(array('name'=>'resumes'));
			$view->config 		= $this->config;
			$view->admin 		= $this->_admin;
			$view->masteradmin 	= $this->_masteradmin;
			$view->title 		= $this->_title;
			$view->seekers 		= $seekers;
			$view->pageNav 		= $pageNav;
			$view->cats 		= $cats;
			$view->types 		= $types;
			$view->filters 		= $filters;
			$view->emp 			= $this->_emp;
			$view->option 		= $this->_option;
			$view->display();
		} else if ($this->_allowsubscriptions) {
			// need to subscribe first
			$employer = new Employer($database);
			if ($employer->loadEmployer($juser->get('id'))) {

				//do we have a pending subscription?
				$subscription = new Subscription($database);
				if ($subscription->loadSubscription($employer->subscriptionid, $juser->get('id'), '', $status=array(0))) {
					$this->_msg_warning = JText::_('WARNING_SUBSCRIPTION_PENDING');
					$this->dashboard();
					return;
				}
			}

			// send to subscription page
			$this->_task = 'newsubscribe';
			$this->subscribe();
		} else {
			$this->view();
		}
	}

	/**
	 * Subscription form
	 * 
	 * @return     void
	 */
	protected function subscribe()
	{
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();

		// Login required
		if ($juser->get('guest')) {
			$this->intro_employer();
			return;
		}

		// are we viewing other person's subscription? (admins only)
		$uid = JRequest::getInt('uid', 0);

		if ($uid && $juser->get('id') != $uid && !$this->_admin) {
			// not authorized
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
			return;
		}
		$uid = $uid ? $uid : $juser->get('id');

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts();

		// Get the member's info
		$profile = new Hubzero_User_Profile();
		$profile->load($uid);

		// load Employer
		$employer = new Employer($database);
		if (!$employer->loadEmployer($uid)) {
			$employer 					= new Employer($database);
			$employer->uid 				= $uid;
			$employer->subscriptionid 	= 0;
			$employer->companyName 		= $profile->get('organization');
			$employer->companyLocation 	= $profile->get('countryresident');
			$employer->companyWebsite 	= $profile->get('url');
		}

		// do we have an active subscription already?
		$subscription = new Subscription($database);
		if (!$subscription->loadSubscription ($employer->subscriptionid, '', '', $status=array(0, 1))) {
			$subscription = new Subscription($database);
			$subscription->uid = $uid;
			$subscription->serviceid = 0;
		}

		// get subscription options
		$objS = new Service($database);
		$specialgroup = $this->config->get('specialgroup', '');
		if($specialgroup)
		{
			ximport('Hubzero_Group');
			$sgroup = Hubzero_Group::getInstance($specialgroup);
			if(!$sgroup)
			{
				$specialgroup = '';
			}
		}
		
		$services = $objS->getServices('jobs', 1, 1, 'ordering', 'ASC', $specialgroup);

		if (!$services) {
			// setup with default info
			$this->setupServices();
		}

		// check available user funds (if paying with points)
		$BTL = new Hubzero_Bank_Teller($database, $subscription->uid);
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance;
		$funds   = ($funds > 0) ? $funds : '0';

		// Output HTML
		$view = new JView(array('name'=>'subscribe'));
		$view->title = $this->_title;
		$view->config = $this->config;
		$view->subscription = $subscription;
		$view->allowsubscriptions = $this->_allowsubscriptions;
		$view->employer = $employer;
		$view->services = $services;
		$view->funds = $funds;
		$view->uid = $uid;
		$view->emp = $this->_emp;
		$view->admin = $this->_admin;
		$view->task = $this->_task;
		$view->option = $this->_option;
		if ($this->getError()) {
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Subscription confirmation
	 * 
	 * @return     void
	 */
	protected function confirm()
	{
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();

		// Login required
		if ($juser->get('guest')) {
			$this->intro_employer();
			return;
		}

		$uid = JRequest::getInt('uid', $juser->get('id'));
		if ($uid && $juser->get('id') != $uid && !$this->_admin) {
			// not authorized
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
			return;
		}
		$uid = $uid ? $uid : $juser->get('id');

		// Get the member's info
		$profile = new Hubzero_User_Profile();
		$profile->load($uid);

		// are we renewing?
		$subid 	= JRequest::getInt('subid', 0);
		$sconfig =& JComponentHelper::getParams('com_services');
		$autoapprove = $sconfig->get('autoapprove');

		// load Employer
		$employer = new Employer($database);
		if (!$employer->loadEmployer($uid)) {
			$employer = new Employer($database);
			$employer->uid = $uid;
		}

		$subid = $employer->subscriptionid ? $employer->subscriptionid : $subid;

		$employer->companyName 		= JRequest::getVar('companyName', $profile->get('organization'));
		$employer->companyLocation 	= JRequest::getVar('companyLocation', $profile->get('countryresident'));
		$employer->companyWebsite 	= JRequest::getVar('companyWebsite', $profile->get('url'));

		// do we have a subscription already?
		$subscription = new Subscription($database);
		if (!$subscription->load($subid)) {
			$subscription = new Subscription($database);
		}

		$serviceid 	= JRequest::getInt('serviceid', 0);
		// get service
		$service = new Service($database);

		if (!$serviceid or !$service->loadService('', $serviceid)) {
			JError::raiseError(500, JText::_('ERROR_SUBSCRIPTION_CHOOSE_SERVICE'));
			return;
		}

		$units 		= JRequest::getInt('units_'.$serviceid, 0);
		$contact 	= JRequest::getVar('contact', '');
		$total 		= $service->unitprice * $units;
		$now 		= date('Y-m-d H:i:s', time());
		$new 		= 0;
		$credit 	= 0;

		// we got an order
		if ($units) {
			$months = $units * $service->unitsize;
			$newexprire = date("Y-m-d",strtotime("+".$months."months"));

			if ($total && !$contact) {
				// need contact info with payment
				JError::raiseError(500, JText::_('ERROR_SUBSCRIPTION_NO_PHONE'));
				return;
			}

			$newunitcost  = $service->unitprice;

			if ($subid) {
				// get cost per unit (to compute required refund)
				$prevunitcost = $serviceid != $subscription->serviceid ? $service->getServiceCost($subscription->serviceid) : $newunitcost;
				$unitsleft 	  = 0;
				$refund		  = 0;

				// we are upgrading / downgrading - or replacing cancelled subscription
				if ($serviceid != $subscription->serviceid or $subscription->status==2) {
					if ($prevunitcost > 0 && $subscription->status != 2) {
						$unitsleft = $subscription->getRemaining('unit', $subscription, $service->maxunits, $service->unitsize);
						$refund = ($subscription->totalpaid > 0 && ($subscription->totalpaid - $unitsleft * $prevunitcost) < 0) ? $unitsleft * $prevunitcost : 0;

						// calculate available credit - if upgrading
						if ($newunitcost > $prevunitcost) {
							$credit = 0; // TBD
						}
					}

					// cancel previous subscription & issue a refund if applicable
					if ($subscription->status != 2) {
						$subscription->cancelSubscription($subid, $refund, $unitsleft);
					}

					// enroll in new service
					$subscription = new Subscription($database);
					$new = 1;
				} else if ($subscription->expires > $now) {	// extending?
					$subscription->status = $autoapprove && !$total ? 1 : $subscription->status;
					$subscription->status = $subscription->status == 2 ? 1 : $subscription->status;
					$subscription->units = $autoapprove && !$total ? $subscription->units + $units : $subscription->units;
					$subscription->pendingunits = $autoapprove && !$total ? 0 : $units;
					$subscription->pendingpayment = $autoapprove && !$total ? 0 : $units * $newunitcost;
					$newexprire = date("Y-m-d",strtotime("+".$subscription->units * $service->unitsize ."months"));
					$subscription->expires = $autoapprove && !$total ? $newexprire : $subscription->expires;
					$subscription->updated = $now;
				} else {
					// expired - treat like new
					$new = 1;
					$subscription->updated = $now;
				}
			} else {
				// this is a new subscription
				$new = 1;
			}
		}

		// this is a new subscription
		if ($new) {
			$subscription->added = $now;
			$subscription->status = $autoapprove && !$total ? 1 : 0; // activate if no funds are expected
			$subscription->units = $autoapprove && !$total ? $units : 0;
			$subscription->pendingunits = $autoapprove && !$total ? 0 : $units;
			$subscription->pendingpayment = $autoapprove && !$total ? 0 : $units * $newunitcost;
			$subscription->pendingpayment = $credit ? $subscription->pendingpayment < $credit : $subscription->pendingpayment;
			$subscription->pendingpayment = $subscription->pendingpayment < 0 ? 0 : $subscription->pendingpayment;
			$subscription->expires = $autoapprove && !$total ? $newexprire : NULL;
		}

		// save subscription information
		if ($units or $contact != $subscription->contact or !$subid or $serviceid != $subscription->serviceid) 
		{
			$subscription->contact = $contact;
			$subscription->uid = $uid;
			$subscription->serviceid = $serviceid;

			if (!$subscription->id) {
				// get unique code
				$subscription->code = $subscription->generateCode();
			}
			if (!$subscription->check()) {
				JError::raiseError(500, $subscription->getError());
				return;
			}
			if (!$subscription->store()) {
				JError::raiseError(500, $subscription->getError());
				return;
			}
			if (!$subscription->id) {
				$subscription->checkin();
			}
		}

		// save employer information
		$employer->subscriptionid = $subscription->id;
		if (!$employer->store()) {
			JError::raiseError(500, $employer->getError());
			return;
		}

		$this->_msg = $subid ? JText::_('MSG_SUBSCRIPTION_PROCESSED') : JText::_('MSG_SUBSCRIPTION_ACCEPTED');
		if ($units) {
			$this->_msg .= $autoapprove && !$total
					? ' '.JText::_('MSG_SUBSCRIPTION_YOU_HAVE_ACCESS').' '.$subscription->units.' '.JText::_('MONTHS')
					: ' '.JText::_('MSG_SUBSCRIPTION_WE_WILL_CONTACT');
		}

		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&task=dashboard&msg='.$this->_msg);
		return;
	}

	/**
	 * Subscription cancellation
	 * 
	 * @return     void
	 */
	protected function cancel()
	{
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();

		// Login required
		if ($juser->get('guest')) 
		{
			$this->intro_employer();
			return;
		}

		$uid = JRequest::getInt('uid', $juser->get('id'));

		// non-admins can only cancel their own subscription
		if ($uid && $juser->get('id') != $uid && !$this->_admin) 
		{
			// not authorized
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
			return;
		}
		$uid = $uid ? $uid : $juser->get('id');

		// load Employer
		$employer = new Employer($database);
		if (!$employer->loadEmployer($uid)) {
			JError::raiseError(404, JText::_('ERROR_EMPLOYER_NOT_FOUND'));
			return;
		}

		// load subscription to cancel
		$subscription = new Subscription($database);
		if (!$subscription->load($employer->subscriptionid)) 
		{
			JError::raiseError(404, JText::_('ERROR_SUBSCRIPTION_NOT_FOUND'));
			return;
		}

		// get service
		$service = new Service($database);
		if (!$service->loadService('', $subscription->serviceid)) 
		{
			JError::raiseError(404, JText::_('ERROR_SERVICE_NOT_FOUND'));
			return;
		}

		$refund    = 0;
		$unitsleft = $subscription->getRemaining('unit', $subscription, $service->maxunits, $service->unitsize);

		// get cost per unit (to compute required refund)	
		$refund = ($subscription->totalpaid > 0 && $unitsleft > 0 && ($subscription->totalpaid - $unitsleft * $unitcost) > 0) ? $unitsleft * $prevunitcost : 0;

		// cancel previous subscription & issue a refund if applicable
		if ($subscription->cancelSubscription($employer->subscriptionid, $refund, $unitsleft)) 
		{
			$this->_msg = JText::_('MSG_SUBSCRIPTION_CANCELLED');
			$this->view();
			return;
		}
		$this->view();
	}

	/**
	 * Dashboard
	 * 
	 * @return     void
	 */
	protected function dashboard()
	{
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();

		// Login required
		if ($juser->get('guest')) 
		{
			$this->intro_employer();
			return;
		}

		// Incoming message
		$this->_msg_passed = $this->_msg_passed ? $this->_msg_passed : JRequest::getVar('msg', '');

		$uid = JRequest::getInt('uid', $juser->get('id'));
		if ($uid && $juser->get('id') != $uid && !$this->_admin) 
		{
			// not authorized
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
			return;
		}
		$uid = $uid ? $uid : $juser->get('id');
		$admin = $this->_admin && !$this->_emp && $juser->get('id') == $uid ? 1 : 0;

		// Make sure we have special admin subscription
		if ($admin) 
		{
			$this->authorize_employer(1);
		}

		// Get the member's info
		$profile = new Hubzero_User_Profile();
		$profile->load($uid);

		// load Employer
		$employer = new Employer($database);

		if (!$employer->loadEmployer($uid) && !$this->_admin) 
		{
			// send to subscription page
			$this->_task = 'newsubscribe';
			$this->subscribe();
			return;
		} 
		else if ($admin) 
		{
			$employer->id = 1;
		} 
		else if (!isset($employer->id)) 
		{
			JError::raiseError(404, JText::_('ERROR_EMPLOYER_NOT_FOUND'));
			return;
		}

		// do we have a subscription already?
		$subscription = new Subscription($database);
		if (!$subscription->load($employer->subscriptionid) && !$this->_admin) 
		{
			// send to subscription page
			$this->_task = 'newsubscribe';
			$this->subscribe();
			return;
		}

		$service = new Service($database);

		if (!$service->loadService('', $subscription->serviceid) && !$this->_admin) 
		{
			JError::raiseError(404, JText::_('ERROR_SERVICE_NOT_FOUND'));
			return;
		} 
		else 
		{
			// get service params like maxads
			$this->getServiceParams(&$service);
		}

		// Get current stats for dashboard
		$jobstats = new JobStats($database);
		$stats = $jobstats->getStats($uid, 'employer', $admin);

		// Get job postings
		$job 		= new Job($database);
		$myjobs 	= $job->get_my_openings($uid, 0, $admin);
		$activejobs = $job->countMyActiveOpenings ($uid, 1, $admin);

		// Push some styles to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts();

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$view =  new JView(array('name'=>'dashboard'));
		$view->title = $this->_title;
		$view->config = $this->config;
		$view->admin = $this->_admin;
		$view->masteradmin = $this->_masteradmin;
		$view->emp = 1;
		$view->task = $this->_task;
		$view->option = $this->_option;
		$view->updated = 0;
		$view->msg_passed = $this->_msg_passed;
		$view->msg_warning = $this->_msg_warning;
		$view->myjobs = $myjobs;
		$view->activejobs = $activejobs;
		$view->subscription = $subscription;
		$view->employer = $employer;
		$view->service = $service;
		$view->login = $profile->get('username');
		$view->uid = $uid;
		$view->stats = $stats;
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Intro screen for employers before they login
	 * 
	 * @return     void
	 */
	protected function intro_employer()
	{
		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

		// Output HTML
		$view = new JView(array('name'=>'introemp'));
		$view->title = $this->_title;
		$view->config = $this->config;
		$view->task = $this->_task;
		$view->option = $this->_option;
		$view->banking = $this->_banking;
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}
		$view->display();

		return;
	}

	/**
	 * Link to Add Resume (goes to profile "Resume" tab)
	 * 
	 * @return     void
	 */
	public function addresume()
	{
		$juser =& JFactory::getUser();

		// Login required
		if ($juser->get('guest')) {
			$this->_msg = JText::_('MSG_LOGIN_RESUME');
			$this->login();
		} else {
			$this->_redirect = JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=resume');
			return;
		}
	}

	/**
	 * Apply to a job
	 * 
	 * @return     void
	 */
	public function apply()
	{
		$database =& JFactory::getDBO();
		$juser    =& JFactory::getUser();

		// Incoming
		$code = JRequest::getVar('code', '');

		// Set page title
		$this->_buildTitle();

		$job = new Job($database);
		if (!$job->loadJob($code)) {
			// Set the pathway
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			if (count($pathway->getPathWay()) <= 0) {
				$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
			}

			// Error view
			$this->setError(JText::_('ERROR_JOB_INACTIVE'));
			$view = new JView(array('name'=>'error'));
			$view->title = JText::_(strtoupper($this->_name));
			if ($this->getError()) {
				$view->setError($this->getError());
			}
			$view->display();
			return;
		}

		// Set the pathway
		$this->_jobcode = $job->code;
		$this->_jobtitle = $job->title;
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

		// Login required
		if ($juser->get('guest')) {
			$this->_msg = JText::_('MSG_LOGIN_APPLY');
			$this->login();
			return;
		}

		$ja = new JobApplication($database);

		// if application already exists, load it to edit
		if ($ja->loadApplication ($juser->get('id'), 0, $code) && $ja->status != 2) {
			$this->_task = 'editapp';
		}

		if ($this->_task != 'editapp') {
			$ja->cover = '';
		}

		$js = new JobSeeker($database);
		$seeker = $js->getSeeker($juser->get('id'), $juser->get('id'));
		$seeker = count($seeker) > 0 ? $seeker[0] : NULL;

		// Output HTML
		$view = new JView(array('name'=>'apply'));
		$view->title = $this->_title;
		$view->config = $this->config;
		$view->emp = $this->_emp;
		$view->job = $job;
		$view->seeker = $seeker;
		$view->admin = $this->_admin;
		$view->masteradmin = $this->_masteradmin;
		$view->allowsubscriptions = $this->_allowsubscriptions;
		$view->error = $this->_error;
		$view->application = $ja;
		$view->task = $this->_task;
		$view->option = $this->_option;
		if ($this->getError()) {
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Save job application
	 * 
	 * @return     void
	 */
	public function saveapp()
	{
		$database =& JFactory::getDBO();
		$juser    =& JFactory::getUser();

		// Incoming job id
		$code  = JRequest::getVar('code', '');
		$appid = JRequest::getInt('appid', 0, 'post');

		if (!$code) {
			$this->view();
			return;
		}

		// Login required
		if ($juser->get('guest')) {
			$this->_msg = JText::_('MSG_LOGIN_SAVE_APPLICATION');
			$this->login();
			return;
		}

		$job = new Job ($database);
		$ja = new JobApplication ($database);
		$now = date('Y-m-d H:i:s', time());

		if (!$job->loadJob($code)) {
			$this->setError(JText::_('ERROR_APPLICATION_ERROR'));
		} 
		
		// Load application if exists
		if(!$ja->loadApplication ($juser->get('id'), 0, $code))
		{
			$ja = new JobApplication ($database);
		}
		
		if($this->_task == 'withdraw' && !$ja->id)
		{
			$this->setError(JText::_('ERROR_WITHDRAW_ERROR'));
		}
		
		// Save
		if(!$this->getError()) {
			if ($this->_task == 'withdraw') {
				$ja->withdrawn 	= $now;
				$ja->status 	= 2;
				$ja->reason 	= JRequest::getVar('reason', '');
			} else {
				// Save new information
				$ja->bind($_POST);
				$ja->applied = $appid ? $ja->applied : $now;
				$ja->status =	1;
			}

			if (!$ja->store()) {
				JError::raiseError(500, $ja->getError());
				return;
			}
			else {
				$this->_msg_passed 	= $this->_task == 'withdraw' ? JText::_('MSG_APPLICATION_WITHDRAWN') : JText::_('MSG_APPLICATION_ACCEPTED');
				$this->_msg_passed  = $appid ? JText::_('MSG_APPLICATION_EDITS_ACCEPTED') : $this->_msg_passed;
			}
		}

		// return to the job posting
		$this->_jobcode = $job->code;
		$this->job();
		return;
	}

	/**
	 * Job posting
	 * 
	 * @return     void
	 */
	public function job()
	{
		$database =& JFactory::getDBO();
		$juser    =& JFactory::getUser();

		// Incoming
		$code = JRequest::getVar('code', '');
		$code = !$code && $this->_jobcode ? $this->_jobcode : $code;

		$obj = new Job($database);
		$job = $obj->get_opening (0, $juser->get('id'), $this->_masteradmin, $code);

		// Push some styles to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts();

		if (!$job) {
			$this->setError(JText::_('ERROR_JOB_INACTIVE'));

			// Set the pathway
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			if (count($pathway->getPathWay()) <= 0) {
				$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
			}

			// Output HTML
			$view = new JView(array('name'=>'error'));
			$view->title = JText::_(strtoupper($this->_name));
			if ($this->getError()) {
				$view->setError($this->getError());
			}
			$view->display();
			return;
		}

		if ($juser->get('id') == $job->employerid && !$this->_emp && !$this->_masteradmin) {
			// check validity of subscription
			$this->_msg_warning = JText::_('WARNING_SUBSCRIPTION_INVALID');
			$this->dashboard();
			return;
		}

		// Set the pathway
		$this->_jobid = $job->id;
		$this->_jobtitle = $job->title;
		$this->_buildPathway();

		if ($juser->get('guest') && $job->status != 1) {
			// Not authorized
			$error = JText::_('ERROR_NOT_AUTHORIZED_JOB_VIEW');
			$error.= $juser->get('guest') ? ' '.JText::_('WARNING_LOGIN_REQUIRED'): '';
			$this->setError($error);

			// Output HTML
			$view = new JView(array('name'=>'error'));
			$view->title = JText::_(strtoupper($this->_name));
			if ($this->getError()) {
				$view->setError($this->getError());
			}
			$view->display();
			return;
		}
		if ($job->status != 1 && !$this->_admin && (!$this->_emp && $juser->get('id') != $job->employerid)) {
			// Not authorized
			JError::raiseError(403, JText::_('ERROR_NOT_AUTHORIZED_JOB_VIEW'));
			return;
		}

		// Set page title
		$this->_subtitle = $job->status==4 ? JText::_('ACTION_PREVIEW_AD').' '.$job->code : $job->title;
		$this->_buildTitle();

		// Get category & type names
		$jt = new JobType($database);
		$jc = new JobCategory($database);
		$job->type = $jt->getType($job->type);
		$job->cat = $jc->getCat($job->cid);

		// Get applications
		$ja = new JobApplication($database);
		$job->applications = ($this->_admin or ($this->_emp && $juser->get('id') == $job->employerid)) ? $ja->getApplications ($job->id) : array();

		// Get profile info of applicants
		$job->withdrawnlist = array();
		if (count($job->applications) > 0) {
			$js = new JobSeeker($database);
			foreach ($job->applications as $ap)
			{
				$seeker = $js->getSeeker($ap->uid, $job->employerid);
				$ap->seeker = (!$seeker or count($seeker)==0) ? NULL : $seeker[0];

				if ($ap->status == 2) {
					$job->withdrawnlist[] = $ap;
				}
			}
		}

		// Output HTML
		$view = new JView(array('name'=>'job'));
		$view->title = $this->_title;
		$view->config = $this->config;
		$view->emp = $this->_emp;
		$view->job = $job;
		$view->msg_warning = $this->_msg_warning;
		$view->msg_passed = $this->_msg_passed;
		$view->admin = $this->_admin;
		$view->masteradmin = $this->_masteradmin;
		$view->allowsubscriptions = $this->_allowsubscriptions;
		$view->error = $this->_error;
		$view->task = $this->_task;
		$view->option = $this->_option;
		if ($this->getError()) {
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Save job
	 * 
	 * @return     void
	 */
	public function savejob()
	{
		$database =& JFactory::getDBO();
		$juser    =& JFactory::getUser();

		// Incoming
		$employerid = JRequest::getInt('employerid', 0);
		$min = ($this->_task == 'confirmjob' 
				or $this->_task == 'unpublish' 
				or $this->_task == 'reopen' 
				or $this->_task == 'remove') ? 1 : 0;
		$code = $this->_jobcode ? $this->_jobcode : JRequest::getVar('code', '');

		// Login required
		if ($juser->get('guest')) {
			$this->intro_employer();
			return;
		}

		// Do we need admin approval for job publications?
		$autoapprove = $this->config->get('autoapprove')  ? $this->config->get('autoapprove') : 1;

		$job = new Job($database);
		$jobadmin = new JobAdmin($database);
		$employer = new Employer($database);

		if ($code) {
			if (!$job->loadJob($code)) {
				JError::raiseError(404, JText::_('ERROR_JOB_NOT_FOUND'));
				return;
			}

			// check if user is authorized to edit		
			if ($this->_admin or $jobadmin->isAdmin($juser->get('id'), $job->id) 
			or $juser->get('id') == $job->employerid) 
			{
				// we are editing
				$code = $job->code;
			} else 
			{
				JError::raiseError(403, JText::_('ALERTNOTAUTH'));
				return;
			}

			$job->editedBy = $juser->get('id');
			$job->edited = date('Y-m-d H:i:s');
		} else {
			$job->added = date('Y-m-d H:i:s');
			$job->addedBy = $juser->get('id');
		}

		$employerid = $code ? $job->employerid : $employerid;
		$job->employerid = $employerid;

		// load Employer
		if (!$employer->loadEmployer($employerid)) {
			JError::raiseError(404, JText::_('ERROR_EMPLOYER_NOT_FOUND'));
			return;
		}

		// check validity of subscription
		if ($juser->get('id') == $job->employerid && !$this->_emp && !$this->_masteradmin) {
			$this->_msg_warning = JText::_('WARNING_SUBSCRIPTION_INVALID');
			$this->dashboard();
			return;
		}

		if (!$min) {
			$job->description   	= rtrim(stripslashes($_POST['description']));
			$job->title   			= rtrim(stripslashes($_POST['title']));
			$job->companyName   	= rtrim(stripslashes($_POST['companyName']));
			$job->companyLocation   = rtrim(stripslashes($_POST['companyLocation']));
			$applyInternal			= JRequest::getInt('applyInternal', 0);
			$applyExternalUrl		= JRequest::getVar('applyExternalUrl', '');
			
			// Need at least one way to apply to a job
			//$job->applyInternal    	= ($applyInternal or !$applyExternalUrl) ? 1 : 0;

			// missing required information
			if (!$job->description or !$job->title or !$job->companyName or !$job->companyLocation) {
				$job->bind($_POST);
				$this->_job 	= $job;
				$this->_jobcode = $code;
				$this->setError(JText::_('ERROR_MISSING_INFORMATION'));
				$this->editjob();
				return;
			}
		}

		$job->companyLocationCountry = $job->companyLocationCountry ? $job->companyLocationCountry : JText::_('JOBS_DEFAULT_COUNTRY') ;

		// Save new information
		if (!$min) {
			$job->bind($_POST);
			$job->description   	= rtrim(stripslashes($_POST['description']));
			$job->title   			= rtrim(stripslashes($_POST['title']));
			$job->companyName   	= rtrim(stripslashes($_POST['companyName']));
			$job->companyLocation   = rtrim(stripslashes($_POST['companyLocation']));
			$job->applyInternal		= JRequest::getInt('applyInternal', 0);
			$job->applyExternalUrl	= JRequest::getVar('applyExternalUrl', '');
			
		} else if ($job->status==4 && $this->_task == 'confirmjob') {
			// make sure we aren't over quota			
			$allowed_ads = $this->_masteradmin && $employerid==1 ? 1 : $this->checkQuota($job, $juser, $database);

			if ($allowed_ads <=0) {
				$this->setError(JText::_('ERROR_JOB_CANT_PUBLISH_OVER_LIMIT'));
			} else {
				// confirm 
				$job->status 	= !$autoapprove && !$this->_masteradmin ? 0 : 1;
				$job->opendate 	= !$autoapprove && !$this->_masteradmin ? '' : date('Y-m-d H:i:s'); // set open date as of now, if confirming new ad publication
				$this->_msg_passed 	= !$autoapprove && !$this->_masteradmin ? JText::_('MSG_SUCCESS_JOB_PENDING_APPROVAL') : JText::_('MSG_SUCCESS_JOB_POSTED');
			}
		} else if ($job->status==1 && $this->_task == 'unpublish') {
			$job->status = 3;
			$this->_msg_warning = JText::_('MSG_JOB_UNPUBLISHED');
		} else if ($job->status==3 && $this->_task == 'reopen') {
			// make sure we aren't over quota			
			$allowed_ads = $this->_masteradmin && $employerid==1 ? 1 : $this->checkQuota($job, $juser, $database);

			if ($allowed_ads <= 0) {
				$this->setError(JText::_('ERROR_JOB_CANT_REOPEN_OVER_LIMIT'));
			}
			else {
				$job->status = 1;
				$this->_msg_passed = JText::_('MSG_JOB_REOPENED');
			}
		} else if ($this->_task == 'remove') {
			$job->status = 2;
		}

		// get unique number code for this new job posting
		if (!$code) {
			$subscription = new Subscription($database);
			$code = $subscription->generateCode(8, 8, 0, 1, 0);
			$job->code = $code;
		}

		if (!$job->store()) {
			JError::raiseError(500, $job->getError());
			return;
		}
		if (!$job->id) {
			$job->checkin();
		}

		if ($this->_task == 'remove') {
			$this->_msg_passed = JText::_('MSG_JOB_REMOVED');
			$this->dashboard();
			return;
		}

		$this->_jobcode = $job->code;
		$this->job();
	}

	/**
	 * Add/edit job form
	 * 
	 * @return     void
	 */
	public function editjob()
	{
		$database 	=& JFactory::getDBO();
		$juser    	=& JFactory::getUser();
		$jconfig 	=& JFactory::getConfig();
		$live_site = rtrim(JURI::base(),'/');
		
		// Incoming
		$code 	= JRequest::getVar('code', '');
		$empid  = $this->_admin ? 1 : $juser->get('id');
		$code = !$code && $this->_jobcode ? $this->_jobcode : $code;

		// Login required
		if ($juser->get('guest')) {
			if($this->_allowsubscriptions) { $this->intro_employer();	}
			else { $this->login(); }
			return;
		}

		$job = new Job($database);
		$jobadmin = new JobAdmin($database);
		$employer = new Employer($database);

		if (!$this->_emp && !$this->_admin) {
			// need to subscribe first
			$employer = new Employer($database);
			if ($employer->loadEmployer($empid)) {

				//do we have a pending subscription?
				$subscription = new Subscription($database);
				if ($subscription->loadSubscription($employer->subscriptionid, $juser->get('id'), '', $status=array(0))) {
					$this->_msg_warning = JText::_('WARNING_SUBSCRIPTION_PENDING');
					$this->dashboard();
					return;
				}
			}

			// send to subscription page
			$this->_task = 'newsubscribe';
			$this->subscribe();
			return;
		}

		if ($code) {
			if (!$job->loadJob($code)) {
				JError::raiseError(404, JText::_('ERROR_JOB_NOT_FOUND'));
				return;
			}
			// check if user is authorized to edit		
			if ($this->_admin or $jobadmin->isAdmin($juser->get('id'), $job->id) or $juser->get('id') == $job->employerid) {
				// we are editing
				$code = $job->code;
			} else {
				JError::raiseError(403, JText::_('ALERTNOTAUTH'));
				return;
			}
		}

		// display with errors
		if ($this->_job) {
			$job = $this->_job;
		}

		$uid = $code ? $job->employerid : $juser->get('id');
		$job->admins = $code ? $jobadmin->getAdmins($job->id) : array($juser->get('id'));

		// Get the member's info
		$profile = new Hubzero_User_Profile();
		$profile->load($uid);

		// load Employer
		if (!$employer->loadEmployer($uid) && !$this->_admin) {
			JError::raiseError(404, JText::_('ERROR_EMPLOYER_NOT_FOUND'));
			return;
		} else if (!$employer->id && $this->_admin) {
			$employer->uid = 1;
			$employer->subscriptionid = 1;
			$employer->companyName 		= $jconfig->getValue('config.sitename');
			$employer->companyLocation  = '';
			$employer->companyWebsite   = $live_site;
			$uid = 1; // site admin
		}

		// Push some styles to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts();
		
		// Get JS
		$document =& JFactory::getDocument();
		//$document->addScript('components' . DS . $this->_option . DS . 'js' . DS . 'calendar.rc4.js');
		$document->addStyleSheet('components' . DS . 'com_events' . DS . 'calendar.css');	

		$jt = new JobType($database);
		$jc = new JobCategory($database);

		// get job types			
		$types = $jt->getTypes();
		$types[0] = JText::_('TYPE_ANY');

		// get job categories
		$cats = $jc->getCats();
		$cats[0] = JText::_('CATEGORY_NO_SPECIFIC');

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_jobid = $job->id;
		$this->_jobtitle = $job->title;
		$this->_buildPathway();

		// Output HTML
		$view = new JView(array('name'=>'editjob'));
		$view->title = $this->_title;
		$view->config = $this->config;
		$view->uid = $uid;
		$view->profile = $profile;
		$view->emp = $this->_emp;
		$view->job = $job;
		$view->jobid = $job->id;
		$view->types = $types;
		$view->cats = $cats;
		$view->employer = $employer;
		$view->admin = $this->_admin;
		$view->masteradmin = $this->_masteradmin;
		$view->error = $this->_error;
		$view->task = $this->_task;
		$view->option = $this->_option;
		if ($this->getError()) {
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Authorizations
	 * 
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function authorize_employer($admin = 0)
	{
		$database =& JFactory::getDBO();
		$juser    =& JFactory::getUser();

		$emp = 0;

		$employer = new Employer($database);
		if ($admin) {
			$adminemp = $employer->isEmployer($juser->get('id'), 1);
			if (!$adminemp) {
				// will require setup only once
				$subscription = new Subscription($database);
				$subscription->status = 1;
				$subscription->uid = 1;
				$subscription->units = 72;
				$subscription->serviceid = 1;
				$subscription->expires = date("Y-m-d",strtotime("+ 72 months"));
				$subscription->added = date('Y-m-d H:i:s', time());

				if (!$subscription->store()) {
					JError::raiseError(500, $subscription->getError());
					return;
				}

				if (!$subscription->id) {
					$subscription->checkin();
				}

				// make sure we have dummy admin employer account
				$jconfig 	=& JFactory::getConfig();
				$live_site = rtrim(JURI::base(),'/');
				
				$employer->uid = 1;
				$employer->subscriptionid = $subscription->id;
				$employer->companyName 		= $jconfig->getValue('config.sitename');
				$employer->companyLocation  = '';
				$employer->companyWebsite   = $live_site;

				// save employer information		
				if (!$employer->store()) {
					JError::raiseError(500, $employer->getError());
					return;
				}
			}
		} else {
			$emp = $employer->isEmployer($juser->get('id'));
		}

		$this->_emp = $emp;
	}

	/**
	 * Short description for 'authorize_admin'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     void
	 */
	public function authorize_admin($admin = 0)
	{
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			// Check if they're a site admin (from Joomla)
			if ($juser->authorize($this->_option, 'manage')) {
				$admin = 1;
			}

			// check if they belong to a dedicated admin group
			$admingroup = $this->config->get('admingroup') ? $this->config->get('admingroup') : '' ;
			if ($admingroup) {
				ximport('Hubzero_User_Helper');

				$ugs = Hubzero_User_Helper::getGroups($juser->get('id'));
				if ($ugs && count($ugs) > 0) {
					foreach ($ugs as $ug)
					{
						if ($ug->cn == $admingroup) {
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
	 * @param      unknown $database Parameter description (if any) ...
	 * @param      object $juser Parameter description (if any) ...
	 * @param      string $category Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function updatePrefs($database, $juser, $category = 'resume')
	{
		$saveprefs  = JRequest::getInt('saveprefs', 0, 'post');

		$p = new Prefs($database);

		$filters = $this->getFilters (0, 0, 0);
		if($category == 'job')
		{
			$filters['sortby'] = trim(JRequest::getVar('sortby', 'title'));
		}
		
		$text = 'filterby=' . $filters['filterby'].'&amp;match=1&amp;search=' 
		. $filters['search'].'&amp;category='. $filters['category'] 
		. '&amp;type='. $filters['type'].'&amp;sortby=';

		if ($category == 'job' && isset($_GET["performsearch"])) {
			$text .= $filters['sortby'];
			if (!$p->loadPrefs($juser->get('id'), $category)) {
				$p = new Prefs($database);
				$p->uid = $juser->get('id');
				$p->category = $category;
			}
			$p->filters = $text;

			// Store content
			if (!$p->store()) {
				JError::raiseError(500, $p->getError());
				return;
			}
		} else {
			if ($saveprefs && isset($_POST["performsearch"])) {
				if (!$p->loadPrefs($juser->get('id'), $category)) {
					$p = new Prefs($database);
					$p->uid = $juser->get('id');
					$p->category = $category;
					$text .= 'bestmatch';
				} else {
					$text .= $filters['sortby'];
				}

				$p->filters = $text;

				// Store content
				if (!$p->store()) {
					JError::raiseError(500, $p->getError());
					return;
				}
			} else if ($p->loadPrefs($juser->get('id'), $category) && isset($_POST["performsearch"]))  {
				// delete prefs
				$p->delete();
			}
		}
	}

	/**
	 * Short description for 'getPrefs'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $database Parameter description (if any) ...
	 * @param      object $juser Parameter description (if any) ...
	 * @param      string $category Parameter description (if any) ...
	 * @return     void
	 */
	public function getPrefs($database, $juser, $category = 'resume')
	{
		$p = new Prefs($database);
		if ($p->loadPrefs($juser->get('id'), $category)) {
			if (isset($p->filters) && $p->filters) {
				// get individual filters
				$col = explode("&amp;",$p->filters);

				if (count($col > 0)) {
					foreach ($col as $c)
					{
						$nuk = explode("=",$c);

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
	 * @param      integer $admin Parameter description (if any) ...
	 * @param      integer $emp Parameter description (if any) ...
	 * @param      integer $checkstored Parameter description (if any) ...
	 * @param      integer $jobs Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getFilters($admin=0, $emp = 0, $checkstored = 1, $jobs = 0)
	{
		// Query filters defaults
		$filters = array();

		// jobs filters
		if ($jobs) {
			$filters['sortby']   = $this->getVar("sortby") && $checkstored 
			? $this->getVar("sortby", "title") : trim(JRequest::getVar('sortby', 'title'));
			$filters['category'] = $this->getVar("category") && $checkstored ? $this->getVar("category") : JRequest::getInt('category',  'all');
		} else {
			$filters['sortby']   = $this->getVar("sortby") && $checkstored ? $this->getVar("sortby") : trim(JRequest::getVar('sortby', 'lastupdate'));
			$filters['category'] = $this->getVar("category") && $checkstored ? $this->getVar("category") : JRequest::getInt('category',  0);
		}

		$filters['type']   = $this->getVar("type") && $checkstored ? $this->getVar("type") : JRequest::getInt('type',  0);
		$filters['search'] = $this->getVar("search") && $checkstored ? $this->getVar("search") : trim(JRequest::getVar('q', ''));
		$filters['filterby'] = trim(JRequest::getVar('filterby', 'all'));
		$filters['sortdir']	 = JRequest::getVar('sortdir', 'ASC');

		// did we get stored prefs?
		$filters['match'] = $this->getVar("match") && $checkstored ? $this->getVar("match") : JRequest::getInt('match', 0);

		// Paging vars
		$filters['limit'] = JRequest::getInt('limit', $this->config->get('jobslimit'));
		$filters['start'] = JRequest::getInt('limitstart', 0, 'get');

		// Task-specific
		//$filters['sortby'] = $this->_task != 'browse' ? 'opendate' : $filters['sortby'] ;
		//$filters['limit'] = $this->_task != 'browse' ? 10 : $filters['limit'] ;

		// admins and employers
		$filters['admin'] = $admin;
		$filters['emp'] = $emp;

		// Return the array
		return $filters;
	}

	/**
	 * Batch resume download
	 * 
	 * @return     void
	 */
	public function batch()
	{
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();

		// Login required
		if ($juser->get('guest')) {
			if ($this->_allowsubscriptions) {
				$this->intro_employer();
			} else {
				$this->login();
			}
			return;
		}

		// Check authorization
		if (!$this->_admin && !$this->_emp) {
			if ($this->_allowsubscriptions) {
				$this->intro_employer();
			} else {
				$this->_task = 'newsubscribe'; $this->subscribe();
			}
			return;
		}

		// Incoming
		$pile = JRequest::getVar('pile', 'all');

		// Zip the requested resumes
		$archive = $this->archiveResumes ($pile);

		if ($archive) {
			// Initiate a new content server and serve up the file
			ximport('Hubzero_Content_Server');
			jimport('joomla.filesystem.file');
			$xserver = new Hubzero_Content_Server();
			$xserver->filename($archive['path']);

			$xserver->disposition('attachment');
			$xserver->acceptranges(false);
			$xserver->saveas(JText::_('JOBS_RESUME_BATCH=Resume Batch'));
			$result = $xserver->serve_attachment($archive['path'], $archive['name'], false);

			// Delete downloaded zip			
			JFile::delete($archive['path']);

			if (!$result) {
            	JError::raiseError(404, JText::_('ERROR_ARCHIVE_FAILED'));
			} else {
				exit;
			}
		} else {
			$this->setError(JText::_('ERROR_ARCHIVE_FAILED'));
			$this->dashboard();
		}
	}

	/**
	 * Create resume archive
	 * 
	 * @param      string $pile Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function archiveResumes($pile = 'all')
	{
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();

		// Get available resume files
		$resume = new Resume ($database);
		$files 	= $resume->getResumeFiles($pile, $juser->get('id'), $this->_masteradmin);
		$batch  = array();

		if (count($files) > 0) {
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'includes' . DS . 'pcl' . DS . 'pclzip.lib.php');
			if (!extension_loaded('zlib')) {
				JError::raiseError(500, JText::_('ERROR_MISSING_PHP_LIBRARY'));
				return;
			}

			// Get Members plugins
			JPluginHelper::importPlugin('members', 'resume');
			$dispatcher =& JDispatcher::getInstance();

			$pile .= $pile != 'all' ? '_'.$juser->get('id') : '';
			$zipname = JText::_('Resumes').'_'.$pile.'.zip';

			$mconfig =& JComponentHelper::getParams('com_members');
			$base_path = $mconfig->get('webpath');

			if ($base_path) {
				// Make sure the path doesn't end with a slash
				if (substr($base_path, -1) == DS) {
					$base_path = substr($base_path, 0, strlen($base_path) - 1);
				}
				// Ensure the path starts with a slash
				if (substr($base_path, 0, 1) != DS) {
					$base_path = DS.$base_path;
				}
			}

			$archive = new PclZip(JPATH_ROOT.$base_path . DS . $zipname);
			$rfiles  = '';
			$i = 0;
			// Go through file names and get full paths
			foreach ($files as $avalue => $alabel)
			{
				$i++;
				$apath =  $dispatcher->trigger('build_path', array($avalue));
				$path  = is_array($apath) ? $apath[0] : '';
				$file = $path ? JPATH_ROOT.$path . DS . $alabel : '';
				
				if(!is_file($file))
				{
					continue;
				}
				
				$rfiles .= $file;
				$rfiles .= $i == count($files) ? '' : ',';
			}

			$v_list = $archive->create($rfiles, PCLZIP_OPT_REMOVE_ALL_PATH);

			if ($v_list == 0) {
				JError::raiseError(500, $archive->errorInfo(true));
				return;
			} else {
				$archive = array();
				$archive['path'] = JPATH_ROOT.$base_path . DS . $zipname;
				$archive['name'] = $zipname;
				return $archive;
			}
		}
		return false;
	}

	/**
	 * Check job ad quota depending on subscription
	 * 
	 * @param      object $job Parameter description (if any) ...
	 * @param      object $juser Parameter description (if any) ...
	 * @param      unknown $database Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function checkQuota ($job, $juser, $database)
	{
		// make sure we aren't over quota
		$service = new Service($database);
		$servicename = $service->getUserService($juser->get('id'));
		if (!$service->loadService($servicename)) {
			return 0;
		} else {
			$this->getServiceParams(&$service);
			$maxads = $service->maxads > 0 ? $service->maxads : 1;
			$activejobs = $job->countMyActiveOpenings($juser->get('id'), 1);
			$allowed_ads = $maxads - $activejobs;
			return $allowed_ads;
		}
	}

	/**
	 * Initial setup of default services
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	protected function setupServices()
	{
		$database =& JFactory::getDBO();

		$objS = new Service($database);
		$now = date('Y-m-d H:i:s', time());

		$default1 = array (
					'id' => 0,
					'title' => JText::_('Employer Service, Basic'),
					'category' => strtolower(JText::_('jobs')),
					'alias' => JText::_('employer_basic'),
					'status' => 1,
					'description' => JText::_('Allows to search member resumes and post one job ad'),
					'unitprice' => '0.00',
					'pointprice' => 0,
					'currency' => '$',
					'maxunits' => 6,
					'minunits' => 1,
					'unitsize' => 1,
					'unitmeasure' => strtolower(JText::_('month')),
					'changed' => $now,
					'params' => 'promo=First 3 months FREE
promomaxunits=3
maxads=1'
					);
		$default2 = array (
					'id' => 0,
					'title' => JText::_('Employer Service, Premium'),
					'category' => strtolower(JText::_('jobs')),
					'alias' => JText::_('employer_premium'),
					'status' => 0,
					'description' => JText::_('Allows to search member resumes and post up to 3 job ads'),
					'unitprice' => '500.00',
					'pointprice' => 0,
					'currency' => '$',
					'maxunits' => 6,
					'minunits' => 1,
					'unitsize' => 1,
					'unitmeasure' => strtolower(JText::_('month')),
					'changed' => $now,
					'params' => 'promo=
promomaxunits=
maxads=3'
					);

		if (!$objS->bind($default1)) {
			$this->_error = $objS->getError();
			return false;
		}
		if (!$objS->store()) {
			$this->_error = $objS->getError();
			return false;
		}
		if (!$objS->bind($default2)) {
			$this->_error = $objS->getError();
			return false;
		}
		if (!$objS->store()) {
			$this->_error = $objS->getError();
			return false;
		}
	}

	/**
	 * Get service params
	 * 
	 * @param      object &$service Parameter description (if any) ...
	 * @return     void
	 */
	public function getServiceParams (&$service)
	{
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}
		$params = new $paramsClass($service->params);
		$service->maxads = $params->get('maxads', '');
		$service->maxads = intval(str_replace(" ","",$service->maxads));
	}
}

