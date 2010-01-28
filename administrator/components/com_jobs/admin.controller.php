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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JobsController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $error  = NULL;

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
	
	private function getTask()
	{
		$task = JRequest::getVar( 'task', '' );
		$this->_task = $task;
		return $task;
	}
	
	//-----------

	public function execute()
	{
		
		$database =& JFactory::getDBO();
		
		// Load the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		
		switch ( $this->getTask() ) 
		{
			// Jobs
			case 'jobs':   			$this->jobs();     		break;
			case 'add':          	$this->edit(1);         break;
			case 'edit':         	$this->edit(0);         break;
			case 'save':         	$this->save();          break;
			case 'remove':       	$this->remove();        break;
			case 'cancel':       	$this->jobs();        	break;
						
			// Job Categories
			case 'categories':   	$this->categories();   	break;
			case 'cancelcat':   	$this->categories();    break;
			case 'newcat':      	$this->newcat();       	break;
			case 'editcat':     	$this->editcat();      	break;
			case 'savecat':     	$this->savecat();      	break;
			case 'deletecat':   	$this->deletecat();    	break;
			case 'saveorder':   	$this->saveorder();    	break;
			
			// Job Types
			case 'types':   		$this->types();   		break;
			case 'canceltype':   	$this->types();     	break;
			case 'newtype':      	$this->newtype();       break;
			case 'edittype':     	$this->edittype();      break;
			case 'savetype':     	$this->savetype();      break;
			case 'deletetype':   	$this->deletetype();    break;
			
			// List of jobs
			default: 				$this->jobs(); break;
		}
		
	}	
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	//---------------------
	// Jobs List
	//---------------------
	
	public function jobs()
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();
		
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components'.DS.$this->_option.DS.'admin.jobs.css');
		
		// Get configuration
		$config = JFactory::getConfig();
		
		// Incoming
		$filters = array();
		$filters['limit']    	= $app->getUserStateFromRequest($this->_option.'.jobs.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start']   	= $app->getUserStateFromRequest($this->_option.'.jobs.limitstart', 'limitstart', 0, 'int');
		$filters['category']   	= trim($app->getUserStateFromRequest($this->_option.'.jobs.category','category', 'all'));
		$filters['sortby']     	= trim($app->getUserStateFromRequest($this->_option.'.jobs.sortby', 'filter_order', 'added'));
		$filters['filterby']    = '';
		$filters['sort_Dir'] 	= trim($app->getUserStateFromRequest($this->_option.'.jobs.sortdir', 'filter_order_Dir', 'DESC'));
		$filters['search']		= urldecode(trim($app->getUserStateFromRequest($this->_option.'.jobs.search','search', '')));
		
		// Get data
		$obj = new Job( $database );
		$jobs = $obj->get_openings ($filters, $juser->get('id'), 1);
		
		$total = $jobs ? count($jobs) : 0;
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total , $filters['start'], $filters['limit'] );

		// Output HTML
		JobsHtml::jobs( $database, $jobs, $pageNav, $this->_option, $filters, $this->config);
	
	}
	
	//---------------------
	// Save Job Posting
	//---------------------
	
	public function save()
	{
		$database =& JFactory::getDBO();
		$juser    =& JFactory::getUser();
		
		$data 		= array_map('trim',$_POST);
		$action	 	= JRequest::getVar( 'action', '' );
		$message	= JRequest::getVar( 'message', '' );
		$id 		= JRequest::getInt( 'id', 0 );
		$employerid = JRequest::getInt( 'employerid', 0 );
		$emailbody 	= '';
		$statusmsg	= '';
		
		
		$job = new Job ( $database );
		$employer = new Employer ( $database );
		
		if($id) {
			if(!$job->load($id)) {
				echo JobsHtml::alert(JText::_('Error: job not found.') );
				return;
			}
		}
		else { // saving new job
			ximport( 'subscriptions' );
			$subscription = new Subscription($database);
			$code = $subscription->generateCode(8, 8, 0, 1, 0);
			$job->code = $code;
			
			$job->added = date('Y-m-d H:i:s');
			$job->addedBy = $juser->get('id');	
		}
		
		$subject    = $id ? JText::_('Status update on your job ad #').$job->code : '';
		
		// save any new info
		$job->bind( $_POST );
		
		// some clean-up
		$job->description   	= rtrim(stripslashes($job->description));
		$job->title   			= rtrim(stripslashes($job->title));
		$job->companyName   	= rtrim(stripslashes($job->companyName));
		$job->companyLocation   = rtrim(stripslashes($job->companyLocation));
		
		
		// admin actions
		if($id) {
			switch($action)
			{
				case 'publish':
								
				// make sure we aren't over quota			
				$allowed_ads = $employerid==1 ? 1 : $this->checkQuota ($job, $employerid, $database);
				
				if($allowed_ads <= 0 ) {
					$statusmsg .= JobsHtml::error (JText::_('Failed to publish this ad because user is over the limit according to the terms of his/her subscription.'));
					$action = '';
				}
				else {						
					$job->status 	= 1;
					$job->opendate	=  date('Y-m-d H:i:s');
					$statusmsg .= JText::_('The job ad has been approved and published by site administrators.');		
				}				 
					
				break;
				
				case 'unpublish': 
				$job->status 	= 3;
				$statusmsg .= JText::_('The job ad has been unpublished by site administrators.');	
				break;
				
				case 'message':
				//$statusmsg = $message ? JText::_('Site administrators sent a new message.') : ''; 
				break;
				
				case 'delete':
				$job->status 	= 2; 
				$statusmsg .= JText::_('The job ad has been permanently deleted by site administrators.');	
				break;
				
			}
			
			$job->editedBy = $juser->get('id');
			$job->edited = date('Y-m-d H:i:s');			
					
		}
		
		if (!$job->store()) {
			echo JobsHtml::alert( $job->getError() );
			exit();
		}
		
		if (!$job->id) {
			$job->checkin();
		}
		
		if(($message && $action == 'message' && $id) or ($action && $action != 'message')) {
		
			// Email all the contributors
			$xhub =& XFactory::getHub();

			// E-mail "from" info
			$from = array();
			$from['email'] = $xhub->getCfg('hubSupportEmail');
			$from['name']  = $xhub->getCfg('hubShortURL').' '.JText::_('Jobs');
			
					
			$juri =& JURI::getInstance();

			$sef = JRoute::_('index.php?option='.$this->_option.a.'id='. $job->id);
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef,1,strlen($sef));
			}
			
			// start email message
			$emailbody .= $subject.':'.r.n;
			$emailbody .= '----------------------------------------------------------'.r.n;	
			$emailbody .= $statusmsg;
			if($message) {
				$emailbody .= "\r\n";
				$emailbody .= $message;
			}
			// Link to job ad
			$emailbody  .= "\r\n".JText::_('View job ad:').' '.$xhub->getCfg('hubLongURL').DS.'jobs'.DS.$id;
				
			
			JPluginHelper::importPlugin( 'xmessage' );
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger( 'onSendMessage', array( 'jobs_ad_status_changed', $subject, $emailbody, $from, array($job->addedBy), $this->_option ))) {
				$this->setError( JText::_('Failed to message users.') );
			}
		}
		
		// Redirect
		//$this->_redirect = 'index.php?option='.$this->_option.'&task=edit&id[]='.$job->id;
		$this->_redirect = 'index.php?option='.$this->_option;
		$msg  = JText::_('Job successfully saved.');
		$msg .= $statusmsg ? ' '.$statusmsg : ''; 
		$this->_message = JText::_($msg);
		
	}
	
	//----------------------------------------------------------
	// Check job ad quota depending on subscription
	//----------------------------------------------------------
	
	public function checkQuota ($job, $uid, $database)
	{
		// make sure we aren't over quota
		ximport( 'subscriptions' );
		$objS = new Service($database);	
		$maxads = isset($this->config->parameters['maxads']) && intval($this->config->parameters['maxads']) > 0  ? $this->config->parameters['maxads'] : 3;	
		$service = $objS->getUserService($uid);
		$activejobs = $job->countMyActiveOpenings ($uid, 1);
		$allowed_ads = $service == 'employer_basic' ? 1 - $activejobs : $maxads - $activejobs;
		
		return $allowed_ads;
	}
	
	//---------------------
	// Remove Job Posting
	//---------------------
	
	public function remove()
	{
		$database =& JFactory::getDBO();
		
		// Incoming (expecting an array)
		$ids = JRequest::getVar( 'id', array() );

		// Ensure we have an ID to work with
		if (empty($ids)) {
			$this->_message = JText::_('No job selected');
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
		
		$row = new Job ( $database );
		
		foreach ($ids as $id) 
		{
			// Delete the type
			$row->delete( $id );
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('Job(s) successfully removed');
		
	}
	
	//---------------------
	// Edit Job Posting
	//---------------------
	
	protected function edit( $isnew=0 ) 
	{
		$juser 		=& JFactory::getUser();
		$database 	=& JFactory::getDBO();
		$jconfig 	=& JFactory::getConfig();
		
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components'.DS.$this->_option.DS.'admin.jobs.css');
		
		// Incoming job ID
		$id = JRequest::getVar( 'id', array(0) );
		if (is_array( $id )) {
			$id = $id[0];
		}		

		// Grab some filters for returning to place after editing
		$return = array();
		$return['sortby']   = JRequest::getVar( 'sortby', 'added' );

		$row = new Job( $database );
		
		$jobadmin = new JobAdmin ( $database );
		$employer = new Employer ( $database );
		ximport( 'subscriptions' );
		
		// Is this a new job?
		if (!$id) {
			$row->created      = date( 'Y-m-d H:i:s', time() );
			$row->created_by   = $juser->get('id');
			$row->modified     = '0000-00-00 00:00:00';
			$row->modified_by  = 0;
			$row->publish_up   = date( 'Y-m-d H:i:s', time() );
			$row->employerid   = 1; // admin
		}
		else if(!$row->load($id)) {
			echo JobsHtml::alert( JText::_('Error: job not found.'));
			exit();
		}
	
		$job = $row->get_opening ($id, $juser->get('id'), 1);
		
				
		// Get employer information
		if ($row->employerid != 1) {
			if(!$employer->loadEmployer($row->employerid)) {
				echo JobsHtml::alert( JText::_('Employer information not found.'));
				exit();
			}
		}
		else {
			// site admin
			$employer->uid = 1;
			$employer->subscriptionid = 1;
			$employer->companyName 		= $jconfig->getValue('config.sitename');
			$employer->companyLocation  = '';
			$employer->companyWebsite   = $jconfig->getValue('config.live_site');
		}
		
		// Get subscription info
		$subscription = new Subscription($database);
		$subscription->loadSubscription ($employer->subscriptionid, '', '', $status=array( 0, 1));
		
		// Get job types and categories
		$jt = new JobType ( $database );
		$jc = new JobCategory ( $database );
		
		// get job types			
		$types = $jt->getTypes();
		$types[0] = JText::_('Any type');
				
		// get job categories
		$cats = $jc->getCats();
		$cats[0] = JText::_('No specific category');
		
		// Output HTML
		JobsHtml::editJob( $this->config, $row, $job, $types, $cats, $employer, $this->_option, $isnew, $return, $subscription );
	}
	

	//---------------------
	// Categores
	//---------------------
	
	public function categories()
	{
		$database =& JFactory::getDBO();
		$app =& JFactory::getApplication();
		
		// Get configuration
		$config = JFactory::getConfig();
		
		// Instantiate an object
		$jc = new JobCategory ( $database );
		
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.cats.limit', 'limit', 25, 'int');
		$filters['start'] = $app->getUserStateFromRequest($this->_option.'.cats.limitstart', 'limitstart', 0, 'int');
		$filters['sort']  = trim($app->getUserStateFromRequest($this->_option.'.cats.sort', 'filter_order', 'ordernum'));
		$filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.cats.sortdir', 'filter_order_Dir', 'ASC'));		
			
		// Get records
		$rows = $jc->getCats($filters['sort'], $filters['sort_Dir'], 1);
		$total = count($rows);
		
		// initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		JobsHtml::categories( $rows, $pageNav, $this->_option, $filters );
	}
	
	//-----------
	
	function saveorder()
	{

		$database =& JFactory::getDBO();
		$order	= JRequest::getVar( 'order', array(), 'post', 'array' );
		JArrayHelper::toInteger($order);
		
		// Instantiate an object
		$jc = new JobCategory ( $database );

		if(count($order) > 0) {
			foreach ($order as $id => $num) { 
				$jc->updateOrder($id, $num);	
			}
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=categories';
		$this->_message = JText::_('Order successfully saved');
	}
	
	//-----------
	
	protected function newcat() 
	{
		$this->editcat();
	}
	
	//-----------

	protected function editcat()
	{
		$database =& JFactory::getDBO();
	
		// Incoming (expecting an array)
		$id = JRequest::getVar( 'id', array(0) );
		if (is_array($id)) {
			$id = $id[0];
		} else {
			$id = 0;
		}
		
		// Load the object
		$row =new JobCategory ( $database );
		$row->load( $id );
	
		// Output HTML
		JobsHtml::editCat( $row, $this->_option);
	}
	
	//-----------

	protected function savecat()
	{
		$database =& JFactory::getDBO();
	
		// Initiate extended database class
		$row = new JobCategory ( $database );
		if (!$row->bind( $_POST )) {
			echo JobsHtml::alert( $row->getError() );
			exit();
		}
		
		// Store new content
		if (!$row->store()) {
			echo JobsHtml::alert( $row->getError() );
			exit();
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=categories';
		$this->_message = JText::_('Type successfully saved');
	}
	
	//-----------

	protected function deletecat()
	{
		$database =& JFactory::getDBO();
		
		// Incoming (expecting an array)
		$ids = JRequest::getVar( 'id', array() );

		// Ensure we have an ID to work with
		if (empty($ids)) {
			$this->_message = JText::_('No category selected');
			$this->_redirect = 'index.php?option='.$this->_option.'&task=categories';
			return;
		}
		
		$jc = new JobCategory ( $database );
		
		foreach ($ids as $id) 
		{
			// Delete the type
			$jc->delete( $id );
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=categories';
		$this->_message = JText::_('Category(ies) successfully removed');
	}
	
	//---------------------
	// Types
	//---------------------
	
	public function types()
	{
		$database =& JFactory::getDBO();
		$app =& JFactory::getApplication();
		
		// Get configuration
		$config = JFactory::getConfig();
		
		// Instantiate an object
		$jt = new JobType ( $database );
		
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.types.limit', 'limit', 25, 'int');
		$filters['start'] = $app->getUserStateFromRequest($this->_option.'.types.limitstart', 'limitstart', 0, 'int');
		$filters['sort']  = trim($app->getUserStateFromRequest($this->_option.'.types.sort', 'filter_order', 'id'));
		$filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.types.sortdir', 'filter_order_Dir', 'ASC'));		
			
		// Get records
		$rows = $jt->getTypes($filters['sort'], $filters['sort_Dir']);
		$total = count($rows);
		
		// Load default types if none found
		if(!$total) {
			$default = array();
			$default[] = array (
					'id' => 0, 
					'category' => ucfirst(JText::_('Full-time')));
			$default[] = array (
					'id' => 0, 
					'category' => ucfirst(JText::_('Part-time')));
			$default[] = array (
					'id' => 0, 
					'category' => ucfirst(JText::_('Contract')));
			$default[] = array (
					'id' => 0, 
					'category' => ucfirst(JText::_('Internship')));
			$default[] = array (
					'id' => 0, 
					'category' => ucfirst(JText::_('Temporary')));
					
			foreach ($default as $d) {
				if (!$jt->bind($d)) {
					$this->_error = $jt->getError();
					return false;
				}
				if (!$jt->store()) {
					$this->_error = $jt->getError();
					return false;
				}
			}
			
			// Get new records
			$rows = $jt->getTypes();
			$total = count($rows);
		}
		
		// initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		JobsHtml::types( $rows, $pageNav, $this->_option, $filters );
	}
	
	//-----------
	
	protected function newtype() 
	{
		$this->edittype();
	}
	
	//-----------

	protected function edittype()
	{
		$database =& JFactory::getDBO();
	
		// Incoming (expecting an array)
		$id = JRequest::getVar( 'id', array(0) );
		if (is_array($id)) {
			$id = $id[0];
		} else {
			$id = 0;
		}
		
		// Load the object
		$row = new JobType ( $database );
		$row->load( $id );
	
		// Output HTML
		JobsHtml::editType( $row, $this->_option);
	}
	
	//-----------

	protected function savetype()
	{
		$database =& JFactory::getDBO();
	
		// Incoming
		//$_POST = array_map('trim',$_POST);

		// Initiate extended database class
		$row = new JobType ( $database );
		if (!$row->bind( $_POST )) {
			echo JobsHtml::alert( $row->getError() );
			exit();
		}
		
		// Store new content
		if (!$row->store()) {
			echo JobsHtml::alert( $row->getError() );
			exit();
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=types';
		$this->_message = JText::_('Type successfully saved');
	}
	
	//-----------

	protected function deletetype()
	{
		$database =& JFactory::getDBO();
		
		// Incoming (expecting an array)
		$ids = JRequest::getVar( 'id', array() );

		// Ensure we have an ID to work with
		if (empty($ids)) {
			$this->_message = JText::_('No type selected');
			$this->_redirect = 'index.php?option='.$this->_option.'&task=types';
			return;
		}
		
		$jt = new JobType ( $database );
		
		foreach ($ids as $id) 
		{
			// Delete the type
			$jt->delete( $id );
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=types';
		$this->_message = JText::_('Type(s) successfully removed');
	}



	//-----------


	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}
}
?>