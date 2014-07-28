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
 * Newsletter Mailing List Controller
 */
class NewsletterControllerMailinglist extends \Hubzero\Component\SiteController
{
	/**
	 * Override parent build title method
	 *
	 * @param 	object	$newsletter		Newsletter object for adding campaign name to title
	 */
	public function _buildTitle( $newsletter = null )
	{
		//default if no campaign
		$this->_title = JText::_(strtoupper($this->_option));

		//add campaign name to title
		if (is_object($newsletter) && $newsletter->id)
		{
			$this->_title = JText::_('COM_NEWSLETTER_NEWSLETTER') . ': ' . $newsletter->name;
		}

		//if we are unsubscribing
		if ($this->_task == 'unsubscribe')
		{
			$this->_title = JText::_('COM_NEWSLETTER_NEWSLETTER') . ': ' . JText::_('COM_NEWSLETTER_UNSUBSCRIBE');
		}

		//if we are subscribing
		if ($this->_task == 'subscribe')
		{
			$this->_title = JText::_('COM_NEWSLETTER_NEWSLETTER') . ': ' . JText::_('COM_NEWSLETTER_SUBSCRIBE');
		}

		//set title of browser window
		$document = JFactory::getDocument();
		$document->setTitle( $this->_title );
	}


	/**
	 * Override parent build pathway method
	 *
	 * @param 	object	$campaign		Newsletter object for adding campaign name pathway
	 */
	public function _buildPathway( $newsletter = null )
	{
		//get the pathway
		$pathway = JFactory::getApplication()->getPathway();

		//add 'newlsetters' item to pathway
		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem( JText::_(strtoupper($this->_option)), 'index.php?option=' . $this->_option );
		}

		//add campaign
		if (is_object($newsletter) && $newsletter->id)
		{
			$pathway->addItem( JText::_($newsletter->name), 'index.php?option=' . $this->_option . '&id=' . $newsletter->id );
		}

		//if we are unsubscribing
		if ($this->_task == 'unsubscribe')
		{
			$pathway->addItem( JText::_('COM_NEWSLETTER_SUBSCRIBE'), 'index.php?option=' . $this->_option . '&task=unsubscribe' );
		}

		//if we are subscribing
		if ($this->_task == 'subscribe')
		{
			$pathway->addItem( JText::_('COM_NEWSLETTER_SUBSCRIBE'), 'index.php?option=' . $this->_option . '&task=subscribe' );
		}
	}


	/**
	 * Subscribe to Mailing Lists View
	 *
	 * @return 	void
	 */
	public function subscribeTask()
	{
		//set layout
		$this->view->setLayout('subscribe');

		//must be logged in
		if ($this->juser->get('guest'))
		{
			//build return url and redirect url
			$return 	= JRoute::_('index.php?option=com_newsletter&task=subscribe');
			$redirect	= JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($return) );

			//redirect
			$this->setRedirect( $redirect, JText::_('COM_NEWSLETTER_LOGIN_TO_SUBSCRIBE'), 'warning');
			return;
		}

		//get mailing lists user belongs to
		$newsletterMailinglist = new NewsletterMailinglist( $this->database );
		$this->view->mylists = $newsletterMailinglist->getListsForEmail( $this->juser->get('email') );

		//get all lists
		$this->view->alllists = $newsletterMailinglist->getLists( null, 'public' );

		//build title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		//set vars for view
		$this->view->title = $this->_title;

		//output
		$this->view->display();
	}


	/**
	 * Subscribe to *Single* Mailing List (Newsletter Module)
	 *
	 * @return 	void
	 */
	public function doSingleSubscribeTask()
	{
		//check to make sure we have a valid token
		if (!JRequest::checkToken()) die('Invalid Token');

		//get request vars
		$list   = JRequest::getInt('list_' . JUtility::getToken(), '', 'post');
		$email  = JRequest::getVar('email_' . JUtility::getToken(), $this->juser->get('email'), 'post');
		$sid    = JRequest::getInt('subscriptionid', 0);
		$hp1    = JRequest::getVar('hp1', '', 'post');
		$return = base64_decode( JRequest::getVar('return', '/', 'post') );

		//check to make sure our honey pot is good
		if ($hp1 != '')
		{
			die(JText::_('COM_NEWSLETTER_HP_ERROR'));
		}

		//validate email
		if (!isset($email) || $email == '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			//inform user and redirect
			$this->_messageType = 'error';
			$this->_message     = JText::_('COM_NEWSLETTER_SUBSCRIBE_BADEMAIL');
			$this->_redirect    = JRoute::_( $return );
			return;
		}

		//validate list
		if (!isset($list) || !is_numeric($list))
		{
			//inform user and redirect
			$this->_messageType = 'error';
			$this->_message     = JText::_('COM_NEWSLETTER_SUBSCRIBE_BADLIST');
			$this->_redirect    = JRoute::_( $return );
			return;
		}

		//load mailing list object
		$newsletterMailinglist = new NewsletterMailinglist( $this->database );
		$newsletterMailinglist->load( $list );

		//make sure its not private or already deleted
		if (is_object($newsletterMailinglist) && !$newsletterMailinglist->private && !$newsletterMailinglist->deleted)
		{
			$subscription             = new stdClass;
			$subscription->id          = $sid;
			$subscription->mid        = $list;
			$subscription->email      = $email;
			$subscription->status     = 'inactive';
			$subscription->date_added = JFactory::getDate()->toSql();

			//mail confirmation email and save subscription
			if (NewsletterHelper::sendMailinglistConfirmationEmail( $email, $newsletterMailinglist, false ))
			{
				$newsletterMailinglistEmail = new NewsletterMailinglistEmail( $this->database );
				$newsletterMailinglistEmail->save( $subscription );
			}
		}

		//inform user and redirect
		$this->_message 	= JText::sprintf('COM_NEWSLETTER_SUBSCRIBE_SUCCESS', $newsletterMailinglist->name);
		$this->_redirect 	= JRoute::_( $return );
	}


	/**
	 * Subscribe/Unsubscribe from *Multiple* Mailing Lists
	 *
	 * @return 	void
	 */
	public function doMultiSubscribeTask()
	{
		//get request vars
		$lists = JRequest::getVar('lists', array(), 'post');
		$email = $this->juser->get('email');

		//get my lists
		$newsletterMailinglist = new NewsletterMailinglist( $this->database );
		$mylists = $newsletterMailinglist->getListsForEmail( $email, 'mailinglistid' );

		// subscribe user to checked lists
		foreach ($lists as $list)
		{
			//only subscribe if not previously
			if (!in_array($list, array_keys($mylists)))
			{
				//load mailing list object
				$newsletterMailinglist = new NewsletterMailinglist( $this->database );
				$newsletterMailinglist->load( $list );

				//make sure its not private or already deleted
				if (is_object($newsletterMailinglist) && !$newsletterMailinglist->private && !$newsletterMailinglist->deleted)
				{
					$subscription             = new stdClass;
					$subscription->mid        = $list;
					$subscription->email      = $email;
					$subscription->status     = 'inactive';
					$subscription->date_added = JFactory::getDate()->toSql();

					//mail confirmation email and save subscription
					if (NewsletterHelper::sendMailinglistConfirmationEmail( $email, $newsletterMailinglist, false ))
					{
						$newsletterMailinglistEmail = new NewsletterMailinglistEmail( $this->database );
						$newsletterMailinglistEmail->save( $subscription );
					}
				}
			}
		}

		//check to make sure we dont need to unsubscribe from lists
		foreach ($mylists as $mylist)
		{
			//instantiate newsletter mailing email
			$newsletterMailinglistEmail = new NewsletterMailinglistEmail( $this->database );
			$newsletterMailinglistEmail->load( $mylist->id );

			//do we want to mark as active or mark as unsubscribed
			if (!in_array($mylist->mailinglistid, $lists))
			{
				//set as unsubscribed
				$newsletterMailinglistEmail->status         = 'unsubscribed';
				$newsletterMailinglistEmail->confirmed      = 0;
				$newsletterMailinglistEmail->date_confirmed = null;
			}
			else if ($mylist->status != 'active')
			{
				//set as active
				$newsletterMailinglistEmail->status = 'inactive';

				//load mailing list object
				$newsletterMailinglist = new NewsletterMailinglist( $this->database );
				$newsletterMailinglist->load( $mylist->mailinglistid );

				//send a new confirmation
				NewsletterHelper::sendMailinglistConfirmationEmail( $email, $newsletterMailinglist, false );

				//delete all unsubscribes
				$sql = "DELETE FROM #__newsletter_mailinglist_unsubscribes
						WHERE mid=" . $this->database->quote( $mylist->mailinglistid ) . "
						AND email=" . $this->database->quote( $email );
				$this->database->setQuery( $sql );
				$this->database->query();
			}

			//save
			$newsletterMailinglistEmail->save( $newsletterMailinglistEmail );
		}

		//inform user and redirect
		$this->_message  = JText::_('COM_NEWSLETTER_MAILINGLISTS_SAVE_SUCCESS');
		$this->_redirect = JRoute::_('index.php?option=com_newsletter&task=subscribe');
		return;
	}


	/**
	 * Unsubscribe From Mailing Lists
	 *
	 * @return 	void
	 */
	public function unsubscribeTask()
	{
		//set layout
		$this->view->setLayout('unsubscribe');

		//get request vars
		$email = JRequest::getVar('e', '');
		$token = JRequest::getVar('t', '');

		//parse token
		$recipient = NewsletterHelper::parseMailingToken( $token );

		//make sure mailing recipient email matches email param
		if ($email != $recipient->email)
		{
			$this->_messageType = 'error';
			$this->_message 	= JText::_('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_LINK_ISSUE');
			$this->_redirect 	= JRoute::_('index.php?option=com_newsletter&task=subscribe');
			return;
		}

		//get newsletter mailing to get mailing list id mailing was sent to
		$newsletterMailing = new NewsletterMailing( $this->database );
		$mailing = $newsletterMailing->getMailings( $recipient->mid );

		//make sure we have a mailing object
		if (!is_object($mailing))
		{
			$this->_messageType = 'error';
			$this->_message 	= JText::_('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_NO_MAILING');
			$this->_redirect 	= JRoute::_('index.php?option=com_newsletter&task=subscribe');
			return;
		}

		//is the mailing list to the default hub mailing list?
		if ($mailing->lid == '-1')
		{
			$mailinglist 				= new stdClass;
			$mailinglist->id 			= '-1';
			$mailinglist->name 			= 'HUB Members';
			$mailinglist->description 	= JText::_('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_DEFAULTLIST');
		}
		else
		{
			//load mailing list
			$newsletterMailinglist = new NewsletterMailinglist( $this->database );
			$mailinglist = $newsletterMailinglist->getLists( $mailing->lid );
		}

		//check to make sure were not already unsubscribed
		$unsubscribedAlready = false;
		if ($mailing->lid == '-1')
		{
			$sql = "SELECT *
					FROM #__xprofiles as p
					WHERE p.email=" . $this->database->quote( $mailing->email ) . "
					AND p.mailPreferenceOption > " . $this->database->quote( 0 );
			$this->database->setQuery( $sql );
			$profile = $this->database->loadObject();

			if (!is_object( $profile ) || $profile->uidNumber == '')
			{
				$unsubscribedAlready = true;
			}
		}
		else
		{
			//check to make sure email is on list
			$sql = "SELECT *
					FROM #__newsletter_mailinglist_emails as mle
					WHERE mle.mid=" . $this->database->quote( $mailing->lid ) . "
					AND mle.email=" . $this->database->quote( $recipient->email ) . "
					AND mle.status=" . $this->database->quote( 'active' );
			$this->database->setQuery( $sql );
			$list = $this->database->loadObject();

			if (!is_object( $list ) || $list->id == '')
			{
				$unsubscribedAlready = true;
			}
		}

		//are we unsubscribed already
		if ($unsubscribedAlready)
		{
			$this->_messageType = 'error';
			$this->_message 	= JText::sprintf('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_ALREADY_UNSUBSCRIBED', $mailinglist->name);
			$this->_redirect 	= JRoute::_('index.php?option=com_newsletter&task=subscribe');
			if ($this->juser->get('guest'))
			{
				$this->_redirect 	= JRoute::_('index.php?option=com_newsletter');
			}
			return;
		}

		//build title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		//set vars for view
		$this->view->title 			= $this->_title;
		$this->view->juser 			= $this->juser;
		$this->view->mailinglist 	= $mailinglist;

		//output
		$this->view->display();
	}


	/**
	 * Unsubscribe User Mailing Lists
	 *
	 * @return 	void
	 */
	public function doUnsubscribeTask()
	{
		//get request vars
		$email 		= JRequest::getVar('e', '');
		$token 		= JRequest::getVar('t', '');
		$reason 	= JRequest::getVar('reason', '');
		$reason_alt = JRequest::getVar('reason-alt', '');

		//grab the reason explaination if user selected other
		if ($reason == 'Other')
		{
			$reason = $reason_alt;
		}

		//parse mailing token
		$recipient = NewsletterHelper::parseMailingToken( $token );

		//make sure the token is valid
		if (!is_object($recipient) || $email != $recipient->email)
		{
			$this->_messageType = 'error';
			$this->_message 	= JText::_('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_LINK_ISSUE');
			$this->_redirect 	= JRoute::_('index.php?option=com_newsletter&task=subscribe');
			return;
		}

		//get newsletter mailing to get mailing list id mailing was sent to
		$newsletterMailing = new NewsletterMailing( $this->database );
		$mailing = $newsletterMailing->getMailings( $recipient->mid );

		//make sure we have a mailing object
		if (!is_object($mailing))
		{
			$this->_messageType = 'error';
			$this->_message 	= JText::_('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_NO_MAILING');
			$this->_redirect 	= JRoute::_('index.php?option=com_newsletter&task=subscribe');
			return;
		}

		//are we unsubscribing from default list?
		$sql = '';
		if ($mailing->lid == '-1')
		{
			if (!$this->juser->get('guest'))
			{
				$sql = "UPDATE #__xprofiles SET mailPreferenceOption=0 WHERE uidNumber=" . $this->database->quote( $this->juser->get('id') );
			}
			else
			{
				//build return url and redirect url
				$return = JRoute::_('index.php?option=com_newsletter&task=unsubscribe&e=' . $email . '&t=' . $token );

				//inform user and redirect
				$this->_messageType = 'warning';
				$this->_message 	= JText::_('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_MUST_LOGIN');
				$this->_redirect	= JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($return) );
				return;
			}
		}
		else
		{
			//update the emails status on the mailing list
			$sql = "UPDATE #__newsletter_mailinglist_emails
					SET status=" . $this->database->quote('unsubscribed') . "
					WHERE mid=" . $this->database->quote( $mailing->lid ) . "
					AND email=" . $this->database->quote( $recipient->email );
		}

		//set query and execute
		$this->database->setQuery( $sql );
		if (!$this->database->query())
		{
			$this->_messageType = 'error';
			$this->_message		= JText::_('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_ERROR');
			$this->_redirect 	= JRoute::_('index.php?option=com_newsletter&task=unsubscribe&e=' . $email . '&t=' . $token);
			return;
		}

		//insert unsubscribe reason
		$sql = "INSERT INTO #__newsletter_mailinglist_unsubscribes(mid,email,reason)
				VALUES(".$this->database->quote( $mailing->lid ).",".$this->database->quote( $recipient->email ).",".$this->database->quote( $reason ).")";
		$this->database->setQuery( $sql );
		$this->database->query();

		//inform user of successful unsubscribe
		$this->_messageType = 'success';
		$this->_message		= JText::_('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_SUCCESS');
		$this->_redirect 	= JRoute::_('index.php?option=com_newsletter&task=subscribe');
		if ($this->juser->get('guest'))
		{
			$this->_redirect 	= JRoute::_('index.php?option=com_newsletter');
		}
		return;
	}


	/**
	 * Confirm Subscription to Mailing list
	 *
	 * @return 	void
	 */
	public function confirmTask()
	{
		//get request vars
		$email = JRequest::getVar('e', '');
		$token = JRequest::getVar('t', '');

		//make sure we have an email
		$mailinglistEmail = NewsletterHelper::parseConfirmationToken( $token );

		//make sure the token is valid
		if (!is_object($mailinglistEmail) || $email != $mailinglistEmail->email)
		{
			$this->_messageType = 'error';
			$this->_message 	= JText::_('COM_NEWSLETTER_MAILINGLIST_CONFIRMATION_LINK_ISSUE');
			$this->_redirect 	= JRoute::_('index.php?option=com_newsletter');
			return;
		}

		//instantiate mailing list email object and load based on id
		$newsletterMailinglistEmail = new NewsletterMailinglistEmail( $this->database );
		$newsletterMailinglistEmail->load( $mailinglistEmail->id );

		//set that we are now confirmed
		$newsletterMailinglistEmail->status			= 'active';
		$newsletterMailinglistEmail->confirmed 		= 1;
		$newsletterMailinglistEmail->date_confirmed = JFactory::getDate()->toSql();

		//save
		$newsletterMailinglistEmail->save( $newsletterMailinglistEmail );

		//inform user
		$this->_messageType = 'success';
		$this->_message 	= JText::_('COM_NEWSLETTER_MAILINGLIST_CONFIRM_SUCCESS');;
		$this->_redirect 	= JRoute::_('index.php?option=com_newsletter&task=subscribe');

		//if were not logged in go back to newsletter page
		if (JFactory::getUser()->get('guest'))
		{
			$this->_redirect = JRoute::_('index.php?option=com_newsletter');
		}
	}


	/**
	 * Remove From Mailing list
	 *
	 * @param 	$email
	 * @return 	void
	 */
	public function removeTask()
	{
		//get request vars
		$email = JRequest::getVar('e', '');
		$token = JRequest::getVar('t', '');

		//make sure we have an email
		$mailinglistEmail = NewsletterHelper::parseConfirmationToken( $token );

		//make sure the token is valid
		if (!is_object($mailinglistEmail) || $email != $mailinglistEmail->email)
		{
			$this->_messageType = 'error';
			$this->_message 	= JText::_('COM_NEWSLETTER_MAILINGLIST_CONFIRMATION_LINK_ISSUE');
			$this->_redirect 	= JRoute::_('index.php?option=com_newsletter');
			return;
		}

		//instantiate mailing list email object and load based on id
		$newsletterMailinglistEmail = new NewsletterMailinglistEmail( $this->database );
		$newsletterMailinglistEmail->load( $mailinglistEmail->id );

		//unsubscribe & unconfirm email
		$newsletterMailinglistEmail->status 	= "unsubscribed";
		$newsletterMailinglistEmail->confirmed 	= 0;

		//save
		$newsletterMailinglistEmail->save( $newsletterMailinglistEmail );

		//inform user
		$this->_messageType = 'success';
		$this->_message 	= JText::_('COM_NEWSLETTER_MAILINGLIST_REMOVED_SUCCESS');
		$this->_redirect 	= JRoute::_('index.php?option=com_newsletter');
	}


	/**
	 * Resend Newsletter Confirmation
	 * 
	 * @return [type] [description]
	 */
	public function resendConfirmationTask()
	{
		//get request vars
		$mid = JRequest::getInt('mid', 0);

		//instantiate mailing list object
		$newsletterMailinglist = new NewsletterMailinglist( $this->database );
		$newsletterMailinglist->load( $mid );

		//send confirmation email
		NewsletterHelper::sendMailinglistConfirmationEmail( $this->juser->get('email'), $newsletterMailinglist, false );

		//inform user and redirect
		$this->_message 	= JText::sprintf('COM_NEWSLETTER_MAILINGLISTS_CONFIRM_SENT', $this->juser->get('email'));
		$this->_redirect 	= JRoute::_('index.php?option=com_newsletter&task=subscribe');
		return;
	}
}