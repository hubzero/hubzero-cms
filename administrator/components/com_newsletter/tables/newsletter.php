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

class NewsletterNewsletter extends JTable
{
	/**
	 * Campaign ID
	 * 
	 * @var int(11)
	 */
	var $id 				= NULL;
	
	/**
	 * Campaign Alias
	 * 
	 * @var varchar(150)
	 */
	var $alias 				= NULL;
	
	/**
	 * Campaign Name
	 * 
	 * @var varchar(150)
	 */
	var $name				= NULL;
	
	/**
	 * Campaign Issue
	 * 
	 * @var int(11)
	 */
	var $issue				= NULL;
	
	/**
	 * Campaign Type - HTML or Plain Text
	 * 
	 * @var varchar(50)
	 */
	var $type				= NULL;
	
	/**
	 * Campaign Template
	 * 
	 * @var int(11)
	 */
	var $template 			= NULL;
	
	/**
	 * Campaign Public
	 * 
	 * @var int(11)
	 */
	var $published 			= NULL;
	
	/**
	 * Campaign Sent?
	 * 
	 * @var int(11)
	 */
	var $sent 				= NULL;
	
	/**
	 * Campaign Content
	 * 
	 * @var text
	 */
	var $html_content    	= NULL;

	/**
	 * Campaign Plain Text Content
	 * 
	 * @var text
	 */
	var $plain_content	    = NULL;
	
	/**
	 * Campaign Tracking
	 * 
	 * @var int(11)
	 */
	var $tracking 			= NULL;
	
	/**
	 * Campaign Created Date
	 * 
	 * @var datetime
	 */
	var $created 			= NULL;
	
	/**
	 * Campaign Created By
	 * 
	 * @var int
	 */
	var $created_by 		= NULL;
	
	/**
	 * Campaign Modified Date
	 * 
	 * @var datetime
	 */
	var $modified 			= NULL;
	
	/**
	 * Campaign Modified By
	 * 
	 * @var datetime
	 */
	var $modified_by		= NULL;
	
	/**
	 * Campaign Deleted?
	 * 
	 * @var int(11)
	 */
	var $deleted 			= NULL;
	
	/**
	 * Campaign Params
	 * 
	 * @var text
	 */
	var $params 			= NULL;
	
	
	/**
	 * Newsletter Campaign object constructor
	 * 
	 * @param 	$db		Database Object
	 * @return 	void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__newsletters', 'id', $db );
	}
	
	
	/**
	 * Newsletter save check
	 * 
	 * @return 	boolean
	 */
	public function check()
	{
		if (trim($this->name) == '')
		{
			$this->setError('Newsletter must have a name.');
			return false;
		}
		
		if (trim($this->template) == '')
		{
			$this->setError('Newsletter must have a template or choose to override with content and template.');
			return false;
		}
		
		return true;
	}
	
	
	/**
	 * Duplicate newsletter
	 * 
	 * @param 	$id		Newsletter Id
	 * @return 	boolean
	 */
	public function duplicate( $id )
	{
		//check to make sure we passed in id
		// if not use this classes id
		if (!$id)
		{
			$id = $this->id;
		}
		
		//check to make sure again
		if (!$id)
		{
			$this->setError('You must supply an newsletter id to duplicate.');
			return false;
		}
		
		//load newsletter we want to duplicate
		$this->load( $id );
		
		//remove the classes id so that it saves as a copy
		unset($this->id);
		
		//add copy to the name
		$this->name .= ' (copy)';
		$this->alias .= 'copy';

		// mark unpublished
		$this->published = 0;
		$this->sent      = 0;

		// set new created & modified date/by
		$this->created     = JFactory::getDate()->toSql();
		$this->created_by  = JFactory::getUser()->get('id');
		$this->modified    = JFactory::getDate()->toSql();
		$this->modified_by = JFactory::getUser()->get('id');
		
		//save the copy
		$this->save( $this );
		
		//get all primary stories
		$newsletterPrimaryStory = new NewsletterPrimaryStory( $this->_db );
		$primaryStories = $newsletterPrimaryStory->getStories( $id );
		
		//get all secondary stories
		$newsletterSecondaryStory = new NewsletterSecondaryStory( $this->_db );
		$secondaryStories = $newsletterSecondaryStory->getStories( $id );
		
		//duplicate primary stories for new newsletter
		if (count($primaryStories) > 0)
		{
			foreach ($primaryStories as $primaryStory)
			{
				//remove id
				unset($primaryStory->id);
				
				//set the new id
				$primaryStory->nid = $this->id;
				
				//save the story
				$newsletterPrimaryStory = new NewsletterPrimaryStory( $this->_db );
				$newsletterPrimaryStory->save( $primaryStory );
			}
		}
		
		//duplicate secondary stories for new newsletter
		if (count($secondaryStories) > 0)
		{
			foreach ($secondaryStories as $secondaryStory)
			{
				//remove id
				unset($secondaryStory->id);
				
				//set the new id
				$secondaryStory->nid = $this->id;
				
				//save the story
				$newsletterSecondaryStory = new NewsletterSecondaryStory( $this->_db );
				$newsletterSecondaryStory->save( $secondaryStory );
			}
		}
		
		return true;
	}
	
	
	/**
	 * Get Newsletters
	 * 
	 * @param 	$id		Newsletter Id
	 * @return 	array
	 */
	public function getNewsletters( $id = null, $publishedOnly = false )
	{
		$sql = "SELECT * FROM {$this->_tbl} WHERE deleted=0";
		
		if ($publishedOnly)
		{
			$sql .= " AND published=1";
		}
		
		if ($id)
		{
			$sql .= " AND id=".$id;
			$this->_db->setQuery($sql);
			return $this->_db->loadObject();
		}
		else
		{
			$sql .= " ORDER BY created DESC";
			$this->_db->setQuery($sql);
			return $this->_db->loadObjectList();
		}
	}
	
	
	/**
	 * Get Current Newsletter
	 * 
	 * @return 	void
	 */
	public function getCurrentNewsletter()
	{
		$sql = "SELECT * FROM {$this->_tbl} WHERE published=1 AND deleted=0 ORDER BY created DESC LIMIT 1";
		$this->_db->setQuery($sql);
		return $this->_db->loadObject();
	}
	
	
	/**
	 * Build Newsletter Content
	 * 
	 * @param 	$campaign				Campaign Object
	 * @param 	$stripHtmlAndBodyTags	Strip out <html> & <body> tags?
	 * @return 	Campaign HTML
	 */
	public function buildNewsletter( $campaign, $stripHtmlAndBodyTags = false )
	{
		//are we overriding content with template vs using stories?
		if ($campaign->template == '-1')
		{
			$campaignTemplate = $campaign->html_content;
			$campaignPrimaryStories = '';
			$campaignSecondaryStories = '';
		}
		else
		{
			//instantiate objects
			$newsletterTemplate = new NewsletterTemplate( $this->_db );
			$newsletterPrimaryStory = new NewsletterPrimaryStory( $this->_db );
			$newsletterSecondaryStory = new NewsletterSecondaryStory( $this->_db );
			
			//get the campaign template
			$newsletterTemplate->load( $campaign->template );
			$campaignTemplate = $newsletterTemplate->template;
			
			//get primary & secondary colors
			$primaryTitleColor 		= ($newsletterTemplate->primary_title_color) ? $newsletterTemplate->primary_title_color : '#000000';
			$secondaryTitleColor 	= ($newsletterTemplate->secondary_title_color) ? $newsletterTemplate->secondary_title_color : '#666666';
			$primaryTextColor 		= ($newsletterTemplate->primary_text_color) ? $newsletterTemplate->primary_text_color : '#444444';
			$secondaryTextColor 	= ($newsletterTemplate->secondary_text_color) ? $newsletterTemplate->secondary_text_color : '#999999';
			
			//get and format primary stories
			$campaignPrimaryStories = "";
			$primaryStories = $newsletterPrimaryStory->getStories( $campaign->id ); 
			foreach ($primaryStories as $pStory)
			{
				$campaignPrimaryStories .= "<span style=\"display:block;page-break-inside:avoid;font-size:20px;font-weight:bold;color:".$primaryTitleColor.";font-family:arial;line-height:100%;margin-bottom:10px;\">";
				$campaignPrimaryStories .= $pStory->title;
				$campaignPrimaryStories .= "</span>";
				$campaignPrimaryStories .= "<span style=\"display:block;page-break-inside:avoid;font-size:14px;font-weight:normal;color:".$primaryTextColor.";font-family:arial;margin-bottom:50px;\">";
				$campaignPrimaryStories .= $pStory->story;
				
				//do we have a readmore link
				if ($pStory->readmore_link)
				{
					$readmore_title = ($pStory->readmore_title) ? $pStory->readmore_title : 'Read More &rsaquo;';
					$campaignPrimaryStories .= "<br /><br /><a href=\"{$pStory->readmore_link}\" target=\"\">{$readmore_title}</a>";
				}
				
				$campaignPrimaryStories .= "</span>";
			}
			
			//get secondary stories
			$campaignSecondaryStories = "<br /><br />";
			$secondaryStories = $newsletterSecondaryStory->getStories( $campaign->id );
			foreach ($secondaryStories as $sStory)
			{
				$campaignSecondaryStories .= "<span style=\"display:block;page-break-inside:avoid;font-size:15px;font-weight:bold;color:".$secondaryTitleColor.";font-family:arial;line-height:150%;\">";
				$campaignSecondaryStories .= $sStory->title;
				$campaignSecondaryStories .= "</span>";
				$campaignSecondaryStories .= "<span style=\"display:block;page-break-inside:avoid;font-size:12px;color:".$secondaryTextColor.";\">";
				$campaignSecondaryStories .= $sStory->story;
				
				//do we have a readmore link
				if ($sStory->readmore_link)
				{
					$readmore_title = ($sStory->readmore_title) ? $sStory->readmore_title : 'Read More &rsaquo;';
					$campaignSecondaryStories .= "<br /><br /><a href=\"{$sStory->readmore_link}\" target=\"\">{$readmore_title}</a>";
				}
				
				$campaignSecondaryStories .= "</span>";
				$campaignSecondaryStories .= "<br /><br />";
			}
		}
		
		//get the hub
		$hub = $_SERVER['SERVER_NAME'];
		
		//build link to newsletters for email
		$link = 'https://' . $hub . DS . 'newsletter' . DS . $campaign->alias;
		
		//replace placeholders in template
		$campaignParsed = str_replace("{{LINK}}", $link, $campaignTemplate);
		$campaignParsed = str_replace("{{ALIAS}}", $campaign->alias, $campaignParsed);
		$campaignParsed = str_replace("{{TITLE}}", $campaign->name, $campaignParsed);
		$campaignParsed = str_replace("{{ISSUE}}", $campaign->issue, $campaignParsed);
		$campaignParsed = str_replace("{{PRIMARY_STORIES}}", $campaignPrimaryStories, $campaignParsed);
		$campaignParsed = str_replace("{{SECONDARY_STORIES}}", $campaignSecondaryStories, $campaignParsed);
		$campaignParsed = str_replace("{{COPYRIGHT}}", date("Y"), $campaignParsed);
		
		//replace .org, .com., .net, .edu 's
		// if ($campaign->type == 'html')
		// {
		// 	$campaignParsed = str_replace(".org", "&#8203;.org", $campaignParsed);
		// 	$campaignParsed = str_replace(".com", "&#8203;.com", $campaignParsed);
		// 	$campaignParsed = str_replace(".net", "&#8203;.net", $campaignParsed);
		// 	$campaignParsed = str_replace(".edu", "&#8203;.edu", $campaignParsed);
		// }
		
		//do we want to strip <html> & <body> tags
		if ($stripHtmlAndBodyTags)
		{
			$campaignParsed = preg_replace('/<html[^>]*>/', '', $campaignParsed);
			$campaignParsed = preg_replace('/<body[^>]*>/', '', $campaignParsed);
			$campaignParsed = str_replace('</body>', '', $campaignParsed);
			$campaignParsed = str_replace('</html>', '', $campaignParsed);
		}
		
		return $campaignParsed;
	}

	/**
	 * Build Newsletter Content, Plain Text Part
	 * 
	 * @param 	$campaign				Campaign Object
	 * @return 	Campaign text
	 */
	public function buildNewsletterPlainTextPart( $campaign )
	{
		if ($campaign->plain_content != '')
		{
			return $campaign->plain_content;
		}

		// add campaign name 
		$title  = str_repeat('==', 40) . "\r\n\r\n";
		$title .= $campaign->name . "\r\n\r\n"; 
		$title .= str_repeat('==', 40);
		$title .= "\r\n\r\n\r\n";

		// vars to hold story content
		$primary   = array();
		$secondary = array();

		// add story divider
		$storySeparator  = "\r\n\r\n\r\n";
		$storySeparator .= str_repeat('--', 40);
		$storySeparator .= "\r\n\r\n\r\n";

		// add section divider
		$sectionSeparator  = "\r\n\r\n\r\n\r\n";
		$sectionSeparator .= str_repeat('+++', 40);
		$sectionSeparator .= "\r\n\r\n\r\n\r\n";

		// instantiate primary & secondary store objects
		$newsletterPrimaryStory   = new NewsletterPrimaryStory( $this->_db );
		$newsletterSecondaryStory = new NewsletterSecondaryStory( $this->_db );
		
		// get primary & secondary stories by campaign
		$primaryStories   = $newsletterPrimaryStory->getStories( $campaign->id ); 
		$secondaryStories = $newsletterSecondaryStory->getStories( $campaign->id );
		
		// add primary stories
		foreach ($primaryStories as $primaryStory)
		{
			// create story
			$story  = strtoupper($primaryStory->title) . "\r\n\r\n";
			$story .= trim(preg_replace('/<br[^>]*>/', "\r\n", $primaryStory->story));

			//do we have a readmore link
			if ($primaryStory->readmore_link)
			{
				$story .= "\r\n" . $primaryStory->readmore_link;
			}

			// add to the primary stories
			$primary[] = strip_tags($story);
		}

		// add secondary stories
		foreach ($secondaryStories as $secondaryStory)
		{
			// create story
			$story  = strtoupper($secondaryStory->title) . "\r\n\r\n";
			$story .= trim(preg_replace('/<br[^>]*>/', "\r\n", $secondaryStory->story));

			//do we have a readmore link
			if ($secondaryStory->readmore_link)
			{
				$story .= "\r\n" . $secondaryStory->readmore_link;
			}

			// add to the primary stories
			$secondary[] = strip_tags($story);
		}

		// put it all together
		$plainText = $title . implode($storySeparator, $primary) . $sectionSeparator . implode($storySeparator, $secondary);

		return $plainText;
	}
}