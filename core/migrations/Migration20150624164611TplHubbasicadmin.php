<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing site Hubbasicadmin template
 **/
class Migration20150624164611TplHubbasicadmin extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__template_styles'))
		{
			$query = "SELECT `template` FROM `#__template_styles` WHERE `client_id`=1 AND `home`=1";
			$this->db->setQuery($query);
			if ($template = $this->db->loadResult())
			{
				if ($template == 'hubbasicadmin')
				{
					$query = "UPDATE `#__template_styles` SET `home`=1 WHERE `client_id`=1 AND `template`=" . $this->db->quote('kameleon');
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}

		$this->deleteTemplateEntry('hubbasicadmin', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addTemplateEntry('hubbasicadmin', 'Hubbasicadmin', 1, 1, 0);
	}
}