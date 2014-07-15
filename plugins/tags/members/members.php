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
 * Tags plugin class for members
 */
class plgTagsMembers extends \Hubzero\Plugin\Plugin
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
			'title'   => JText::_('PLG_TAGS_MEMBERS'),
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

		$database->setQuery($f_count . $f_from . ") AS f");
		$response['total'] = $database->loadResult();

		if ($response['total'])
		{
			\Hubzero\Document\Assets::addComponentStylesheet('com_members');
		}

		if ($areas && $areas == $response['name'])
		{
			$database->setQuery($f_fields . $f_from .  $order_by);
			$response['results'] = $database->loadObjectList();
		}
		else
		{
			$response['sql'] = $f_fields . $f_from;
		}

		return $response;
	}

	/**
	 * Include needed libraries and push scripts and CSS to the document
	 *
	 * @return     void
	 */
	public static function documents()
	{
		\Hubzero\Document\Assets::addComponentStylesheet('com_members');
	}

	/**
	 * Static method for formatting results
	 *
	 * @param      object $row Database row
	 * @return     string HTML
	 */
	public static function out($row)
	{
		$member = \Hubzero\User\Profile::getInstance($row->id);

		$row->href = JRoute::_('index.php?option=com_members&id=' . $row->id);
		if (strstr($row->href, 'index.php'))
		{
			$row->href = JRoute::_($row->href);
		}
		$juri = JURI::getInstance();

		$html  = "\t" . '<li class="member">' . "\n";
		$html .= "\t\t" . '<p class="photo"><img width="50" height="50" src="' . $member->getPicture() . '" alt="" /></p>' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>' . "\n";
		if ($row->ftext)
		{
			$html .= "\t\t" . \Hubzero\Utility\String::truncate(\Hubzero\Utility\Sanitize::stripAll(stripslashes($row->ftext)), 200) . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . $juri->base() . ltrim($row->href, DS) . '</p>' . "\n";
		$html .= "\t" . '</li>' . "\n";
		return $html;
	}
}
