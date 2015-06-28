<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing document root from migration scope
 **/
class Migration20140922135214Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__migrations') && $this->db->tableHasField('#__migrations', 'scope'))
		{
			$query = "UPDATE `#__migrations` SET `scope` = REPLACE(`scope`, " . $this->db->quote(PATH_ROOT . DS) . ", '')";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}