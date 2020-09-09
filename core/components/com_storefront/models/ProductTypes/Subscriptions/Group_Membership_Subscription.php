<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

require_once __DIR__ . '/BaseSubscription.php';

class Group_Membership_Subscription extends BaseSubscription
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

	/**
	 * Get expiration info.
	 *
	 * @return  void
	 * @throws  Exception
	 */
	public function _getExpiration()
	{
		// This will get expiration from the correct place
		throw new Exception('not implemented');
	}
}
