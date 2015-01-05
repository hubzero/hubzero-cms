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
 * Tags plugin class for wiki pages
 */
class plgTagsWiki extends \Hubzero\Plugin\Plugin
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
			'title'   => JText::_('PLG_TAGS_WIKI'),
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

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');

		// Instantiate some needed objects
		$wp = new WikiTablePage($database);

		// Build query
		$filters = array();
		$filters['tags'] = $ids;
		$filters['sortby'] = ($sort) ? $sort : 'date';
		$filters['authorized'] = $this->_authorize();

		$filters['select'] = 'count';
		$filters['limit']  = 'all';

		$database->setQuery($this->_buildPluginQuery($filters));
		$response['total'] = $database->loadResult();

		if ($areas && $areas == $response['name'])
		{
			$filters['select']     = 'records';
			$filters['limit']      = $limit;
			$filters['limitstart'] = $limitstart;

			$database->setQuery($this->_buildPluginQuery($filters));
			$response['results'] = $database->loadObjectList();

			// Did we get any results?
			if ($response['results'])
			{
				// Loop through the results and set each item's HREF
				foreach ($response['results'] as $key => $row)
				{
					$response['results'][$key]->href = JRoute::_($response['results'][$key]->href);
					$response['results'][$key]->text = $response['results'][$key]->itext;
				}
			}
		}
		else
		{
			$filters['select']     = 'records';
			$filters['limitstart'] = $limitstart;

			$response['sql'] = $this->_buildPluginQuery($filters);
		}

		return $response;
	}

	/**
	 * Build a database query
	 *
	 * @param      array $filters Options for building the query
	 * @return     string SQL
	 */
	private function _buildPluginQuery($filters=array())
	{
		$juser = JFactory::getUser();

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$searchquery = $filters['search'];
			$phrases = $searchquery->searchPhrases;
		}

		$groupAuth = array();
		$groupAuth[] = 'xg.plugins LIKE \'%wiki=anyone%\'';
		if (!$juser->get('guest'))
		{
			$groupAuth[] = 'xg.plugins LIKE \'%wiki=registered%\'';
			$profile = \Hubzero\User\Profile::getInstance($juser->get('id'));
			$gids = array();
			foreach ($profile->getGroups() as $group)
			{
				$gids[] = $group->gidNumber;
			}
			if (count($gids) > 0)
			{
				$groupAuth[] = '(xg.plugins LIKE \'%wiki=members%\' AND xg.gidNumber IN (' . join(',', $gids) . '))';
			}
		}

		if (isset($filters['select']) && $filters['select'] == 'count')
		{
			if (isset($filters['tags']))
			{
				$query = "SELECT COUNT(f.id) FROM (SELECT v.pageid AS id, COUNT(DISTINCT t.tagid) AS uniques ";
			}
			else
			{
				$query = "SELECT COUNT(*) FROM (SELECT COUNT(DISTINCT v.pageid) ";
			}
		}
		else
		{
			$query = "SELECT v.pageid AS id, w.title, w.pagename AS alias, v.pagetext AS itext, v.pagehtml AS ftext, w.state, v.created, v.created_by,
						v.created AS modified, v.created AS publish_up, NULL AS publish_down,
						CASE
							WHEN w.group_cn LIKE 'pr-%' THEN concat('index.php?option=com_projects&scope=', w.scope, '&pagename=', w.pagename)
							WHEN w.group_cn != '' THEN CONCAT('index.php?option=com_groups&scope=', w.scope, '&pagename=', w.pagename)
							ELSE CONCAT('index.php?option=com_wiki&pagename=', w.pagename)
						END AS href,
						'wiki' AS section ";
			if (isset($filters['tags']))
			{
				$query .= ", COUNT(DISTINCT t.tagid) AS uniques ";
			}
			$query .= ", w.params, NULL AS rcount, w.scope AS data1, NULL AS data2, NULL AS data3 ";
		}
		$query .= "FROM #__wiki_page AS w
					INNER JOIN #__wiki_version AS v ON v.id=w.version_id
					LEFT JOIN `#__xgroups` xg ON xg.cn = w.group_cn";
		if (isset($filters['tags']))
		{
			$query .= ", #__tags_object AS t ";
		}
		$query .= "WHERE w.id=v.pageid AND v.approved=1 AND w.state < 2 AND (xg.gidNumber IS NULL OR (" . implode(' OR ', $groupAuth) . "))";
		if (isset($filters['tags']))
		{
			$ids = implode(',', $filters['tags']);
			$query .= "AND w.id=t.objectid AND t.tbl='wiki' AND t.tagid IN ($ids) ";
		}

		$query .= "GROUP BY pageid ";
		if (isset($filters['tags']))
		{
			$query .= "HAVING uniques=" . count($filters['tags']) . " ";
		}
		if (isset($filters['select']) && $filters['select'] != 'count')
		{
			if (isset($filters['sortby']))
			{
				$query .= "ORDER BY ";
				switch ($filters['sortby'])
				{
					case 'title':     $query .= 'title ASC';      break;
					case 'id':        $query .= "id DESC";        break;
					case 'rating':    $query .= "rating DESC";    break;
					case 'ranking':   $query .= "ranking DESC";   break;
					case 'relevance': $query .= "relevance DESC"; break;
					case 'usage':
					case 'hits':      $query .= 'hits DESC';      break;
					case 'date':
					default:          $query .= 'created DESC';   break;
				}
			}
			if (isset($filters['limit']) && $filters['limit'] != 'all')
			{
				$query .= " LIMIT " . $filters['limitstart'] . "," . $filters['limit'];
			}
		}
		if (isset($filters['select']) && $filters['select'] == 'count')
		{
			$query .= ") AS f";
		}
		return $query;
	}

	/**
	 * Check if a user is logged in
	 *
	 * @return     boolean True if logged in
	 */
	private function _authorize()
	{
		// Check if they are logged in
		$juser = JFactory::getUser();
		if ($juser->get('guest'))
		{
			return false;
		}
		return true;
	}
}
