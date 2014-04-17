<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140417134640TplKameleonAdmin extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->db->setQuery("SELECT `extension_id` FROM `#__extensions` WHERE `type`='template' AND `element`='kameleon' AND `client_id`=1");
		if (!$this->db->loadResult())
		{
			$query = "INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) 
					VALUES (NULL, 'kameleon (admin)', 'template', 'kameleon', '', '1', '1', '1', '0', '{}', '{}', '', '', '0', '0000-00-00 00:00:00', '0', '0');";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "INSERT INTO `#__template_styles` (`id`, `template`, `client_id`, `home`, `title`, `params`) 
					VALUES (NULL, 'kameleon', '1', '0', 'kameleon (admin)', '{\"header\":\"dark\",\"theme\":\"salmon\"}');";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->db->setQuery("SELECT `extension_id` FROM `#__extensions` WHERE `type`='template' AND `element`='kameleon' AND `client_id`=1");
		if ($this->db->loadResult())
		{
			$query = "DELETE FROM `#__extensions` WHERE `type`='template' AND `element`='kameleon' AND `client_id`=1;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DELETE FROM `#__template_styles` WHERE `template`='kameleon' AND `client_id`=1;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}