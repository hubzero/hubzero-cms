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
 * What's New Plugin class for com_content articles
 */
class plgWhatsnewContent extends \Hubzero\Plugin\Plugin
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
			'content' => Lang::txt('PLG_WHATSNEW_CONTENT')
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
		$c_count  = " SELECT count(DISTINCT c.id)";
		$c_fields = " SELECT c.id, c.title, c.alias, c.created, b.path, CONCAT(c.introtext, c.fulltext) AS text, CONCAT('index.php?option=com_content&task=view&id=', c.id) AS href, NULL AS fsection, b.alias AS category, 'content' AS section, NULL AS subsection";
		$c_from   = " FROM #__content AS c INNER JOIN #__categories AS b ON b.id=c.catid";

		$c_where = "c.publish_up > " . $database->quote($period->cStartDate) . " AND c.publish_up < " . $database->quote($period->cEndDate) . " AND c.state='1'";

		$order_by  = " ORDER BY publish_up DESC, title";
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if ($limit)
		{
			// Get results
			$database->setQuery($c_fields . $c_from . " WHERE " . $c_where . $order_by);
			$rows = $database->loadObjectList();

			if ($rows)
			{
				foreach ($rows as $key => $row)
				{
					$path = Route::url($row->href);
					if ($row->path)
					{
						$path = ltrim(Request::base(true), '/') . '/' . $row->path . '/' . $row->alias;
					}

					preg_match_all("/\{xhub:\s*[^\}]*\}/i", $rows[$key]->text, $matches, PREG_SET_ORDER);
					if ($matches)
					{
						foreach ($matches as $match)
						{
							if (preg_match("/\{xhub:\s*([^\s]+)\s*(.*)/i", $match[0], $tag))
							{
								switch (strtolower(trim($tag[1])))
								{
									case 'include':
										$rows[$key]->text = str_replace($match[0], '', $rows[$key]->text);
									break;
								}
							}
						}
					}

					$rows[$key]->text = Html::content('prepare', $rows[$key]->text, '', 'com_content.article');
					$rows[$key]->text = strip_tags($row->text);
					$rows[$key]->href = $path;
				}
			}

			return $rows;
		}
		else
		{
			// Get a count
			$database->setQuery($c_count . $c_from . " WHERE " . $c_where);
			return $database->loadResult();
		}
	}

	/**
	 * Special formatting for results
	 * 
	 * @param      object $row    Database row
	 * @param      string $period Time period
	 * @return     string
	 */
	public static function out($row, $period)
	{
		if (strstr($row->href, 'index.php'))
		{
			$row->href = Route::url($row->href);
		}

		$html  = "\t" . '<li>' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>' . "\n";
		if ($row->text)
		{
			$html .= "\t\t" . '<p>' . \Hubzero\Utility\String::truncate(\Hubzero\Utility\Sanitize::stripAll(stripslashes($row->text)), 200) . '</p>' . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . Request::base() . ltrim($row->href, '/') . '</p>' . "\n";
		$html .= "\t" . '</li>' . "\n";

		// Return output
		return $html;
	}

	/**
	 * Find the menu item alias for a page
	 *
	 * @param      integer $id       Menu item ID
	 * @param      boolean $startnew Reset the array?
	 * @return     array
	 */
	private function _recursiveMenuLookup($id, $startnew=true)
	{
		static $aliases = array();

		if ($startnew)
		{
			unset($aliases);
		}

		$database = App::get('db');
		$database->setQuery("SELECT alias, parent FROM `#__menu` WHERE id=" . $database->quote($id) . " LIMIT 1");
		$level = $database->loadRow();

		$aliases[] = $level[0];
		if ($level[1])
		{
			$a = $this->_recursiveMenuLookup($level[1], false);
		}

		return $aliases;
	}
}

