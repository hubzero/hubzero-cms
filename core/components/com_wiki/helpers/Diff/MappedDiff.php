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

// No direct access.
defined('_HZEXEC_') or die();

/**
 * Short description for 'class'
 *
 * Long description (if any) ...
 */
class MappedDiff extends Diff
{
	/**
	 * Constructor.
	 *
	 * Computes diff between sequences of strings.
	 *
	 * This can be used to compute things like
	 * case-insensitive diffs, or diffs which ignore
	 * changes in white-space.
	 *
	 * @param   array  $from_lines          An array of strings. (Typically these are lines from a file.)
	 * @param   array  $to_lines Parameter  An array of strings.
	 * @param   array  $mapped_from_lines   This array should have the same size number of elements as $from_lines.
	 *                                      The elements in $mapped_from_lines and $mapped_to_lines are what is actually compared when computing the diff.
	 * @param   array  $mapped_to_lines     This array should have the same number of elements as $to_lines.
	 * @return  void
	 */
	public function __construct($from_lines, $to_lines, $mapped_from_lines, $mapped_to_lines)
	{
		assert(count($from_lines) == count($mapped_from_lines));
		assert(count($to_lines) == count($mapped_to_lines));

		parent::__construct($mapped_from_lines, $mapped_to_lines);

		$xi = $yi = 0;
		for ($i = 0; $i < count($this->edits); $i++)
		{
			$orig = &$this->edits[$i]->orig;
			if (is_array($orig))
			{
				$orig = array_slice($from_lines, $xi, count($orig));
				$xi += count($orig);
			}

			$closing = &$this->edits[$i]->closing;
			if (is_array($closing))
			{
				$closing = array_slice($to_lines, $yi, count($closing));
				$yi += count($closing);
			}
		}
	}
}
