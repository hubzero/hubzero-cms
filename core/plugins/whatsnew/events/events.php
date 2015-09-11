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

// No direct access
defined('_HZEXEC_') or die();

/**
 * What's New Plugin class for com_events
 */
class plgWhatsnewEvents extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function onWhatsnewAreas()
	{
		return array(
			'events' => Lang::txt('PLG_WHATSNEW_EVENTS')
		);
	}

	/**
	 * Pull a list of records that were created within the time frame ($period)
	 *
	 * @param      object  $period     Time period to pull results for
	 * @param      mixed   $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      array   $areas      Active area(s)
	 * @param      array   $tagids     Array of tag IDs
	 * @return     array
	 */
	public function onWhatsnew($period, $limit=0, $limitstart=0, $areas=null, $tagids=array())
	{
		if (is_array($areas) && $limit)
		{
			if (!isset($areas[$this->_name])
			 && !in_array($this->_name, $areas))
			{
				return array();
			}
		}

		// Do we have a search term?
		if (!is_object($period))
		{
			return array();
		}

		$database = App::get('db');

		// Build the query
		$e_count = "SELECT count(DISTINCT e.id)";
		$e_fields = "SELECT e.id, e.title, NULL AS alias, e.content AS itext, NULL AS ftext, e.state, e.created, e.modified, e.publish_up, NULL AS params,
					CONCAT('index.php?option=com_events&task=details&id=', e.id) AS href, 'events' AS section, NULL AS area, NULL AS category, NULL AS rating, NULL AS times_rated, NULL AS ranking ";
		$e_from = " FROM #__events AS e";

		$e_where = "e.created > '$period->cStartDate' AND e.created < '$period->cEndDate' AND scope='events'";

		$order_by  = " ORDER BY publish_up DESC, title";
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if (!$limit)
		{
			// Get a count
			$database->setQuery($e_count . $e_from . " WHERE " . $e_where);
			return $database->loadResult();
		}
		else
		{
			// Get results
			$query = $e_fields . $e_from . " WHERE " . $e_where . $order_by;
			$database->setQuery($query);
			$rows = $database->loadObjectList();

			if ($rows)
			{
				foreach ($rows as $key => $row)
				{
					$rows[$key]->href = Route::url($row->href);
					$rows[$key]->text = $rows[$key]->itext;
				}
			}

			return $rows;
		}
	}

	/**
	 * Push styles to the document
	 *
	 * @return     void
	 */
	public function documents()
	{
		\Hubzero\Document\Assets::addComponentStylesheet('com_events');
	}

	/**
	 * Special formatting for results
	 *
	 * @param      object $row    Database row
	 * @param      string $period Time period
	 * @return     string
	 */
	public function out($row, $period)
	{
		// Start building the HTML
		$html  = "\t" . '<li class="event">' . "\n";
		$html .= "\t\t" . '<p class="event-date"><span class="month">' . Date::of($row->publish_up)->toLocal('M') . '</span>';
		$html .= '<span class="day">' . Date::of($row->publish_up)->toLocal('d') . '</span> ';
		$html .= '<span class="year">' . Date::of($row->publish_up)->toLocal('Y') . '</span></p>' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>'."\n";
		if ($row->itext)
		{
			$row->itext = str_replace('[[BR]]', '', $row->itext);
			$html .= "\t\t".'<p>' . \Hubzero\Utility\String::truncate(\Hubzero\Utility\Sanitize::stripAll(stripslashes($row->itext)), 200) . '</p>' . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . Request::base() . trim($row->href, '/') . '</p>' . "\n";
		$html .= "\t" . '</li>' . "\n";

		// Return output
		return $html;
	}
}

