<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

class MembersController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	
	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		//Set the controller name
		if (empty( $this->_name ))
		{
			if (isset($config['name']))  {
				$this->_name = $config['name'];
			}
			else
			{
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
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
		$task = strtolower(JRequest::getVar('task', ''));
		
		$id = JRequest::getInt( 'id', 0 );
		if ($id && !$task) {
			$task = 'view';
		}

		$this->_task = $task;
		return $task;
	}
	
	//-----------
	
	private function getView()
	{
		$view = strtolower(JRequest::getVar('view', 'members'));
		$this->_view = $view;
		return $view;
	}
	
	//-----------
	
	public function execute()
	{
		// Load the component config
		$component =& JComponentHelper::getComponent( $this->_option );
		if (!trim($component->params)) {
			return $this->abort();
		} else {
			$config =& JComponentHelper::getParams( $this->_option );
		}
		$this->config = $config;

		$this->getView();

		switch ( $this->getTask() ) 
		{
			case 'upload':     $this->upload();     break;
			case 'deleteimg':  $this->deleteimg();  break;
			case 'img':        $this->img();        break;
			case 'cancel':     $this->cancel();     break;
			case 'save':       $this->save();       break;
			case 'edit':       $this->edit();       break;
			case 'view':       $this->view();       break;
			case 'browse':     $this->browse();     break;
			case 'saveaccess': $this->saveaccess(); break;
			case 'changepassword': $this->changepassword(); break;
			case 'raiselimit': $this->raiselimit(); break;

			case 'whois':      $this->whois();      break;
			case 'activity':   $this->activity();   break;

			default: $this->browse(); break;
		}
	}

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message );
		}
	}
	
	//-----------
	
	private function getStyles($option='') 
	{
		$option = ($option) ? 'com_'.$option : $this->_option;
		ximport('xdocument');
		XDocument::addComponentStylesheet($option);
	}

	//-----------
	
	private function getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file('components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
		}
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function abort() 
	{
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)) );

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		
		// Instantiate the view
		jimport( 'joomla.application.component.view');

		$view = new JView( array('name'=>'abort') );
		$view->option = $this->_option;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------

	protected function browse()
	{
		// Include some needed styles and scripts
		$this->getStyles();

		// Incoming
		$filters = array();
		$filters['limit']  = JRequest::getVar( 'limit', 25, 'request' );
		$filters['start']  = JRequest::getInt( 'limitstart', 0, 'get' );
		$filters['show']   = JRequest::getVar( 'show', $this->_view );
		$filters['sortby'] = JRequest::getVar( 'sortby', 'fullname ASC' );
		$filters['search'] = JRequest::getVar( 'search', '' );
		$filters['index']  = JRequest::getVar( 'index', '' );

		if ($filters['limit'] == 0) {
			$filters['limit'] = 100;
		}

		// Build the page title
		if ($filters['show'] == 'contributors') {
			$title = JText::_('CONTRIBUTORS');
		} else {
			$title = JText::_('MEMBERS');
		}
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		if ($filters['index']) {
			$pathway->addItem( strtoupper($filters['index']), 'index.php?option='.$this->_option.'&index='.$filters['index'] );
		}

		// Check authorization
		$authorized = $this->_authorize();

		if ($authorized === 'admin') {
			$admin = true;
		} else {
			$admin = false;
		}
		$filters['authorized'] = $authorized;
		
		$database =& JFactory::getDBO();
	
		// Initiate a contributor object
		$c = new MembersProfile( $database );
	
		// Get record count
		$total = $c->getCount( $filters, $admin );

		// Get records
		$rows = $c->getRecords( $filters, $admin );

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );
	
		// Instantiate the view
		jimport( 'joomla.application.component.view');

		$view = new JView( array('name'=>'browse') );
		$view->option = $this->_option;
		$view->title = $title;
		$view->rows = $rows;
		$view->filters = $filters;
		$view->total = $total;
		$view->authorized = $authorized;
		$view->pageNav = $pageNav;
		$view->view = $this->_view;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function view() 
	{
		// Build the page title
		if ($this->_task == 'saveaccess') {
			$this->_task = 'view';
		}
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		
		// Include some needed styles and scripts
		$this->getStyles();
		$this->getScripts();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$tab = JRequest::getVar( 'active', 'profile' );  // The active tab (section)

		// Ensure we have an ID
		if (!$id) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'task='.$this->_task );
			
			JError::raiseError( 404, JText::_('MEMBERS_NO_ID') );
			return;
		}

		// Check administrative access
		$authorized = $this->_authorize( $id );

		// Get the member's info
		$profile = new XProfile();
		$profile->load( $id );
		
		// Check subscription to Employer Services
		if($this->config->get('employeraccess') && $tab == 'resume') {
			JPluginHelper::importPlugin( 'members', 'resume' );
			$dispatcher =& JDispatcher::getInstance();
			$checkemp 	= $dispatcher->trigger( 'isEmployer', array() );
			$emp 		= is_array($checkemp) ? $checkemp[0] : 0;		
			$authorized = $emp ? 1 : $authorized;
		}		

		// Ensure we have a member
		if (!$profile->get('name') && !$profile->get('surname')) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'task='.$this->_task );
			
			JError::raiseError( 404, JText::_('MEMBERS_NOT_FOUND') );
			return;
		}

		// Check if the profile is public/private and the user has access
		if ($profile->get('public') != 1 && !$authorized) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'task='.$this->_task );
			
			JError::raiseError( 403, JText::_('MEMBERS_NOT_PUBLIC') );
			return;
		}
		
		if (!$profile->get('name')) {
			$name  = $profile->get('givenName').' ';
			$name .= ($profile->get('middleName')) ? $profile->get('middleName').' ' : '';
			$name .= $profile->get('surname');
			$profile->set('name', $name);
		}
		
		// Get plugins
		JPluginHelper::importPlugin( 'members' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Get the active tab (section)
		$tab = JRequest::getVar( 'active', 'profile' );
		
		// Trigger the functions that return the areas we'll be using
		$cats = $dispatcher->trigger( 'onMembersAreas', array($authorized) );
		
		$available = array();
		foreach ($cats as $cat) 
		{
			$name = key($cat);
			if ($name != '') {
				$available[] = $name;
			}
		}
		if ($tab != 'profile' && !in_array($tab, $available)) {
			$tab = 'profile';
		}
		
		// Get the sections
		$sections = $dispatcher->trigger( 'onMembers', array($profile, $this->_option, $authorized, array($tab)) );
		
		$rparams =& new JParameter( $profile->get('params') );
		$params = $this->config;
		$params->merge( $rparams );
		
		// Add the default "Profile" section to the beginning of the lists
		$body = '';
		if ($tab == 'profile') {
			// Perform some basic maintenance on the data
			if ($profile->get('url')) {
				if ((substr($profile->get('url'), 0, 6) != 'https:') && (substr($profile->get('url'), 0, 5) != 'http:')) {
					$profile->set('url', 'http://'.$profile->get('url'));
				}
			}

			// Load the component config
			$config = $this->config;

			// Get the member's picture (if it exist)
			if ($profile->get('picture')) {
				$dir = FileUploadUtils::niceidformat( $id );
				if (!file_exists(JPATH_ROOT.$config->get('webpath').DS.$dir.DS.$profile->get('picture'))) {
					$profile->set('picture', $config->get('defaultpic'));
				} else {
					$profile->set('picture', $config->get('webpath').DS.$dir.DS.stripslashes($profile->get('picture')));
				}
			} else {
				if (!file_exists(JPATH_ROOT.$config->get('defaultpic'))) {
					$profile->set('picture', '');
				} else {
					$profile->set('picture', $config->get('defaultpic'));
				}	
			}
			
			// Load some needed libraries
			ximport('xregistration');
			
			// Find out which fields are hidden, optional, or required
			$registration = new JObject();
			$registration->Fullname = $this->registrationField('registrationFullname','RRRR','edit');
			$registration->Email = $this->registrationField('registrationEmail','RRRR','edit');
			$registration->URL = $this->registrationField('registrationURL','HHHH','edit');
			$registration->Phone = $this->registrationField('registrationPhone','HHHH','edit');
			$registration->Employment = $this->registrationField('registrationEmployment','HHHH','edit');
			$registration->Organization = $this->registrationField('registrationOrganization','HHHH','edit');
			$registration->Citizenship = $this->registrationField('registrationCitizenship','HHHH','edit');
			$registration->Residency = $this->registrationField('registrationResidency','HHHH','edit');
			$registration->Sex = $this->registrationField('registrationSex','HHHH','edit');
			$registration->Disability = $this->registrationField('registrationDisability','HHHH','edit');
			$registration->Hispanic = $this->registrationField('registrationHispanic','HHHH','edit');
			$registration->Race = $this->registrationField('registrationRace','HHHH','edit');
			$registration->Interests = $this->registrationField('registrationInterests','HHHH','edit');
			$registration->Reason = $this->registrationField('registrationReason','HHHH','edit');
			$registration->OptIn = $this->registrationField('registrationOptIn','HHHH','edit');

			$body = MembersHtml::profile( $sections, $this->_option, $title, $profile, $authorized, $params, $registration );
		}
		
		$cat = array();
		$cat['profile'] = JText::_('MEMBERS_PROFILE');
		array_unshift($cats, $cat);
		array_unshift($sections, array('html'=>$body,'metadata'=>''));
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title.': '.stripslashes($profile->get('name')) );
		
		// Set the pathway
		$pathway->addItem( stripslashes($profile->get('name')), 'index.php?option='.$this->_option.a.'id='.$profile->get('uidNumber') );

		// Output HTML
		echo MembersHtml::view( $profile, $authorized, $this->_option, $cats, $sections, $tab );
	}

	//-----------

	protected function changepassword() 
	{
		jimport( 'joomla.application.component.view');
		
		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.'&id='.$id.'&task='.$this->_task );
			
			$view = new JView( array('name'=>'login') );
			$view->title = $title;
			$view->display();
			return;
		}
		
		if (!$id) {
			$id = $juser->get('id');
		}
		
		// Ensure we have an ID
		if (!$id) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.'&id='.$id.'&task='.$this->_task );

			JError::raiseError( 404, JText::_('MEMBERS_NO_ID') );
			return;
		}
		
		// Check authorization
		$authorized = $this->_authorize( $id );
		if (!$authorized) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.'&id='.$id.'&task='.$this->_task );
			
			JError::raiseError( 403, JText::_('MEMBERS_NOT_AUTH') );
			return;
		}
		
		// Initiate profile class
		$profile = new XProfile();
		$profile->load( $id );
		
		// Ensure we have a member
		if (!$profile->get('name')) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.'&id='.$id.'&task='.$this->_task );
			
			JError::raiseError( 404, JText::_('MEMBERS_NOT_FOUND') );
			return;
		}
		
		// Include some needed styles and scripts
		$this->getStyles();
		
		// Add to the pathway
		$pathway->addItem( stripslashes($profile->get('name')), 'index.php?option='.$this->_option.'&id='.$profile->get('uidNumber') );
		$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.'&id='.$profile->get('uidNumber').'&task='.$this->_task );
		
		// Load some needed libraries
		ximport('xregistrationhelper');
		ximport('xuserhelper');
		
		if (XUserHelper::isXDomainUser($juser->get('id'))) {
			JError::raiseError( 403, JText::_('MEMBERS_PASS_CHANGE_LINKED_ACCOUNT') );
			return;
		}
		
		// Incoming data
		$change   = JRequest::getVar('change', '', 'post');
		$oldpass  = JRequest::getVar('oldpass', '', 'post');
		$newpass  = JRequest::getVar('newpass', '', 'post');
		$newpass2 = JRequest::getVar('newpass2', '', 'post');
		
		$view = new JView( array('name'=>'changepassword') );
		$view->option = $this->_option;
		$view->title = $title;
		$view->profile = $profile;
		$view->change = $change;
		$view->oldpass = $oldpass;
		$view->newpass = $newpass;
		$view->newpass2 = $newpass2;
		
		// Blank form request (no data submitted)
		if (empty($change))  {
			$view->display();
			return;
		}

		if ($profile->get('userPassword') != XUserHelper::encrypt_password($oldpass)) {
			$this->setError( JText::_('MEMBERS_PASS_INCORRECT') );
		} elseif (!$newpass || !$newpass2) {
			$this->setError( JText::_('MEMBERS_PASS_MUST_BE_ENTERED_TWICE') );
		} elseif ($newpass != $newpass2) {
			$this->setError( JText::_('MEMBERS_PASS_NEW_CONFIRMATION_MISMATCH') );
		} elseif (!XRegistrationHelper::validpassword($newpass)) {
			$this->setError( JText::_('MEMBERS_PASS_INVALID') );
		}

		if ($this->getError()) {
			$view->setError( $this->getError() );
			$view->display();
			return;
		}

		// Encrypt the password and update the profile
		$userPassword = XUserHelper::encrypt_password($newpass);
		$profile->set('userPassword', $userPassword);

		// Save the changes
		if (!$profile->update()) {
			$view->setError( JText::_('MEMBERS_PASS_CHANGE_FAILED') );
			$view->display();
			return;
		}

		// Redirect user back to main account page
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&id='.$id);
	}
	
	//-----------

	protected function raiselimit() 
	{
		jimport( 'joomla.application.component.view');
		
		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';

		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.'&id='.$id.'&task='.$this->_task );
			
			$view = new JView( array('name'=>'login') );
			$view->title = $title;
			$view->display();
			return;
		}
		
		// Ensure we have an ID
		if (!$id) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.'&id='.$id.'&task='.$this->_task );
			
			JError::raiseError( 404, JText::_('MEMBERS_NO_ID') );
			return;
		}
		
		$view = new JView( array('name'=>'raiselimit') );
		$view->option = $this->_option;
		$view->title = $title;
		
		// Check authorization
		$view->authorized = $this->_authorize( $id );
		if (!$view->authorized) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.'&id='.$id.'&task='.$this->_task );
			
			JError::raiseError( 403, JText::_('MEMBERS_NOT_AUTH') );
			return;
		}
		
		// Include some needed styles and scripts
		$this->getStyles();
		
		// Initiate profile class
		$profile = new XProfile();
		$profile->load( $id );

		// Ensure we have a member
		if (!$profile->get('name')) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.'&id='.$id.'&task='.$this->_task );
			
			JError::raiseError( 404, JText::_('MEMBERS_NOT_FOUND') );
			return;
		}
		
		$view->profile = $profile;
		
		// Add to the pathway
		$pathway->addItem( stripslashes($profile->get('name')), 'index.php?option='.$this->_option.'&id='.$profile->get('uidNumber') );
		$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.'&id='.$profile->get('uidNumber').'&task='.$this->_task );

		// Incoming
		$request = JRequest::getVar('request',null,'post');
		$raiselimit = JRequest::getVar('raiselimit', null, 'post');

		if ($raiselimit) {
			$k = '';
			if (is_array($raiselimit)) {
				$k = key($raiselimit);
			}

			switch ($k) 
			{
				case 'sessions':
					$oldlimit = intval( $profile->get('jobsAllowed') );
					$newlimit = $oldlimit + 3; 
					
					$resourcemessage = 'session limit from '. $oldlimit .' to '. $newlimit .' sessions ';

					if ($view->authorized == 'admin') {
						$profile->set('jobsAllowed', $newlimit);
						$profile->update();
						
						$resourcemessage = 'The session limit for [' . $profile->get('username') . '] has been raised from ' . $oldlimit . ' to ' . $newlimit . ' sessions.';
					} else if ($request === null) {
						$view->resource = $k;
						$view->setLayout('select');
						$view->display();
						return;
					}
				break;

				case 'storage':
					$oldlimit = 'unknown'; // $profile->get('quota');
					$newlimit = 'unknown'; // $profile->get('quota') + 100;

					$resourcemessage = ' storage limit has been raised from '. $oldlimit .' to '. $newlimit .'.';

					if ($view->authorized == 'admin') {
						// $profile->set('quota', $newlimit);
						// $profile->update();
						
						$resourcemessage = 'The storage limit for [' . $profile->get('username') . '] has been raised from '. $oldlimit .' to '. $newlimit .'.';
					} else {
						$view->resource = $k;
						$view->setLayout('select');
						$view->display();
						return;
					}
				break;

				case 'meetings':
					$oldlimit = 'unknown'; // $profile->get('max_meetings');
					$newlimit = 'unknown'; // $profile->get('max_meetings') + 3;

					$resourcemessage = ' meeting limit has been raised from '. $oldlimit .' to '. $newlimit .'.';

					if ($view->authorized == 'admin') {
						// $profile->set('max_meetings', $newlimit);
						// $profile->update();
						
						$resourcemessage = 'The meeting limit for [' . $profile->get('username') . '] has been raised from '. $oldlimit .' to '. $newlimit .'.';
					} else {
						$view->resource = $k;
						$view->setLayout('select');
						$view->display();
						return;
					}
				break;

				default:
					// Show limit selection form
					$view->display();
					return;
				break;
			}
		}
		
		// Do we need to email admin?
		if ($request !== null && !empty($resourcemessage)) {
			$juri =& JURI::getInstance();
			$xhub =& XFactory::getHub();
			$hubName = $xhub->getCfg('hubShortName');
			$hubUrl = $xhub->getCfg('hubLongURL');
			
			// Email subject
			$subject = $hubName . " Account Resource Request";
			
			// Email message
			$message = 'Name: ' . $profile->get('name');
			if ($profile->get('organization')) {
				$message .= " / " . $profile->get('organization');
			}
			$message .= "\r\n";
			$message .= "Email: " . $profile->get('email') . "\r\n";
			$message .= "Username: " . $profile->get('username') . "\r\n\r\n";
			$message .= 'Has requested an increases in their ' . $hubName;
			$message .= $resourcemessage . "\r\n\r\n";
			$message .= "Reason: ";
			if (empty($request)) {
				$message .= "NONE GIVEN\r\n\r\n";
			} else {
				$message .= $request . "\r\n\r\n";
			}
			$message .= "Click the following link to grant this request:\r\n";
			
			$sef = JRoute::_('index.php?option='.$this->_option.'&id='.$profile->get('uidNumber').'&task='.$this->_task);
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef,1,strlen($sef));
			}
			$url = $juri->base().$sef;
			
			$message .= $url . "\r\n\r\n";
			$message .= "Click the following link to review this user's account:\r\n";
			
			$sef = JRoute::_('index.php?option='.$this->_option.'&id='.$profile->get('uidNumber'));
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef,1,strlen($sef));
			}
			$url = $juri->base().$sef;
			
			$message .= $url . "\r\n";
			
			// Get the administrator's email address
			$emailadmin = $xhub->getCfg('hubSupportEmail');
			
			// Send an e-mail to admin
			if (!XHubHelper::send_email($emailadmin, $subject, $message)) { 
				return JError::raiseError(500, 'xHUB Internal Error: Error mailing resource request to site administrator(s).');
			}
			
			// Output the view
			$view->resourcemessage = $resourcemessage;
			$view->setLayout('success');
			$view->display();
			return;
		} else if ($view->authorized == 'admin' && !empty($resourcemessage)) {
			// Output the view
			$view->resourcemessage = $resourcemessage;
			$view->setLayout('success');
			$view->display();
			return;
		}
		
		// Output the view
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------

	protected function edit($xregistration=null, $profile=null)
	{
		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'id='.$id.a.'task='.$this->_task );
			
			jimport('joomla.application.component.view');
			
			$view = new JView( array('name'=>'login') );
			$view->title = $title;
			$view->display();
			return;
		}
		
		// Ensure we have an ID
		if (!$id) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'id='.$id.a.'task='.$this->_task );
			
			JError::raiseError( 404, JText::_('MEMBERS_NO_ID') );
			return;
		}
		
		// Check authorization
		$authorized = $this->_authorize( $id );
		if (!$authorized) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'id='.$id.a.'task='.$this->_task );
			
			JError::raiseError( 403, JText::_('MEMBERS_NOT_AUTH') );
			return;
		}
		
		// Include some needed styles and scripts
		$this->getStyles();
		
		// Initiate profile class if we don't already have one and load info
		// Note: if we already have one then we just came from $this->save()
		if (!is_object($profile)) {
			$profile = new XProfile();
			$profile->load( $id );
		}

		// Ensure we have a member
		if (!$profile->get('name') && !$profile->get('surname')) {
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'id='.$id.a.'task='.$this->_task );
			
			JError::raiseError( 404, JText::_('MEMBERS_NOT_FOUND') );
			return;
		}
		
		// Get the user's interests (tags)
		$database =& JFactory::getDBO();
		$mt = new MembersTags( $database );
		$tags = $mt->get_tag_string( $id );

		// Add to the pathway
		$pathway->addItem( stripslashes($profile->get('name')), 'index.php?option='.$this->_option.a.'id='.$profile->get('uidNumber') );
		$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'id='.$profile->get('uidNumber').a.'task='.$this->_task );

		// Load some needed libraries
		ximport('xhubhelper');
		ximport('xregistration');
		ximport('xregistrationhelper');
		
		// Instantiate an xregistration object if we don't already have one
		// Note: if we already have one then we just came from $this->save()
		if (!is_object($xregistration)) {
			$xregistration = new XRegistration();
		}

		// Find out which fields are hidden, optional, or required
		$registration = new JObject();
		$registration->Username = $this->registrationField('registrationUsername','RROO',$this->_task);
		$registration->Password = $this->registrationField('registrationPassword','RRHH',$this->_task);
		$registration->ConfirmPassword = $this->registrationField('registrationConfirmPassword','RRHH',$this->_task);
		$registration->Fullname = $this->registrationField('registrationFullname','RRRR',$this->_task);
		$registration->Email = $this->registrationField('registrationEmail','RRRR',$this->_task);
		$registration->ConfirmEmail = $this->registrationField('registrationConfirmEmail','RRRR',$this->_task);
		$registration->URL = $this->registrationField('registrationURL','HHHH',$this->_task);
		$registration->Phone = $this->registrationField('registrationPhone','HHHH',$this->_task);
		$registration->Employment = $this->registrationField('registrationEmployment','HHHH',$this->_task);
		$registration->Organization = $this->registrationField('registrationOrganization','HHHH',$this->_task);
		$registration->Citizenship = $this->registrationField('registrationCitizenship','HHHH',$this->_task);
		$registration->Residency = $this->registrationField('registrationResidency','HHHH',$this->_task);
		$registration->Sex = $this->registrationField('registrationSex','HHHH',$this->_task);
		$registration->Disability = $this->registrationField('registrationDisability','HHHH',$this->_task);
		$registration->Hispanic = $this->registrationField('registrationHispanic','HHHH',$this->_task);
		$registration->Race = $this->registrationField('registrationRace','HHHH',$this->_task);
		$registration->Interests = $this->registrationField('registrationInterests','HHHH',$this->_task);
		$registration->Reason = $this->registrationField('registrationReason','HHHH',$this->_task);
		$registration->OptIn = $this->registrationField('registrationOptIn','HHHH',$this->_task);
		$registration->TOU = $this->registrationField('registrationTOU','HHHH',$this->_task);

		// Ouput HTML
		echo MembersHtml::edit( $authorized, $title, $profile, $this->_option, $tags, $registration, $xregistration );
	}

	//-----------

	private function registrationField($name, $default, $task = 'create')
	{
		if (($task == 'register') || ($task == 'create')) {
			$index = 0;
		} else if ($task == 'proxy') {
			$index = 1;
		} else if ($task == 'update') {
			$index = 2;
		} else if ($task == 'edit') {
			$index = 3;
		} else {
			$index = 0;
		}

		$hconfig =& JComponentHelper::getParams('com_hub');
		
		$default    = str_pad($default, '-', 4);
		$configured  = $hconfig->get($name);
		if (empty($configured))
		    	$configured = $default;
		$length     = strlen($configured);
		if ($length > $index) {
			$value = substr($configured, $index, 1);
		} else {
			$value = substr($default, $index, 1);
		}
		
		switch ($value)
		{
			case 'R': return(REG_REQUIRED);
			case 'O': return(REG_OPTIONAL);
			case 'H': return(REG_HIDE);
			case '-': return(REG_HIDE);
			case 'U': return(REG_READONLY);
			default : return(REG_HIDE);
		}
	}

	//----------------------------------------------------------
	//  Processors
	//----------------------------------------------------------
	
	protected function save() 
	{
		ximport('xhubhelper');
		ximport('xregistration');
		ximport('xregistrationhelper');

		// Incoming user ID
		$id = JRequest::getInt( 'id', 0, 'post' );
		
		// Do we have an ID?
		if (!$id) {
			echo MembersHtml::error( JText::_('MEMBERS_NO_ID') );
			return;
		}

		// Incoming profile edits
		$p = JRequest::getVar( 'profile', array(), 'post' );
		$n = JRequest::getVar( 'name', array(), 'post' );
		
		// Load the profile
		$profile = new XProfile();
		$profile->load( $id );
		
		$oldemail = $profile->get('email');
		
		$profile->set('givenName', trim($n['first']));
		$profile->set('middleName', trim($n['middle']));
		$profile->set('surname', trim($n['last']));
		$name  = trim($n['first']).' ';
		$name .= (trim($n['middle']) != '') ? trim($n['middle']).' ' : '';
		$name .= trim($n['last']);
		$profile->set('name', $name);
		
		$profile->set('bio', trim($p['bio']));

		if (isset($p['vip'])) {
			$profile->set('vip',$p['vip']);
		} else {
			$profile->set('vip',0);
		}

		if (isset($p['public'])) {
			$profile->set('public',$p['public']);
		} else {
			$profile->set('public',0);
		}
		
		// Get the user's interests (tags)
		$tags = trim(JRequest::getVar( 'tags', '' ));
		
		// Set some post data for the xregistration class
		JRequest::setVar('interests',$tags,'post');
		JRequest::setVar('usageAgreement',1,'post');
		
		// Instantiate a new XRegistration
		$xregistration = new XRegistration();
		$xregistration->loadPOST();
		
		// Push the posted data to the profile
		// Note: this is done before the required fields check so, if we need to display the edit form, it'll show all the new changes
		$profile->set('email',$xregistration->_registration['email']);
		
		// Unconfirm if the email address changed
		if ($oldemail != $xregistration->_registration['email']) {
			// Get a new confirmation code
			$confirm = XRegistrationHelper::genemailconfirm();

			$profile->set('emailConfirmed',$confirm);
		}
		
		if (!is_null($xregistration->_registration['countryresident']))
			$profile->set('countryresident',$xregistration->_registration['countryresident']);

		if (!is_null($xregistration->_registration['countryorigin']))
			$profile->set('countryorigin',$xregistration->_registration['countryorigin']);

		if (!is_null($xregistration->_registration['nativetribe']))
			$profile->set('nativeTribe',$xregistration->_registration['nativetribe']);

		if (!is_null($xregistration->_registration['org']) && trim($xregistration->_registration['org']) != '') {
			$profile->set('organization', $xregistration->_registration['org']);
		} elseif (!is_null($xregistration->_registration['orgtext']) && trim($xregistration->_registration['orgtext']) != '') {
			$profile->set('organization', $xregistration->_registration['orgtext']);
		}

		if (!is_null($xregistration->_registration['web']))
			$profile->set('url',$xregistration->_registration['web']);

		if (!is_null($xregistration->_registration['phone']))
			$profile->set('phone',$xregistration->_registration['phone']);

		if (!is_null($xregistration->_registration['orgtype']))
			$profile->set('orgtype',$xregistration->_registration['orgtype']);

		if (!is_null($xregistration->_registration['sex']))
			$profile->set('gender',$xregistration->_registration['sex']);
		
		if (!is_null($xregistration->_registration['disability']))
			$profile->set('disability',$xregistration->_registration['disability']);
		
		if (!is_null($xregistration->_registration['hispanic']))
			$profile->set('hispanic',$xregistration->_registration['hispanic']);

		if (!is_null($xregistration->_registration['race']))
			$profile->set('race',$xregistration->_registration['race']);
	
		if (!is_null($xregistration->_registration['mailPreferenceOption']))
			$profile->set('mailPreferenceOption',$xregistration->_registration['mailPreferenceOption']);

		// Check that required fields were filled in properly
		if (!$xregistration->check('edit', $profile->get('uidNumber'))) {
			$this->_task = 'edit';
			$this->edit( $xregistration, $profile );
			return;
		}
		
		// Set the last modified datetime
		$profile->set('modifiedDate', date( 'Y-m-d H:i:s', time() ));

		// Save the changes
		if (!$profile->update()) {
			JError::raiseWarning('', $profile->getError() );
			return false;
		}
		
		// Process tags
		$database =& JFactory::getDBO();
		$mt = new MembersTags( $database );
		$mt->tag_object($id, $id, $tags, 1, 1);
		
		$email = $profile->get('email');
		
		// Make sure certain changes make it back to the Joomla user table
		if ($id > 0) {
			$juser =& JUser::getInstance($id);
			$jname = $juser->get('name');
			$jemail = $juser->get('email');
			if ($name != trim($jname)) {
				$juser->set('name', $name);
			}
			if ($email != trim($jemail)) {
				$juser->set('email', $email);
			}
			if ($name != trim($jname) || $email != trim($jemail)) {
				if (!$juser->save()) {
					JError::raiseWarning('', JText::_( $juser->getError() ));
					return false;
				}
			}
		}
		
		// Send a new confirmation code AFTER we've successfully saved the changes to the e-mail address
		if ($email != $oldemail) {
			$this->_message = $this->send_confirmation_code($profile->get('username'), $email, $confirm);
		}
		
		// Redirect
		$url  = 'index.php?option='.$this->_option;
		$url .= ($id) ? '&id='.$id : '';

		$this->_redirect = JRoute::_( $url );
	}
	
	//-----------
	
	private function send_confirmation_code($login, $email, $confirm) 
	{
		$xhub =& XFactory::getHub();
		
		// Get a new confirmation code
		//$confirm = XRegistrationHelper::genemailconfirm();

		// Email subject
		$subject  = $xhub->getCfg('hubShortName') .' account email confirmation';
		
		// Email message
		$message  = "This email is to confirm the email address for the ".$xhub->getCfg('hubShortName')." account: $login.\r\n\r\n";
		$message .= "Click the following link to confirm your email address and activate your account.\r\n\r\n";
		$message .= $xhub->getCfg('hubLongURL') .DS. 'register/confirm/' . -$confirm . "\r\n";

		// Send the email
		if (XHubHelper::send_email($email, $subject, $message)) {
			$msg = 'A confirmation email has been sent to "'. htmlentities($email,ENT_COMPAT,'UTF-8') .'". You must click the link in that email to re-activate your account.';
		} else {
			$msg = 'An error occurred emailing "'. htmlentities($email,ENT_COMPAT,'UTF-8') .'" your confirmation.';
		}
		
		return $msg;
	}
	
	//-----------
	
	protected function saveaccess() 
	{
		// Incoming user ID
		$id = JRequest::getInt( 'id', 0 );
		
		// Do we have an ID?
		if (!$id) {
			echo MembersHtml::error( JText::_('MEMBERS_NO_ID') );
			return;
		}
		
		// Incoming profile edits
		$p = JRequest::getVar( 'access', array(), 'post' );
		if (is_array( $p )) {
			// Load the profile
			$profile = new XProfile();
			$profile->load( $id );
			
			foreach ($p as $k=>$v) 
			{
				$profile->setParam('access_'.$k, $v);
			}
			
			// Save the changes
			if (!$profile->update()) {
				JError::raiseWarning('', $profile->getError() );
				return false;
			}
		}
		
		// Push through to the profile view
		$this->view();
	}
	
	//-----------
	
	protected function activity()
	{
		jimport( 'joomla.application.component.view');
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)) );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);
		
		// Push some styles to the template
		$this->getStyles();
		$this->getStyles('usage');
		
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			/*echo MembersHtml::div( MembersHtml::hed( 2, JText::_('Active Users and Guests') ), 'full', 'content-header' );
			echo '<div class="main section">'.n;
			echo MembersHtml::warning( JText::_('MEMBERS_NOT_LOGGEDIN') );
			echo XModuleHelper::renderModules('force_mod');
			echo '</div><!-- / .main section -->'.n;*/
			$view = new JView( array('name'=>'login') );
			$view->title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
			$view->display();
			return;
		}
		if (!$juser->authorize($this->_option, 'manage')) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
		}
		
		$database =& JFactory::getDBO();
		
		// Get logged-in users
		$prevuser = '';
		$user  = array();
		$users = array();

		$sql = "SELECT s.username, x.host, x.ip, (UNIX_TIMESTAMP(NOW()) - s.time) AS idle 
				FROM #__session AS s, #__xsession AS x
				WHERE s.username <> '' AND s.session_id=x.session_id
				ORDER BY username, host, ip, idle DESC";

		$database->setQuery( $sql );
		$result = $database->loadObjectList();

		if ($result && count($result) > 0) {
			foreach ($result as $row)
			{
				if ($prevuser != $row->username) {
					if ($user) {
						//$xlog->logDebug("com_whois::activity($prevuser)");
						$xuser =& XUser::getInstance($prevuser);
						$users[$prevuser] = $user;
						$users[$prevuser]['name'] = $xuser->get('name');
						$users[$prevuser]['org'] = $xuser->get('org');
						$users[$prevuser]['orgtype'] = $xuser->get('orgtype');
						//$users[$prevuser]['countryresident'] = getcountry($xuser->get('countryresident'));
						$users[$prevuser]['countryresident'] = $xuser->get('countryresident');
					}
					$prevuser = $row->username;
					$user = array();
				}
				array_push($user, array('host' => $row->host, 'ip' => $row->ip, 'idle' => $row->idle));
			}
			if ($user) {
				//$xlog->logDebug("com_whois::activity($prevuser)");
				$xuser =& XUser::getInstance($prevuser);
				$users[$prevuser] = $user;
				$users[$prevuser]['name'] = $xuser->get('name');
				$users[$prevuser]['org'] = $xuser->get('org');
				$users[$prevuser]['orgtype'] = $xuser->get('orgtype');
				//$users[$prevuser]['countryresident'] = getcountry($xuser->get('countryresident'));
				$users[$prevuser]['countryresident'] = $xuser->get('countryresident');
			}
		}

		/*$ds = array();
		
		$ipdbo =& WhoisUtils::getIPDBO();
		if ($ipdbo) {
			$ipdbo->setQuery( "SELECT domain FROM domainfilter" );
			$domains = $ipdbo->loadObjectList();
			if ($domains) {
				foreach ($domains as $domain)
				{
					$ds[] = $domain->domain;
				}
			}
		}
		
		$ds = implode("','",$ds);*/
		
		$guests = array();
		/*
		$sql = "SELECT x.host, x.ip, (UNIX_TIMESTAMP(NOW()) - s.time) AS idle 
				FROM #__session AS s, #__xsession AS x 
				WHERE s.username = '' AND s.session_id=x.session_id AND x.domain NOT IN ('$ds')
				ORDER BY host DESC, ip, idle DESC";
		*/
		$sql = "SELECT x.host, x.ip, (UNIX_TIMESTAMP(NOW()) - s.time) AS idle 
				FROM #__session AS s, #__xsession AS x 
				WHERE s.username = '' AND s.session_id=x.session_id
				ORDER BY host DESC, ip, idle DESC";

		$database->setQuery( $sql );
		$result = $database->loadObjectList();
		if ($result) {
			if (count($result) > 0) {
				foreach($result as $row) 
				{
					array_push($guests, array('host' => $row->host, 'ip' => $row->ip, 'idle' => $row->idle));
				}
			}
		}
		
		// Output View
		$view = new JView( array('name'=>'activity') );
		$view->title = JText::_('Active Users and Guests');
		$view->option = $this->_option;
		$view->users = $users;
		$view->guests = $guests;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------

	protected function whois()
	{
		jimport( 'joomla.application.component.view');
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)) );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&task='.$this->_task);
		
		// Push some styles to the template
		$this->getStyles();
		
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$view = new JView( array('name'=>'login') );
			$view->title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
			$view->display();
			return;
		}
		if (!$juser->authorize($this->_option, 'manage')) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
		}
		
		// Incoming
		$query = JRequest::getVar( 'query', '' );
		$uid   = JRequest::getVar( 'username', '' );
		$mail  = JRequest::getVar( 'email', '' );
		
		if ($uid) {
			$search = 'uid=' . $uid;
		} elseif ($mail) {
			$search = 'mail=' . $mail;
		} else {
			$search = $query;
		}

		$view = new JView( array('name'=>'whois') );
		$view->summaries = null;
		$view->user = null;
		$view->query = ($search) ? $search : '';
		
		// Do we have a query?
		if ($search) {
			// Parse the querystring
			$result = $this->_parse_simplesearch($search);

			// Perform the query
			$logins = $this->_get_usernamesbyfilter($result); 
			$summaries = $this->_get_summarybyfilter($result);
			
			// Did we get any results?
			if ((count($logins) <= 0) || ($logins == false))  {
				$this->setError( JText::_('No results found matching the provided query.') );
			} elseif (count($logins) > 1) {
				$view->summaries = $summaries;
			} else {
				$view->user = $logins[0]->username;
			}
		}

		// Output View
		$view->title = JText::_('Lookup User(s)');
		$view->option = $this->_option;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	private function _get_summarybyfilter($args) 
	{
		return( $this->_get_attrsbyfilter('username uidNumber email name',$args) );
	}
	
	//-----------

	private function _get_usernamesbyfilter($args) 
	{
		return( $this->_get_attrsbyfilter('username',$args) );
	}

	//-----------

	private function _get_attrsbyfilter($attrs, $filters) 
	{
		$result = false;
		$filter = '';
		
		$select = '*';
		if ($attrs) {
			$attr_req = explode(' ', $attrs);
			$attr_req = array_map('trim', $attr_req);
			$select = '`'.implode('`,`',$attr_req).'`';
		}
		
		if ($filters) {
			$filter = implode(' OR ', $filters);
		}

		$database =& JFactory::getDBO();
		$mp = new MembersProfile( $database );
		$result = $mp->selectWhere( $select, $filter );

		return $result;
	}

	//-----------

	private function _parse_email_address($adrstring) 
	{
		$address = '';
		// < > delimited email addresses override any others
		if (ereg("< *(.+)\@(.+) *>", $adrstring, $match) === false)
			ereg("([^ <\"]+)\@([^ >\"]+)", $adrstring, $match);
		$mailbox = $match[1];
		$host    = $match[2];
		// remove email portion to get name portion
		$name = str_replace($match[0], "", $adrstring);
		// strip any exterior parens from name
		if ( ereg("^ *\((.*)\) *$", $name, $match) )
			$name = $match[1];
		// strip any exterior quotes from name
		if ( ereg("^ *\"(.*)\" *$", $name, $match) )
			$name = $match[1];
		$personal=trim($name);

		if ($mailbox && $host)
			$addr = $mailbox .'@'. $host;
		else
			$addr = '';

		return( array($addr, $personal) );
	}

	//-----------

	private function _parse_simplesearch($searchstr) 
	{
		$address = array();
		$subs = preg_split("/\s*,\s*/", $searchstr);

		for ($i=0; $i < count($subs); $i++) 
		{
			if (strlen($subs[$i]) <= 0) {
				;
			}
			else if (preg_match("/^proxyUidNumber\s*(\!?\-?\+?=)\s*([^\s]+)/i", $subs[$i], $match)) {
				//$thisresult = 'proxyUidNumber';
				if ($match[1] == "=") {
					//$thisresult .= '=';
					$result[] = $this->_like('proxyUidNumber', $match[2]);
				}
				elseif ($match[1] == "-=") {
					//$thisresult .= '<=';
					$result[] = "proxyUidNumber <= '".$match[2]."'";
				}
				elseif ($match[1] == "+=") {
					//$thisresult .= '>=';
					$result[] = "proxyUidNumber >= '".$match[2]."'";
				}
				/*if ($match[1] == "!=") {
					$thisresult .= '=';
				}*/
				//$thisresult .= $match[2];
				/*if ($match[1] == "!=") {
					$result[] = "!(" . $thisresult . ")";
				}
				else {*/
					//$result[] = $thisresult;
				//}
			}
			else if (preg_match("/^proxyConfirmed\s*(\!?=)\s*([^\s]+)/i", $subs[$i], $match)) {
				$thisresult = null;
				if (strtolower($match[2]) == "true" || $match[2] == 1 || $match[2] == -1) {
					$thisresult = true;
				}
				elseif (strtolower($match[2]) == "false" || $match[2] == 0) {
					$thisresult = false;
				}
				if($thisresult === true || $thisresult === false) {
					if ($match[1] == "!=") {
						$thisresult = !$thisresult;
					}
					if ($thisresult) {
						$result[] = "&(!(proxyPassword=*))(proxyUidNumber=*)";
					}
					else {
						$result[] = "&(proxyPassword=*)(proxyUidNumber=*)";
					}
				}
			}
			else if (preg_match("/^emailConfirmed\s*(\!?\-?\+?=)\s*([^\s]+)/i", $subs[$i], $match)) {
				$thisresult = 'emailConfirmed';
				if ($match[1] == "=") {
					$thisresult .= '=';
				}
				elseif ($match[1] == "-=") {
					$thisresult .= '<=';
				}
				elseif ($match[1] == "+=") {
					$thisresult .= '>=';
				}
				elseif ($match[1] == "!=") {
					$thisresult .= '=';
				}
				if (strtolower($match[2]) == "true") {
					$thisresult .= "1";
					if ($match[1] != "=" && $match[1] != "!=") {
						$match[1] = "=";
						$thisresult = "";
					}
				}
				elseif (strtolower($match[2]) == "false") {
					$thisresult .= "1";
					if ($match[1] == "=") {
						$match[1] = "!=";
					}
					elseif ($match[1] == "!=") {
						$match[1] = "=";
					}
					else {
						$match[1] = "=";
						$thisresult = "";
					}
				}
				else {
					$thisresult .= "'".$match[2]."'";
				}
				/*if ($match[1] == "!=") {
					$result[] = "!(" . $thisresult . ")";
				}
				else {*/
					$result[] = $thisresult;
				//}
			}
			else if (preg_match("/^uidNumber\s*(\!?\-?\+?=)\s*([^\s]+)/i", $subs[$i], $match)) {
				//$thisresult = 'uidNumber';
				if ($match[1] == "=") {
					//$thisresult .= '=';
					$result[] = $this->_like('uidNumber', $match[2]);
				}
				elseif ($match[1] == "-=") {
					//$thisresult .= '<=';
					$result[] = "uidNumber <= '".$match[2]."'";
				}
				elseif ($match[1] == "+=") {
					//$thisresult .= '>=';
					$result[] = "uidNumber >= '".$match[2]."'";
				}
				/*if ($match[1] == "!=") {
					$thisresult .= '=';
				}*/
				//$thisresult .=  '\''.$match[2] .'\'';
				/*if ($match[1] == "!=") {
					$result[] = "!(" . $thisresult . ")";
				}
				else {*/
					//$result[] = $thisresult;
				//}
			}
			else if (preg_match("/^uid\s*=\s*([^\s]+)/i", $subs[$i], $match)) {
				$result[] = $this->_like('username', $match[1]);
			}
			else if (preg_match("/^username\s*=\s*([^\s]+)/i", $subs[$i], $match)) {
				$result[] = $this->_like('username', $match[1]);
			}
			else if (preg_match("/^login\s*=\s*([^\s]+)/i", $subs[$i], $match)) {
				$result[] = $this->_like('username', $match[1]);
			}
			else if (preg_match("/^(em|m)ail\s*=\s*([^\s]+)/i", $subs[$i], $match)) {
				$result[] = 'email=\''. $match[2] .'\'';
			}
			else if (preg_match("/^name\s*=\s*([^\s]+)/i", $subs[$i], $match)) {
				$result[] = $this->_like('name', $match[1]);
			}
			else if (preg_match("/^cn\s*=\s*([^\s]+)/i", $subs[$i], $match)) {
				$result[] = $this->_like('name', $match[1]);
			}
			else if (preg_match("/=/", $subs[$i], $match)) {
				;
			}
			else if ( preg_match("/^[0-9]+$/", $subs[$i]) ) {
				$result[] = 'uidNumber=\''. $subs[$i] .'\'';
			}
			else if ( preg_match("/^[^\s@]+$/", $subs[$i]) ) {
				$result[] = $this->_like('username', $subs[$i]);
			}
			else {
				$address[] = $subs[$i];
			}
		}

		for ($i = 0; $i < count($address); $i++) 
		{
			$addr = $this->_parse_email_address($address[$i]);
			if ($addr[0])
				$result[] = "email='". $addr[0] ."'";
				if ($addr[1])
					$result[] = $this->_like('name', $addr[1]);
		}
		return( $result );
	}
	
	//-----------
	
	private function _like($k, $v) 
	{
		if ((substr($v, -1) == '*' || substr($v, -1) == '?') 
		 && (substr($v, 0, 1) == '*' || substr($v, 0, 1) == '?')) {
			return $k." LIKE '%". substr($v, 1, -1) ."%'";
		} elseif (substr($v, -1) == '*' || substr($v, -1) == '?') {
			return $k." LIKE '". substr($v, 0, -1) ."%'";
		} elseif (substr($v, 0, 1) == '*' || substr($v, 0, 1) == '?') {
			return $k." LIKE '%". substr($v, 1) ."'";
		} else {
			return $k."='". $v ."'";
		}
	}

	//-----------

	protected function cancel() 
	{
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Redirect
		$this->_redirect = JRoute::_( 'index.php?option='.$this->_option.'&id='.$id );
	}
	
	//----------------------------------------------------------
	//  Image handling
	//----------------------------------------------------------

	protected function upload()
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Load the component config
		$config = $this->config;
		
		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('MEMBERS_NO_ID') );
			$this->img( '', $id );
			return;
		}
		
		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name']) {
			$this->setError( JText::_('MEMBERS_NO_FILE') );
			$this->img( '', $id );
			return;
		}
		
		// Build upload path
		$dir  = FileUploadUtils::niceidformat( $id );
		$path = JPATH_ROOT;
		if (substr($config->get('webpath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $config->get('webpath').DS.$dir;
		
		if (!is_dir( $path )) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) {
				$this->setError( JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
				$this->img( '', $id );
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);
		
		// Do we have an old file we're replacing?
		$curfile = JRequest::getVar( 'currentfile', '' );
		
		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path.DS.$file['name'])) {
			$this->setError( JText::_('ERROR_UPLOADING') );
			$file = $curfile;
		} else {
			$ih = new MembersImgHandler();
			
			if ($curfile != '') {
				// Yes - remove it
				if (file_exists($path.DS.$curfile)) {
					if (!JFile::delete($path.DS.$curfile)) {
						$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
						$this->img( $file['name'], $id );
						return;
					}
				}
				$curthumb = $ih->createThumbName($curfile);
				if (file_exists($path.DS.$curthumb)) {
					if (!JFile::delete($path.DS.$curthumb)) {
						$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
						$this->img( $file['name'], $id );
						return;
					}
				}
			}

			// Instantiate a profile, change some info and save
			$profile = new XProfile();
			$profile->load( $id );
			$profile->set('picture', $file['name']);
			if (!$profile->update()) {
				$this->setError( $profile->getError() );
			}
			
			// Resize the image if necessary
			$ih->set('image',$file['name']);
			$ih->set('path',$path.DS);
			$ih->set('maxWidth', 186);
			$ih->set('maxHeight', 186);
			if (!$ih->process()) {
				$this->setError( $ih->getError() );
			}
			
			// Create a thumbnail image
			$ih->set('maxWidth', 50);
			$ih->set('maxHeight', 50);
			$ih->set('cropratio', '1:1');
			$ih->set('outputName', $ih->createThumbName());
			if (!$ih->process()) {
				$this->setError( $ih->getError() );
			}
			
			$file = $file['name'];
		}
		
		// Push through to the image view
		$this->img( $file, $id );
	}

	//-----------

	protected function deleteimg()
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Load the component config
		$config = $this->config;
		
		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('MEMBERS_NO_ID') );
			$this->img( '', $id );
		}
		
		// Incoming file
		$file = JRequest::getVar( 'file', '' );
		if (!$file) {
			$this->setError( JText::_('MEMBERS_NO_FILE') );
			$this->img( '', $id );
		}
		
		// Build the file path
		$dir  = FileUploadUtils::niceidformat( $id );
		$path = JPATH_ROOT;
		if (substr($config->get('webpath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $config->get('webpath').DS.$dir;

		if (!file_exists($path.DS.$file) or !$file) { 
			$this->setError( JText::_('FILE_NOT_FOUND') ); 
		} else {
			$ih = new MembersImgHandler();
			
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path.DS.$file)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
				$this->img( $file, $id );
				return;
			}
			
			$curthumb = $ih->createThumbName($file);
			if (file_exists($path.DS.$curthumb)) {
				if (!JFile::delete($path.DS.$curthumb)) {
					$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
					$this->img( $file, $id );
					return;
				}
			}
			
			// Instantiate a profile, change some info and save
			$profile = new XProfile();
			$profile->load( $id );
			$profile->set('picture', '');
			if (!$profile->update()) {
				$this->setError( $profile->getError() );
			}

			$file = '';
		}
	
		// Push through to the image view
		$this->img( $file, $id );
	}

	//-----------

	protected function img( $file='', $id=0 )
	{
		// Load the component config
		$config = $this->config;
		
		// Get the app
		$app =& JFactory::getApplication();
		
		// Incoming
		if (!$id) {
			$id = JRequest::getInt( 'id', 0, 'get' );
		}

		$file = ($file) 
			  ? $file 
			  : JRequest::getVar( 'file', '', 'get' );
		
		// Build the file path
		$dir = FileUploadUtils::niceidformat( $id );
		$path = JPATH_ROOT.DS.$config->get('webpath').DS.$dir;
		
		// Output HTML
		MembersHtml::writeImage( $app, $this->_option, $config->get('webpath'), $config->get('defaultpic'), $dir, $file, $path, $id, $this->getErrors() );
	}

	//----------------------------------------------------------
	//	Private functions
	//----------------------------------------------------------

	private function _authorize($uid=0)
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			return 'admin';
		}

		// Check if they're the member
		if (is_numeric($uid)) {
			if ($juser->get('id') == $uid) {
				return true;
			}
		} else {
			if ($juser->get('username') == $uid) {
				return true;
			}
		}

		return false;
	}
}
?>