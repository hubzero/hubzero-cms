<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

use Hubzero\Content\Migration\Base;

class Migration20240209000000RemovePageExpirationCol extends Base
{

	static $tableName = '#__reply_pages';

	public function up()
	{
		$tableName = self::$tableName;

		$alterTable = "ALTER TABLE $tableName DROP COLUMN next_expiration;";

		if ($this->db->tableExists($tableName))
		{
			if ($this->db->tableHasField($tableName, 'next_expiration')) 
			{
				$this->log('Column `next_expiration` found in table, dropping...');
				$this->db->setQuery($alterTable);
				$this->db->query();
			}
		}
	}

	public function down()
	{
		// no-op
	}
}
