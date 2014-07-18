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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Controller class for dipslaying what's new
 */
class WhatsnewControllerResults extends \Hubzero\Component\SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->registerTask('feedrss', 'feed');

		parent::execute();
	}

	/**
	 * Display a list of new item's
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$menu = JFactory::getApplication()->getMenu()->getActive();
		if (!$menu)
		{
			$menu = new stdClass;
			$menu->params = '';
		}

		$menu->param = new JRegistry($menu->params);

		// Incoming
		$this->view->period = JRequest::getVar('period', $menu->param->get('period', 'month'));

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Paging variables
		$this->view->start = JRequest::getInt('limitstart', 0);
		$this->view->limit = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));

		// Get categories
		$areas = $this->_getAreas();

		// Was there a category passed in the querystring?
		$area = trim(JRequest::getWord('category', ''));

		// Check the search string for a category prefix
		if ($this->view->period != NULL)
		{
			$searchstring = strtolower($this->view->period);
			foreach ($areas as $c => $t)
			{
				$regexp = '/' . $c . ':/';
				if (strpos($searchstring, $c . ':') !== false)
				{
					// We found an active category
					// NOTE: this will override any category sent in the querystring
					$area = $c;
					// Strip it off the search string
					$searchstring = preg_replace($regexp, '', $searchstring);
					break;
				}
				// Does the category contain sub-categories?
				if (is_array($t) && !empty($t))
				{
					// It does - loop through them and perform the same check
					foreach ($t as $sc=>$st)
					{
						$regexp = '/' . $sc . ':/';
						if (strpos($searchstring, $sc . ':') !== false)
						{
							// We found an active category
							// NOTE: this will override any category sent in the querystring
							$area = $sc;
							// Strip it off the search string
							$searchstring = preg_replace($regexp, '', $searchstring);
							break;
						}
					}
				}
			}
			$this->view->period = trim($searchstring);
		}

		// Get the active category
		if ($area)
		{
			$activeareas = array($area);
		}
		else
		{
			$limit = 5;
			$activeareas = $areas;
		}

		// Load plugins
		JPluginHelper::importPlugin('whatsnew');
		$dispatcher = JDispatcher::getInstance();

		// Process the keyword for exact phrase matches, etc.
		$p = new WhatsnewPeriod($this->view->period);
		$p->process();

		// Get the search result totals
		$this->view->totals = $dispatcher->trigger(
			'onWhatsnew',
			array(
				$p,
				0,
				0,
				$activeareas
			)
		);

		$this->view->limit = ($this->view->limit == 0) ? 'all' : $this->view->limit;

		// Get the search results
		$this->view->results = $dispatcher->trigger(
			'onWhatsnew',
			array(
				$p,
				$this->view->limit,
				$this->view->start,
				$activeareas
			)
		);

		// Get the total results found (sum of all categories)
		$i = 0;
		$this->view->total = 0;
		$this->view->cats = array();
		foreach ($areas as $c => $t)
		{
			$this->view->cats[$i]['category'] = $c;

			// Do sub-categories exist?
			if (is_array($t) && !empty($t))
			{
				// They do - do some processing
				$this->view->cats[$i]['title'] = ucfirst($c);
				$this->view->cats[$i]['total'] = 0;
				$this->view->cats[$i]['_sub']  = array();
				$z = 0;
				// Loop through each sub-category
				foreach ($t as $s => $st)
				{
					// Ensure a matching array of totals exist
					if (is_array($this->view->totals[$i])
					 && !empty($this->view->totals[$i])
					 && isset($this->view->totals[$i][$z]))
					{
						// Add to the parent category's total
						$this->view->cats[$i]['total'] = $this->view->cats[$i]['total'] + $this->view->totals[$i][$z];
						// Get some info for each sub-category
						$this->view->cats[$i]['_sub'][$z]['category'] = $s;
						$this->view->cats[$i]['_sub'][$z]['title']    = stripslashes($st);
						$this->view->cats[$i]['_sub'][$z]['total']    = $this->view->totals[$i][$z];
					}
					$z++;
				}
			}
			else
			{
				// No sub-categories - this should be easy
				$this->view->cats[$i]['title'] = JText::_($t);
				$this->view->cats[$i]['total'] = (!is_array($this->view->totals[$i])) ? $this->view->totals[$i] : 0;
			}

			// Add to the overall total
			$this->view->total = $this->view->total + intval($this->view->cats[$i]['total']);
			$i++;
		}

		// Do we have an active area?
		$this->view->active = '';
		if (count($activeareas) == 1)
		{
			$this->view->active = $activeareas[0];
		}

		// Set the page title
		$this->view->title = JText::_(strtoupper($this->_option)) . ': ' . $this->_jtext($this->view->period);

		$document = JFactory::getDocument();
		$document->setTitle($this->view->title);

		// Set the pathway
		$pathway = JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		$pathway->addItem(
			$this->_jtext($this->view->period),
			'index.php?option=' . $this->_option . '&period=' . $this->view->period
		);

		// Build some options for the time period <select>
		$this->view->periodlist = array();
		$this->view->periodlist[] = JHTMLSelect::option('week', JText::_('COM_WHATSNEW_OPT_WEEK'));
		$this->view->periodlist[] = JHTMLSelect::option('month', JText::_('COM_WHATSNEW_OPT_MONTH'));
		$this->view->periodlist[] = JHTMLSelect::option('quarter', JText::_('COM_WHATSNEW_OPT_QUARTER'));
		$this->view->periodlist[] = JHTMLSelect::option('year', JText::_('COM_WHATSNEW_OPT_YEAR'));

		$thisyear = strftime("%Y",time());
		for ($y = $thisyear; $y >= 2002; $y--)
		{
			if (time() >= strtotime('10/1/' . $y))
			{
				$this->view->periodlist[] = JHTMLSelect::option($y, JText::_('COM_WHATSNEW_OPT_FISCAL_YEAR') . ' ' . $y);
			}
		}
		for ($y = $thisyear; $y >= 2002; $y--)
		{
			if (time() >= strtotime('01/01/' . $y))
			{
				$this->view->periodlist[] = JHTMLSelect::option('c_' . $y, JText::_('COM_WHATSNEW_OPT_CALENDAR_YEAR') . ' ' . $y);
			}
		}

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Generate an RSS feed
	 *
	 * @return     void
	 */
	public function feedTask()
	{
		include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');

		$app = JFactory::getApplication();

		// Set the mime encoding for the document
		$jdoc = JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		$doc = new JDocumentFeed;
		$doc->link = JRoute::_('index.php?option=' . $this->_option);

		// Incoming
		$period = JRequest::getVar('period', 'month');

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Paging variables
		$start = JRequest::getInt('limitstart', 0);
		$limit = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));

		// Get categories
		$areas = $this->_getAreas();

		// Was there a category passed in the querystring?
		$area = trim(JRequest::getWord('category', ''));

		// Check the search string for a category prefix
		if ($period != NULL)
		{
			$searchstring = strtolower($period);
			foreach ($areas as $c=>$t)
			{
				$regexp = '/' . $c . ':/';
				if (strpos($searchstring, $c . ':') !== false)
				{
					// We found an active category
					// NOTE: this will override any category sent in the querystring
					$area = $c;
					// Strip it off the search string
					$searchstring = preg_replace($regexp, '', $searchstring);
					break;
				}
				// Does the category contain sub-categories?
				if (is_array($t) && !empty($t))
				{
					// It does - loop through them and perform the same check
					foreach ($t as $sc=>$st)
					{
						$regexp = '/' . $sc . ':/';
						if (strpos($searchstring, $sc . ':') !== false)
						{
							// We found an active category
							// NOTE: this will override any category sent in the querystring
							$area = $sc;
							// Strip it off the search string
							$searchstring = preg_replace($regexp, '', $searchstring);
							break;
						}
					}
				}
			}
			$period = trim($searchstring);
		}

		// Get the active category
		if ($area)
		{
			$activeareas = array($area);
		}
		else
		{
			$limit = 5;
			$activeareas = $areas;
		}

		// Load plugins
		JPluginHelper::importPlugin('whatsnew');
		$dispatcher = JDispatcher::getInstance();

		// Process the keyword for exact phrase matches, etc.
		$p = new WhatsnewPeriod($period);
		$p->process();

		// Fetch results
		$results = $dispatcher->trigger(
			'onWhatsNew',
			array(
				$p,
				$limit,
				$start,
				$activeareas
			)
		);

		$jconfig = JFactory::getConfig();

		// Run through the array of arrays returned from plugins and find the one that returned results
		$rows = array();
		if ($results)
		{
			foreach ($results as $result)
			{
				if (is_array($result) && !empty($result))
				{
					$rows = $result;
					break;
				}
			}
		}

		// Build some basic RSS document information
		$doc->title  = $jconfig->getValue('config.sitename') . ' - ' . JText::_('COM_WHATSNEW_RSS_TITLE') . ': ' . $period;
		$doc->title .= ($area) ? ': ' . $area : '';
		$doc->description = JText::sprintf('COM_WHATSNEW_RSS_DESCRIPTION', $jconfig->getValue('config.sitename'));
		$doc->copyright   = JText::sprintf('COM_WHATSNEW_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category    = JText::_('COM_WHATSNEW_RSS_CATEGORY');

		// Start outputing results if any found
		if (count($rows) > 0)
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');

			foreach ($rows as $row)
			{
				// Prepare the title
				$title = strip_tags(stripslashes($row->title));
				$title = html_entity_decode($title);

				// URL link to article
				$row->href = DS . ltrim($row->href, DS);
				if (strstr($row->href, 'view'))
				{
					// tests to see if itemid has already been included - this occurs for typed content items
					if (!strstr($row->href, 'Itemid'))
					{
						$temp = explode('id=', $row->href);
						if (isset($temp[1]))
						{
							$row->href .= '&Itemid=' . $app->getItemid($temp[1]);
						}
					}
				}
				$link = JRoute::_($row->href);

				if (!isset($row->text) && isset($row->itext))
				{
					$row->text = $row->itext;
				}

				// Strip html from feed item description text
				$description = preg_replace("'<script[^>]*>.*?</script>'si", '', stripslashes($row->text));
				$description = \Hubzero\Utility\String::truncate($description, 300);
				$author = '';
				@$date = ($row->publish_up ? date('r', strtotime($row->publish_up)) : '');

				if (isset($row->ranking) || isset($row->rating))
				{
					$resourceEx = new ResourcesHelper($row->id, $this->database);
					$resourceEx->getCitationsCount();
					$resourceEx->getLastCitationDate();
					$resourceEx->getContributors();

					$author = strip_tags($resourceEx->contributors);
				}

				// Load individual item creator class
				$item = new JFeedItem();
				$item->title       = $title;
				$item->link        = $link;
				$item->description = $description;
				$item->date        = $date;
				$item->category    = (isset($row->typetitle)) ? $row->typetitle : '';
				$item->author      = $author;

				// Loads item info into rss array
				$doc->addItem($item);
			}
		}

		// Output the feed
		echo $doc->render();
	}

	/**
	 * Short description for '_jtext'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $period Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function _jtext($period)
	{
		switch ($period)
		{
			case 'week':    return JText::_('COM_WHATSNEW_OPT_WEEK');    break;
			case 'month':   return JText::_('COM_WHATSNEW_OPT_MONTH');   break;
			case 'quarter': return JText::_('COM_WHATSNEW_OPT_QUARTER'); break;
			case 'year':    return JText::_('COM_WHATSNEW_OPT_YEAR');    break;
			default:
				$thisyear = strftime("%Y", time());
				for ($y = $thisyear; $y >= 2002; $y--)
				{
					if (time() >= strtotime('10/1/' . $y))
					{
						if ($y == $period)
						{
							return JText::_('COM_WHATSNEW_OPT_FISCAL_YEAR') . ' ' . $y;
						}
					}
				}
				for ($y = $thisyear; $y >= 2002; $y--)
				{
					if (time() >= strtotime('01/01/' . $y))
					{
						if ('c_' . $y == $period)
						{
							return JText::_('COM_WHATSNEW_OPT_CALENDAR_YEAR') . ' ' . $y;
						}
					}
				}
			break;
		}
	}

	/**
	 * Short description for '_getAreas'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	private function _getAreas()
	{
		// Do we already have an array of areas?
		if (!isset($this->searchareas) || empty($this->searchareas))
		{
			// No - so we'll need to get it
			$areas = array();

			// Load the whatsnew plugins
			JPluginHelper::importPlugin('whatsnew');
			$dispatcher = JDispatcher::getInstance();

			// Trigger the functions that return the areas we'll be searching
			$searchareas = $dispatcher->trigger('onWhatsNewAreas');

			// Build an array of the areas
			foreach ($searchareas as $area)
			{
				$areas = array_merge($areas, $area);
			}

			// Save the array for use elsewhere
			$this->searchareas = $areas;
		}

		// Return the array
		return $this->searchareas;
	}
}

