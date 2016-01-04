<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mod_feedaggregator 
 **/
class Migration20160104220531ModFeedaggregator extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$element = 'mod_feedaggregator';
		$this->addModuleEntry($element);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "DELETE from `#__extensions` WHERE name = 'mod_feedaggregator';";
		$this->db->setQuery($query);
		$this->db->query();
	}
}
