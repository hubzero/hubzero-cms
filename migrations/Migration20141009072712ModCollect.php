<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding the collect module and removing collect plugins
 **/
class Migration20141009072712ModCollect extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deletePluginEntry('content', 'collect');
		$this->deletePluginEntry('resources', 'collect');
		$this->deletePluginEntry('wiki', 'collect');

		$this->addModuleEntry('mod_collect', 1, '', 0);

		$query = "SELECT COUNT(*) FROM `#__modules` WHERE `module`='mod_collect'";
		$this->db->setQuery($query);
		if (!$this->db->loadResult())
		{
			$position = 'endpage';
			$found = false;

			$query  = "SELECT COUNT(*) FROM `#__modules` WHERE `client_id`=0 AND `position`=";
			$this->db->setQuery($query . $this->db->quote($position));
			if ($this->db->loadResult())
			{
				$found = true;
			}

			if (!$found)
			{
				$position = 'footer';
				$this->db->setQuery($query . $this->db->quote($position));
				if ($this->db->loadResult())
				{
					$found = true;
				}
			}

			if ($found)
			{
				$query  = "SELECT `ordering` FROM `#__modules` WHERE `position`='{$position}' ORDER BY `ordering` DESC LIMIT 1";
				$this->db->setQuery($query);
				$ordering = intval($this->db->loadResult());
				$ordering++;

				$query  = "INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`)";
				$query .= " VALUES ('Collect', '', '', '{$ordering}', '{$position}', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_collect', 1, 0, '', 0, '*')";
				$this->db->setQuery($query);
				$this->db->query();

				$query  = "SELECT id FROM `#__modules` WHERE `module`='mod_collect' LIMIT 1";
				$this->db->setQuery($query);
				$id = $this->db->loadResult();

				$query  = "INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES ({$id}, 0)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Up
	 **/
	public function down()
	{
		$this->addPluginEntry('content', 'collect');
		$this->addPluginEntry('resources', 'collect');
		$this->addPluginEntry('wiki', 'collect');

		$this->deleteModuleEntry('mod_collect');
	}
}