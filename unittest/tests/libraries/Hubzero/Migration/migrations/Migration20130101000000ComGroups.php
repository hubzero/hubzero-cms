<?php

class Migration20130101000000ComGroups extends Hubzero_Migration
{
	protected static function up(&$db)
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__migrations_sample_table` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		$db->setQuery($query);
		$db->query();
	}

	protected function down(&$db)
	{
		$query = "DROP TABLE `#__migrations_sample_table`;";

		$db->setQuery($query);
		$db->query();
	}

	protected function pre(&$db)
	{
		// perform checks
	}

	protected function post(&$db)
	{
		// make sure everything looks ok
	}
}