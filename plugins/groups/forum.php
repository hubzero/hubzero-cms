<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_groups_forum' );

//-----------

class plgGroupsForum extends JPlugin
{
	public function plgGroupsForum(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'groups', 'forum' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onGroupAreas()
	{
		$area = array(
			'name' => 'forum',
			'title' => JText::_('PLG_GROUPS_FORUM'),
			'default_access' => $this->_params->get('plugin_access','members')
		);
		
		return $area;
	}

	//-----------

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
	
	//-----------
	
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
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Return the output
		return $view->loadTemplate();
	}
	
	//-----------
	
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
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Return the output
		return $view->loadTemplate();
	}
	
	//-----------
	
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
		
		// Delete all replies on a topic
		if (!$forum->deleteReplies( $id )) {
			$app->enqueueMessage($forum->getError(),'error');
			$app->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum') );
		}

		// Delete the topic itself
		if (!$forum->delete( $id )) {
			$app->enqueueMessage($forum->getError(),'error');
			$app->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum') );
		}
		
		// Return the topics list
		$app =& JFactory::getApplication();
		$app->enqueueMessage('The topic was successfully deleted.','passed');
		$app->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum') );
	}
	
	//-----------
	
	protected function edittopic()
	{
		//check to make sure editor is a member
		if(!in_array($this->juser->get('id'),$this->members) && $this->authorized != 'admin') {
			// Return the topics list
			$app =& JFactory::getApplication();
			$app->enqueueMessage( JText::sprintf('PLG_GROUPS_FORUM_MUST_MEMBER', 'create/edit a topic'),'warning');
			$app->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum') );
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
	
	//-----------
	
	protected function savetopic() 
	{
		//check to make sure editor is a member
		if(!in_array($this->juser->get('id'),$this->members) && $this->authorized != 'admin') {
			// Return the topics list
			$app =& JFactory::getApplication();
			$app->enqueueMessage( JText::sprintf('PLG_GROUPS_FORUM_MUST_MEMBER', 'create/edit a topic or reply'),'warning');
			$app->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum') );
		}
		
		//instantaite database object
		$database =& JFactory::getDBO();
		
		//get the incoming topic details
		$incoming = JRequest::getVar('topic',array(),'post');
		
		//instantiate forum object
		$row = new XForum( $database );

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
		
		//if we are replying redirect back to that topic
		$app =& JFactory::getApplication();
		if ($row->parent) {
			$app->enqueueMessage('You have successfully commented on the topic.','passed');
			$app->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=topic&topic='.$row->parent) );
		} else {
			$app->enqueueMessage('You have successfully added a topic.','passed');
			$app->redirect( JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum') );
		}
	}

	//-----------
	
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
	
	//-----------
	
	public function onGroupDeleteCount( $group ) 
	{
	}
	
	//-----------
	
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

	
	
	//-------
	
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
