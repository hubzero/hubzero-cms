<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Tags plugin class for wiki pages
 */
class plgTagsWiki extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Retrieve records for items tagged with specific tags
	 *
	 * @param   array    $tags        Tags to match records against
	 * @param   mixed    $limit       SQL record limit
	 * @param   integer  $limitstart  SQL record limit start
	 * @param   string   $sort        The field to sort records by
	 * @param   mixed    $areas       An array or string of areas that should retrieve records
	 * @return  mixed    Returns integer when counting records, array when retrieving records
	 */
	public function onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)
	{
		$response = array(
			'name'    => $this->_name,
			'title'   => Lang::txt('PLG_TAGS_WIKI'),
			'total'   => 0,
			'results' => null,
			'sql'     => ''
		);

		if (empty($tags))
		{
			return $response;
		}

		$database = App::get('db');

		$ids = array();
		foreach ($tags as $tag)
		{
			$ids[] = $tag->get('id');
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');

		// Instantiate some needed objects
		$wp = new \Components\Wiki\Tables\Page($database);

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
					$response['results'][$key]->href = Route::url($response['results'][$key]->href);
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
	 * @param   array   $filters  Options for building the query
	 * @return  string  SQL
	 */
	private function _buildPluginQuery($filters=array())
	{
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$searchquery = $filters['search'];
			$phrases = $searchquery->searchPhrases;
		}

		$groupAuth = array();
		$groupAuth[] = 'xg.plugins LIKE \'%wiki=anyone%\'';
		if (!User::isGuest())
		{
			$groupAuth[] = 'xg.plugins LIKE \'%wiki=registered%\'';
			$profile = \Hubzero\User\Profile::getInstance(User::get('id'));
			$gids = array();
			$profileGroups = $profile->getGroups();
			if (!empty($profileGroups))
			{
				foreach ($profile->getGroups() as $group)
				{
					$gids[] = $group->gidNumber;
				}
				if (count($gids) > 0)
				{
					$groupAuth[] = '(xg.plugins LIKE \'%wiki=members%\' AND xg.gidNumber IN (' . join(',', $gids) . '))';
				}
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
	 * @return  boolean  True if logged in
	 */
	private function _authorize()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return false;
		}
		return true;
	}
}
