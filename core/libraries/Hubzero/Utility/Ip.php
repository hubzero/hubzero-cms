<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

/**
 * IP address class
 */
class Ip
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
			throw new \RuntimeException('Invalid input, not an IP address');
		}

		return $above ? ($this->ipLong >= $threshold) : ($this->ipLong <= $threshold);
	}
}
