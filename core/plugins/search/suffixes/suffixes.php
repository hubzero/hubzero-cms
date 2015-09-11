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
 * Short description for 'plgSearchSuffixes'
 *
 * Long description (if any) ...
 */
class plgSearchSuffixes extends \Hubzero\Plugin\Plugin
{
	/**
	 * Short description for 'onSearchExpandTerms'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array &$terms Parameter description (if any) ...
	 * @return     void
	 */
	public static function onSearchExpandTerms(&$terms)
	{
		$add = array();
		foreach ($terms as $term)
		{
			// eg electric <-> electronic
			if (preg_match('/^(.*?)(on)?ic$/', $term, $match))
			{
				$add[] = count($match) == 3 ? $match[1] . 'ic' : $match[1] . 'onic';
			}

			// the fulltxt indexer mangles course names, but it helps if we add a space between the letters and numbers
			if (preg_match('/^([a-zA-Z]+)(\d+)/', $term, $course_name))
			{
				$add[] = $course_name[1] . ' ' . $course_name[2];
			}
		}
		$terms = array_merge($terms, $add);
		foreach ($terms as $term)
		{
			// try plural
			$add[] = substr($term, 0, -1) == 's' ? $term . 'es' : $term . 's';
			if (substr($term, 0, -1) == 'y')
			{
				$add[] = substr($term, 0, strlen($term) -1) . 'ies';
			}
		}
		$terms = array_merge($terms, $add);
	}
}

