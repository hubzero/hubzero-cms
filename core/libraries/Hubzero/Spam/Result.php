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

namespace Hubzero\Spam;

/**
 * Spam result.
 *
 * Based on work by Laju Morrison <morrelinko@gmail.com>
 */
class Result
{
	/**
	 * @var  bool
	 */
	protected $isSpam = false;

	/**
	 * @var  array
	 */
	protected $messages = array();

	/**
	 * Constructor
	 *
	 * @param   bool   $isSpam    Result from spam detectors
	 * @param   array  $messages  Messages to pass along
	 * @return  void
	 */
	public function __construct($isSpam, array $messages = array())
	{
		$this->isSpam   = $isSpam;
		$this->messages = $messages;
	}

	/**
	 * Alias of SpamResult::failed();
	 *
	 * @return  bool
	 */
	public function isSpam()
	{
		return $this->failed();
	}

	/**
	 * Did the content pass?
	 *
	 * @return  bool
	 */
	public function passed()
	{
		return $this->isSpam == false;
	}

	/**
	 * Did the content fail?
	 *
	 * @return  bool
	 */
	public function failed()
	{
		return !$this->passed();
	}

	/**
	 * Get the list of messages
	 *
	 * @return  array
	 */
	public function getMessages()
	{
		return $this->messages;
	}
}
