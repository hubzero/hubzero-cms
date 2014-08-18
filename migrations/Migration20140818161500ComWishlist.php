<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding indices to com_wishlist tables
 **/
class Migration20140818161500ComWishlist extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__wishlist_vote'))
		{
			$query = "ALTER TABLE `#__wishlist_vote`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `wishid` `wishid` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `userid` `userid` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `importance` `importance` INT(3)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `effort` `effort` INT(3)  NOT NULL  DEFAULT '0'
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableHasKey('#__wishlist_vote', 'jos_wishlist_vote_wishid_idx'))
			{
				$query = "ALTER TABLE `#__wishlist_vote` DROP INDEX `jos_wishlist_vote_wishid_idx`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wishlist_vote', 'idx_wishid'))
			{
				$query = "ALTER TABLE `#__wishlist_vote` ADD INDEX `idx_wishid` (`wishid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wishlist_vote', 'idx_userid'))
			{
				$query = "ALTER TABLE `#__wishlist_vote` ADD INDEX `idx_userid` (`userid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wishlist_owners'))
		{
			$query = "ALTER TABLE `#__wishlist_owners`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `wishlist` `wishlist` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `userid` `userid` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `type` `type` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__wishlist_owners', 'idx_wishlist'))
			{
				$query = "ALTER TABLE `#__wishlist_owners` ADD INDEX `idx_wishlist` (`wishlist`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wishlist_owners', 'idx_userid'))
			{
				$query = "ALTER TABLE `#__wishlist_owners` ADD INDEX `idx_userid` (`userid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wishlist_owners', 'idx_type'))
			{
				$query = "ALTER TABLE `#__wishlist_owners` ADD INDEX `idx_type` (`type`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wishlist_ownergroups'))
		{
			$query = "ALTER TABLE `#__wishlist_ownergroups`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `wishlist` `wishlist` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `groupid` `groupid` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__wishlist_ownergroups', 'idx_wishlist'))
			{
				$query = "ALTER TABLE `#__wishlist_ownergroups` ADD INDEX `idx_wishlist` (`wishlist`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wishlist_ownergroups', 'idx_groupid'))
			{
				$query = "ALTER TABLE `#__wishlist_ownergroups` ADD INDEX `idx_groupid` (`groupid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wishlist_implementation'))
		{
			if (!$this->db->tableHasKey('#__wishlist_implementation', 'idx_wishid'))
			{
				$query = "ALTER TABLE `#__wishlist_implementation` ADD INDEX `idx_wishid` (`wishid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wishlist_implementation', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__wishlist_implementation` ADD INDEX `idx_created_by` (`created_by`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wishlist_implementation', 'idx_approved'))
			{
				$query = "ALTER TABLE `#__wishlist_implementation` ADD INDEX `idx_approved` (`approved`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wishlist'))
		{
			if (!$this->db->tableHasKey('#__wishlist', 'idx_category_referenceid'))
			{
				$query = "ALTER TABLE `#__wishlist` ADD INDEX `idx_category_referenceid` (`category`, `referenceid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wishlist', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__wishlist` ADD INDEX `idx_created_by` (`created_by`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wishlist', 'idx_state'))
			{
				$query = "ALTER TABLE `#__wishlist` ADD INDEX `idx_state` (`state`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wish_attachments'))
		{
			if (!$this->db->tableHasKey('#__wish_attachments', 'idx_wish'))
			{
				$query = "ALTER TABLE `#__wish_attachments` ADD INDEX `idx_wish` (`wish`);";
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
		if ($this->db->tableExists('#__wishlist_vote'))
		{
			if ($this->db->tableHasKey('#__wishlist_vote', 'idx_wishid'))
			{
				$query = "ALTER TABLE `#__wishlist_vote` DROP INDEX `idx_wishid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__wishlist_vote', 'idx_userid'))
			{
				$query = "ALTER TABLE `#__wishlist_vote` DROP INDEX `idx_userid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wishlist_owner'))
		{
			if ($this->db->tableHasKey('#__wishlist_owner', 'idx_wishlist'))
			{
				$query = "ALTER TABLE `#__wishlist_owner` DROP INDEX `idx_wishlist`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__wishlist_owner', 'idx_userid'))
			{
				$query = "ALTER TABLE `#__wishlist_owner` DROP INDEX `idx_userid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__wishlist_owner', 'idx_type'))
			{
				$query = "ALTER TABLE `#__wishlist_owner` DROP INDEX `idx_type`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wishlist_ownergroups'))
		{
			if ($this->db->tableHasKey('#__wishlist_ownergroups', 'idx_wishlist'))
			{
				$query = "ALTER TABLE `#__wishlist_ownergroups` DROP INDEX `idx_wishlist`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__wishlist_ownergroups', 'idx_groupid'))
			{
				$query = "ALTER TABLE `#__wishlist_ownergroups` DROP INDEX `idx_groupid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wishlist_implementation'))
		{
			if ($this->db->tableHasKey('#__wishlist_implementation', 'idx_wishid'))
			{
				$query = "ALTER TABLE `#__wishlist_implementation` DROP INDEX `idx_wishid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__wishlist_implementation', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__wishlist_implementation` DROP INDEX `idx_created_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__wishlist_implementation', 'idx_approved'))
			{
				$query = "ALTER TABLE `#__wishlist_implementation` DROP INDEX `idx_approved`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wishlist'))
		{
			if ($this->db->tableHasKey('#__wishlist', 'idx_category_referenceid'))
			{
				$query = "ALTER TABLE `#__wishlist` DROP INDEX `idx_category_referenceid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__wishlist', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__wishlist` DROP INDEX `idx_created_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__wishlist', 'idx_state'))
			{
				$query = "ALTER TABLE `#__wishlist` DROP INDEX `idx_state`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wish_attachments'))
		{
			if ($this->db->tableHasKey('#__wish_attachments', 'idx_wish'))
			{
				$query = "ALTER TABLE `#__wish_attachments` DROP INDEX `idx_wish`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}