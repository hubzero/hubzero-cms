<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Tables;

/**
 * Table class for newsletters
 */
class Newsletter extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  $db  Database Object
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__newsletters', 'id', $db);
	}

	/**
	 * Newsletter save check
	 *
	 * @return  boolean
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
	 * @param   integer  $id  Newsletter Id
	 * @return  boolean
	 */
	public function duplicate($id)
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
		$this->load($id);

		//remove the classes id so that it saves as a copy
		$this->id = 0;

		//add copy to the name
		$this->name .= ' (copy)';
		$this->alias .= 'copy';

		// mark unpublished
		$this->published = 0;
		$this->sent      = 0;

		// set new created & modified date/by
		$this->created     = \Date::toSql();
		$this->created_by  = \User::get('id');
		$this->modified    = \Date::toSql();
		$this->modified_by = \User::get('id');

		//save the copy
		$this->save($this);

		//get all primary stories
		$newsletterPrimaryStory = new PrimaryStory($this->_db);
		$primaryStories = $newsletterPrimaryStory->getStories($id);

		//get all secondary stories
		$newsletterSecondaryStory = new SecondaryStory($this->_db);
		$secondaryStories = $newsletterSecondaryStory->getStories($id);

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
				$newsletterPrimaryStory = new PrimaryStory($this->_db);
				$newsletterPrimaryStory->save($primaryStory);
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
				$newsletterSecondaryStory = new SecondaryStory($this->_db);
				$newsletterSecondaryStory->save($secondaryStory);
			}
		}

		return true;
	}

	/**
	 * Get Newsletters
	 *
	 * @param   integer  $id             Newsletter Id
	 * @param   boolean  $publishedOnly
	 * @return  array
	 */
	public function getNewsletters($id = null, $publishedOnly = false)
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
	 * @param   object   $campaign              Campaign Object
	 * @param   boolean  $stripHtmlAndBodyTags  Strip out <html> & <body> tags?
	 * @return  string   Campaign HTML
	 */
	public function buildNewsletter($campaign, $stripHtmlAndBodyTags = false)
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
			$newsletterTemplate = new Template($this->_db);
			$newsletterPrimaryStory = new PrimaryStory($this->_db);
			$newsletterSecondaryStory = new SecondaryStory($this->_db);

			//get the campaign template
			$newsletterTemplate->load($campaign->template);
			$campaignTemplate = $newsletterTemplate->template;

			//get primary & secondary colors
			$primaryTitleColor   = ($newsletterTemplate->primary_title_color) ? $newsletterTemplate->primary_title_color : '#000000';
			if (strlen($primaryTitleColor) <= 7 && !strstr($primaryTitleColor, ';'))
			{
				$primaryTitleColor = 'font-size:20px;font-weight:bold;color:'.$primaryTitleColor.';font-family:arial;line-height:100%;margin-bottom:10px;';
			}
			$primaryTextColor    = ($newsletterTemplate->primary_text_color) ? $newsletterTemplate->primary_text_color : '#444444';
			if (strlen($primaryTextColor) <= 7 && !strstr($primaryTextColor, ';'))
			{
				$primaryTextColor = 'font-size:14px;font-weight:normal;color:'.$primaryTextColor.';font-family:arial;margin-bottom:50px;';
			}

			$secondaryTitleColor = ($newsletterTemplate->secondary_title_color) ? $newsletterTemplate->secondary_title_color : '#666666';
			if (strlen($secondaryTitleColor) <= 7 && !strstr($secondaryTitleColor, ';'))
			{
				$secondaryTitleColor = 'font-size:15px;font-weight:bold;color:'.$secondaryTitleColor.';font-family:arial;line-height:150%;';
			}
			$secondaryTextColor  = ($newsletterTemplate->secondary_text_color) ? $newsletterTemplate->secondary_text_color : '#999999';
			if (strlen($secondaryTextColor) <= 7 && !strstr($secondaryTextColor, ';'))
			{
				$secondaryTextColor = 'font-size:12px;color:'.$secondaryTextColor.';font-family:arial;';
			}

			//get and format primary stories
			$campaignPrimaryStories = '';
			$primaryStories = $newsletterPrimaryStory->getStories($campaign->id);
			foreach ($primaryStories as $pStory)
			{
				$campaignPrimaryStories .= '<span style="display:block;page-break-inside:avoid;'.$primaryTitleColor.'">';
				$campaignPrimaryStories .= $pStory->title;
				$campaignPrimaryStories .= '</span>';
				$campaignPrimaryStories .= '<span style="display:block;page-break-inside:avoid;'.$primaryTextColor.'">';
				$campaignPrimaryStories .= $pStory->story;

				//do we have a readmore link
				if ($pStory->readmore_link)
				{
					$readmore_title = ($pStory->readmore_title) ? $pStory->readmore_title : 'Read More &rsaquo;';
					$campaignPrimaryStories .= "<br /><br /><a href=\"{$pStory->readmore_link}\" target=\"\">{$readmore_title}</a>";
				}

				$campaignPrimaryStories .= '</span>';
			}

			//get secondary stories
			$campaignSecondaryStories = '<br /><br />';
			$secondaryStories = $newsletterSecondaryStory->getStories($campaign->id);
			foreach ($secondaryStories as $sStory)
			{
				$campaignSecondaryStories .= '<span style="display:block;page-break-inside:avoid;'.$secondaryTitleColor.'">';
				$campaignSecondaryStories .= $sStory->title;
				$campaignSecondaryStories .= '</span>';
				$campaignSecondaryStories .= '<span style="display:block;page-break-inside:avoid;'.$secondaryTextColor.'">';
				$campaignSecondaryStories .= $sStory->story;

				//do we have a readmore link
				if ($sStory->readmore_link)
				{
					$readmore_title = ($sStory->readmore_title) ? $sStory->readmore_title : 'Read More &rsaquo;';
					$campaignSecondaryStories .= "<br /><br /><a href=\"{$sStory->readmore_link}\" target=\"\">{$readmore_title}</a>";
				}

				$campaignSecondaryStories .= '</span>';
				$campaignSecondaryStories .= '<br /><br />';
			}
		}

		//get the hub
		$hub = $_SERVER['SERVER_NAME'];

		//build link to newsletters for email
		$link = 'https://' . $hub . '/newsletter/' . $campaign->alias;

		//replace placeholders in template
		$campaignParsed = str_replace("{{LINK}}", $link, $campaignTemplate);
		$campaignParsed = str_replace("{{ALIAS}}", $campaign->alias, $campaignParsed);
		$campaignParsed = str_replace("{{TITLE}}", $campaign->name, $campaignParsed);
		$campaignParsed = str_replace("{{ISSUE}}", $campaign->issue, $campaignParsed);
		$campaignParsed = str_replace("{{PRIMARY_STORIES}}", $campaignPrimaryStories, $campaignParsed);
		$campaignParsed = str_replace("{{SECONDARY_STORIES}}", $campaignSecondaryStories, $campaignParsed);
		$campaignParsed = str_replace("{{COPYRIGHT}}", date("Y"), $campaignParsed);
		$campaignParsed = str_replace('src="/site', 'src="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $campaignParsed);

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
	 * @param   object  $campaign  Campaign Object
	 * @return  string  Campaign text
	 */
	public function buildNewsletterPlainTextPart($campaign)
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
		$newsletterPrimaryStory   = new PrimaryStory($this->_db);
		$newsletterSecondaryStory = new SecondaryStory($this->_db);

		// get primary & secondary stories by campaign
		$primaryStories   = $newsletterPrimaryStory->getStories($campaign->id);
		$secondaryStories = $newsletterSecondaryStory->getStories($campaign->id);

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