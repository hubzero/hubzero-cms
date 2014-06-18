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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'helpers' . DS . 'handler.php');

/**
 * Support Tagging class
 */
class SupportTags extends TagsHandler
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
		$this->_tbl = 'support';
	}

	/**
	 * Get a tag cloud for an object
	 *
	 * @param      integer $showsizes Show tag size based on use?
	 * @param      integer $admin     Show admin tags?
	 * @param      integer $objectid  Object ID
	 * @return     mixed Return description (if any) ...
	 */
	public function get_tag_cloud($showsizes=0, $admin=0, $oid=NULL)
	{
		// set some variables
		$min_font_size = 1;
		$max_font_size = 1.8;

		$filter = "";
		if ($oid)
		{
			$filter .= "WHERE rt.objectid=" . $oid;
		}
		if ($admin == 0)
		{
			if ($oid)
			{
				$filter .= " AND t.admin=0 ";
			}
			else
			{
				$filter .= "WHERE t.admin=0 ";
			}
		}
		else
		{
			$filter .= "";
		}

		// find all tags
		$sql = "SELECT t.tag, t.raw_tag, t.admin, COUNT(*) as count
				FROM #__tags AS t INNER JOIN #__tags_object AS rt ON (rt.tagid = t.id) AND rt.tbl='$this->_tbl' $filter
				GROUP BY raw_tag
				ORDER BY raw_tag ASC";
		$this->_db->setQuery($sql);
		$tags = $this->_db->loadObjectList();

		$html = '';

		if ($tags && count($tags) > 0)
		{
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
			if ($showsizes == 3)
			{
				$bits = array();
			}
			else
			{
				$html = '<ol class="tags">' . "\n";
			}
			foreach ($tags as $tag)
			{
				$class = '';
				if ($tag->admin == 1)
				{
					$class = ' class="admin"';
				}

				$tag->raw_tag = str_replace('&amp;', '&', $tag->raw_tag);
				$tag->raw_tag = str_replace('&', '&amp;', $tag->raw_tag);

				switch ($showsizes)
				{
					case 3:
						$bits[] = '<a' . $class . ' href="'.JRoute::_('index.php?option=com_support&task=tickets&find=tag:' . $tag->tag) . '">' . $tag->raw_tag . '</a>';
					break;

					case 2:
						$html .= ' <li' . $class . '><a href="javascript:void(0);" onclick="addtag(\'' . $tag->tag . '\');">' . $tag->raw_tag . '</a></li>' . "\n";
					break;

					case 1:
						$size = $min_font_size + ($tag->count - $min_qty) * $step;
						$html .= "\t" . '<li' . $class . '><span style="font-size: ' . round($size, 1) . 'em"><a href="'.JRoute::_('index.php?option=com_tags&tag=' . $tag->tag) . '">' . $tag->raw_tag . '</a></span></li>' . "\n";
					break;

					default:
						$html .= "\t" . '<li' . $class . '><a href="' . JRoute::_('index.php?option=com_tags&tag=' . $tag->tag) . '">' . $tag->raw_tag . '</a></li>' . "\n";
					break;
				}
			}
			if ($showsizes == 3)
			{
				$html = implode(', ', $bits);
			}
			else
			{
				$html .= '</ol>' . "\n";
			}
		}

		return $html;
	}

	/**
	 * Check tag existence for tickets
	 *
	 * @param      integer $id        Resource ID
	 * @param      integer $tagger_id Tagger ID
	 * @param      integer $strength  Tag strength
	 * @param      integer $admin     Admin flag
	 * @return     array
	 */
	public function checkTags($id, $tagger_id=0, $strength=0, $admin=0)
	{
		$sql = "SELECT rt.id, rt.objectid FROM $this->_obj_tbl AS rt WHERE ";

		if (is_array($id))
		{
			$id = array_map('intval', $id);
			$id = implode(',', $id);
		}

		$where = array();
		$where[] = "rt.objectid IN ($id)";
		$where[] = "rt.tbl='$this->_tbl'";
		/*if ($admin != 1)
		{
			$where[] = "t.admin=0";
		}*/
		if ($tagger_id != 0)
		{
			$where[] = "rt.taggerid=" . $tagger_id;
		}
		if ($strength)
		{
			$where[] = "rt.strength=" . $strength;
		}

		$sql .= implode(" AND ", $where) . " GROUP BY rt.objectid";

		$this->_db->setQuery($sql);
		return $this->_db->loadAssocList('objectid');
	}
}

