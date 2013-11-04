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

class NewsletterControllerMailing extends Hubzero_Controller
{
	/**
	 * Display Newsletter Mailings
	 *
	 * @return 	void
	 */
	public function displayTask()
	{
		//set layout
		$this->view->setLayout('display');
		
		//instantiate newsletter mailing object
		$newsletterMailing = new NewsletterMailing( $this->database );
		$this->view->mailings = $newsletterMailing->getMailingNewsletters();
		
		//add the number sent 
		foreach ($this->view->mailings as $mailing)
		{
			$newsletterMailingRecipient = new NewsletterMailingRecipient( $this->database );
			$mailing->emails_sent = count( $newsletterMailingRecipient->getRecipients( $mailing->mailing_id, 'sent' ) );
			$mailing->emails_total = count( $newsletterMailingRecipient->getRecipients( $mailing->mailing_id ) );
		}
		
		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		
		// Output the HTML
		$this->view->display();
	}
	
	
	/**
	 * View Tracking Information task
	 *
	 * @return 	void
	 */
	public function trackingTask()
	{
		//set layout
		$this->view->setLayout('tracking');
		
		//get request vars
		$ids = JRequest::getVar('id', array());
		$id = (isset($ids)) ? $ids[0] : null;
		
		//instantiate newsletter mailing object
		$newsletterMailing = new NewsletterMailing( $this->database );
		$mailing = $newsletterMailing->getMailings( $id );
		
		//get mailing recipients
		$newsletterMailingRecipient = new NewsletterMailingRecipient( $this->database );
		$this->view->recipients = $newsletterMailingRecipient->getRecipients( $id, 'sent' );
		
		//instantiate newsletter object
		$newsletterNewsletter = new NewsletterNewsletter( $this->database );
		$newsletterNewsletter->load( $mailing->nid );
		
		//make sure we are supposed to be tracking
		if (!$newsletterNewsletter->tracking)
		{
			$this->setError('Tracking information is not being captured with this newsletter mailing.');
			$this->displayTask();
			return;
		}
		
		//get bounces
		$sql = "SELECT * FROM jos_email_bounces 
				WHERE component='com_newsletter' 
				AND object=" . $this->database->quote('Campaign Mailing') . " 
				AND object_id=" . $this->database->quote( $id );
		$this->database->setQuery( $sql );
		$this->view->bounces = $this->database->loadObjectList();
		
		//new mailing recipient action object
		$newsletterMailingRecipientAction = new NewsletterMailingRecipientAction( $this->database );
		
		//get opens, clicks, forwards, and prints
		$this->view->opens    = $newsletterMailingRecipientAction->getMailingActions( $id, 'open' );
		$this->view->forwards = $newsletterMailingRecipientAction->getMailingActions( $id, 'forward' );
		$this->view->prints   = $newsletterMailingRecipientAction->getMailingActions( $id, 'print' );
		
		//get opens geo
		$this->view->opensGeo = $this->getOpensGeoTask( $id );
		
		//get clicks and process
		$clicks = $newsletterMailingRecipientAction->getMailingActions( $id, 'click' );
		$this->view->clicks = array();
		foreach ($clicks as $click)
		{
			//get click action
			$clickAction = json_decode( $click->action_vars );
			$this->view->clicks[$clickAction->url] = (isset($this->view->clicks[$clickAction->url])) ? $this->view->clicks[$clickAction->url] + 1 : 1;
		}
		
		//get jquery plugin & parse params
		$jqueryPlugin = JPluginHelper::getPlugin('system', 'jquery');
		$jqueryPluginParams = new JParameter( $jqueryPlugin->params );
		
		//add jquery if we dont have the jquery plugin enabled or not active on admin
		if (!JPluginHelper::isEnabled('system', 'jquery') || !$jqueryPluginParams->get('activateAdmin'))
		{
			$base = str_replace('/administrator', '', rtrim(JURI::getInstance()->base(true), '/'));
			$document =& JFactory::getDocument();
			$document->addScript( $base . DS . 'media' . DS . 'system' . DS . 'js' . DS . 'jquery.js' );
			$document->addScript( $base . DS . 'media' . DS . 'system' . DS . 'js' . DS . 'jquery.noconflict.js' );
			
			$document->addScript( $base . '/media/system/js/jvectormap/jquery.jvectormap.min.js' );
			$document->addScript( $base . '/media/system/js/jvectormap/maps/jquery.jvectormap.us.js' );
			$document->addScript( $base . '/media/system/js/jvectormap/maps/jquery.jvectormap.world.js' );
			
			$document->addScript( 'components/com_newsletter/assets/js/newsletter.jquery.js?v=' . time() );
			$document->addStyleSheet( 'components/com_newsletter/assets/css/newsletter.css' );
		}
		
		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		
		// Output the HTML
		$this->view->display();
	}
	
	
	public function getOpensGeoTask( $mailingId = null )
	{
		//are we getting through ajax
		$no_html = JRequest::getInt('no_html', 0);
		
		//get the mailing id
		if (is_null($mailingId))
		{
			$mailingId = JRequest::getVar('mailingid', 0);
		}
		
		$states = array(
			"alabama" => 'al',
			"alaska" => 'ak',
			"arizona" => 'az',
			"arkansas" => 'ar',
			"california" => 'ca',
			"colorado" => 'co',
			"connecticut" => 'ct',
			"delaware" => 'de',
			"florida" => 'fl',
			"georgia" => 'ga',
			"hawaii" => 'hi',
			"idaho" => 'id',
			"illinois" => 'il',
			"indiana" => 'in',
			"iowa" => 'ia',
			"kansas" => 'ks',
			"kentucky" => 'ky',
			"louisiana" => 'la',
			"maine" => 'me',
			"maryland" => 'md',
			"massachusetts" =>' ma',
			"michigan" => 'mi',
			"minnesota" => 'mn',
			"mississippi" => 'ms',
			"missouri" => 'mo',
			"montana" => 'mt',
			"nebraska" => 'ne',
			"nevada" => 'nv',
			"new hampshire" => 'nh',
			"new jersey" => 'nj',
			"new mexico" => 'nm',
			"new york" => 'ny',
			"north carolina" => 'nc',
			"north dakota" => 'nd',
			"ohio" =>  'oh',
			"oklahoma" => 'ok',
			"oregon" => 'or',
			"pennsylvania" => 'pa',
			"rhode island" => 'ri',
			"south carolina" => 'sc',
			"south dakota" => 'sd',
			"tennessee" => 'tn',
			"texas" => 'tx',
			"utah" => 'ut',
			"vermont" => 'vt',
			"virginia" => 'va',
			"washington" => 'wa',
			"west virginia" => 'wv',
			"wisconsin" => 'wi',
			"wyoming" => 'wy'
		);
		
		//new mailing recipient action object
		$newsletterMailingRecipientAction = new NewsletterMailingRecipientAction( $this->database );
		
		//get opens
		$opens = $newsletterMailingRecipientAction->getMailingActions( $mailingId, 'open' );
		
		//get country and state data
		$countryGeo = array();
		$statesGeo = array();
		foreach ($opens as $open)
		{
			$country = ($open->countrySHORT) ? strtolower($open->countrySHORT) : 'undetermined';
			$state = ($open->ipREGION) ? 'us-' . strtolower($states[strtolower($open->ipREGION)]) : 'undetermined';
			
			$countryGeo[$country] = (isset($countryGeo[$country])) ? $countryGeo[$country] + 1 : 1;
			$statesGeo[$state] = (isset($statesGeo[$state])) ? $statesGeo[$state] + 1 : 1;
		}
		
		//build return object
		$geo = array(
			'country' => $countryGeo,
			'state' => $statesGeo
		);
		
		//return
		if ($no_html) 
		{
			echo json_encode($geo);
			exit();
		}
		else
		{
			return $geo;
		}
	}
	
	/**
	 * Stop sending campaign or deleted scheduled
	 *
	 * @return 	void
	 */
	public function stopTask()
	{
		//get request vars
		$ids = JRequest::getVar('id', array());
		$id = (isset($ids)) ? $ids[0] : null;
		
		//instantiate newsletter mailing object
		$newsletterMailing = new NewsletterMailing( $this->database );
		$newsletterMailing->load( $id );
		
		//mark as deleted
		$newsletterMailing->deleted = 1;
		
		//save
		if (!$newsletterMailing->save( $newsletterMailing ))
		{
			$this->setError( $newsletterMailing->getError() );
			$this->displayTask();
			return;
		}
		
		//inform and redirect
		$this->_message = 'You have successfully stopped the mailing from being sent';
		$this->_redirect = 'index.php?option=com_newsletter&controller=mailing';
	}
	
	
	/**
	 * Cancel on Mailings Controller
	 *
	 * @return 	void
	 */
	public function cancelTask()
	{
		$this->_redirect = 'index.php?option=com_newsletter&controller=mailing';
	}
	
	
	/**
	 * Process Queued Emails - TO BE MOVED TO CRON
	 *
	 * @return 	void
	 */
	public function processTask()
	{
		//limit to process
		$limit = 15;
		
		//var to hold processed
		$processed 		= array();
		$notProcessed 	= array();
		
		//get all queued mailing recipients
		$sql = "SELECT nmr.id AS mailing_recipientid, nm.id AS mailingid, nm.nid AS newsletterid, nm.lid AS mailinglistid, nmr.email, nm.subject, nm.body, nm.headers, nm.args, nm.tracking
				FROM #__newsletter_mailings AS nm, #__newsletter_mailing_recipients AS nmr
				WHERE nm.id=nmr.mid
				AND nmr.status='queued'
				AND nm.deleted=0
				AND UTC_TIMESTAMP() >= nm.date
				ORDER BY nmr.date_added
				LIMIT {$limit}";
		$this->database->setQuery( $sql );
		$queuedEmails = $this->database->loadObjectList();
		
		//make sure we have emails to send out
		if (!$queuedEmails || count($queuedEmails) < 1)
		{
			$this->setError('There are no emails to process at this time.');
			$this->displayTask();
			return;
		}
		
		//loop through each newsletter and mail
		foreach ($queuedEmails as $queuedEmail)
		{
			//get tracking & unsubscribe token
			$emailToken = Hubzero_Newsletter_Helper::generateMailingToken( $queuedEmail );
			
			//is tracking enabled
			if ($queuedEmail->tracking)
			{
				$queuedEmail->body = Hubzero_Newsletter_Helper::addTrackingToEmailMessage( $queuedEmail->body, $emailToken );
			}
			
			//create unsubscribe link
			$unsubscribeMailtoLink 	= '';
			$unsubscribeLink 		= 'https://' . $_SERVER['SERVER_NAME'] . '/newsletter/unsubscribe?e=' . $queuedEmail->email . '&t=' . $emailToken;
			
			//add unsubscribe link - placeholder & in header (must do after adding click tracking!!)
			$queuedEmail->body 		= str_replace("{{UNSUBSCRIBE_LINK}}", $unsubscribeLink, $queuedEmail->body);
			$queuedEmail->headers 	= str_replace("{{UNSUBSCRIBE_LINK}}", $unsubscribeLink, $queuedEmail->headers);
			$queuedEmail->headers 	= str_replace("{{UNSUBSCRIBE_MAILTO_LINK}}", $unsubscribeMailtoLink, $queuedEmail->headers);
			
			//add mailing id to header
			$queuedEmail->headers = str_replace("{{CAMPAIGN_MAILING_ID}}", $queuedEmail->mailingid, $queuedEmail->headers);
			
			//mail message
			if (mail($queuedEmail->email, $queuedEmail->subject, $queuedEmail->body, $queuedEmail->headers, $queuedEmail->args))
			{
				//add to process email array
				$processed[] = $queuedEmail->email;
				
				//load recipient object
				$newsletterMailingRecipient = new NewsletterMailingRecipient( $this->database );
				$newsletterMailingRecipient->load( $queuedEmail->mailing_recipientid );
				
				//mark as sent and save
				$newsletterMailingRecipient->status		= 'sent';
				$newsletterMailingRecipient->date_sent 	= date('Y-m-d H:i:s');
				$newsletterMailingRecipient->save( $newsletterMailingRecipient );
			}
			else
			{
				$notProcessed[] = $queuedEmail->email;
			}
		}
		
		//get the application
		$application = JFactory::getApplication();
		
		//did we have any successful mailings
		if (count($processed) > 0)
		{
			$application->enqueueMessage('The following emails have been processed and sent: <br /><br />' . implode('<br />', $processed), 'success');
		}
		
		//did we have any unsuccessful mailings
		if (count($notProcessed) > 0)
		{
			$application->enqueueMessage('The following emails have been NOT processed: <br /><br />' . implode('<br />', $notProcessed), 'error');
		}
		
		//redirect back to newsletters
		$this->_redirect = 'index.php?option=com_newsletter&controller=mailing';
	}
	
	
	/**
	 * Process Click & Open IP's - TO BE MOVED TO CRON
	 *
	 * @return 	void
	 */
	public function processIpsTask()
	{
		//import geo db
		ximport('Hubzero_Geo');
		
		//get deo db
		$geodatabase = Hubzero_Geo::getGeoDBO();
		
		//get actions
		$newsletterMailingRecipientAction = new NewsletterMailingRecipientAction( $this->database );
		$unconvertedActions = $newsletterMailingRecipientAction->getUnconvertedActions();
		
		//check to make sure we have actions to convert
		if (count($unconvertedActions) < 1)
		{
			$this->_messageType = 'warning';
			$this->_message = 'There are no click/open tracking IP addresses to process at this time.';
			$this->_redirect = 'index.php?option=com_newsletter&controller=mailing';
			return;
		}
		
		//convert all unconverted actions
		foreach ($unconvertedActions as $action)
		{
			//get geo information
			$geodatabase->setQuery("SELECT * FROM ipcitylatlong WHERE INET_ATON('{$action->ip}') BETWEEN ipFROM and ipTO");
			$result = $geodatabase->loadObject();
			
			//if we got a valid result lets update our action with location info
			if (is_object($result) && $result->countrySHORT != '' && $result->countrySHORT != '-')
			{
				$sql = "UPDATE jos_newsletter_mailing_recipient_actions
						SET 
							countrySHORT=" . $this->database->quote( $result->countrySHORT ) . ",
							countryLONG=" . $this->database->quote( $result->countryLONG ) . ",
							ipREGION=" . $this->database->quote( $result->ipREGION ) . ",
							ipCITY=" . $this->database->quote( $result->ipCITY ) . ",
							ipLATITUDE=" . $this->database->quote( $result->ipLATITUDE ) . ",
							ipLONGITUDE=" . $this->database->quote( $result->ipLONGITUDE ) . "
						WHERE id=" . $this->database->quote( $action->id );
				$this->database->setQuery( $sql );
				$this->database->query();
			}
		}
		
		//redirect after converting
		$this->_redirect = 'index.php?option=com_newsletter&controller=mailing';
	}
}