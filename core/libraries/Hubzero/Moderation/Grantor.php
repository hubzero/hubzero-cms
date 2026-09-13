<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation;

/**
 * How credits get handed out
 *
 * Who *may* receive credits is settled once, in Eligibility. How many arrive
 * and how often is policy, and policy varies with the size of the site: a
 * scheme that ties moderation capacity to traffic is the better mechanism on
 * a busy site and issues nothing at all on a quiet one. So it is swappable,
 * and the seam exists from the first implementation rather than being
 * retrofitted around the second.
 */
interface Grantor
{
	/**
	 * What this grantor is called, for the record it leaves
	 *
	 * @return  string
	 */
	public function name();

	/**
	 * Hand out credits for one item type
	 *
	 * @param   string  $itemType
	 * @param   array   $eligible  User ids, from Eligibility::pool()
	 * @return  array   counts for the grant record
	 */
	public function run($itemType, array $eligible);
}
