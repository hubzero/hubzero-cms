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
 * Tags plugin class for blog articles
 */
class plgTagsBlogs extends JPlugin
{
	/**
	 * Record count
	 * 
	 * @var integer
	 */
	private $_total = null;

	/**
	 * Constructor
	 * 
	 * @param      object &$subject The object to observe
	 * @param      array  $config   An optional associative array of configuration settings.
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the name of the area this plugin retrieves records for
	 * 
	 * @return     array
	 */
	public function onTagAreas()
	{
		$areas = array(
			'blogs' => JText::_('PLG_TAGS_BLOGS')
		);
		return $areas;
	}

	/**
	 * Retrieve records for items tagged with specific tags
	 * 
	 * @param      array   $tags       Tags to match records against
	 * @param      mixed   $limit      SQL record limit
	 * @param      integer $limitstart SQL record limit start
	 * @param      string  $sort       The field to sort records by
	 * @param      mixed   $areas      An array or string of areas that should retrieve records
	 * @return     mixed Returns integer when counting records, array when retrieving records
	 */
	public function onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)
	{
		if (is_array($areas) && $limit) 
		{
			if (!array_intersect($areas, $this->onTagAreas()) 
			 && !array_intersect($areas, array_keys($this->onTagAreas()))) 
			{
				return array();
			}
		}

		// Do we have a member ID?
		if (empty($tags)) 
		{
			return array();
		}

		$database =& JFactory::getDBO();

		$ids = array();
		foreach ($tags as $tag)
		{
			$ids[] = $tag->get('id');
		}
		$ids = implode(',', $ids);

		$now = date('Y-m-d H:i:s', time() + 0 * 60 * 60);

		// Build the query
		$e_count = "SELECT COUNT(f.id) FROM (SELECT e.id, COUNT(DISTINCT t.tagid) AS uniques";
		$e_fields = "SELECT e.id, e.title, e.alias, NULL AS itext, e.content AS ftext, e.state, e.created, e.created_by, 
					NULL AS modified, e.publish_up, e.publish_down, CONCAT('index.php?option=com_blog&task=view&id=', e.id) AS href, 
					'blogs' AS section, COUNT(DISTINCT t.tagid) AS uniques, e.params, e.scope AS rcount, u.name AS data1, 
					NULL AS data2, NULL AS data3 ";
		$e_from  = " FROM #__blog_entries AS e, #__tags_object AS t, #__users AS u";
		$e_where = " WHERE e.created_by=u.id AND t.objectid=e.id AND t.tbl='blog' AND t.tagid IN ($ids)";
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$e_where .= " AND e.state=1";
		} 
		else 
		{
			$e_where .= " AND e.state>0";
		}
		$e_where .= " AND (e.publish_up = '0000-00-00 00:00:00' OR e.publish_up <= '" . $now . "') ";
		$e_where .= " AND (e.publish_down = '0000-00-00 00:00:00' OR e.publish_down >= '" . $now . "') ";
		$e_where .= " GROUP BY e.id HAVING uniques=".count($tags);
		$order_by  = " ORDER BY ";
		switch ($sort)
		{
			case 'title': $order_by .= 'title ASC, publish_up';  break;
			case 'id':    $order_by .= "id DESC";                break;
			case 'date':
			default:      $order_by .= 'publish_up DESC, title'; break;
		}
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if (!$limit) 
		{
			// Get a count
			$database->setQuery($e_count . $e_from . $e_where . ") AS f");
			$this->_total = $database->loadResult();
			return $this->_total;
		} 
		else 
		{
			if (count($areas) > 1) 
			{
				return $e_fields . $e_from . $e_where;
			}

			if ($this->_total != null) 
			{
				if ($this->_total == 0) 
				{
					return array();
				}
			}

			// Get results
			$database->setQuery($e_fields . $e_from . $e_where . $order_by);
			$rows = $database->loadObjectList();

			if ($rows) 
			{
				$yearFormat = '%Y';
				$monthFormat = '%b';
				$tz = 0;
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$yearFormat = 'Y';
					$monthFormat = 'm';
					$tz = true;
				}

				foreach ($rows as $key => $row)
				{
					switch ($row->rcount)
					{
						case 'site':
							$rows[$key]->href = JRoute::_('index.php?option=com_blog&task=' . JHTML::_('date', $row->publish_up, $yearFormat, $tz) . '/' . JHTML::_('date', $row->publish_up, $monthFormat, $tz) . '/' . $row->alias);
						break;
						case 'member':
							$rows[$key]->href = JRoute::_('index.php?option=com_members&id=' . $row->created_by . '&active=blog&task=' . JHTML::_('date', $row->publish_up, $yearFormat, $tz) . '/' . JHTML::_('date', $row->publish_up, $monthFormat, $tz) . '/' . $row->alias);
						break;
						case 'group':
						break;
					}
					$rows[$key]->href = JRoute::_($row->href);
				}
			}

			return $rows;
		}
	}

	/**
	 * Static method for formatting results
	 * 
	 * @param      object $row Database row
	 * @return     string HTML
	 */
	public function out($row)
	{
		$juri =& JURI::getInstance();

		$yearFormat = '%Y';
		$monthFormat = '%b';
		$dateFormat = '%d %b %Y';
		$tz = 0;
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$yearFormat = 'Y';
			$monthFormat = 'm';
			$dateFormat = 'd M Y';
			$tz = true;
		}

		switch ($row->rcount)
		{
			case 'site':
				$row->href = JRoute::_('index.php?option=com_blog&task=' . JHTML::_('date', $row->publish_up, $yearFormat, $tz) . '/' . JHTML::_('date', $row->publish_up, $monthFormat, $tz) . '/' . $row->alias);
			break;
			case 'member':
				$row->href = JRoute::_('index.php?option=com_members&id=' . $row->created_by . '&active=blog&task=' . JHTML::_('date', $row->publish_up, $yearFormat, $tz) . '/' . JHTML::_('date', $row->publish_up, $monthFormat, $tz) . '/' . $row->alias);
			break;
			case 'group':
			break;
		}
		$row->href = JRoute::_($row->href);

		// Start building the HTML
		$html  = "\t" . '<li class="blog-entry">' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>' . "\n";
		$html .= "\t\t" . '<p class="details">' . JHTML::_('date', $row->publish_up, $dateFormat, $tz);
		$html .= ' <span>|</span> ' . JText::sprintf('PLG_TAGS_BLOGS_POSTED_BY', '<cite><a href="' . JRoute::_('index.php?option=com_members&id=' . $row->created_by) . '">' . stripslashes($row->data1) . '</a></cite>');
		$html .= '</p>'."\n";
		if ($row->ftext) 
		{
			$html .= "\t\t" . Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->ftext)), 200) . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . rtrim($juri->base(), DS) . DS . ltrim($row->href, DS) . '</p>' . "\n";
		$html .= "\t" . '</li>' . "\n";

		// Return output
		return $html;
	}
}

