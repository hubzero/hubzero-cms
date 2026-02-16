<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for weblinks table changes
 *
*/
class Migration20130924000014ComWeblinks extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__weblinks')) {
            return;
        }
        // modifyInteger handles AUTO_INCREMENT appropriately for each database
        $schema->modifyColumn('#__weblinks', 'id')->integer()->notNull()->autoIncrement()->execute();

        // Rename column if needed
        if ($schema->hasColumn('#__weblinks', 'published') && !$schema->hasColumn('#__weblinks', 'state')) {
            $schema->renameColumn('published', 'state')->tinyInteger()->notNull()->default(0);
        }

        // Add column with position (MySQL-specific)
        if (!$schema->hasColumn('#__weblinks', 'access') && $schema->hasColumn('#__weblinks', 'approved')) {
            $schema->addColumn('access')->integer()->notNull()->default(1)->after('approved');
        }

        // Add new columns
        if (!$schema->hasColumn('#__weblinks', 'language')) {
            $schema->addColumn('language')->char(7)->notNull()->default('');
        }
        if (!$schema->hasColumn('#__weblinks', 'created')) {
            $schema->addColumn('created')->datetime()->notNull()->default('0000-00-00 00:00:00');
        }
        if (!$schema->hasColumn('#__weblinks', 'created_by')) {
            $schema->addColumn('created_by')->integer()->unsigned()->notNull()->default(0);
        }
        if (!$schema->hasColumn('#__weblinks', 'created_by_alias')) {
            $schema->addColumn('created_by_alias')->string(255)->notNull()->default('');
        }
        if (!$schema->hasColumn('#__weblinks', 'modified')) {
            $schema->addColumn('modified')->datetime()->notNull()->default('0000-00-00 00:00:00');
        }
        if (!$schema->hasColumn('#__weblinks', 'modified_by')) {
            $schema->addColumn('modified_by')->integer()->unsigned()->notNull()->default(0);
        }
        if (!$schema->hasColumn('#__weblinks', 'metakey')) {
            $schema->addColumn('metakey')->text()->notNull();
        }
        if (!$schema->hasColumn('#__weblinks', 'metadesc')) {
            $schema->addColumn('metadesc')->text()->notNull();
        }
        if (!$schema->hasColumn('#__weblinks', 'metadata')) {
            $schema->addColumn('metadata')->text()->notNull();
        }
        if (!$schema->hasColumn('#__weblinks', 'featured')) {
            $schema->addColumn('featured')->tinyInteger()->unsigned()->notNull()->default(0);
        }
        if (!$schema->hasColumn('#__weblinks', 'xreference')) {
            $schema->addColumn('xreference')->string(50)->notNull();
        }
        if (!$schema->hasColumn('#__weblinks', 'publish_up')) {
            $schema->addColumn('publish_up')->datetime()->notNull()->default('0000-00-00 00:00:00');
        }
        if (!$schema->hasColumn('#__weblinks', 'publish_down')) {
            $schema->addColumn('publish_down')->datetime()->notNull()->default('0000-00-00 00:00:00');
        }

        // Batch column modification and index operations
        $schema->alterTable('#__weblinks')
            ->modifyColumn('alias')->string(255)->notNull()->default('')
            ->dropIndex('catid')
            ->addIndex('idx_access', 'access')
            ->addIndex('idx_checkout', 'checked_out')
            ->addIndex('idx_state', 'state')
            ->addIndex('idx_catid', 'catid')
            ->addIndex('idx_createdby', 'created_by')
            ->addIndex('idx_language', 'language')
            ->addIndex('idx_xreference', 'xreference')
            ->execute();

        // Add composite index
        if ($schema->hasColumn('#__weblinks', 'featured') && $schema->hasColumn('#__weblinks', 'catid')) {
            $schema->addIndex('#__weblinks', 'idx_featured_catid', ['featured', 'catid']);
        }
    }
}
