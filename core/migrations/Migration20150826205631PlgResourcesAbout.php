<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for for adding default plg_about and plg_abouttool parameters for tool resource type
 **/
class Migration20150826205631PlgResourcesAbout extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resource_types'))
		{
			$this->db->setQuery("SELECT id, params FROM `#__resource_types` WHERE `category`=27 and `alias`='tools'");

			$records = $this->db->loadObjectList();

			foreach ($records as $record)
			{
				$params = $record->params;

				$matches=null;

				if (preg_match("/^\s*{/", $params, $matches))
				{
					// Looks like a json format, ignore entry.
					continue;
				}

				if (!preg_match("/plg_about\s*=/", $params))
				{
					$params .= "\nplg_about=0";
				}

				if (!preg_match("/plg_abouttool\s*=/", $params))
				{
					$params .= "\nplg_abouttool=1";
				}

				if ($params != $record->params)
				{
					$this->db->setQuery("UPDATE `#__resource_types` SET params=" . $this->db->quote($params) . " WHERE `id`=" . $this->db->quote($record->id) . ";");
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
	}
}
