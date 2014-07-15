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
 * Tags plugin class for groups
 */
class plgTagsGroups extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

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
		$response = array(
			'name'    => $this->_name,
			'title'   => JText::_('PLG_TAGS_GROUPS'),
			'total'   => 0,
			'results' => null,
			'sql'     => ''
		);

		if (empty($tags))
		{
			return $response;
		}

		$database = JFactory::getDBO();

		$ids = array();
		foreach ($tags as $tag)
		{
			$ids[] = $tag->get('id');
		}
		$ids = implode(',', $ids);

		$from = '';

		$juser = JFactory::getUser();
		if (!$juser->authorise('core.view', 'com_groups'))
		{
			$from = " JOIN #__xgroups_members AS m ON m.gidNumber=a.gidNumber AND m.uidNumber=" . $juser->get('id');
		}

		// Build the query
		$f_count = "SELECT COUNT(f.gidNumber) FROM (SELECT a.gidNumber, COUNT(DISTINCT t.tagid) AS uniques ";

		$f_fields = "SELECT a.gidNumber AS id, a.description AS title, a.cn AS alias, NULL AS itext, a.public_desc AS ftext, a.type AS state, a.created,
					a.created_by, NULL AS modified, NULL AS publish_up,
					NULL AS publish_down, CONCAT('index.php?option=com_groups&cn=', a.cn) AS href, 'groups' AS section, COUNT(DISTINCT t.tagid) AS uniques,
					a.params, NULL AS rcount, NULL AS data1, NULL AS data2, NULL AS data3 ";
		$f_from = " FROM #__xgroups AS a $from
					JOIN #__tags_object AS t
					WHERE a.type=1 AND a.discoverability=0
					AND a.gidNumber=t.objectid
					AND t.tbl='groups'
					AND t.tagid IN ($ids)";
		$f_from .= " GROUP BY a.gidNumber HAVING uniques=" . count($tags);
		$order_by  = " ORDER BY ";
		switch ($sort)
		{
			case 'title': $order_by .= 'title ASC, publish_up';  break;
			case 'id':    $order_by .= "id DESC";                break;
			case 'date':
			default:      $order_by .= 'publish_up DESC, title'; break;
		}
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		$database->setQuery($f_count . $f_from . ") AS f");
		$response['total'] = $database->loadResult();

		if ($areas && $areas == $response['name'])
		{
			$database->setQuery($f_fields . $f_from .  $order_by);
			$response['results'] = $database->loadObjectList();
			if ($response['results'])
			{
				// Loop through the results and set each item's HREF
				foreach ($response['results'] as $key => $row)
				{
					$response['results'][$key]->href = JRoute::_('index.php?option=com_groups&cn=' . $row->alias);
				}
			}
		}
		else
		{
			$response['sql'] = $f_fields . $f_from;
		}

		return $response;
	}
}
