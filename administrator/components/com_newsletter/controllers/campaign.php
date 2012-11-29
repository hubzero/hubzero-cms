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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

class NewsletterControllerCampaign extends Hubzero_Controller
{
	
	public function displayTask()
	{
		//database
		$database =& JFactory::getDBO();
		
		//
		$nc = new NewsletterCampaign( $database );
		$newsletters = $nc->getCampaign();
		
		$this->view->newsletters = $newsletters;
		
		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}
	
	public function editTask()
	{
		$id = JRequest::getInt("id", 0);
		
		//database
		$database =& JFactory::getDBO();
		
		$this->view->campaign = array(
			'id' => null,
			'name' => null,
			'template' => null,
			'issue' => null,
			'date' => null,
			'sent' => null
		);
		
		$this->view->campaignPrimary = null;
		$this->view->campaignSecondary = null;
		
		//get any templates that exist
		$nt = new NewsletterTemplate( $database );
		$this->view->templates = $nt->getTemplate();
		
		//are we editing
		if($id)
		{
			$nc = new NewsletterCampaign( $database );
			$campaign = $nc->getCampaign( $id );
			$this->view->campaign = array(
				'id' => $campaign->id,
				'name' => $campaign->name,
				'template' => $campaign->template,
				'issue' => $campaign->issue,
				'date' => $campaign->date,
				'sent' => $campaign->sent
			);
			
			//get primary stories
			$nps = new NewsletterPrimaryStory( $database );
			$this->view->campaignPrimary = $nps->getStories( $id );
			
			//get secondary stories
			$nss = new NewsletterSecondaryStory( $database );
			$this->view->campaignSecondary = $nss->getStories( $id );
		}
		
		//check if we have any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}
	
	public function saveTask()
	{
		$campaign = JRequest::getVar("campaign", array(), 'post');
		                                          
		$database = JFactory::getDBO();
		$nc = new NewsletterCampaign( $database );
		
		if($nc->save($campaign))
		{
			$this->_redirect = 'index.php?option=com_newsletter&controller=campaign';
			$this->_message = JText::_('Campaign Successfully Saved');
		}
	}
	
	public function cancel()
	{
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
	}

	////////////////////////////////////////////////////////////////
	
	public function sendTask()
	{
		$database = JFactory::getDBO();
		$sql = "SELECT DISTINCT email FROM #__xprofiles WHERE emailConfirmed = '1' AND mailPreferenceOption > '0'";
		$database->setQuery($sql);
		$emails = $database->loadResultArray();
		
		//build newsletter
		$database = JFactory::getDBO();
		$nc = new NewsletterCampaign( $database );
		$campaign = JRequest::getVar("campaign", array(), "post");
		$newsletter = $nc->buildNewsletter( $campaign );
		
		//send the newsletter to the
		$this->send($newsletter, $emails, true);
	}
	
	//-----
	
	public function sendTestTask()
	{
		//get request vars
		$emails = JRequest::getVar("test");
		$emails = array_map("trim", explode(",", $emails));
		$campaign = JRequest::getVar("campaign", array(), "post");
		
		//build newsletter
		$database = JFactory::getDBO();
		$nc = new NewsletterCampaign( $database );
		$newsletter = $nc->buildNewsletter( $campaign );
		
		//$sql = "INSERT INTO jos_test(`id`,`text`) VALUES(1,'".mysql_real_escape_string($newsletter)."')";
		//$database->setQuery($sql);
		//$database->query();
		//die();
		
		//send the newsletter to the
		$this->send($newsletter, $emails);
		
		//redirect after sent
		$this->_redirect = 'index.php?option=com_newsletter&controller=campaign';
		$this->_message = JText::_('Test Campaign Sent to: ' . implode(", ", $emails));
	}
	
	//-----
	
	private function send( $content, $emails, $logging = false )
	{
		$config = JFactory::getConfig();
		
		//$log_file = "/var/log/newsletter/newsletter-mailing-".date("Y_m_d_H_i_s").".txt";
		//$log = fopen($log_file, 'w+') or die("can't open file");
		
		$from = '"'.$config->getValue("sitename").' Newsletter" <contact@nanohub.org>';
		$subject = 'Your '.$config->getValue("sitename").'.org Newsletter';
		$body = $content;
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "From: $from" . "\r\n";
		$headers .= "Reply-To: $from". "\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		
		foreach($emails as $email)
		{
			mail($email, $subject, $body, $headers);
			if($logging)
			{
				$line = $email."\t".date("Y-m-d H:i:s")."\t".'Newsletter'."\t"."\n";
				//fwrite($log,$line);
			}
		}
		
		
		//fclose($log); 
	}
	
	
}
