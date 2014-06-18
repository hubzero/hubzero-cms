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
 * What's New Plugin class for com_wiki articles
 */
class plgWhatsnewWiki extends \Hubzero\Plugin\Plugin
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
			'wiki' => JText::_('PLG_WHATSNEW_WIKI')
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

		// Do we have a time period?
		if (!is_object($period))
		{
			return array();
		}

		$database = JFactory::getDBO();

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');

		// Instantiate some needed objects
		$wp = new WikiTablePage($database);

		// Build query
		$filters = array();
		$filters['startdate']  = $period->cStartDate;
		$filters['enddate']    = $period->cEndDate;
		$filters['sortby']     = 'date';

		$juser = JFactory::getUser();
		$filters['authorized'] = false;
		if (!$juser->get('guest'))
		{
			$filters['authorized'] = true;
		}

		if (count($tagids) > 0)
		{
			$filters['tags'] = $tagids;
		}

		if (!$limit)
		{
			// Get a count
			$filters['select'] = 'count';

			$database->setQuery($wp->buildPluginQuery($filters));
			return $database->loadResult();
		}
		else
		{
			// Get results
			$filters['select'] = 'records';
			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;

			$database->setQuery($wp->buildPluginQuery($filters));
			$rows = $database->loadObjectList();

			if ($rows)
			{
				foreach ($rows as $key => $row)
				{
					if ($row->area != '' && $row->category != '')
					{
						$rows[$key]->href = JRoute::_('index.php?option=com_groups&scope=' . $row->category . '&pagename=' . $row->alias);
					}
					else
					{
						$rows[$key]->href = JRoute::_('index.php?option=com_wiki&scope=' . $row->category . '&pagename=' . $row->alias);
					}
					$rows[$key]->text = strip_tags($rows[$key]->itext);
					if ($row->title == '')
					{
						$rows[$key]->title = $rows[$key]->alias;
					}
				}
			}

			return $rows;
		}
	}
}

