<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for updating default tools plugin values
 **/
class Migration20160401212121PlgTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT * FROM `#__extensions` WHERE `element`=" . $this->db->quote('novnc') . " AND `folder`=" . $this->db->quote('tools');
			$this->db->setQuery($query);
			$result = $this->db->loadObject();

			if ($result && $result->extension_id)
			{
				$params = new \Hubzero\Config\Registry($result->params);
				$params->set('browsers', '*, safari 5.1
*, chrome 26.0
*, iceweasel 38.0
*, firefox 30.0
*, opera 23.0
*, mozilla 5.0
iOS, safari 1.0
Windows, msie 10.0
Windows, ie 10.0');

				$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote($params->toString()) . " WHERE `extension_id`=" . $this->db->quote($result->extension_id);
				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "SELECT * FROM `#__extensions` WHERE `element`=" . $this->db->quote('java') . " AND `folder`=" . $this->db->quote('tools');
			$this->db->setQuery($query);
			$result = $this->db->loadObject();

			if ($result && $result->extension_id)
			{
				$params = new \Hubzero\Config\Registry($result->params);
				$params->set('browsers', '*, chrome 999999.0
*, safari 1.0
*, iceweasel 1.0
*, firefox 1.0
*, opera 1.0
*, IE 3.0
*, mozilla 5.0
iOS, Safari 9999.9');

				$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote($params->toString()) . " WHERE `extension_id`=" . $this->db->quote($result->extension_id);
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
	}
}