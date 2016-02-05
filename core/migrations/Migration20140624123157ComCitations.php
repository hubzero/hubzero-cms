<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for changing uid from username to int
 **/
class Migration20140624123157ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "describe #__citations uid";
		$this->db->setQuery($query);
		$uidField = $this->db->loadObject();

		// if we have an INT already, were good to go
		if (strtolower($uidField->Type) == 'int(11)')
		{
			return;
		}

		// load all citations
		$query = "SELECT id, uid FROM `#__citations`";
		$this->db->setQuery($query);
		$citations = $this->db->loadObjectList();
		foreach ($citations as $citation)
		{
			if (!is_numeric($citation->uid))
			{
				$newId = 62;
				$profile = \Hubzero\User\Profile::getInstance($citation->uid);
				if (is_object($profile))
				{
					$newId = $profile->get('uidNumber');
				}

				$query = "UPDATE `#__citations` SET uid=" . $this->db->quote($newId) . " WHERE id=" . $this->db->quote($citation->id);
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		// change column name
		$query = "ALTER TABLE `#__citations` CHANGE uid uid INT(11);";
		$this->db->setQuery($query);
		$this->db->query();
	}
}