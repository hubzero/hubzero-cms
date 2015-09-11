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
 * Short description for 'plgSearchSortCourses'
 *
 * Long description (if any) ...
 */
class plgSearchSortCourses extends \Hubzero\Plugin\Plugin
{
	/**
	 * Short description for 'onYSearchSort'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $a Parameter description (if any) ...
	 * @param      object $b Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function onSearchSort($a, $b)
	{
		if ($a->get_plugin() !== 'resources' || $b->get_plugin() !== 'resources'
		 || $a->get_section() !== 'Courses' || $b->get_section() !== 'Courses')
		{
			return 0;
		}

		// Compare the leading parts of the resources to guess whether they
		// refer to the same course
		$title_a = preg_replace('/[^a-z]/', '', strtolower($a->get_title()));
		$title_b = preg_replace('/[^a-z]/', '', strtolower($a->get_title()));
		$match_threshold = 10;
		$match = true;
		for ($idx = 0; $idx < min($match_threshold, min(strlen($title_a), strlen($title_b))); ++$idx)
		{
			if ($title_a[$idx] !== $title_b[$idx])
			{
				$match = false;
				break;
			}
		}
		if (!$match)
		{
			return 0;
		}

		return $a->get_date() > $b->get_date() ? 1 : -1;
	}
}

