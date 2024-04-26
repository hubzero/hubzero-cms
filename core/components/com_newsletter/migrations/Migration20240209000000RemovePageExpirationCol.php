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

	// next_expiration column should not be present in core implementation of table
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

	// next_expiration column was previously present in custom implementation of table
	// this is for consistency with the com_reply implementation only. Column is unused.
	public function down()
	{
		$tableName = self::$tableName;

		$alterTable = "ALTER TABLE $tableName ADD COLUMN next_expiration timestamp;";

		if ($this->db->tableExists($tableName))
		{
			if (!$this->db->tableHasField($tableName, 'next_expiration')) 
			{
				$this->db->setQuery($alterTable);
				$this->db->query();
			}
		}
	}
}
