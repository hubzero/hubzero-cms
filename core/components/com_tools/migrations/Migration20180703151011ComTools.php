<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for com_tools to specify display ranges assigned to a hub,
 * i.e., the range used on an execution host.  
 **/

class Migration20180703151011ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$mwdb = $this->getMWDBO())
		{
			$this->setError('Failed to connect to the middleware database', 'warning');
			return false;
		}

		// ADD COLUMN first_display to table host
		if ($mwdb->tableExists('host') && !$mwdb->tableHasField('host', 'first_display')) 
		{
			$query = "ALTER TABLE host ADD COLUMN first_display INT DEFAULT 1;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$mwdb = $this->getMWDBO())
		{
			$this->setError('Failed to connect to the middleware database', 'warning');
			return false;
		}

		// Drop column first_display
		if ($mwdb->tableExists('host') && $mwdb->tableHasField('host', 'first_display')) 
		{
			$query = "ALTER TABLE host DROP COLUMN first_display;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
