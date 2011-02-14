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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_groups_messages' );

//-----------

class plgGroupsMessages extends JPlugin
{
	public function plgGroupsMessages(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'groups', 'messages' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onGroupAreas( $authorized ) 
	{
		/*if (!$authorized) {
			$areas = array();
		} else {*/
			$areas = array(
				'messages' => JText::_('PLG_GROUPS_MESSAGES')
			);
		//}
		return $areas;
	}

	//-----------

	public function onGroup( $group, $option, $authorized, $limit=0, $limitstart=0, $action='', $areas=null )
	{
		$return = 'html';
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onGroupAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onGroupAreas( $authorized ) ) )) {
				$return = '';
			}
		}

		// The output array we're returning
		$arr = array(
			'html'=>'',
			'metadata'=>'',
			'dashboard'=>''
		);

		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			if ($return == 'html') {
				ximport('Hubzero_Module_Helper');
				$arr['html']  = '<p class="warning">'. JText::_('GROUPS_LOGIN_NOTICE') .'</p>';
				$arr['html'] .= Hubzero_Module_Helper::renderModules('force_mod');
			}
			return $arr;
		}
		
		// Return no data if the user is not authorized
		if (!$authorized || ($authorized != 'admin' && $authorized != 'manager' && $authorized != 'member')) {
			if ($return == 'html') {
				$arr['html'] = '<p class="warning">'. JText::_('You are not authorized to view this content.') .'</p>';
			}
			return $arr;
		}
		
		// Are we on the overview page?
		if ($areas[0] == 'overview') {
			$return = 'metadata';
		}
		
		// Do we need to return any data?
		if ($return != 'html' && $return != 'metadata') {
			return $arr;
		}
		
		// Set some variables so other functions have access
		$this->authorized = $authorized;
		$this->action = $action;
		$this->_option = $option;
		$this->group = $group;
		$this->_name = substr($option,4,strlen($option));
		
		// Are we returning HTML?
		if ($return == 'html') {
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('groups', 'messages');
			
			// Load some needed libraries
			ximport('Hubzero_Message');
			
			$task = strtolower(trim($action));
			
			$mid = JRequest::getInt('msg',0);
			if ($mid) {
				$task = 'view';
			}

			switch ($task) 
			{
				case 'send': $arr['html'] = $this->_send();     break;
				case 'new':  $arr['html'] = $this->_create();   break;
				case 'view': $arr['html'] = $this->_view($mid); break;
				case 'sent':          
				default:     $arr['html'] = $this->_sent();     break;
			}
		} else {
			//$recipient = new Hubzero_Message_Recipient( $database );
			//$rows = $recipient->getUnreadMessages( $member->get('uidNumber'), 0 );
			
			$arr['metadata'] = ''; //'<p class="messages"><a href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages').'">'.JText::sprintf('%s Unread Messages', count($rows)).'</a></p>'.n;
			
			$arr['dashboard'] = '';
		}

		// Return data
		return $arr;
	}

	//-----------
	
	protected function _sent() 
	{
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$this->group->get('description').': '.JText::_('PLG_GROUPS_MESSAGES_SENT') );
		
		// Filters for returning results
		$filters = array();
		$filters['limit'] = JRequest::getInt('limit', 10);
		$filters['start'] = JRequest::getInt('limitstart', 0);
		$filters['group_id'] = $this->group->get('gidNumber');
		
		// Instantiate our message object
		$database =& JFactory::getDBO();
		$recipient = new Hubzero_Message_Message( $database );
		
		// Retrieve data
		$total = $recipient->getSentMessagesCount( $filters );
		
		$rows = $recipient->getSentMessages( $filters );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Instantiate a view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'groups',
				'element'=>'messages',
				'name'=>'sent'
			)
		);
		
		// Pass some info to the view
		$view->option = $this->_option;
		$view->group = $this->group;
		$view->authorized = $this->authorized;
		$view->rows = $rows;
		$view->pageNav = $pageNav;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Return the output
		return $view->loadTemplate();
	}
	
	//-----------
	
	protected function _view($mid) 
	{
		$database =& JFactory::getDBO();
		
		// Load the message and parse it
		$xmessage = new Hubzero_Message_Message( $database );
		$xmessage->load( $mid );
		$xmessage->message = stripslashes($xmessage->message);
		$xmessage->message = str_replace("\n","\n ",$xmessage->message);
		$xmessage->message = preg_replace_callback("/[^=\"\'](https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])/", array('plgGroupsMessages','autolink'), $xmessage->message);
		$xmessage->message = nl2br($xmessage->message);
		$xmessage->message = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$xmessage->message);
		
		if (substr($xmessage->component,0,4) == 'com_') {
			$xmessage->component = substr($xmessage->component,4);
		}
		
		// Instantiate the view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'groups',
				'element'=>'messages',
				'name'=>'message'
			)
		);
		
		// Pass the view some info
		$view->option = $this->_option;
		$view->group = $this->group;
		$view->authorized = $this->authorized;
		$view->xmessage = $xmessage;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Return the output
		return $view->loadTemplate();
	}
	
	//-----------

	public function autolink($matches) 
	{
		$href = $matches[0];

		if (substr($href, 0, 1) == '!') {
			return substr($href, 1);
		}
		
		$href = str_replace('"','',$href);
		$href = str_replace("'",'',$href);
		$href = str_replace('&#8221','',$href);
		
		$h = array('h','m','f','g','n');
		if (!in_array(substr($href,0,1), $h)) {
			$href = substr($href, 1);
		}
		$name = trim($href);
		if (substr($name, 0, 7) == 'mailto:') {
			$name = substr($name, 7, strlen($name));
			$name = plgGroupsMessages::obfuscate($name);
			//$href = Eventshtml::obfuscate($href);
			$href = 'mailto:'.$name;
		}
		$l = sprintf(
			' <a class="ext-link" href="%s" rel="external">%s</a>',$href,$name
		);
		return $l;
	}
	
	//-----------
	
	public function obfuscate( $email )
	{
		$length = strlen($email);
		$obfuscatedEmail = '';
		for ($i = 0; $i < $length; $i++) 
		{
			$obfuscatedEmail .= '&#'. ord($email[$i]) .';';
		}
		
		return $obfuscatedEmail;
	}

	//-----------
	
	protected function _create() 
	{
		// Ensure only admins and group managers can create messages
		if ($this->authorized != 'manager' && $this->authorized != 'admin') {
			return false;
		}
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$this->group->get('description').': '.JText::_('PLG_GROUPS_MESSAGES_SEND') );
		
		// Instantiate a vew
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'groups',
				'element'=>'messages',
				'name'=>'create'
			)
		);
		
		// Pass the view some info
		$view->option = $this->_option;
		$view->group = $this->group;
		$view->authorized = $this->authorized;
		$view->users = JRequest::getVar( 'users', array('all') );
		$view->no_html = JRequest::getInt( 'no_html', 0 );
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Return the output
		return $view->loadTemplate();
	}
	
	//-----------
	
	protected function _send()
	{
		// Ensure the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Incoming array of users to message
		$mbrs = JRequest::getVar( 'users', array(0), 'post' );
		switch ($mbrs[0]) 
		{
			case 'invitees':
				$mbrs = $this->group->get('invitees');
				$action = 'group_invitees_message';
				$group_id = $this->group->get('gidNumber');
			break;
			case 'pending':
				$mbrs = $this->group->get('pending');
				$action = 'group_pending_message';
				$group_id = $this->group->get('gidNumber');
			break;
			case 'managers':
				$mbrs = $this->group->get('managers');
				$action = 'group_managers_message';
				$group_id = $this->group->get('gidNumber');
			break;
			case 'all':
				$mbrs = $this->group->get('members');
				$action = 'group_members_message';
				$group_id = $this->group->get('gidNumber');
			break;
			default:
				$action = '';
				$group_id = 0;
			break;
		}
		
		// Incoming message and subject
		$subject = JRequest::getVar( 'subject', JText::_('PLG_GROUPS_MESSAGES_SUBJECT') );
		$message = JRequest::getVar( 'message', '' );
		
		// Ensure we have a message
		if (!$subject || !$message) {
			return false;
		}
		
		// Add a link to the group page to the bottom of the message
		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option='.$this->_option.'&gid='. $this->group->get('cn'));
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		$message .= "\r\n". $juri->base().$sef . "\r\n";
		
		// Build the "from" data for the e-mail
		$from = array();
		$from['name']  = $juser->get('name').' ('.JText::_(strtoupper($this->_name)).': '.$this->group->get('cn').')';
		$from['email'] = $juser->get('email');
		
		// Send the message
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( 'group_message', $subject, $message, $from, $mbrs, $this->_option, null, '', $group_id ))) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MEMBERS_FAILED') );
		}

		// Log the action
		if ($action) {
			$database =& JFactory::getDBO();
			$log = new XGroupLog( $database );
			$log->gid = $this->group->get('gidNumber');
			$log->timestamp = date( 'Y-m-d H:i:s', time() );
			$log->action = $action;
			$log->actorid = $juser->get('id');
			if (!$log->store()) {
				$this->setError( $log->getError() );
			}
		}
		
		// Determine if we're returning HTML or not
		// (if no - this is an AJAX call)
		$no_html = JRequest::getInt( 'no_html', 0 );
		if (!$no_html) {
			$html = '';
			if ($this->getError()) {
				$html .= '<p class="error">'. $this->getError() .'</p>';
			}
			$html .= $this->_sent();

			return $html;
		}
	}
}
