<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing 'jos_' references
 **/
class Migration20141021161308Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__categories') && $this->db->tableHasField('#__categories', 'asset_id'))
		{
			$info = $this->db->getTableColumns('#__categories', false);

			if (strpos($info['asset_id']->Comment, 'jos_') !== false)
			{
				$query = "ALTER TABLE `#__categories` CHANGE COLUMN `asset_id` `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__content') && $this->db->tableHasField('#__content', 'asset_id'))
		{
			$info = $this->db->getTableColumns('#__content', false);

			if (strpos($info['asset_id']->Comment, 'jos_') !== false || strpos($info['asset_id']->Comment, '#_assets') !== false)
			{
				$query = "ALTER TABLE `#__content` CHANGE COLUMN `asset_id` `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__menu') && $this->db->tableHasField('#__menu', 'component_id'))
		{
			$info = $this->db->getTableColumns('#__menu', false);

			if (strpos($info['component_id']->Comment, 'jos_') !== false)
			{
				$query = "ALTER TABLE `#__menu` CHANGE COLUMN `component_id` `component_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to #__extensions.id'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__user_usergroup_map'))
		{
			$info = $this->db->getTableColumns('#__user_usergroup_map', false);

			if ($this->db->tableHasField('#__user_usergroup_map', 'user_id') && strpos($info['user_id']->Comment, 'jos_') !== false)
			{
				$query = "ALTER TABLE `#__user_usergroup_map` CHANGE COLUMN `user_id` `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__users.id'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__user_usergroup_map', 'group_id') && strpos($info['group_id']->Comment, 'jos_') !== false)
			{
				$query = "ALTER TABLE `#__user_usergroup_map` CHANGE COLUMN `group_id` `group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__usergroups.id'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}