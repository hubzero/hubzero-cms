<?php
/**
 * @package		HUBzero                                  CMS
 * @author		Shawn                                     Rice <zooley@purdue.edu>
 * @copyright	Copyright                               2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *                                                    
 *                                                    Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 *                                                    All rights reserved.
 *                                                    
 *                                                    This program is free software; you can redistribute it and/or
 *                                                    modify it under the terms of the GNU General Public License,
 *                                                    version 2 as published by the Free Software Foundation.
 *                                                    
 *                                                    This program is distributed in the hope that it will be useful,
 *                                                    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                                                    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                                                    GNU General Public License for more details.
 *                                                    
 *                                                    You should have received a copy of the GNU General Public License
 *                                                    along with this program; if not, write to the Free Software
 *                                                    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

/**
 * Short description for 'ForumController'
 * 
 * Long description (if any) ...
 */
class ForumController extends Hubzero_Controller
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

		$this->_task = JRequest::getVar('task','');
		$this->_topic = JRequest::getVar('topic','');

		//$this->limit = JRequest::getVar('limit');

		switch($this->_task)
		{
			case 'addtopic':		$this->addTopic();		break;
			case 'edittopic':		$this->editTopic();		break;
			case 'savetopic':		$this->saveTopic();		break;
			case 'deletetopic':		$this->deleteTopic();	break;
			case 'topic':			$this->topic();			break;
			default:				$this->topics();		break;
		}
	}

	/**
	 * Short description for 'setNotification'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $message Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @return     void
	 */
	function setNotification( $message, $type )
	{
		//get the app
		$app =& JFactory::getApplication();

		//if type is not set, set to error message
		$type = ($type == '') ? 'error' : $type;

		//if message is set push to notifications
		if($message != '') {
			$app->enqueueMessage($message, $type);
		}
	}

	/**
	 * Short description for 'getNotifications'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	function getNotifications()
	{
		//get the app
		$app =& JFactory::getApplication();

		//getmessages in quene 
		$messages = $app->getMessageQueue();

		//if we have any messages return them
		if($messages) {
			return $messages;
		}
	}

	/**
	 * Short description for '_authorize'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	function _authorize()
	{
		$status = "";

		$juser =& JFactory::getUser();
		if(!$juser->get('guest')) {
			$status = "registered";
		}

		if($this->juser->authorize($this->_option, 'manage')) {
			$status = "admin";
		}

		return $status;
	}

	/////////////////////////////////////////////////
	// Views                                      //
	////////////////////////////////////////////////

	/**
	 * Short description for 'topics'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	private function topics()
	{
		$title = "Discussion Forum";

		$option = substr($this->_option,4);
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_($title), 'index.php?option='.$this->_option);
		}

		// Incoming
		$filters = array();
		$filters['authorized'] = 1;
		$filters['limit'] = JRequest::getVar('limit', 10);
		$filters['start'] = JRequest::getVar('limitstart', 0);
		$filters['group'] = "-1";
		$filters['search'] = JRequest::getVar('q', '');

		// Initiate a forum object
		$database =& JFactory::getDBO();
		$forum = new XForum( $database );

		// Get record count
		$total = $forum->getCount( $filters );

		// Get records
		$rows = $forum->getRecords( $filters );

		//get the styles
		$this->_getStyles();

		//get authorization
		$authorized = $this->_authorize();

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		$view = new JView( array('name'=>'topics') );
		$view->title = $title;
		$view->option = $this->_option;
		$view->authorized = $authorized;

		$view->total = $total;
		$view->rows = $rows;
		$view->search = $filters['search'];
		$view->pageNav = $pageNav;
		$view->forum = $forum;
		$view->start = $filters['start'];
		$view->limit = $filters['limit'];
		$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$view->display();
	}

	/**
	 * Short description for 'topic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function topic()
	{
		$title = "Discussion Forum";

		$topicID = JRequest::getVar('topic','');

		// Incoming
		$filters = array();
		//$filters['authorized'] = $this->authorized;
		$filters['limit']  = $this->limit;
		$filters['start']  = $this->limitstart;
		$filters['parent'] = $topicID;

		if ($filters['parent'] == 0) {
			return $this->topics();
		}

		// Initiate a forum object
		$database =& JFactory::getDBO();
		$forum = new XForum( $database );

		// Load the topic
		$forum->load( $filters['parent'] );

		// Get reply count
		$total = $forum->getCount( $filters );

		// Get replies
		$rows = $forum->getRecords( $filters );

		// Record the hit
		$forum->hit();

		//get status
		$authorized = $this->_authorize();

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		$this->_getStyles();

		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_($title), 'index.php?option='.$this->_option);
			$pathway->addItem(JText::_($forum->topic), 'index.php?option='.$this->_option.'&task=topic&topic='.$forum->id);
		}

		$view = new JView( array('name'=>'reply') );
		//$view->title = $title;
		$view->option = $this->_option;
		$view->authorized = $authorized;

		$view->rows = $rows;
		$view->forum = $forum;
		$view->pageNav = $pageNav;
		$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$view->display();
	}

	/**
	 * Short description for 'addTopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	private function addTopic()
	{
		$this->editTopic();
	}

	/**
	 * Short description for 'editTopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function editTopic()
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setNotification( JText::_('FORUM_LOGIN_NOTICE'),'warning' );
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

		$this->_getStyles();

		$view = new JView( array('name'=>'topic') );
		//$view->title = $title;
		$view->option = $this->_option;
		$view->authorized = $this->authorized;
		$view->group = $this->group;
		$view->row = $row;
		$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$view->display();
	}

	/**
	 * Short description for 'saveTopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function saveTopic()
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		$topic = JRequest::getVar('topic',array());

		$editing = JRequest::getVar('editing','','post');

		$row = new XForum( $database );
		if (!$row->bind( $topic )) {
			$this->setNotification( $row->getError(), 'error' );
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

		if (!isset($topic['sticky'])) {
			$row->sticky = 0;
		}

		if (!isset($topic['anonymous'])) {
			$row->anonymous = 0;
		}

		$row->group = "-1";

		// Check content
		if (!$row->check()) {
			$this->setNotification( $row->getError(), 'error' );
			if($row->parent) {
				$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=topic&topic='.$row->parent."#commentform");
				return;
			} else {
				return $this->editTopic();
			}
		}

		// Store new content
		if (!$row->store()) {
			$this->setNotification( $row->getError(), 'error' );
			return $this->topic();
		}

		if ($row->parent) {
			$text = ($editing) ? JText::_('FORUM_COMMENT_EDITED') : JText::_('FORUM_COMMENT_ADDED');
			$this->setNotification($text, 'passed');
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=topic&topic='.$row->parent.'#c'.$row->id);
		} else {
			$text = ($editing) ? JText::_('FORUM_TOPIC_EDITED') : JText::_('FORUM_TOPIC_ADDED');
			$this->setNotification($text, 'passed');
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=topic&topic='.$row->id);
		}
	}

	/**
	 * Short description for 'deleteTopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function deleteTopic()
	{
		// Is the user logged in?
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('GROUPS_LOGIN_NOTICE') );
			return $this->topics();
		}

		$authorized = $this->_authorize();
		if ($authorized != 'admin') {
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

		$this->setNotification( JText::_('FORUM_TOPIC_DELETED'), 'passed');
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=topics');
	}
}