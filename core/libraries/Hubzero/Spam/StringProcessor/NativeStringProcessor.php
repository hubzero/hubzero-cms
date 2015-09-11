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

namespace Hubzero\Spam\StringProcessor;

/**
 * Spam string processor.
 *
 * Based on work by Laju Morrison <morrelinko@gmail.com>
 */
class NativeStringProcessor implements StringProcessorInterface
{
	/**
	 * Perform ASCII conversion?
	 *
	 * @var  bool
	 */
	protected $asciiConversion = true;

	/**
	 * Aggressive processing?
	 *
	 * @var  bool
	 */
	protected $aggressive = false;

	/**
	 * Constructor
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		foreach ($options as $option => $value)
		{
			switch ($option)
			{
				case 'ascii_conversion':
					$this->asciiConversion = (bool) $value;
				break;
				case 'aggressive':
					$this->aggressive = (bool) $value;
				break;
				default:
				break;
			}
		}
	}

	/**
	 * Prepare a string
	 *
	 * @param   string  $string
	 * @return  mixed
	 */
	public function prepare($string)
	{
		if ($this->asciiConversion)
		{
			setlocale(LC_ALL, 'en_us.UTF8');
			$string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
		}

		if ($this->aggressive)
		{
			// Convert some characters that 'MAY' be used as alias
			$string = str_replace(array('@', '$', '[dot]', '(dot)'), array('at', 's', '.', '.'), $string);

			// Remove special characters
			$string = preg_replace("/[^a-zA-Z0-9-\.]/", "", $string);

			// Strip multiple dots (.) to one. eg site......com to site.com
			$string = preg_replace("/\.{2,}/", '.', $string);
		}

		$string = trim(strtolower($string));
		$string = str_replace(array("\t", "\r\n", "\r", "\n"), '', $string);

		return $string;
	}
}
