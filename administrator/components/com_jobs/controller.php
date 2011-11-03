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
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

/**
 * Short description for 'JobsController'
 * 
 * Long description (if any) ...
 */
class JobsController extends Hubzero_Controller
{

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->_task = JRequest::getVar( 'task', '' );

		switch ($this->_task)
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

	/**
	 * Short description for 'jobs'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function jobs()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'jobs') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components'.DS.$this->_option.DS.'jobs.css');

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$view->filters = array();
		$view->filters['limit']    = $app->getUserStateFromRequest($this->_option.'.jobs.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start']    = $app->getUserStateFromRequest($this->_option.'.jobs.limitstart', 'limitstart', 0, 'int');
		$view->filters['category'] = trim($app->getUserStateFromRequest($this->_option.'.jobs.category','category', 'all'));
		$view->filters['sortby']   = trim($app->getUserStateFromRequest($this->_option.'.jobs.sortby', 'filter_order', 'added'));
		$view->filters['filterby'] = '';
		$view->filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.jobs.sortdir', 'filter_order_Dir', 'DESC'));
		$view->filters['search']   = urldecode(trim($app->getUserStateFromRequest($this->_option.'.jobs.search','search', '')));

		// Get data
		$obj = new Job( $this->database );
		$view->rows = $obj->get_openings($view->filters, $this->juser->get('id'), 1);

		$view->total = ($view->rows) ? count($view->rows) : 0;

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total , $view->filters['start'], $view->filters['limit'] );

		$view->config = $this->config;

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	//---------------------
	// Save Job Posting
	//---------------------

	/**
	 * Short description for 'save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$data 		= array_map('trim',$_POST);
		$action	 	= JRequest::getVar( 'action', '' );
		$message	= JRequest::getVar( 'message', '' );
		$id 		= JRequest::getInt( 'id', 0 );
		$employerid = JRequest::getInt( 'employerid', 0 );
		$emailbody 	= '';
		$statusmsg	= '';

		$job = new Job( $this->database );
		$employer = new Employer( $this->database );

		if ($id) {
			if (!$job->load($id)) {
				echo JobsHtml::alert(JText::_('Error: job not found.') );
				return;
			}
		} else { // saving new job
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_services'.DS.'tables'.DS.'subscription.php' );
			$subscription = new Subscription($this->database);
			$code = $subscription->generateCode(8, 8, 0, 1, 0);
			$job->code = $code;

			$job->added = date('Y-m-d H:i:s');
			$job->addedBy = $juser->get('id');
		}

		$subject = $id ? JText::_('Status update on your job ad #').$job->code : '';

		// save any new info
		$job->bind( $_POST );

		// some clean-up
		$job->description   	= rtrim(stripslashes($job->description));
		$job->title   			= rtrim(stripslashes($job->title));
		$job->companyName   	= rtrim(stripslashes($job->companyName));
		$job->companyLocation   = rtrim(stripslashes($job->companyLocation));

		// admin actions
		if ($id) {
			switch ($action)
			{
				case 'publish':
					// make sure we aren't over quota			
					$allowed_ads = $employerid==1 ? 1 : $this->checkQuota($job, $employerid, $this->database);

					if ($allowed_ads <= 0 ) {
						$statusmsg .= JobsHtml::error(JText::_('Failed to publish this ad because user is over the limit according to the terms of his/her subscription.'));
						$action = '';
					} else {
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

			$job->editedBy = $this->juser->get('id');
			$job->edited = date('Y-m-d H:i:s');
		}

		if (!$job->store()) {
			echo JobsHtml::alert( $job->getError() );
			exit();
		}

		if (!$job->id) {
			$job->checkin();
		}

		if (($message && $action == 'message' && $id) or ($action && $action != 'message')) {
			// Email all the contributors
			$jconfig =& JFactory::getConfig();

			// E-mail "from" info
			$from = array();
			$from['email'] = $jconfig->getValue('config.mailfrom');
			$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_('Jobs');

			$juri =& JURI::getInstance();

			$sef = JRoute::_('index.php?option='.$this->_option.'&id='. $job->id);
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef,1,strlen($sef));
			}

			// start email message
			$emailbody .= $subject.':'."\r\n";
			$emailbody .= '----------------------------------------------------------'."\r\n";
			$emailbody .= $statusmsg;
			if ($message) {
				$emailbody .= "\r\n";
				$emailbody .= $message;
			}
			// Link to job ad
			$emailbody  .= "\r\n".JText::_('View job ad:').' '.$jconfig->getValue('config.sitename').DS.'jobs'.DS.$id;

			JPluginHelper::importPlugin( 'xmessage' );
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger( 'onSendMessage', array( 'jobs_ad_status_changed', $subject, $emailbody, $from, array($job->addedBy), $this->_option ))) {
				$this->setError( JText::_('Failed to message users.') );
			}
		}

		// Redirect
		//$this->_redirect = 'index.php?option='.$this->_option.'&task=edit&id[]='.$job->id;
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message  = JText::_('Job successfully saved.');
		$this->_message .= $statusmsg ? ' '.$statusmsg : '';
	}

	//----------------------------------------------------------
	// Check job ad quota depending on subscription
	//----------------------------------------------------------

	/**
	 * Short description for 'checkQuota'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $job Parameter description (if any) ...
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      unknown $database Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function checkQuota($job, $uid, $database)
	{
		// make sure we aren't over quota
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_services'.DS.'tables'.DS.'service.php' );
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

	/**
	 * Short description for 'remove'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming (expecting an array)
		$ids = JRequest::getVar( 'id', array() );

		// Ensure we have an ID to work with
		if (empty($ids)) {
			$this->_message = JText::_('No job selected');
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}

		$row = new Job( $this->database );

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

	/**
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $isnew Parameter description (if any) ...
	 * @return     void
	 */
	protected function edit( $isnew=0 )
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'job') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		$jconfig =& JFactory::getConfig();

		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components'.DS.$this->_option.DS.'jobs.css');

		// Incoming job ID
		$id = JRequest::getVar( 'id', array(0) );
		if (is_array( $id )) {
			$id = $id[0];
		}

		// Grab some filters for returning to place after editing
		$view->return = array();
		$view->return['sortby'] = JRequest::getVar( 'sortby', 'added' );

		$view->row = new Job( $this->database );

		$view->jobadmin = new JobAdmin( $this->database );
		$view->employer = new Employer( $this->database );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_services'.DS.'tables'.DS.'subscription.php' );

		// Is this a new job?
		if (!$id) {
			$view->row->created      = date( 'Y-m-d H:i:s', time() );
			$view->row->created_by   = $this->juser->get('id');
			$view->row->modified     = '0000-00-00 00:00:00';
			$view->row->modified_by  = 0;
			$view->row->publish_up   = date( 'Y-m-d H:i:s', time() );
			$view->row->employerid   = 1; // admin
		} else if (!$view->row->load($id)) {
			echo JobsHtml::alert( JText::_('Error: job not found.'));
			exit();
		}

		$view->job = $view->row->get_opening($id, $this->juser->get('id'), 1);

		// Get employer information
		if ($view->row->employerid != 1) {
			if (!$view->employer->loadEmployer($view->row->employerid)) {
				echo JobsHtml::alert( JText::_('Employer information not found.'));
				exit();
			}
		} else {
			// site admin
			$view->employer->uid = 1;
			$view->employer->subscriptionid = 1;
			$view->employer->companyName 		= $jconfig->getValue('config.sitename');
			$view->employer->companyLocation  = '';
			$view->employer->companyWebsite   = $jconfig->getValue('config.live_site');
		}

		// Get subscription info
		$view->subscription = new Subscription($this->database);
		$view->subscription->loadSubscription($view->employer->subscriptionid, '', '', $status=array(0, 1));

		// Get job types and categories
		$jt = new JobType( $this->database );
		$jc = new JobCategory( $this->database );

		// get job types			
		$view->types = $jt->getTypes();
		$view->types[0] = JText::_('Any type');

		// get job categories
		$view->cats = $jc->getCats();
		$view->cats[0] = JText::_('No specific category');

		$view->config = $this->config;
		$view->isnew = $isnew;

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	//---------------------
	// Categores
	//---------------------

	/**
	 * Short description for 'categories'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function categories()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'categories') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Instantiate an object
		$jc = new JobCategory( $this->database );

		$view->filters = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.cats.limit', 'limit', 25, 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option.'.cats.limitstart', 'limitstart', 0, 'int');
		$view->filters['sort']  = trim($app->getUserStateFromRequest($this->_option.'.cats.sort', 'filter_order', 'ordernum'));
		$view->filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.cats.sortdir', 'filter_order_Dir', 'ASC'));

		// Get records
		$view->rows = $jc->getCats($view->filters['sort'], $view->filters['sort_Dir'], 1);
		$view->total = count($view->rows);

		// initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'saveorder'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function saveorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$order = JRequest::getVar( 'order', array(), 'post', 'array' );
		JArrayHelper::toInteger($order);

		// Instantiate an object
		$jc = new JobCategory( $this->database );

		if (count($order) > 0) {
			foreach ($order as $id => $num)
			{
				$jc->updateOrder($id, $num);
			}
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=categories';
		$this->_message = JText::_('Order successfully saved');
	}

	/**
	 * Short description for 'newcat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function newcat()
	{
		$this->editcat();
	}

	/**
	 * Short description for 'editcat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function editcat()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'category') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Incoming (expecting an array)
		$id = JRequest::getVar( 'id', array(0) );
		if (is_array($id)) {
			$id = $id[0];
		} else {
			$id = 0;
		}

		// Load the object
		$view->row =new JobCategory( $this->database );
		$view->row->load( $id );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'savecat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function savecat()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initiate extended database class
		$row = new JobCategory( $this->database );
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

	/**
	 * Short description for 'deletecat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function deletecat()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming (expecting an array)
		$ids = JRequest::getVar( 'id', array() );

		// Ensure we have an ID to work with
		if (empty($ids)) {
			$this->_message = JText::_('No category selected');
			$this->_redirect = 'index.php?option='.$this->_option.'&task=categories';
			return;
		}

		$jc = new JobCategory( $this->database );

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

	/**
	 * Short description for 'types'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function types()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'types') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Instantiate an object
		$jt = new JobType( $this->database );

		$view->filters = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.types.limit', 'limit', 25, 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option.'.types.limitstart', 'limitstart', 0, 'int');
		$view->filters['sort']  = trim($app->getUserStateFromRequest($this->_option.'.types.sort', 'filter_order', 'id'));
		$view->filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.types.sortdir', 'filter_order_Dir', 'ASC'));

		// Get records
		$view->rows = $jt->getTypes($view->filters['sort'], $view->filters['sort_Dir']);
		$view->total = count($view->rows);

		// Load default types if none found
		if (!$view->total) {
			$default = array();
			$default[] = array(
					'id' => 0,
					'category' => ucfirst(JText::_('Full-time')));
			$default[] = array(
					'id' => 0,
					'category' => ucfirst(JText::_('Part-time')));
			$default[] = array(
					'id' => 0,
					'category' => ucfirst(JText::_('Contract')));
			$default[] = array(
					'id' => 0,
					'category' => ucfirst(JText::_('Internship')));
			$default[] = array(
					'id' => 0,
					'category' => ucfirst(JText::_('Temporary')));

			foreach ($default as $d)
			{
				if (!$jt->bind($d)) {
					$this->setError( $jt->getError() );
					return false;
				}
				if (!$jt->store()) {
					$this->setError( $jt->getError() );
					return false;
				}
			}

			// Get new records
			$view->rows = $jt->getTypes();
			$view->total = count($view->rows);
		}

		// initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'newtype'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function newtype()
	{
		$this->edittype();
	}

	/**
	 * Short description for 'edittype'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function edittype()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'type') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Incoming (expecting an array)
		$id = JRequest::getVar( 'id', array(0) );
		if (is_array($id)) {
			$id = $id[0];
		} else {
			$id = 0;
		}

		// Load the object
		$view->row = new JobType( $this->database );
		$view->row->load( $id );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'savetype'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function savetype()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initiate extended database class
		$row = new JobType( $this->database );
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

	/**
	 * Short description for 'deletetype'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function deletetype()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming (expecting an array)
		$ids = JRequest::getVar( 'id', array() );

		// Ensure we have an ID to work with
		if (empty($ids)) {
			$this->_message = JText::_('No type selected');
			$this->_redirect = 'index.php?option='.$this->_option.'&task=types';
			return;
		}

		$jt = new JobType( $this->database );

		foreach ($ids as $id)
		{
			// Delete the type
			$jt->delete( $id );
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=types';
		$this->_message = JText::_('Type(s) successfully removed');
	}
}

