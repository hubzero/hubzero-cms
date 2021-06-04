<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications child sorter class
 */
class PublicationChildSorter
{
	/**
	 * Description for 'order'
	 *
	 * @var  array
	 */
	private $order;

	/**
	 * Constructor
	 *
	 * @param   array  $order
	 * @return  void
	 */
	public function __construct($order)
	{
		$this->order = $order;
	}

	/**
	 * Sort objects
	 *
	 * @param   object  $a
	 * @param   object  $b
	 * @return  integer
	 */
	public function sort($a, $b)
	{
		$a_id = $a->get('id');
		$b_id = $b->get('id');
		$sec_diff = strcmp($a->get_section(), $b->get_section());
		if ($sec_diff < 0)
		{
			return -1;
		}
		if ($sec_diff > 0)
		{
			return 1;
		}
		$a_ord = $this->order[$a_id];
		$b_ord = $this->order[$b_id];
		return $a_ord == $b_ord ? 0 : ($a_ord < $b_ord ? -1 : 1);
	}
}
