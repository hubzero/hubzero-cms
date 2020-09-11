<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

require_once __DIR__ . '/BaseSubscription.php';

class Access_Group_Membership_Subscription extends BaseSubscription
{
	/**
	 * Constructor
	 *
	 * @param   integer  $pId
	 * @param   integer  $uId
	 * @return  void
	 */
	public function __construct($pId, $uId)
	{
		parent::__construct($pId, $uId);
	}
}
