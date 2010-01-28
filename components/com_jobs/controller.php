<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JobsController extends JObject
{
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $_error = NULL;

	//-----------

	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';

		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}

		// Set the component name
		$this->_option = 'com_'.$this->_name;
	}
	
	//-----------

	public function setVar ($property, $value)
	{
		$this->$property = $value;
	}
	
	//-----------

	public function getVar ($property)
	{
		return $this->$property;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	//-----------

	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
	//-----------

	public function getStyles($option='', $css='')
	{
		ximport('xdocument');
		if ($option) {
			XDocument::addComponentStylesheet($option, $css);
		} else {
			XDocument::addComponentStylesheet($this->_option);
		}

	}
	//-----------
	
	public function getScripts($option='',$name='')
	{
		$document =& JFactory::getDocument();
		
		if ($option) {
			$name = ($name) ? $name : $option;
			if (is_file(JPATH_ROOT.DS.'components'.DS.$option.DS.$name.'.js')) {
				$document->addScript('components'.DS.$option.DS.$name.'.js');
			}
		} else {
			if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
			}
		}
	}
	
	//-----------

	public function getTask()
	{
		$task = JRequest::getVar( 'task', '', 'post' );
		if (!$task) {
			$task = JRequest::getVar( 'task', '', 'get' );
		}
		$this->_task = $task;

		return $task;
	}
	
	//-----------
	
	public function execute()
	{
			
		// Get the component parameters
		$config = new JobsConfig( $this->_option );
		$this->config = $config;
		
		// are we using banking functions?
		$xhub =& XFactory::getHub();
		$banking = $xhub->getCfg('hubBankAccounts');
		$this->banking = ($banking && isset($this->config->parameters['banking']) && $this->config->parameters['banking']==1 ) ? 1: 0 ;
		
		if ($banking) {
			ximport( 'bankaccount' );
		}
		
		$this->industry = isset($this->config->parameters['industry']) ? $this->config->parameters['industry'] : '';
		$this->allowsubscriptions = isset($this->config->parameters['allowsubscriptions']) ? $this->config->parameters['allowsubscriptions'] : 1;
		
		// Get admin priviliges
		JobsController::authorize_admin();
		
		// Get employer priviliges
		if($this->allowsubscriptions) {
			JobsController::authorize_employer();	
		}
		else {
			$this->_emp = 0;
		}		
			
		switch( $this->getTask() ) 
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
			
			// subscription management
			case 'subscribe':  		$this->subscribe();    	break;
			case 'confirm':  		$this->confirm();    	break;
			case 'cancel':  		$this->cancel();    	break;
						
			// Should only be called via AJAX
			case 'plugin':     $this->plugin();     break;

			default: $this->view(); break;
		}
	}

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	public function login($msg='', $plugin=0) 
	{
		if(!$plugin) {		
			// Set the page title
			$title = JText::_(strtoupper($this->_name));
			$title.= $this->industry ? ' '.JText::_('IN').' '.$this->industry : '';
			
			$document =& JFactory::getDocument();
			$document->setTitle( $title.' - '.JText::_('LOGIN') );
			
			$japp =& JFactory::getApplication();
			$pathway =& $japp->getPathway();
			
			$pathway->addItem( $title, 'index.php?option='.$this->_option );
			$pathway->addItem( JText::_('LOGIN'), 'index.php?option='.$this->_option.a.'task='.$this->_task );		
		}
			
		echo JobsHtml::div( JobsHtml::hed( 2, $title.': '.JText::_('LOGIN') ), 'full', 'content-header' );
		echo '<div class="main section">'.n;
		if ($msg) {
			echo JobsHtml::warning( $msg );
		}
		ximport('xmodule');
		XModuleHelper::displayModules('force_mod');
		echo '</div><!-- / .main section -->'.n;	
	}
	
	//-----------
	// NOTE: This view should only be called through AJAX

	protected function plugin()
	{
		// Incoming
		$trigger = trim(JRequest::getVar( 'trigger', '' ));
		
		// Ensure we have a trigger
		if (!$trigger) {
			echo JobsHtml::error( JText::_('RESOURCES_NO_TRIGGER_FOUND') );
			return;
		}
		
		// Get Members plugins
		JPluginHelper::importPlugin( 'members', 'resume' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Call the trigger
		$results = $dispatcher->trigger( $trigger, array() );
		if (is_array($results)) {
			$html = $results[0]['html'];
		}
		
		// Output HTML
		echo $html;
	}
	
	//-----------

	public function shortlist()
	{
		$oid  = JRequest::getInt( 'oid', 0 );
		
		// Get Members plugins
		JPluginHelper::importPlugin( 'members', 'resume' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Call the trigger
		$results = $dispatcher->trigger( 'shortlist', array($oid, $ajax=0) );
		
		// Go back to the page
		$referer = JRequest::getVar( 'HTTP_REFERER', NULL, 'server' ); // What page they came from
		$this->_redirect = $referer;
	
	}

	
	//-----------------------------
	// Introductory page/ Jobs list
	//-----------------------------

	public function view()
	{

		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();
		
		// get action
		$action  = JRequest::getVar( 'action', '' );
		if($action == 'login' && $juser->get('guest')) {
			$msg = JText::_('Please login to view extra options');
			$this->login($msg);
			return;
		}
		
		if(!$juser->get('guest') && ($this->_task == 'browse' or !$this->allowsubscriptions )) {	
			// save incoming prefs
			$this->updatePrefs($database, $juser, 'job');
				
			// get stored preferences
			$this->getPrefs($database, $juser, 'job');
		}
		
		// Get filters
		$filters = JobsController::getFilters($this->_admin, 0 , 1 , 1);
		
		// Get data
		$obj = new Job( $database );
		$filters['sortby'] = $this->_task != 'browse' ? 'opendate' : $filters['sortby'] ;
		$filters['limit'] = $this->_task != 'browse' ? 10 : $filters['limit'] ;
		$jobs = ($this->_task != 'browse' && $this->allowsubscriptions) ? $obj->get_openings ($filters, $juser->get('id'), 0) : $obj->get_openings ($filters, $juser->get('id'), $this->_admin);
		
		// Initiate paging 
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( count($jobs), $filters['start'], $filters['limit'] );
				
		// Add the CSS to the template
		JobsController::getStyles();
		
		// Set the page title
		$title = JText::_(strtoupper($this->_name));
		$title.= $this->industry ? ' '.JText::_('IN').' '.$this->industry : '';
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		jimport( 'joomla.application.component.view');
		
		// Set breadcrumbs
		$japp =& JFactory::getApplication();
		$pathway =& $japp->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( $title, 'index.php?option='.$this->_option );
		}
		if($this->_task == 'browse' or !$this->allowsubscriptions) {
			$pathway->addItem( JText::_('Browse'), 'index.php?option='.$this->_option.a.'task=browse'  );
		}
		
		// Output HTML
		$view = ($this->_task == 'browse' or !$this->allowsubscriptions) ? new JView( array('name'=>'jobs') ) : new JView( array('name'=>'intro') );
		$view->title = $title;
		$view->config = $this->config;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->option = $this->_option;
		$view->emp = $this->_emp;
		$view->admin = $this->_admin;
		$view->pageNav = $pageNav;
		$view->allowsubscriptions = $this->allowsubscriptions;
		
		if($this->_task == 'browse' or !$this->allowsubscriptions) {
			$view->jobs = $jobs;
			$view->mini = 0;
			$view->database = $database;
			$view->filters = $filters;
			$view->display();
			return;
		}
		
		// Output intro	
		$view->msg = '';
		$view->display();
		
		// show recent jobs
		$view = new JView( array('name'=>'jobs') );
		$view->config = $this->config;
		$view->jobs = $jobs;
		$view->mini = 1;
		$view->admin = $this->_admin;
		$view->database = $database;
		$view->filters = $filters;
		$view->emp = $this->_emp;
		$view->option = $this->_option;
		$view->allowsubscriptions = $this->allowsubscriptions;
		$view->display();					
	}
	
	//-----------------------------
	// List of candidates
	//-----------------------------

	public function resumes()
	{

		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();
		
		// Login required
		if ($juser->get('guest')) {
			if($this->allowsubscriptions) {
			$this->intro_employer();
			
			}
			else {
			$this->login();
			}
			return;
		}
		
		// Set the page title
		$title = JText::_(strtoupper($this->_name));
		$title.= $this->industry ? ' '.JText::_('IN').' '.$this->industry : '';
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title.': '.JText::_('Browse Resumes') );
				
		// Add the CSS to the template
		JobsController::getStyles();
		JobsController::getScripts();	
					
		if($this->_admin or $this->_emp ) {
		
			$japp =& JFactory::getApplication();
			$pathway =& $japp->getPathway();
			
			$pathway->addItem( $title, 'index.php?option='.$this->_option );
			$pathway->addItem( JText::_('Browse Resumes'), 'index.php?option='.$this->_option.a.'task='.$this->_task );			
				
			// save incoming prefs
			$this->updatePrefs($database, $juser);
			
			// get stored preferences
			$this->getPrefs($database, $juser);
			
			// get filters
			$filters = JobsController::getFilters($this->_admin, $this->_emp);
			
			// get job types
			$jt = new JobType ( $database );		
			$types = $jt->getTypes();
			$types[0] = JText::_('Any type');
				
			// get job categories
			$jc = new JobCategory ( $database );
			$cats = $jc->getCats();
			$cats[0] = JText::_('Any category');
	
			// get users with resumes
			$js = new JobSeeker ( $database );
			$admin = $this->_admin && !$this->_emp ? 1 : 0;
			$seekers = $js->getSeekers ($filters, $juser->get('id'), 0, $admin );
		
			$total = $js->countSeekers ($filters, $juser->get('id'));
			
			// Initiate paging 
			jimport('joomla.html.pagination');
			$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );
						
			// display list of users with resumes
			jimport( 'joomla.application.component.view');
			
			$view 			= new JView( array('name'=>'resumes') );
			$view->config 	= $this->config;
			$view->admin 	= $this->_admin;			
			$view->title 	= $title.': '.JText::_('Browse Resumes');
			$view->seekers 	= $seekers;
			$view->pageNav 	= $pageNav;
			$view->cats 	= $cats;
			$view->types 	= $types;		
			$view->filters 	= $filters;
			$view->emp 		= $this->_emp;
			$view->option 	= $this->_option;
			$view->display();		
			
		}
		else if($this->allowsubscriptions) {
			// have to subscribe first
			
			$employer = new Employer ( $database );
			if ($employer->loadEmployer($juser->get('id'))) {
				ximport( 'subscriptions' );
				//do we have a pending subscription?
				$subscription = new Subscription($database);
				if($subscription->loadSubscription ($employer->subscriptionid, $juser->get('id'), '', $status=array(0))) {
					$this->_msg = JobsHtml::warning(JText::_('Your subscription is pending approval. Access to Employer Services will be granted once the subscription is approved.'));
					$this->dashboard();
					return;
				}
			}
			
		
			// send to subscription page
			$this->subscribe();
		
		}	
		else {
			$this->view();
		}			
		
	}
	
	//-----------------------------
	// Subscription cancellation
	//-----------------------------
	
	protected function cancel() 
	{
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();
		
		// Login required
		if ($juser->get('guest')) {
			$this->intro_employer();
			return;
		}
		
		$uid 	= JRequest::getInt( 'uid', $juser->get('id') );	
		
		// non-admins can only cancel their own subscription	
		if($uid && $juser->get('id') != $uid && !$this->_admin) {
			// not authorized
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}
		$uid = $uid ? $uid : $juser->get('id');
		
		ximport( 'subscriptions' );
		
		// load Employer
		$employer = new Employer ( $database );
		if (!$employer->loadEmployer($uid)) {
			JError::raiseError( 404, JText::_('Employer profile not found. ') );
			return;
		}
		
		// load subscription to cancel
		$subscription = new Subscription($database);
		if(!$subscription->load ($employer->subscriptionid)) {
			JError::raiseError( 404, JText::_('Subscription not found. ') );
			return;
		}
		
		// get service
		$service = new Service($database);
		
		if(!$service->loadService ('', $subscription->serviceid)) {
			JError::raiseError( 404, JText::_('Service not found. ') );
			return;
		}
		
		$refund 	  = 0;
		$unitsleft 	  = $subscription->getRemaining('unit', $subscription, $service->maxunits, $service->unitsize);
		
		// get cost per unit (to compute required refund)	
		$refund = ($subscription->totalpaid > 0 && $unitsleft > 0 && ($subscription->totalpaid - $unitsleft * $unitcost) > 0 ) ? $unitsleft * $prevunitcost : 0;
					
		// cancel previous subscription & issue a refund if applicable
		if($subscription->cancelSubscription($employer->subscriptionid, $refund, $unitsleft)) {
		
			// Get filters
			$filters = JobsController::getFilters($this->_admin);
		
			// Get data
			$obj = new Job( $database );
			$jobs = $obj->get_openings ($filters, $juser->get('id'), $this->_admin);
		
			// Add the CSS to the template
			JobsController::getStyles();
		
			// Set the page title
			$title = JText::_(strtoupper($this->_name));
			$title.= $this->industry ? ' '.JText::_('IN').' '.$this->industry : '';
		
			$document =& JFactory::getDocument();
			$document->setTitle( $title );
		
			// Set breadcrumbs
			$japp =& JFactory::getApplication();
			$pathway =& $japp->getPathway();
			if (count($pathway->getPathWay()) <= 0) {
				$pathway->addItem( $title, 'index.php?option='.$this->_option );
			}
		
			jimport( 'joomla.application.component.view');
		
			// Output HTML
			$view = new JView( array('name'=>'intro') );
			$view->title = $title;
			$view->config = $this->config;
			$view->jobs = $jobs;
			$view->emp = 0;
			$view->admin = $this->_admin;
			$view->allowsubscriptions = $this->allowsubscriptions;
			$view->msg = JText::_('Your subscription has been successfully cancelled.');
			$view->option = $this->_option;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			
			return;
		}
		
		$this->view();
					
		
	}
	
	//-----------------------------
	// Subscription confirmation
	//-----------------------------
	
	protected function confirm() 
	{
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();
		
		// Login required
		if ($juser->get('guest')) {
			$this->intro_employer();
			return;
		}
		
		$uid 	= JRequest::getInt( 'uid', $juser->get('id') );		
		if($uid && $juser->get('id') != $uid && !$this->_admin) {
			// not authorized
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}
		$uid = $uid ? $uid : $juser->get('id');
		
		ximport( 'subscriptions' );
		
		// Get the member's info
		ximport('xprofile');
		$profile = new XProfile();
		$profile->load( $uid );
		
		// are we renewing?
		$subid 	= JRequest::getInt( 'subid', 0 );
		$sconfig =& JComponentHelper::getParams( 'com_services' );
		$autoapprove = $sconfig->get('autoapprove');
			
		// load Employer
		$employer = new Employer ( $database );
		if (!$employer->loadEmployer($uid)) {
			$employer = new Employer ( $database );
			$employer->uid = $uid;
		}
		
		$subid = $employer->subscriptionid ? $employer->subscriptionid : $subid;
		
		$employer->companyName = JRequest::getVar( 'companyName', $profile->get('organization') );
		$employer->companyLocation = JRequest::getVar( 'companyLocation', $profile->get('countryresident') );
		$employer->companyWebsite =  JRequest::getVar( 'companyWebsite', $profile->get('url') );		
	
		// do we have a subscription already?
		$subscription = new Subscription($database);
		if(!$subscription->load ($subid)) {
			$subscription = new Subscription($database);
		}
		
		$serviceid 	= JRequest::getInt( 'serviceid', 0 );
		// get service
		$service = new Service($database);
		
		if(!$serviceid or !$service->loadService ('', $serviceid)) {
			echo JobsHtml::alert( JText::_('Cannot proceed without selection of subscription service') );
			exit();
		}
			
		$units 	= JRequest::getInt( 'units_'.$serviceid, 0 );
		$contact = JRequest::getVar( 'contact', '' );
		$total 	= $service->unitprice * $units;
		$now = date( 'Y-m-d H:i:s', time() );
		$new = 0;
		$credit = 0;
		
		// we got an order
		if ($units) {
			
			$months = $units * $service->unitsize;
			$newexprire = date("Y-m-d",strtotime("+".$months."months"));
			
			if($total && !$contact) {
				// need contact info with payment
				echo JobsHtml::alert( JText::_('Cannot proceed without a valid contact phone number.') );
				exit();
			}
			
			$newunitcost  = $service->unitprice;
					
			if($subid) {
				
				// get cost per unit (to compute required refund)
				$prevunitcost = $serviceid != $subscription->serviceid ? $service->getServiceCost($subscription->serviceid) : $newunitcost;
				$unitsleft 	  = 0;
				$refund		  = 0;
				
				// we are upgrading / downgrading - or replacing cancelled subscription
				if($serviceid != $subscription->serviceid or $subscription->status==2) {
										
					if($prevunitcost > 0 && $subscription->status != 2) {				
						$unitsleft = $subscription->getRemaining('unit', $subscription, $service->maxunits, $service->unitsize);
						$refund = ($subscription->totalpaid > 0 && ($subscription->totalpaid - $unitsleft * $prevunitcost) < 0 ) ? $unitsleft * $prevunitcost : 0;
					
						// calculate available credit - if upgrading
						if($newunitcost > $prevunitcost ) {
							$credit = 0; // TBD
						}
					}
										
					// cancel previous subscription & issue a refund if applicable
					if($subscription->status != 2) {
						$subscription->cancelSubscription($subid, $refund, $unitsleft);
					}
					
					// enroll in new service
					$subscription = new Subscription($database);
					$new = 1;
					
				}
				
				// extending?
				else if($subscription->expires > $now) {
					
					$subscription->status = $autoapprove && !$total ? 1 : $subscription->status;
					$subscription->status = $subscription->status == 2 ? 1 : $subscription->status;										
					$subscription->units = $autoapprove && !$total ? $subscription->units + $units : $subscription->units;
					$subscription->pendingunits = $autoapprove && !$total ? 0 : $units;	
					$subscription->pendingpayment = $autoapprove && !$total ? 0 : $units * $newunitcost;			
					$newexprire = date("Y-m-d",strtotime("+".$subscription->units * $service->unitsize ."months"));
					$subscription->expires = $autoapprove && !$total ? $newexprire : $subscription->expires;
					$subscription->updated = $now;		
				}
				else {
					// expired - treat like new
					$new = 1;
					$subscription->updated = $now;				
				}
					
			}
			else {
				// this is a new subscription
				$new = 1;
			}		
		}
		
		// this is a new subscription
		if($new) {
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
		if($units or $contact != $subscription->contact or !$subid or $serviceid != $subscription->serviceid ) {
			$subscription->contact = $contact;
			$subscription->uid = $uid;
			$subscription->serviceid = $serviceid;
			
			if(!$subscription->id) {
				// get unique code
				$subscription->code = $subscription->generateCode();
			}
			
			if (!$subscription->check()) {
				echo JobsHtml::alert( $subscription->getError() );
				exit();
			}
			if (!$subscription->store()) {
				echo JobsHtml::alert( $subscription->getError() );
				exit();
			}
			if (!$subscription->id) {
				$subscription->checkin();
			}
	
		}
		
		// save employer information		
		$employer->subscriptionid = $subscription->id;
		if (!$employer->store()) {
			echo JobsHtml::alert( $employer->getError() );
			exit();
		}
		
		$msg = $subid ? JText::_('Your subscription has been processed.') : JText::_('Your subscription has been accepted. Thank you!');
		if($units) {
		$msg .= $autoapprove && !$total ? ' '.JText::_('You have access to employer services for the next').' '.$subscription->units.' '.JText::_('month(s)') : ' '.JText::_('We will contact you soon regarding activation of your subscription request.');
		$this->_msg = JobsHtml::passed($msg);
		}
		
		
		$this->dashboard();
		
	}	
	
	//-----------------------------
	// Dashboard
	//-----------------------------
	
	protected function dashboard() 
	{
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();
		
		// Login required
		if ($juser->get('guest')) {
			$this->intro_employer();
			return;
		}
		
		$uid 	= JRequest::getInt( 'uid', $juser->get('id') );		
		if($uid && $juser->get('id') != $uid && !$this->_admin) {
			// not authorized
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}
		$uid = $uid ? $uid : $juser->get('id');
		$admin = $this->_admin && !$this->_emp ? 1 : 0;
		if($admin) {
			$this->authorize_employer(1);
		}
		
		// Get the member's info
		ximport('xprofile');
		$profile = new XProfile();
		$profile->load( $uid );
					
		ximport( 'subscriptions' );
		
		// load Employer
		$employer = new Employer ( $database );
		
		if (!$employer->loadEmployer($uid) && !$this->_admin) {
			// send to subscription page
			$this->subscribe();
			return;
		}	
		else if($admin) {
			$employer->id = 1;
		}
		
		// do we have a subscription already?
		$subscription = new Subscription($database);
		if(!$subscription->load ($employer->subscriptionid) && !$this->_admin) {
			// send to subscription page
			$this->subscribe();
			return;
		}
			
		$service = new Service($database);
		
		if(!$service->loadService ('', $subscription->serviceid) && !$this->_admin) {
			JError::raiseError( 404, JText::_('Subscription service not found.') );
			return;
		}
		else {
			// get service params like maxads
			$this->getServiceParams (&$service);
		}
				
		// Get current stats for dashboard
		$jobstats = new JobStats($database);
		$stats = $jobstats->getStats($uid, 'employer', $admin);
		
		// Get job postings
		$job = new Job ( $database );
		$myjobs = $job->get_my_openings($juser->get('id'), 0, $admin);
		$activejobs = $job->countMyActiveOpenings ($juser->get('id'), 1, $admin);
		
		// Set the page title
		$title = JText::_(strtoupper($this->_name));
		$title.= $this->industry ? ' '.JText::_('IN').' '.$this->industry : '';
		$subtitle = JText::_('Employer Dashboard');
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title.': '.$subtitle );
		
		$japp =& JFactory::getApplication();
		$pathway =& $japp->getPathway();
		
		$pathway->addItem( $title, 'index.php?option='.$this->_option );
		$pathway->addItem( $subtitle, 'index.php?option='.$this->_option.a.'task=dashboard' );
		
		// Add the CSS to the template
		JobsController::getStyles();
		JobsController::getScripts();
		
		jimport( 'joomla.application.component.view');
		
		// Output HTML
		$view =  new JView( array('name'=>'dashboard') );
		$view->title = $title.': '.$subtitle;
		$view->config = $this->config;
		$view->updated = 0;
		$view->msg = $this->_msg;
		$view->myjobs = $myjobs;
		$view->activejobs = $activejobs;
		$view->subscription = $subscription;
		$view->employer = $employer;
		$view->admin = $this->_admin;
		$view->service = $service;
		$view->login = $profile->get('username');
		$view->uid = $uid;
		$view->stats = $stats;
		$view->emp = 1;
		$view->task = $this->_task;
		$view->option = $this->_option;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
		
		
		
	}
	//-----------------------------
	// Subscription form
	//-----------------------------
	
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
		$uid 	= JRequest::getInt( 'uid', 0 );		
		
		if($uid && $juser->get('id') != $uid && !$this->_admin) {
			// not authorized
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}
		$uid = $uid ? $uid : $juser->get('id');
		
		ximport( 'subscriptions' );
		
		// Set the page title
		$title = JText::_(strtoupper($this->_name));
		$title.= $this->industry ? ' '.JText::_('IN').' '.$this->industry : '';
		$subtitle = $this->_task == 'subscribe' ? JText::_('Edit Subscription') : JText::_('Subscribe as Employer');
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title.': '.$subtitle );
		
		$japp =& JFactory::getApplication();
		$pathway =& $japp->getPathway();
		
		$pathway->addItem( $title, 'index.php?option='.$this->_option );
		$pathway->addItem( $subtitle, 'index.php?option='.$this->_option.a.'task='.$this->_task );
		
		// Add the CSS to the template
		JobsController::getStyles();
		JobsController::getScripts();
		
		// Get the member's info
		ximport('xprofile');
		$profile = new XProfile();
		$profile->load( $uid );
		
		// load Employer
		$employer = new Employer ( $database );
		if (!$employer->loadEmployer($uid)) {
			$employer = new Employer ( $database );
			$employer->uid = $uid;
			$employer->subscriptionid = 0;
			$employer->companyName = $profile->get('organization');
			$employer->companyLocation = $profile->get('countryresident');
			$employer->companyWebsite =  $profile->get('url');
		}
										
		// do we have an active subscription already?
		$subscription = new Subscription($database);
		if(!$subscription->loadSubscription ($employer->subscriptionid, '', '', $status=array( 0, 1))) {
			$subscription = new Subscription($database);
			$subscription->uid = $uid;
			$subscription->serviceid = 0;
		}		
		
		// get subscription options
		$objS = new Service($database);
		$specialgroup = isset($this->config->parameters['specialgroup'])  ? $this->config->parameters['specialgroup'] : '';	
		$services = $objS->getServices('jobs', 1, 1, 'ordering', 'ASC', $specialgroup);
		
		if(!$services) {
			// setup with default info
			$this->setupServices();
		}
		
		// check available user funds (if paying with points)
		$BTL 		= new BankTeller( $database, $subscription->uid);
		$balance 	= $BTL->summary();			
		$credit  	= $BTL->credit_summary();
		$funds   	= $balance;			
		$funds   	= ($funds > 0) ? $funds : '0';
				
		jimport( 'joomla.application.component.view');
		
		// Output HTML
		$view = new JView( array('name'=>'subscribe') );
		$view->title = $title.': '.$subtitle;
		$view->config = $this->config;
		$view->subscription = $subscription;
		$view->employer = $employer;
		$view->services = $services;
		$view->funds = $funds;
		$view->uid = $uid;
		$view->emp = $this->_emp;
		$view->task = $this->_task;
		$view->option = $this->_option;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	
	}
	
	//----------------------------------------------------------
	// Initial setup of default services
	//----------------------------------------------------------
	
	protected function setupServices() 
	{
		ximport( 'subscriptions' );
		$database =& JFactory::getDBO();
		
		$objS = new Service($database);
		$now = date( 'Y-m-d H:i:s', time() );
		
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
	
	//----------------------------------------------------------
	// Intro screen for employers before they login
	//----------------------------------------------------------
	
	protected function intro_employer() 
	{
		// Set the page title
		$title = JText::_(strtoupper($this->_name));
		$title.= $this->industry ? ' '.JText::_('IN').' '.$this->industry : '';
		$subtitle = $this->_task== 'resumes' ? JText::_('Browse Resumes') : JText::_('Post a Job');
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title.': '.$subtitle );
		
		$japp =& JFactory::getApplication();
		$pathway =& $japp->getPathway();
		
		$pathway->addItem( $title, 'index.php?option='.$this->_option );
		$pathway->addItem( $subtitle, 'index.php?option='.$this->_option.a.'task='.$this->_task );
				
		// Add the CSS to the template
		JobsController::getStyles();
		
		jimport( 'joomla.application.component.view');
		
		// Output HTML
		$view = new JView( array('name'=>'introemp') );
		$view->title = $title.': '.$subtitle;
		$view->config = $this->config;
		$view->task = $this->_task;
		$view->option = $this->_option;
		$view->banking = $this->_banking;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
			
		return;		
	
	}
		
	//----------------------------------------------------------
	// Link to Add Resume (goes to profile "Resume" tab)
	//----------------------------------------------------------
	
	public function addresume()
	{
		$juser    =& JFactory::getUser();
		
		// Login required
		if ($juser->get('guest')) {
			$msg = JText::_('Please login to post or access your resume');
			$this->login($msg);
		}
		else {
			$this->_redirect = JRoute::_('index.php?option=com_members'.a.'id='.$juser->get('id').a.'active=resume');
			return;
		}
	
	}
	
	//----------------------------------------------------------
	// Save job application
	//----------------------------------------------------------
	
	public function saveapp()
	{
		$database =& JFactory::getDBO();
		$juser    =& JFactory::getUser();
		
		// Incoming job id
		$id 	  = JRequest::getInt( 'jid', 0 );
		$appid 	  = JRequest::getInt( 'appid', 0, 'post' );
		$msg 	  = $this->_task == 'withdraw' ? JobsHtml::passed (JText::_('Your application has been withdrawn per your request.')) : JobsHtml::passed (JText::_('Thank you for applying to this job. The employer may contact you directly via information you provided in your resume. Good luck with your job search!'));
		$msg  	  = $appid ? JobsHtml::passed (JText::_('Your application has been updated. Thank you!')) : $msg;
		
		// Login required
		if ($juser->get('guest')) {
			$this->login(JText::_('Please login to save job aplication.'));
			return;
		}
		
		$job = new Job ( $database );
		$ja = new JobApplication ( $database );
		$now = date( 'Y-m-d H:i:s', time() );
		
		if(!$job->load($id)) {
			$msg = JobsHtml::error (JText::_('Sorry, there was an error with your application. Please try again.'));
		}
		else if(!$ja->loadApplication ($juser->get('id'), $id) && $this->_task == 'withdraw') {
			$msg = JobsHtml::error (JText::_('Sorry, there was an error withdrawing your application. Please try again.'));
		}
		else  
		{			
			if($this->_task == 'withdraw') {
				$ja->withdrawn 	= $now;
				$ja->status 	= 2;
				$ja->reason 	= JRequest::getVar( 'reason', '' );
			}
			else {
				// Save new information
				$ja->bind( $_POST );
				$ja->applied = $appid ? $ja->applied : $now;
				$ja->status 	=	1;
			}
				
			if (!$ja->store()) {
				echo JobsHtml::alert( $ja->getError() );
				exit();
			}
		}
		
		$this->jobid 	= $id;
		$this->_msg 	= $msg;
		
		// return to the job posting
		$this->job();
		return;	
		
	}
	
	//----------------------------------------------------------
	// Apply to a job
	//----------------------------------------------------------
	
	public function apply()
	{
		$database =& JFactory::getDBO();
		$juser    =& JFactory::getUser();
		
		// Incoming
		$id 	  = JRequest::getInt( 'id', 0 );
		
		// Login required
		if ($juser->get('guest')) {
			$this->login(JText::_('Please login to apply to this job ad.'));
			return;
		}
		
		// Build the page title
		$title  = JText::_(strtoupper($this->_name));
		$title.= $this->industry ? ' '.JText::_('IN').' '.$this->industry : '';
		$document =& JFactory::getDocument();
		
		// Add the CSS to the template
		JobsController::getStyles();
		
		// Set breadcrumbs
		$japp =& JFactory::getApplication();
		$pathway =& $japp->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( $title, 'index.php?option='.$this->_option );
		}
		
		$job = new Job ( $database );
		if(!$job->load($id)) {
			$document->setTitle( $title.': '.JText::_('Apply'));
			echo JobsHtml::error (JText::_('Sorry, job posting not active.'));
			return;	
		}
					
		$ja = new JobApplication ( $database );
		
		// if application already exists, load it to edit
		if($ja->loadApplication ($juser->get('id'), $id) && $ja->status != 2) {
			$this->_task = 'editapp';
		}
		
		if($this->_task != 'editapp') {
			$ja->cover = '';
		}
		
		$subtitle = $this->_task=='editapp' ? JText::_('Edit Application') : JText::_('Apply');
		
		// Set the page title
		$document->setTitle( $title.': '.$subtitle);
		$pathway->addItem( $job->title, 'index.php?option='.$this->_option.a.'task=job'.a.'id='.$job->id  );
		$pathway->addItem( $subtitle, 'index.php?option='.$this->_option.a.'task=apply'.a.'id='.$job->id  );
		
		/*
		if($ja->loadApplication ($juser->get('id'), $id)) {
			//echo JobsHtml::error (JText::_('You have previously applied to this job.'));
			//return;			
		}	*/	
		
		$js = new JobSeeker ( $database );
		$seeker = $js->getSeeker($juser->get('id'), $juser->get('id'));
		$seeker = count($seeker) > 0 ? $seeker[0] : NULL;
	
		jimport( 'joomla.application.component.view');
		
		// Output HTML
		$view = new JView( array('name'=>'apply') );
		$view->title = $title.': '.$subtitle;
		$view->config = $this->config;
		$view->emp = $this->_emp;
		$view->job = $job;
		$view->seeker = $seeker;
		$view->admin = $this->_admin;
		$view->error = $this->_error;
		$view->application = $ja;
		$view->task = $this->_task;
		$view->option = $this->_option;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
				
	}
	
	//----------------------------------------------------------
	// Job posting
	//----------------------------------------------------------
	
	public function job()
	{
		$database =& JFactory::getDBO();
		$juser    =& JFactory::getUser();
		
		// Incoming
		$id 	  = JRequest::getInt( 'id', 0 );
		$id 	  = !$id && $this->jobid ? $this->jobid : $id;
		$msg  	  = $this->_msg ? $this->_msg : '';
		
		$obj = new Job ( $database );
		$admin = $this->_admin && !$this->_emp ? 1 : 0;
		$job = $obj->get_opening ($id, $juser->get('id'), $admin);
						
		// Build the page title
		$title  = JText::_(strtoupper($this->_name));
		$title.= $this->industry ? ' '.JText::_('IN').' '.$this->industry : '';
		$document =& JFactory::getDocument();
		
		// Add the CSS to the template
		JobsController::getStyles();
		JobsController::getScripts();
		
		// Set breadcrumbs
		$japp =& JFactory::getApplication();
		$pathway =& $japp->getPathway();
		
		if(!$job) {
			$pathway->addItem( $title, 'index.php?option='.$this->_option );
			$document->setTitle( $title.': '.JText::_('Job Posting'));
			echo JobsHtml::div( JobsHtml::hed( 2, $title.': '.JText::_('Job Posting')), 'full', 'content-header' );
			echo JobsHtml::error (JText::_('Sorry, job posting not active or does not exist.'));
			return;	
		}
		
		if($juser->get('guest') && $job->status != 1) {
			// not autorized
			echo JobsHtml::div( JobsHtml::hed( 2, $title.': '.JText::_('Job Posting').' '.$job->code), 'full', 'content-header' );
			$error = JText::_('Sorry, you are not authorized to view this job posting.');
			$error.= $juser->get('guest') ? ' '.JText::_('You may need to login to the site first, to view this restricted ad.'): '';
			echo JobsHtml::error ($error);
			return;	
		}
		if($job->status != 1 && !$this->_admin && (!$this->_emp && $juser->get('id') != $job->employerid )) {
			// not autorized
			echo JobsHtml::div( JobsHtml::hed( 2, $title.': '.JText::_('Job Posting').' '.$job->code), 'full', 'content-header' );
			$error = JText::_('Sorry, you are not authorized to view this job posting. It is possible that the employer has taken this posting down.');
			echo JobsHtml::error ($error);
			return;	
		}		

		if($juser->get('id') == $job->employerid && !$this->_emp && !$admin) {
			// check validity of subscription
			$this->_msg = JobsHtml::warning(JText::_('Cannot proceed without a valid subscription. Your subscription is pending approval or expired.'));
			$this->dashboard();
			return;
		}
			
		$subtitle = $job->status==4 ? JText::_('Preview Ad').' '.$job->code : $job->title;
		
		// Set the page title
		$document->setTitle( $title.': '.$subtitle);
		
		$pathway->addItem( $title, 'index.php?option='.$this->_option );
		if ($job->id) {
		$pathway->addItem( $job->title, 'index.php?option='.$this->_option.a.'task=job'.a.'id='.$job->id  );
		}
		
		$jt = new JobType ( $database );
		$jc = new JobCategory ( $database );
		$job->type = $jt->getType($job->type);
		$job->cat = $jc->getCat($job->cid);
		
		$ja = new JobApplication ( $database );
		$job->applications = ($this->_admin or ($this->_emp && $juser->get('id') == $job->employerid ) ) ? $ja->getApplications ($job->id) : array();
		
		// get profile info
		$job->withdrawnlist = array();
		if(count($job->applications) > 0 ) {
			$js = new JobSeeker ( $database );
			foreach ($job->applications as $ap) {			
				$seeker = $js->getSeeker($ap->uid, $job->employerid);
				$ap->seeker = (!$seeker or count($seeker)==0) ? NULL : $seeker[0];
				
				if($ap->status == 2) {
					$job->withdrawnlist[] = $ap;
				}
			}
		}
				
		jimport( 'joomla.application.component.view');
		
		// Output HTML
		$view = new JView( array('name'=>'job') );
		$view->title = $title.': '.$subtitle;
		$view->config = $this->config;
		$view->emp = $this->_emp;
		$view->job = $job;
		$view->msg = $msg;
		$view->admin = $this->_admin;
		$view->error = $this->_error;
		$view->task = $this->_task;
		$view->option = $this->_option;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
				
	}
	
	//----------------------------------------------------------
	// Save job
	//----------------------------------------------------------
	
	public function savejob()
	{
		$database =& JFactory::getDBO();
		$juser    =& JFactory::getUser();
		
		// Incoming
		$id 		= JRequest::getInt( 'id', 0 );
		$employerid = JRequest::getInt( 'employerid', 0 );
		$min = ($this->_task == 'confirmjob' or $this->_task == 'unpublish' or $this->_task == 'reopen' or $this->_task == 'remove') ? 1 : 0;
		
		// so that we don't make another entry
		$id = !$id && $this->jobid ? $this->jobid : $id;
			
		// Login required
		if ($juser->get('guest')) {
			$this->intro_employer();
			return;
		}
		
		// Do we need admin approval for job publications?
		$autoapprove = isset($this->config->parameters['autoapprove'])  ? $this->config->parameters['autoapprove'] : 1;	
		
		$job = new Job ( $database );
		$jobadmin = new JobAdmin ( $database );
		$employer = new Employer ( $database );
		
		$admin = $this->_admin && !$this->_emp ? 1 : 0;
				
		if($id) {
		
			if(!$job->load($id)) {
				JError::raiseError( 404, JText::_('Error: job not found.') );
				return;
			}
		
			// check if user is authorized to edit		
			if($this->_admin or $jobadmin->isAdmin($juser->get('id'), $id) or $juser->get('id') == $job->employerid) {		
				// we are editing
				$id = $job->id;
			}
			else {
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
			
			$job->editedBy = $juser->get('id');
			$job->edited = date('Y-m-d H:i:s');
						
		} else {
			$job->added = date('Y-m-d H:i:s');
			$job->addedBy = $juser->get('id');
		}
		
		
		$employerid = $id ? $job->employerid : $employerid;
		$job->employerid = $employerid;
		
		// load Employer
		if (!$employer->loadEmployer($employerid)) {
			JError::raiseError( 404, JText::_('Employer information not found.') );
			return;
		}
		
		ximport( 'subscriptions' );
		if($juser->get('id') == $job->employerid && !$this->_emp && !$admin) {
			// check validity of subscription
			$this->_msg = JobsHtml::warning(JText::_('Cannot proceed without a valid subscription. Your subscription is pending approval or expired.'));
			$this->dashboard();
			return;
		}
		
		if(!$min) {		
			$job->description   	= rtrim(stripslashes($_POST['description']));
			$job->title   			= rtrim(stripslashes($_POST['title']));
			$job->companyName   	= rtrim(stripslashes($_POST['companyName']));
			$job->companyLocation   = rtrim(stripslashes($_POST['companyLocation']));
			
			// missing required information
			if(!$job->description or !$job->title or !$job->companyName or !$job->companyLocation) {
				$job->bind( $_POST );
				$this->job = $job;
				$this->jobid = $id;
				$this->_error = JobsHtml::error(JText::_('Missing some required information. Please ensure all required fields are filled in.'));
				$this->editjob();
				return;
				//echo JobsHtml::alert( JText::_('Missing some required information. Please ensure all required fields are filled in.') );
				//exit();
			}
		}
		
		$job->companyLocationCountry = $job->companyLocationCountry ? $job->companyLocationCountry : 'United States' ;
		
		// Save new information
		if(!$min) {	
			$job->bind( $_POST );
		}
		else if ($job->status==4 && $this->_task == 'confirmjob') {
			
			// make sure we aren't over quota			
			$allowed_ads = $admin && $employerid==1 ? 1 : $this->checkQuota ($job, $juser, $database);
			
			if($allowed_ads <=0 ) {
				$this->_msg = JobsHtml::error (JText::_('Sorry, cannot publish this position due to the limit of active ads allowed with your employer subscription service. '));
			}
			else {					
				// confirm 
				$job->status 	= !$autoapprove && !$admin ? 0 : 1;			
				$job->opendate 	= !$autoapprove && !$admin ? '' : date('Y-m-d H:i:s'); // set open date as of now, if confirming new ad publication
				$this->_msg 	= !$autoapprove && !$admin 
									? JobsHtml::passed (JText::_('Your job ad is now being reviewed by administrators and will be published as soon as it it approved.')) 
									: JobsHtml::passed (JText::_('Your job ad has been successfully posted'));
			}
		}
		else if ($job->status==1 && $this->_task == 'unpublish') {
			$job->status = 3;
			$this->_msg = JobsHtml::warning (JText::_('Your job ad has been unpublished.'));
		}
		
		else if ($job->status==3 && $this->_task == 'reopen') {
			// make sure we aren't over quota			
			$allowed_ads = $admin && $employerid==1 ? 1 : $this->checkQuota ($job, $juser, $database);
			
			if($allowed_ads <= 0 ) {
				$this->_msg = JobsHtml::error (JText::_('Sorry, cannot re-open this position due to the limit of active ads allowed with your employer subscription service.'));
			}
			else {						
				$job->status = 1;
				$this->_msg = JobsHtml::warning (JText::_('Your job posting has been re-opened.'));
			}
		}
		else if ($this->_task == 'remove') {
			$job->status = 2;	
		}
		
		// get unique number code for this new job posting
		if(!$id) {
			$subscription = new Subscription($database);
			$code = $subscription->generateCode(8, 8, 0, 1, 0);
			$job->code = $code;
		}		
				
		if (!$job->store()) {
			echo JobsHtml::alert( $job->getError() );
			exit();
		}
		if (!$job->id) {
			$job->checkin();
		}
		
		$this->jobid = $job->id;
		$this->job();		
	}
	
	//----------------------------------------------------------
	// Get service params
	//----------------------------------------------------------
	
	public function getServiceParams (&$service)
	{
		
		$params 			=& new JParameter( $service->params );
		$service->maxads  	= $params->get( 'maxads', '' );
		$service->maxads 	= intval(str_replace(" ","",$service->maxads));				
		
	}
	
	//----------------------------------------------------------
	// Check job ad quota depending on subscription
	//----------------------------------------------------------
	
	public function checkQuota ($job, $juser, $database)
	{
		// make sure we aren't over quota
		ximport( 'subscriptions' );
		$service = new Service($database);	
		$servicename = $service->getUserService($juser->get('id'));
		if(!$service->loadService($servicename)) {
			return 0;			
		}
		else {
			$this->getServiceParams (&$service);
			$maxads = $service->maxads > 0 ? $service->maxads : 1;
			$activejobs = $job->countMyActiveOpenings ($juser->get('id'), 1);
			$allowed_ads = $maxads - $activejobs;
			return $allowed_ads;
		}
		
	}
	
	//----------------------------------------------------------
	// Add/edit job form
	//----------------------------------------------------------
	
	public function editjob()
	{
		$database 	=& JFactory::getDBO();
		$juser    	=& JFactory::getUser();
		$jconfig 	=& JFactory::getConfig();

		// Incoming
		$id 	= JRequest::getInt( 'id', 0 );
		$empid  = $this->_admin ? 1 : $juser->get('id');
		
		$id = !$id && $this->jobid ? $this->jobid : $id;		
		
		// Login required
		if ($juser->get('guest')) {
			if($this->allowsubscriptions) {
			$this->intro_employer();
			
			}
			else {
			$this->login();
			}
			return;
		}
					
		$job = new Job ( $database );
		$jobadmin = new JobAdmin ( $database );
		$employer = new Employer ( $database );
		ximport( 'subscriptions' );
		
		if(!$this->_emp && !$this->_admin) {
			// need to subscribe first

			$employer = new Employer ( $database );
			if ($employer->loadEmployer($empid)) {

				//do we have a pending subscription?
				$subscription = new Subscription($database);
				if($subscription->loadSubscription ($employer->subscriptionid, $juser->get('id'), '', $status=array(0))) {
					$this->_msg = JobsHtml::warning(JText::_('Your subscription is pending approval. Access to Employer Services will be granted once the subscription is approved.'));
					$this->dashboard();
					return;
				}
			}
				
			// send to subscription page
			$this->subscribe();
			return;
		}
		
		if($id) {
		
			if(!$job->load($id)) {
				JError::raiseError( 404, JText::_('Error: job not found.') );
				return;
			}
		
			// check if user is authorized to edit		
			if($this->_admin or $jobadmin->isAdmin($juser->get('id'), $id) or $juser->get('id') == $job->employerid) {		
				// we are editing
				$id = $job->id;
			}
			else {
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}			
			
		}
		
		// display with errors
		if($this->job) {
			$job = $this->job;
		}
	
		
		$uid = $id ? $job->employerid : $juser->get('id');
				
		$job->admins = $id ? $jobadmin->getAdmins($id) : array($juser->get('id')); 
		
		// How many active jobs does the user manage?
		//$activejobs = $job->countMyActiveOpenings ($juser->get('id'), 1);
		
		// Get the member's info
		ximport('xprofile');
		$profile = new XProfile();
		$profile->load( $uid );
		
		// load Employer
		if (!$employer->loadEmployer($uid) && !$this->_admin) {
			JError::raiseError( 404, JText::_('Employer information not found.') );
			return;
		}
		else if(!$employer->id && $this->_admin) {
			// site admin
			$employer->uid = 1;
			$employer->subscriptionid = 1;
			$employer->companyName 		= $jconfig->getValue('config.sitename');
			$employer->companyLocation  = '';
			$employer->companyWebsite   = $jconfig->getValue('config.live_site');
			$uid = 1; // site admin
		}
		
		// check level of service
		/*
		if(!$id) {
			$objS = new Service($database);	
			$maxads = isset($this->config->parameters['maxads']) && intval($this->config->parameters['maxads']) > 0  ? $this->config->parameters['maxads'] : 3;	
			$service = $objS->getUserService($juser->get('id'));
			$allowed_ads = $service == 'employer_basic' ? 1 - $activejobs : $maxads - $activejobs;
			
			if($allowed_ads <=0 ) {
				$this->dashboard();
				return;
			}					
		}*/
				
		// Add the CSS to the template
		$this->getStyles();
		$this->getScripts();
			
		$jt = new JobType ( $database );
		$jc = new JobCategory ( $database );
		
		// get job types			
		$types = $jt->getTypes();
		$types[0] = JText::_('Any type');
				
		// get job categories
		$cats = $jc->getCats();
		$cats[0] = JText::_('No specific category');
		
		// Build the page title
		$title  = JText::_(strtoupper($this->_name));
		$subtitle = $this->_task=='addjob' ? JText::_('TASK_ADD_JOB') : JText::_('TASK_EDIT_JOB');
					
		$title.= $this->industry ? ' '.JText::_('IN').' '.$this->industry : '';
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title.': '.$subtitle);
		
		// Set breadcrumbs
		$japp =& JFactory::getApplication();
		$pathway =& $japp->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( $title, 'index.php?option='.$this->_option );
		}
		if($this->_task == 'addjob') {
		$pathway->addItem( JText::_('TASK_ADD_JOB'), 'index.php?option='.$this->_option.a.'task=addjob'  );
		}
		else if ($id) {
		$pathway->addItem( $job->title, 'index.php?option='.$this->_option.a.'task=job'.a.'id='.$job->id  );		
		$pathway->addItem( JText::_('TASK_EDIT_JOB'), 'index.php?option='.$this->_option.a.'task=editjob'.a.'id='.$job->id  );
		}
		
		jimport( 'joomla.application.component.view');
		
		// Output HTML
		$view = new JView( array('name'=>'editjob') );
		$view->title = $title.': '.$subtitle;
		$view->config = $this->config;
		$view->uid = $uid;
		$view->profile = $profile;
		$view->emp = $this->_emp;
		$view->job = $job;
		$view->jobid = $id;
		$view->types = $types;
		$view->cats = $cats;
		$view->employer = $employer;
		$view->admin = $this->_admin;
		$view->error = $this->_error;
		$view->task = $this->_task;
		$view->option = $this->_option;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
						
		//echo JobsHtml::editjob ( $title.': '.$subtitle, $id, $job, $otherjobs, $this->_error, $this->_option, $this->_admin, $this->_task, $juser) ;
		
	}

	//----------------------------------------------------------
	// Authorizations
	//----------------------------------------------------------
	
	public function authorize_employer($admin = 0)
	{
		
		$database =& JFactory::getDBO();
		$juser    =& JFactory::getUser();
		
		$emp = 0;
		
		$employer = new Employer ( $database );
		if($admin) {
			$adminemp = $employer->isEmployer($juser->get('id'), 1);
			if(!$adminemp) {
				// will require setup only once
				ximport( 'subscriptions' );
				$subscription = new Subscription($database);
				$subscription->status = 1;
				$subscription->uid = 1;
				$subscription->units = 72;
				$subscription->serviceid = 1; 
				$subscription->expires = $newexprire = date("Y-m-d",strtotime("+ 72 months"));
				$subscription->added = date( 'Y-m-d H:i:s', time() );	
				
				if (!$subscription->store()) {
				echo JobsHtml::alert( $subscription->getError() );
				exit();
				}
				
				if (!$subscription->id) {
				$subscription->checkin();
				}
				
				// make sure we have dummy admin employer account
				$jconfig 	=& JFactory::getConfig();
				$employer->uid = 1;
				$employer->subscriptionid = $subscription->id;
				$employer->companyName 		= $jconfig->getValue('config.sitename');
				$employer->companyLocation  = '';
				$employer->companyWebsite   = $jconfig->getValue('config.live_site');
				
				// save employer information		
				if (!$employer->store()) {
					echo JobsHtml::alert( $employer->getError() );
					exit();
				}
				
				
			}
		}
		else {
			$emp = $employer->isEmployer($juser->get('id'));
		}
		
		$this->_emp = $emp;
	}
	
	//------------
	
	public function authorize_admin($admin = 0)
	{
		$juser =& JFactory::getUser();
		if(!$juser->get('guest')) {
			// Check if they're a site admin (from LDAP)
			$xuser =& XFactory::getUser();
			if (is_object($xuser)) {
				$app =& JFactory::getApplication();
				if (in_array(strtolower($app->getCfg('sitename')), $xuser->get('admin'))) {
					$admin = 1;
				}
			}
				
			// Check if they're a site admin (from Joomla)
			if ($juser->authorize($this->_option, 'manage')) {
				$admin = 1;
			}
			
			// check if they belong to a dedicated admin group
			$admingroup = (isset($this->config->parameters['admingroup']) && $this->config->parameters['admingroup'] != '' ) ? $this->config->parameters['admingroup'] : '' ;
			if($admingroup) {
				ximport('xgroup');
				ximport('xuserhelper');
				
				$ugs = XUserHelper::getGroups( $juser->get('id') );
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
	
	//----------------------------------------------------------
	// Search preferences
	//----------------------------------------------------------
	
	public function updatePrefs($database, $juser, $category = 'resume')
	{
		
		$saveprefs  = JRequest::getInt( 'saveprefs', 0, 'post');
		
		$p = new Prefs($database);
		
		$filters = $this->getFilters (0, 0, 0);
		$text = 'filterby='.$filters['filterby'].'&amp;match=1&amp;search='.$filters['search'].'&amp;category='.$filters['category'].'&amp;type='.$filters['type'].'&amp;sortby=';
		
		if ($category == 'job' && isset($_POST["performsearch"])) {
				$text .= $filters['sortby'];
				if (!$p->loadPrefs($juser->get('id'), $category)) {
					$p = new Prefs($database);
					$p->uid = $juser->get('id');
					$p->category = $category;
				}
				$p->filters = $text;
				
				// Store content
				if (!$p->store()) {
					echo JobsHtml::alert( $p->getError() );
					exit();
				}
				
		}
		else {
		
			if($saveprefs && isset($_POST["performsearch"])) {
				
				if (!$p->loadPrefs($juser->get('id'), $category)) {
					$p = new Prefs($database);
					$p->uid = $juser->get('id');
					$p->category = $category;
					$text .= 'bestmatch';
				}
				else {
					$text .= $filters['sortby'];
				}
				
				$p->filters = $text;
				
				// Store content
				if (!$p->store()) {
					echo JobsHtml::alert( $p->getError() );
					exit();
				}
			}
			else if ($p->loadPrefs($juser->get('id'), $category) && isset($_POST["performsearch"]))  {
				// delete prefs
				$p->delete();
			}
		}
		
	}
	
	//-----------
	
	public function getPrefs($database, $juser, $category = 'resume')
	{
		
		$p = new Prefs($database);
		if($p->loadPrefs($juser->get('id'), $category)) {
						
			if(isset($p->filters) && $p->filters) {
				// get individual filters
				$col = explode("&amp;",$p->filters);
		
				if(count($col > 0 )) {
					foreach ($col as $c) {
						$nuk = explode("=",$c);
						
						// set filter variables
						$this->setVar ($nuk[0], $nuk[1]);
					}
				}
							
			}
		}	
		
	}
	
	//----------------------------------------------------------
	// Search filters
	//----------------------------------------------------------
	
	public function getFilters($admin=0, $emp = 0, $checkstored = 1, $jobs = 0)
	{
		
		// Query filters defaults
		$filters = array();
		
		// jobs filters
		if($jobs) {
		$filters['sortby'] 	 =  $this->getVar("sortby") && $checkstored ? $this->getVar("sortby") : trim(JRequest::getVar( 'sortby', 'category' ));
		$filters['category'] = $this->getVar("category") && $checkstored ? $this->getVar("category") : JRequest::getInt( 'category',  'all');
		}
		else {		
		$filters['sortby'] = $this->getVar("sortby") && $checkstored ? $this->getVar("sortby") : trim(JRequest::getVar( 'sortby', 'lastupdate' ));				
		$filters['category'] = $this->getVar("category") && $checkstored ? $this->getVar("category") : JRequest::getInt( 'category',  0);
		}
		
		$filters['type'] 	 = $this->getVar("type") && $checkstored ? $this->getVar("type") : JRequest::getInt( 'type',  0);
		$filters['search'] = $this->getVar("search") && $checkstored ? $this->getVar("search") : trim(JRequest::getVar( 'q', '' ));
		$filters['filterby'] = trim(JRequest::getVar( 'filterby', 'all' ));	
		
		// did we get stored prefs?
		$filters['match'] = $this->getVar("match") && $checkstored ? $this->getVar("match") : JRequest::getInt( 'match', 0 );

		// Paging vars
		$filters['limit'] = JRequest::getInt( 'limit', 25 );
		$filters['start'] = JRequest::getInt( 'limitstart', 0, 'get' );
		
		// admins and employers
		$filters['admin'] = $admin;
		$filters['emp'] = $emp;
			
		// Return the array
		return $filters;
	}
	
		//----------------------------------------------------------
	// Misc
	//----------------------------------------------------------

	public function server($index = '')
	{		
		if (!isset($_SERVER[$index])) {
			return FALSE;
		}
		
		return $_SERVER[$index];
	}	

	//-----------

	public function mkt($stime)
	{
		if ($stime && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $stime, $regs )) {
			$stime = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return $stime;
	}
	
	//-----------
	
	public function timeAgoo($timestamp)
	{
		// Store the current time
		$current_time = time();
		
		// Determine the difference, between the time now and the timestamp
		$difference = $current_time - $timestamp;
		
		// Set the periods of time
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		
		// Set the number of seconds per period
		$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);
		
		// Determine which period we should use, based on the number of seconds lapsed.
		// If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
		// Go from decades backwards to seconds
		for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);
		
		// Ensure the script has found a match
		if ($val < 0) $val = 0;
		
		// Determine the minor value, to recurse through
		$new_time = $current_time - ($difference % $lengths[$val]);
		
		// Set the current value to be floored
		$number = floor($number);
		
		// If required create a plural
		if($number != 1) $periods[$val].= "s";
		
		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);
		
		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0)){
			$text .= JobsController::TimeAgoo($new_time);
		}
		
		return $text;
	}
	
	//-----------
	
	public function timeAgo($timestamp) 
	{
		$text = $this->timeAgoo($timestamp);
		
		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];
		//$text .= ($parts[2]) ? ' '.$parts[2].' '.$parts[3] : '';
		return $text;
	}

	//------------
	
	public function purifyText( &$text ) 
	{
		$text = preg_replace( '/{kl_php}(.*?){\/kl_php}/s', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		$text = preg_replace( '/&nbsp;/', ' ', $text );
		$text = preg_replace( '/&amp;/', ' ', $text );
		$text = preg_replace( '/&quot;/', ' ', $text );
		$text = strip_tags( $text );
		return $text;
	}
	
	//------------	
}
?>
