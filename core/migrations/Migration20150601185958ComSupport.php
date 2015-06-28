<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for setting ticket closed time
 **/
class Migration20150601185958ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_tickets'))
		{
			$query = "UPDATE `#__support_tickets` AS t SET t.`closed`=(SELECT `created` FROM `#__support_comments` AS c WHERE c.ticket=t.id ORDER BY c.created DESC LIMIT 1) WHERE t.`open`=0 AND t.`closed`='0000-00-00 00:00:00';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}