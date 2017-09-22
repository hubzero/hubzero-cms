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

/**
 * Utility class working with phpsetting
 */
class ComponentsSystemHelpersHtmlPhpsetting
{
	/**
	 * Method to generate a boolean message for a value
	 *
	 * @param   boolean  $val  Is the value set?
	 * @return  string   html code
	 */
	public static function boolean($val)
	{
		return ($val ? '<span class="state on"><span>' . Lang::txt('JON') : '<span class="state off"><span>' . Lang::txt('JOFF')) . '</span></span>';
	}

	/**
	 * Method to generate a boolean message for a value
	 *
	 * @param   boolean  $val Is the value set?
	 * @return  string   html code
	 */
	public static function set($val)
	{
		return ($val ? '<span class="state yes"><span>' . Lang::txt('JYES') : '<span class="state no"><span>' . Lang::txt('JNO')) . '</span></span>';
	}

	/**
	 * Method to generate a string message for a value
	 *
	 * @param   string  $val  A php ini value
	 * @return  string  html code
	 */
	public static function string($val)
	{
		return (empty($val) ? Lang::txt('JNONE') : $val);
	}

	/**
	 * Method to generate an integer from a value
	 *
	 * @param   string   $val  A php ini value
	 * @return  integer
	 */
	public static function integer($val)
	{
		return intval($val);
	}
}
