<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'cloud.php');

/**
 * Collections Tagging class
 */
class CollectionsModelTags extends TagsModelCloud
{
	/**
	 * Object type, used for linking objects (such as resources) to tags
	 *
	 * @var  string
	 */
	protected $_scope = 'bulletinboard';

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

		$sql = "SELECT t.tag, t.raw_tag, t.admin, rt.objectid
				FROM " . $tt->getTableName() . " AS t 
				INNER JOIN " . $tj->getTableName() . " AS rt ON (rt.tagid = t.id) AND rt.tbl='" . $this->_scope . "' 
				WHERE rt.objectid IN (" . implode(',', $ids) . ") ";

		switch ($admin)
		{
			case 1:
				$sql .= "";
			break;
			case 0:
			default:
				$sql .= "AND t.admin=0 ";
			break;
		}
		$sql .= "ORDER BY raw_tag ASC";
		$this->_db->setQuery($sql);

		$tags = array();
		if ($items = $this->_db->loadObjectList())
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

	/**
	 * Append a tag to the internal cloud
	 * 
	 * @param   mixed   $tag
	 * @return  object
	 */
	public function append($tag)
	{
		if (!($this->_cache['tags.list'] instanceof \Hubzero\Base\ItemList))
		{
			$this->_cache['tags.list'] = new \Hubzero\Base\ItemList(array());
		}

		if (!$tag)
		{
			return $this;
		}

		if (!($tag instanceof TagsModelTag))
		{
			if (is_array($tag))
			{
				foreach ($tag as $t)
				{
					$this->_cache['tags.list']->add(new TagsModelTag($t));
				}
				return $this;
			}
			else
			{
				$tag = new TagsModelTag($tag);
			}
		}
		$this->_cache['tags.list']->add($tag);

		return $this;
	}
}

