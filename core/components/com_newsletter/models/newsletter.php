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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Models;

use Hubzero\Database\Relational;
use Date;
use User;

require_once __DIR__ . DS . 'template.php';
require_once __DIR__ . DS . 'primary.php';
require_once __DIR__ . DS . 'secondary.php';

/**
 * Newsletter model
 */
class Newsletter extends Relational
{
	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'name'     => 'notempty',
		'template' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'modified',
		'modified_by',
		'alias',
		'plain_content'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Generates automatic alias value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : $data['title']);
		$alias = str_replace(' ', '-', $alias);
		$alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));

		$alias = $this->uniqueAlias($alias, (isset($data['id']) ? $data['id'] : 0));

		return $alias;
	}

	/**
	 * Get Unique newsletter alias
	 *
	 * @param   string   $alias
	 * @param   integer  $id
	 * @return  string
	 */
	private function uniqueAlias($alias, $id)
	{
		$model = self::all()
			->whereEquals('alias', $alias);

		if ($id)
		{
			$model->where('id', '!=', $id);
		}

		$aliases = $model->total();

		if ($aliases > 0)
		{
			$alias .= rand(0, 100);
			$alias = $this->uniqueAlias($alias, $id);
		}

		return $alias;
	}

	/**
	 * Generates automatic created field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return (isset($data['id']) && $data['id'] ? Date::of('now')->toSql() : '0000-00-00 00:00:00');
	}

	/**
	 * Generates automatic modified by field value
	 *
	 * @param   array    $data  the data being saved
	 * @return  integer
	 */
	public function automaticModifiedBy($data)
	{
		return (isset($data['id']) && $data['id'] ? User::get('id') : 0);
	}

	/**
	 * Generates automatic plain_content value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticPlainContent($data)
	{
		// if no plain text was entered lets take the html content
		if (!isset($data['plain_content']))
		{
			$data['plain_content'] = '';
		}

		if (!$data['plain_content'] && isset($data['html_content']))
		{
			$data['plain_content'] = strip_tags($data['html_content']);
			$data['plain_content'] = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}\n/', '', $data['plain_content']);
		}

		// remove html from plain content
		$data['plain_content'] = strip_tags($data['plain_content']);

		return $data['plain_content'];
	}

	/**
	 * Defines a belongs to one relationship between newsletter and user
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Defines a belongs to one relationship between newsletter and user
	 *
	 * @return  object
	 */
	public function modifier()
	{
		return $this->belongsToOne('Hubzero\User\User', 'modified_by');
	}

	/**
	 * Defines a belongs to one relationship between template and newsletter
	 *
	 * @return  object
	 */
	public function template()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Template', 'template_id');
	}

	/**
	 * Get a list of primary stories
	 *
	 * @return  object
	 */
	public function primary()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Primary', 'nid');
	}

	/**
	 * Get a list of secondary stories
	 *
	 * @return  object
	 */
	public function secondary()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Secondary', 'nid');
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove comments
		foreach ($this->primary()->rows() as $primary)
		{
			if (!$primary->destroy())
			{
				$this->addError($primary->getError());
				return false;
			}
		}

		// Remove vote logs
		foreach ($this->secondary()->rows() as $secondary)
		{
			if (!$secondary->destroy())
			{
				$this->addError($secondary->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Get a list of secondary stories
	 *
	 * @return  object
	 */
	public static function current()
	{
		return self::all()
			->whereEquals('published', 1)
			->whereEquals('deleted', 0)
			->order('created', 'desc')
			->row();
	}

	/**
	 * Duplicate newsletter
	 *
	 * @return  boolean
	 */
	public function duplicate()
	{
		if (!$this->get('id'))
		{
			$this->addError('You must supply an newsletter id to duplicate.');
			return false;
		}

		$newsletter = self::blank()->set($this->toArray());

		// Add copy to the name
		$newsletter->set('id', null);
		$newsletter->set('name', $newsletter->get('name') . ' (copy)');
		$newsletter->set('alias', $newsletter->get('alias') . 'copy');

		// Mark unpublished
		$newsletter->set('published', 0);
		$newsletter->set('sent', 0);

		// Set new created & modified date/by
		$newsletter->set('created', Date::toSql());
		$newsletter->set('created_by', User::get('id'));
		$newsletter->set('modified', Date::toSql());
		$newsletter->set('modified_by', User::get('id'));

		// Save the copy
		if (!$newsletter->save())
		{
			$this->addError($newsletter->getError());
			return false;
		}

		foreach ($this->primaries as $primary)
		{
			$primary->set('nid', $newsletter->get('id'));

			if (!$primary->save())
			{
				$this->addError($primary->getError());
				return false;
			}
		}

		foreach ($this->secondaries as $secondary)
		{
			$secondary->set('nid', $newsletter->get('id'));

			if (!$secondary->save())
			{
				$this->addError($secondary->getError());
				return false;
			}
		}

		return true;
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
			//get the campaign template
			$newsletterTemplate = $campaign->template;
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
			$primaryStories = $campaign->primary;
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
			$secondaryStories = $campaign->secondary;
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

		// Handle the AUTOGEN sections
		if (preg_match_all("(\\{.*?\\}\\})", $campaignPrimaryStories, $matches) !== false)
		{
			foreach ($matches[0] as &$match)
			{
				// A field to hold HTML content for the section
				$html = '';

				// Hold onto the original token
				$originMatch = $match;

				// Perform some cleanup, stripping
				$match = ltrim($match, "{{");
				$match = rtrim($match, "}}");

				// Explode on the delimiter
				$parts = explode("_", $match);

				// Make sure we're doing "it" on the right token
				if ($parts[0] == "AUTOGEN")
				{
					// Get the content
					$enabledPlugins = \Event::trigger('newsletter.onGetEnabledDigests');

					// Ascertain the key, based on plugin ordering
					$key = array_keys($enabledPlugins, strtolower($parts[1]))[0];

					// Get the content for the desired plugin
					$content = \Event::trigger('newsletter.onGetLatest', array($parts[2]))[$key];

					// Apply the view template
					$view = new \Hubzero\Component\View(array());

					// Written emphatically, set the paths and whatnot
					$view->setName('storytemplates');
					$view->setLayout(strtolower($parts[3]));
					$view->setBasePath(\Component::path('com_newsletter') . DS . 'admin');

					// Pass the object through to the view
					$view->object = $content;
					$view->addTemplatePath(\Component::path('com_newsletter') . DS . 'admin' . DS . 'views' . DS . 'storytemplates' . DS . 'tmpl');

					// Oh, what's this a buffer hijack?
					ob_start();

					// Render the view within the buffer.
					$view->display();

					// Grab the buffer's content
					$html = ob_get_contents();

					// Clear and close the buffer
					ob_end_clean();

					// Do some string replacement on the original token.
					$campaignPrimaryStories = str_replace($originMatch, $html, $campaignPrimaryStories);
				}
			}
		}

		$campaignParsed = str_replace("{{PRIMARY_STORIES}}", $campaignPrimaryStories, $campaignParsed);
		$campaignParsed = str_replace("{{SECONDARY_STORIES}}", $campaignSecondaryStories, $campaignParsed);
		$campaignParsed = str_replace("{{COPYRIGHT}}", date("Y"), $campaignParsed);
		$campaignParsed = str_replace('src="/site', 'src="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $campaignParsed);

		// replace .org, .com., .net, .edu 's
		//if ($campaign->type == 'html')
		//{
		//	$campaignParsed = str_replace(".org", "&#8203;.org", $campaignParsed);
		//	$campaignParsed = str_replace(".com", "&#8203;.com", $campaignParsed);
		//	$campaignParsed = str_replace(".net", "&#8203;.net", $campaignParsed);
		//	$campaignParsed = str_replace(".edu", "&#8203;.edu", $campaignParsed);
		//}

		// Do we want to strip <html> & <body> tags
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

		// get primary & secondary stories by campaign
		$primaryStories   = $campaign->primary;
		$secondaryStories = $campaign->secondary;

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
