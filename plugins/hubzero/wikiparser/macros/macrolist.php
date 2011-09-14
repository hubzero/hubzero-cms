<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class MacrolistMacro extends WikiMacro
{
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Displays a list of all installed Wiki macros, including documentation if available. Optionally, the name of a specific macro can be provided as an argument. In that case, only the documentation for that macro will be rendered.';
		$txt['html'] = '<p>Displays a list of all installed Wiki macros, including documentation if available. Optionally, the name of a specific macro can be provided as an argument. In that case, only the documentation for that macro will be rendered.</p>';
		return $txt['html'];
	}

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
		$txt = '<dl>'."\n";

		foreach ($macros as $f)
		{
			include_once($path.DS.$f);

			$f = str_replace('.php','',$f);

			$macroname = ucfirst($f).'Macro';

			if (class_exists($macroname)) {
				$macro = new $macroname();

				//$m[$macroname] = $macro->description();
				$macroname = substr($macroname, 0, (strlen($macroname) - 5));
				$txt .= '<dt><a name="'.strtolower($macroname).'"></a><code>&#91;&#91;'.$macroname.'(args)&#93;&#93;</code></dt><dd>'.$macro->description().'</dd>'."\n";
			}
		}
		$txt .= '</dl>'."\n";

		return $txt;
	}
}

