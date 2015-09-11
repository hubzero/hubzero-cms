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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Helpers;

/**
 * HTML helper for system
 */
class Html
{
	/**
	 * Sortable table header in "scripts for this host" view
	 *
	 * @param  string  $key    Sort key
	 * @param  string  $name   Link name
	 * @param  string  $extra  Extra data to append to URL
	 * @param  string
	 */
	public static function sortheader($MYREQUEST, $MY_SELF_WO_SORT, $key, $name, $extra='')
	{
		if ($MYREQUEST['SORT1'] == $key)
		{
			$MYREQUEST['SORT2'] = $MYREQUEST['SORT2']=='A' ? 'D' : 'A';
		}

		return "<a class=\"sortable\" href=\"$MY_SELF_WO_SORT$extra&amp;SORT1=$key&amp;SORT2=" . $MYREQUEST['SORT2'] . "\">$name</a>";
	}

	/**
	 * Pretty printer for byte values
	 *
	 * @param   integer  $s  Byte value
	 * @return  string
	 */
	public static function bsize($s)
	{
		foreach (array('', 'K', 'M', 'G') as $i => $k)
		{
			if ($s < 1024)
			{
				break;
			}
			$s/=1024;
		}
		return sprintf("%5.1f %sBytes", $s, $k);
	}
}