<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to move existing antispam plugins if they haven't been already (fixing bad migration)
 **/
class Migration20150720105039PlgAntispam extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$params = '';

		if ($this->db->tableExists('#__extensions'))
		{
			// Move the existing plugin entries when possible to preserve params,
			// otherwise add the entry
			foreach (array('akismet', 'mollom', 'spamassassin') as $plg)
			{
				$this->db->setQuery("SELECT extension_id FROM `#__extensions` WHERE `type`='plugin' AND `element`=" . $this->db->quote($plg));
				if ($id = $this->db->loadResult())
				{
					$this->db->setQuery("UPDATE `#__extensions` SET `folder`='antispam', `name`=" . $this->db->quote('plg_antispam_' . $plg) . " WHERE `extension_id`=" . $this->db->quote($id));
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
			// Move the existing plugin entries when possible to preserve params,
			// otherwise add the entry
			foreach (array('akismet', 'mollom', 'spamassassin') as $plg)
			{
				$this->db->setQuery("SELECT extension_id FROM `#__extensions` WHERE `type`='plugin' AND `folder`='antispam' AND `element`=" . $this->db->quote($plg));
				if ($id = $this->db->loadResult())
				{
					$this->db->setQuery("UPDATE `#__extensions` SET `folder`='content', `name`=" . $this->db->quote('plg_content_' . $plg) . " WHERE `extension_id`=" . $this->db->quote($id));
					$this->db->query();
				}
			}
		}
	}
}