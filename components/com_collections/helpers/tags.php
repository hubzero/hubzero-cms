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
 * Answers Tagging class
 */
class CollectionsTags extends TagsHandler
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
		$this->_tbl = 'bulletinboard';
	}

	/**
	 * Get tags for a list of IDs
	 * 
	 * @param      array   $ids       Bulletin ids
	 * @param      integer $admin     Admin flag
	 * @return     array
	 */
	public function getTagsForIds($ids=array(), $admin=0)
	{
		$tt = new TagsTableTag($this->_db);
		$tj = new TagsTableObject($this->_db);

		if (!is_array($ids) || empty($ids))
		{
			return false;
		}

		$ids = array_map('intval', $ids);

		$sql  = "SELECT t.tag, t.raw_tag, t.admin, COUNT(*) as count, rt.objectid
				FROM " . $tt->getTableName() . " AS t 
				INNER JOIN " . $tj->getTableName() . " AS rt ON (rt.tagid = t.id) AND rt.tbl='$this->_tbl' 
				WHERE rt.objectid IN (" . implode(',', $ids) . ") ";

		switch ($admin)
		{
			/*case 0:
				$sql .= (isset($objectid) && $objectid) ? "AND (t.state=1 OR t.state=0) " : "WHERE (t.state=1 OR t.state=0) ";
			break;*/
			case 1:
				$sql .= "";
			break;
			case 0:
			default:
				$sql .= "AND t.admin=0 ";
			break;
		}
		$sql .= "GROUP BY raw_tag
				ORDER BY raw_tag ASC";
		$this->_db->setQuery($sql);
		$items = $this->_db->loadObjectList();
		
		$tags = array();
		if ($items)
		{
			foreach ($items as $item)
			{
				if (!isset($tags[$item->objectid]))
				{
					$tags[$item->objectid] = array();
				}
				$tags[$item->objectid][] = $item;
			}
		}
		return $tags;
	}
}

