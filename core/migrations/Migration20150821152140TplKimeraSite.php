<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding site Kimera template
 **/
class Migration20150821152140TplKimeraSite extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `element` = 'kimera' AND `type`='template'";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if (!$id)
			{
				$this->addTemplateEntry('kimera', 'Kimera (site)', 0);

				$query = "UPDATE `#__extensions` SET `protected` = 1 WHERE `element` = 'kimera' AND `type`='template'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteTemplateEntry('kimera', 0);
	}
}