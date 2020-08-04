<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache;

/**
 * Cache storage helper functions.
 */
class Auditor
{
	/**
	 * Cache data group
	 *
	 * @var  string
	 */
	public $group = '';

	/**
	 * Cached item size
	 *
	 * @var  string
	 */
	public $size = 0;

	/**
	 * Counter
	 *
	 * @var  integer
	 */
	public $count = 0;

	/**
	 * Constructor
	 *
	 * @param   string  $group  The cache data group
	 * @return  void
	 */
	public function __construct($group)
	{
		$this->group = $group;
	}

	/**
	 * Increase cache items count.
	 *
	 * @param   string  $size  Cached item size
	 * @return  void
	 */
	public function tally($size)
	{
		$this->size = number_format($this->size + $size, 2, '.', '');
		$this->count++;
	}
}
