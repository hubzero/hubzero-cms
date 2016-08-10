<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for languages table addition
 **/
class Migration20150826245312Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__languages'))
		{
			$query = "UPDATE `#__languages` SET access=1 WHERE lang_id=1 AND access=0;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
