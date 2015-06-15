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

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

/**
 * Wiki macro class for wrapping content in a div
 */
class Div extends Macro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Allows content to be wrapped in a `div` tag. This macro must be used twice: `Div(start)` to indicate where to create the opening `div` tag and `Div(end)` to indicate where to close the resulting `div` tag. Attributes may be applied by separating name/value pairs with a comma. Example: Div(start, class=myclass)';
		$txt['html'] = '<p>Allows content to be wrapped in a <code>&lt;div&gt;</code> tag. This macro must be used twice: <code>[[Div(start)]]</code> to indicate where to create the opening <code>&lt;div&gt;</code> tag and <code>[[Div(end)]]</code> to indicate where to close the resulting <code>&lt;div&gt;</code> tag. Attributes may be applied by separating name/value pairs with a comma. Example: <code>[[Div(start, class=myclass)]]</code>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		$et = $this->args;

		if (!$et)
		{
			return '';
		}

		$attribs = explode(',', $et);
		$text = array_shift($attribs);

		if (trim($text) == 'start')
		{
			$atts = array();
			if (!empty($attribs) && count($attribs) > 0)
			{
				foreach ($attribs as $a)
				{
					$a = explode('=', $a);
					$key = trim($a[0]);
					$val = trim(end($a));
					$val = trim($val, '"');
					$val = trim($val, "'");

					$key = htmlentities($key, ENT_COMPAT, 'UTF-8');
					$val = htmlentities($val, ENT_COMPAT, 'UTF-8');

					$atts[] = $key . '="' . $val . '"';
				}
			}

			$div  = '<div';
			$div .= (!empty($atts)) ? ' ' . implode(' ', $atts) . '>' : '>';
		}
		elseif (trim($text) == 'end')
		{
			$div  = '</div>';
		}

		return $div;
	}
}

