<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for truncating possibly large obsolete session_log table
 **/
class  Migration20161018093917Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		/* Future migration should drop the table */
		$query = "TRUNCATE #__session_log;";
		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		/*
		   No down method, truncated data can not be recovered nor
		   should it need to be 
		*/
	}
}
