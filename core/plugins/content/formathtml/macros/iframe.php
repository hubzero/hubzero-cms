<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		$host = 'https://' . \Request::getVar('HTTP_HOST', '', 'server');

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
