<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'blocked' state to auth log
 **/
class Migration20160808124602ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__users_log_auth', 'status'))
		{
			// Check to see if the column has the value
			$query = "SHOW COLUMNS FROM #__users_log_auth WHERE Field = 'status';";
			$this->db->setQuery($query);
			$this->db->query();
			$result = $this->db->loadAssoc();

			preg_match("/^enum\(\'(.*)\'\)$/", $result['Type'], $matches);
			$enum = explode("','", $matches[1]);
			
			// Add it if it's missing
			if (!in_array('blocked', $enum))
			{
				$query = "ALTER TABLE jos_users_log_auth MODIFY COLUMN status ENUM('success','failure','blocked');";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__users_log_auth', 'status'))
		{
			// Check to see if the column has the value
			$query = "SHOW COLUMNS FROM #__users_log_auth WHERE Field = 'status';";
			$this->db->setQuery($query);
			$this->db->query();
			$result = $this->db->loadAssoc();

			preg_match("/^enum\(\'(.*)\'\)$/", $result['Type'], $matches);
			$enum = explode("','", $matches[1]);
			
			// Add it if it's missing
			if (in_array('blocked', $enum))
			{
				$query = "ALTER TABLE jos_users_log_auth MODIFY COLUMN status ENUM('success','failure');";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
