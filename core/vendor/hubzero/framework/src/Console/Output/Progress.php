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
 * @package   framework
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Output;

use Hubzero\Console\Output;

/**
 * Output class for rendering progress bar/text
 **/
class Progress extends Output
{
	/**
	 * Track content length
	 *
	 * @var  int
	 **/
	private $contentLength = 0;

	/**
	 * Save initial message length
	 *
	 * @var  int
	 **/
	private $initMessageLength = 0;

	/**
	 * Initialize progress counter
	 *
	 * @param   string  $initMessage  Initial message
	 * @param   string  $type         Progress type
	 * @param   int     $total        Total number of progress points
	 * @return  void
	 **/
	public function init($initMessage = null, $type = 'percentage', $total = null)
	{
		// Force interactivity of this class (this doesn't affect our primary output class)
		$this->makeInteractive();

		if (isset($initMessage))
		{
			// Add the intital message
			$this->addString($initMessage);

			// Track some string lengths
			$this->initMessageLength = strlen($initMessage);
		}

		switch ($type)
		{
			case 'ratio':
				$this->setProgress(0, $total);
				break;
			case 'percentage':
			default:
				// Set current progress to 0
				$this->setProgress('0');
				break;
		}
	}

	/**
	 * Set the current progress val
	 *
	 * @param   int  $val  Progress value
	 * @param   int  $tot  Total value
	 * @return  void
	 **/
	public function setProgress($val, $tot = null)
	{
		if ($this->contentLength > 0)
		{
			// Back up current length of content
			$this->backspace($this->contentLength);
		}

		if (!is_null($tot))
		{
			$content = "({$val}/{$tot})";
		}
		else
		{
			// Get new content
			$content = "{$val}%";
		}

		// Save length of content for next call
		$this->contentLength = strlen($content);

		// Add the string
		$this->addString($content);
	}

	/**
	 * Finish progress output
	 *
	 * @return  void
	 **/
	public function done()
	{
		// Compute the totall length of the output
		$length = $this->contentLength + $this->initMessageLength;

		// Back up all the way
		$this->backspace($length, true);

		// In case this gets used again...
		$this->contentLength     = 0;
		$this->initMessageLength = 0;
	}
}