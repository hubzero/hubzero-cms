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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'helpers' . DS . 'handler.php');

/**
 * Publication Tagging class
 */
class PublicationTags extends TagsHandler
{
	/**
	 * Constructor
	 *
	 * @param      object $db     JDatabase
	 * @param      array  $config Optional configurations
	 * @return     void
	 */
	public function __construct($db, $config=array())
	{
		$this->_db  = $db;
		$this->_tbl = 'publications';
	}

	/**
	 * Get tags on a publication
	 *
	 * @param      integer $id        Resource ID
	 * @param      integer $tagger_id Tagger ID
	 * @param      integer $strength  Tag strength
	 * @param      integer $admin     Admin flag
	 * @return     array
	 */
	public function getTags($id, $tagger_id=0, $strength=0, $admin=0)
	{
		$sql = "SELECT DISTINCT t.* FROM $this->_tag_tbl AS t, $this->_obj_tbl AS rt WHERE ";

		$where = array();
		$where[] = "rt.objectid=$id";
		$where[] = "rt.tbl='$this->_tbl'";
		$where[] = "rt.tagid=t.id";
		if ($admin != 1)
		{
			$where[] = "t.admin=0";
		}
		if ($tagger_id != 0)
		{
			$where[] = "rt.taggerid=" . $tagger_id;
		}
		if ($strength)
		{
			$where[] = "rt.strength=" . $strength;
		}

		$sql .= implode(" AND ", $where) . " ORDER BY t.raw_tag";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get all tags with a publication association
	 *
	 * @param      integer $id   Publication ID
	 * @param      integer $cat	 Publication category (optional)
	 * @param      string  $tag  Parameter description (if any) ...
	 * @return     array
	 */
	public function get_tags_with_objects($id=0, $category=0, $tag='')
	{
		$juser = JFactory::getUser();
		$now = JFactory::getDate()->toSql();

		$this->_db->setQuery("SELECT objectid FROM $this->_tag_tbl AS t,
			$this->_obj_tbl AS o WHERE o.tagid=t.id AND t.tag='$tag' AND o.tbl='$this->_tbl'");
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
				FROM $this->_tag_tbl AS t, $this->_obj_tbl AS o, #__publications AS r
				JOIN #__publication_versions as V ON V.publication_id = r.id AND V.main = 1
				WHERE o.tbl='$this->_tbl'
				AND o.tagid=t.id
				AND t.admin=0
				AND o.objectid=r.id
				AND V.state=1
				AND (V.publish_up = '0000-00-00 00:00:00' OR V.publish_up <= '$now')
				AND (V.publish_down = '0000-00-00 00:00:00' OR V.publish_down >= '$now') ";
		if ($category)
		{
			$sql .= "AND r.category=" . $category . " ";
		}

		if (!$juser->get('guest'))
		{
			$sql .= "AND (V.access=0 OR V.access=1 OR V.access=2) ";
		}
		else
		{
			$sql .= "AND (V.access=0 OR V.access=2) ";
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
	public function getUsersGroups($groups)
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
	 * Get all publications associated with a tag
	 *
	 * @param      string  $tag      Tag to find data for
	 * @param      integer $id       Resource ID
	 * @param      integer $type     Publication category
	 * @param      string  $sortby   Sort data by
	 * @param      string  $tag2     Secondary tag
	 * @param      array   $filterby Extra, optional filters
	 * @return     array
	 */
	public function get_objects_on_tag($tag='', $id=0, $category=0, $sortby='title', $tag2='', $filterby=array())
	{
		$juser = JFactory::getUser();
		$now  = JFactory::getDate()->toSql();

		if ($tag || $tag2)
		{
			$query  = "SELECT C.id, TA.tag, COUNT(DISTINCT TA.tag) AS uniques, V.title ";
			switch ($sortby)
			{
				case 'users':
					$query .= ", (SELECT rs.users FROM #__resource_stats AS rs WHERE rs.resid=C.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS users ";
				break;
				case 'jobs':
					$query .= ", (SELECT rs.jobs FROM #__resource_stats AS rs WHERE rs.resid=C.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS jobs ";
				break;
			}
			$query .= "FROM #__publications AS C ";
			$query .= "JOIN #__publication_versions as V ON V.publication_id = C.id AND V.main = 1 ";
			$query .= " LEFT JOIN #__publication_audience AS TTA ON C.id=TTA.rid ";
			$query .= ", $this->_obj_tbl AS RTA INNER JOIN #__tags AS TA ON (RTA.tagid = TA.id) ";
		}
		else
		{
			$query  = "SELECT C.id, V.title  ";
			switch ($sortby)
			{
				case 'users':
					$query .= ", (SELECT rs.users FROM #__resource_stats AS rs WHERE rs.resid=C.id AND rs.period=12 ORDER BY rs.datetime DESC LIMIT 1) AS users ";
				break;
				case 'jobs':
					$query .= ", (SELECT rs.jobs FROM #__resource_stats AS rs WHERE rs.resid=C.id AND rs.period=12 ORDER BY rs.datetime DESC LIMIT 1) AS jobs ";
				break;
			}
			$query .= "FROM #__publications AS C ";
			$query .= "JOIN #__publication_versions as V ON V.publication_id = C.id AND V.main = 1 ";
			$query .= " LEFT JOIN #__publication_audience AS TTA ON C.id=TTA.rid ";
		}

		$query .= "WHERE V.state = 1 ";
		if ($category) {
			$query .= "AND C.category=".$category." ";
		}
		$query .= "AND (V.published_up = '0000-00-00 00:00:00' OR V.published_up <= '".$now."') ";
		$query .= "AND (V.published_down = '0000-00-00 00:00:00' OR V.published_down >= '".$now."') AND ";

		$query .= (!$juser->get('guest'))
			   ? "(C.access=0 OR C.access=1) "
			   : "(C.access=0) ";

		if ($tag || $tag2)
		{
			if ($tag && !$tag2)
			{
				$query .= "AND RTA.objectid=C.id AND RTA.tbl='$this->_tbl' AND (TA.tag IN ('" . $tag . "'))";
				$query .= " GROUP BY C.id HAVING uniques=1";
			}
			else if ($tag2 && !$tag)
			{
				$query .= "AND RTA.objectid=C.id AND RTA.tbl='$this->_tbl' AND (TA.tag IN ('" . $tag2 . "'))";
				$query .= " GROUP BY C.id HAVING uniques=1";
			}
			else if ($tag && $tag2)
			{
				$query .= "AND RTA.objectid=C.id AND RTA.tbl='$this->_tbl' AND (TA.tag IN ('" . $tag . "','" . $tag2 . "'))";
				$query .= " GROUP BY C.id HAVING uniques=2";
			}
		}
		switch ($sortby)
		{
			case 'ranking':
				$sort = "C.ranking DESC";
			break;
			case 'date':
				$sort = "V.published_up DESC";
			break;
			case 'users':
				$sort = "users DESC";
			break;
			case 'jobs':
				$sort = "jobs DESC";
			break;
			default:
			case 'title':
				$sort = "V.title ASC";
			break;
		}
		$query .= " ORDER BY " . $sort . ", V.published_up";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Check if a tag is being used
	 *
	 * @param      string  $tag   Tag
	 * @param      integer $id    Publication ID
	 * @param      string  $alias Publication alias
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
						FROM $this->_obj_tbl AS ta, $this->_tag_tbl AS t
						WHERE ta.tagid=t.id
						AND t.tag='" . $tag . "'
						AND ta.tbl='publications'
						AND ta.objectid=" . $id;
		}
		if (!$id && $alias)
		{
			$query = "SELECT COUNT(*)
						FROM $this->_obj_tbl AS ta, $this->_tag_tbl AS t, #__publications AS r
						WHERE ta.tagid=t.id
						AND t.tag='" . $tag . "'
						AND ta.tbl='publications'
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
					FROM $this->_obj_tbl AS ta, $this->_tag_tbl AS t, #__publications AS r
					WHERE ta.tagid=t.id
					AND t.tag='".$tag."'
					AND ta.tbl='publications'
					AND ta.objectid=r.id";

		$this->_db->setQuery($query);
		return $this->_db->loadResultArray();
	}

	/**
	 * Get tag count
	 *
	 * @param      integer $id
	 *
	 * @return     string
	 */
	public function countTags($id)
	{
		$sql = "SELECT COUNT(*) FROM $this->_tag_tbl AS t,
			$this->_obj_tbl AS rt WHERE rt.objectid=$id
			AND rt.tbl='$this->_tbl' AND rt.tagid=t.id";
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}

	/**
	 * Get picked tags
	 *
	 * @param      array $tags
	 *
	 * @return     object
	 */
	public function getPickedTags($tags = array())
	{
		if (empty($tags))
		{
			return $tags;
		}

		$picked = '';
		$k = 1;
		foreach ($tags as $att)
		{
			$picked  .= $att;
			$picked .= $k == count($tags) ? '' : ',';
			$k++;
		}

		$sql  = "SELECT DISTINCT t.* FROM $this->_tag_tbl AS t ";
		$sql .= "WHERE  t.id IN (".$picked.")";
		$sql .= " ORDER BY t.raw_tag";
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

	/**
	 * Get suggested tags
	 *
	 * @param      string 	$pubtitle
	 * @param      string 	$typealias
	 * @param      array 	$attached_tags
	 * @param      integer 	$limit
	 * @param      integer	$tcount
	 *
	 * @return     object
	 */
	public function getSuggestedTags($pubtitle = '', $typealias = '', $attached_tags = array(), $limit = 10, $tcount = 2)
	{
		$picked = '';
		$k = 1;
		if (!empty($attached_tags))
		{
			foreach ($attached_tags as $att)
			{
				$picked  .= $att->id;
				$picked .= $k == count($attached_tags) ? '' : ',';
				$k++;
			}
		}
		$keywords = array();
		if ($pubtitle)
		{
			$words = explode(' ', $pubtitle );
			foreach ($words as $word)
			{
				if ($word != '')
				{
					$keywords[] = trim($word);
				}
			}
		}

		$sql  = "SELECT DISTINCT t.*, tj.tagid, tj.objectid, COUNT(tj.tagid) AS tcount  ";
		$sql .= "FROM $this->_tag_tbl AS t ";
		$sql .= "JOIN $this->_obj_tbl AS tj ON t.id=tj.tagid ";
		$sql .= "WHERE ";
		$sql .= $picked ? " t.id NOT IN (".$picked.")" : "1=1";
		$sql .= " GROUP BY tagid ";
		$sql .= " HAVING tcount > ".$tcount;

		if (!empty($keywords))
		{
			$sql .= ' OR (';
			$w = 1;
			foreach ($keywords as $key)
			{
				$sql .= 't.raw_tag LIKE "%'.$key.'%"';
				$sql .= $w == count($keywords) ? '' : ' OR ';
				$w++;
			}
			$sql .= ')';
		}
		$sql .= " AND t.admin=0 ";
		$sql .= " ORDER BY  RAND()";
		$sql .= " LIMIT ".$limit;
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

/**
	 * Get a tag cloud of top publication tags
	 *
	 * @param      integer $limit
	 * @param      string $tagstring
	 * @return     mixed Return description (if any) ...
	 */
	public function getTopTagCloud($limit, $tagstring='')
	{
		$tags = $this->getTopTags($limit);
		return $this->buildTopCloud($tags, 'alpha', 0, $tagstring);
	}

	/**
	 * Get top publication tags
	 *
	 * @param      integer $limit
	 * @return     mixed Return description (if any) ...
	 */
	public function getTopTags($limit)
	{
		$tj = new TagsTableObject($this->_db);

		$sql  = "SELECT t.tag, t.raw_tag, t.admin, tj.tagid, tj.objectid, COUNT(tj.tagid) AS tcount ";
		$sql .= "FROM #__tags AS t  ";
		$sql .= "JOIN " . $tj->getTableName() . " AS tj ON t.id=tj.tagid ";
		$sql .= " LEFT JOIN #__publication_versions AS V ON V.publication_id=tj.objectid AND tj.tbl='publications' ";
		$sql .= "WHERE t.id=tj.tagid AND t.admin=0 ";
		$sql .= "AND tj.tbl=" . $this->_db->Quote($this->_tbl) . " ";
		$sql .= "AND V.state=1 AND V.main=1 AND V.access!=4 ";
		$sql .= "GROUP BY tagid ";
		$sql .= "ORDER BY tcount DESC ";
		$sql .= "LIMIT $limit";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Turn a string of tags to an array
	 *
	 * @param      string $tag Tag string
	 * @return     mixed
	 */
	public function parseTopTags($tag, $remove='')
	{
		if (is_array($tag))
		{
			$bunch = $tag;
		}
		else
		{
			$bunch = $this->_parse_tags($tag);
		}

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
				$lst = $this->_parse_tags($tagstring);
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
					$tll[$tag->tag] = "\t".'<li' . $class . '><span style="font-size: ' . round($size, 1) . 'em"><a href="' . JRoute::_('index.php?option=com_publications&task=browse&tag=' . implode(',', $lsst)) . '">' . stripslashes($tag->raw_tag) . '</a></li>' . "\n"; //' <span>' . $tag->count . '</span></a></span></li>' . "\n";
				}
				else
				{
					$tll[$tag->tag] = "\t".'<li' . $class . '><a href="' . urldecode(JRoute::_('index.php?option=com_publications&task=browse&tag=' . implode(',', $lsst))) . '">' . stripslashes($tag->raw_tag) . '</a></li>' . "\n"; //' <span>' . $tag->count . '</span></a></li>' . "\n";
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

