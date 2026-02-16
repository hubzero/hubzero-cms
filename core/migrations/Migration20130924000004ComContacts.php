<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for contact details
**/
class Migration20130924000004ComContacts extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__contact_details')) {
            return;
        }
        $schema->setTableEngine('#__contact_details', 'MYISAM');

        // Add new columns (simple adds without position)
        if (!$schema->hasColumn('#__contact_details', 'sortname1')) {
            $schema->addColumn('#__contact_details', 'sortname1')
                ->string(255)
                ->notNull()
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'sortname2')) {
            $schema->addColumn('#__contact_details', 'sortname2')
                ->string(255)
                ->notNull()
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'sortname3')) {
            $schema->addColumn('#__contact_details', 'sortname3')
                ->string(255)
                ->notNull()
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'language')) {
            $schema->addColumn('#__contact_details', 'language')
                ->char(7)
                ->notNull()
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'created')) {
            $schema->addColumn('#__contact_details', 'created')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'created_by')) {
            $schema->addColumn('#__contact_details', 'created_by')
                ->integer(10)
                ->unsigned()
                ->notNull()
                ->default(0)
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'created_by_alias')) {
            $schema->addColumn('#__contact_details', 'created_by_alias')
                ->string(255)
                ->notNull()
                ->default('')
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'modified')) {
            $schema->addColumn('#__contact_details', 'modified')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'modified_by')) {
            $schema->addColumn('#__contact_details', 'modified_by')
                ->integer(10)
                ->unsigned()
                ->notNull()
                ->default(0)
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'metakey')) {
            $schema->addColumn('#__contact_details', 'metakey')
                ->text()
                ->notNull()
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'metadesc')) {
            $schema->addColumn('#__contact_details', 'metadesc')
                ->text()
                ->notNull()
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'metadata')) {
            $schema->addColumn('#__contact_details', 'metadata')
                ->text()
                ->notNull()
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'featured')) {
            $schema->addColumn('#__contact_details', 'featured')
                ->tinyInteger(3)
                ->unsigned()
                ->notNull()
                ->default(0)
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'xreference')) {
            $schema->addColumn('#__contact_details', 'xreference')
                ->string(50)
                ->notNull()
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'publish_up')) {
            $schema->addColumn('#__contact_details', 'publish_up')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->execute();
        }
        if (!$schema->hasColumn('#__contact_details', 'publish_down')) {
            $schema->addColumn('#__contact_details', 'publish_down')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->execute();
        }

        // Batch column modifications and index operations
        $schema->table('#__contact_details')->alter()
            ->modifyColumn('alias')->string(255)->notNull()->default('')
            ->modifyColumn('checked_out')->integer(10)->unsigned()->notNull()->default(0)
            ->modifyColumn('access')->integer(10)->unsigned()->notNull()->default(0)
            ->modifyColumn('published')->tinyInteger(1)->notNull()->default(0)
            ->dropIndex('catid')
            ->addIndex('idx_access', 'access')
            ->addIndex('idx_checkout', 'checked_out')
            ->addIndex('idx_state', 'published')
            ->addIndex('idx_catid', 'catid')
            ->addIndex('idx_createdby', 'created_by')
            ->addIndex('idx_language', 'language')
            ->addIndex('idx_xreference', 'xreference')
            ->execute();

        // Add composite index
        if (!$schema->hasKey('#__contact_details', 'idx_featured_catid')) {
            $schema->table('#__contact_details')->alter()
                ->addIndex('idx_featured_catid', ['featured', 'catid'])
                ->execute();
        }
    }
}
