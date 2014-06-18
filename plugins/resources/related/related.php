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
 * Resources Plugin class for related resources
 */
class plgResourcesRelated extends \Hubzero\Plugin\Plugin
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
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function &onResourcesSubAreas($resource)
	{
		$areas = array(
			'related' => JText::_('PLG_RESOURCES_RELATED')
		);
		return $areas;
	}

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 *
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      integer $miniview  View style
	 * @return     array
	 */
	public function onResourcesSub($resource, $option, $miniview=0)
	{
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		$database = JFactory::getDBO();

		// Build the query that checks topic pages
		$sql1 = "SELECT v.id, v.pageid, MAX(v.version) AS version, w.title, w.pagename AS alias, v.pagetext AS introtext,
					NULL AS type, NULL AS published, NULL AS publish_up, w.scope, w.rating, w.times_rated, w.ranking, 'Topic' AS section, w.`group_cn`
				FROM `#__wiki_page` AS w
				JOIN `#__wiki_version` AS v ON w.id=v.pageid
				JOIN `#__wiki_page_links` AS wl ON wl.page_id=w.id
				WHERE v.approved=1 AND wl.scope='resource' AND wl.scope_id=" . $database->Quote($resource->id);

		$juser = JFactory::getUser();
		if (!$juser->get('guest'))
		{
			if ($juser->authorize('com_resources', 'manage')
			 || $juser->authorize('com_groups', 'manage'))
			{
				$sql1 .= '';
			}
			else
			{
				$ugs = \Hubzero\User\Helper::getGroups($juser->get('id'), 'members');
				$groups = array();
				if ($ugs && count($ugs) > 0)
				{
					foreach ($ugs as $ug)
					{
						$groups[] = $ug->cn;
					}
				}
				$g = "'" . implode("','", $groups) . "'";

				$sql1 .= "AND (w.access!=1 OR (w.access=1 AND (w.group_cn IN ($g) OR w.created_by='" . $juser->get('id') . "'))) ";
			}
		}
		else
		{
			$sql1 .= "AND w.access!=1 ";
		}
		$sql1 .= "GROUP BY pageid ORDER BY ranking DESC, title LIMIT 10";

		// Build the query that checks resource parents
		$sql2 = "SELECT DISTINCT r.id, NULL AS pageid, NULL AS version, r.title, r.alias, r.introtext, r.type, r.published, r.publish_up, "
			 . " NULL AS scope, r.rating, r.times_rated, r.ranking, rt.type AS section, NULL AS `group` "
			 . " FROM #__resource_types AS rt, #__resources AS r"
			 . " JOIN #__resource_assoc AS a ON r.id=a.parent_id"
			 . " LEFT JOIN #__resource_types AS t ON r.logical_type=t.id"
			 . " WHERE r.published=1 AND a.child_id=" . $resource->id . " AND r.type=rt.id AND r.type!=8 ";
		if (!$juser->get('guest'))
		{
			if ($juser->authorize('com_resources', 'manage')
			 || $juser->authorize('com_groups', 'manage'))
			{
				$sql2 .= '';
			}
			else
			{
				$sql2 .= "AND (r.access!=1 OR (r.access=1 AND (r.group_owner IN ($g) OR r.created_by='" . $juser->get('id') . "'))) ";
			}
		}
		else
		{
			$sql2 .= "AND r.access=0 ";
		}
		$sql2 .= "ORDER BY r.ranking LIMIT 10";

		// Build the final query
		$query = "SELECT k.* FROM (($sql1) UNION ($sql2)) AS k ORDER BY ranking DESC LIMIT 10";

		// Execute the query
		$database->setQuery($query);

		$view = new \Hubzero\Plugin\View(array(
			'folder'  => $this->_type,
			'element' => $this->_name,
			'name'    => 'browse'
		));

		// Instantiate a view
		if ($miniview)
		{
			$view->setLayout('mini');
		}

		// Pass the view some info
		$view->option   = $option;
		$view->resource = $resource;
		$view->related  = $database->loadObjectList();
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		// Return the output
		$arr['html'] = $view->loadTemplate();

		// Return the an array of content
		return $arr;
	}
}

