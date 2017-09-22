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
 * Utility class working with directory
 */
class ComponentsSystemHelpersHtmlDirectory
{
	/**
	 * Method to generate a (un)writable message for directory
	 *
	 * @param   boolean  $writable  is the directory writable?
	 * @return  string   html code
	 */
	public static function writable($writable)
	{
		if ($writable)
		{
			return '<span class="writable" style="color: green;">' . Lang::txt('COM_SYSTEM_INFO_WRITABLE') . '</span>';
		}
		else
		{
			return '<span class="unwritable" style="color: red;">' . Lang::txt('COM_SYSTEM_INFO_UNWRITABLE') . '</span>';
		}
	}

	/**
	 * Method to generate a message for a directory
	 *
	 * @param   string   $dir      the directory
	 * @param   boolean  $message  the message
	 * @param   boolean  $visible  is the $dir visible?
	 * @return  string   html code
	 */
	public static function message($dir, $message, $visible=true)
	{
		if ($visible)
		{
			$output = $dir;
		}
		else
		{
			$output ='';
		}
		if (empty($message))
		{
			return $output;
		}
		else
		{
			return $output . ' <strong>' . Lang::txt($message) . '</strong>';
		}
	}
}
