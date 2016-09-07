<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to change database engine for cart_meta table
 **/
class Migration2016090710430000ComCart extends Base
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
		$this->changeEngine('#__cart_meta','MyISAM');
	}

	public function down()
	{
	}
}
