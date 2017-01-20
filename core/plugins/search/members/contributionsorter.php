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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Contributions sorter
 */
class ContributionSorter
{
	/**
	 * Sorting helper
	 *
	 * @param   object   $a
	 * @param   object   $b
	 * @return  integer
	 */
	public static function sort($a, $b)
	{
		$sec_diff = strcmp($a->get_section(), $b->get_section());
		if ($sec_diff < 0)
		{
			return -1;
		}
		if ($sec_diff > 0)
		{
			return 1;
		}
		$a_ord = $a->get('ordering');
		$b_ord = $b->get('ordering');
		return $a_ord == $b_ord ? 0 : $a_ord < $b_ord ? -1 : 1;
	}

	/**
	 * Sort by weight
	 *
	 * @param   object   $a
	 * @param   object   $b
	 * @return  integer
	 */
	public static function sort_weight($a, $b)
	{
		$aw = $a->get_weight();
		$bw = $b->get_weight();
		if ($aw == $bw)
		{
			return 0;
		}
		return $aw > $bw ? -1 : 1;
	}

	/**
	 * Sort by title
	 *
	 * @param   object  $a
	 * @param   object  $b
	 * @return  object
	 */
	public static function sort_title($a, $b)
	{
		return strcmp($a->get_title(), $b->get_title());
	}
}
