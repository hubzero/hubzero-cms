<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Project Info, renaming Project blog, updating ordering
 **/
class Migration20160907185952PlgProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('projects', 'info');

		if ($this->db->tableExists('#__extensions'))
		{
			$query = "UPDATE `#__extensions` SET `element`='feed' WHERE `folder`='projects' AND `element`='blog' AND `type`='plugin'";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "SELECT * FROM `#__extensions` WHERE `folder`='projects' AND `type`='plugin' ORDER BY `ordering` ASC";
			$this->db->setQuery($query);
			$plugins = $this->db->loadObjectList();

			$i = 1;
			foreach ($plugins as $plugin)
			{
				// Skip number 2
				if ($i == 2)
				{
					// Up it to number 3
					$i++;
				}

				$num = $i;
				// Force info to second place
				if ($plugin->element == 'info')
				{
					$num = 2;
				}

				$query = "UPDATE `#__extensions` SET `ordering`=" . $num . " WHERE `extension_id`=" . $plugin->extension_id;
				$this->db->setQuery($query);
				$this->db->query();

				$i++;
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('projects', 'info');

		if ($this->db->tableExists('#__extensions'))
		{
			$query = "UPDATE `#__extensions` SET `element`='blog' WHERE `folder`='projects' AND `element`='feed' AND `type`='plugin'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
