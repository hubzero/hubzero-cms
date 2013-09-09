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
 * Tags plugin class for Knowledge Base articles
 */
class plgTagsKb extends JPlugin
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
			'kb' => JText::_('PLG_TAGS_KB')
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
		$e_fields = "SELECT e.id, e.title, e.alias, e.fulltxt AS itext, e.fulltxt AS ftext, e.state, e.created, e.created_by, e.modified, e.created AS publish_up, 
					NULL AS publish_down, CONCAT('index.php?option=com_kb&section=&category=&alias=', e.alias) AS href, 'kb' AS section, COUNT(DISTINCT t.tagid) AS uniques, 
					NULL AS params, e.helpful AS rcount, cc.alias AS data1, c.alias AS data2, NULL AS data3 ";
		$e_from  = " FROM #__faq AS e
		 			LEFT JOIN #__faq_categories AS c ON c.id = e.section 
					LEFT JOIN #__faq_categories AS cc ON cc.id = e.category
					LEFT JOIN #__tags_object AS t ON t.objectid=e.id AND t.tbl='kb' AND t.tagid IN ($ids)";
		$e_where  = " WHERE e.state=1";
		$e_where .= " GROUP BY e.id HAVING uniques=" . count($tags);
		$order_by  = " ORDER BY ";
		switch ($sort)
		{
			case 'title': $order_by .= 'title ASC, created';  break;
			case 'id':    $order_by .= "id DESC";                break;
			case 'date':
			default:      $order_by .= 'created DESC, title'; break;
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
				foreach ($rows as $key => $row)
				{
					$rows[$key]->href = JRoute::_('index.php?option=com_kb&section=' . $row->data2 . '&category=' . $row->data1 . '&alias=' . $row->alias);
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
		if (strstr($row->href, 'index.php')) 
		{
			$row->href = JRoute::_('index.php?option=com_kb&section=' . $row->data2 . '&category=' . $row->data1 . '&alias=' . $row->alias);
		}
		$juri =& JURI::getInstance();
		//$row->href = ltrim($row->href, DS);

		// Start building the HTML
		$html  = "\t" . '<li class="kb-entry">' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>' . "\n";
		if ($row->ftext) 
		{
			$html .= "\t\t" . Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->ftext)), 200) . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . $juri->base() . ltrim($row->href, DS) . '</p>' . "\n";
		$html .= "\t" . '</li>' . "\n";

		// Return output
		return $html;
	}
}

