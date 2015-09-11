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
 * A wiki macro for embedding links
 */
class Link extends Macro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $allowPartial = true;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     string
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = "Embed a link.

Examples:

{{{
[[Link(/SomePage SomePage)]]
}}}
";
		$txt['html'] = '<p>Embed a link.</p>
<p>Examples:</p>
<ul>
<li><code>[[Link(/SomePage SomePage)]]</code></li>
</ul>';

		return $txt['html'];
	}

	/**
	 * Generate macro output based on passed arguments
	 *
	 * @return     string HTML image tag on success or error message on failure
	 */
	public function render()
	{
		$content = $this->args;

		// args will be null if the macro is called without parenthesis.
		if (!$content)
		{
			return '';
		}

		$cls = 'wiki';

		// Parse arguments
		// We expect the 1st argument to be a filename
		$args  = explode(' ', $content);
		$href  = array_shift($args);
		$title = (count($args) > 0) ? implode(' ', $args) : $href;

		$title = preg_replace('/\(.*?\)/', '', $title);
		$title = preg_replace('/^.*?\:/', '', $title);

		return '<a class="' . $cls . '" href="' . $href . '">' . trim($title) . '</a>';
	}
}

