<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for deleting com_search
 **/
class Migration20140113135231ComSearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_search' AND `protected`=1;";

		$this->db->setQuery($query);

		if ($id = $this->db->loadResult())
		{
			$this->deleteComponentEntry('search');

			$this->deletePluginEntry('search');

			$query = "UPDATE `#__extensions` SET `element`='com_search', `name`='Search' WHERE `type`='component' AND `element`='com_ysearch';";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__menu` SET `title`='com_search', `alias`='search', `path`='search', `link`='index.php?option=com_search&task=configure' WHERE `title`='com_ysearch';";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__extensions` SET `folder`='search' WHERE `folder`='ysearch' AND `type`='plugin';";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "SELECT `extension_id`, `name`, `element`, `folder` FROM `#__extensions` WHERE `type`='plugin' AND `folder`='search';";
			$this->db->setQuery($query);
			if ($results = $this->db->loadObjectList())
			{
				foreach ($results as $result)
				{
					$query = "UPDATE `#__extensions` SET `name`=" . $this->db->quote('plg_' . $result->folder . '_' . $result->element) . " WHERE `extension_id`=" . $this->db->quote($result->extension_id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_search' AND `protected`=0;";

		$this->db->setQuery($query);

		if ($id = $this->db->loadResult())
		{
			$query = "UPDATE `#__extensions` SET `element`='com_ysearch', `name`='YSearch' WHERE `type`='component' AND `element`='com_search' AND `protected`=0;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__extensions` SET `folder`='ysearch' WHERE `folder`='search' AND `type`='plugin';";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__menu` SET `title`='com_ysearch', `alias`='ysearch', `path`='ysearch', `link`='index.php?option=com_ysearch&task=configure' WHERE `title`='com_search';";
			$this->db->setQuery($query);
			$this->db->query();

			$this->addComponentEntry('search');

			$query = "UPDATE `#__extensions` SET `protected`=1 WHERE `type`='component' AND `element`='com_search';";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "SELECT `extension_id`, `name`, `element`, `folder` FROM `#__extensions` WHERE `type`='plugin' AND `folder`='ysearch';";
			$this->db->setQuery($query);
			if ($results = $this->db->loadObjectList())
			{
				foreach ($results as $result)
				{
					$query = "UPDATE `#__extensions` SET `name`=" . $this->db->quote('plg_' . $result->folder . '_' . $result->element) . " WHERE `extension_id`=" . $this->db->quote($result->extension_id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}