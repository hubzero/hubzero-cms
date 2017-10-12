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
 * iso-8859-x non-breaking space.
 */
define('NBSP', '&#160;');

/**
 * Additions by Axel Boldt follow,
 * partly taken from diff.php, phpwiki-1.3.3
 */
class _HWLDF_WordAccumulator
{
	/**
	 * Short description for '_HWLDF_WordAccumulator'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	public function __construct()
	{
		$this->_lines = array();
		$this->_line = '';
		$this->_group = '';
		$this->_tag = '';
	}

	/**
	 * Short description for '_flushGroup'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $new_tag Parameter description (if any) ...
	 * @return     void
	 */
	public function _flushGroup($new_tag)
	{
		if ($this->_group !== '')
		{
			if ($this->_tag == 'ins')
			{
				$this->_line .= '<ins class="diffchange">' . htmlspecialchars($this->_group) . '</ins>';
			}
			elseif ($this->_tag == 'del')
			{
				$this->_line .= '<del class="diffchange">' . htmlspecialchars($this->_group) . '</del>';
			}
			else
			{
				$this->_line .= htmlspecialchars($this->_group);
			}
		}
		$this->_group = '';
		$this->_tag = $new_tag;
	}

	/**
	 * Short description for '_flushLine'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $new_tag Parameter description (if any) ...
	 * @return     void
	 */
	public function _flushLine($new_tag)
	{
		$this->_flushGroup($new_tag);
		if ($this->_line != '')
		{
			array_push($this->_lines, $this->_line);
		}
		else
		{
			// make empty lines visible by inserting an NBSP
			array_push($this->_lines, NBSP);
		}
		$this->_line = '';
	}

	/**
	 * Short description for 'addWords'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $words Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     void
	 */
	public function addWords($words, $tag = '')
	{
		if ($tag != $this->_tag)
		{
			$this->_flushGroup($tag);
		}

		foreach ($words as $word)
		{
			// new-line should only come as first char of word.
			if ($word == '')
			{
				continue;
			}
			if ($word[0] == "\n")
			{
				$this->_flushLine($tag);
				$word = substr($word, 1);
			}
			assert(!strstr($word, "\n"));
			$this->_group .= $word;
		}
	}

	/**
	 * Short description for 'getLines'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function getLines()
	{
		$this->_flushLine('~done');
		return $this->_lines;
	}
}
