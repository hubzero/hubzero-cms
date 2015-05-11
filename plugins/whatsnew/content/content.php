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

		$database = JFactory::getDBO();

		// Build the query
		$c_count = " SELECT count(DISTINCT c.id)";
		$c_fields = " SELECT "
			. " c.id,"
			. " c.title, c.alias, c.created, "
			. " CONCAT(c.introtext, c.fulltext) AS text,"
			. " CONCAT('index.php?option=com_content&task=view&id=', c.id) AS href, NULL AS fsection, b.alias AS category,"
			. " 'content' AS section, NULL AS subsection";
		$c_from = " FROM #__content AS c"
			. " INNER JOIN #__categories AS b ON b.id=c.catid";

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

					$rows[$key]->text = JHtml::_('content.prepare', $rows[$key]->text, '', 'com_content.article');
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

		$database = JFactory::getDBO();
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

