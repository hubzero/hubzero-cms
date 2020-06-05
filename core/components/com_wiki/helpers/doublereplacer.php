<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Class to perform secondary replacement within each replacement string
 */
class DoubleReplacer extends Replacer
{
	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $from Parameter description (if any) ...
	 * @param      unknown $to Parameter description (if any) ...
	 * @param      integer $index Parameter description (if any) ...
	 * @return     void
	 */
	function __construct($from, $to, $index = 0)
	{
		$this->from = $from;
		$this->to = $to;
		$this->index = $index;
	}

	/**
	 * Short description for 'replace'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $matches Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	function replace($matches)
	{
		return str_replace($this->from, $this->to, $matches[$this->index]);
	}
}
