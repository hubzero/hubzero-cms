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

class NewsletterControllerNewsletter extends Hubzero_Controller
{
	/**
	 * Dependency check
	 *
	 * @return    void
	 */
	private function dependencyCheck()
	{
		$sql = "SELECT * FROM `jos_cron_jobs` WHERE `plugin`=" . $this->database->quote( 'newsletter' ) . " AND `event`=" . $this->database->quote( 'processMailings' );
		$this->database->setQuery( $sql );
		$sendMailingsCronJob = $this->database->loadObject();
		
		//if we dont have an object create new cron job
		if (!is_object($sendMailingsCronJob))
		{
			return false;
		}
		
		//is the cron job turned off
		if (is_object($sendMailingsCronJob) && $sendMailingsCronJob->state == 0)
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Display all newsletters task
	 *
	 * @return 	void
	 */
	public function displayTask()
	{
		//dependency check
		if (!$this->dependencyCheck())
		{
			//show missing dependency layout
			$this->view->setLayout('dependency');
			$this->view->display();
			return;
		}
		
		//set layout
		$this->view->setLayout('display');
		
		//instantiate newsletter campaign object
		$newsletterNewsletter = new NewsletterNewsletter( $this->database );
		
		//get list of all newsletter campaigns
		$this->view->newsletters = $newsletterNewsletter->getNewsletters();
		
		//get any templates that exist
		$newsletterTemplate = new NewsletterTemplate( $this->database );
		$this->view->templates = $newsletterTemplate->getTemplates();
		
		//get jquery plugin & parse params
		$jqueryPlugin = JPluginHelper::getPlugin('system', 'jquery');
		$jqueryPluginParams = new JParameter( $jqueryPlugin->params );
		
		//add jquery if we dont have the jquery plugin enabled or not active on admin
		if (!JPluginHelper::isEnabled('system', 'jquery') || !$jqueryPluginParams->get('activateAdmin'))
		{
			$document =& JFactory::getDocument();
			$document->addScript( DS . 'media' . DS . 'system' . DS . 'js' . DS . 'jquery.js' );
			$document->addScript( DS . 'media' . DS . 'system' . DS . 'js' . DS . 'jquery.noconflict.js' );
			$document->addScript( DS . 'media' . DS . 'system' . DS . 'js' . DS . 'jquery.fancybox.js' );
			$document->addStylesheet( DS . 'media' . DS . 'system' . DS . 'css' . DS . 'jquery.fancybox.css' );
			$document->addScript( 'components/com_newsletter/assets/js/newsletter.jquery.js' );
		}
		
		// Set any errors if we have any
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		
		// Output the HTML
		$this->view->display();
	}
	
	
	/**
	 * Add newsletter task - directs to editTask()
	 *
	 * @return 	void
	 */
	public function addTask()
	{
		$this->editTask( 'add' );
	}
	
	
	/**
	 * Edit newsletter task
	 *
	 * @return 	void
	 */
	public function editTask( $task = 'edit' )
	{
		//force layout (for when redirecting back to edit from saveTask())
		$this->view->setLayout('edit');
		
		//instantiate newsletter object
		$this->view->newsletter->id 			= null;
		$this->view->newsletter->alias 			= null;
		$this->view->newsletter->name 			= null;
		$this->view->newsletter->template 		= null;
		$this->view->newsletter->issue 			= null;
		$this->view->newsletter->date 			= null;
		$this->view->newsletter->sent 			= null;
		$this->view->newsletter->type 			= null;
		$this->view->newsletter->tracking		= 1;
		$this->view->newsletter->published 		= null;
		$this->view->newsletter->created 		= null;
		$this->view->newsletter->created_by 	= null;
		$this->view->newsletter->modified 		= null;
		$this->view->newsletter->modified_by 	= null;
		$this->view->newsletter->params			= null;
		
		//default primary and secondary stories to null
		$this->view->newsletter_primary 		= null;
		$this->view->newsletter_secondary 		= null;
		
		//get any templates that exist
		$newsletterTemplate = new NewsletterTemplate( $this->database );
		$this->view->templates = $newsletterTemplate->getTemplates();
		
		//get the request vars
		$ids = JRequest::getVar("id", array());
		$id = (isset($ids[0])) ? $ids[0] : null;
		
		if ($task == 'add')
		{
			$id = null;
		}
		
		//are we editing
		if ($id)
		{
			$newsletterNewsletter = new NewsletterNewsletter( $this->database );
			$this->view->newsletter = $newsletterNewsletter->getNewsletters( $id );
			
			//get primary stories
			$newsletterPrimaryStory = new NewsletterPrimaryStory( $this->database );
			$this->view->newsletter_primary = $newsletterPrimaryStory->getStories( $id );
			$this->view->newsletter_primary_highest_order = $newsletterPrimaryStory->_getCurrentHighestOrder( $id );
			
			//get secondary stories
			$newsletterSecondaryStory = new NewsletterSecondaryStory( $this->database );
			$this->view->newsletter_secondary = $newsletterSecondaryStory->getStories( $id );
			$this->view->newsletter_secondary_highest_order = $newsletterSecondaryStory->_getCurrentHighestOrder( $id );
			
			//get mailing lists
			$newsletterMailinglist = new NewsletterMailinglist( $this->database );
			$this->view->mailingLists = $newsletterMailinglist->getLists();
		}
		
		//are we passing newsletter object from saveTask()?
		if ($this->newsletter)
		{
			$this->view->newsletter = $this->newsletter;
		}
		
		//check if we have any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		
		//set vars for view
		$this->view->config = $this->config;
		
		// Output the HTML
		$this->view->display();
	}
	
	public function applyTask()
	{
		$this->saveTask( $apply = true );
	}
	
	
	/**
	 * Save campaign task
	 *
	 * @return 	void
	 */
	public function saveTask( $apply = false )
	{
		//get post
		$newsletter = JRequest::getVar("newsletter", array(), 'post', 'ARRAY', JREQUEST_ALLOWHTML);
		
		//make sure we have valid alias
		if ($newsletter['alias'])
		{
			$newsletter['alias'] = str_replace(" ", "", strtolower($newsletter['alias']));
		}
		else
		{
			$newsletter['alias'] = str_replace(" ", "", strtolower($newsletter['name']));
		}
		
		//get unique newsletter name
		$newsletter['alias'] = $this->_getUniqueNewsletterAlias($newsletter['alias'], $newsletter['id']);
		
		//instantiate campaign object
		$newsletterNewsletter = new NewsletterNewsletter( $this->database );
		
		//do we need to set the created and created_by
		if (!isset($newsletter['id']))
		{
			//update the modified info
			$newsletter['created'] 		= date("Y-m-d H:i:s");
			$newsletter['created_by'] 	= $this->juser->get('id');
		}
		else
		{
			$newsletterNewsletter->load( $newsletter['id'] );
		}
		
		//did we have params
		if (isset($newsletter['params']))
		{
			//load previous params
			$params = new JParameter( $newsletterNewsletter->params );
			
			//set from name
			if (isset($newsletter['params']['from_name']))
			{
				$params->set('from_name', $newsletter['params']['from_name']);
			}
			
			//set from address
			if (isset($newsletter['params']['from_address']))
			{
				$params->set('from_address', $newsletter['params']['from_address']);
			}
			
			//set reply-to name
			if (isset($newsletter['params']['replyto_name']))
			{
				$params->set('replyto_name', $newsletter['params']['replyto_name']);
			}
			
			//set reply-to address
			if (isset($newsletter['params']['replyto_address']))
			{
				$params->set('replyto_address', $newsletter['params']['replyto_address']);
			}
			
			//newsletter params to string
			$newsletter['params'] = $params->toString();
		}
		
		//update the modified info
		$newsletter['modified'] 		= date("Y-m-d H:i:s");
		$newsletter['modified_by'] 		= $this->juser->get('id');
		
		//save campaign
		if (!$newsletterNewsletter->save( $newsletter ))
		{
			$this->newsletter 					= new stdClass;
			$this->newsletter->id 				= $newsletterNewsletter->id;
			$this->newsletter->alias 			= $newsletterNewsletter->alias;
			$this->newsletter->name 			= $newsletterNewsletter->name;
			$this->newsletter->issue 			= $newsletterNewsletter->issue;
			$this->newsletter->type 			= $newsletterNewsletter->type;
			$this->newsletter->template 		= $newsletterNewsletter->template;
			$this->newsletter->published		= $newsletterNewsletter->published;
			$this->newsletter->sent 			= $newsletterNewsletter->sent;
			$this->newsletter->content			= $newsletterNewsletter->content;
			$this->newsletter->tracking			= $newsletterNewsletter->tracking;
			$this->newsletter->created			= $newsletterNewsletter->created;
			$this->newsletter->created_by		= $newsletterNewsletter->created_by;
			$this->newsletter->modified			= $newsletterNewsletter->modified;
			$this->newsletter->modified_by		= $newsletterNewsletter->modified_by;
			$this->newsletter->params			= $newsletterNewsletter->params;
			
			//set the id so we can pick up the stories
			JRequest::setVar('id', array($this->newsletter->id));
			
			$this->setError( $newsletterNewsletter->getError() );
			$this->editTask();
			return;
		}
		else
		{
			//set success message
			$this->_message = JText::_('Campaign Successfully Saved');
			
			//redirect back to campaigns list
			$this->_redirect = 'index.php?option=com_newsletter&controller=newsletter';
			
			//if we just created campaign go back to edit form so we can add content
			if (!$newsletter['id'] || $apply)
			{
				$this->_redirect = 'index.php?option=com_newsletter&controller=newsletter&task=edit&id[]=' . $newsletterNewsletter->id;
			}
		}
	}
	
	
	/**
	 * Duplicate newsletters
	 *
	 * @return 	void
	 */
	public function duplicateTask()
	{
		//get the request vars
		$ids = JRequest::getVar("id", array());
		
		//make sure we have ids
		if (isset($ids) && count($ids) > 0)
		{
			//delete each newsletter
			foreach ($ids as $id)
			{
				//instantiate newsletter object
				$newsletterNewsletter = new NewsletterNewsletter( $this->database );
				$newsletterNewsletter->duplicate( $id );
			}
		}
		
		//set success message
		$this->_message = JText::_('Newsletter(s) successfully duplicated.');
		
		//redirect back to campaigns list
		$this->_redirect = 'index.php?option=com_newsletter&controller=newsletter';
	}
	
	
	/**
	 * Delete Task
	 *
	 * @return 	void
	 */
	public function deleteTask()
	{
		//get the request vars
		$ids = JRequest::getVar("id", array());
		
		//make sure we have ids
		if (isset($ids) && count($ids) > 0)
		{
			//delete each newsletter
			foreach ($ids as $id)
			{
				//instantiate newsletter object
				$newsletterNewsletter = new NewsletterNewsletter( $this->database );
				$newsletterNewsletter->load( $id );
				
				//mark as deleted
				$newsletterNewsletter->deleted = 1;
				
				//save campaign marking as deleted
				if (!$newsletterNewsletter->save($newsletterNewsletter))
				{
					$this->setError('Unable to delete selected newsletters.');
					$this->displayTask();
					return;
				}
			}
		}
		
		//set success message
		$this->_message = JText::_('Newsletter(s) successfully deleted.');
		
		//redirect back to campaigns list
		$this->_redirect = 'index.php?option=com_newsletter&controller=newsletter';
	}
	
	
	/**
	 * Publish newsletter task
	 *
	 * @return 	void
	 */
	public function publishTask()
	{
		$this->togglePublishedStateTask( 1 );
	}
	
	
	/**
	 * Unpublish newsletter task
	 *
	 * @return 	void
	 */
	public function unpublishTask()
	{
		$this->togglePublishedStateTask( 0 );
	}
	
	
	/**
	 * Toggle published state for newsletter
	 *
	 * @return 	void
	 */
	private function togglePublishedStateTask( $publish = 1 )
	{
		//get the request vars
		$ids = JRequest::getVar("id", array());
		
		//make sure we have ids
		if (isset($ids) && count($ids) > 0)
		{
			//delete each newsletter
			foreach ($ids as $id)
			{
				//instantiate newsletter object
				$newsletterNewsletter = new NewsletterNewsletter( $this->database );
				$newsletterNewsletter->load( $id );
				
				//mark as deleted
				$newsletterNewsletter->published = $publish;
				
				//save campaign marking as deleted
				if (!$newsletterNewsletter->save($newsletterNewsletter))
				{
					$this->setError('Unable to delete selected newsletters.');
					$this->displayTask();
					return;
				}
			}
		}
		
		
		//set success message
		$msg = ($publish) ? 'Newsletter(s) successfully published.' : 'Newsletter(s) successfully unpublished.';
		$this->_message = JText::_( $msg );
		
		//redirect back to campaigns list
		$this->_redirect = 'index.php?option=com_newsletter&controller=newsletter';
	}
	
	
	public function previewTask()
	{
		//get the request vars
		$id = JRequest::getInt("id", 0);
		
		//get the newsletter
		$newsletterNewsletter = new NewsletterNewsletter( $this->database );
		$newsletterNewsletter->load( $id );
		
		//build newsletter for displaying preview
		echo $newsletterNewsletter->buildNewsletter( $newsletterNewsletter );
	}
	
	
	/**
	 * Display send test newsletter form
	 *
	 * @return 	void
	 */
	public function sendTestTask()
	{
		//set layout
		$this->view->setLayout('test');
		
		//get the request vars
		$ids = JRequest::getVar("id", array());
		$id = (isset($ids[0])) ? $ids[0] : null;
		
		//make sure we have an id
		if (!$id)
		{
			$this->setError('You must select a newsletter to send a test mailing.');
			$this->displayTask();
			return;
		}
		
		//get the newsletter
		$newsletterNewsletter = new NewsletterNewsletter( $this->database );
		$this->view->newsletter = $newsletterNewsletter->getNewsletters( $id );
		
		//check if we have any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}
	
	
	/**
	 * Send test newsletter
	 *
	 * @return 	void
	 */
	public function doSendTestTask()
	{
		//vars needed for test sending
		$goodEmails = array();
		$badEmails = array();
		
		//get request vars
		$emails 		= JRequest::getVar('emails', '');
		$newsletterId 	= JRequest::getInt('nid', 0);
		
		//parse emails
		$emails = array_filter(array_map("strtolower", array_map("trim", explode(",", $emails))));
		
		//make sure we have valid email addresses
		foreach ($emails as $k => $email)
		{
			if (filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				if (count($goodEmails) <= 4)
				{
					$goodEmails[] = $email;
				}
				else
				{
					$badEmails[] = $email;
				}
			}
			else
			{
				$badEmails[] = $email;
			}
		}
		
		//instantiate newsletter campaign object & load campaign
		$newsletterNewsletter = new NewsletterNewsletter( $this->database );
		$newsletterNewsletter->load( $newsletterId );
		
		//build newsletter for sending
		$newsletterNewsletterContent = $newsletterNewsletter->buildNewsletter( $newsletterNewsletter );
		
		//send campaign
		$this->_send( $newsletterNewsletter, $newsletterNewsletterContent, $goodEmails, $newsletterMailinglist = null, $sendingTest = true );
		
		//get application
		$application =& JFactory::getApplication();
		
		//do we have good emails to tell user about
		if (count($goodEmails))
		{
			$message = $newsletterNewsletter->name . ' Sent to: <br /><br />' . implode("<br />", $goodEmails);
			$application->enqueueMessage($message,'success');
		}
		
		//do we have any bad emails to tell user about
		if (count($badEmails))
		{
			$message = $newsletterNewsletter->name . ' NOT SENT to: <br /><br />' . implode("<br />", $badEmails);
			$application->enqueueMessage($message,'error');
		}
		
		//redirect after sent
		$this->_redirect = 'index.php?option=com_newsletter&controller=newsletter';
	}
	
	
	/**
	 * Display send newsletter form
	 *
	 * @return 	void
	 */
	public function sendNewsletterTask()
	{
		//set layout
		$this->view->setLayout('send');
		
		//get the request vars
		$ids = JRequest::getVar("id", array());
		$id = (isset($ids[0])) ? $ids[0] : null;
		
		//make sure we have an id
		if (!$id)
		{
			$this->setError('You must select a newsletter to send a test mailing.');
			$this->displayTask();
			return;
		}
		
		//get the newsletter
		$newsletterNewsletter = new NewsletterNewsletter( $this->database );
		$this->view->newsletter = $newsletterNewsletter->getNewsletters( $id );
		
		//get newsletter mailing lists
		$newsletterMailinglist = new NewsletterMailinglist( $this->database );
		$this->view->mailinglists = $newsletterMailinglist->getLists();
		
		//get jquery plugin & parse params
		$jqueryPlugin = JPluginHelper::getPlugin('system', 'jquery');
		$jqueryPluginParams = new JParameter( $jqueryPlugin->params );
		
		//add jquery if we dont have the jquery plugin enabled or not active on admin
		if (!JPluginHelper::isEnabled('system', 'jquery') || !$jqueryPluginParams->get('activateAdmin'))
		{
			$document =& JFactory::getDocument();
			$document->addScript( DS . 'media' . DS . 'system' . DS . 'js' . DS . 'jquery.js' );
			$document->addScript( DS . 'media' . DS . 'system' . DS . 'js' . DS . 'jquery.noconflict.js' );
			$document->addScript( DS . 'media' . DS . 'system' . DS . 'js' . DS . 'jquery.ui.js' );
			$document->addStylesheet( DS . 'media' . DS . 'system' . DS . 'css' . DS . 'jquery.ui.css' );
			$document->addScript( 'components/com_newsletter/assets/js/newsletter.jquery.js' );
		}
		
		//check if we have any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		
		//vars for view
		$this->view->database = $this->database;
		
		// Output the HTML
		$this->view->display();
	}
	
	
	/**
	 * Send Newsletter
	 *
	 * @return 	void
	 */
	public function doSendNewsletterTask()
	{
		//get request vars
		$newsletterId 	= JRequest::getInt('nid', 0);
		$mailinglistId 	= JRequest::getInt('mailinglist', '-1');
		
		//instantiate newsletter campaign object & load campaign
		$newsletterNewsletter = new NewsletterNewsletter( $this->database );
		$newsletterNewsletter->load( $newsletterId );
		
		//check to make sure we have an object
		if (!is_object($newsletterNewsletter) || $newsletterNewsletter->name == '')
		{
			$this->setError('We unable to locate a newsletter to send.');
			$this->displayTask();
			return;
		}
		
		//make sure it wasnt deleted
		if ($newsletterNewsletter->deleted == 1)
		{
			$this->setError('The newsletter you are attempting to send has been previously deleted.');
			$this->displayTask();
			return;
		}
		
		//get emails based on mailing list
		$newsletterMailinglist = new NewsletterMailinglist( $this->database );
		$filters = array( 'status' => 'active' );
		$emails = array_keys( $newsletterMailinglist->getListEmails( $mailinglistId, 'email', $filters ) );
		
		//make sure we have emails
		if (count($emails) < 1)
		{
			$this->setError('The newsletter mailing list you are attempting to send the newsletter to, has no members. Please add emails to the mailing list and try again.');
			$this->displayTask();
			return;
		}
		
		//build newsletter for sending
		$newsletterNewsletterContent = $newsletterNewsletter->buildNewsletter( $newsletterNewsletter );
		
		//ximport('Hubzer_Image_MozifyHelper');
		//$newsletterNewsletterContent = Hubzero_Image_MozifyHelper::mozifyHtml( $newsletterNewsletterContent, 5 );
		
		//send campaign
		$this->_send( $newsletterNewsletter, $newsletterNewsletterContent, $emails, $mailinglistId, $sendingTest = false );
		
		//mark campaign as sent
		$newsletterNewsletter->sent = 1;
		if ($newsletterNewsletter->save( $newsletterNewsletter ))
		{
			//set message for user
			$this->_message = JText::_($newsletterNewsletter->name . ' has or will be sent to "' . count($emails) . '" members.');

			//redirect after sent
			$this->_redirect = 'index.php?option=com_newsletter&controller=newsletter';
		}
	}
	
	
	/**
	 * Send Newsletter
	 *
	 * @return 	void
	 */
	private function _send( $newsletter, $newsletterContent, $newsletterContacts, $newsletterMailinglist, $sendingTest = false )
	{
		//get site config
		$config = JFactory::getConfig();
		
		//set default mail from and reply-to names and addresses
		$defaultMailFromName 		= $config->getValue("sitename") . ' Newsletter';
		$defaultMailFromAddress 	= 'contact@' . $_SERVER['HTTP_HOST'];
		$defaultMailReplytoName 	= $config->getValue("sitename") . ' Newsletter - Do Not Reply';
		$defaultMailReplytoAddress 	= 'do-not-reply@' . $_SERVER['HTTP_HOST'];
		
		//get the config mail from and reply-to names and addresses
		$mailFromName 				= $this->config->get( 'newsletter_from_name', $defaultMailFromName );
		$mailFromAddress 			= $this->config->get( 'newsletter_from_address', $defaultMailFromAddress );
		$mailReplytoName 			= $this->config->get( 'newsletter_replyto_name', $defaultMailReplytoName );
		$mailReplytoAddress 		= $this->config->get( 'newsletter_replyto_address', $defaultMailReplytoAddress );
		
		//parse newsletter specific emails
		$params 					= new JParameter( $newsletter->params );
		$mailFromName 				= $params->get('from_name', $mailFromName);
		$mailFromAddress 			= $params->get('from_address', $mailFromAddress);
		$mailReplytoName 			= $params->get('replyto_name', $mailReplytoName);
		$mailReplytoAddress 		= $params->get('replyto_address', $mailReplytoAddress);
		
		//set final mail from and reply-to
		$mailFrom 					= '"' . $mailFromName . '" <' . $mailFromAddress . '>';
		$mailReplyTo 				= '"' . $mailReplytoName . '" <' . $mailReplytoAddress . '>';
		
		//set subject and body
		$mailSubject 	= ($newsletter->name) ? $newsletter->name : 'Your '.$config->getValue("sitename").'.org Newsletter';
		$mailBody		= $newsletterContent;
		
		//set mail headers
		$mailHeaders  = "MIME-Version: 1.0" . "\r\n";
		$mailHeaders .= "Content-type: text/" . $newsletter->type ."; charset=iso-8859-1" . "\r\n";
		$mailHeaders .= "From: {$mailFrom}" . "\r\n";
		$mailHeaders .= "Reply-To: {$mailReplyTo}" . "\r\n";
		
		//set mail priority
		$mailHeaders .= "X-Priority: 3" . "\r\n";
		$mailHeaders .= "X-MSMail-Priority: Normal" . "\r\n";
		$mailHeaders .= "Importance: Normal\n";
		
		//set extra headers
		$mailHeaders .= "X-Mailer: PHP/" . phpversion()  . "\r\n";
		$mailHeaders .= "X-Component: " . $this->_option . "\r\n";
		$mailHeaders .= "X-Component-Object: Campaign Mailing" . "\r\n";
		$mailHeaders .= "X-Component-ObjectId: {{CAMPAIGN_MAILING_ID}}" . "\r\n";
		$mailHeaders .= "List-Unsubscribe: <mailto:{{UNSUBSCRIBE_MAILTO_LINK}}>, <{{UNSUBSCRIBE_LINK}}>";
		
		//set mail args
		$mailArgs = '-f hubmail-bounces@' . $_SERVER['HTTP_HOST'];
		
		//are we sending test mailing
		if ($sendingTest)
		{
			foreach ($newsletterContacts as $contact)
			{
				$mailSubject = '[SENDING TEST] - ' . $mailSubject;
				mail($contact, $mailSubject, $mailBody, $mailHeaders, $mailArgs);
			}
			return true;
		}
		
		//get the scheduling
		$scheduler = JRequest::getInt('scheduler', 1);
		
		if ($scheduler == '1')
		{
			$scheduledDate = date('Y-m-d H:i:s');
		}
		else
		{
			$schedulerDate = JRequest::getVar('scheduler_date', '');
			$schedulerHour = JRequest::getVar('scheduler_date_hour', '00');
			$schedulerMinute = JRequest::getVar('scheduler_date_minute', '00');
			$schedulerMeridian = JRequest::getVar('scheduler_date_meridian', 'AM');
			
			//make sure we have at least the date or we use now
			if (!$schedulerDate)
			{
				$scheduledDate = date('Y-m-d H:i:s');
			}
			
			//break apart parts of date
			$schedulerDateParts = explode('/', $schedulerDate);
			
			//make sure its in 24 time 
			if ($schedulerMeridian == 'pm') 
			{
				$schedulerHour += 12;
			}
			
			//build scheduled time
			$scheduledTime = $schedulerDateParts[2] . '-' . $schedulerDateParts[0] . '-' . $schedulerDateParts[1];
			$scheduledTime .= ' ' . $schedulerHour . ':' . $schedulerMinute . ':00';
			$scheduledDate = date('Y-m-d H:i:s', strtotime( $scheduledTime ));
		}
		
		//create mailing object
		$mailing 			= new stdClass;
		$mailing->nid 		= $newsletter->id;
		$mailing->lid 		= $newsletterMailinglist;
		$mailing->subject 	= $mailSubject;
		$mailing->body 		= $mailBody;
		$mailing->headers 	= $mailHeaders;
		$mailing->args 		= $mailArgs;
		$mailing->tracking  = $newsletter->tracking;
		$mailing->date		= $scheduledDate;
		
		//save mailing object
		$newsletterMailing = new NewsletterMailing( $this->database );
		if (!$newsletterMailing->save( $mailing ))
		{
			$this->setError('Unable to send newsletter.');
			$this->sendNewsletterTask();
			return;
		}
		
		//loop through each email and send mail
		foreach ($newsletterContacts as $contact)
		{
			//create mailing recipient object
			$mailingRecipient 				= new stdClass;
			$mailingRecipient->mid 			= $newsletterMailing->id;
			$mailingRecipient->email 		= $contact;
			$mailingRecipient->status 		= 'queued';
			$mailingRecipient->date_added 	= date('Y-m-d H:i:s');
			
			//save mailing recipient object
			$newsletterMailingRecipient = new NewsletterMailingRecipient( $this->database );
			$newsletterMailingRecipient->save( $mailingRecipient );
		}
		
		return true;
	}
	
	
	/**
	 * Get Unique newsletter alias
	 * 
	 * @param     $alias    Newsletter Alias
	 * @param     $id       Newsletter Id
	 * 
	 * @return    string
	 */
	private function _getUniqueNewsletterAlias( $alias, $id )
	{
		$sql = "SELECT `alias` FROM `#__newsletters` WHERE `id` NOT IN (".$this->database->quote($id).")";
		$this->database->setQuery( $sql );
		$aliases = $this->database->loadResultArray();
		
		//if we have another newsletter with this alias lets add random #
		if (in_array($alias, $aliases))
		{
			$alias .= rand(0, 100);
		}
		
		return $alias;
	}
}
