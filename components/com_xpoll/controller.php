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

class XPollController extends JObject
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
	
	public function execute()
	{
		$this->_task = JRequest::getVar( 'task', 'latest' );
		
		switch ($this->_task) 
		{
			case 'latest': $this->latest(); break;
			case 'vote':   $this->vote();   break;
			case 'view':   $this->view();   break;

			default: $this->view(); break;
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
	
	private function _getStyles() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}

	//-----------
	
	private function _getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file('components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
		}
	}
	
	//-----------

	private function _buildPathway($poll=null) 
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}
		if ($this->_task && $this->_task != 'view') {
			$pathway->addItem(
				JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
				'index.php?option='.$this->_option.'&task='.$this->_task
			);
		}
		if (is_object($poll)) {
			$pathway->addItem(
				stripslashes($poll->title),
				'index.php?option='.$this->_option.'&task=view&id='.$poll->id
			);
		}
	}
	
	//-----------
	
	private function _buildTitle($poll=null) 
	{
		$this->_title = JText::_(strtoupper($this->_option));
		if ($this->_task && $this->_task != 'view') {
			$this->_title .= ': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		}
		if (is_object($poll)) {
			$this->_title .= ': '.stripslashes($poll->title);
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $this->_title );
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function vote() 
	{
		$database =& JFactory::getDBO();
		$redirect = 1;
		
		// Instantiate a view
		$view = new JView( array('name'=>'vote') );
		$view->option = $this->_option;

		// Incoming poll ID
		$uid = JRequest::getInt( 'id', 0 );
		$view->pollid = $uid;
		
		// Load the poll
		$poll = new XPollPoll( $database );
		if (!$poll->load( $uid )) {
			$view->setError( JText::_('COM_XPOLL_NOTAUTH') );
			$view->display();
			return;
		}

		// Set the cookie name
		$cookiename = 'voted'.$poll->id;
		
		// Check if this user has already voted
		$voted = JRequest::getVar( $cookiename, '0', 'cookie' );
		if ($voted) {
			$view->setError( JText::_('COM_XPOLL_ALREADY_VOTED') );
			$view->display();
			return;
		}

		// Check if the user made a selection (voted for something)
		$voteid = JRequest::getInt( 'voteid', 0 );
		if (!$voteid) {
			$view->setError( JText::_('COM_XPOLL_NO_SELECTION') );
			$view->display();
			return;
		}
		
		// Set a cookie so we know they voted
		setcookie( $cookiename, '1', time()+$poll->lag );

		// Increase the hits for the item that was voted for
		$vote = new XPollData( $database );
		$vote->load( $voteid );
		$vote->hits++;
		if (!$vote->check()) {
			JError::raiseError( 500, $vote->getError() );
			return;
		}
		if (!$vote->store()) {
			JError::raiseError( 500, $vote->getError() );
			return;
		}
		
		// Increase the total vote count for the poll
		$poll->increaseVoteCount();
		
		// Get voter's IP
		$ip = (getenv(HTTP_X_FORWARDED_FOR))
	    	?  getenv(HTTP_X_FORWARDED_FOR)
	    	:  getenv(REMOTE_ADDR);
		
		// Store the data about this vote
		$xpdate = new XPollDate( $database );
		$xpdate->date = date("Y-m-d G:i:s");
		$xpdate->vote_id = $voteid;
		$xpdate->poll_id = $poll->id;
		$xpdate->voter_ip = $ip;
		if (!$xpdate->check()) {
			JError::raiseError( 500, $vote->getError() );
			return;
		}
		if (!$xpdate->store()) {
			JError::raiseError( 500, $vote->getError() );
			return;
		}
		
		// Choose the action
		if ($redirect) {
			$this->_redirect = JRoute::_( 'index.php?option='.$this->_option.'&task=view&id='.$uid );
		} else {
			// Set the title
			$this->_buildTitle($poll);

			// Set the pathway
			$this->_buildPathway($poll);
			
			// Display the poll
			$view->display();
		}
	}

	//-----------

	protected function view() 
	{
		$database =& JFactory::getDBO();
		
		// Instantiate a view
		$view = new JView( array('name'=>'view') );
		$view->option = $this->_option;
		
		// Incoming
		$uid = JRequest::getInt( 'id', 0 );

		// Load the poll
		$poll = new XPollPoll( $database );
		$poll->load( $uid );

		// If id value is passed and poll not published then exit
		if ($poll->id != '' && !$poll->published) {
			$view->setError( JText::_('COM_XPOLL_POLL_NOT_FOUND') );
			$view->display();
			return;
		}

		$view->first_vote = '';
		$view->last_vote  = '';

		// Check if there is a poll corresponding to id and if poll is published
		if (isset($poll->id) && $poll->id != '' && $poll->published == 1) {
			if (empty($poll->title)) {
				$poll->id = '';
				$poll->title = JText::_('COM_XPOLL_SELECT_POLL');
			}

			// Get the first and last vote dates
			$xpdate = new XPollDate( $database );
			$dates = $xpdate->getMinMaxDates( $poll->id );

			if (isset($dates[0]->mindate)) {
				$view->first_vote = JHTML::_( 'date', $dates[0]->mindate, JText::_('COM_XPOLL_DATE_FORMAT_LC2') );
				$view->last_vote  = JHTML::_( 'date', $dates[0]->maxdate, JText::_('COM_XPOLL_DATE_FORMAT_LC2') );
			}
			
			// Get the poll data
			$xpdata = new XPollData( $database );
			$view->votes = $xpdata->getPollData( $poll->id );
		}
	
		// Get all published polls
		$view->polls = $poll->getAllPolls();
		
		// Push some needed styles and javascript to the template
		$this->_getStyles();
		$this->_getScripts();
		
		// Set the page title
		$this->_buildTitle($poll);

		// Set the pathway
		$this->_buildPathway($poll);

		$view->poll = $poll;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	protected function latest()
	{
		$database =& JFactory::getDBO();

		// Instantiate a new view
		$view = new JView( array('name'=>'latest') );
		$view->option = $this->_option;

		// Load the latest poll
		$view->poll = new XPollPoll( $database );
		$view->poll->getLatestPoll();

		// Did we get a result from the database?
		if ($view->poll->id && $view->poll->title) {
			$xpdata = new XPollData( $database );
			$view->options = $xpdata->getPollOptions( $view->poll->id, false );
		} else {
			$view->options = array();
		}
		
		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
}
?>