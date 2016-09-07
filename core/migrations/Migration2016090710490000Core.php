<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to change database engine for a few core tables that could have been missed
 **/
class Migration2016090710490000Core extends Base
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
		$this->changeEngine('#__viewlevels','MyISAM');
		$this->changeEngine('#__languages','MyISAM');
		$this->changeEngine('#__associations','MyISAM');
	}

	public function down()
	{
	}
}
