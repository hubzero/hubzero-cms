<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing unnecessary ticket severity level
 **/
class Migration20151215155336ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_tickets'))
		{
			$query = "UPDATE `#__support_tickets` SET `severity`=" . $this->db->quote('minor') . " WHERE `severity`=" . $this->db->quote('trivial');
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__support_tickets'))
		{
			// nothing to do here...
		}
	}
}