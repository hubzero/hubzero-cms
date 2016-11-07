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
 * Members Plugin class for usage
 */
class plgMembersUsage extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   object  $user    User
	 * @param   object  $member  MembersProfile
	 * @return  array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		$areas = array(
			'usage' => Lang::txt('PLG_MEMBERS_USAGE'),
			'icon'  => 'f080'
		);
		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param   object  $user    User
	 * @param   object  $member  MembersProfile
	 * @param   string  $option  Component name
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;

		$arr = array(
			'html' => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				$returnhtml = false;
			}
		}

		$database = App::get('db');
		$tables = $database->getTableList();

		if ($returnhtml &&
			(!in_array($database->getPrefix() . 'author_stats', $tables)
		 || !in_array($database->getPrefix() . 'metrics_author_cluster', $tables)))
		{
			$arr['html'] = '<p class="error">' . Lang::txt('PLG_MEMBERS_USAGE_ERROR_MISSING_TABLE') . '</p>';
			return $arr;
		}

		if ($returnhtml)
		{
			$view = $this->view('default', 'summary');

			$view->member = $member;
			$view->option = $option;
			$view->contribution = $this->first_last_contribution($member->get('id'));
			$view->rank = $this->get_rank($member->get('id'));

			$view->total_tool_users    = $this->get_total_stats($member->get('id'), 'tool_users',14);
			$view->total_andmore_users = $this->get_total_stats($member->get('id'), 'andmore_users',14);
			$view->citation_count      = self::get_citationcount(null, $member->get('id'));

			$cluster = self::get_classroom_usage($member->get('id'));
			$view->cluster_classes = $cluster['classes'];
			$view->cluster_users   = $cluster['users'];
			$view->cluster_schools = $cluster['schools'];

			$sql = 'SELECT DISTINCT r.id, r.title, DATE_FORMAT(r.publish_up, "%d %b %Y") AS publish_up, rt.type
					FROM `#__resources` AS r
					LEFT JOIN `#__resource_types` AS rt ON r.TYPE=rt.id
					LEFT JOIN `#__author_assoc` AS aa ON aa.subid=r.id AND aa.subtable="resources"
					WHERE r.standalone=1 AND r.published=1 AND r.type=7 AND (aa.authorid="'.intval($member->get('id')).'") AND (r.access=0 OR r.access=3)
					ORDER BY r.publish_up DESC';

			$database->setQuery($sql);
			$view->tool_stats    = $database->loadObjectList();
			$view->tool_total_12 = $this->get_total_stats($member->get('id'), 'tool_users', 12);
			$view->tool_total_14 = $this->get_total_stats($member->get('id'), 'tool_users', 14);

			$sql = "SELECT DISTINCT r.id, r.title, DATE_FORMAT(r.publish_up, '%d %b %Y') AS publish_up, rt.type
					FROM `#__resources` AS r
					LEFT JOIN `#__resource_types` AS rt ON r.TYPE=rt.id
					LEFT JOIN `#__author_assoc` AS aa ON aa.subid=r.id AND aa.subtable='resources'
					WHERE r.standalone=1 AND r.published=1 AND r.type<>7 AND aa.authorid=" . $database->quote(intval($member->get('id')) . " AND aa.role!='submitter' AND (r.access=0 OR r.access=3)
					ORDER BY r.publish_up DESC";

			$database->setQuery($sql);
			$view->andmore_stats    = $database->loadObjectList();
			$view->andmore_total_12 = $this->get_total_stats($member->get('id'), 'andmore_users', 12);
			$view->andmore_total_14 = $this->get_total_stats($member->get('id'), 'andmore_users', 14);

			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}

			$arr['html'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Convert negative IDs to n IDS (-15 -> n15)
	 *
	 * @param   number  $uid  User ID
	 * @return  mixed
	 */
	public function uid($uid)
	{
		if ($uid < 0)
		{
			return 'n' . -$uid;
		}
		return $uid;
	}

	/**
	 * Get contributions
	 *
	 * @param   string  $authorid  User ID
	 * @return  array
	 */
	public function first_last_contribution($authorid)
	{
		$database = App::get('db');

		$sql = "SELECT COUNT(DISTINCT aa.subid) as contribs, DATE_FORMAT(MIN(res.publish_up), '%d %b %Y') AS first_contrib, DATE_FORMAT(MAX(res.publish_up), '%d %b %Y') AS last_contrib
				FROM `#__resources` AS res, `#__author_assoc` AS aa, `#__resource_types` AS restypes
				WHERE res.id = aa.subid AND res.type = restypes.id AND aa.authorid = " . $database->quote($authorid) . " AND aa.role!='submitter'
				AND res.standalone = 1
				AND res.published = 1
				AND (res.access=0 OR res.access=3)
				AND aa.subtable = 'resources'";

		$database->setQuery($sql);
		$results = $database->loadObjectList();

		$contribution = array();
		$contribution['contribs'] = '';
		$contribution['first']    = '';
		$contribution['last']     = '';

		if ($results)
		{
			foreach ($results as $row)
			{
				$contribution['contribs'] = $row->contribs;
				$contribution['first']    = $row->first_contrib;
				$contribution['last']     = $row->last_contrib;
			}
		}

		return $contribution;
	}

	/**
	 * Get simulation count
	 *
	 * @param   integer  $resid   Resource ID
	 * @param   string   $period  Time period to find data for
	 * @return  integer
	 */
	public static function get_simcount($resid, $period)
	{
		$database = App::get('db');

		$sql = "SELECT jobs FROM `#__resource_stats_tools` WHERE resid=" . $database->quote($resid) . " AND period=" . $database->quote($period) . " ORDER BY datetime DESC LIMIT 1";

		$database->setQuery($sql);
		$result = $database->loadResult();
		if ($result)
		{
			return $result;
		}

		return 0;
	}

	/**
	 * Get a count of users
	 *
	 * @param   string  $resid    Resource ID
	 * @param   string  $period   Time period
	 * @param   string  $restype  Resource type
	 * @return  string
	 */
	public static function get_usercount($resid, $period, $restype='0')
	{
		$database = App::get('db');

		if ($restype == '7')
		{
			$table = "`#__resource_stats_tools`";
		}
		else
		{
			$table = "`#__resource_stats`";
		}

		$data = '-';
		$sql = "SELECT MAX(datetime), users FROM " . $table . " WHERE resid = " . $database->quote($resid) . " AND period = " . $database->quote($period) . " GROUP BY datetime ORDER BY datetime DESC LIMIT 1";

		$database->setQuery($sql);
		$results = $database->loadObjectList();
		if ($results)
		{
			foreach ($results as $row)
			{
				$data = $row->users;
			}
		}

		return $data;
	}

	/**
	 * Get classroom usage
	 *
	 * @param   mixed  $authorid  User ID
	 * @return  array
	 */
	public function get_classroom_usage($authorid)
	{
		$database = App::get('db');

		$cluster['classes'] = 0;
		$cluster['users']   = 0;
		$cluster['schools'] = 0;

		$sql = "SELECT classes FROM `#__metrics_author_cluster` WHERE authorid = " . $database->quote($authorid);
		$database->setQuery($sql);
		$result = $database->loadResult();
		if ($result)
		{
			$cluster['classes'] = $result;
		}

		$sql = "SELECT users FROM `#__metrics_author_cluster` WHERE authorid = " . $database->quote($authorid);
		$database->setQuery($sql);
		$result = $database->loadResult();
		if ($result)
		{
			$cluster['users'] = $result;
		}

		$sql = "SELECT schools FROM `#__metrics_author_cluster` WHERE authorid = " . $database->quote($authorid);
		$database->setQuery($sql);
		$result = $database->loadResult();
		if ($result)
		{
			$cluster['schools'] = $result;
		}
		return $cluster;

	}

	/**
	 * Get a count of citations
	 *
	 * @param   string  $resid     Resource ID
	 * @param   mixed   $authorid  User ID
	 * @return  string
	 */
	public static function get_citationcount($resid, $authorid=0)
	{
		$database = App::get('db');

		if ($authorid)
		{
			$sql = "SELECT COUNT(DISTINCT (c.id))
					FROM `#__citations` c, `#__citations_assoc` ca, `#__author_assoc` aa, `#__resources` r
					WHERE c.id = ca.cid AND r.id = ca.oid AND r.id = aa.subid AND  aa.subtable = 'resources' AND ca.tbl = 'resource' AND r.published=1
					AND r.standalone=1 AND aa.authorid = " . $database->quote($authorid);
		}
		else
		{
			$sql = "SELECT COUNT(DISTINCT (c.id)) AS citations
					FROM `#__resources` r, `#__citations` c, `#__citations_assoc` ca
					WHERE r.id = ca.oid AND ca.cid = c.id AND ca.tbl = 'resource' AND standalone=1 AND r.id = " . $database->quote($resid);
		}

		$database->setQuery($sql);
		$result = $database->loadResult();
		if ($result)
		{
			return $result;
		}
		return '-';
	}

	/**
	 * Get the user's rank
	 *
	 * @param   integer  $authorid  User ID
	 * @return  string
	 */
	public function get_rank($authorid)
	{
		$database = App::get('db');

		$rank = 0;
		$i = 1;
		$sql = 'SELECT a.uidNumber AS aid, COUNT(DISTINCT aa.subid) AS contribs
				FROM `#__xprofiles` a, `#__resources` res, `#__author_assoc` aa
				WHERE a.uidNumber = aa.authorid AND res.id = aa.subid AND res.published=1 AND (res.access=0 OR res.access=3) AND aa.subtable = "resources"
				AND res.standalone=1 GROUP BY aid ORDER BY contribs DESC';

		$database->setQuery($sql);
		$results = $database->loadObjectList();

		if ($results)
		{
			foreach ($results as $row)
			{
				if ($row->aid == $authorid)
				{
					$rank = $i;
				}
				$i++;
			}
		}

		if ($rank)
		{
			$sql = 'SELECT COUNT(DISTINCT a.uidNumber) as authors
				FROM `#__xprofiles` a, `#__author_assoc` aa, `#__resources` res
				WHERE a.uidNumber=aa.authorid AND aa.subid=res.id AND aa.subtable="resources" AND res.published=1 AND (res.access=0 OR res.access=3)
				AND res.standalone=1';

			$database->setQuery($sql);
			$total_authors = $database->loadResult();

			$rank = $rank . ' / ' . $total_authors;
		}
		else
		{
			$rank = '-';
		}
		return $rank;
	}

	/**
	 * Get total stats for a user
	 *
	 * @param   string  $authorid   User ID
	 * @param   string  $user_type  User type
	 * @param   string  $period     Time period
	 * @return  integer
	 */
	public function get_total_stats($authorid, $user_type, $period)
	{
		$database = App::get('db');

		$sql = "SELECT " . $user_type . " FROM `#__author_stats` WHERE authorid = " . $database->quote($authorid) . " AND period = " . $database->quote($period) . " ORDER BY datetime DESC LIMIT 1";

		$database->setQuery($sql);
		return $database->loadResult();
	}
}

