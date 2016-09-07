<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to change database engine for support_quest_folders
 **/
class Migration2016090710560000ComSupport extends Base
{	
	private function changeEngine($table,$engine)
	{
		if ($this->db->tableExists($table) && strtolower($this->db->getEngine($table)) != $engine)
		{
			$query = "ALTER TABLE `" . $table . "` ENGINE = " . $engine;
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function up()
	{
		$this->changeEngine('#__support_query_folders','MyISAM');
	}

	public function down()
	{
	}
}
