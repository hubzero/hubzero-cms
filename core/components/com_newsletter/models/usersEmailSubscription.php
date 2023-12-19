<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Models;

use Hubzero\Database\Relational;

class UsersEmailSubscription extends Relational
{
	protected $table = '#__users_email_subscriptions';

	public $initiate = ['created'];

	public function save() {
		if (!$this->isNew())
		{
			$this->set('modified', date("Y-m-d H:i:s"));
		}

		return parent::save();
	}

}
