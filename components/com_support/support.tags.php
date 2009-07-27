<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

require_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.class.php' );

//----------------------------------------------------------
//  Resources Tagging class
//----------------------------------------------------------

class SupportTags extends Tags
{
	public function __construct( $db, $config=array() )
	{
		$this->_db  = $db;
		$this->_tbl = 'support';
		
		if (isset($config['normalized_valid_chars'])) {
			$this->_normalized_valid_chars = $config['normalized_valid_chars'];
		}
		if (isset($config['normalize_tags'])) {
			$this->_normalize_tags = $config['normalize_tags'];
		}
		if (isset($config['max_tag_length'])) {
			$this->_max_tag_length = $config['max_tag_length'];
		}
		if (isset($config['block_multiuser_tag_on_object'])) {
			$this->_block_multiuser_tag_on_object = $config['block_multiuser_tag_on_object'];
		}
	}
	
	//-----------
	
	public function get_tag_cloud($showsizes=0, $admin=0, $oid=NULL)
	{
		// set some variables
		$min_font_size = 1;
		$max_font_size = 1.8;
		
		$filter = "";
		if ($oid) {
			$filter .= "WHERE rt.objectid=".$oid;
		}
		if ($admin == 0) {
			if ($oid) {
				$filter .= " AND t.admin=0 ";
			} else {
				$filter .= "WHERE t.admin=0 ";
			}
		} else {
			$filter .= "";
		}
		
		// find all tags
		$sql = "SELECT t.tag, t.raw_tag, t.admin, COUNT(*) as count
				FROM $this->_tag_tbl AS t INNER JOIN $this->_obj_tbl AS rt ON (rt.tagid = t.id) AND rt.tbl='$this->_tbl' $filter
				GROUP BY raw_tag
				ORDER BY raw_tag ASC";
		$this->_db->setQuery( $sql );
		$tags = $this->_db->loadObjectList();
	
		$html = '';
		
		if ($tags && count($tags) > 0) {
			if ($showsizes) {
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
				if (0 == $spread) { // Divide by zero
					$spread = 1;
				}
				$step = ($max_font_size - $min_font_size)/($spread);
			}
			
			// build HTML
			if ($showsizes == 3) {
				$bits = array();
			} else {
				$html = '<ol class="tags">'."\n";
			}
			foreach ($tags as $tag)
			{
				$class = '';
				if ($tag->admin == 1) {
					$class = ' class="admin"';
				}

				$tag->raw_tag = str_replace( '&amp;', '&', $tag->raw_tag );
				$tag->raw_tag = str_replace( '&', '&amp;', $tag->raw_tag );

				switch ($showsizes) 
				{
					case 3:
						$bits[] = '<a'.$class.' href="'.JRoute::_('index.php?option=com_support&amp;task=tickets&amp;find=status:open tag:'.$tag->tag).'">'.$tag->raw_tag.'</a>';
					break;
					
					case 2:
						$html .= ' <li'.$class.'><a href="javascript:void(0);" onclick="addtag(\''.$tag->tag.'\');">'.$tag->raw_tag.'</a></li>'."\n";
					break;
					
					case 1:
						$size = $min_font_size + ($tag->count - $min_qty) * $step;
						$html .= "\t".'<li'.$class.'><span style="font-size: '. round($size,1) .'em"><a href="'.JRoute::_('index.php?option=com_tags&amp;tag='.$tag->tag).'">'.$tag->raw_tag.'</a></span></li>'."\n";
					break;
					
					default:
						$html .= "\t".'<li'.$class.'><a href="'.JRoute::_('index.php?option=com_tags&amp;tag='.$tag->tag).'">'.$tag->raw_tag.'</a></li>'."\n";
					break;
				}
			}
			if ($showsizes == 3) {
				$html = implode(', ',$bits);
			} else {
				$html .= '</ol>'."\n";
			}
		}

		return $html;
	}
}
?>