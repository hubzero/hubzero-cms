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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Cron plugin for support tickets
 */
class plgCronNewsletter extends JPlugin
{
	/**
	 * Return a list of events
	 * 
	 * @return     array
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			array(
				'name'   => 'processMailings',
				'label'  => JText::_('PLG_CRON_NEWSLETTER_PROCESS_QUEUE'),
				'params' => 'processmailings'
			),
			array(
				'name'   => 'processIps',
				'label'  => JText::_('PLG_CRON_NEWSLETTER_IP_ADDRESSES_TO_LOCATION'),
				'params' => ''
			)
		);
		
		return $obj;
	}
	
	
	/**
	 * Processes any queued newsletter mailings.
	 * 
	 * @return     void
	 */
	public function processMailings( $params = null )
	{
		//load needed libraries
		require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'mailing.recipient.php';
		
		//needed vars
		$limit     = 25;
		$processed = array();
		
		//do we have a param defined limit
		if (is_object($params) && $params->get('newsletter_queue_limit'))
		{
			$paramDefinedLimit = $params->get('newsletter_queue_limit');
			if (is_numeric($paramDefinedLimit) && $paramDefinedLimit > 0 && $paramDefinedLimit < 100)
			{
				$limit = $paramDefinedLimit;
			}
		}
		
		//create needed objects
		$database = JFactory::getDBO();
		
		//get all queued mailing recipients
		$sql = "SELECT nmr.id AS mailing_recipientid, nm.id AS mailingid, nm.nid AS newsletterid, nm.lid AS mailinglistid, nmr.email, nm.subject, nm.html_body, nm.plain_body, nm.headers, nm.args, nm.tracking
				FROM `#__newsletter_mailings` AS nm, `#__newsletter_mailing_recipients` AS nmr
				WHERE nm.id=nmr.mid
				AND nmr.status='queued'
				AND nm.deleted=0
				AND UTC_TIMESTAMP() >= nm.date
				ORDER BY nmr.date_added
				LIMIT {$limit}";
		$database->setQuery( $sql );
		$queuedEmails = $database->loadObjectList();
		
		//loop through each newsletter recipient, prepare and mail
		foreach ($queuedEmails as $queuedEmail)
		{
			//get tracking & unsubscribe token
			$emailToken = Hubzero_Newsletter_Helper::generateMailingToken( $queuedEmail );
			
			//if tracking is on add it to email
			if ($queuedEmail->tracking)
			{
				$queuedEmail->html_body = Hubzero_Newsletter_Helper::addTrackingToEmailMessage( $queuedEmail->html_body, $emailToken );
			}
			
			//create unsubscribe link
			$unsubscribeMailtoLink = '';
			$unsubscribeLink       = 'https://' . $_SERVER['SERVER_NAME'] . '/newsletter/unsubscribe?e=' . $queuedEmail->email . '&t=' . $emailToken;
			
			//add unsubscribe link - placeholder & in header (must do after adding tracking!!)
			$queuedEmail->html_body = str_replace("{{UNSUBSCRIBE_LINK}}", $unsubscribeLink, $queuedEmail->html_body);
			$queuedEmail->headers   = str_replace("{{UNSUBSCRIBE_LINK}}", $unsubscribeLink, $queuedEmail->headers);
			$queuedEmail->headers   = str_replace("{{UNSUBSCRIBE_MAILTO_LINK}}", $unsubscribeMailtoLink, $queuedEmail->headers);
			
			//add mailing id to header
			$queuedEmail->headers  = str_replace("{{CAMPAIGN_MAILING_ID}}", $queuedEmail->mailingid, $queuedEmail->headers);
			
			// create new message
			$message = new Hubzero_Mail_Message();
			
			// add headers
			foreach (explode("\r\n", $queuedEmail->headers) as $header)
			{
				$parts = array_map("trim", explode(':', $header));
				$message->addHeader($parts[0], $parts[1]);
			}

			// build message object and send
			$message->setSubject($queuedEmail->subject)
					->setTo($queuedEmail->email)
					->setBody($queuedEmail->plain_body, 'text/plain')
					->addPart($queuedEmail->html_body, 'text/html');

			//mail message
			if ($message->send())
			{
				//add to process email array
				$processed[] = $queuedEmail->email;
				
				//load recipient object
				$newsletterMailingRecipient = new NewsletterMailingRecipient( $database );
				$newsletterMailingRecipient->load( $queuedEmail->mailing_recipientid );
				
				//mark as sent and save
				$newsletterMailingRecipient->status    = 'sent';
				$newsletterMailingRecipient->date_sent = JFactory::getDate()->toSql();
				$newsletterMailingRecipient->save( $newsletterMailingRecipient );
			}
		}
		
		return true;
	}
	
	
	/**
	 * Processes newsletter mailing actions (clicks, opens, etc) IP addresses into location data for stats
	 * 
	 * @return     void
	 */
	public function processIps( $params = null )
	{
		//load needed libraries
		require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'mailing.recipient.action.php';
		
		//import geo db
		ximport('Hubzero_Geo');
		
		//get deo db
		$geodatabase = Hubzero_Geo::getGeoDBO();
		$database    = JFactory::getDBO();
		
		//make sure we have a database
		if (!is_object($geodatabase))
		{
			return false;
		}
		
		//get actions
		$newsletterMailingRecipientAction = new NewsletterMailingRecipientAction( $database );
		$unconvertedActions = $newsletterMailingRecipientAction->getUnconvertedActions();
		
		//convert all unconverted actions
		foreach ($unconvertedActions as $action)
		{
			//get geo information
			$geodatabase->setQuery("SELECT * FROM ipcitylatlong WHERE INET_ATON('{$action->ip}') BETWEEN ipFROM and ipTO");
			$result = $geodatabase->loadObject();
			
			//if we got a valid result lets update our action with location info
			if (is_object($result) && $result->countrySHORT != '' && $result->countrySHORT != '-')
			{
				$sql = "UPDATE `#__newsletter_mailing_recipient_actions`
						SET 
							countrySHORT=" . $database->quote( $result->countrySHORT ) . ",
							countryLONG=" . $database->quote( $result->countryLONG ) . ",
							ipREGION=" . $database->quote( $result->ipREGION ) . ",
							ipCITY=" . $database->quote( $result->ipCITY ) . ",
							ipLATITUDE=" . $database->quote( $result->ipLATITUDE ) . ",
							ipLONGITUDE=" . $database->quote( $result->ipLONGITUDE ) . "
						WHERE id=" . $database->quote( $action->id );
				$database->setQuery( $sql );
				$database->query();
			}
		}
		
		return true;
	}
}

