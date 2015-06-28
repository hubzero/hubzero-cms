<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for making default collections private.
 *
 * The collections component only pulls public collections. This allows
 * default collections to be renamed and publicly displayed, whereas
 * previously they would be filtered out by the component.
 **/
class Migration20140630084843ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__collections'))
		{
			$query = "UPDATE `#__collections` SET access=4 WHERE is_default=1 AND access=0";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__collections'))
		{
			$query = "UPDATE `#__collections` SET access=0 WHERE is_default=1 AND access=4";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}