<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Brandon Beatty
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Table class for tags
 */
class GeosearchTags
{
	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $id          = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $taggerid          = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $objectid          = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		$this->_db = $db;
	}

	/**
	 *
	 * Get user IDs from tags
	 *
	 * @param     array $tags (UI)
	 * @return    object tagger IDs
	 */
	public function searchTagsMems($tags=array(), $filters=array())
	{
		$tagsql = "";
		$i = 0;
		// make tags list and counter
		foreach ($tags as $tag)
		{
			// prevent SQL injection
			$tagsql .= $this->_db->quote($tag);
			$i++;
			if (count($tags) > 1 && $i < count($tags)) {
				$tagsql .= ", ";
			}
		}

		// select IDs exclusive
		$sql = "SELECT DISTINCT tob.taggerid, count(DISTINCT t.tag) AS tag_count FROM #__tags_object AS tob LEFT JOIN #__tags AS t ON tob.tagid = t.id ";
		$sql .= "WHERE t.tag IN ($tagsql) ";
		$sql .= "GROUP BY tob.taggerid HAVING tag_count = " . $this->_db->quote($i);
		$sql .= " LIMIT {$filters['start']}, {$filters['limit']}";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 *
	 * Get events IDs from tags
	 *
	 * @param     array $tags (UI)
	 * @return    object tag object IDs (= event IDs)
	 */
	public function searchTagsEvents($tags=array(), $filters=array())
	{
		$tagsql = "";
		$i = 0;
		foreach ($tags as $tag)
		{
			$tagsql .= $this->_db->quote($tag);
			$i++;
			if (count($tags) > 1 && $i < count($tags))
			{
				$tagsql .= ", ";
			}
		}
		$sql = "SELECT DISTINCT tob.objectid, count(DISTINCT t.tag) AS tag_count FROM #__tags_object AS tob LEFT JOIN #__tags AS t ON tob.tagid = t.id ";
		$sql .= "WHERE t.tag IN ($tagsql) AND tob.tbl = " . $this->_db->quote('events');
		$sql .= " GROUP BY tob.objectid HAVING tag_count = " . $this->_db->quote($i);
		$sql .= " LIMIT {$filters['start']}, {$filters['limit']}";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 *
	 * Get organization IDs from tags
	 *
	 * @param     array $tags (UI)
	 * @return    object tag object IDs (= event IDs)
	 */
	public function searchTagsOrgs($tags=array(), $filters=array())
	{
		$tagsql = "";
		$i = 0;
		foreach ($tags as $tag)
		{
			$tagsql .= $this->_db->quote($tag);
			$i++;
			if (count($tags) > 1 && $i < count($tags))
			{
				$tagsql .= " OR ";
			}
		}

		$sql = "SELECT DISTINCT tob.objectid, count(DISTINCT t.tag) AS tag_count FROM #__tags_object AS tob LEFT JOIN #__tags AS t ON tob.tagid = t.id ";
		$sql .= "INNER JOIN jos_resources AS r ON r.id = tob.objectid ";
		$sql .= "WHERE t.tag IN ($tagsql) AND tob.tbl = " . $this->_db->quote('resources') . " AND r.type = " . $this->_db->quote('90');
		$sql .= " GROUP BY tob.objectid HAVING tag_count = " . $this->_db->quote($i);
		$sql .= " LIMIT {$filters['start']}, {$filters['limit']}";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}

