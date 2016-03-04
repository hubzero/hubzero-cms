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

namespace Components\Resources\Helpers;

use Components\Tags\Models\Cloud;
use Components\Tags\Tables\Tag;

require_once(dirname(dirname(__DIR__)) . DS . 'com_tags' . DS . 'models' . DS . 'cloud.php');

/**
 * Resources Tagging class
 */
class Tags extends Cloud
{
	/**
	 * Object type, used for linking objects (such as resources) to tags
	 *
	 * @var string
	 */
	protected $_scope = 'resources';

	/**
	 * Get all tags with a resource association
	 *
	 * @param      integer $id   Resource ID
	 * @param      integer $type Resource type (optional)
	 * @param      string  $tag  Parameter description (if any) ...
	 * @return     array
	 */
	public function get_tags_with_objects($id=0, $type=0, $tag='')
	{
		$now = \Date::toSql();

		$this->_db->setQuery("SELECT objectid FROM `#__tags` AS t, `#__tags_object` AS o WHERE o.tagid=t.id AND t.tag='$tag' AND o.tbl='$this->_scope'");
		$objs = $this->_db->loadObjectList();

		$ids = '';
		if ($objs)
		{
			$s = array();
			foreach ($objs as $obj)
			{
				$s[] = $obj->objectid;
			}
			$ids = implode(',', $s);
		}

		$sql = "SELECT t.id, t.tag, t.raw_tag, r.id AS rid, 0 AS ucount, NULL AS rids
				FROM `#__tags` AS t, `#__tags_object` AS o, `#__resources` AS r
				WHERE o.tbl='$this->_scope'
				AND o.tagid=t.id
				AND t.admin=0
				AND o.objectid=r.id
				AND r.published=1
				AND r.standalone=1
				AND (r.publish_up = '0000-00-00 00:00:00' OR r.publish_up <= '$now')
				AND (r.publish_down = '0000-00-00 00:00:00' OR r.publish_down >= '$now') ";
		if ($type)
		{
			$sql .= "AND r.type=" . $type . " ";
		}

		if (!\User::isGuest())
		{
			$xgroups = \Hubzero\User\Helper::getGroups(\User::get('id'), 'all');
			if ($xgroups != '')
			{
				$usersgroups = self::getUsersGroups($xgroups);
				if (count($usersgroups) > 1)
				{
					$groups = implode("','", $usersgroups);
				}
				else
				{
					$groups = count($usersgroups) ? $usersgroups[0] : '';
				}
				$sql .= "AND (r.access=0 OR r.access=1 OR r.access=3 OR (r.access=4 AND (r.group_owner IN ('" . $groups . "') ";
				foreach ($usersgroups as $group)
				{
					$sql .= " OR r.group_access LIKE '%;" . $group . ";%'";
				}
				$sql .= "))) ";
			}
			else
			{
				$sql .= "AND (r.access=0 OR r.access=1 OR r.access=3) ";
			}
		}
		else
		{
			$sql .= "AND (r.access=0 OR r.access=3) ";
		}
		if ($ids)
		{
			$sql .= "AND o.objectid IN ($ids) ";
		}
		$sql .= "ORDER BY t.raw_tag ASC";

		$this->_db->setQuery($sql);
		$results = $this->_db->loadObjectList();

		$rows = array();
		if ($results)
		{
			foreach ($results as $result)
			{
				if (!isset($rows[$result->id]))
				{
					$rows[$result->id] = $result;
					$rows[$result->id]->ucount++;
					$rows[$result->id]->rids = array($result->rid);
				}
				else
				{
					if (!in_array($result->rid, $rows[$result->id]->rids))
					{
						$rows[$result->id]->ucount++;
						$rows[$result->id]->rids[] = $result->rid;
					}
				}
			}
		}
		return $rows;
	}

	/**
	 * Push group alias into array for easier searching
	 *
	 * @param      array $groups User's gorups
	 * @return     array
	 */
	public static function getUsersGroups($groups)
	{
		$arr = array();

		if (!empty($groups))
		{
			foreach ($groups as $group)
			{
				if ($group->regconfirmed)
				{
					$arr[] = $group->cn;
				}
			}
		}

		return $arr;
	}

	/**
	 * Get all resources associated with a tag
	 *
	 * @param      string  $tag      Tag to find data for
	 * @param      integer $id       Resource ID
	 * @param      integer $type     Resource type
	 * @param      string  $sortby   Sort data by
	 * @param      string  $tag2     Secondary tag
	 * @param      array   $filterby Extra, optional filters
	 * @return     array
	 */
	public function get_objects_on_tag($tag='', $id=0, $type=0, $sortby='title', $tag2='', $filterby=array())
	{
		$now  = \Date::toSql();

		if ($tag || $tag2)
		{
			$query  = "SELECT C.id, TA.tag, COUNT(DISTINCT TA.tag) AS uniques, ";
			if ($type == 7)
			{
				$query.= "TV.title ";
			}
			else
			{
				$query.= "C.title ";
			}
			switch ($sortby)
			{
				case 'users':
					$query .= ", (SELECT rs.users FROM #__resource_stats AS rs WHERE rs.resid=C.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS users ";
				break;
				case 'jobs':
					$query .= ", (SELECT rs.jobs FROM #__resource_stats AS rs WHERE rs.resid=C.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS jobs ";
				break;
			}
			$query .= "FROM #__resources AS C ";
			if ($id)
			{
				$query .= "INNER JOIN #__resource_assoc AS RA ON (RA.child_id = C.id AND RA.parent_id=" . $id . ")";
			}
			if ($type == 7)
			{
				if (!empty($filterby))
				{
					$query .= " LEFT JOIN #__resource_taxonomy_audience AS TTA ON C.id=TTA.rid ";
				}
				$query .= ", #__tool_version as TV ";
			}
			$query .= ", `#__tags_object` AS RTA INNER JOIN #__tags AS TA ON (RTA.tagid = TA.id) ";
		}
		else
		{
			$query  = "SELECT C.id,  ";
			if ($type == 7)
			{
				$query .= "TV.title ";
			}
			else
			{
				$query .= "C.title ";
			}
			switch ($sortby)
			{
				case 'users':
					$query .= ", (SELECT rs.users FROM #__resource_stats AS rs WHERE rs.resid=C.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS users ";
				break;
				case 'jobs':
					$query .= ", (SELECT rs.jobs FROM #__resource_stats AS rs WHERE rs.resid=C.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS jobs ";
				break;
			}
			$query .= "FROM #__resources AS C ";
			if ($id)
			{
				$query .= "INNER JOIN #__resource_assoc AS RA ON (RA.child_id = C.id AND RA.parent_id=" . $id . ")";
			}
			if ($type == 7)
			{
				if (!empty($filterby))
				{
					$query .= " LEFT JOIN #__resource_taxonomy_audience AS TTA ON C.id=TTA.rid ";
				}
				$query .= ", #__tool_version as TV ";
			}
		}

		$query .= "WHERE C.published=1 AND C.standalone=1 ";
		if ($type)
		{
			$query .= "AND C.type=" . $type . " ";
		}
		if ($type == 7)
		{
			$query .= " AND TV.toolname=C.alias AND TV.state=1 AND TV.revision = (SELECT MAX(revision) FROM #__tool_version as TV WHERE TV.toolname=C.alias AND TV.state=1 GROUP BY TV.toolid) ";
		}
		if (!empty($filterby) && $type == 7)
		{
			$fquery = " AND ((";
			for ($i=0, $n=count($filterby); $i < $n; $i++)
			{
				$fquery .= " TTA." . $filterby[$i] . " = '1'";
				$fquery .= ($i + 1) == $n ? "" : " OR ";
			}
			$fquery .= ") OR (";
			for ($i=0, $n=count($filterby); $i < $n; $i++)
			{
				$fquery .= " TTA." . $filterby[$i] . " IS NULL";
				$fquery .= ($i + 1) == $n ? "" : " OR ";
			}
			$fquery .= "))";
			$query .= $fquery;
		}
		$query .= "AND (C.publish_up = '0000-00-00 00:00:00' OR C.publish_up <= '" . $now . "') ";
		$query .= "AND (C.publish_down = '0000-00-00 00:00:00' OR C.publish_down >= '" . $now . "') AND ";

		if (!\User::isGuest())
		{
			$xgroups = \Hubzero\User\Helper::getGroups(\User::get('id'), 'all');
			if ($xgroups != '')
			{
				$usersgroups = self::getUsersGroups($xgroups);
				if (count($usersgroups) > 1)
				{
					$groups = implode("','", $usersgroups);
				}
				else
				{
					$groups = count($usersgroups) ? $usersgroups[0] : '';
				}
				$query .= "(C.access=0 OR C.access=1 OR C.access=3 OR (C.access=4 AND (C.group_owner IN ('" . $groups . "') ";
				foreach ($usersgroups as $group)
				{
					$query .= " OR C.group_access LIKE '%;" . $group . ";%'";
				}
				$query .= "))) ";
			} else {
				$query .= "(C.access=0 OR C.access=1 OR C.access=3) ";
			}
		}
		else
		{
			$query .= "(C.access=0 OR C.access=3) ";
		}
		if ($tag || $tag2)
		{
			if ($tag && !$tag2)
			{
				$query .= "AND RTA.objectid=C.id AND RTA.tbl='$this->_scope' AND (TA.tag IN ('" . $tag . "'))";
				$query .= " GROUP BY C.id HAVING uniques=1";
			}
			else if ($tag2 && !$tag)
			{
				$query .= "AND RTA.objectid=C.id AND RTA.tbl='$this->_scope' AND (TA.tag IN ('" . $tag2 . "'))";
				$query .= " GROUP BY C.id HAVING uniques=1";
			}
			else if ($tag && $tag2)
			{
				$query .= "AND RTA.objectid=C.id AND RTA.tbl='$this->_scope' AND (TA.tag IN ('" . $tag . "','" . $tag2 . "'))";
				$query .= " GROUP BY C.id HAVING uniques=2";
			}
		}
		switch ($sortby)
		{
			case 'ranking':
				$sort = "ranking DESC";
			break;
			case 'date':
				$sort = "publish_up DESC";
			break;
			case 'users':
				$sort = "users DESC";
			break;
			case 'jobs':
				$sort = "jobs DESC";
			break;
			default:
			case 'title':
				$sort = "title ASC";
			break;
		}
		$query .= " ORDER BY " . $sort . ", publish_up";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Check if a tag is being used
	 *
	 * @param      string  $tag   Tag
	 * @param      integer $id    Resource ID
	 * @param      string  $alias Resource alias
	 * @return     mixed Return description (if any) ...
	 */
	public function checkTagUsage($tag, $id=0, $alias='')
	{
		if (!$id && !$alias)
		{
			return false;
		}
		if ($id)
		{
			$query = "SELECT COUNT(*)
						FROM `#__tags_object` AS ta, `#__tags` AS t
						WHERE ta.tagid=t.id
						AND t.tag='" . $tag . "'
						AND ta.tbl='resources'
						AND ta.objectid=" . $id;
		}
		if (!$id && $alias)
		{
			$query = "SELECT COUNT(*)
						FROM `#__tags_object` AS ta, `#__tags` AS t, `#__resources` AS r
						WHERE ta.tagid=t.id
						AND t.tag='" . $tag . "'
						AND ta.tbl='resources'
						AND ta.objectid=r.id
						AND r.alias='" . $alias . "'";
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a singular field, such as ID, for all items with a specific tag
	 *
	 * @param      string $tag  Tag to get data for
	 * @param      string $rtrn Field to return
	 * @return     array
	 */
	public function getTagUsage($tag, $rtrn='id')
	{
		if (!$tag)
		{
			return array();
		}

		$query = "SELECT r.$rtrn
					FROM `#__tags_object` AS ta, `#__tags` AS t, `#__resources` AS r
					WHERE ta.tagid=t.id
					AND t.tag='".$tag."'
					AND ta.tbl='resources'
					AND ta.objectid=r.id";

		$this->_db->setQuery($query);
		return $this->_db->loadColumn();
	}

	/**
	 * Get a tag cloud for an object
	 *
	 * @param      integer $showsizes Show tag size based on use?
	 * @param      integer $admin     Show admin tags?
	 * @param      integer $objectid  Object ID
	 * @return     mixed Return description (if any) ...
	 */
	public function getTopTagCloud($limit, $tagstring='')
	{
		$t = new Tag($this->_db);
		$tags = $t->getTopTags($limit, $this->_scope, 'tcount DESC', 0);

		return $this->buildTopCloud($tags, 'alpha', 0, $tagstring);
	}

	/**
	 * Get a tag cloud for an object
	 *
	 * @param      integer $showsizes Show tag size based on use?
	 * @param      integer $admin     Show admin tags?
	 * @param      integer $objectid  Object ID
	 * @return     mixed Return description (if any) ...
	 */
	public function getTopTags($limit)
	{
		$t = new Tag($this->_db);
		return $t->getTopTags($limit, $this->_scope, 'tcount DESC', 0);
	}

	/**
	 * Get a tag cloud for an object
	 *
	 * @param      integer $showsizes Show tag size based on use?
	 * @param      integer $admin     Show admin tags?
	 * @param      integer $objectid  Object ID
	 * @return     mixed Return description (if any) ...
	 */
	public function getTopTagString($limit)
	{
		$t = new Tag($this->_db);

		$tags = $t->getTopTags($limit, $this->_scope, 'tcount DESC', 0);

		if ($tags && count($tags) > 0)
		{
			$tagarray = array();
			foreach ($tags as $tag)
			{
				$tagarray[] = $tag->raw_tag;
			}
			$tags = implode(', ', $tagarray);
		}
		else
		{
			$tags = (is_array($tags)) ? implode('', $tags) : '';
		}
		return $tags;
	}

	/**
	 * Turn a comma-separated string of tags into an array of normalized tags
	 *
	 * @param      string  $tag_string Comma-separated string of tags
	 * @param      integer $keep       Use normalized tag as array key
	 * @return     array
	 */
	public function parseTags($tag_string, $keep=0)
	{
		return $this->_parse($tag_string, $keep);
	}

	/**
	 * Turn a string of tags to an array
	 *
	 * @param      string $tag Tag string
	 * @return     mixed
	 */
	public function parseTopTags($tag, $remove='')
	{
		$bunch = $this->_parse($tag);

		$tags = array();
		if ($remove)
		{
			foreach ($bunch as $t)
			{
				if ($remove == $t)
				{
					continue;
				}
				$tags[] = $t;
			}
		}
		else
		{
			return $bunch;
		}

		return $tags;
	}

	/**
	 * Build a tag cloud
	 *
	 * @param      array   $tags      List of tags
	 * @param      string  $sort      How to sort tags?
	 * @param      integer $showsizes Show tag size based on use?
	 * @return     string HTML
	 */
	public function buildTopCloud($tags, $sort='alpha', $showsizes=0, $tagstring='')
	{
		$html = '';

		if ($tags && count($tags) > 0)
		{
			$lst = array();
			if (is_string($tagstring))
			{
				$lst = $this->_parse($tagstring);
			}
			else
			{
				$lst = $tagstring;
			}

			$min_font_size = 1;
			$max_font_size = 1.8;

			if ($showsizes)
			{
				$retarr = array();
				foreach ($tags as $tag)
				{
					$retarr[$tag->raw_tag] = $tag->count;
				}
				ksort($retarr);

				$max_qty = max(array_values($retarr));  // Get the max qty of tagged objects in the set
				$min_qty = min(array_values($retarr));  // Get the min qty of tagged objects in the set

				// For ever additional tagged object from min to max, we add $step to the font size.
				$spread = $max_qty - $min_qty;
				if (0 == $spread)
				{ // Divide by zero
					$spread = 1;
				}
				$step = ($max_font_size - $min_font_size)/($spread);
			}

			// build HTML
			$html .= '<ol class="tags">' . "\n";
			$tll = array();
			foreach ($tags as $tag)
			{
				$class = '';
				switch ($tag->admin)
				{
					/*case 0:
						$class = ' class="restricted"';
					break;*/
					case 1:
						$class = ' class="admin"';
					break;
				}

				$lsst = $lst;

				if ($tagstring)
				{
					if (!in_array($tag->tag, $lst))
					{
						$lsst[] = $tag->tag;
					}
				}
				else
				{
					$lsst = array($tag->tag);
				}

				$tag->raw_tag = stripslashes($tag->raw_tag);
				$tag->raw_tag = str_replace('&amp;', '&', $tag->raw_tag);
				$tag->raw_tag = str_replace('&', '&amp;', $tag->raw_tag);
				if ($showsizes == 1)
				{
					$size = $min_font_size + ($tag->count - $min_qty) * $step;
					$tll[$tag->tag] = "\t".'<li' . $class . '><span style="font-size: ' . round($size, 1) . 'em"><a class="tag' . ($tag->admin ? ' admin' : '') . '" href="' . Route::url('index.php?option=com_resources&task=browse&tag=' . implode(',', $lsst)) . '">' . stripslashes($tag->raw_tag) . '</a></li>' . "\n"; //' <span>' . $tag->count . '</span></a></span></li>' . "\n";
				}
				else
				{
					$tll[$tag->tag] = "\t".'<li' . $class . '><a class="tag' . ($tag->admin ? ' admin' : '') . '" href="' . urldecode(Route::url('index.php?option=com_resources&task=browse&tag=' . implode(',', $lsst))) . '">' . stripslashes($tag->raw_tag) . '</a></li>' . "\n"; //' <span>' . $tag->count . '</span></a></li>' . "\n";
				}
			}
			if ($sort == 'alpha')
			{
				ksort($tll);
				$html .= implode('', $tll);
			}
			$html .= '</ol>' . "\n";
		}

		return $html;
	}
}

