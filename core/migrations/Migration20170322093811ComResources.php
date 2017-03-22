<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding normalized resource aliases where missing
 **/
class Migration20170322093811ComResources extends Base
{
	public function normalize($txt)
	{
		return preg_replace("/[^a-zA-Z0-9\-_]/", '', strtolower($txt));
	}

	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resource_types'))
		{
			$query = "SELECT id,type FROM `#__resource_types` WHERE alias='' OR alias IS NULL";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			foreach($results as $result)
			{
				$alias = $this->normalize($result->type);
				$query = "UPDATE #__resource_types SET alias=" . $this->db->quote($alias) . " WHERE id = " . $this->db->quote($result->id);
	                        $this->db->setQuery($query);
	                        $this->db->execute();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		/* Can't undo this without recording which entries changed. */
	}
}
