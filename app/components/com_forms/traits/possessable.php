<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Traits;

trait possessable
{

	/**
	 * Does record belong to user with given ID
	 *
	 * @param    int    $userId   User ID
	 * @return   bool
	 */
	public function isOwnedBy($userId)
	{
		$ownerForeignKey = $this->_ownerForeignKey;

		return $userId === $this->$ownerForeignKey;
	}

}
