<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for updating menu items to point to com_login
 **/
class Migration20190505000000ComLogin extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions') && $this->db->tableExists('#__menu'))
		{
			$query = "SELECT extension_id FROM `#__extensions` WHERE `element`='com_login'";
			$this->db->setQuery($query);
			$extension_id = $this->db->loadResult();

			if ($extension_id)
			{
				// Login link
				$query = "SELECT * FROM `#__menu` WHERE `link`='index.php?option=com_users&view=login'";
				$this->db->setQuery($query);
				$menu = $this->db->loadObject();

				if ($menu && $menu->id)
				{
					$link = 'index.php?option=com_login&view=login';

					$query = "UPDATE `#__menu` SET `link`=" . $this->db->quote($link) . ", `component_id`=" . $this->db->quote($extension_id) . " WHERE `id`=" . $this->db->quote($menu->id);
					$this->db->setQuery($query);
					if ($this->db->query())
					{
						$this->log('Updated login menu link to use `com_login`');
					}
					else
					{
						$this->log($query, 'warning');
					}
				}

				// Logout link
				$query = "SELECT * FROM `#__menu` WHERE `link`='index.php?option=com_users&view=logout'";
				$this->db->setQuery($query);
				$menu = $this->db->loadObject();

				if ($menu && $menu->id)
				{
					$link = 'index.php?option=com_login&view=logout';

					$query = "UPDATE `#__menu` SET `link`=" . $this->db->quote($link) . ", `component_id`=" . $this->db->quote($extension_id) . " WHERE `id`=" . $this->db->quote($menu->id);
					$this->db->setQuery($query);
					if ($this->db->query())
					{
						$this->log('Updated logout menu link to use `com_login`');
					}
					else
					{
						$this->log($query, 'warning');
					}
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__extensions') && $this->db->tableExists('#__menu'))
		{
			$query = "SELECT extension_id FROM `#__extensions` WHERE `element`='com_users'";
			$this->db->setQuery($query);
			$extension_id = $this->db->loadResult();

			if ($extension_id)
			{
				// Login link
				$query = "SELECT * FROM `#__menu` WHERE `link`='index.php?option=com_login' OR `link`='index.php?option=com_login&view=login'";
				$this->db->setQuery($query);
				$menu = $this->db->loadObject();

				if ($menu && $menu->id)
				{
					$link = 'index.php?option=com_users&view=login';

					$query = "UPDATE `#__menu` SET `link`=" . $this->db->quote($link) . ", `component_id`=" . $this->db->quote($extension_id) . " WHERE `id`=" . $this->db->quote($menu->id);
					$this->db->setQuery($query);

					if ($this->db->query())
					{
						$this->log('Updated login menu link to use `com_users`');
					}
					else
					{
						$this->log($query, 'warning');
					}
				}

				// Logout link
				$query = "SELECT * FROM `#__menu` WHERE `link`='index.php?option=com_login&task=logout' OR `link`='index.php?option=com_login&view=logout'";
				$this->db->setQuery($query);
				$menu = $this->db->loadObject();

				if ($menu && $menu->id)
				{
					$link = 'index.php?option=com_users&view=logout';

					$query = "UPDATE `#__menu` SET `link`=" . $this->db->quote($link) . ", `component_id`=" . $this->db->quote($extension_id) . " WHERE `id`=" . $this->db->quote($menu->id);
					$this->db->setQuery($query);

					if ($this->db->query())
					{
						$this->log('Updated logout menu link to use `com_users`');
					}
					else
					{
						$this->log($query, 'warning');
					}
				}
			}
		}
	}
}
