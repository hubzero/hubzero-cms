<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indices to xmessage tables
**/
class Migration20140829131400PlgMembersMessages extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xmessage')) {
            // Standardize column types to INT UNSIGNED
            $schema->alterTable('#__xmessage')
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyInteger('group_id', true)->notNull()->default(0)
                ->modifyInteger('created_by', true)->notNull()->default(0)
                ->addIndex('idx_component', 'component')
                ->addIndex('idx_group_id', 'group_id')
                ->execute();
        }

        if ($schema->tableExists('#__xmessage_component')) {
            $schema->addIndex('#__xmessage_component', 'idx_component', 'component');
        }

        if ($schema->tableExists('#__xmessage_notify')) {
            // Standardize column types to INT UNSIGNED
            $schema->alterTable('#__xmessage_notify')
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyInteger('uid', true)->notNull()->default(0)
                ->addIndex('idx_uid', 'uid')
                ->addIndex('idx_method', 'method')
                ->execute();
        }

        if ($schema->tableExists('#__xmessage_seen')) {
            $schema->alterTable('#__xmessage_seen')
                ->dropIndex('uid')
                ->addIndex('idx_uid', 'uid')
                ->dropIndex('mid')
                ->addIndex('idx_mid', 'mid')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xmessage')) {
            $schema->dropIndex('#__xmessage', 'idx_component');

            $schema->dropIndex('#__xmessage', 'idx_group_id');
        }

        if ($schema->tableExists('#__xmessage_component')) {
            $schema->dropIndex('#__xmessage_component', 'idx_component');
        }

        if ($schema->tableExists('#__xmessage_notify')) {
            $schema->dropIndex('#__xmessage_notify', 'idx_uid');

            $schema->dropIndex('#__xmessage_notify', 'idx_method');
        }

        if ($schema->tableExists('#__xmessage_seen')) {
            $schema->dropIndex('#__xmessage_seen', 'idx_uid');

            $schema->addIndex('#__xmessage_seen', 'uid', 'uid');

            $schema->dropIndex('#__xmessage_seen', 'idx_mid');

            $schema->addIndex('#__xmessage_seen', 'mid', 'mid');
        }
    }
}
