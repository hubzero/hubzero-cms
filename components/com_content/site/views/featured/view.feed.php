<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Frontpage View class
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since		1.5
 */
class ContentViewFeatured extends JViewLegacy
{
	function display($tpl = null)
	{
		// Parameters
		$app       = JFactory::getApplication();
		$doc       = Document::instance();
		$params    = $app->getParams();
		$feedEmail = Config::get('feed_email', 'author');
		$siteEmail = Config::get('mailfrom');

		$doc->link = Route::url('index.php?option=com_content&view=featured');

		// Get some data from the model
		Request::setVar('limit', Config::get('feed_limit'));
		$categories = JCategories::getInstance('Content');

		$rows = $this->get('Items');
		foreach ($rows as $row)
		{
			// strip html from feed item title
			$title = $this->escape($row->title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			// Compute the article slug
			$row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;

			// Url link to article
			$link = Route::url(ContentHelperRoute::getArticleRoute($row->slug, $row->catid, $row->language));

			// Get row fulltext
			$db = JFactory::getDBO();
			$query = 'SELECT' .$db->quoteName('fulltext'). 'FROM #__content WHERE id ='.$row->id;
			$db->setQuery($query);
			$row->fulltext = $db->loadResult();

			$description = ($params->get('feed_summary', 0) ? $row->introtext.$row->fulltext : $row->introtext);
			$author      = $row->created_by_alias ? $row->created_by_alias : $row->author;

			// Load individual item creator class
			$item = new \Hubzero\Document\Type\Feed\Item();
			$item->title   = $title;
			$item->link    = $link;
			$item->date    = $row->publish_up;
			$item_category = $categories->get($row->catid);

			$item->category   = array();
			$item->category[] = Lang::txt('JFEATURED'); // All featured articles are categorized as "Featured"

			for ($item_category = $categories->get($row->catid); $item_category !== null; $item_category = $item_category->getParent())
			{
				// Only add non-root categories
				if ($item_category->id > 1)
				{
					$item->category[] = $item_category->title;
				}
			}

			$item->author = $author;
			if ($feedEmail == 'site')
			{
				$item->authorEmail = $siteEmail;
			}
			elseif ($feedEmail === 'author')
			{
				$item->authorEmail = $row->author_email;
			}

			// Add readmore link to description if introtext is shown, show_readmore is true and fulltext exists
			if (!$params->get('feed_summary', 0) && $params->get('feed_show_readmore', 0) && $row->fulltext)
			{
				$description .= '<p class="feed-readmore"><a target="_blank" href ="' . $item->link . '">'.Lang::txt('COM_CONTENT_FEED_READMORE').'</a></p>';
			}

			// Load item description and add div
			$item->description = '<div class="feed-description">'.$description.'</div>';

			// Loads item info into rss array
			$doc->addItem($item);
		}
	}
}
