<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

use Hubzero\Content\Migration\Base;

class Migration20240210000000DropTableReplyAccessCode  extends Base
{

	static $tableName = '#__reply_access_codes';

	// jos_reply_access_codes table should not be present in core implementation 
	public function up()
	{
		$tableName = self::$tableName;

		$alterTable = "DROP TABLE $tableName;";

		if ($this->db->tableExists($tableName))
		{
			$this->log('Table `jos_reply_access_codes` found in db, dropping...');
			$this->db->setQuery($alterTable);
			$this->db->query();
		}
	}

	// jos_reply_access_codes table was previously present in custom com_reply implementation 
	// This is for consistency with the com_reply implementation only. Table is unused.
	public function down()
	{
		$tableName = self::$tableName;

		$createTable = "CREATE TABLE $tableName (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`user_id` int(11) unsigned NOT NULL,
			`page_id` int(11) unsigned NOT NULL,
			`code` char(64) NOT NULL,
			`expiration` timestamp NULL DEFAULT NULL,
			`created` timestamp NULL DEFAULT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY(code),
			INDEX(user_id)
		) ENGINE=MYISAM DEFAULT CHARSET=utf8;";

		if (!$this->db->tableExists($tableName))
		{
			$this->db->setQuery($createTable);
			$this->db->query();
		}
	}
}
