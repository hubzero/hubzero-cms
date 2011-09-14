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

ximport('Hubzero_Controller');

/**
 * Short description for 'XPollController'
 * 
 * Long description (if any) ...
 */
class XPollController extends Hubzero_Controller
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
		$this->_task = JRequest::getVar( 'task', 'latest' );

		switch ($this->_task)
		{
			case 'latest': $this->latest(); break;
			case 'vote':   $this->vote();   break;
			case 'view':   $this->view();   break;

			default: $this->view(); break;
		}
	}

	/**
	 * Short description for '_buildPathway'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $poll Parameter description (if any) ...
	 * @return     void
	 */
	protected function _buildPathway($poll=null)
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

	/**
	 * Short description for '_buildTitle'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $poll Parameter description (if any) ...
	 * @return     void
	 */
	protected function _buildTitle($poll=null)
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


	/**
	 * Short description for 'vote'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function vote()
	{
		$redirect = 1;

		// Instantiate a view
		$view = new JView( array('name'=>'vote') );
		$view->option = $this->_option;

		// Incoming poll ID
		$uid = JRequest::getInt( 'id', 0 );
		$view->pollid = $uid;

		// Load the poll
		$poll = new XPollPoll( $this->database );
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
		$vote = new XPollData( $this->database );
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
		$xpdate = new XPollDate( $this->database );
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

	/**
	 * Short description for 'view'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function view()
	{
		// Instantiate a view
		$view = new JView( array('name'=>'view') );
		$view->option = $this->_option;

		// Incoming
		$uid = JRequest::getInt( 'id', 0 );

		// Load the poll
		$poll = new XPollPoll( $this->database );
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
			$xpdate = new XPollDate( $this->database );
			$dates = $xpdate->getMinMaxDates( $poll->id );

			if (isset($dates[0]->mindate)) {
				$view->first_vote = JHTML::_( 'date', $dates[0]->mindate, JText::_('COM_XPOLL_DATE_FORMAT_LC2') );
				$view->last_vote  = JHTML::_( 'date', $dates[0]->maxdate, JText::_('COM_XPOLL_DATE_FORMAT_LC2') );
			}

			// Get the poll data
			$xpdata = new XPollData( $this->database );
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

	/**
	 * Short description for 'latest'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function latest()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'latest') );
		$view->option = $this->_option;

		// Load the latest poll
		$view->poll = new XPollPoll( $this->database );
		$view->poll->getLatestPoll();

		// Did we get a result from the database?
		if ($view->poll->id && $view->poll->title) {
			$xpdata = new XPollData( $this->database );
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

