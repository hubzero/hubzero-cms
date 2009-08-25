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

class GroupsController extends JObject
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
		$gid  = JRequest::getVar( 'gid', '' );
		$active = JRequest::getVar( 'active', '' );
		if ($gid && !$task) {
			$task = 'view';
		}
		if ($active && $task) {
			$this->action = ($task == 'view') ? '' : $task;
			$task = 'view';
		}
		$this->_task = $task;
		$this->gid = $gid;

		return $task;
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
		
		switch ( $this->getTask() ) 
		{
			// File manager for uploading images/files to be used in group descriptions
			case 'media':        $this->media();        break;
			case 'listfiles':    $this->listfiles();    break;
			case 'upload':       $this->upload();       break;
			case 'deletefolder': $this->deletefolder(); break;
			case 'deletefile':   $this->deletefile();   break;
			
			// Autocompleter - called via AJAX
			case 'autocomplete': $this->autocomplete(); break;
			
			// Group management
			case 'new':     $this->edit();    break;
			case 'edit':    $this->edit();    break;
			case 'save':    $this->save();    break;
			case 'delete':  $this->delete();  break;
			case 'invite':  $this->invite();  break;
			case 'accept':  $this->accept();  break;
			
			// Admin option
			case 'approve': $this->approve(); break;
			
			// User options
			case 'join':    $this->join();    break;
			case 'cancel':  $this->cancel();  break;
			case 'confirm':	$this->confirm(); break;
			
			// General views
			case 'view':    $this->view();    break;
			case 'browse':  $this->browse();  break;
			case 'login':   $this->login();   break;

			default: $this->intro(); break;
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
	
	private function getStyles() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}

	//-----------
	
	private function getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
		}
	}

	//----------------------------------------------------------
	// Main displays
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
		
		// Output HTML
		echo GroupsHtml::div( GroupsHtml::hed( 2, JText::_(strtoupper($this->_name)) ), 'full', 'content-header');
		echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NOT_CONFIGURED') ), 'main section');
	}
	
	//-----------
	
	protected function login($title='') 
	{
		$title = ($title) ? $title : JText::_(strtoupper($this->_name));
		
		$html  = GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
		$h  = GroupsHtml::warning( JText::_('GROUPS_NOT_LOGGEDIN') );
		$h .= XModuleHelper::renderModules('force_mod');
		$html .= GroupsHtml::div( $h, 'main section');
		
		echo $html;
	}
	
	//-----------

	protected function intro()
	{
		// Build the page title
		$title = JText::_(strtoupper($this->_name));
		
		// Write title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}

		// Push some styles to the template
		$this->getStyles();
		
		$database =& JFactory::getDBO();
		
		$sql = "SELECT g.gidNumber, g.cn, g.description, g.public_desc, (SELECT COUNT(*) FROM #__xgroups_members AS gm WHERE gm.gidNumber=g.gidNumber) AS members
				FROM #__xgroups AS g 
				WHERE g.type=1
				AND g.published=1
				AND g.privacy!=4
				ORDER BY members DESC LIMIT 3";
		$database->setQuery( $sql );
		$popular = $database->loadObjectList();

		jimport( 'joomla.application.component.view');

		// Output HTML
		$view = new JView( array('name'=>'intro') );
		$view->title = $title;
		$view->groups = $popular;
		$view->config = $this->config;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------

	protected function browse()
	{
		// Check if they're logged in	
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			$authorized = true;
		} else {
			$authorized = false;
		}

		// Push some styles to the template
		$this->getStyles();
		
		// Incoming
		$filters = array();
		$filters['type']   = 'hub';
		$filters['authorized'] = $authorized;
		
		// Filters for getting a result count
		$filters['limit']  = 'all';
		$filters['fields'] = array('COUNT(*)');
		$filters['search'] = JRequest::getVar('search', '');
		$filters['sortby'] = JRequest::getVar('sortby', 'description ASC');
		$filters['index']  = JRequest::getVar('index', '');
		
		// Get a record count
		$total = XGroupHelper::get_groups($filters['type'], false, $filters);

		// Filters for returning results
		$filters['limit']  = JRequest::getInt('limit', 25);
		$filters['limit']  = ($filters['limit']) ? $filters['limit'] : 'all';
		$filters['start']  = JRequest::getInt('limitstart', 0);
		$filters['fields'] = array('description','published','gidNumber','type','public_desc','join_policy');

		// Get a list of all groups
		$groups = XGroupHelper::get_groups($filters['type'], false, $filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );
		// Get a list of all groups
		//$groups = XGroupHelper::get_groups('hub', false, array('description','published'));

		// Run through the master list of groups and mark the user's status in that group
		if ($authorized) {
			$groups = $this->getGroups( $groups );
		}
		
		// Build the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);

		// Are there any errors?
		$errors = '';
		if ($this->getError()) {
			$errors = implode(n.'<br />',$this->getErrors());
		}

		// Output HTML
		echo GroupsHtml::browse( $this->_option, $groups, $authorized, $title, $pageNav, $filters, $errors );
	}

	//-----------

	protected function view() 
	{
		// Ensure we have a group to work with
		if ($this->gid == '') {
			echo GroupsHtml::div( GroupsHtml::hed( 2, JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)) ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_ID') ), 'main section');
			return;
		}

		// Load the group page
		$group = new XGroup();
		$group->select( $this->gid );
		$this->gid = $group->get('cn');
		
		// Ensure we found the group info
		if (!$group->get('gidNumber') && !$group->get('cn')) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)) ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_FOUND') ), 'main section');
			return;
		}

		// Ensure it's an allowable group type to display
		if ($group->get('type') != 1) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_FOUND') );
			return;
		}
		
		// Push some needed styles to the template
		$this->getStyles();
		
		// Push some needed scripts to the template
		$this->getScripts();
		
		// Check authorization
		$authorized = $this->_authorize();
		$ismember = $this->_authorize(true);

		// Get the active tab (section)
		$tab = JRequest::getVar( 'active', 'overview' );
		
		// Get the wiki parser and parse the group texts
		$public_desc = $group->get('public_desc');
		$private_desc = $group->get('private_desc');
		if (!$public_desc) {
			$public_desc = $group->get('description');
		}
		if ($public_desc || $private_desc) {
			ximport('wiki.parser');
			$config = $this->config;
			$p = new WikiParser( $group->get('cn'), $this->_option, $group->get('cn').DS.'wiki', 'group', $group->get('gidNumber'), $config->get('uploadpath'), $group->get('cn') );
		}
		if ($public_desc) {
			$public_desc = $p->parse( n.stripslashes($public_desc) );
		}
		if ($private_desc) {
			$private_desc = $p->parse( n.stripslashes($private_desc) );
		}
		
		// Set the page title
		$title = ($group->get('description')) ? $group->get('description') : $group->get('cn');
		$document =& JFactory::getDocument();
		$document->setTitle( ucfirst($this->_name).': '.$title );
		
		// Build the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem($title,'index.php?option='.$this->_option.a.'gid='.$group->get('cn'));
		
		// Incoming
		$limit = JRequest::getInt('limit', 25);
		$start = JRequest::getInt('limitstart', 0);
		
		// Get plugins
		JPluginHelper::importPlugin( 'groups' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Trigger the functions that return the areas we'll be using
		$cats = $dispatcher->trigger( 'onGroupAreas', array(
				$authorized)
			);
		
		// Add the active tab to the pathway
		$available = array();
		foreach ($cats as $cat)
		{
			$name = key($cat);
			if ($name != '') {
				$available[] = $name;
				if (strtolower($name) == $tab) {
					$pathway->addItem($cat[$name],'index.php?option='.$this->_option.a.'gid='.$group->get('cn').a.'active='.$name);
				}
			}
		}
		if ($tab != 'overview' && !in_array($tab, $available)) {
			$tab = 'overview';
		}
		
		// Limit the records if we're on the overview page
		if ($tab == 'overview') {
			$limit = 5;
		}
		$limit = ($limit == 0) ? 'all' : $limit;
		
		// Get the sections
		$sections = $dispatcher->trigger( 'onGroup', array(
				$group,
				$this->_option,
				$authorized,
				$limit,
				$start,
				$this->action,
				array($tab))
			);

		$juser =& JFactory::getUser();
		
		// Show a login prompt if the group is restricted or private and the user isn't logged in
		if ($juser->get('guest') && $group->get('privacy') > 0) {
			return $this->login( $title );
		} else {
			// Logged in user - does the user have authority to view this group?
			if ($group->get('privacy') > 1 && !$authorized) {
				echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
				echo GroupsHtml::div( GroupsHtml::error( JText::_('You must be a member of this group to view its content.') ), 'main section');
				return;
			}
		}

		// Add the default "About" section to the beginning of the lists
		if ($tab == 'overview') {
			$body = GroupsHtml::overview( $sections, $cats, $this->_option, $group, $juser->get('id'), $authorized, $ismember, $public_desc, $private_desc, $this->getError() );
		} else {
			$body = '';
		}

		// Push the overview view to the array of sections we're going to output
		$cat = array();
		$cat['overview'] = JText::_('GROUPS_OVERVIEW');
		array_unshift($cats, $cat);
		array_unshift($sections, array('html'=>$body,'metadata'=>''));

		// Output HTML
		echo GroupsHtml::view( $group, $authorized, $this->_option, $cats, $sections, $tab );
	}

	//----------------------------------------------------------
	// User actions
	//----------------------------------------------------------

	protected function join() 
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
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}

		// Ensure we have a group to work with
		if (!$this->gid) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_ID') ), 'main section');
			return;
		}

		// Load the group page
		$group = new XGroup();
		$group->select( $this->gid );

		// Ensure we found the group info
		if (!$group) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_FOUND') ), 'main section');
			return;
		}
		
		$gtitle = ($group->get('description')) ? stripslashes($group->get('description')) : stripslashes($group->get('cn'));
		
		// Reset the title to include the group name
		$title  = JText::_(strtoupper($this->_name));
		$title .= ': '.$gtitle;
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		$document->setTitle( $title );
		
		$pathway->addItem($gtitle,'index.php?option='.$this->_option.a.'gid='.$group->get('cn'));
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'gid='.$group->get('cn').a.'task='.$this->_task);
		
		// Check if they're logged in	
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login( $title );
			return;
		}
		
		// Check if the user is already a member, applicant, invitee, or manager
		if ($group->is_member_of('applicants',$juser->get('id')) || 
			$group->is_member_of('members',$juser->get('id')) || 
			$group->is_member_of('managers',$juser->get('id')) || 
			$group->is_member_of('invitees',$juser->get('id'))) {
			// Already a member - show the group page
			$this->view();
			return;
		}
		
		switch ($group->get('join_policy')) 
		{
			case 3:
				// Closed membership - show the group page
				$this->view();
			break;
			case 2:
				// Invite only - show the group page
				$this->view();
			break;
			case 1:
				// Restricted membership - show a form for the user to explain why they should be a member
				echo GroupsHtml::join( $this->_option, $title, $group );
			break;
			case 0:
			default:
				// Open membership - Go ahead and make them a member
				$this->confirm();
			break;
		}
	}
	
	//-----------
	
	protected function cancel() 
	{
		$return = strtolower(trim(JRequest::getVar( 'return', '', 'get' )));
		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Check if they're logged in	
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login( $title );
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_ID') ), 'main section');
			return;
		}
		
		// Load the group
		$group = new XGroup();
		$group->select( $this->gid );

		// Ensure we found the group info
		if (!$group) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_FOUND') ), 'main section');
			return;
		}
		
		// Remove the user from the group
		$group->remove('managers',$juser->get('id'));
		$group->remove('members',$juser->get('id'));
		$group->remove('applicants',$juser->get('id'));
		$group->remove('invitees',$juser->get('id'));
		$group->update();
		if ($group->getError()) {
			$this->setError( JText::_('GROUPS_ERROR_CANCEL_MEMBERSHIP_FAILED') );
		}
		
		$database =& JFactory::getDBO();
		
		// Log the membership cancellation
		$log = new XGroupLog( $database );
		$log->gid = $group->get('gidNumber');
		$log->uid = $juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		$log->action = 'membership_cancelled';
		$log->actorid = $juser->get('id');
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}
		
		// Remove record of reason wanting to join group
		$reason = new GroupsReason( $database );
		$reason->deleteReason( $juser->get('username'), $group->get('cn') );

		$xhub =& XFactory::getHub();
		$jconfig =& JFactory::getConfig();
		
		// Email subject
		$subject = JText::sprintf('GROUPS_SUBJECT_MEMBERSHIP_CANCELLED', $group->get('cn'));

		// Build the e-mail message
		$message  = JText::sprintf('GROUPS_EMAIL_MSG',$jconfig->getValue('config.sitename')).r.n.r.n;
		$message .= t.' '.JText::_('GROUP').': '. $group->get('description') .' ('.$group->get('cn').')'.r.n;
		$message .= t.' '.JText::_('GROUPS_MEMBERSHIP_CANCELLED').':'.r.n;
		$message .= t.t.$juser->get('name');
		//$message .= ($xuser->get('org')) ? ' / '. $xuser->get('org') : '';
		$message .= r.n;
		$message .= t.t. $juser->get('username') .' ('. $juser->get('email') . ')'.r.n.r.n;
		$message .= JText::_('GROUPS_USE_LINK_TO_REVIEW_MEMBERSHIPS').r.n;
		
		$juri =& JURI::getInstance();
		
		$sef = JRoute::_('index.php?option='.$this->_option.a.'gid='. $group->get('cn').a.'active=members');
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		
		$message .= $juri->base().$sef.r.n;

		// Get the managers' e-mails
		$emailmanagers = $group->getEmails('managers');

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// E-mail the administrator
		/*if (!$this->email($emailadmin, $subject, $message, $from)) {
			$success = false;
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_ADMIN_FAILED') );
		}
		
		// Do we have manager e-mails?
		if (count($emailmanagers) > 0) {
			// We do, so e-mail them
			foreach ($emailmanagers as $email) 
			{
				if (!$this->email($email, $subject, $message, $from)) {
					$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') );
				}
			}
		}
		
		ximport('xmessage');
		if (!XMessageHelper::sendMessage( $type, $subject, $message, $from, $group->get('managers') )) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED').' '.$emailadmin );
		}*/
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( $type, $subject, $message, $from, $group->get('managers'), $this->_option ))) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED').' '.$emailadmin );
		}

		// Action Complete. Redirect to appropriate page
		if ($return == 'browse') {
			$xhub->redirect(JRoute::_('index.php?option='.$this->_option));
		} else {
			$xhub->redirect(JRoute::_('index.php?option='.$this->_option.a.'gid='. $group->get('cn')));
		}
	}
	
	//-----------
	
	protected function confirm() 
	{
		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Check if they're logged in	
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login( $title );
			return;
		}
		// Ensure we have a group to work with
		if (!$this->gid) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_ID') ), 'main section');
			return;
		}
		
		// Load the group
		$group = new XGroup();
		$group->select( $this->gid );

		// Ensure we found the group info
		if (!$group) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_FOUND') ), 'main section');
			return;
		}

		// Get the managers' e-mails
		$emailmanagers = $group->getEmails('managers');
		
		// Auto-approve member for group without any managers
		if (count($emailmanagers) < 1) {
			$group->add('managers',array($juser->get('id')));
		} else {
			if ($group->get('join_policy') == 0) {
				$group->add('members',array($juser->get('id')));
			} else {
				$group->add('applicants',array($juser->get('id')));
			}
		}
		$group->update();
		if ($group->getError()) {
			$this->setError( JText::_('GROUPS_ERROR_REGISTER_MEMBERSHIP_FAILED') );
		}

		$database =& JFactory::getDBO();
		
		if ($group->get('join_policy') == 1) {
			// Instantiate the reason object and bind the incoming data
			$row = new GroupsReason( $database );
			$row->uidNumber = $juser->get('id');
			$row->gidNumber = $group->get('gidNumber');
			$row->reason    = JRequest::getVar( 'reason', JText::_('GROUPS_NO_REASON_GIVEN'), 'post' );
			$row->reason    = $this->cleanText($row->reason);
			$row->date      = date( 'Y-m-d H:i:s', time());

			// Check and store the reason
			if (!$row->check()) {
				echo GroupsHtml::alert( $row->getError() );
				exit();
			}
			if (!$row->store()) {
				echo GroupsHtml::alert( $row->getError() );
				exit();
			}
		}
		
		// Log the membership request
		$log = new XGroupLog( $database );
		$log->gid = $group->get('gidNumber');
		$log->uid = $juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		$log->action = 'membership_requested';
		$log->actorid = $juser->get('id');
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}
		// Log the membership approval if the join policy is open
		if ($group->get('join_policy') == 0) {
			$log2 = new XGroupLog( $database );
			$log2->gid = $group->get('gidNumber');
			$log2->uid = $juser->get('id');
			$log2->timestamp = date( 'Y-m-d H:i:s', time() );
			$log2->action = 'membership_approved';
			$log2->actorid = $juser->get('id');
			if (!$log2->store()) {
				$this->setError( $log2->getError() );
			}
		}
		
		$xhub =& XFactory::getHub();
		$jconfig =& JFactory::getConfig();
			
		// E-mail subject
		$subject = JText::sprintf('GROUPS_SUBJECT_MEMBERSHIP', $group->get('cn'));

		// Build the e-mail message
		$message  = JText::sprintf('GROUPS_EMAIL_MSG',$jconfig->getValue('config.sitename')).r.n.r.n;
		$message .= t.' '.JText::_('GROUP').': '. $group->get('description') .' ('.$group->get('cn').')'.r.n;
		$message .= t.' '.JText::_('GROUPS_MEMBERSHIP_REQUEST').': '.r.n;
		$message .= r.n.'---------------------------------------------------------------------------------------'.r.n;
		$message .= t.t.$juser->get('name');
		//$message .= ($xuser->get('org')) ? ' / '. $xuser->get('org') : '';
		$message .= r.n;
		$message .= t.t. $juser->get('username') .' ('. $juser->get('email') . ')';
		if ($group->get('join_policy') == 1) {
			$message .= JText::_('GROUPS_APPROVE_PERSON_BECAUSE').' '.r.n. stripslashes($row->reason);
		}
		$message .= r.n.'---------------------------------------------------------------------------------------'.r.n.r.n;
		$message .= JText::_('GROUPS_USE_LINK_TO_REVIEW_REQUEST').r.n;
		
		$juri =& JURI::getInstance();
		
		$sef = JRoute::_('index.php?option='.$this->_option.a.'gid='. $group->get('cn').a.'active=members');
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		
		$message .= $juri->base().$sef.r.n;

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');
		
		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// E-mail the administrator
		/*if (!$this->email($emailadmin, $subject, $message, $from)) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_ADMIN_FAILED') );
		}
		
		// Do we have manager e-mails?
		if ($emailmanagers) {
			// We do, so e-mail them
			foreach ($emailmanagers as $email) 
			{
				if (!$this->email($email, $subject, $message, $from)) {
					$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') );
				}
			}
		}
		ximport('xmessage');
		if (!XMessageHelper::sendMessage( 'groups_requests_membership', $subject, $message, $from, $group->get('managers') )) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED').' '.$emailadmin );
		}*/
		if ($group->get('join_policy') == 1) {
			$url = 'index.php?option='.$this->_option.a.'gid='.$group->get('cn').a.'active=members';
			JPluginHelper::importPlugin( 'xmessage' );
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger( 'onSendMessage', array( 'groups_requests_membership', $subject, $message, $from, $group->get('managers'), $this->_option, $group->get('gidNumber'), $url ))) {
				$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED').' '.$emailadmin );
			}
		}

		// Push through to the groups listing
		$this->view();
	}
	
	//-----------
	
	protected function accept() 
	{
		$return = strtolower(trim(JRequest::getVar( 'return', '', 'get' )));

		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Check if they're logged in	
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login( $title );
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_ID') ), 'main section');
			return;
		}
		
		// Load the group
		$group = new XGroup();
		$group->select( $this->gid );

		// Ensure we found the group info
		if (!$group) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_FOUND') ), 'main section');
			return;
		}

		// Move the member from the invitee list to the members list
		$group->add('members',array($juser->get('id')));
		$group->remove('invitees',array($juser->get('id')));
		$group->update();
		if ($group->getError()) {
			$this->setError( JText::_('GROUPS_ERROR_REGISTER_MEMBERSHIP_FAILED') );
		}
		
		// Log the invite acceptance
		$database =& JFactory::getDBO();
		$log = new XGroupLog( $database );
		$log->gid = $group->get('gidNumber');
		$log->uid = $juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		$log->action = 'membership_invite_accepted';
		$log->actorid = $juser->get('id');
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}

		$xhub =& XFactory::getHub();
		$jconfig =& JFactory::getConfig();
		
		// E-mail subject
		$subject = JText::sprintf('GROUPS_SUBJECT_MEMBERSHIP', $group->get('cn'));

		// Build the e-mail message
		$message  = JText::sprintf('GROUPS_EMAIL_MSG',$jconfig->getValue('config.sitename')).r.n.r.n;
		$message .= t.' '.JText::_('GROUP').': '. $group->get('description') .' ('.$group->get('cn').')'.r.n;
		$message .= t.' '.JText::_('GROUPS_MEMBERSHIP_ACCEPTED').': '.r.n;
		$message .= t.t.$juser->get('name');
		//$message .= ($xuser->get('org')) ? ' / '. $xuser->get('org') : '';
		$message .= r.n;
		$message .= t.t. $juser->get('username') .' ('. $juser->get('email') . ')'.r.n.r.n;
		$message .= JText::_('GROUPS_USE_LINK_TO_REVIEW_MEMBERSHIPS').r.n;
		
		$juri =& JURI::getInstance();
		
		$sef = JRoute::_('index.php?option='.$this->_option.a.'gid='. $group->get('cn').a.'active=members');
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		
		$message .= $juri->base().$sef.r.n;
			
		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// E-mail the administrator
		/*if (!$this->email($emailadmin, $subject, $message, $from)) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_ADMIN_FAILED') );
		}
		
		// Get the managers' e-mails
		$emailmanagers = $group->getEmails('managers');
		
		// Do we have manager e-mails?
		if ($emailmanagers) {
			// We do, so e-mail them
			foreach ($emailmanagers as $email) 
			{
				if (!$this->email($email, $subject, $message, $from)) {
					$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') );
				}
			}
		}
		ximport('xmessage');
		if (!XMessageHelper::sendMessage( 'groups_accepts_membership', $subject, $message, $from, $group->get('managers') )) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED').' '.$emailadmin );
		}*/
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( 'groups_accepts_membership', $subject, $message, $from, $group->get('managers'), $this->_option ))) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED').' '.$emailadmin );
		}

		// Action Complete. Redirect to appropriate page
		if ($return == 'browse') {
			$xhub->redirect(JRoute::_('index.php?option='.$this->_option));
		} else {
			$xhub->redirect(JRoute::_('index.php?option='.$this->_option.a.'gid='. $group->get('cn')));
		}
	}
	
	//----------------------------------------------------------
	// Group management
	//----------------------------------------------------------

	protected function edit()
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
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		
		// Push some needed styles to the template
		$this->getStyles();
		
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);
			
			$this->login( $title );
			return;
		}
		
		// Check authorization
		$authorized = $this->_authorize();
		if (!$authorized && $this->_task != 'new') {
			$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);
			
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::warning( JText::_('GROUPS_NOT_AUTH') ), 'main section');
			return;
		}

		// Instantiate an XGroup object
		$group = new XGroup();
		
		if ($this->_task != 'new') {
			// Ensure we have a group to work with
			if (!$this->gid) {
				$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);
				
				echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
				echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_ID') ), 'main section');
				return;
			}

			// Load the group
			$group->select( $this->gid );

			// Ensure we found the group info
			if (!$group) {
				$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);
				
				echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
				echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_FOUND') ), 'main section');
				return;
			}
			
			$gtitle = ($group->get('description')) ? stripslashes($group->get('description')) : stripslashes($group->get('cn'));
			
			// Set the pathway
			$pathway->addItem($gtitle,'index.php?option='.$this->_option.a.'gid='.$group->get('cn'));
		}

		$p  = 'index.php?option='.$this->_option.a.'task='.$this->_task;
		$p .= ($group->get('cn')) ? a.'gid='.$group->get('cn') : '';
		$pathway->addItem(JText::_(strtoupper($this->_task)),$p);

		// Get the group's interests (tags)
		$database =& JFactory::getDBO();
		$gt = new GroupsTags( $database );
		$tags = $gt->get_tag_string( $group->get('gidNumber') );

		// Output HTML
		echo GroupsHtml::edit( $this->_option, $group, $this->_task, $title, $tags, '' );
	}
	
	//-----------

	protected function approve() 
	{
		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		
		$authorized = $this->_authorize();
		
		// Check authorization
		if ($authorized != 'admin') {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::warning( JText::_('GROUPS_NOT_AUTH') ), 'main section');
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_ID') ), 'main section');
			return;
		}
		
		// Load the group
		$group = new XGroup();
		$group->select( $this->gid );

		// Ensure we found the group info
		if (!$group) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_FOUND') ), 'main section');
			return;
		}
		
		// Approve the group
		$group->set('published',1);
		$group->save();
		
		if ($group->getError()) {
			$this->setError( JText::_('GROUPS_ERROR_APPROVING_GROUP') );
			$this->view();
			return;
		}
		
		// Log the group approval
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		$log = new XGroupLog( $database );
		$log->gid = $group->get('gidNumber');
		$log->uid = $juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		$log->action = 'group_approved';
		$log->actorid = $juser->get('id');
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}
		
		$xhub =& XFactory::getHub();
		$jconfig =& JFactory::getConfig();
		
		// Get the managers' e-mails
		$emails = $group->getEmails('managers');
		
		// E-mail subject
		$subject = JText::sprintf('GROUPS_SUBJECT_GROUP_APPROVED', $group->get('cn'));
		
		// Build the e-mail message			
		$message  = JText::sprintf('GROUPS_EMAIL_MSG_APPROVED', $jconfig->getValue('config.sitename')).r.n.r.n;
		$message .= JText::_('GROUPS_ID').': '. $group->get('cn') .r.n;
		$message .= JText::_('GROUPS_TITLE').': '. $group->get('description') .r.n;
		switch ($group->get('access')) 
		{
			case 4: $privacy = JText::_('GROUPS_ACCESS_PRIVATE');   break;
			case 3: $privacy = JText::_('GROUPS_ACCESS_PROTECTED'); break;
			case 0: $privacy = JText::_('GROUPS_ACCESS_PUBLIC');    break;
		}
		$message .= JText::_('GROUPS_PRIVACY').': '. $privacy .r.n;
		$message .= r.n;
		$message .= JText::_('GROUPS_USE_LINK_TO_REVIEW_GROUP').r.n;
		
		$juri =& JURI::getInstance();
		
		$sef = JRoute::_('index.php?option='.$this->_option.a.'gid='. $group->get('cn'));
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		
		$message .= $juri->base().$sef.r.n;
		
		// Get the administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');
		
		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');
			
		// Send e-mail to the administrator
		/*if (!$this->email($emailadmin, $subject, $message, $from)) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_ADMIN_FAILED') );
		}
		
		// Send e-mail to the group managers
		foreach ($emails as $email) 
		{
			if (!$this->email($email, $subject, $message, $from)) {
				$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED').' '.$email );
			}
		}
		ximport('xmessage');
		if (!XMessageHelper::sendMessage( 'groups_approved', $subject, $message, $from, $group->get('managers') )) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED').' '.$emailadmin );
		}*/
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( 'groups_approved', $subject, $message, $from, $group->get('managers'), $this->_option ))) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED').' '.$emailadmin );
		}
		
		// Push through to the group page
		$this->view();
	}
	
	//-----------
	
	protected function save() 
	{	
		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login( $title );
			return;
		}

		// Incoming
		$g_cn           = strtolower(trim(JRequest::getVar( 'cn', '', 'post' )));
		$g_description  = trim(JRequest::getVar( 'description', JText::_('NONE'), 'post' ));
		$g_privacy      = JRequest::getInt('privacy', 0, 'post' );
		$g_access       = JRequest::getInt('access', 0, 'post' );
		$g_gidNumber    = JRequest::getInt('gidNumber', 0, 'post' );
		$g_public_desc  = trim(JRequest::getVar( 'public_desc',  '', 'post', 'none', 2 ));
		$g_private_desc = trim(JRequest::getVar( 'private_desc', '', 'post', 'none', 2 ));
		$g_restrict_msg = trim(JRequest::getVar( 'restrict_msg', '', 'post', 'none', 2 ));
		$g_join_policy  = JRequest::getInt('join_policy', 0, 'post' );
		$tags = trim(JRequest::getVar( 'tags', '' ));
		
		// Instantiate an XGroup object
		$group = new XGroup();
		
		// Is this a new entry or updating?
		$isNew = false;
		if (!$g_gidNumber) {
			$isNew = true;
			
			// Set the task - if anything fails and we re-enter edit mode 
			// we need to know if we were creating new or editing existing
			$this->_task = 'new';
		} else {
			$this->_task = 'edit';
			
			// Load the group
			$group->select( $g_gidNumber );
		}

		// Check for any missing info
		if (!$g_cn) {
			$this->setError( JText::_('GROUPS_ERROR_MISSING_INFORMATION').': '.JText::_('GROUPS_ID') );
		}
		if (!$g_description) {
			$this->setError( JText::_('GROUPS_ERROR_MISSING_INFORMATION').': '.JText::_('GROUPS_TITLE') );
		}
		
		// Push back into edit mode if any errors
		if ($this->getError()) {
			// Set the page title
			$title  = JText::_(strtoupper($this->_name));
			$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';

			$document =& JFactory::getDocument();
			$document->setTitle( $title );

			// Set the pathway
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			if (count($pathway->getPathWay()) <= 0) {
				$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
			}
			$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);

			// Push some needed styles to the template
			$this->getStyles();

			$group->set('description', $g_description );
			$group->set('access', $g_access );
			$group->set('privacy', $g_privacy );
			$group->set('public_desc', $g_public_desc );
			$group->set('private_desc', $g_private_desc );
			$group->set('restrict_msg',$g_restrict_msg);
			$group->set('join_policy',$g_join_policy);
			$group->set('cn',$g_cn);
			
			echo GroupsHtml::edit( $this->_option, $group, $this->_task, $title, $tags, $this->getErrors() );
			return;
		}
		
		// Ensure the data passed is valid
		if ($g_cn == 'new' || $g_cn == 'browse') {
			$this->setError( JText::_('GROUPS_ERROR_INVALID_ID') );
		}
		if (!XGroupHelper::valid_cn($g_cn)) {
			$this->setError( JText::_('GROUPS_ERROR_INVALID_ID') );
		}
		if ($isNew && XGroupHelper::groups_exists($g_cn)) {
			$this->setError( JText::_('GROUPS_ERROR_GROUP_ALREADY_EXIST') );
		}
		
		// Push back into edit mode if any errors
		if ($this->getError()) {
			// Set the page title
			$title  = JText::_(strtoupper($this->_name));
			$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';

			$document =& JFactory::getDocument();
			$document->setTitle( $title );

			// Set the pathway
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			if (count($pathway->getPathWay()) <= 0) {
				$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
			}
			$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);

			// Push some needed styles to the template
			$this->getStyles();
			
			$group->set('description', $g_description );
			$group->set('access', $g_access );
			$group->set('privacy', $g_privacy );
			$group->set('public_desc', $g_public_desc );
			$group->set('private_desc', $g_private_desc );
			$group->set('restrict_msg',$g_restrict_msg);
			$group->set('join_policy',$g_join_policy);
			$group->set('cn',$g_cn);
			
			echo GroupsHtml::edit( $this->_option, $group, $this->_task, $title, $tags, $this->getErrors() );
			return;
		}
		
		// Get some needed objects
		$xhub =& XFactory::getHub();
		$jconfig =& JFactory::getConfig();
		//$xuser =& XFactory::getUser();
				
		// Build the e-mail message
		if ($isNew) {
			$subject = JText::sprintf('GROUPS_SUBJECT_GROUP_REQUESTED', $g_cn);
		} else {
			$subject = JText::sprintf('GROUPS_SUBJECT_GROUP_UPDATED', $g_cn);
		}
								
		// Build the e-mail message
		// Note: this is done *before* pushing the changes to the group so we can show, in the message, what was changed
		$message = $juser->get('name');
		/*if ($xuser->get('org')) {
			$message .= ' / '. $xuser->get('org');
		}*/
		$message .= ','.r.n;
		$message .= $juser->get('username') .'" ('. $juser->get('email') .')'.r.n;
		if ($isNew) {
			$message .= JText::sprintf('GROUPS_USER_HAS_REQUESTED_GROUP', $jconfig->getValue('config.sitename')) .':'.r.n.r.n;
			$message .= JText::_('GROUPS_ID').': '. $g_cn .r.n;
			
			$type = 'groups_created';
		} else {
			$message .= JText::sprintf('GROUPS_USER_HAS_CHANGED_GROUP', $jconfig->getValue('config.sitename')) .':'.r.n.r.n;
			$message .= JText::_('GROUPS_TITLE').': '. $group->get('description') .r.n;
			switch ($group->get('join_policy')) 
			{
				case 3: $policy = JText::_('GROUPS_POLICY_CLOSED');   break;
				case 2: $policy = JText::_('GROUPS_POLICY_INVITE');   break;
				case 1: $policy = JText::_('GROUPS_POLICY_RESTRICTED'); break;
				case 0: $policy = JText::_('GROUPS_POLICY_OPEN');    break;
			}
			$message .= JText::_('GROUPS_JOIN_POLICY').': '. $policy .r.n;
			switch ($group->get('privacy')) 
			{
				case 4: $privacy = JText::_('GROUPS_ACCESS_PRIVATE');   break;
				case 3: $privacy = JText::_('GROUPS_ACCESS_PROTECTED'); break;
				case 0: $privacy = JText::_('GROUPS_ACCESS_PUBLIC');    break;
			}
			$message .= JText::_('GROUPS_PRIVACY').': '. $privacy .r.n;
			switch ($group->get('access')) 
			{
				case 4: $access = JText::_('GROUPS_ACCESS_PRIVATE');   break;
				case 3: $access = JText::_('GROUPS_ACCESS_PROTECTED'); break;
				case 0: $access = JText::_('GROUPS_ACCESS_PUBLIC');    break;
			}
			$message .= JText::_('GROUPS_CONTENT_PRIVACY').': '. $access .r.n;
			$message .= r.n;
			$message .= JText::_('GROUPS_NOW_DEFINED_AS').':'.r.n;
			
			$type = 'groups_changed';
		}
		$message .= JText::_('GROUPS_TITLE').': '. $g_description .r.n;
		switch ($g_join_policy) 
		{
			case 3: $policy = JText::_('GROUPS_POLICY_CLOSED');   break;
			case 2: $policy = JText::_('GROUPS_POLICY_INVITE');   break;
			case 1: $policy = JText::_('GROUPS_POLICY_RESTRICTED'); break;
			case 0: $policy = JText::_('GROUPS_POLICY_OPEN');    break;
		}
		$message .= JText::_('GROUPS_JOIN_POLICY').': '. $policy .r.n;
		switch ($g_privacy) 
		{
			case 4: $privacy = JText::_('GROUPS_ACCESS_PRIVATE');   break;
			case 3: $privacy = JText::_('GROUPS_ACCESS_PROTECTED'); break;
			case 0: $privacy = JText::_('GROUPS_ACCESS_PUBLIC');    break;
		}
		$message .= JText::_('GROUPS_PRIVACY').': '. $privacy .r.n;
		switch ($g_access) 
		{
			case 4: $access = JText::_('GROUPS_ACCESS_PRIVATE');   break;
			case 3: $access = JText::_('GROUPS_ACCESS_PROTECTED'); break;
			case 0: $access = JText::_('GROUPS_ACCESS_PUBLIC');    break;
		}
		$message .= JText::_('GROUPS_CONTENT_PRIVACY').': '. $access .r.n;
		$message .= r.n;
		$message .= JText::_('GROUPS_USE_LINK_TO_REVIEW_GROUP').r.n;
		
		$juri =& JURI::getInstance();
		
		$sef = JRoute::_('index.php?option='.$this->_option.a.'gid='. $g_cn);
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		
		$message .= $juri->base().$sef.r.n;
				
		// Set the group changes and save
		$group->set('cn', $g_cn );
		if ($isNew) {
			$group->set('type', 1 );
			$group->set('published', 1 );
			
			$group->add('managers',array($juser->get('id')));
			$group->add('members',array($juser->get('id')));
		}
		$group->set('description', $g_description );
		$group->set('access', $g_access );
		$group->set('privacy', $g_privacy );
		$group->set('public_desc', $g_public_desc );
		$group->set('private_desc', $g_private_desc );
		$group->set('restrict_msg',$g_restrict_msg);
		$group->set('join_policy',$g_join_policy);
		$group->save();
		
		// Process tags
		$database =& JFactory::getDBO();
		$gt = new GroupsTags( $database );
		$gt->tag_object($juser->get('id'), $group->get('gidNumber'), $tags, 1, 1);
		
		// Log the group save
		$database =& JFactory::getDBO();
		$log = new XGroupLog( $database );
		$log->gid = $group->get('gidNumber');
		$log->uid = $juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		$log->actorid = $juser->get('id');
		
		// Rename the temporary upload directory if it exist
		if ($isNew) {
			$lid = JRequest::getInt( 'lid', 0, 'post' );
			if ($lid != $group->get('gidNumber')) {
				$config = $this->config;
				$bp = JPATH_ROOT;
				if (substr($config->get('uploadpath'), 0, 1) != DS) {
					$bp .= DS;
				}
				$bp .= $config->get('uploadpath');
				if (is_dir($bp.DS.$lid)) {
					rename($bp.DS.$lid, $bp.DS.$group->get('gidNumber'));
				}
			}
			
			$log->action = 'group_created';
		} else {
			$log->action = 'group_edited';
		}
		
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}
		
		// Get the administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');
		
		// Get the "from" info
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');
				
		// E-mail the administrator
		/*if (!$this->email($emailadmin, $subject, $message, $from)) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_ADMIN_FAILED').' '.$emailadmin );
		}
		ximport('xmessage');
		if (!XMessageHelper::sendMessage( $type, $subject, $message, $from, $group->get('managers') )) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') );
		}*/
		// Get plugins
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( $type, $subject, $message, $from, $group->get('managers'), $this->_option ))) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') );
		}
		
		if ($this->getError()) {
			echo GroupsHtml::error( $this->getError() );
			return;
		}
		
		// Redirect back to the group page
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'gid='.$g_cn);
	}

	//-----------
	
	protected function invite() 
	{
		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login( $title );
			return;
		}
		
		// Check authorization
		$authorized = $this->_authorize();
		if (!$authorized) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::warning( JText::_('GROUPS_NOT_AUTH') ), 'main section');
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_ID') ), 'main section');
			return;
		}

		// Load the group page
		$group = new XGroup();
		$group->select( $this->gid );
		
		// Ensure we found the group info
		if (!$group) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_FOUND') ), 'main section');
			return;
		}
		
		// Incoming
		$process = JRequest::getVar( 'process', '' );
		$logins = trim(JRequest::getVar( 'logins', '', 'post' ));
		$msg = trim(JRequest::getVar( 'msg', '', 'post' ));
		$return = trim(JRequest::getVar( 'return', '', 'get' ));
		
		// Did they confirm delete?
		if (!$process || !$logins) {
			if ($process && !$logins) {
				$this->setError( JText::_('GROUPS_ERROR_PROVIDE_LOGINS') );
			}
			
			// Push some needed styles to the template
			$this->getStyles();

			// Push some needed scripts to the template
			$this->getScripts();
			
			// Set the pathway
			$gtitle = ($group->get('description')) ? stripslashes($group->get('description')) : stripslashes($group->get('cn'));
			
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			if (count($pathway->getPathWay()) <= 0) {
				$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
			}
			$pathway->addItem($gtitle,'index.php?option='.$this->_option.a.'gid='.$group->get('cn'));
			$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'gid='.$group->get('cn').a.'task='.$this->_task);
			
			// Output HTML
			echo GroupsHtml::invite($this->_option, $title, $group, $msg, $return, $this->getError());	
			return;
		}
		
		$return = trim(JRequest::getVar( 'return', '', 'post' ));
		$invitees = array();
		$inviteemails = array();
		$apps = array();
		$mems = array();
		$registeredemails = array();

		$database =& JFactory::getDBO();

		// Get all the group's managers
		$members = $group->get('members');
		$applicants = $group->get('applicants');

		// Explod the string of logins/e-mails into an array
		$la = explode(',',$logins);
		foreach ($la as $l) 
		{
			// Trim up the content
			$l = trim($l);
			
			// Check if it's an e-mail address
			if (eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $l)) {
				// Try to find an account that might match this e-mail
				$database->setQuery("SELECT u.id FROM #__users AS u WHERE u.email='". $l ."' OR u.email LIKE '".$l."\'%' LIMIT 1;");
				$uid = $database->loadResult();
				if (!$database->query()) {
					$this->setError( $database->getErrorMsg() );
				}
				
				// If we found an ID, add it to the invitees list
				if ($uid) {
					$invitees[] = $uid;
				}
				$inviteemails[] = $l;
				$registeredemails[] = $l;
			} else {
				// Retrieve user's account info
				$user =& XUser::getInstance($l);
				//$user = JUserHelper::getUserId($user);

				// Ensure we found an account
				if (is_object($user)) {
					$uid = $user->get('uid');
					if (!in_array($uid,$members)) {
						if (in_array($uid,$applicants)) {
							$apps[] = $uid;
							$mems[] = $uid;
						} else {
							$invitees[] = $uid;
							$inviteemails[] = $user->get('email');
							$registeredemails[] = $user->get('email');
						}
					}
				}
			}
		}

		// Add the users to the invitee list and save
		$group->remove('applicants', $apps );
		$group->add('members', $mems );
		$group->add('invitees', $invitees );
		$group->update();
		
		// Log the sending of invites
		foreach ($invitees as $invite) 
		{
			$log = new XGroupLog( $database );
			$log->gid = $group->get('gidNumber');
			$log->uid = $invite;
			$log->timestamp = date( 'Y-m-d H:i:s', time() );
			$log->action = 'membership_invites_sent';
			$log->actorid = $juser->get('id');
			if (!$log->store()) {
				$this->setError( $log->getError() );
			}
		}
		
		// Get and set some vars
		$xhub =& XFactory::getHub();
		$jconfig =& JFactory::getConfig();
		
		// Build the "from" info for e-mails
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');
								
		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option='.$this->_option.a.'gid='. $group->get('cn'));
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		$sef = $juri->base().$sef;

		// Email group members
		
			// E-mail subject
			$subject = JText::sprintf('GROUPS_SUBJECT_INVITE', $group->get('cn'));

			// Build the e-mail message
			$email  = JText::sprintf('GROUPS_USER_HAS_INVITED', $juser->get('name'), $group->get('description'), $jconfig->getValue('config.sitename')).r.n.r.n;

			if ($msg) {
				$email .= '====================='.r.n;
				$email .= stripslashes($msg).r.n;
				$email .= '====================='.r.n.r.n;
			}

			$email .= JText::sprintf('GROUPS_PLEASE_JOIN', $sef).r.n.r.n;

			

			$email .= JText::sprintf('GROUPS_EMAIL_USER_IF_QUESTIONS', $juser->get('name'), $juser->get('email')).r.n;
		
		foreach ($inviteemails as $mbr) 
		{
			if (!in_array($mbr, $registeredemails)) {
				$email .= JText::sprintf('GROUPS_PLEASE_REGISTER', $jconfig->getValue('config.sitename'), $juri->base() . 'register').r.n.r.n;
			}
			
			// Send the e-mail
			if (!$this->email($mbr, $jconfig->getValue('config.sitename').' '.$subject, $email, $from)) {
				$this->setError( JText::_('GROUPS_ERROR_EMAIL_INVITEE_FAILED').' '.$mbr );
			}
		}
		
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( 'groups_invite', $subject, $email, $from, $invitees, $this->_option ))) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_INVITEE_FAILED') );
		}
		
		if ($return == 'members') {
			$xhub->redirect( JRoute::_('index.php?option='.$this->_option.a.'gid='. $group->get('cn').a.'active=members') );
		}

		// Push through to the group page
		$this->view();
	}

	//-----------
	
	protected function delete() 
	{
		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login( $title );
			return;
		}
		
		// Check authorization
		$authorized = $this->_authorize();
		if (!$authorized) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::warning( JText::_('GROUPS_NOT_AUTH') ), 'main section');
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_ID') ), 'main section');
			return;
		}

		// Load the group page
		$group = new XGroup();
		$group->select( $this->gid );
		
		// Ensure we found the group info
		if (!$group) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( JText::_('GROUPS_NO_GROUP_FOUND') ), 'main section');
			return;
		}

		// Push some needed styles to the template
		$this->getStyles();
		
		// Push some needed scripts to the template
		$this->getScripts();
		
		// Get number of group members
		$members = $group->get('members');

		// Get plugins
		JPluginHelper::importPlugin( 'groups' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Incoming
		$process = JRequest::getVar( 'process', '' );
		$confirmdel = JRequest::getVar( 'confirmdel', '' );
		$msg = trim(JRequest::getVar( 'msg', '', 'post' ));
		
		// Did they confirm delete?
		if (!$process || !$confirmdel) {
			if ($process && !$confirmdel) {
				$this->setError( JText::_('GROUPS_ERROR_CONFIRM_DELETION') );
			}
			
			$log = JText::sprintf('GROUPS_MEMBERS_LOG',count($members));

			// Trigger the functions that delete associated content
			// Should return logs of what was deleted
			$logs = $dispatcher->trigger( 'onGroupDeleteCount', array($group) );
			if (count($logs) > 0) {
				$log .= '<br />'.implode('<br />',$logs);
			}
			
			// Set the pathway
			$gtitle = ($group->get('description')) ? stripslashes($group->get('description')) : stripslashes($group->get('cn'));
			
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			if (count($pathway->getPathWay()) <= 0) {
				$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
			}
			$pathway->addItem($gtitle,'index.php?option='.$this->_option.a.'gid='.$group->get('cn'));
			$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'gid='.$group->get('cn').a.'task='.$this->_task);
			
			// Output HTML
			echo GroupsHtml::delete($this->_option, $title, $this->gid, $group, $log, $msg, $this->getError());	
			return;
		}
		
		// Start log
		$log  = JText::sprintf('GROUPS_SUBJECT_GROUP_DELETED', $group->get('cn'));
		$log .= JText::_('GROUPS_TITLE').': '.$group->get('description').n;
		$log .= JText::_('GROUPS_ID').': '.$group->get('cn').n;
		$log .= JText::_('GROUPS_PRIVACY').': '.$group->get('access').n;
		$log .= JText::_('GROUPS_PUBLIC_TEXT').': '.stripslashes($group->get('public_desc')) .n;
		$log .= JText::_('GROUPS_PRIVATE_TEXT').': '.stripslashes($group->get('private_desc')) .n;
		$log .= JText::_('GROUPS_RESTRICTED_MESSAGE').': '.stripslashes($group->get('restrict_msg'))  .n;
		
		// Log ids of group members
		if ($groupusers) {
			$log .= JText::_('GROUPS_MEMBERS').': ';
			foreach ($groupusers as $gu) 
			{
				$log .= $gu.' ';
			}
			$log .= '' .n;
		}
		$log .= JText::_('GROUPS_MANAGERS').': ';
		foreach ($groupmanagers as $gm) 
		{
			$log .= $gm.' ';
		}
		$log .= '' .n;
		
		// Trigger the functions that delete associated content
		// Should return logs of what was deleted
		$logs = $dispatcher->trigger( 'onGroupDelete', array($group) );
		if (count($logs) > 0) {
			$log .= implode('',$logs);
		}
		
		// Build the file path
		$path = JPATH_ROOT;
		$config = $this->config;
		if (substr($config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $config->get('uploadpath').DS.$group->get('gidNumber');

		if (is_dir($path)) { 
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFolder::delete($path)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_DIRECTORY') );
			}
		}
		
		// Delete group
		if (!$group->delete()) {
			echo GroupsHtml::div( GroupsHtml::hed( 2, $title ), 'full', 'content-header');
			echo GroupsHtml::div( GroupsHtml::error( $group->getError() ), 'main section');
			return;
		}
		
		// Get and set some vars
		$date = date( 'Y-m-d H:i:s', time());
		//$xuser =& XFactory::getUser();
		$xhub =& XFactory::getHub();
		$jconfig =& JFactory::getConfig();
		
		// Build the "from" info for e-mails
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');
		
		// Email group members
		/*foreach ($members as $mbr) 
		{
			//$targetuser =& XUser::getInstance($mbr['login']);
			$targetuser =& JUser::getInstance($mbr);
			
			if (is_object($targetuser)) {
				// E-mail subject
				$subject = JText::sprintf('GROUPS_SUBJECT_GROUP_DELETED', $group->get('cn'));

				// Build the e-mail message
				$email  = JText::sprintf('GROUPS_USER_HAS_DELETED_GROUP', $group->get('cn'), $juser->get('username')).r.n.r.n;
				if ($msg) {
					$email .= stripslashes($msg).r.n.r.n;
				}
				$email .= JText::sprintf('GROUPS_EMAIL_USER_IF_QUESTIONS', $juser->get('username'), $juser->get('email')).r.n;

				// Send the e-mail
				$this->email($targetuser->get('email'), $jconfig->getValue('config.sitename').' '.$subject, $email, $from);
			}
		}*/
		
		// E-mail subject
		$subject = JText::sprintf('GROUPS_SUBJECT_GROUP_DELETED', $group->get('cn'));

		// Build the e-mail message
		$message  = JText::sprintf('GROUPS_USER_HAS_DELETED_GROUP', $group->get('cn'), $juser->get('username')).r.n.r.n;
		if ($msg) {
			$message .= stripslashes($msg).r.n.r.n;
		}
		$message .= JText::sprintf('GROUPS_EMAIL_USER_IF_QUESTIONS', $juser->get('username'), $juser->get('email')).r.n;
		
		/*ximport('xmessage');
		if (!XMessageHelper::sendMessage( 'groups_deleted', $subject, $message, $from, $group->get('members') )) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MEMBERS_FAILED') );
		}*/
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( 'groups_deleted', $subject, $message, $from, $group->get('members'), $this->_option ))) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MEMBERS_FAILED') );
		}
		
		// Log the deletion
		$database =& JFactory::getDBO();
		$xlog = new XGroupLog( $database );
		$xlog->gid = $group->get('gidNumber');
		$xlog->uid = $juser->get('id');
		$xlog->timestamp = date( 'Y-m-d H:i:s', time() );
		$xlog->action = 'group_deleted';
		$xlog->comments = $log;
		$xlog->actorid = $juser->get('id');
		if (!$xlog->store()) {
			$this->setError( $xlog->getError() );
		}
								
		// Redirect back to the groups page
		$this->_redirect = JRoute::_('index.php?option='.$this->_option);
	}

	//----------------------------------------------------------
	// media manager
	//----------------------------------------------------------

	protected function upload()
	{
		// Load the component config
		$config = $this->config;
		
		// Incoming
		$listdir = JRequest::getInt( 'listdir', 0, 'post' );

		// Ensure we have an ID to work with
		if (!$listdir) {
			$this->setError( JText::_('GROUPS_NO_ID') );
			$this->media();
			return;
		}
		
		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name']) {
			$this->setError( JText::_('GROUPS_NO_FILE') );
			$this->media();
			return;
		}

		// Build the upload path if it doesn't exist
		$path = JPATH_ROOT;
		if (substr($config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $config->get('uploadpath').DS.$listdir;
		
		if (!is_dir( $path )) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) {
				$this->setError( JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
				$this->media();
				return;
			}
		}
		
		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path.DS.$file['name'])) {
			$this->setError( JText::_('ERROR_UPLOADING') );
		}
		
		// Push through to the media view
		$this->media();
	}

	//-----------

	protected function deletefolder() 
	{
		// Load the component config
		$config = $this->config;
		
		// Incoming group ID
		$listdir = JRequest::getInt( 'listdir', 0, 'get' );
		if (!$listdir) {
			$this->setError( JText::_('GROUPS_NO_ID') );
			$this->media();
			return;
		}
		
		// Incoming file
		$file = trim(JRequest::getVar( 'folder', '', 'get' ));
		if (!$file) {
			$this->setError( JText::_('GROUPS_NO_DIRECTORY') );
			$this->media();
			return;
		}

		// Delete the folder
		if (is_dir($del_folder)) { 
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFolder::delete($del_folder)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_DIRECTORY') );
			}
		}
		
		// Push through to the media view
		$this->media();
	}

	//-----------

	protected function deletefile() 
	{
		// Load the component config
		$config = $this->config;
		
		// Incoming group ID
		$listdir = JRequest::getInt( 'listdir', 0, 'get' );
		if (!$listdir) {
			$this->setError( JText::_('GROUPS_NO_ID') );
			$this->media();
			return;
		}
		
		// Incoming file
		$file = trim(JRequest::getVar( 'file', '', 'get' ));
		if (!$file) {
			$this->setError( JText::_('GROUPS_NO_FILE') );
			$this->media();
			return;
		}
		
		// Build the file path
		$path = JPATH_ROOT;
		if (substr($config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $config->get('uploadpath').DS.$listdir;

		if (!file_exists($path.DS.$file) or !$file) { 
			$this->setError( JText::_('FILE_NOT_FOUND') );
			$this->media();
			return;
		} else {
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path.DS.$file)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
			}
		}
		
		// Push through to the media view
		$this->media();
	}

	//-----------

	protected function media() 
	{
		// Load the component config
		$config = $this->config;
		
		// Incoming
		$listdir = JRequest::getInt( 'listdir', 0 );
		if (!$listdir) {
			$this->setError( JText::_('GROUPS_NO_ID') );
		}
		
		// Output HTML
		echo GroupsHtml::media($config, $listdir, $this->_option, $this->_name, $this->getError());
	}

	//-----------

	protected function recursive_listdir($base) 
	{ 
	    static $filelist = array(); 
	    static $dirlist  = array(); 

	    if (is_dir($base)) { 
	       $dh = opendir($base); 
	       while (false !== ($dir = readdir($dh))) 
		   { 
	           if (is_dir($base .DS. $dir) && $dir !== '.' && $dir !== '..' && strtolower($dir) !== 'cvs') { 
	                $subbase    = $base .DS. $dir; 
	                $dirlist[]  = $subbase; 
	                $subdirlist = $this->recursive_listdir($subbase); 
	            } 
	        } 
	        closedir($dh); 
	    } 
	    return $dirlist; 
	} 

	//-----------
 
	protected function listfiles() 
	{
		$database =& JFactory::getDBO();
		$config =& $this->config;
		
		// Incoming
		$listdir = JRequest::getInt( 'listdir', 0, 'get' );
		
		if (!$listdir) {
			$this->setError( JText::_('GROUPS_NO_ID') );
		}
		
		$path = JPATH_ROOT;
		if (substr($config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $config->get('uploadpath').DS.$listdir;

		// Get the directory we'll be reading out of
		$d = @dir($path);

		$html = GroupsHtml::attachTop( $this->_option, $this->_name );
		if ($d) {
			$images  = array();
			$folders = array();
			$docs    = array();
	
			// Loop through all files and separate them into arrays of images, folders, and other
			while (false !== ($entry = $d->read())) 
			{
				$img_file = $entry; 
				if (is_file($path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "bmp|gif|jpg|jpeg|jpe|tif|tiff|png", $img_file )) {
						$images[$entry] = $img_file;
					} else {
						$docs[$entry] = $img_file;
					}
				} else if (is_dir($path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'cvs') {
					$folders[$entry] = $img_file;
				}
			}
			$d->close();	

			//$html .= GroupsHtml::imageStyle( $listdir );	

			if (count($images) > 0 || count($folders) > 0 || count($docs) > 0) {	
				ksort($images);
				ksort($folders);
				ksort($docs);

				$html .= GroupsHtml::draw_table_header();

				for ($i=0; $i<count($folders); $i++) 
				{
					$folder_name = key($folders);		
					GroupsHtml::show_dir( DS.$folders[$folder_name], $folder_name, $listdir, $this->_option );
					next($folders);
				}
				for ($i=0; $i<count($docs); $i++) 
				{
					$doc_name = key($docs);	
					$iconfile = $config->get('iconpath').DS.substr($doc_name,-3).'.png';

					if (file_exists(JPATH_ROOT.$iconfile))	{
						$icon = $iconfile;
					} else {
						$icon = $config->get('iconpath').DS.'unknown.png';
					}
					
					//$a = $attachment->getID($doc_name, $listdir);
					
					$html .= GroupsHtml::show_doc($this->_option, $docs[$doc_name], $listdir, $icon);
					next($docs);
				}
				for ($i=0; $i<count($images); $i++) 
				{
					$image_name = key($images);
					$iconfile = $config->get('iconpath').DS.substr($image_name,-3).'.png';
					if (file_exists(JPATH_ROOT.$iconfile))	{
						$icon = $iconfile;
					} else {
						$icon = $config->get('iconpath').DS.'unknown.png';
					}

					//$a = $attachment->getID($image_name, $listdir);

					$html .= GroupsHtml::show_doc($this->_option, $images[$image_name], $listdir, $icon);
					next($images);
				}
				
				$html .= GroupsHtml::draw_table_footer();
			} else {
				$html .= GroupsHtml::draw_no_results();
			}
		} else {
			$html .= GroupsHtml::draw_no_results();
		}
		$html .= GroupsHtml::attachBottom();
		echo $html;
	}

	//----------------------------------------------------------
	// Misc Functions
	//----------------------------------------------------------

	private function _authorize($checkonlymembership=false) 
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		if (!$checkonlymembership) {
			// Check if they're a site admin (from Joomla)
			if ($juser->authorize($this->_option, 'manage')) {
				return 'admin';
			}
		}
		
		// Get the user's groups
		$invitees = XUserHelper::getGroups( $juser->get('id'), 'invitees' );
		$members = XUserHelper::getGroups( $juser->get('id'), 'members' );
		$managers = XUserHelper::getGroups( $juser->get('id'), 'managers' );

		$groups = array();
		$managerids = array();
		if ($managers && count($managers) > 0) {
			foreach ($managers as $manager) 
			{
				$groups[] = $manager;
				$managerids[] = $manager->cn;
			}
		}
		if ($members && count($members) > 0) {
			foreach ($members as $mem) 
			{
				if (!in_array($mem->cn,$managerids)) {
					$groups[] = $mem;
				}
			}
		}
		// Check if they're a member of this group
		if ($groups && count($groups) > 0) {
			foreach ($groups as $ug) 
			{
				if ($ug->cn == $this->gid) {
					// Check if they're a manager of this group
					if ($ug->manager) {
						return 'manager';
					}
					// Are they a confirmed member?
					if ($ug->regconfirmed) {
						return 'member';
					}
				}
			}
		}
		// Check if they're invited to this group
		if ($invitees && count($invitees) > 0) {
			foreach ($invitees as $ug) 
			{
				if ($ug->cn == $this->gid) {
					return 'invitee';
				}
			}
		}
		
		return false;
	}
	
	//-----------

	private function getGroups( $groups )
	{
		$juser =& JFactory::getUser();

		if (!$juser->get('guest')) {
			$ugs = XUserHelper::getGroups( $juser->get('id') );

			for ($i = 0; $i < count($groups); $i++) 
			{
				if (!isset($groups[$i]->cn)) {
					$groups[$i]->cn = '';
				}
				$groups[$i]->registered   = 0;
				$groups[$i]->regconfirmed = 0;
				$groups[$i]->manager      = 0;
				
				if ($ugs && count($ugs) > 0) {
					foreach ($ugs as $ug) 
					{
						if (is_object($ug) && $ug->cn == $groups[$i]->cn) {
							$groups[$i]->registered   = $ug->registered;
							$groups[$i]->regconfirmed = $ug->regconfirmed;
							$groups[$i]->manager      = $ug->manager;
						}
					}
				}
			}
		}
	
		return $groups;
	}
	
	//-----------

	public function email($email, $subject, $message, $from) 
	{
		if ($from) {
			$args = "-f '" . $from['email'] . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=utf-8\n";
			$headers .= 'From: ' . $from['name'] .' <'. $from['email'] . ">\n";
			$headers .= 'Reply-To: ' . $from['name'] .' <'. $from['email'] . ">\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: '. $from['name'] .n;
			if (mail($email, $subject, $message, $headers, $args)) {
				return true;
			}
		}
		return false;
	}
	
	//-----------
	
	private function cleanText( $txt ) 
	{
		$txt = trim($txt);
		$txt = preg_replace( '/{kl_php}(.*?){\/kl_php}/s', '', $txt );
		$txt = preg_replace( '/{.+?}/', '', $txt );
		$txt = preg_replace( "'<script[^>]*>.*?</script>'si", '', $txt );
		$txt = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2', $txt );
		$txt = preg_replace( '/<!--.+?-->/', '', $txt );
		$txt = preg_replace( '/&nbsp;/', ' ', $txt );
		$txt = strip_tags( $txt );
		return $txt;
	}
	
	//-----------

	protected function autocomplete() 
	{
		$filters = array();
		$filters['limit']  = 20;
		$filters['start']  = 0;
		$filters['search'] = trim(JRequest::getString( 'value', '' ));
		
		// Fetch results
		$rows = $this->getAutocomplete( $filters );

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0) {
			foreach ($rows as $row) 
			{
				$json[] = '["'.$row->description.'","'.$row->cn.'"]';
			}
		}
		
		echo '['.implode(',',$json).']';
	}
	
	//-----------

	private function getAutocomplete( $filters=array() ) 
	{
		$database =& JFactory::getDBO();
		
		$query = "SELECT t.gidNumber, t.cn, t.description 
					FROM #__xgroups AS t 
					WHERE (t.type=1 OR t.type=2) AND (LOWER( t.cn ) LIKE '%".$filters['search']."%' OR LOWER( t.description ) LIKE '%".$filters['search']."%')
					ORDER BY t.description ASC";

		$database->setQuery( $query );
		return $database->loadObjectList();
	}
}
?>
