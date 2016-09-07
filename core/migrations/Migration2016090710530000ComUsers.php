<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to change database engine for some user tables
 **/
class Migration2016090710530000ComUsers extends Base
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
		$this->changeEngine('#__users_tool_preferences','MyISAM');
		$this->changeEngine('#__users_quotas_classes_groups','MyISAM');
	}

	public function down()
	{
	}
}
