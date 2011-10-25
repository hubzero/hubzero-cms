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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Plugin');
include_once(JPATH_ROOT.DS.'libraries'.DS.'Hubzero'.DS.'Emailtoken.php');
JPlugin::loadLanguage( 'plg_groups_forum' );

//-----------

class plgGroupsForum extends Hubzero_Plugin
{
	public function plgGroupsForum(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'groups', 'forum' );
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
			'name' => 'forum',
			'title' => JText::_('PLG_GROUPS_FORUM'),
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
		$active = 'forum';
		$active_real = 'discussion';

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

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html') {

			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//Create user object
			$juser =& JFactory::getUser();

			//get the group members
			$members = $group->get('members');

			//if set to nobody make sure cant access
			if($group_plugin_acl == 'nobody') {
				$arr['html'] = "<p class=\"info\">".JText::sprintf('GROUPS_PLUGIN_OFF', ucfirst($active_real))."</p>";
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if ($juser->get('guest') && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members')) {
				ximport('Hubzero_Module_Helper');
				$arr['html']  = "<p class=\"warning\">".JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active_real))."</p>";
				$arr['html'] .= Hubzero_Module_Helper::renderModules('force_mod');
				return $arr;
			}

			//check to see if user is member and plugin access requires members
			if(!in_array($juser->get('id'),$members) && $group_plugin_acl == 'members' && $authorized != 'admin') {
				$arr['html'] = "<p class=\"info\">".JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active_real))."</p>";
				return $arr;
			}

			//user vars
			$this->juser = $juser;
			$this->authorized = $authorized;

			//group vars
			$this->group = $group;
			$this->members = $members;

			//option and paging vars
			$this->option = $option;
			$this->name = substr($option,4,strlen($option));
			$this->limitstart = $limitstart;
			$this->limit = $limit;

			//if we dont have an action check if were trying to view the topic
			if (!$action) {
				$t = JRequest::getInt( 'topic', 0 );
				if ($t) {
					$action = 'topic';
				}
			}

			//push the stylesheet to the view
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('groups', 'forum');

			//include 
			include_once(JPATH_ROOT.DS.'plugins'.DS.'groups'.DS.'forum'.DS.'forum.helper.php');
			include_once(JPATH_ROOT.DS.'plugins'.DS.'groups'.DS.'forum'.DS.'forum.class.php');

			switch ($action)
			{
				case 'newtopic':    $arr['html'] .= $this->edittopic();   break;
				case 'edittopic':   $arr['html'] .= $this->edittopic();   break;
				case 'savetopic':   $arr['html'] .= $this->savetopic();   break;
				case 'deletetopic': $arr['html'] .= $this->deletetopic(); break;

				//case 'reply':       $arr['html'] .= $this->reply();       break;
				//case 'savereply':   $arr['html'] .= $this->savereply();   break;
				//case 'deletereply': $arr['html'] .= $this->deletereply(); break;
				case 'topic':       $arr['html'] .= $this->topic();       break;
				case 'topics':      $arr['html'] .= $this->topics();      break;

				default: 			$arr['html'] .= $this->topics(); 	  break;
			}
		}

		// Return the output
		return $arr;
	}

	/**
	 * Short description for 'topics'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
	protected function topics()
	{
		// Incoming
		$filters = array();
		$filters['authorized'] = $this->authorized;
		$filters['limit'] = $this->limit;
		$filters['start'] = $this->limitstart;
		$filters['group'] = $this->group->get('gidNumber');
		$filters['search'] = JRequest::getVar('q', '');

		// Initiate a forum object
		$database =& JFactory::getDBO();
		$forum = new XForum( $database );

		// Get record count
		$total = $forum->getCount( $filters );

		// Get records
		$rows = $forum->getRecords( $filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Instantiate a vew
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'groups',
				'element'=>'forum',
				'name'=>'browse'
			)
		);

		// Pass the view some info
		$view->juser = $this->juser;
		$view->authorized = $this->authorized;

		$view->group = $this->group;
		$view->members = $this->members;

		$view->forum = $forum;
		$view->rows = $rows;

		$view->total = $total;
		$view->search = $filters['search'];
		$view->limit = $filters['limit'];
		$view->pageNav = $pageNav;
		$view->option = $this->option;
		
		$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Short description for 'topic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $id Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	protected function topic($id=0)
	{
		// Incoming
		$filters = array();
		$filters['authorized'] = $this->authorized;
		$filters['limit']  = $this->limit;
		$filters['start']  = $this->limitstart;
		$filters['parent'] = ($id) ? $id : JRequest::getInt( 'topic', 0 );

		if ($filters['parent'] == 0) {
			return $this->topics();
		}

		// Initiate a forum object
		$database =& JFactory::getDBO();
		$forum = new XForum( $database );

		// Load the topic
		$forum->load( $filters['parent'] );

		if ($forum->access == 4 && !$this->authorized) {
			return $this->topics();
		}

		// Get reply count
		$total = $forum->getCount( $filters );

		// Get replies
		$rows = $forum->getRecords( $filters );

		// Record the hit
		$forum->hit();

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Instantiate a vew
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'groups',
				'element'=>'forum',
				'name'=>'topic'
			)
		);

		// Pass the view some info
		$view->juser = $this->juser;
		$view->authorized = $this->authorized;

		$view->group = $this->group;
		$view->members = $this->members;

		$view->rows = $rows;
		$view->forum = $forum;

		$view->pageNav = $pageNav;
		$view->option = $this->option;
		$view->limit = $filters['limit'];
		$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Short description for 'deletetopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function deletetopic()
	{
		// Is the user logged in?
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('GROUPS_LOGIN_NOTICE') );
			return $this->topics();
		}

		if ($this->authorized != 'manager' && $this->authorized != 'admin') {
			return $this->topics();
		}

		// Incoming
		$id = JRequest::getInt( 'topic', 0 );
		if (!$id) {
			return $this->topics();
		}

		// Initiate a forum object
		$database =& JFactory::getDBO();
		$forum = new XForum( $database );

		$forum->load($id);
		$hasParent = $forum->parent;
		
		// Delete all replies on a topic
		if (!$forum->deleteReplies( $id )) {
			$this->addPluginMessage( $forum->getError(),'error' );
			$this->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum') );
		}

		// Delete the topic itself
		if (!$forum->delete( $id )) {
			$this->addPluginMessage( $forum->getError(),'error' );
			$this->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum') );
		}

		// Return the topics list
		if($hasParent) {
			$this->addPluginMessage( 'The topic was successfully deleted.','passed' );
			$this->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=topic&topic='.$hasParent) );
		} else {
			$this->addPluginMessage( 'The topic was successfully deleted.','passed' );
			$this->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum') );
		}
	}

	/**
	 * Short description for 'edittopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
	protected function edittopic()
	{
		//check to make sure editor is a member
		if(!in_array($this->juser->get('id'),$this->members) && $this->authorized != 'admin') {
			// Return the topics list
			$this->addPluginMessage( JText::sprintf('PLG_GROUPS_FORUM_MUST_MEMBER', 'create/edit a topic'),'warning' );
			$this->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum') );
		}

		// get the passed in topic
		$id = JRequest::getInt( 'topic', 0 );

		//instantiate db object
		$database =& JFactory::getDBO();

		//instantiate forum object
		$row = new XForum( $database );

		//load the forum
		$row->load( $id );

		//are we in edit mode
		if (!$id) {
			// New review, get the user's ID
			$row->created_by = $this->juser->get('id');
		} else {
			// Editing a review, do some prep work
			$row->comment = str_replace('<br />','',$row->comment);
		}

		// Instantiate a vew
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'groups',
				'element'=>'forum',
				'name'=>'reply'
			)
		);

		// Pass the view some info
		$view->juser = $this->juser;
		$view->authorized = $this->authorized;

		$view->group = $this->group;
		$view->members = $this->members;

		$view->row = $row;

		$view->option = $this->option;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Short description for 'savetopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function savetopic()
	{
		//check to make sure editor is a member
		if(!in_array($this->juser->get('id'),$this->members) && $this->authorized != 'admin') {
			// Return the topics list
			$this->addPluginMessage( JText::sprintf('PLG_GROUPS_FORUM_MUST_MEMBER', 'create/edit a topic or reply'),'warning' );
			$this->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum') );
		}

		//instantaite database object
		$database =& JFactory::getDBO();

		//get the incoming topic details
		$incoming = JRequest::getVar('topic',array(),'post');

		//instantiate forum object
		/* @var $row XForum */
		$row = new XForum( $database );

		/* @var $group Hubzero_Group */
		$group = $this->group;
		
		//bind the data
		if (!$row->bind( $incoming )) {
			$this->setError( $row->getError() );
			exit();
		}

		//if we are modifying or creating
		if (!$row->id) {
			$row->created = date( 'Y-m-d H:i:s', time() );  // use gmdate() ?
			$row->created_by = $this->juser->get('id');
		} else {
			$row->modified = date( 'Y-m-d H:i:s', time() );  // use gmdate() ?
			$row->modified_by = $this->juser->get('id');
		}

		//create topic from comment if not one entered
		if (trim($row->topic) == '') {
			$row->topic = substr($row->comment, 0, 70);
			if (strlen($row->topic >= 70)) {
				$row->topic .= '...';
			}
		}

		//is this a sticky topic
		if (!isset($incoming['sticky'])) {
			$row->sticky = 0;
		}

		//is this an anonymous topic
		if (!isset($incoming['anonymous'])) {
			$row->anonymous = 0;
		}

		//is this a public topic
		if (!isset($incoming['access'])) {
			$row->access = 4;
		} else {
			$row->access = 0;
		}

   		// Check content
		if (!$row->check()) {
			$this->setError( $row->getError() );
			return $this->edittopic();
		}

		// Store new content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return $this->edittopic();
		}

		// Build outgoing email message
        $originalMessage = $row->comment;
		$originalMessage .= "\n\n%%tokenplaceholder%%\n";
        $originalMessage .= "NOTE: The above line is required in any email reply to this discussion. \nOnly text before this section will be added to the discussion\n";
        $originalMessage .= "When you reply you might want to remove the previous message text if it is included in the reply\n\n\n";

		// Translate the message wiki formatting to html
		/*
		ximport('Hubzero_Wiki_Parser');

		$p =& Hubzero_Wiki_Parser::getInstance();
		
		$wikiconfig = array(
			'option'   => $this->option,
			'scope'    => 'group'.DS.'forum',
			'pagename' => 'group',
			'pageid'   => $this->group->get('gidNumber'),
			'filepath' => '',
			'domain'   => ''
		);
		
		$originalMessage = $p->parse( "\n".stripslashes($originalMessage), $wikiconfig );		
		*/
		
        $encryptor = new Hubzero_Email_Token();

		// Figure out who should be notified about this comment (all group members for now)
        $members = $this->group->get('members');
		$userIDsToEmail = array();
		
		foreach($members as $mbr) 
        {
			//Look up user info 
			$user = new JUser();
				
			if($user->load($mbr)){
				
				include_once(JPATH_ROOT.DS.'plugins'.DS.'groups'.DS.'memberoptions'.DS.'memberoption.class.php');
				
				// Find the user's group settings, do they want to get email (0 or 1)?
				$groupMemberOption = new XGroups_MemberOption($database);
				$groupMemberOption->loadRecord($group->get('gidNumber'), $user->id, GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION);

				if($groupMemberOption->id)
					$sendEmail = $groupMemberOption->optionvalue;
				else
					$sendEmail = 0;
				
				if($sendEmail)
					$userIDsToEmail[] = $user->id;	
			}
		}
		
        JPluginHelper::importPlugin( 'xmessage' );
        $dispatcher =& JDispatcher::getInstance();

		// Email each group member separately, each needs a user specific token
		foreach($userIDsToEmail as $userID) 
		{

			// Construct User specific Email ThreadToken
            // Version, type, userid, xforumid
            // Note, for original posts, $row->parent will be 0, so we take the id instead
            $token = $encryptor->buildEmailToken(1, 2, $user->id, $row->id);

			// Put Token into generic message
			$subject = $group->get('cn') . ' group discussion post (' . $row->id . ')';
            
			$message = str_replace('%%tokenplaceholder%%', $token, $originalMessage);
			
            $jconfig =& JFactory::getConfig();
			$from = array();
			$from['name']  = $jconfig->getValue('config.sitename').' ';
			$from['email'] = $jconfig->getValue('config.mailfrom');
		
	        if (!$dispatcher->trigger( 'onSendMessage', array( 'group_message', $subject, $message, $from, array($userID), $this->_option, null, '', $this->group->get('gidNumber') ))) {
	            $this->setError( JText::_('GROUPS_ERROR_EMAIL_MEMBERS_FAILED') );
	        }

		}

		//print_r($usersToEmail);
		//return;
		//exit;

		

		//if we are replying redirect back to that topic
		if ($row->parent) {
			$this->addPluginMessage( 'You have successfully commented on the topic.','passed' );
			$this->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=topic&topic='.$row->parent) );
		} else {
			$this->addPluginMessage( 'You have successfully added a topic.','passed' );
			$this->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum') );
		}
	}

	/**
	 * Short description for 'onGroupDelete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $group Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function onGroupDelete( $group )
	{
		//ximport('xforum');
		include_once(JPATH_ROOT.DS.'plugins'.DS.'groups'.DS.'forum'.DS.'forum.class.php');
		$database =& JFactory::getDBO();

		$results = $this->getForumIDs( $group->get('cn') );

		$log = JText::_('PLG_GROUPS_FORUM').': ';
		if ($results && count($results) > 0) {
			// Initiate a forum object
			$forum = new XForum( $database );

			foreach ($results as $result)
			{
				$forum->deleteReplies( $result->id );
				$forum->delete( $result->id );

				$log .= $result->id.' '."\n";
			}
		} else {
			$log .= JText::_('PLG_GROUPS_FORUM_NO_RESULTS')."\n";
		}

		return $log;
	}

	/**
	 * Short description for 'onGroupDeleteCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $group Parameter description (if any) ...
	 * @return     void
	 */
	public function onGroupDeleteCount( $group )
	{
	}

	/**
	 * Short description for 'getForumIDs'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $gid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getForumIDs( $gid=NULL )
	{
		if (!$gid) {
			return array();
		}
		$database =& JFactory::getDBO();

		// Initiate a forum object
		$forum = new XForum( $database );

		// Get records
		$filters = array();
		$filters['start'] = 0;
		$filters['group'] = $gid;

		return $forum->getRecords( $filters );
	}

	/**
	 * Short description for '_reply'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
	protected function _reply()
	{
		// Is the user logged in?
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('GROUPS_LOGIN_NOTICE') );
			return $this->topics();
		}

		// Incoming
		$parent = JRequest::getInt( 'topic', 0 );
		if (!$parent) {
			$this->setError( JText::_('PLG_GROUPS_FORUM_MISSING_TOPIC') );
			return $this->topics();
		}

		$database =& JFactory::getDBO();

		$row = new XForum( $database );
		$row->load();
		$row->created_by = $juser->get('id');
		$row->parent = $parent;

		// Instantiate a vew
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'groups',
				'element'=>'forum',
				'name'=>'reply'
			)
		);

		// Pass the view some info
		$view->option = $this->option;
		$view->group = $this->group;
		$view->row = $row;
		$view->authorized = $this->authorized;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Return the output
		return $view->loadTemplate();
	}
}
