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

	public function down()
	{
		// No-op
	}

}
