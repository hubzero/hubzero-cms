<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes to #__publications tables
 *
*/
class Migration20170201125723ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publications')) {
            $schema->addIndex('#__publications', 'idx_created_by', 'created_by');

            $schema->addIndex('#__publications', 'idx_master_type', 'master_type');

            $schema->addIndex('#__publications', 'idx_project_id', 'project_id');

            $schema->addIndex('#__publications', 'idx_category', 'category');

            $schema->addIndex('#__publications', 'idx_group_owner', 'group_owner');

            $schema->addIndex('#__publications', 'idx_access', 'access');
        }

        if ($schema->tableExists('#__publication_versions')) {
            $schema->addIndex('#__publication_versions', 'idx_publication_id', 'publication_id');

            $schema->addIndex('#__publication_versions', 'idx_main', 'main');

            $schema->addIndex('#__publication_versions', 'idx_state', 'state');

            $schema->addIndex('#__publication_versions', 'idx_created_by', 'created_by');

            $schema->addIndex('#__publication_versions', 'idx_version_number', 'version_number');
        }

        if ($schema->tableExists('#__publication_screenshots')) {
            $schema->addIndex('#__publication_screenshots', 'idx_publication_id', 'publication_id');

            $schema->addIndex('#__publication_screenshots', 'idx_publication_version_id', 'publication_version_id');
        }

        if ($schema->tableExists('#__publication_ratings')) {
            $schema->addIndex('#__publication_ratings', 'idx_publication_id', 'publication_id');

            $schema->addIndex('#__publication_ratings', 'idx_publication_version_id', 'publication_version_id');

            $schema->addIndex('#__publication_ratings', 'idx_state', 'state');

            $schema->addIndex('#__publication_ratings', 'idx_created_by', 'created_by');
        }

        if ($schema->tableExists('#__publication_master_types')) {
            $schema->addIndex('#__publication_master_types', 'idx_contributable', 'contributable');

            $schema->addIndex('#__publication_master_types', 'idx_supporting', 'supporting');
        }

        if ($schema->tableExists('#__publication_logs')) {
            $schema->addIndex('#__publication_logs', 'idx_publication_id', 'publication_id');

            $schema->addIndex('#__publication_logs', 'idx_publication_version_id', 'publication_version_id');
        }

        if ($schema->tableExists('#__publication_licenses')) {
            $schema->addIndex('#__publication_licenses', 'idx_active', 'active');

            $schema->addIndex('#__publication_licenses', 'idx_main', 'main');

            $schema->addIndex('#__publication_licenses', 'idx_agreement', 'agreement');

            $schema->addIndex('#__publication_licenses', 'idx_customizable', 'customizable');
        }

        if ($schema->tableExists('#__publication_curation')) {
            $schema->addIndex('#__publication_curation', 'idx_publication_id', 'publication_id');

            $schema->addIndex('#__publication_curation', 'idx_publication_version_id', 'publication_version_id');
        }

        if ($schema->tableExists('#__publication_authors')) {
            $schema->addIndex('#__publication_authors', 'idx_publication_version_id', 'publication_version_id');

            $schema->addIndex('#__publication_authors', 'idx_user_id', 'user_id');

            $schema->addIndex('#__publication_authors', 'idx_project_owner_id', 'project_owner_id');

            $schema->addIndex('#__publication_authors', 'idx_status', 'status');

            $schema->addIndex('#__publication_authors', 'idx_repository_contact', 'repository_contact');
        }

        if ($schema->tableExists('#__publication_attachments')) {
            $schema->addIndex('#__publication_attachments', 'idx_publication_id', 'publication_id');

            $schema->addIndex('#__publication_attachments', 'idx_publication_version_id', 'publication_version_id');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publications')) {
            $schema->dropIndex('#__publications', 'idx_created_by');

            $schema->dropIndex('#__publications', 'idx_master_type');

            $schema->dropIndex('#__publications', 'idx_project_id');

            $schema->dropIndex('#__publications', 'idx_category');

            $schema->dropIndex('#__publications', 'idx_group_owner');

            $schema->dropIndex('#__publications', 'idx_access');
        }

        if ($schema->tableExists('#__publication_versions')) {
            $schema->dropIndex('#__publication_versions', 'idx_publication_id');

            $schema->dropIndex('#__publication_versions', 'idx_main');

            $schema->dropIndex('#__publication_versions', 'idx_state');

            $schema->dropIndex('#__publication_versions', 'idx_created_by');

            $schema->dropIndex('#__publication_versions', 'idx_version_number');
        }

        if ($schema->tableExists('#__publication_screenshots')) {
            $schema->dropIndex('#__publication_screenshots', 'idx_publication_id');

            $schema->dropIndex('#__publication_screenshots', 'idx_publication_version_id');
        }

        if ($schema->tableExists('#__publication_ratings')) {
            $schema->dropIndex('#__publication_ratings', 'idx_publication_id');

            $schema->dropIndex('#__publication_ratings', 'idx_publication_version_id');

            $schema->dropIndex('#__publication_ratings', 'idx_state');

            $schema->dropIndex('#__publication_ratings', 'idx_created_by');
        }

        if ($schema->tableExists('#__publication_master_types')) {
            $schema->dropIndex('#__publication_master_types', 'idx_contributable');

            $schema->dropIndex('#__publication_master_types', 'idx_supporting');
        }

        if ($schema->tableExists('#__publication_logs')) {
            $schema->dropIndex('#__publication_logs', 'idx_publication_id');

            $schema->dropIndex('#__publication_logs', 'idx_publication_version_id');
        }

        if ($schema->tableExists('#__publication_licenses')) {
            $schema->dropIndex('#__publication_licenses', 'idx_active');

            $schema->dropIndex('#__publication_licenses', 'idx_main');

            $schema->dropIndex('#__publication_licenses', 'idx_agreement');

            $schema->dropIndex('#__publication_licenses', 'idx_customizable');
        }

        if ($schema->tableExists('#__publication_curation')) {
            $schema->dropIndex('#__publication_curation', 'idx_publication_id');

            $schema->dropIndex('#__publication_curation', 'idx_publication_version_id');
        }

        if ($schema->tableExists('#__publication_authors')) {
            $schema->dropIndex('#__publication_authors', 'idx_publication_version_id');

            $schema->dropIndex('#__publication_authors', 'idx_user_id');

            $schema->dropIndex('#__publication_authors', 'idx_project_owner_id');

            $schema->dropIndex('#__publication_authors', 'idx_status');

            $schema->dropIndex('#__publication_authors', 'idx_repository_contact');
        }

        if ($schema->tableExists('#__publication_attachments')) {
            $schema->dropIndex('#__publication_attachments', 'idx_publication_id');

            $schema->dropIndex('#__publication_attachments', 'idx_publication_version_id');
        }
    }
}
