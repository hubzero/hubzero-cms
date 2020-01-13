<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20190118154719ComFormsCreateFormsPrerequisitesTable extends Base
{

	static $tableName = '#__forms_form_prerequisites';

	public function up()
	{
		$tableName = self::$tableName;

		$createTable = "CREATE TABLE $tableName (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`form_id` int(11) unsigned NOT NULL,
			`prerequisite_id` int(11) unsigned NOT NULL,
			`prerequisite_scope` varchar(50) NOT NULL,
			`order` int(11) unsigned NOT NULL,
			`created_by` int(11) unsigned NOT NULL,
			`created` timestamp NULL DEFAULT NULL,
			UNIQUE KEY `form_prerequisite` (`form_id`, `prerequisite_id`, `prerequisite_scope`),
			PRIMARY KEY (`id`)
		) ENGINE=MYISAM DEFAULT CHARSET=utf8;";

		if (!$this->db->tableExists($tableName))
		{
			$this->db->setQuery($createTable);
			$this->db->query();
		}
	}

	public function down()
	{
		$tableName = self::$tableName;

		$dropTable = "DROP TABLE $tableName";

		if ($this->db->tableExists($tableName))
		{
			$this->db->setQuery($dropTable);
			$this->db->query();
		}
	}

}
