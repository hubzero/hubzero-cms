<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding User - HUBzero plugin
 **/
class Migration20180109000000PlgUserHubzero extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$id = null;

		// Check for plg_user_joomla first
		// If the record exists, update it so we can preserve any params, state, etc.
		if ($this->db->tableExists('#__extensions'))
		{
			$this->db->setQuery("SELECT extension_id FROM `#__extensions` WHERE `type`='plugin' AND `folder`='user' AND `element`='joomla'");
			$id = $this->db->loadResult();
		}

		if ($id)
		{
			$this->db->setQuery("UPDATE `#__extensions` SET `element`='hubzero', `name`='plg_user_hubzero' WHERE `extension_id`=" . $id);
			$this->db->query();
		}
		else
		{
			$this->addPluginEntry('user', 'hubzero');
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('user', 'hubzero');
	}
}
