<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
			$query = "UPDATE `#__migrations` SET `scope` = REPLACE(`scope`, " . $this->db->quote(JPATH_ROOT . DS) . ", '')";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}