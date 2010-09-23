<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

class StoreHtml 
{
	public function productimage( $option, $item, $root, $wpath, $alt, $category )
	{
		if ($wpath) {
			// Strip any trailing slash
			if (substr($wpath, -1) == DS) { 
				$wpath = substr($wpath, 0, strlen($wpath) - 1);
			}
			// Ensure a starting slash
			if (substr($wpath, 0, 1) != DS) { 
				$wpath = DS.$wpath;
			}
			$wpath = $wpath.DS;
		}
		
		$d = @dir($root.$wpath.$item);

		$images = array();
		$html = '';
			
		if ($d) {
			while (false !== ($entry = $d->read())) 
			{
				$img_file = $entry; 
				if (is_file($root.$wpath.$item.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "bmp|gif|jpg|png|swf", $img_file )) {
						$images[] = $img_file;
					}
				}
			}
			$d->close();
		} else {
			if ($category=='service') {
				$html = '<img src="../components/'.$option.'/images/premiumservice.gif" alt="'.JText::_('COM_STORE_PREMIUM_SERVICE').'" />';
			} else {
				$html = '<img src="../components/'.$option.'/images/nophoto.gif" alt="'.JText::_('COM_STORE_MSG_NO_PHOTO').'" />';
			}
		}
		
		sort($images);
		$els = '';
		$k = 0;
		$g = 0;
		
		for ($i=0, $n=count( $images ); $i < $n; $i++) 
		{
			$pic = explode('.',$images[$i]);
			$c = count($pic);
			$pic[$c-2] .= '-tn';
			$end = array_pop($pic);
			$pic[] = 'gif';
			$tn = implode('.',$pic);
			
			$type = explode('.',$images[$i]);
			
			if (is_file($root.$wpath.$item.'/'.$tn)) {
				$k++;
				$els .= '<a rel="lightbox" href="'.$wpath.$item.'/'.$images[$i].'" title="'.$alt.'"><img src="'.$wpath.$item.'/'.$tn.'" alt="'.$alt.'" /></a>';
			}
		}
		
		if ($els) {
			$html .= $els;
		}
		return $html;
	}
}
