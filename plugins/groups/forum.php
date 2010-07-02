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
	
	public function &onGroupAreas( $authorized )
	{
		$areas = array(
			'forum' => JText::_('PLG_GROUPS_FORUM')
		);
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
		
		// Are we on the overview page?
		if ($areas[0] == 'overview') {
			$return = 'metadata';
		}
		
		// The output array we're returning
		$arr = array(
			'html'=>'',
			'metadata'=>'',
			'dashboard'=>''
		);

		// Do we need to return any data?
		if ($return != 'html' && $return != 'metadata') {
			return $arr;
		}
		
		//ximport('xforum');
		include_once(JPATH_ROOT.DS.'plugins'.DS.'groups'.DS.'forum'.DS.'forum.helper.php');
		include_once(JPATH_ROOT.DS.'plugins'.DS.'groups'.DS.'forum'.DS.'forum.class.php');
		
		$this->group = $group;
		$this->option = $option;
		$this->name = substr($option,4,strlen($option));
		$this->limitstart = $limitstart;
		$this->limit = $limit;
		$this->authorized = $authorized;
		
		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html') {
			// Set the page title
			$document =& JFactory::getDocument();
			$document->setTitle( JText::_(strtoupper($this->name)).': '.$this->group->get('description').': '.JText::_('PLG_GROUPS_FORUM') );
			
			if ($this->getError()) {
				$arr['html'] .= $this->getError();
			}
			
			if (!$action) {
				$t = JRequest::getInt( 'topic', 0 );
				if ($t) {
					$action = 'topic';
				}
			}
			
			ximport('xdocument');
			XDocument::addPluginStylesheet('groups', 'forum');
			
			switch ($action) 
			{
				case 'newtopic':    $arr['html'] .= $this->edittopic();   break;
				case 'savetopic':   $arr['html'] .= $this->savetopic();   break;
				case 'deletetopic': $arr['html'] .= $this->deletetopic(); break;
				case 'edittopic':   $arr['html'] .= $this->edittopic();   break;
				case 'reply':       $arr['html'] .= $this->reply();       break;
				case 'savereply':   $arr['html'] .= $this->savereply();   break;
				case 'deletereply': $arr['html'] .= $this->deletereply(); break;
				case 'topic':       $arr['html'] .= $this->topic();       break;
				case 'topics':      $arr['html'] .= $this->topics();      break;
				
				default: $arr['html'] .= $this->topics(); break;
			}
		} else {
			$database =& JFactory::getDBO();
			
			/*
			// Get a count of the number of topics
			$forum = new XForum( $database );
			$num = $forum->getCount();

			// Build the HTML meant for the "profile" tab's metadata overview
			$metadata = '<p class="discussions"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'active=forum').'">'.JText::sprintf('NUMBER_DISCUSSIONS',$num).'</a></p>'.n;
			*/

			$tables = $database->getTableList();
			$table = $database->_table_prefix.'xforum';
			if (!in_array($table,$tables)) {
				$database->setQuery( "CREATE TABLE `#__xforum` (
				  `id` int(11) NOT NULL auto_increment,
				  `topic` varchar(255) default NULL,
				  `comment` text,
				  `created` datetime NOT NULL default '0000-00-00 00:00:00',
				  `created_by` int(11) default '0',
				  `state` tinyint(3) NOT NULL default '0',
				  `sticky` tinyint(2) NOT NULL default '0',
				  `parent` int(11) NOT NULL default '0',
				  `hits` int(11) default '0',
				  `group` int(11) default '0',
				  `access` tinyint(2) default '4',
				  PRIMARY KEY  (`id`),
				  FULLTEXT KEY `question` (`comment`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;" );
				if (!$database->query()) {
					echo $database->getErrorMsg();
					return false;
				}
			}

			// Incoming
			$filters = array();
			$filters['authorized'] = $this->authorized;
			$filters['limit'] = $this->limit;
			$filters['start'] = 0;
			$filters['group'] = $this->group->get('gidNumber');
			$filters['sticky'] = false;

			// Initiate a forum object
			$forum = new XForum( $database );

			$num = $forum->getCount( $filters );
			
			// Get records
			$rows = $forum->getRecords( $filters );

			// Output HTML
			$arr['metadata'] = '<a href="'.JRoute::_('index.php?option='.$option.'&gid='.$group->get('cn').'&active=forum').'">'.JText::sprintf('PLG_GROUPS_FORUM_NUMBER_DISCUSSIONS',$num).'</a>';
			//$arr['dashboard'] = $this->topicsHtml( $this->group, $forum, 0, $rows, null, $this->authorized, '', $this->getError() );
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
			$view->option = $this->option;
			$view->group = $this->group;
			$view->authorized = $this->authorized;
			$view->total = 0;
			$view->rows = $rows;
			$view->search = '';
			$view->pageNav = null;
			$view->forum = $forum;
			$view->limit = $filters['limit'];
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}

			// Return the output
			$arr['dashboard'] = $view->loadTemplate();
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
		$view->option = $this->option;
		$view->group = $this->group;
		$view->authorized = $this->authorized;
		$view->total = $total;
		$view->rows = $rows;
		$view->search = $filters['search'];
		$view->pageNav = $pageNav;
		$view->forum = $forum;
		$view->limit = $filters['limit'];
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
		$view->option = $this->option;
		$view->group = $this->group;
		$view->authorized = $this->authorized;
		$view->rows = $rows;
		$view->forum = $forum;
		$view->pageNav = $pageNav;
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
			$this->setError( $forum->getError() );
			return $this->topics();
		}

		// Delete the topic itself
		if (!$forum->delete( $id )) {
			$this->setError( $forum->getError() );
		}
		
		// Return the topics list
		return $this->topics();
	}
	
	//-----------
	
	protected function edittopic()
	{
		// Is the user logged in?
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('GROUPS_LOGIN_NOTICE') );
			return $this->topics();
		}

		// Incoming
		$id = JRequest::getInt( 'topic', 0 );

		$database =& JFactory::getDBO();
		
		$row = new XForum( $database );
		$row->load( $id );
		if (!$id) {
			// New review, get the user's ID
			$row->created_by = $juser->get('id');
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
	
	//-----------
	
	protected function reply()
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
	
	//-----------
	
	protected function savetopic() 
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('GROUPS_LOGIN_NOTICE') );
			return;
		}
		
		$database =& JFactory::getDBO();
		
		$incoming = JRequest::getVar('topic',array(),'post');
		
		$row = new XForum( $database );
		if (!$row->bind( $incoming )) {
			$this->setError( $row->getError() );
			exit();
		}
		
		if (!$row->id) {
			$row->created = date( 'Y-m-d H:i:s', time() );  // use gmdate() ?
			$row->created_by = $juser->get('id');
		} else {
			$row->modified = date( 'Y-m-d H:i:s', time() );  // use gmdate() ?
			$row->modified_by = $juser->get('id');
		}
		
		if (trim($row->topic) == '') {
			$row->topic = substr($row->comment, 0, 70);
			if (strlen($row->topic >= 70)) {
				$row->topic .= '...';
			}
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

		if ($row->parent) {
			return $this->topic($row->parent);
		} else {
			return $this->topics();
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
}