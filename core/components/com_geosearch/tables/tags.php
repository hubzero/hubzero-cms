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
 * @author    Brandon Beatty
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

