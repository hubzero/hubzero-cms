<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for updating closed timestamp on support tickets
 **/
class Migration20150122165523ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_tickets'))
		{
			$query = "UPDATE `#__support_tickets` AS t SET t.`closed`=(SELECT c.created FROM `#__support_comments` AS c WHERE c.ticket=t.id ORDER BY c.created DESC LIMIT 1) WHERE t.`closed`='0000-00-00 00:00:00' AND t.`open`=0";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}