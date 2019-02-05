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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Admin\Helpers;

/**
 * HTML helper
 */
class Html
{
	/**
	 * Outputs a <select> element with a specific value chosen
	 *
	 * @param   mixed   $val   Chosen value
	 * @param   string  $name  Field name
	 * @param   string  $id    ID
	 * @param   string  $atts  Attributes
	 * @return  string  HTML <select>
	 */
	public static function scopes($val, $name, $id = null, $atts = null)
	{
		$adapters = \Filesystem::files(dirname(dirname(__DIR__)) . '/models/adapters', '\.php$');

		$out  = '<select name="' . $name . '" id="' . ($id ? $id : str_replace(array('[', ']'), '', $name)) . '"' . ($atts ? ' ' . $atts : '') . '>';
		$out .= '<option value="">' . \Lang::txt('COM_BLOG_SELECT_SCOPE') . '</option>';
		foreach ($adapters as $adapter)
		{
			$adapter = ltrim($adapter, DS);
			$adapter = preg_replace('#\.[^.]*$#', '', $adapter);

			if ($adapter == 'base')
			{
				continue;
			}

			$selected = ($adapter == $val)
					  ? ' selected="selected"'
					  : '';

			$out .= '<option value="' . $adapter . '"' . $selected . '>' . $adapter . '</option>';
		}
		$out .= '</select>';

		return $out;
	}
}
