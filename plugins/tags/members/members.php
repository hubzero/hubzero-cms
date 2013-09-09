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
 * Tags plugin class for members
 */
class plgTagsMembers extends JPlugin
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
			'members' => JText::_('PLG_TAGS_MEMBERS')
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
		// Check if our area is in the array of areas we want to return results for
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

		// Build the query
		$f_count = "SELECT COUNT(f.uidNumber) FROM (SELECT a.uidNumber, COUNT(DISTINCT t.tagid) AS uniques ";

		$f_fields = "SELECT a.uidNumber AS id, a.name AS title, a.username as alias, NULL AS itext, b.bio AS ftext, a.emailConfirmed AS state, a.registerDate AS created, 
					a.uidNumber AS created_by, NULL AS modified, a.registerDate AS publish_up, a.picture AS publish_down, 
					CONCAT('index.php?option=com_members&id=', a.uidNumber) AS href, 'members' AS section, COUNT(DISTINCT t.tagid) AS uniques, a.params, NULL AS rcount, 
					NULL AS data1, NULL AS data2, NULL AS data3 ";

		$f_from = " FROM #__xprofiles AS a LEFT JOIN #__xprofiles_bio AS b ON a.uidNumber=b.uidNumber, #__tags_object AS t
					WHERE a.public=1 
					AND a.uidNumber=t.objectid 
					AND t.tbl='xprofiles' 
					AND t.tagid IN ($ids)";
		$f_from .= " GROUP BY a.uidNumber HAVING uniques=".count($tags);
		$order_by  = " ORDER BY ";
		switch ($sort)
		{
			case 'title': $order_by .= 'title ASC, publish_up';  break;
			case 'id':    $order_by .= "id DESC";                break;
			case 'date':
			default:      $order_by .= 'publish_up DESC, title'; break;
		}
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		// Execute the query
		if (!$limit) 
		{
			$database->setQuery($f_count . $f_from . ") AS f");
			$this->_total = $database->loadResult();
			return $this->_total;
		} 
		else 
		{
			if (count($areas) > 1) 
			{
				ximport('Hubzero_Document');
				Hubzero_Document::addComponentStylesheet('com_members');

				return $f_fields . $f_from;
			}

			if ($this->_total != null) 
			{
				if ($this->_total == 0) 
				{
					return array();
				}
			}

			$database->setQuery($f_fields . $f_from .  $order_by);
			$rows = $database->loadObjectList();

			// Did we get any results?
			if ($rows) 
			{
				// Loop through the results and set each item's HREF
				foreach ($rows as $key => $row)
				{
					$rows[$key]->href = JRoute::_('index.php?option=com_members&id=' . $row->id);
				}
			}

			// Return the results
			return $rows;
		}
	}

	/**
	 * Include needed libraries and push scripts and CSS to the document
	 * 
	 * @return     void
	 */
	public function documents()
	{
		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet('com_members');
	}

	/**
	 * Static method for formatting results
	 * 
	 * @param      object $row Database row
	 * @return     string HTML
	 */
	public function out($row)
	{
		ximport('Hubzero_User_Profile');
		ximport('Hubzero_User_Profile_Helper');
		$member = Hubzero_User_Profile::getInstance($row->id);

		if (strstr($row->href, 'index.php')) 
		{
			$row->href = JRoute::_($row->href);
		}
		$juri =& JURI::getInstance();

		$html  = "\t" . '<li class="member">' . "\n";
		$html .= "\t\t" . '<p class="photo"><img width="50" height="50" src="' . Hubzero_User_Profile_Helper::getMemberPhoto($member, 0) . '" alt="" /></p>' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>' . "\n";
		if ($row->ftext) 
		{
			$html .= "\t\t" . Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->ftext)), 200) . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . $juri->base() . ltrim($row->href, DS) . '</p>' . "\n";
		$html .= "\t" . '</li>' . "\n";
		return $html;
	}
}
