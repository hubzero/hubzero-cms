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


class SpanMacro extends WikiMacro 
{
	public function description() 
	{
		$txt = array();
		$txt['wiki'] = "Wraps text or other elements inside a `<span>` tag.";
		$txt['html'] = "<p>Wraps text or other elements inside a <code>&lt;span&gt;</code> tag.</p>";
		return $txt['html'];
	}
	
	//-----------
	
	public function render() 
	{
		$et = $this->args;
		
		if (!$et) {
			return '';
		}
		
		$attribs = explode(',', $et);
		$text = array_shift($attribs);
		
		$atts = array();
		if (!empty($attribs) && count($attribs) > 0) {
			foreach ($attribs as $a) 
			{
				$a = split('=',$a);
				$key = $a[0];
				$val = end($a);

				$atts[] = $key.'="'.$val.'"';
			}
		}
		
		$span  = '<span';
		$span .= (!empty($atts)) ? ' '.implode(' ',$atts).'>' : '>';
		$span .= trim($text).'</span>';
			
		return $span;
	}
}
