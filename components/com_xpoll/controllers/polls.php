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
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * XPoll controller class for polls
 */
class XPollControllerPolls extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->registerTask('__default', 'latest');

		parent::execute();
	}

	/**
	 * Build the document pathway (breadcrumbs)
	 * 
	 * @param      object $poll XPollPoll
	 * @return     void
	 */
	protected function _buildPathway($poll=null)
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task && $this->_task != 'view') 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
		if (is_object($poll)) 
		{
			$pathway->addItem(
				stripslashes($poll->title),
				'index.php?option=' . $this->_option . '&task=view&id=' . $poll->id
			);
		}
	}

	/**
	 * Build the document title
	 * 
	 * @param      object $poll XPollPoll
	 * @return     void
	 */
	protected function _buildTitle($poll=null)
	{
		$this->_title = JText::_(strtoupper($this->_option));
		if ($this->_task && $this->_task != 'view') 
		{
			$this->_title .= ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		if (is_object($poll)) 
		{
			$this->_title .= ': ' . stripslashes($poll->title);
		}
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Vote for a poll option
	 * 
	 * @return     void
	 */
	public function voteTask()
	{
		$redirect = 1;

		// Incoming poll ID
		$uid = JRequest::getInt('id', 0);
		$this->view->pollid = $uid;

		// Load the poll
		$poll = new XPollPoll($this->database);
		if (!$poll->load($uid)) 
		{
			$this->view->setError(JText::_('COM_XPOLL_NOTAUTH'));
			$this->view->display();
			return;
		}

		// Set the cookie name
		$cookiename = 'voted' . $poll->id;

		// Check if this user has already voted
		$voted = JRequest::getVar($cookiename, '0', 'cookie');
		if ($voted) 
		{
			$this->view->setError(JText::_('COM_XPOLL_ALREADY_VOTED'));
			$this->view->display();
			return;
		}

		// Check if the user made a selection (voted for something)
		$voteid = JRequest::getInt('voteid', 0);
		if (!$voteid) 
		{
			$this->view->setError(JText::_('COM_XPOLL_NO_SELECTION'));
			$this->view->display();
			return;
		}

		// Set a cookie so we know they voted
		setcookie($cookiename, '1', time()+$poll->lag);

		// Increase the hits for the item that was voted for
		$vote = new XPollData($this->database);
		$vote->load($voteid);
		$vote->hits++;
		if (!$vote->check()) 
		{
			JError::raiseError(500, $vote->getError());
			return;
		}
		if (!$vote->store()) 
		{
			JError::raiseError(500, $vote->getError());
			return;
		}

		// Increase the total vote count for the poll
		$poll->increaseVoteCount();

		// Get voter's IP
		ximport('Hubzero_Environment');

		// Store the data about this vote
		$xpdate = new XPollDate($this->database);
		$xpdate->date     = date("Y-m-d G:i:s");
		$xpdate->vote_id  = $voteid;
		$xpdate->poll_id  = $poll->id;
		$xpdate->voter_ip = Hubzero_Environment::ipAddress();
		if (!$xpdate->check()) 
		{
			JError::raiseError(500, $vote->getError());
			return;
		}
		if (!$xpdate->store()) 
		{
			JError::raiseError(500, $vote->getError());
			return;
		}

		// Choose the action
		if ($redirect) 
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&task=view&id=' . $uid);
		} 
		else 
		{
			// Set the title
			$this->_buildTitle($poll);

			// Set the pathway
			$this->_buildPathway($poll);

			// Display the poll
			$this->view->display();
		}
	}

	/**
	 * View a poll's results
	 * 
	 * @return     void
	 */
	public function viewTask()
	{
		// Incoming
		$uid = JRequest::getInt('id', 0);

		// Load the poll
		$poll = new XPollPoll($this->database);
		$poll->load($uid);

		// If id value is passed and poll not published then exit
		if ($poll->id != '' && !$poll->published) 
		{
			$this->view->setError(JText::_('COM_XPOLL_POLL_NOT_FOUND'));
			$this->view->display();
			return;
		}

		$this->view->first_vote = '';
		$this->view->last_vote  = '';

		// Check if there is a poll corresponding to id and if poll is published
		if (isset($poll->id) && $poll->id != '' && $poll->published == 1) 
		{
			if (empty($poll->title)) 
			{
				$poll->id = '';
				$poll->title = JText::_('COM_XPOLL_SELECT_POLL');
			}

			// Get the first and last vote dates
			$xpdate = new XPollDate($this->database);
			$dates = $xpdate->getMinMaxDates($poll->id);

			if (isset($dates[0]->mindate)) 
			{
				$this->view->first_vote = JHTML::_('date', $dates[0]->mindate, JText::_('COM_XPOLL_DATE_FORMAT_LC2'));
				$this->view->last_vote  = JHTML::_('date', $dates[0]->maxdate, JText::_('COM_XPOLL_DATE_FORMAT_LC2'));
			}

			// Get the poll data
			$xpdata = new XPollData($this->database);
			$this->view->votes = $xpdata->getPollData($poll->id);
		}

		// Get all published polls
		$this->view->polls = $poll->getAllPolls();

		// Push some needed styles and javascript to the template
		$this->_getStyles();
		$this->_getScripts('assets/js/xpoll');

		// Set the page title
		$this->_buildTitle($poll);

		// Set the pathway
		$this->_buildPathway($poll);

		$this->view->poll = $poll;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Display the latest poll
	 * 
	 * @return     void
	 */
	public function latestTask()
	{
		// Load the latest poll
		$this->view->poll = new XPollPoll($this->database);
		$this->view->poll->getLatestPoll();

		// Did we get a result from the database?
		if ($this->view->poll->id && $this->view->poll->title) 
		{
			$xpdata = new XPollData($this->database);
			$this->view->options = $xpdata->getPollOptions($this->view->poll->id, false);
		} 
		else 
		{
			$this->view->options = array();
		}

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some needed styles and javascript to the template
		$this->_getStyles();

		// Output HTML
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}
}

