<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to update file handler base path
 **/
class Migration20170822114718PlgProjectsFiles extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT * FROM `#__extensions` WHERE `folder`='projects' AND `element`='files'";
			$this->db->setQuery($query);
			$row = $this->db->loadObject();

			if ($row && $row->params)
			{
				$params = json_decode($row->params);
				if ($params && isset($params->handler_base_path) && $params->handler_base_path != '/srv/projects/')
				{
					$params->handler_base_path = rtrim($params->handler_base_path, '/') . '/{project}/{file}';

					$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote(json_encode($params)) . " WHERE `extension_id`=" . $row->extension_id;
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
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT * FROM `#__extensions` WHERE `folder`='projects' AND `element`='files'";
			$this->db->setQuery($query);
			$row = $this->db->loadObject();

			if ($row && $row->params)
			{
				$params = json_decode($row->params);
				if ($params && isset($params->handler_base_path) && strstr($params->handler_base_path, '{'))
				{
					$params->handler_base_path = str_replace('{project}/{file}', '', $params->handler_base_path);

					$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote(json_encode($params)) . " WHERE `extension_id`=" . $row->extension_id;
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}
