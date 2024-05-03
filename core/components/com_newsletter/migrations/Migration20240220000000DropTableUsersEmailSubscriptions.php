<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

use Hubzero\Content\Migration\Base;

class Migration20240220000000DropTableUsersEmailSubscriptions extends Base
{

	static $tableName = '#__users_email_subscriptions';

	// users_email_subscriptions table should not be present in core implementation 
	public function up()
	{
		$tableName = self::$tableName;

		$alterTable = "DROP TABLE $tableName;";

		if ($this->db->tableExists($tableName))
		{
			$this->log('Table `jos_users_email_subscriptions` found in db, dropping...');
			$this->db->setQuery($alterTable);
			$this->db->query();
		}
	}

	public function down()
	{
		// no-op
	}
}
