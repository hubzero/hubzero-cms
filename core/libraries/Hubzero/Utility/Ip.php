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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

use Hubzero\Base\Object;

/**
 * IP address class
 */
class Ip extends Object
{
	/**
	 * The instance ip address (in standard dotted form)
	 *
	 * @var  string
	 **/
	private $ip;

	/**
	 * The instance ip address (in long integer form)
	 *
	 * @var  int
	 **/
	private $ipLong;

	/**
	 * Constructs a new instance
	 *
	 * @param   string  $ip  The ip address in dotted form
	 * @return  void
	 **/
	public function __construct($ip)
	{
		$this->ip     = $ip;
		$this->ipLong = ip2long($ip);
	}

	/**
	 * Checks to see if the ip address is of valid form
	 *
	 * @param   string  $type  The IP Protocol version to validate against
	 * @return  bool
	 **/
	public function isValid($type = 'both')
	{
		$type  = strtolower($type);
		$flags = 0;

		if ($type === 'ipv4')
		{
			$flags = FILTER_FLAG_IPV4;
		}
		if ($type === 'ipv6')
		{
			$flags = FILTER_FLAG_IPV6;
		}

		return (bool) filter_var($this->ip, FILTER_VALIDATE_IP, ['flags' => $flags]);
	}

	/**
	 * Checks to see if the ip address is within a private range
	 *
	 * @return  bool
	 **/
	public function isPrivate()
	{
		return ($this->isBetween('192.168.0.0', '192.168.255.255') ||
		        $this->isBetween('10.0.0.0', '10.255.255.255') ||
		        $this->isBetween('172.16.0.0', '172.31.255.255'));
	}

	/**
	 * Checks to see if the ip address is between the given range (inclusive)
	 *
	 * @param   string  $low   The low end check
	 * @param   string  $high  The high end check
	 * @return  bool
	 **/
	public function isBetween($low, $high)
	{
		return ($this->isAbove($low) && $this->isBelow($high));
	}

	/**
	 * Checks to see if the ip address is greater than or equal to the given
	 *
	 * @param   string  $threshold  The comparison threshold
	 * @return  bool
	 **/
	public function isAbove($threshold)
	{
		return $this->isRelativeTo($threshold);
	}

	/**
	 * Checks to see if the ip address is less than or equal to the given
	 *
	 * @param   string  $threshold  The comparison threshold
	 * @return  bool
	 **/
	public function isBelow($threshold)
	{
		return $this->isRelativeTo($threshold, false);
	}

	/**
	 * Checks to see if the ip address is less than or greater than the given
	 *
	 * @param   string  $threshold  The comparison threshold
	 * @param   bool    $above      Whether to check above or below
	 * @return  bool
	 **/
	private function isRelativeTo($threshold, $above = true)
	{
		$threshold = ip2long($threshold);

		if (!$threshold)
		{
			throw new \RuntimeException("Invalid input, not an IP address");
		}

		return $above ? ($this->ipLong >= $threshold) : ($this->ipLong <= $threshold);
	}
}
