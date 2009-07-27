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

class MacrolistMacro extends WikiMacro 
{
	public function description() 
	{
		$txt = array();
		$txt['wiki'] = 'Displays a list of all installed Wiki macros, including documentation if available. Optionally, the name of a specific macro can be provided as an argument. In that case, only the documentation for that macro will be rendered.';
		$txt['html'] = '<p>Displays a list of all installed Wiki macros, including documentation if available. Optionally, the name of a specific macro can be provided as an argument. In that case, only the documentation for that macro will be rendered.</p>';
		return $txt['html'];
	}
	
	//-----------
	
	public function render() 
	{
		$path = dirname(__FILE__);
		
		$d = @dir($path);
		
		$macros = array();
		
		if ($d) {
			while (false !== ($entry = $d->read())) 
			{			
				$img_file = $entry;
				if (is_file($path.DS.$entry) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "php", $entry )) {
						$macros[] = $entry;
					}
				}
			}
										
			$d->close();
		}
		
		//$m = array();
		$txt = '<dl>'.n;
		
		foreach ($macros as $f) 
		{
			include_once($path.DS.$f);
			
			$f = str_replace('.php','',$f);
			
			$macroname = ucfirst($f).'Macro';

			if (class_exists($macroname)) {
				$macro = new $macroname();

				//$m[$macroname] = $macro->description();
				$macroname = substr($macroname, 0, (strlen($macroname) - 5));
				$txt .= '<dt><code>&#91;&#91;'.$macroname.'(args)&#93;&#93;</code></dt><dd>'.$macro->description().'</dd>'.n;
			}
		}
		$txt .= '</dl>'.n;
		
		return $txt;
	}
}
?>