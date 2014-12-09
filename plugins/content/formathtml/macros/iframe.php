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
 * Macro class for displaying an Iframe
 */
class Iframe extends Macro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		// use host for example, that way  its not block
		$host = 'https://' . \JRequest::getVar('HTTP_HOST', '', 'server');

		$txt = array();
		$txt['wiki'] = 'Embeds an Iframe into the Page';
		$txt['html'] = '<p>Embeds an iframe into the page.</p>
						<p>Examples:</p>
						<ul>
							<li><code>[[Iframe(' . $host . ')]]</code></li>
							<li><code>[[Iframe(' . $host . ', 640, 380)]] - width 640px, height 380px</code></li>
						</ul>
						<p>Displays:</p>
						<iframe src="'. $host.'" width="640px" height="380px" border="0"></iframe>';

		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		//get the args passed in
		$content = $this->args;

		// defaults
		$default_width = 640;
		$default_height = 380;

		// args will be null if the macro is called without parenthesis.
		if (!$content)
		{
			return '';
		}

		// split up the args
		$args = array_map('trim', explode(',', $content));
		$url  = $args[0];

		// did user pass width/height args
		$width  = (isset($args[1]) && $args[1] != '') ? $args[1] : $default_width;
		$height = (isset($args[2]) && $args[2] != '') ? $args[2] : $default_height;

		//return the emdeded youtube video
		return '<iframe src="' . $url . '" width="' . $width . '" height="' . $height . '" frameborder="0" allowfullscreen="true" allowtransparency="true"></iframe>';
	}
}
