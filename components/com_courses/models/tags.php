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
 * Helper class for handling course tags
 */
class CoursesTags extends TagsHandler
{
	/**
	 * Constructor
	 * 
	 * @param      object $db     JDatabase
	 * @param      array  $config Array of optional configurations
	 * @return     void
	 */
	public function __construct($db, $config=array())
	{
		$this->_db  = $db;
		$this->_tbl = 'courses';
	}

	/**
	 * Get a tag cloud for an object
	 * 
	 * @param      integer $showsizes Show tag size based on use?
	 * @param      integer $admin     Show admin tags?
	 * @param      integer $objectid  Object ID
	 * @return     mixed Return description (if any) ...
	 */
	public function getTagCloud($limit, $tagstring='')
	{
		$t = new TagsTableTag($this->_db);
		//$tags = $t->getCloud($this->_tbl, $admin, null);
		$tags = $t->getTopTags($limit, 'courses', 'tcount DESC', 0);

		return $this->buildCloud($tags, 'alpha', 0, $tagstring);
	}

	/**
	 * Get a tag cloud for an object
	 * 
	 * @param      integer $showsizes Show tag size based on use?
	 * @param      integer $admin     Show admin tags?
	 * @param      integer $objectid  Object ID
	 * @return     mixed Return description (if any) ...
	 */
	public function getTags($limit)
	{
		$t = new TagsTableTag($this->_db);
		//$tags = $t->getCloud($this->_tbl, $admin, null);
		return $t->getTopTags($limit, 'courses', 'tcount DESC', 0);
	}

	/**
	 * Get a tag cloud for an object
	 * 
	 * @param      integer $showsizes Show tag size based on use?
	 * @param      integer $admin     Show admin tags?
	 * @param      integer $objectid  Object ID
	 * @return     mixed Return description (if any) ...
	 */
	public function getTagString($limit)
	{
		$t = new TagsTableTag($this->_db);
		/*$query = "SELECT t.id, t.tag, t.raw_tag
					FROM $this->_tag_tbl AS t 
					JOIN $this->_obj_tbl AS ta ON ta.tagid=t.id
					JOIN #__courses AS c ON c.id=ta.objectid
					WHERE ta.tbl='courses' 
					AND t.admin=0";

		$this->_db->setQuery($query);
		$tags = $this->_db->loadResultArray();*/
		$tags = $t->getTopTags($limit, 'courses', 'tcount DESC', 0);

		if ($tags && count($tags) > 0) 
		{
			$tagarray = array();
			foreach ($tags as $tag)
			{
				//$tagarray[] = $tag['raw_tag'];
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
	 * Build a tag cloud
	 * 
	 * @param      array   $tags      List of tags
	 * @param      string  $sort      How to sort tags?
	 * @param      integer $showsizes Show tag size based on use?
	 * @return     string HTML
	 */
	public function buildCloud($tags, $sort='alpha', $showsizes=0, $tagstring='')
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

				if ($tagstring)
				{
					if (!in_array($tag->tag, $lst))
					{
						$lst[] = $tag->tag;
					}
				}
				else
				{
					$lst = array($tag->tag);
				}

				$tag->raw_tag = stripslashes($tag->raw_tag);
				$tag->raw_tag = str_replace('&amp;', '&', $tag->raw_tag);
				$tag->raw_tag = str_replace('&', '&amp;', $tag->raw_tag);
				if ($showsizes == 1) 
				{
					$size = $min_font_size + ($tag->count - $min_qty) * $step;
					$tll[$tag->tag] = "\t".'<li' . $class . '><span style="font-size: ' . round($size, 1) . 'em"><a href="' . JRoute::_('index.php?option=com_courses&task=browse&tag=' . implode(',', $lst)) . '">' . stripslashes($tag->raw_tag) . '</a></li>' . "\n"; //' <span>' . $tag->count . '</span></a></span></li>' . "\n";
				} 
				else 
				{
					$tll[$tag->tag] = "\t".'<li' . $class . '><a href="' . urldecode(JRoute::_('index.php?option=com_courses&task=browse&tag=' . implode(',', $lst))) . '">' . stripslashes($tag->raw_tag) . '</a></li>' . "\n"; //' <span>' . $tag->count . '</span></a></li>' . "\n";
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

