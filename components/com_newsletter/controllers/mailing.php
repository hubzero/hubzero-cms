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

/**
 * Newsletter Mailing Controller
 */
class NewsletterControllerMailing extends \Hubzero\Component\SiteController
{
	/**
	 * General Tracking Task - Routes to open and click tracking based on type
	 *
	 * @return 	void
	 */
	public function trackTask()
	{
		$type = JRequest::getVar('type');
		switch ($type)
		{
			case 'open':	$this->openTrackingTask();		break;
			case 'click': 	$this->clickTrackingTask();		break;
			case 'print': 	$this->printTrackingTask();		break;
			case 'forward': $this->forwardTrackingTask();	break;
		}
	}


	/**
	 * Open Tracker
	 *
	 * @return 	void
	 */
	private function openTrackingTask()
	{
		//get reqest vars
		$token = JRequest::getVar('t', '');

		//parse mailing token
		$recipient = NewsletterHelper::parseMailingToken( $token );

		//if we found an object lets track it
		if (is_object($recipient) && $recipient->id)
		{
			//new mailing recipient action objec
			$newsletterMailingRecipientAction = new NewsletterMailingRecipientAction( $this->database );

			//check to see if we already opened
			if (!$newsletterMailingRecipientAction->actionExistsForMailingAndEmail( $recipient->mid, $recipient->email, 'open' ))
			{
				//create object holding our vars to store action
				$action              = new stdClass;
				$action->mailingid   = $recipient->mid;
				$action->action      = 'open';
				$action->action_vars = null;
				$action->email       = $recipient->email;
				$action->ip          = $_SERVER['REMOTE_ADDR'];
				$action->user_agent  = $_SERVER['HTTP_USER_AGENT'];
				$action->date        = JFactory::getDate()->toSql();

				//save action
				$newsletterMailingRecipientAction->save( $action );
			}
		}

		//create image to ouput
		NewsletterHelper::mailingOpenTrackerGif();
		exit();
	}


	/**
	 * Click Tracker
	 *
	 * @return 	void
	 */
	private function clickTrackingTask()
	{
		//get reqest vars
		$token 	= JRequest::getVar('t', '');
		$link 	= JRequest::getVar('l', '', 'get', 'STRING', JREQUEST_ALLOWRAW);

		//parse mailing token
		$recipient = NewsletterHelper::parseMailingToken( $token );

		//url decode and replace zero width spaces
		$link = urldecode( $link );
		$link = str_replace('&#8203;', '', $link);

		//if we found an object lets track it
		if (is_object($recipient) && $recipient->id)
		{
			//new mailing recipient action objec
			$newsletterMailingRecipientAction = new NewsletterMailingRecipientAction( $this->database );

			//array of action vars, json encoded for saving to db
			$actionVars = json_encode( array( 'url' => $link ) );

			//create object holding our vars to store action
			$action              = new stdClass;
			$action->mailingid   = $recipient->mid;
			$action->action      = 'click';
			$action->action_vars = $actionVars;
			$action->email       = $recipient->email;
			$action->ip          = $_SERVER['REMOTE_ADDR'];
			$action->user_agent  = $_SERVER['HTTP_USER_AGENT'];
			$action->date        = JFactory::getDate()->toSql();

			//save action
			$newsletterMailingRecipientAction->save( $action );
		}

		//make sure we have a valid link
		//if we do redirect
		if (filter_var($link, FILTER_VALIDATE_URL))
		{
			JFactory::getApplication()->redirect( $link );
		}
	}


	/**
	 * Print Tracker
	 *
	 * @return 	void
	 */
	private function printTrackingTask()
	{
		//get reqest vars
		$token = JRequest::getVar('t', '');

		//parse mailing token
		$recipient = NewsletterHelper::parseMailingToken( $token );

		//if we found an object lets track it
		if (is_object($recipient) && $recipient->id)
		{
			//new mailing recipient action objec
			$newsletterMailingRecipientAction = new NewsletterMailingRecipientAction( $this->database );

			//create object holding our vars to store action
			$action              = new stdClass;
			$action->mailingid   = $recipient->mid;
			$action->action      = 'print';
			$action->action_vars = null;
			$action->email       = $recipient->email;
			$action->ip          = $_SERVER['REMOTE_ADDR'];
			$action->user_agent  = $_SERVER['HTTP_USER_AGENT'];
			$action->date        = JFactory::getDate()->toSql();

			//save action
			$newsletterMailingRecipientAction->save( $action );
		}
	}


	/**
	 * Forward Tracker
	 *
	 * @return 	void
	 */
	private function forwardTrackingTask()
	{
		//get reqest vars
		$token = JRequest::getVar('t', '');

		//parse mailing token
		$recipient = NewsletterHelper::parseMailingToken( $token );

		//if we found an object lets track it
		if (is_object($recipient) && $recipient->id)
		{
			//new mailing recipient action objec
			$newsletterMailingRecipientAction = new NewsletterMailingRecipientAction( $this->database );

			//create object holding our vars to store action
			$action              = new stdClass;
			$action->mailingid   = $recipient->mid;
			$action->action      = 'forward';
			$action->action_vars = null;
			$action->email       = $recipient->email;
			$action->ip          = $_SERVER['REMOTE_ADDR'];
			$action->user_agent  = $_SERVER['HTTP_USER_AGENT'];
			$action->date        = JFactory::getDate()->toSql();

			//save action
			$newsletterMailingRecipientAction->save( $action );
		}
	}
}