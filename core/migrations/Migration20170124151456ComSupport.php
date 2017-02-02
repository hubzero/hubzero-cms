<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding MP4 extension to params
 **/
class Migration20170124151456ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT params FROM #__extensions WHERE name = 'com_support';";
			$this->db->setQuery($query);
			$params = $this->db->query()->loadResult();
			$params = json_decode($params);
			$fileExt = explode(",", $params->file_ext);

			// Prevent duplicates
			if (!in_array('mp4', $fileExt))
			{
				array_push($fileExt, 'mp4');
			}

			$params->file_ext = implode(",", $fileExt);
		  $params = json_encode($params);

			$query2 = "UPDATE `#__extensions` SET params=" . $this->db->quote($params) . " WHERE name='com_support';";
			$this->db->setQuery($query2);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "SELECT params FROM #__extensions WHERE name = 'com_support';";
		$this->db->setQuery($query);

		$params = $this->db->query()->loadResult();
		$params = json_decode($params);

		$fileExt = explode(",", $params->file_ext);
		$index = array_search('mp4', $fileExt);

		// Prevents invalid array access
		if ($index !== false)
		{
			unset($fileExt[$index]);
		}

		$params->file_ext = implode(",", $fileExt);
		$params = json_encode($params);

		$query2 = "UPDATE `#__extensions` SET params=" . $this->db->quote($params) . " WHERE name='com_support';";
		$this->db->setQuery($query2);
		$this->db->query();
	}
}
