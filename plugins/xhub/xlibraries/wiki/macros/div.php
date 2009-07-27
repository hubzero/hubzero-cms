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

//----------------------------------------------------------

class DivMacro extends WikiMacro 
{
	public function description() 
	{
		$txt = array();
		$txt['wiki'] = "Allows content to be wrapped in a `div` tag. This macro must be used twice: `Div(start)` to indicate where to create the opening `div` tag and `Div(end)` to indicate where to close the resulting `div` tag. Attributes may be applied by separating name/value pairs with a comma. Example: Div(start, class=myclass)";
		$txt['html'] = "<p>Allows content to be wrapped in a <code>&lt;div&gt;</code> tag. This macro must be used twice: <code>[[Div(start)]]</code> to indicate where to create the opening <code>&lt;div&gt;</code> tag and <code>[[Div(end)]]</code> to indicate where to close the resulting <code>&lt;div&gt;</code> tag. Attributes may be applied by separating name/value pairs with a comma. Example: <code>[[Div(start, class=myclass)]]</code>";
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
		
		if (trim($text) == 'start') {
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
			
			$div  = '<div';
			$div .= (!empty($atts)) ? ' '.implode(' ',$atts).'>' : '>';
		} elseif (trim($text) == 'end') {
			$div  = '</div>';
		}
		
		return $div;
	}
}
?>