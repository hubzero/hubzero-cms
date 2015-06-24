<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing site Hubbasic template
 **/
class Migration20150624171211TplHubbasic extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__template_styles'))
		{
			$query = "SELECT `template` FROM `#__template_styles` WHERE `client_id`=0 AND `home`=1";
			$this->db->setQuery($query);
			if ($template = $this->db->loadResult())
			{
				if ($template == 'hubbasic')
				{
					$query = "UPDATE `#__template_styles` SET `home`=1 WHERE `client_id`=0 AND `template`=" . $this->db->quote('hubbasic2013');
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}

		$this->deleteTemplateEntry('hubbasic', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addTemplateEntry('hubbasic', 'Hubbasic', 0, 1, 0);
	}
}