<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indexes to #__publications tables
 **/
class Migration20170201125723ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publications'))
		{
			if ($this->db->tableHasField('#__publications', 'created_by') && !$this->db->tableHasKey('#__publications', 'idx_created_by'))
			{
				$query = "ALTER IGNORE TABLE `#__publications` ADD INDEX `idx_created_by` (`created_by`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publications', 'master_type') && !$this->db->tableHasKey('#__publications', 'idx_master_type'))
			{
				$query = "ALTER IGNORE TABLE `#__publications` ADD INDEX `idx_master_type` (`master_type`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publications', 'project_id') && !$this->db->tableHasKey('#__publications', 'idx_project_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publications` ADD INDEX `idx_project_id` (`project_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publications', 'category') && !$this->db->tableHasKey('#__publications', 'idx_category'))
			{
				$query = "ALTER IGNORE TABLE `#__publications` ADD INDEX `idx_category` (`category`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publications', 'group_owner') && !$this->db->tableHasKey('#__publications', 'idx_group_owner'))
			{
				$query = "ALTER IGNORE TABLE `#__publications` ADD INDEX `idx_group_owner` (`group_owner`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publications', 'access') && !$this->db->tableHasKey('#__publications', 'idx_access'))
			{
				$query = "ALTER IGNORE TABLE `#__publications` ADD INDEX `idx_access` (`access`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_versions'))
		{
			if ($this->db->tableHasField('#__publication_versions', 'publication_id') && !$this->db->tableHasKey('#__publication_versions', 'idx_publication_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_versions` ADD INDEX `idx_publication_id` (`publication_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_versions', 'main') && !$this->db->tableHasKey('#__publication_versions', 'idx_main'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_versions` ADD INDEX `idx_main` (`main`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_versions', 'state') && !$this->db->tableHasKey('#__publication_versions', 'idx_state'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_versions` ADD INDEX `idx_state` (`state`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_versions', 'created_by') && !$this->db->tableHasKey('#__publication_versions', 'idx_created_by'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_versions` ADD INDEX `idx_created_by` (`created_by`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_versions', 'version_number') && !$this->db->tableHasKey('#__publication_versions', 'idx_version_number'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_versions` ADD INDEX `idx_version_number` (`version_number`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_screenshots'))
		{
			if ($this->db->tableHasField('#__publication_screenshots', 'publication_id') && !$this->db->tableHasKey('#__publication_screenshots', 'idx_publication_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_screenshots` ADD INDEX `idx_publication_id` (`publication_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_screenshots', 'publication_version_id') && !$this->db->tableHasKey('#__publication_screenshots', 'idx_publication_version_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_screenshots` ADD INDEX `idx_publication_version_id` (`publication_version_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_ratings'))
		{
			if ($this->db->tableHasField('#__publication_ratings', 'publication_id') && !$this->db->tableHasKey('#__publication_ratings', 'idx_publication_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_ratings` ADD INDEX `idx_publication_id` (`publication_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_ratings', 'publication_version_id') && !$this->db->tableHasKey('#__publication_ratings', 'idx_publication_version_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_ratings` ADD INDEX `idx_publication_version_id` (`publication_version_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_ratings', 'state') && !$this->db->tableHasKey('#__publication_ratings', 'idx_state'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_ratings` ADD INDEX `idx_state` (`state`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_ratings', 'created_by') && !$this->db->tableHasKey('#__publication_ratings', 'idx_created_by'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_ratings` ADD INDEX `idx_created_by` (`created_by`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_master_types'))
		{
			if ($this->db->tableHasField('#__publication_master_types', 'contributable') && !$this->db->tableHasKey('#__publication_master_types', 'idx_contributable'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_master_types` ADD INDEX `idx_contributable` (`contributable`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_master_types', 'supporting') && !$this->db->tableHasKey('#__publication_master_types', 'idx_supporting'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_master_types` ADD INDEX `idx_supporting` (`supporting`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_logs'))
		{
			if ($this->db->tableHasField('#__publication_logs', 'publication_id') && !$this->db->tableHasKey('#__publication_logs', 'idx_publication_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_logs` ADD INDEX `idx_publication_id` (`publication_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_logs', 'publication_version_id') && !$this->db->tableHasKey('#__publication_logs', 'idx_publication_version_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_logs` ADD INDEX `idx_publication_version_id` (`publication_version_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_licenses'))
		{
			if ($this->db->tableHasField('#__publication_licenses', 'active') && !$this->db->tableHasKey('#__publication_licenses', 'idx_active'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_licenses` ADD INDEX `idx_active` (`active`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_licenses', 'main') && !$this->db->tableHasKey('#__publication_licenses', 'idx_main'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_licenses` ADD INDEX `idx_main` (`main`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_licenses', 'agreement') && !$this->db->tableHasKey('#__publication_licenses', 'idx_agreement'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_licenses` ADD INDEX `idx_agreement` (`agreement`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_licenses', 'customizable') && !$this->db->tableHasKey('#__publication_licenses', 'idx_customizable'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_licenses` ADD INDEX `idx_customizable` (`customizable`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_curation'))
		{
			if ($this->db->tableHasField('#__publication_curation', 'publication_id') && !$this->db->tableHasKey('#__publication_curation', 'idx_publication_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_curation` ADD INDEX `idx_publication_id` (`publication_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_curation', 'publication_version_id') && !$this->db->tableHasKey('#__publication_curation', 'idx_publication_version_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_curation` ADD INDEX `idx_publication_version_id` (`publication_version_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_authors'))
		{
			if ($this->db->tableHasField('#__publication_authors', 'publication_version_id') && !$this->db->tableHasKey('#__publication_authors', 'idx_publication_version_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_authors` ADD INDEX `idx_publication_version_id` (`publication_version_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_authors', 'user_id') && !$this->db->tableHasKey('#__publication_authors', 'idx_user_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_authors` ADD INDEX `idx_user_id` (`user_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_authors', 'project_owner_id') && !$this->db->tableHasKey('#__publication_authors', 'idx_project_owner_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_authors` ADD INDEX `idx_project_owner_id` (`project_owner_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_authors', 'status') && !$this->db->tableHasKey('#__publication_authors', 'idx_status'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_authors` ADD INDEX `idx_status` (`status`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_authors', 'repository_contact') && !$this->db->tableHasKey('#__publication_authors', 'idx_repository_contact'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_authors` ADD INDEX `idx_repository_contact` (`repository_contact`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_attachments'))
		{
			if ($this->db->tableHasField('#__publication_attachments', 'publication_id') && !$this->db->tableHasKey('#__publication_attachments', 'idx_publication_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_attachments` ADD INDEX `idx_publication_id` (`publication_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__publication_attachments', 'publication_version_id') && !$this->db->tableHasKey('#__publication_attachments', 'idx_publication_version_id'))
			{
				$query = "ALTER IGNORE TABLE `#__publication_attachments` ADD INDEX `idx_publication_version_id` (`publication_version_id`)";
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
		if ($this->db->tableExists('#__publications'))
		{
			if ($this->db->tableHasKey('#__publications', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__publications` DROP KEY `idx_created_by`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publications', 'idx_master_type'))
			{
				$query = "ALTER TABLE `#__publications` DROP KEY `idx_master_type`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publications', 'idx_project_id'))
			{
				$query = "ALTER TABLE `#__publications` DROP KEY `idx_project_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publications', 'idx_category'))
			{
				$query = "ALTER TABLE `#__publications` DROP KEY `idx_category`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publications', 'idx_group_owner'))
			{
				$query = "ALTER TABLE `#__publications` DROP KEY `idx_group_owner`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publications', 'idx_access'))
			{
				$query = "ALTER TABLE `#__publications` DROP KEY `idx_access`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_versions'))
		{
			if ($this->db->tableHasKey('#__publication_versions', 'idx_publication_id'))
			{
				$query = "ALTER TABLE `#__publication_versions` DROP KEY `idx_publication_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_versions', 'idx_main'))
			{
				$query = "ALTER TABLE `#__publication_versions` DROP KEY `idx_main`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_versions', 'idx_state'))
			{
				$query = "ALTER TABLE `#__publication_versions` DROP KEY `idx_state`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_versions', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__publication_versions` DROP KEY `idx_created_by`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_versions', 'idx_version_number'))
			{
				$query = "ALTER TABLE `#__publication_versions` DROP KEY `idx_version_number`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_screenshots'))
		{
			if ($this->db->tableHasKey('#__publication_screenshots', 'idx_publication_id'))
			{
				$query = "ALTER TABLE `#__publication_screenshots` DROP KEY `idx_publication_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_screenshots', 'idx_publication_version_id'))
			{
				$query = "ALTER TABLE `#__publication_screenshots` DROP KEY `idx_publication_version_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_ratings'))
		{
			if ($this->db->tableHasKey('#__publication_ratings', 'idx_publication_id'))
			{
				$query = "ALTER TABLE `#__publication_ratings` DROP KEY `idx_publication_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_ratings', 'idx_publication_version_id'))
			{
				$query = "ALTER TABLE `#__publication_ratings` DROP KEY `idx_publication_version_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_ratings', 'idx_state'))
			{
				$query = "ALTER TABLE `#__publication_ratings` DROP KEY `idx_state`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_ratings', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__publication_ratings` DROP KEY `idx_created_by`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_master_types'))
		{
			if ($this->db->tableHasKey('#__publication_master_types', 'idx_contributable'))
			{
				$query = "ALTER TABLE `#__publication_master_types` DROP KEY `idx_contributable`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_master_types', 'idx_supporting'))
			{
				$query = "ALTER TABLE `#__publication_master_types` DROP KEY `idx_supporting`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_logs'))
		{
			if ($this->db->tableHasKey('#__publication_logs', 'idx_publication_id'))
			{
				$query = "ALTER TABLE `#__publication_logs` DROP KEY `idx_publication_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_logs', 'idx_publication_version_id'))
			{
				$query = "ALTER TABLE `#__publication_logs` DROP KEY `idx_publication_version_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_licenses'))
		{
			if ($this->db->tableHasKey('#__publication_licenses', 'idx_active'))
			{
				$query = "ALTER TABLE `#__publication_licenses` DROP KEY `idx_active`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_licenses', 'idx_main'))
			{
				$query = "ALTER TABLE `#__publication_licenses` DROP KEY `idx_main`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_licenses', 'idx_agreement'))
			{
				$query = "ALTER TABLE `#__publication_licenses` DROP KEY `idx_agreement`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_licenses', 'idx_customizable'))
			{
				$query = "ALTER TABLE `#__publication_licenses` DROP KEY `idx_customizable`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_curation'))
		{
			if ($this->db->tableHasKey('#__publication_curation', 'idx_publication_id'))
			{
				$query = "ALTER TABLE `#__publication_curation` DROP KEY `idx_publication_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_curation', 'idx_publication_version_id'))
			{
				$query = "ALTER TABLE `#__publication_curation` DROP KEY `idx_publication_version_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_authors'))
		{
			if ($this->db->tableHasKey('#__publication_authors', 'idx_publication_version_id'))
			{
				$query = "ALTER TABLE `#__publication_authors` DROP KEY `idx_publication_version_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_authors', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__publication_authors` DROP KEY `idx_user_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_authors', 'idx_project_owner_id'))
			{
				$query = "ALTER TABLE `#__publication_authors` DROP KEY `idx_project_owner_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_authors', 'idx_status'))
			{
				$query = "ALTER TABLE `#__publication_authors` DROP KEY `idx_status`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_authors', 'idx_repository_contact'))
			{
				$query = "ALTER TABLE `#__publication_authors` DROP KEY `idx_repository_contact`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__publication_attachments'))
		{
			if ($this->db->tableHasKey('#__publication_attachments', 'idx_publication_id'))
			{
				$query = "ALTER TABLE `#__publication_attachments` DROP KEY `idx_publication_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__publication_attachments', 'idx_publication_version_id'))
			{
				$query = "ALTER TABLE `#__publication_attachments` DROP KEY `idx_publication_version_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
