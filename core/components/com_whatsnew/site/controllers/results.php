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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Whatsnew\Site\Controllers;

use Components\Whatsnew\Helpers\Period;
use Hubzero\Component\SiteController;
use Document;
use Pathway;
use Request;
use Config;
use Event;
use Lang;
use Html;

/**
 * Controller class for dipslaying what's new
 */
class Results extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('feedrss', 'feed');
		$this->registerTask('feed.rss', 'feed');

		parent::execute();
	}

	/**
	 * Display a list of new item's
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$menu = \App::get('menu')->getActive();
		if (!$menu)
		{
			$menu = new \stdClass;
			$menu->params = '';
		}

		$menu->param = new \Hubzero\Config\Registry($menu->params);

		// Incoming
		$period = Request::getVar('period', $menu->param->get('period', 'month'));

		// Paging variables
		$start = Request::getInt('limitstart', 0);
		$limit = Request::getInt('limit', Config::get('list_limit'));

		// Get categories
		$areas = $this->_getAreas();

		// Was there a category passed in the querystring?
		$area = trim(Request::getWord('category', ''));

		// Check the search string for a category prefix
		if ($period != null)
		{
			$searchstring = strtolower($period);
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
					foreach ($t as $sc => $st)
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

		// Process the keyword for exact phrase matches, etc.
		$p = new Period($period);

		// Get the search result totals
		$totals = Event::trigger(
			'whatsnew.onWhatsnew',
			array(
				$p,
				0,
				0,
				$activeareas
			)
		);

		$limit = ($limit == 0) ? 'all' : $limit;

		// Get the search results
		$results = Event::trigger(
			'whatsnew.onWhatsnew',
			array(
				$p,
				$limit,
				$start,
				$activeareas
			)
		);

		// Get the total results found (sum of all categories)
		$i = 0;
		$total = 0;
		$cats = array();
		foreach ($areas as $c => $t)
		{
			$cats[$i]['category'] = $c;

			// Do sub-categories exist?
			if (is_array($t) && !empty($t))
			{
				// They do - do some processing
				$cats[$i]['title'] = ucfirst($c);
				$cats[$i]['total'] = 0;
				$cats[$i]['_sub']  = array();
				$z = 0;
				// Loop through each sub-category
				foreach ($t as $s => $st)
				{
					// Ensure a matching array of totals exist
					if (is_array($totals[$i])
					 && !empty($totals[$i])
					 && isset($totals[$i][$z]))
					{
						// Add to the parent category's total
						$cats[$i]['total'] = $cats[$i]['total'] + $totals[$i][$z];
						// Get some info for each sub-category
						$cats[$i]['_sub'][$z]['category'] = $s;
						$cats[$i]['_sub'][$z]['title']    = stripslashes($st);
						$cats[$i]['_sub'][$z]['total']    = $totals[$i][$z];
					}
					$z++;
				}
			}
			else
			{
				// No sub-categories - this should be easy
				$cats[$i]['title'] = $t;
				$cats[$i]['total'] = (!is_array($totals[$i])) ? $totals[$i] : 0;
			}

			// Add to the overall total
			$total += intval($cats[$i]['total']);
			$i++;
		}

		// Do we have an active area?
		$active = '';
		if (count($activeareas) == 1)
		{
			$active = $activeareas[0];
		}

		// Set the page title
		$title = Lang::txt(strtoupper($this->_option)) . ': ' . $this->_text($period);

		Document::setTitle($title);

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		Pathway::append(
			$this->_text($period),
			'index.php?option=' . $this->_option . '&period=' . $period
		);

		// Build some options for the time period <select>
		$periodlist = array();
		$periodlist[] = Html::select('option', 'week', Lang::txt('COM_WHATSNEW_OPT_WEEK'));
		$periodlist[] = Html::select('option', 'month', Lang::txt('COM_WHATSNEW_OPT_MONTH'));
		$periodlist[] = Html::select('option', 'quarter', Lang::txt('COM_WHATSNEW_OPT_QUARTER'));
		$periodlist[] = Html::select('option', 'year', Lang::txt('COM_WHATSNEW_OPT_YEAR'));

		$thisyear = strftime("%Y", time());
		for ($y = $thisyear; $y >= 2002; $y--)
		{
			if (time() >= strtotime('10/1/' . $y))
			{
				$periodlist[] = Html::select('option', $y, Lang::txt('COM_WHATSNEW_OPT_FISCAL_YEAR') . ' ' . $y);
			}
		}
		for ($y = $thisyear; $y >= 2002; $y--)
		{
			if (time() >= strtotime('01/01/' . $y))
			{
				$periodlist[] = Html::select('option', 'c_' . $y, Lang::txt('COM_WHATSNEW_OPT_CALENDAR_YEAR') . ' ' . $y);
			}
		}

		$this->view
			->set('cats', $cats)
			->set('limit', $limit)
			->set('start', $start)
			->set('totals', $totals)
			->set('total', $total)
			->set('period', $period)
			->set('periodlist', $periodlist)
			->set('area', $area)
			->set('active', $active)
			->set('title', $title)
			->set('results', $results)
			->display();
	}

	/**
	 * Generate an RSS feed
	 *
	 * @return     void
	 */
	public function feedTask()
	{
		// Set the mime encoding for the document
		Document::setType('feed');

		// Start a new feed object
		$doc = Document::instance();
		$doc->link = Route::url('index.php?option=' . $this->_option);

		// Incoming
		$period = Request::getVar('period', 'month');

		// Paging variables
		$start = Request::getInt('limitstart', 0);
		$limit = Request::getInt('limit', Config::get('list_limit'));

		// Get categories
		$areas = $this->_getAreas();

		// Was there a category passed in the querystring?
		$area = trim(Request::getWord('category', ''));

		// Check the search string for a category prefix
		if ($period != null)
		{
			$searchstring = strtolower($period);
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
					foreach ($t as $sc => $st)
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

		// Process the keyword for exact phrase matches, etc.
		$p = new Period($period);

		// Fetch results
		$results = Event::trigger(
			'whatsnew.onWhatsnew',
			array(
				$p,
				$limit,
				$start,
				$activeareas
			)
		);

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
		$doc->title  = Config::get('sitename') . ' - ' . Lang::txt('COM_WHATSNEW_RSS_TITLE') . ': ' . $period;
		$doc->title .= ($area) ? ': ' . $area : '';
		$doc->description = Lang::txt('COM_WHATSNEW_RSS_DESCRIPTION', Config::get('sitename'));
		$doc->copyright   = Lang::txt('COM_WHATSNEW_RSS_COPYRIGHT', gmdate("Y"), Config::get('sitename'));
		$doc->category    = Lang::txt('COM_WHATSNEW_RSS_CATEGORY');

		// Start outputing results if any found
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				// Prepare the title
				$title = strip_tags(stripslashes($row->title));
				$title = html_entity_decode($title);

				// URL link to article
				$row->href = DS . ltrim($row->href, DS);
				/*if (strstr($row->href, 'view'))
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
				}*/
				$link = Route::url($row->href);

				if (!isset($row->text) && isset($row->itext))
				{
					$row->text = $row->itext;
				}

				// Strip html from feed item description text
				$description = preg_replace("'<script[^>]*>.*?</script>'si", '', stripslashes($row->text));
				$description = \Hubzero\Utility\String::truncate($description, 300);
				$author = '';
				@$date = ($row->publish_up ? date('r', strtotime($row->publish_up)) : '');

				// Load individual item creator class
				$item = new \Hubzero\Document\Type\Feed\Item();
				$item->title       = $title;
				$item->link        = $link;
				$item->description = $description;
				$item->date        = $date;
				$item->category    = (isset($row->typetitle)) ? $row->typetitle : '';
				if (isset($row->authors))
				{
					$item->author  = strip_tags($row->authors);
				}

				// Loads item info into rss array
				$doc->addItem($item);
			}
		}
	}

	/**
	 * Get the translated text value for a give time period
	 *
	 * @param   string  $period  Time period
	 * @return  string
	 */
	private function _text($period)
	{
		switch ($period)
		{
			case 'week':
				return Lang::txt('COM_WHATSNEW_OPT_WEEK');
				break;
			case 'month':
				return Lang::txt('COM_WHATSNEW_OPT_MONTH');
				break;
			case 'quarter':
				return Lang::txt('COM_WHATSNEW_OPT_QUARTER');
				break;
			case 'year':
				return Lang::txt('COM_WHATSNEW_OPT_YEAR');
				break;
			default:
				$thisyear = strftime("%Y", time());
				for ($y = $thisyear; $y >= 2002; $y--)
				{
					if (time() >= strtotime('10/1/' . $y))
					{
						if ($y == $period)
						{
							return Lang::txt('COM_WHATSNEW_OPT_FISCAL_YEAR') . ' ' . $y;
						}
					}
				}
				for ($y = $thisyear; $y >= 2002; $y--)
				{
					if (time() >= strtotime('01/01/' . $y))
					{
						if ('c_' . $y == $period)
						{
							return Lang::txt('COM_WHATSNEW_OPT_CALENDAR_YEAR') . ' ' . $y;
						}
					}
				}
				break;
		}
	}

	/**
	 * Get a list of active plugins
	 *
	 * @return  array
	 */
	private function _getAreas()
	{
		// Do we already have an array of areas?
		if (!isset($this->searchareas) || empty($this->searchareas))
		{
			// No - so we'll need to get it
			$areas = array();

			// Trigger the functions that return the areas we'll be searching
			$searchareas = Event::trigger('whatsnew.onWhatsnewAreas');

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
