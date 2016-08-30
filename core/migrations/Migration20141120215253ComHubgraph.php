<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for cleaning up hubgraph extension entry
 **/
class Migration20141120215253ComHubgraph extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$params = array(
			"host"           => "unix:///var/run/hubgraph-server.sock",
			"port"           => null,
			"showTagCloud"   => true,
			"enabledOptions" => ""
		);
		$this->addComponentEntry('Hubgraph', 'com_hubgraph', 1, $params, false);

		if ($this->db->tableExists('#__extensions'))
		{
			// Look for multiple entries
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `element` = 'com_hubgraph' ORDER BY `extension_id` ASC";
			$this->db->setQuery($query);
			$ids = $this->db->loadColumn();

			if ($ids && count($ids) > 1)
			{
				unset($ids[0]);

				foreach ($ids as $id)
				{
					$query = "DELETE FROM `#__extensions` WHERE `extension_id` = " . (int)$id;
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			// Look for non-json params
			$params = $this->getParams('com_hubgraph', true);

			if (is_null(json_decode($params)))
			{
				$object = unserialize($params);
				$params = $object->settings();

				$query = "UPDATE `#__extensions` SET `params` = " . $this->db->quote(json_encode($params)) . " WHERE `element` = 'com_hubgraph'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}

// Placeholder class needed to parse serialized object/params previously stored in extensions directory
class HubgraphConfiguration
{
	private $settings, $idx;

	function settings()
	{
		return $this->settings;
	}
}