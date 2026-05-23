<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Replacement array for FSS with fallback to strtr()
 * Supports lazy initialisation of FSS resource
 */
class ReplacementArray
{
	/*mostly private*/

	/**
	 * Description for 'data'
	 *
	 * @var mixed
	 */
	var $data = false;
	/*mostly private*/

	// Create an object with the specified replacement array
	// The array should have the same form as the replacement array for strtr()

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $data Parameter description (if any) ...
	 * @return     void
	 */
	function __construct($data = array())
	{
		$this->data = $data;
	}

	/**
	 * Short description for '__sleep'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	function __sleep()
	{
		return array('data');
	}

	/**
	 * Short description for '__wakeup'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function __wakeup()
	{
	}

	// Set the whole replacement array at once

	/**
	 * Short description for 'setArray'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $data Parameter description (if any) ...
	 * @return     void
	 */
	function setArray($data)
	{
		$this->data = $data;
	}

	/**
	 * Short description for 'getArray'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	function getArray()
	{
		return $this->data;
	}

	// Set an element of the replacement array

	/**
	 * Short description for 'setPair'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $from Parameter description (if any) ...
	 * @param      unknown $to Parameter description (if any) ...
	 * @return     void
	 */
	function setPair($from, $to)
	{
		$this->data[$from] = $to;
	}

	/**
	 * Short description for 'mergeArray'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $data Parameter description (if any) ...
	 * @return     void
	 */
	function mergeArray($data)
	{
		$this->data = array_merge($this->data, $data);
	}

	/**
	 * Short description for 'merge'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $other Parameter description (if any) ...
	 * @return     void
	 */
	function merge($other)
	{
		$this->data = array_merge($this->data, $other->data);
	}

	/**
	 * Short description for 'replace'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $subject Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	function replace($subject)
	{
		return strtr($subject, $this->data);
	}
}
