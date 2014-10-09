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
				$this->installModule('collect', $position);
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