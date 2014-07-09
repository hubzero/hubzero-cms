<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding mod_supportactivity
 **/
class Migration20140709174727ModSupportactivity extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$element = 'mod_supportactivity';
		$params  = '';
		$enabled = 1;

		if ($this->db->tableExists('#__extensions'))
		{
			$name = $element;

			// First, make sure it isn't already there
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `name` = " . $this->db->quote($name);
			$this->db->setQuery($query);
			if ($this->db->loadResult())
			{
				return true;
			}

			$ordering = 0;

			if (!empty($params) && is_array($params))
			{
				$params = json_encode($params);
			}

			$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)";
			$query .= " VALUES ('{$name}', 'module', '{$element}', '', 1, {$enabled}, 1, 0, '', ".$this->db->quote($params).", '', '', 0, '0000-00-00 00:00:00', {$ordering}, 0)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$element = 'mod_supportactivity';

		if ($this->db->tableExists('#__extensions'))
		{
			// Delete module entry
			$query = "DELETE FROM `#__extensions` WHERE `element` = '{$element}'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}