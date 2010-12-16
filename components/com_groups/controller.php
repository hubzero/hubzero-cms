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

ximport('Hubzero_Controller');
ximport('Hubzero_Group');

class GroupsController extends Hubzero_Controller
{
	public function execute()
	{
		// Get the task
		$this->_task = JRequest::getVar( 'task', '' );
		$this->gid  = JRequest::getVar( 'gid', '' );
		$this->active = JRequest::getVar( 'active', '' );
		if ($this->gid && !$this->_task) {
			$this->_task = 'view';
		}
		if ($this->active && $this->_task) {
			$this->action = ($this->_task == 'view') ? '' : $this->_task;
			$this->_task = 'view';
		}
		
		// Execute the task
		switch ($this->_task) 
		{
			// File manager for uploading images/files to be used in group descriptions
			case 'media':        $this->media();        break;
			case 'listfiles':    $this->listfiles();    break;
			case 'upload':       $this->upload();       break;
			case 'deletefolder': $this->deletefolder(); break;
			case 'deletefile':   $this->deletefile();   break;
			
			// Autocompleter - called via AJAX
			case 'autocomplete': $this->autocomplete(); break;
			case 'memberslist': $this->memberslist(); break;
			
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
		
		$this->setError( JText::_('GROUPS_NOT_CONFIGURED') );
		
		// Output HTML
		$view = new JView( array('name'=>'error') );
		$view->title = JText::_(strtoupper($this->_name));
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	protected function login($title='') 
	{
		$title = ($title) ? $title : JText::_(strtoupper($this->_name));
		
		$view = new JView( array('name'=>'login') );
		$view->title = $title;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
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
		$this->_getStyles();

		$sql = "SELECT g.gidNumber, g.cn, g.description, g.public_desc, (SELECT COUNT(*) FROM #__xgroups_members AS gm WHERE gm.gidNumber=g.gidNumber) AS members
				FROM #__xgroups AS g 
				WHERE g.type=1
				AND g.published=1
				AND g.privacy!=4
				ORDER BY members DESC LIMIT 3";
		$this->database->setQuery( $sql );
		$popular = $this->database->loadObjectList();

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
		if (!$this->juser->get('guest')) {
			$authorized = true;
		} else {
			$authorized = false;
		}

		// Push some styles to the template
		$this->_getStyles();
		
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
		$total = Hubzero_Group::find($filters);

		// Filters for returning results
		$filters['limit']  = JRequest::getInt('limit', 25);
		$filters['limit']  = ($filters['limit']) ? $filters['limit'] : 'all';
		$filters['start']  = JRequest::getInt('limitstart', 0);
		$filters['fields'] = array('cn','description','published','gidNumber','type','public_desc','join_policy');

		// Get a list of all groups
		$groups = Hubzero_Group::find($filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

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
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&task='.$this->_task);

		// Output HTML
		$view = new JView( array('name'=>'browse') );
		$view->title = $title;
		$view->option = $this->_option;
		$view->groups = $groups;
		$view->authorized = $authorized;
		$view->pageNav = $pageNav;
		$view->filters = $filters;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function view() 
	{
		// Ensure we have a group to work with
		if ($this->gid == '') {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_ID') );
			return;
		}

		// Load the group page
		$group = Hubzero_Group::getInstance( $this->gid );
		
		// Ensure we found the group info
		if (!is_object($group) || (!$group->get('gidNumber') && !$group->get('cn')) ) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_FOUND') );
			return;
		}

		$this->gid = $group->get('cn');

		// Ensure it's an allowable group type to display
		if ($group->get('type') != 1) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_FOUND') );
			return;
		}
		
		// Push some needed styles to the template
		$this->_getStyles();
		
		// Push some needed scripts to the template
		$this->_getScripts();
		
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
			$public_desc = $p->parse( "\n".stripslashes($public_desc) );
		}
		if ($private_desc) {
			$private_desc = $p->parse( "\n".stripslashes($private_desc) );
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
		$pathway->addItem($title,'index.php?option='.$this->_option.'&gid='.$group->get('cn'));
		
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
					$pathway->addItem($cat[$name],'index.php?option='.$this->_option.'&gid='.$group->get('cn').'&active='.$name);
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
		
		// Show a login prompt if the group is restricted or private and the user isn't logged in
		if ($this->juser->get('guest') && $group->get('privacy') > 0) {
			return $this->login( $title );
		} else {
			// Logged in user - does the user have authority to view this group?
			if ($group->get('privacy') > 1 && !$authorized) {
				$view = new JView( array('name'=>'error') );
				$view->title = $title;
				$view->setError( JText::_('You must be a member of this group to view its content.') );
				$view->display();
				return;
			}
		}

		// Add the default "About" section to the beginning of the lists
		if ($tab == 'overview') {
			$view = new JView( array('name'=>'view', 'layout'=>'overview') );
			$view->option = $this->_option;
			$view->group = $group;
			$view->authorized = $authorized;
			$view->cats = $cats;
			$view->sections = $sections;
			$view->ismember = $ismember;
			$view->public_desc = $public_desc;
			$view->private_desc = $private_desc;
			$view->juser = $this->juser;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$body = $view->loadTemplate();
		} else {
			$body = '';
		}

		// Push the overview view to the array of sections we're going to output
		$cat = array();
		$cat['overview'] = JText::_('GROUPS_OVERVIEW');
		array_unshift($cats, $cat);
		array_unshift($sections, array('html'=>$body,'metadata'=>''));

		// Output HTML
		$view = new JView( array('name'=>'view') );
		$view->option = $this->_option;
		$view->group = $group;
		$view->authorized = $authorized;
		$view->cats = $cats;
		$view->sections = $sections;
		$view->tab = $tab;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
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
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_ID') );
			return;
		}

		// Load the group page
		$group = Hubzero_Group::getInstance( $this->gid );

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_FOUND') );
			return;
		}
		
		$gtitle = ($group->get('description')) ? stripslashes($group->get('description')) : stripslashes($group->get('cn'));
		
		// Reset the title to include the group name
		$title  = JText::_(strtoupper($this->_name));
		$title .= ': '.$gtitle;
		$title .= ($this->_task) ? ': '.JText::_(strtoupper($this->_task)) : '';
		$document->setTitle( $title );
		
		$pathway->addItem($gtitle,'index.php?option='.$this->_option.'&gid='.$group->get('cn'));
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&gid='.$group->get('cn').'&task='.$this->_task);
		
		// Check if they're logged in	
		if ($this->juser->get('guest')) {
			$this->login( $title );
			return;
		}
		
		// Check if the user is already a member, applicant, invitee, or manager
		if ($group->is_member_of('applicants',$this->juser->get('id')) || 
			$group->is_member_of('members',$this->juser->get('id')) || 
			$group->is_member_of('managers',$this->juser->get('id')) || 
			$group->is_member_of('invitees',$this->juser->get('id'))) {
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
				// Output HTML
				$view = new JView( array('name'=>'join') );
				$view->option = $this->_option;
				$view->title = $title;
				$view->group = $group;
				if ($this->getError()) {
					$view->setError( $this->getError() );
				}
				$view->display();
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
		if ($this->juser->get('guest')) {
			$this->login( $title );
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_ID') );
			return;
		}
		
		// Load the group
		$group = Hubzero_Group::getInstance( $this->gid );

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_FOUND') );
			return;
		}
		
		// Remove the user from the group
		$group->remove('managers',$this->juser->get('id'));
		$group->remove('members',$this->juser->get('id'));
		$group->remove('applicants',$this->juser->get('id'));
		$group->remove('invitees',$this->juser->get('id'));
		if ($group->update() === false) {
			$this->setError( JText::_('GROUPS_ERROR_CANCEL_MEMBERSHIP_FAILED') );
		}
		
		// Log the membership cancellation
		$log = new XGroupLog( $this->database );
		$log->gid = $group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		$log->action = 'membership_cancelled';
		$log->actorid = $this->juser->get('id');
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}
		
		// Remove record of reason wanting to join group
		$reason = new GroupsReason( $this->database );
		$reason->deleteReason( $this->juser->get('username'), $group->get('cn') );

		$xhub =& Hubzero_Factory::getHub();
		$jconfig =& JFactory::getConfig();
		
		// Email subject
		$subject = JText::sprintf('GROUPS_SUBJECT_MEMBERSHIP_CANCELLED', $group->get('cn'));

		// Build the e-mail message
		$eview = new JView( array('name'=>'emails','layout'=>'cancelled') );
		$eview->option = $this->_option;
		$eview->hubShortName = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $group;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Get the managers' e-mails
		$emailmanagers = $group->getEmails('managers');

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// E-mail the administrator
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( 'groups_cancelled_me', $subject, $message, $from, $group->get('managers'), $this->_option ))) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED').' '.$emailadmin );
		}

		// Action Complete. Redirect to appropriate page
		if ($return == 'browse') {
			$xhub->redirect(JRoute::_('index.php?option='.$this->_option));
		} else {
			$xhub->redirect(JRoute::_('index.php?option='.$this->_option.'&gid='. $group->get('cn')));
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
		if ($this->juser->get('guest')) {
			$this->login( $title );
			return;
		}
		// Ensure we have a group to work with
		if (!$this->gid) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_ID') );
			return;
		}
		
		// Load the group
		$group = Hubzero_Group::getInstance( $this->gid );

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_FOUND') );
			return;
		}

		// Get the managers' e-mails
		$emailmanagers = $group->getEmails('managers');
		
		// Auto-approve member for group without any managers
		if (count($emailmanagers) < 1) {
			$group->add('managers',array($this->juser->get('id')));
		} else {
			if ($group->get('join_policy') == 0) {
				$group->add('members',array($this->juser->get('id')));
			} else {
				$group->add('applicants',array($this->juser->get('id')));
			}
		}
		if ($group->update() === false) {
			$this->setError( JText::_('GROUPS_ERROR_REGISTER_MEMBERSHIP_FAILED') );
		}

		if ($group->get('join_policy') == 1) {
			// Instantiate the reason object and bind the incoming data
			$row = new GroupsReason( $this->database );
			$row->uidNumber = $this->juser->get('id');
			$row->gidNumber = $group->get('gidNumber');
			$row->reason    = JRequest::getVar( 'reason', JText::_('GROUPS_NO_REASON_GIVEN'), 'post' );
			$row->reason    = Hubzero_View_Helper_Html::purifyText($row->reason);
			$row->date      = date( 'Y-m-d H:i:s', time());

			// Check and store the reason
			if (!$row->check()) {
				JError::raiseError( 500, $row->getError() );
				return;
			}
			if (!$row->store()) {
				JError::raiseError( 500, $row->getError() );
				return;
			}
		}
		
		// Log the membership request
		$log = new XGroupLog( $this->database );
		$log->gid = $group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		$log->action = 'membership_requested';
		$log->actorid = $this->juser->get('id');
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}
		// Log the membership approval if the join policy is open
		if ($group->get('join_policy') == 0) {
			$log2 = new XGroupLog( $this->database );
			$log2->gid = $group->get('gidNumber');
			$log2->uid = $this->juser->get('id');
			$log2->timestamp = date( 'Y-m-d H:i:s', time() );
			$log2->action = 'membership_approved';
			$log2->actorid = $this->juser->get('id');
			if (!$log2->store()) {
				$this->setError( $log2->getError() );
			}
		}
		
		$xhub =& Hubzero_Factory::getHub();
		$jconfig =& JFactory::getConfig();
			
		// E-mail subject
		$subject = JText::sprintf('GROUPS_SUBJECT_MEMBERSHIP', $group->get('cn'));

		// Build the e-mail message
		$eview = new JView( array('name'=>'emails','layout'=>'request') );
		$eview->option = $this->_option;
		$eview->hubShortName = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $group;
		$eview->row = $row;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');
		
		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// E-mail the administrator
		if ($group->get('join_policy') == 1) {
			$url = 'index.php?option='.$this->_option.'&gid='.$group->get('cn').'&active=members';
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
		if ($this->juser->get('guest')) {
			$this->login( $title );
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_ID') );
			return;
		}
		
		// Load the group
		$group = Hubzero_Group::getInstance( $this->gid );

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_FOUND') );
			return;
		}

		// Move the member from the invitee list to the members list
		$group->add('members',array($this->juser->get('id')));
		$group->remove('invitees',array($this->juser->get('id')));
		$group->update();
		if ($group->getError()) {
			$this->setError( JText::_('GROUPS_ERROR_REGISTER_MEMBERSHIP_FAILED') );
		}
		
		// Log the invite acceptance
		$log = new XGroupLog( $this->database );
		$log->gid = $group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		$log->action = 'membership_invite_accepted';
		$log->actorid = $this->juser->get('id');
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}

		$xhub =& Hubzero_Factory::getHub();
		$jconfig =& JFactory::getConfig();
		
		// E-mail subject
		$subject = JText::sprintf('GROUPS_SUBJECT_MEMBERSHIP', $group->get('cn'));

		// Build the e-mail message
		$eview = new JView( array('name'=>'emails','layout'=>'accepted') );
		$eview->option = $this->_option;
		$eview->hubShortName = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $group;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);
		
		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// E-mail the administrator
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( 'groups_accepts_membership', $subject, $message, $from, $group->get('managers'), $this->_option ))) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED').' '.$emailadmin );
		}

		// Action Complete. Redirect to appropriate page
		if ($return == 'browse') {
			$xhub->redirect(JRoute::_('index.php?option='.$this->_option));
		} else {
			$xhub->redirect(JRoute::_('index.php?option='.$this->_option.'&gid='. $group->get('cn')));
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
		$this->_getStyles();
		
		// Check if they're logged in
		if ($this->juser->get('guest')) {
			$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&task='.$this->_task);
			
			$this->login( $title );
			return;
		}
		
		// Check authorization
		$authorized = $this->_authorize();
		if (!$authorized && $this->_task != 'new') {
			$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&task='.$this->_task);
			
			JError::raiseError( 403, JText::_('GROUPS_NOT_AUTH') );
			return;
		}

		// Instantiate an Hubzero_Group object
		$group = new Hubzero_Group();
		
		if ($this->_task != 'new') {
			// Ensure we have a group to work with
			if (!$this->gid) {
				$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&task='.$this->_task);
				
				JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_ID') );
				return;
			}

			// Load the group
			$group->select( $this->gid );

			// Ensure we found the group info
			if (!$group) {
				$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&task='.$this->_task);

				JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_FOUND') );
				return;
			}
			
			$gtitle = ($group->get('description')) ? stripslashes($group->get('description')) : stripslashes($group->get('cn'));
			
			// Set the pathway
			$pathway->addItem($gtitle,'index.php?option='.$this->_option.'&gid='.$group->get('cn'));
		} else {
			$group->set('join_policy', $this->config->get('join_policy'));
			$group->set('privacy', $this->config->get('privacy'));
			$group->set('access', $this->config->get('access'));
		}

		$p  = 'index.php?option='.$this->_option.'&task='.$this->_task;
		$p .= ($group->get('cn')) ? '&gid='.$group->get('cn') : '';
		$pathway->addItem(JText::_(strtoupper($this->_task)),$p);

		// Get the group's interests (tags)
		$gt = new GroupsTags( $this->database );
		$tags = $gt->get_tag_string( $group->get('gidNumber') );

		// Output HTML
		$view = new JView( array('name'=>'edit') );
		$view->option = $this->_option;
		$view->title = $title;
		$view->group = $group;
		$view->tags = $tags;
		$view->task = $this->_task;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
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
			JError::raiseError( 404, JText::_('GROUPS_NOT_AUTH') );
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_ID') );
			return;
		}
		
		// Load the group
		$group = Hubzero_Group::getInstance( $this->gid );

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_FOUND') );
			return;
		}
		
		// Approve the group
		$group->set('published',1);
		$group->update();
		
		if ($group->getError()) {
			$this->setError( JText::_('GROUPS_ERROR_APPROVING_GROUP') );
			$this->view();
			return;
		}
		
		// Log the group approval
		$log = new XGroupLog( $this->database );
		$log->gid = $group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		$log->action = 'group_approved';
		$log->actorid = $this->juser->get('id');
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}
		
		$xhub =& Hubzero_Factory::getHub();
		$jconfig =& JFactory::getConfig();
		
		// Get the managers' e-mails
		$emails = $group->getEmails('managers');
		
		// E-mail subject
		$subject = JText::sprintf('GROUPS_SUBJECT_GROUP_APPROVED', $group->get('cn'));
		
		// Build the e-mail message	
		$eview = new JView( array('name'=>'emails','layout'=>'approved') );
		$eview->option = $this->_option;
		$eview->hubShortName = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $group;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);
		
		// Get the administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');
		
		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');
		
		// Send e-mail to the group managers
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
		if ($this->juser->get('guest')) {
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
		
		// Instantiate an Hubzero_Group object
		$group = new Hubzero_Group();
		
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
			$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&task='.$this->_task);

			// Push some needed styles to the template
			$this->_getStyles();

			$group->set('description', $g_description );
			$group->set('access', $g_access );
			$group->set('privacy', $g_privacy );
			$group->set('public_desc', $g_public_desc );
			$group->set('private_desc', $g_private_desc );
			$group->set('restrict_msg',$g_restrict_msg);
			$group->set('join_policy',$g_join_policy);
			$group->set('cn',$g_cn);
			
			$view = new JView( array('name'=>'edit') );
			$view->title = $title;
			$view->option = $this->_option;
			$view->group = $group;
			$view->task = $this->_task;
			$view->tags = $tags;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			return;
		}
		
		// Ensure the data passed is valid
		if ($g_cn == 'new' || $g_cn == 'browse') {
			$this->setError( JText::_('GROUPS_ERROR_INVALID_ID') );
		}
		if (!$this->_validCn($g_cn)) {
			$this->setError( JText::_('GROUPS_ERROR_INVALID_ID') );
		}
		if ($isNew && Hubzero_Group::exists($g_cn)) {
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
			$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&task='.$this->_task);

			// Push some needed styles to the template
			$this->_getStyles();
			
			$group->set('description', $g_description );
			$group->set('access', $g_access );
			$group->set('privacy', $g_privacy );
			$group->set('public_desc', $g_public_desc );
			$group->set('private_desc', $g_private_desc );
			$group->set('restrict_msg',$g_restrict_msg);
			$group->set('join_policy',$g_join_policy);
			$group->set('cn',$g_cn);
			
			$view = new JView( array('name'=>'edit') );
			$view->valid_cn = $this->_validCn($g_cn);
			$view->title = $title;
			$view->option = $this->_option;
			$view->group = $group;
			$view->task = $this->_task;
			$view->tags = $tags;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			return;
		}
		
		// Get some needed objects
		$xhub =& Hubzero_Factory::getHub();
		$jconfig =& JFactory::getConfig();
				
		// Build the e-mail message
		if ($isNew) {
			$subject = JText::sprintf('GROUPS_SUBJECT_GROUP_REQUESTED', $g_cn);
		} else {
			$subject = JText::sprintf('GROUPS_SUBJECT_GROUP_UPDATED', $g_cn);
		}
								
		if ($isNew) {
			$type = 'groups_created';
		} else {
			$type = 'groups_changed';
		}
		
		// Build the e-mail message
		// Note: this is done *before* pushing the changes to the group so we can show, in the message, what was changed
		$eview = new JView( array('name'=>'emails','layout'=>'saved') );
		$eview->option = $this->_option;
		$eview->hubShortName = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $group;
		$eview->isNew = $isNew;
		$eview->g_description = $g_description;
		$eview->g_access = $g_access;
		$eview->g_privacy = $g_privacy;
		$eview->g_public_desc = $g_public_desc;
		$eview->g_private_desc = $g_private_desc;
		$eview->g_restrict_msg = $g_restrict_msg;
		$eview->g_join_policy = $g_join_policy;
		$eview->g_cn = $g_cn;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);
		
		// Set the group changes and save
		$group->set('cn', $g_cn );
		if ($isNew) {
			$group->create();
			$group->set('type', 1 );
			$group->set('published', 1 );
			
			$group->add('managers',array($this->juser->get('id')));
			$group->add('members',array($this->juser->get('id')));
		}
		$group->set('description', $g_description );
		$group->set('access', $g_access );
		$group->set('privacy', $g_privacy );
		$group->set('public_desc', $g_public_desc );
		$group->set('private_desc', $g_private_desc );
		$group->set('restrict_msg',$g_restrict_msg);
		$group->set('join_policy',$g_join_policy);
		$group->update();
		
		// Process tags
		$gt = new GroupsTags( $this->database );
		$gt->tag_object($this->juser->get('id'), $group->get('gidNumber'), $tags, 1, 1);
		
		// Log the group save
		$log = new XGroupLog( $this->database );
		$log->gid = $group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		$log->actorid = $this->juser->get('id');
		
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
			
			// Get plugins
			JPluginHelper::importPlugin( 'groups' );
			$dispatcher =& JDispatcher::getInstance();
			
			// Trigger the functions that delete associated content
			// Should return logs of what was deleted
			$logs = $dispatcher->trigger( 'onGroupNew', array($group) );
			if (count($logs) > 0) {
				$log->comments .= implode('',$logs);
			}
		} else {
			$log->action = 'group_edited';
		}
		
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}
		
		if ($isNew) {
			
		}
		
		// Get the administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');
		
		// Get the "from" info
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');
		
		// Get plugins
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( $type, $subject, $message, $from, $group->get('managers'), $this->_option ))) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') );
		}
		
		if ($this->getError()) {
			$view = new JView( array('name'=>'error') );
			$view->title = $title;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			return;
		}
		
		// Redirect back to the group page
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&gid='.$g_cn);
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
		if ($this->juser->get('guest')) {
			$this->login( $title );
			return;
		}
		
		// Check authorization
		$authorized = $this->_authorize();
		if (!$authorized) {
			JError::raiseError( 403, JText::_('GROUPS_NOT_AUTH') );
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_ID') );
			return;
		}

		// Load the group page
		$group = Hubzero_Group::getInstance( $this->gid );
		
		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_FOUND') );
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
			$this->_getStyles();

			// Push some needed scripts to the template
			$this->_getScripts();
			
			// Set the pathway
			$gtitle = ($group->get('description')) ? stripslashes($group->get('description')) : stripslashes($group->get('cn'));
			
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			if (count($pathway->getPathWay()) <= 0) {
				$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
			}
			$pathway->addItem($gtitle,'index.php?option='.$this->_option.'&gid='.$group->get('cn'));
			$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&gid='.$group->get('cn').'&task='.$this->_task);
			
			// Output HTML
			$view = new JView( array('name'=>'invite') );
			$view->option = $this->_option;
			$view->title = $title;
			$view->group = $group;
			$view->return = $return;
			$view->msg = $msg;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			return;
		}
		
		$return = trim(JRequest::getVar( 'return', '', 'post' ));
		$invitees = array();
		$inviteemails = array();
		$apps = array();
		$mems = array();
		$registeredemails = array();

		// Get all the group's managers
		$members = $group->get('members');
		$applicants = $group->get('applicants');

		// Explod the string of logins/e-mails into an array
		if (strstr($logins,',')) {
			$la = explode(',',$logins);
		} else {
			$la = array($logins);
		}
		foreach ($la as $l) 
		{
			// Trim up the content
			$l = trim($l);

			// Check if it's an e-mail address
			if (eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $l)) {
				// Try to find an account that might match this e-mail
				$this->database->setQuery("SELECT u.id FROM #__users AS u WHERE u.email='". $l ."' OR u.email LIKE '".$l."\'%' LIMIT 1;");
				$uid = $this->database->loadResult();
				if (!$this->database->query()) {
					$this->setError( $this->database->getErrorMsg() );
				}

				// If we found an ID, add it to the invitees list
				if ($uid) {
					$invitees[] = $uid;
				} else {
					$inviteemails[] = $l;
					//$registeredemails[] = $l;
				}
			} else {
				// Retrieve user's account info
				$user = JUser::getInstance($l);

				// Ensure we found an account
				if (is_object($user)) {
					$uid = $user->get('id');
					if (!in_array($uid,$members)) {
						if (in_array($uid,$applicants)) {
							$apps[] = $uid;
							$mems[] = $uid;
						} else {
							$invitees[] = $uid;
							//$inviteemails[] = $user->get('email');
							//$registeredemails[] = $user->get('email');
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
			$log = new XGroupLog( $this->database );
			$log->gid = $group->get('gidNumber');
			$log->uid = $invite;
			$log->timestamp = date( 'Y-m-d H:i:s', time() );
			$log->action = 'membership_invites_sent';
			$log->actorid = $this->juser->get('id');
			if (!$log->store()) {
				$this->setError( $log->getError() );
			}
		}
		
		// Get and set some vars
		$xhub =& Hubzero_Factory::getHub();
		$jconfig =& JFactory::getConfig();
		
		// Build the "from" info for e-mails
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Message subject
		$subject = JText::sprintf('GROUPS_SUBJECT_INVITE', $group->get('cn'));

		// Message body
		$eview = new JView( array('name'=>'emails','layout'=>'invite') );
		$eview->option = $this->_option;
		$eview->hubShortName = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $group;
		$eview->msg = $msg;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);
		
		$juri = JURI::getInstance();
		
		foreach ($inviteemails as $mbr) 
		{
			if (!in_array($mbr, $registeredemails)) {
				$message .= JText::sprintf('GROUPS_PLEASE_REGISTER', $jconfig->getValue('config.sitename'), $juri->base() . 'register')."\r\n\r\n";
			}
			
			// Send the e-mail
			if (!$this->email($mbr, $jconfig->getValue('config.sitename').' '.$subject, $message, $from)) {
				$this->setError( JText::_('GROUPS_ERROR_EMAIL_INVITEE_FAILED').' '.$mbr );
			}
		}
		
		// Send the message
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( 'groups_invite', $subject, $message, $from, $invitees, $this->_option ))) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_INVITEE_FAILED') );
		}
		
		// Do we need to redirect?
		if ($return == 'members') {
			$xhub->redirect( JRoute::_('index.php?option='.$this->_option.'&gid='. $group->get('cn').'&active=members') );
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
		if ($this->juser->get('guest')) {
			$this->login( $title );
			return;
		}
		
		// Check authorization
		$authorized = $this->_authorize();
		if (!$authorized) {
			JError::raiseError( 403, JText::_('GROUPS_NOT_AUTH') );
			return;
		}

		// Ensure we have a group to work with
		if (!$this->gid) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_ID') );
			return;
		}

		// Load the group page
		$group = Hubzero_Group::getInstance( $this->gid );
		
		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber')) {
			JError::raiseError( 404, JText::_('GROUPS_NO_GROUP_FOUND') );
			return;
		}

		// Push some needed styles to the template
		$this->_getStyles();
		
		// Push some needed scripts to the template
		$this->_getScripts();
		
		// Get number of group members
		$members = $group->get('members');
		$managers = $group->get('managers');

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
			$pathway->addItem($gtitle,'index.php?option='.$this->_option.'&gid='.$group->get('cn'));
			$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&gid='.$group->get('cn').'&task='.$this->_task);
			
			// Output HTML
			$view = new JView( array('name'=>'delete') );
			$view->option = $this->_option;
			$view->title = $title;
			$view->group = $group;
			$view->log = $log;
			$view->msg = $msg;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			return;
		}
		
		// Start log
		$log  = JText::sprintf('GROUPS_SUBJECT_GROUP_DELETED', $group->get('cn'));
		$log .= JText::_('GROUPS_TITLE').': '.$group->get('description')."\n";
		$log .= JText::_('GROUPS_ID').': '.$group->get('cn')."\n";
		$log .= JText::_('GROUPS_PRIVACY').': '.$group->get('access')."\n";
		$log .= JText::_('GROUPS_PUBLIC_TEXT').': '.stripslashes($group->get('public_desc')) ."\n";
		$log .= JText::_('GROUPS_PRIVATE_TEXT').': '.stripslashes($group->get('private_desc')) ."\n";
		$log .= JText::_('GROUPS_RESTRICTED_MESSAGE').': '.stripslashes($group->get('restrict_msg'))."\n";
		
		// Log ids of group members
		if ($members) {
			$log .= JText::_('GROUPS_MEMBERS').': ';
			foreach ($members as $gu) 
			{
				$log .= $gu.' ';
			}
			$log .= '' ."\n";
		}
		$log .= JText::_('GROUPS_MANAGERS').': ';
		foreach ($managers as $gm) 
		{
			$log .= $gm.' ';
		}
		$log .= '' ."\n";
		
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
		
		$gidNumber = $group->get('gidNumber');
		$gcn = $group->get('cn');
		//$members = $group->get('members');
		
		$deletedgroup = clone($group);
		
		// Delete group
		if (!$group->delete()) {
			$view = new JView( array('name'=>'error') );
			$view->title = $title;
			if ($group->getError()) {
				$view->setError( $group->getError() );
			}
			$view->display();
			return;
		}
		
		// Get and set some vars
		$date = date( 'Y-m-d H:i:s', time());

		//$xhub =& Hubzero_Factory::getHub();
		$jconfig =& JFactory::getConfig();
		
		// Build the "from" info for e-mails
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');
		
		// E-mail subject
		$subject = JText::sprintf('GROUPS_SUBJECT_GROUP_DELETED', $gcn);

		// Build the e-mail message
		$eview = new JView( array('name'=>'emails','layout'=>'deleted') );
		$eview->option = $this->_option;
		$eview->hubShortName = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->gcn = $gcn;
		$eview->msg = $msg;
		$eview->group = $deletedgroup;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);
		
		// Send the message
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( 'groups_deleted', $subject, $message, $from, $members, $this->_option ))) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MEMBERS_FAILED') );
		}
		
		// Log the deletion
		$xlog = new XGroupLog( $this->database );
		$xlog->gid = $gidNumber;
		$xlog->uid = $this->juser->get('id');
		$xlog->timestamp = date( 'Y-m-d H:i:s', time() );
		$xlog->action = 'group_deleted';
		$xlog->comments = $log;
		$xlog->actorid = $this->juser->get('id');
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
		// Check if they're logged in
		if ($this->juser->get('guest')) {
			$this->media();
			return;
		}
		
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
		// Check if they're logged in
		if ($this->juser->get('guest')) {
			$this->media();
			return;
		}
		
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
		// Check if they're logged in
		if ($this->juser->get('guest')) {
			$this->media();
			return;
		}
		
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
		$view = new JView( array('name'=>'edit', 'layout'=>'filebrowser') );
		$view->option = $this->_option;
		$view->config = $this->config;
		$view->listdir = $listdir;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
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
		// Incoming
		$listdir = JRequest::getInt( 'listdir', 0, 'get' );
		
		if (!$listdir) {
			$this->setError( JText::_('GROUPS_NO_ID') );
		}
		
		$path = JPATH_ROOT;
		if (substr($this->config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $this->config->get('uploadpath').DS.$listdir;

		// Get the directory we'll be reading out of
		$d = @dir($path);

		$images  = array();
		$folders = array();
		$docs    = array();
		
		if ($d) {
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

			ksort($images);
			ksort($folders);
			ksort($docs);
		//} else {
		//	$this->setError( JText::sprintf('ERROR_MISSING_DIRECTORY', $path) );
		}

		$view = new JView( array('name'=>'edit', 'layout'=>'filelist') );
		$view->option = $this->_option;
		$view->docs = $docs;
		$view->folders = $folders;
		$view->images = $images;
		$view->config = $this->config;
		$view->listdir = $listdir;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//----------------------------------------------------------
	// Misc Functions
	//----------------------------------------------------------

	protected function _authorize($checkonlymembership=false) 
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) {
			return false;
		}
		
		if (!$checkonlymembership) {
			// Check if they're a site admin (from Joomla)
			if ($this->juser->authorize($this->_option, 'manage')) {
				return 'admin';
			}
		}
		
		// Get the user's groups
		$invitees = Hubzero_User_Helper::getGroups( $this->juser->get('id'), 'invitees' );
		$members = Hubzero_User_Helper::getGroups( $this->juser->get('id'), 'members' );
		$managers = Hubzero_User_Helper::getGroups( $this->juser->get('id'), 'managers' );

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
		if (!$this->juser->get('guest')) {
			$ugs = Hubzero_User_Helper::getGroups( $this->juser->get('id') );

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
			$headers .= 'X-Mailer: '. $from['name'] ."\n";
			if (mail($email, $subject, $message, $headers, $args)) {
				return true;
			}
		}
		return false;
	}
	
	//-----------

	protected function autocomplete() 
	{
		$filters = array();
		$filters['limit']  = 20;
		$filters['start']  = 0;
		$filters['search'] = trim(JRequest::getString( 'value', '' ));
		
		// Fetch results
		$rows = $this->_getAutocomplete( $filters );

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0) {
			foreach ($rows as $row) 
			{
				$json[] = '["'.htmlentities(stripslashes($row->description),ENT_COMPAT,'UTF-8').'","'.$row->cn.'"]';
			}
		}
		
		echo '['.implode(',',$json).']';
	}
	
	//-----------

	private function _getAutocomplete( $filters=array() ) 
	{
		$query = "SELECT t.gidNumber, t.cn, t.description 
					FROM #__xgroups AS t 
					WHERE (t.type=1 OR t.type=2) AND (LOWER( t.cn ) LIKE '%".$filters['search']."%' OR LOWER( t.description ) LIKE '%".$filters['search']."%')
					ORDER BY t.description ASC";

		$this->database->setQuery( $query );
		return $this->database->loadObjectList();
	}
	
	//-----------
	
	protected function memberslist() 
	{
		// Fetch results
		$filters = array();
		$filters['cn'] = trim(JRequest::getString( 'group', '' ));
		
		if ($filters['cn']) {
			$query = "SELECT u.username, u.name 
						FROM #__users AS u, #__xgroups_members AS m, #__xgroups AS g
						WHERE g.cn='".$filters['cn']."' AND g.gidNumber=m.gidNumber AND m.uidNumber=u.id
						ORDER BY u.name ASC";
		} else {
			$query = "SELECT a.username, a.name"
				. "\n FROM #__users AS a"
				. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
				. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id"	// map aro to group
				. "\n INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id"
				. "\n WHERE a.block = '0' AND g.id=25"
				. "\n ORDER BY a.name";
		}

		$this->database->setQuery( $query );
		$rows = $this->database->loadObjectList();

		// Output search results in JSON format
		$json = array();
		if ($filters['cn'] == '') {
			$json[] = '{"username":"","name":"No User"}';
		}
		if (count($rows) > 0) {
			foreach ($rows as $row) 
			{
				$json[] = '{"username":"'.$row->username.'","name":"'.htmlentities(stripslashes($row->name),ENT_COMPAT,'UTF-8').'"}';
			}
		}
		
		echo '{"members":['.implode(',',$json).']}';
	}
	
	//-----------
	
    private function _validCn($gid) 
	{
		if (eregi("^[0-9a-zA-Z]+[_0-9a-zA-Z]*$", $gid)) {
			if (is_numeric($gid) && intval($gid) == $gid && $gid >= 0) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
}
