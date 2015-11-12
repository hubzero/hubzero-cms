<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
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

namespace Modules\WhatsNew;

use Hubzero\Module\Module;
use Components\Whatsnew\Helpers\Period;
use MembersModelTags;
use Component;
use Request;
use Event;
use Route;
use Lang;
use User;

/**
 * Module class for displaying what's new in a category of content
 */
class Helper extends Module
{
	/**
	 * Get the categories for What's New
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

	/**
	 * Fromat tags for display
	 *
	 * @param   array    $tags  Tags to format
	 * @param   integer  $num   Number of tags to display
	 * @param   integer  $max   Max characters for a tag
	 * @return  string   HTML
	 */
	public function formatTags($tags=array(), $num=3, $max=25)
	{
		$out = '';

		if (count($tags) > 0)
		{
			$out .= '<span class="taggi">' . "\n";
			$counter = 0;

			foreach ($tags as $i => $tag)
			{
				$counter = $counter + strlen(stripslashes($tag->get('raw_tag')));
				if ($counter > $max)
				{
					$num = $num - 1;
				}
				if ($i < $num)
				{
					// display tag
					$out .= "\t" . '<a href="' . Route::url($tag->link()) . '">' . $this->escape(stripslashes($tag->get('raw_tag'))) . '</a> ' . "\n";
				}
			}
			if ($i > $num)
			{
				$out .= ' (&#8230;)';
			}
			$out .= '</span>' . "\n";
		}

		return $out;
	}

	/**
	 * Get module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		include_once(Component::path('com_whatsnew') . DS . 'helpers' . DS . 'period.php');
		$live_site = rtrim(Request::base(), '/');

		// Get some initial parameters
		$count        = intval($this->params->get('limit', 5));
		$this->feed   = $this->params->get('feed');
		$this->cssId  = $this->params->get('cssId');
		$this->period = $this->params->get('period', 'resources:month');
		$this->tagged = intval($this->params->get('tagged', 0));

		$database = \App::get('db');

		// Build the feed link if necessary
		if ($this->feed)
		{
			$this->feedlink = Route::url('index.php?option=com_whatsnew&task=feed.rss&period=' . $this->period);
			$this->feedlink = DS . trim($this->feedlink, DS);
			$this->feedlink = $live_site . $this->feedlink;
			if (substr($this->feedlink, 0, 5) == 'https')
			{
				$this->feedlink = ltrim($this->feedlink, 'https');
				$this->feedlink = 'http' . $this->feedlink;
			}
		}

		// Get categories
		$areas = $this->_getAreas();

		$area = '';

		// Check the search string for a category prefix
		if ($this->period != NULL)
		{
			$searchstring = strtolower($this->period);
			foreach ($areas as $c=>$t)
			{
				$regexp = "/" . $c . ":/";
				if (strpos($searchstring, $c . ":") !== false)
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
						$regexp = "/" . $sc . ":/";
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
			$this->period = trim($searchstring);
		}
		$this->area = $area;

		// Get the active category
		$activeareas = array();
		if ($area)
		{
			$activeareas[] = $area;
		}

		// Process the keyword for exact time period
		$p = new Period($this->period);

		// Get the search results
		$results = Event::trigger('whatsnew.onWhatsnew', array(
				$p,
				$count,
				0,
				$activeareas,
				array()
			)
		);

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

		$this->rows = $rows;
		$this->rows2 = null;

		if ($this->tagged)
		{
			include_once(Component::path('com_members') . DS . 'models' . DS . 'tags.php');
			$mt = new \Components\Members\Models\Tags(User::get('id'));
			$tags = $mt->tags();

			$this->tags = $tags;

			if (count($tags) > 0)
			{
				$tagids = array();
				foreach ($tags as $tag)
				{
					$tagids[] = $tag->get('id');
				}

				// Get the search results
				$results2 = Event::trigger('onWhatsnew', array(
						$p,
						$count,
						0,
						$activeareas,
						$tagids
					)
				);

				$rows2 = array();

				if ($results2)
				{
					foreach ($results2 as $result2)
					{
						if (is_array($result2) && !empty($result2))
						{
							$rows2 = $result2;
							break;
						}
					}
				}

				$this->rows2 = $rows2;
			}
		}

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}

	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		// Push the module CSS to the template
		$this->css();

		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}
