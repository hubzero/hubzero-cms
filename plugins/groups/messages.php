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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_groups_messages' );

/**
 * Short description for 'plgGroupsMessages'
 * 
 * Long description (if any) ...
 */
class plgGroupsMessages extends JPlugin
{

	/**
	 * Short description for 'plgGroupsMessages'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgGroupsMessages(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'groups', 'messages' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	/**
	 * Short description for 'onGroupAreas'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name' => 'messages',
			'title' => JText::_('PLG_GROUPS_MESSAGES'),
			'default_access' => $this->_params->get('plugin_access','members')
		);

		return $area;
	}

	/**
	 * Short description for 'onGroup'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $group Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      string $authorized Parameter description (if any) ...
	 * @param      integer $limit Parameter description (if any) ...
	 * @param      integer $limitstart Parameter description (if any) ...
	 * @param      string $action Parameter description (if any) ...
	 * @param      array $access Parameter description (if any) ...
	 * @param      unknown $areas Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function onGroup( $group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null )
	{
		$return = 'html';
		$active = 'messages';

		// The output array we're returning
		$arr = array(
			'html'=>''
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if(!in_array($this_area['name'],$areas)) {
				return;
			}
		}

		// Are we returning HTML?
		if ($return == 'html') {

			//get group members plugin access level
			$group_plugin_acl = $access[$active];

			//Create user object
			$juser =& JFactory::getUser();

			//get the group members
			$members = $group->get('members');

			// Set some variables so other functions have access
			$this->juser = $juser;
			$this->authorized = $authorized;
			$this->members = $members;
			$this->group = $group;
			$this->_option = $option;
			$this->action = $action;

			//if set to nobody make sure cant access
			if($group_plugin_acl == 'nobody') {
				$arr['html'] = "<p class=\"info\">".JText::sprintf('GROUPS_PLUGIN_OFF', ucfirst($active))."</p>";
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if ($juser->get('guest') && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members')) {
				ximport('Hubzero_Module_Helper');
				$arr['html']  = "<p class=\"warning\">".JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active))."</p>";
				$arr['html'] .= Hubzero_Module_Helper::renderModules('force_mod');
				return $arr;
			}

			//check to see if user is member and plugin access requires members
			if(!in_array($juser->get('id'),$members) && $group_plugin_acl == 'members' && $authorized != 'admin') {
				$arr['html'] = "<p class=\"info\">".JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active))."</p>";
				return $arr;
			}

			//push styles to the view
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('groups','messages');

			// Load some needed libraries
			ximport('Hubzero_Message');

			$task = strtolower(trim($action));

			switch ($task)
			{
				case 'send': 		$arr['html'] = $this->_send();     break;
				case 'new':  		$arr['html'] = $this->_create();   break;
				case 'viewmessage': $arr['html'] = $this->_view(); 	   break;
				case 'sent':
				default:     		$arr['html'] = $this->_sent();     break;
			}

		}

		// Return data
		return $arr;
	}

	//---------------------------------
	// Views
	//---------------------------------

	/**
	 * Short description for '_sent'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
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

	/**
	 * Short description for '_view'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
	protected function _view()
	{
		//get the message id
		$message = JRequest::getVar('msg','','get');

		//if there is no message id show all sent messages
		if(!$message) {
			return $this->_sent();
		}

		//insantiate db
		$database =& JFactory::getDBO();

		// Load the message and parse it
		$xmessage = new Hubzero_Message_Message( $database );
		$xmessage->load( $message );
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

	/**
	 * Short description for '_create'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
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

		//get all member roles
		$db =& JFactory::getDBO();
		$sql = "SELECT * FROM #__xgroups_roles WHERE gidNumber='".$this->group->get('gidNumber')."'";
		$db->setQuery($sql);
		$member_roles = $db->loadAssocList();

		//get all group members
		$members = $this->group->get('members');

		// Pass the view some info
		$view->option = $this->_option;
		$view->group = $this->group;
		$view->authorized = $this->authorized;

		$view->member_roles = $member_roles;
		$view->members = $members;
		$view->users = JRequest::getVar( 'users', array('all') );
		$view->no_html = JRequest::getInt( 'no_html', 0 );
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Short description for '_send'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
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
			case 'applicants':
				$mbrs = $this->group->get('applicants');
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
				foreach($mbrs as $mbr) {
					if(strstr($mbr,'_')) {
						$role = explode('_',$mbr);
						$db =& JFactory::getDBO();
						$sql = "SELECT uidNumber FROM #__xgroups_member_roles WHERE role='".$role[1]."'";
						$db->setQuery($sql);
						$member_roles = $db->loadAssocList();
						foreach($member_roles as $member) {
							$members[] = $member['uidNumber'];
						}
						$mbrs = $members;
						$action = 'group_role_message';
						$group_id = $this->group->get('gidNumber');
					} else {
						$action = '';
						$group_id = 0;
						break;
					}
				}
			break;
		}

		// Incoming message and subject
		$subject = JRequest::getVar( 'subject', JText::_('PLG_GROUPS_MESSAGES_SUBJECT') );
		$message = JRequest::getVar( 'message', '' );

		// Ensure we have a message
		if (!$subject || !$message) {
			$html  = "<p class=\"error\">You must enter all required fields</p>";
			$html .= $this->_create();
			return $html;
		}

		// Add a link to the group page to the bottom of the message
		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option='.$this->_option.'&gid='. $this->group->get('cn'));
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		$message .= "\r\n\r\n------------------------------------------------\r\n". $juri->base().$sef . "\r\n";

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

	//---------------------------------
	// Functions 
	//---------------------------------

	/**
	 * Short description for 'autolink'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $matches Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
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

	/**
	 * Short description for 'obfuscate'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $email Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

}

