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
 * What's New Plugin class for com_kb articles
 */
class plgWhatsnewKb extends \Hubzero\Plugin\Plugin
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
			'kb' => JText::_('PLG_WHATSNEW_KB')
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
		$f_count = "SELECT COUNT(*)";
		$f_fields = "SELECT"
			. " f.id, "
			. " f.title, "
			. " 'kb' AS section, NULL AS subsection, "
			. " f.fulltxt AS text,"
			. " CONCAT('index.php?option=com_kb&task=article&id=', f.id) AS href";

		$f_from = " FROM #__faq AS f";

		$f_where = "f.state=1 AND f.created > '$period->cStartDate' AND f.created < '$period->cEndDate'";

		$order_by  = " ORDER BY created DESC, title";
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if (!$limit)
		{
			// Get a count
			$database->setQuery($f_count . $f_from . " WHERE " . $f_where);
			return $database->loadResult();
		}
		else
		{
			// Get results
			$database->setQuery($f_fields . $f_from . " WHERE " . $f_where . $order_by);
			$rows = $database->loadObjectList();

			foreach ($rows as $key => $row)
			{
				$rows[$key]->href = JRoute::_($row->href);
			}

			return $rows;
		}
	}
}

