<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for updating menu items to point to com_members
 **/
class Migration20190327000000ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions') && $this->db->tableExists('#__menu'))
		{
			$query = "SELECT extension_id FROM `#__extensions` WHERE `element`='com_members'";
			$this->db->setQuery($query);
			$extension_id = $this->db->loadResult();

			if ($extension_id)
			{
				// Reset link
				$query = "SELECT * FROM `#__menu` WHERE `link`='index.php?option=com_users&view=reset' LIMIT 1";
				$this->db->setQuery($query);
				$menu = $this->db->loadObject();

				if ($menu && $menu->id)
				{
					$link = 'index.php?option=com_members&view=credentials&layout=reset';

					$query = "UPDATE `#__menu` SET `link`=" . $this->db->quote($link) . ", `component_id`=" . $this->db->quote($extension_id) . " WHERE `id`=" . $this->db->quote($menu->id);
					$this->db->setQuery($query);
					if ($this->db->query())
					{
						$this->log('Updated reset menu link to use `com_members`');
					}
					else
					{
						$this->log($query, 'warning');
					}
				}

				// Remind link
				$query = "SELECT * FROM `#__menu` WHERE `link`='index.php?option=com_users&view=remind' LIMIT 1";
				$this->db->setQuery($query);
				$menu = $this->db->loadObject();

				if ($menu && $menu->id)
				{
					$link = 'index.php?option=com_members&view=credentials&layout=remind';

					$query = "UPDATE `#__menu` SET `link`=" . $this->db->quote($link) . ", `component_id`=" . $this->db->quote($extension_id) . " WHERE `id`=" . $this->db->quote($menu->id);
					$this->db->setQuery($query);
					if ($this->db->query())
					{
						$this->log('Updated remind menu link to use `com_members`');
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
				// Reset link
				$query = "SELECT * FROM `#__menu` WHERE `link`='index.php?option=com_members&view=credentials&layout=reset'
						OR `link`='index.php?option=com_members&controller=credentials&task=reset'
						OR `link`='index.php?option=com_members&task=reset'
						LIMIT 1";
				$this->db->setQuery($query);
				$menu = $this->db->loadObject();

				if ($menu && $menu->id)
				{
					$link = 'index.php?option=com_users&view=reset';

					$query = "UPDATE `#__menu` SET `link`=" . $this->db->quote($link) . ", `component_id`=" . $this->db->quote($extension_id) . " WHERE `id`=" . $this->db->quote($menu->id);
					$this->db->setQuery($query);

					$this->log('Updated reset menu link to use `com_users`');
				}

				// Remind link
				$query = "SELECT * FROM `#__menu` WHERE `link`='index.php?option=com_members&view=credentials&layout=remind'
							OR `link`='index.php?option=com_members&controller=credentials&task=remind'
							OR `link`='index.php?option=com_members&task=remind'
							LIMIT 1";
				$this->db->setQuery($query);
				$menu = $this->db->loadObject();

				if ($menu && $menu->id)
				{
					$link = 'index.php?option=com_users&view=remind';

					$query = "UPDATE `#__menu` SET `link`=" . $this->db->quote($link) . ", `component_id`=" . $this->db->quote($extension_id) . " WHERE `id`=" . $this->db->quote($menu->id);
					$this->db->setQuery($query);

					$this->log('Updated remind menu link to use `com_users`');
				}
			}
		}
	}
}
