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


class KbHtml 
{
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	//-----------

	public function formSelect($name, $array, $value, $class='', $id)
	{
		$out  = '<select name="'.$name.'" id="'.$name.'" onchange="return listItemTask(\'cb'. $id .'\',\'regroup\')"';
		$out .= ($class) ? ' class="'.$class.'">'."\n" : '>'."\n";
		$out .= ' <option value="0"';
		$out .= ($value == 0 || $value == '') ? ' selected="selected"' : '';
		$out .= '>'. JText::_('NONE') .'</option>'."\n";
		foreach ($array as $anode) 
		{
			$selected = ($anode->id == $value || $anode->title == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="'.$anode->id.'"'.$selected.'>'.$anode->title.'</option>'."\n";
		}
		$out .= '</select>'."\n";
		return $out;
	}
	
	//-----------
	
	public function sectionSelect( $categories, $val, $name ) 
	{
		$out  = '<select name="'.$name.'">'."\n";
		$out .= "\t".'<option value="">'.JText::_('SELECT_CATEGORY') .'</option>'."\n";
		foreach ($categories as $category) 
		{
			$selected = ($category->id == $val)
					  ? ' selected="selected"'
					  : '';
			$out .= "\t".'<option value="'.$category->id.'"'.$selected.'>'.$category->title.'</option>'."\n";
		}
		$out .= '</select>'."\n";
		return $out;
	}
}
