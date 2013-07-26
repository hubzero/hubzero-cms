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

jimport('joomla.plugin.plugin');

/**
 * What's New Plugin class for com_content articles
 */
class plgWhatsnewContent extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @return     array
	 */
	public function onWhatsnewAreas()
	{
		$areas = array(
			'content' => JText::_('PLG_WHATSNEW_CONTENT')
		);
		return $areas;
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
			if (!array_intersect($areas, $this->onWhatsnewAreas()) 
			 && !array_intersect($areas, array_keys($this->onWhatsnewAreas()))) 
			{
				return array();
			}
		}

		// Do we have a search term?
		if (!is_object($period)) 
		{
			return array();
		}

		$database =& JFactory::getDBO();

		// Build the query
		$c_count = " SELECT count(DISTINCT c.id)";
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$c_fields = " SELECT "
				. " c.id,"
				. " c.title, c.alias, c.created, "
				. " CONCAT(c.introtext, c.fulltext) AS text,"
				. " CONCAT('index.php?option=com_content&task=view&id=', c.id) AS href, u.alias AS fsection, b.alias AS category,"
				. " 'content' AS section, NULL AS subsection";
			$c_from = " FROM #__content AS c"
				. " INNER JOIN #__categories AS b ON b.id=c.catid"
				. " INNER JOIN #__sections AS u ON u.id=c.sectionid";
		}
		else 
		{
			$c_fields = " SELECT "
				. " c.id,"
				. " c.title, c.alias, c.created, "
				. " CONCAT(c.introtext, c.fulltext) AS text,"
				. " CONCAT('index.php?option=com_content&task=view&id=', c.id) AS href, NULL AS fsection, b.alias AS category,"
				. " 'content' AS section, NULL AS subsection";
			$c_from = " FROM #__content AS c"
				. " INNER JOIN #__categories AS b ON b.id=c.catid";
		}

		$c_where = "c.publish_up > '$period->cStartDate' AND c.publish_up < '$period->cEndDate' AND c.state='1'";

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
					if (version_compare(JVERSION, '1.6', 'lt'))
					{
						$database->setQuery("SELECT alias, parent FROM #__menu WHERE link='index.php?option=com_content&view=article&id=" . $row->id . "' AND published=1 LIMIT 1");
						$menuitem = $database->loadRow();
						if ($menuitem[1]) 
						{
							$p = $this->_recursiveMenuLookup($menuitem[1]);
							$path = implode(DS, $p);
							if ($menuitem[0]) 
							{
								$path .= DS . $menuitem[0];
							} 
							else if ($row->alias) 
							{
								$path .= DS . $row->alias;
							}
						} 
						else if ($menuitem[0]) 
						{
							$path = DS . $menuitem[0];
						} 
						else 
						{
							$path = '';
							if ($row->fsection) 
							{
								$path .= DS . $row->fsection;
							}
							if ($row->category && $row->category != $row->fsection) 
							{
								$path .= DS . $row->category;
							}
							if ($row->alias) 
							{
								$path .= DS . $row->alias;
							}
							if (!$path) 
							{
								$path = '/content/article/' . $row->id;
							}
						}
					}
					else 
					{
						$path = JRoute::_($row->href);
					}
					
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
	 * Find the menu item alias for a page
	 * 
	 * @param      integer $id       Menu item ID
	 * @param      boolean $startnew Parameter description (if any) ...
	 * @return     array
	 */
	private function _recursiveMenuLookup($id, $startnew=true)
	{
		static $aliases = array();

		if ($startnew) 
		{
			unset($aliases);
		}

		$database =& JFactory::getDBO();
		$database->setQuery("SELECT alias, parent FROM #__menu WHERE id='$id' LIMIT 1");
		$level = $database->loadRow();

		$aliases[] = $level[0];
		if ($level[1]) 
		{
			$a = $this->_recursiveMenuLookup($level[1], false);
		}

		return $aliases;
	}
}

