<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();


class SimpleCIDR
{
	/**
	 * List of instances
	 *
	 * @var  array
	 */
	protected static $instances = array();

	/**
	 * Network
	 *
	 * @var  string
	 */
	public $network;

	/**
	 * Constructor
	 *
	 * @param   string  $network
	 * @return  void
	 */
	public function __construct($network=false)
	{
		if ($network)
		{
			$this->setNetwork($network);
		}
	}

	/**
	 * Get an instance of the class
	 *
	 * @param   string  $network
	 * @return  object
	 */
	public static function getInstance($network=false)
	{
		$instanceid = $network ? $network : '';
		if (empty(self::$instances[$instanceid]))
		{
			self::$instances[$instanceid] = new SimpleCIDR($instanceid);
		}
		return self::$instances[$instanceid];
	}

	/**
	 * Set network
	 *
	 * @param   string  $network
	 * @return  void
	 */
	public function setNetwork($network=false)
	{
		if ($network)
		{
			$this->network = $network;
		}
	}

	/**
	 * Does a network contian an IP
	 *
	 * @param   string  $ip
	 * @return  bool
	 */
	public function contains($ip)
	{
		list($subnet, $bits) = explode('/', $this->network);
		$ip = ip2long($ip);
		$subnet = ip2long($subnet);
		$mask = -1 << (32 - $bits);
		$subnet &= $mask; // nb: in case the supplied subnet wasn't correctly aligned
		return ($ip & $mask) == $subnet;
	}
}
