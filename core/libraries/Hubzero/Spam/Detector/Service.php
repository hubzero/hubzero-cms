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

namespace Hubzero\Spam\Detector;

use Hubzero\Base\Object;

/**
 * Abstract spam detector service
 */
abstract class Service extends Object implements DetectorInterface
{
	/**
	 * Message to report
	 *
	 * @var  string
	 */
	protected $message = '';

	/**
	 * The value to be validated
	 *
	 * @var mixed
	 */
	protected $_value;

	/**
	 * Returns the validation value
	 *
	 * @return  mixed  Value to be validated
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * Sets the value to be validated and clears the errors arrays
	 *
	 * @param   mixed  $value
	 * @return  void
	 */
	public function setValue($value)
	{
		$this->_value  = $value;
		$this->_errors = array();
		$this->message = '';
	}

	/**
	 * Run content through spam detection
	 *
	 * @param   array  $data
	 * @return  bool
	 */
	public function detect($data)
	{
		return false;
	}

	/**
	 * Train the service
	 *
	 * @param   string   $data
	 * @param   boolean  $isSpam
	 * @return  boolean
	 */
	public function learn($data, $isSpam)
	{
		if (!$data)
		{
			return false;
		}

		return true;
	}

	/**
	 * Forget a trained value
	 *
	 * @param   string   $data
	 * @param   boolean  $isSpam
	 * @return  boolean
	 */
	public function forget($data, $isSpam)
	{
		return true;
	}

	/**
	 * Return any message the service may have
	 *
	 * @return  string
	 */
	public function message()
	{
		return $this->message;
	}
}
