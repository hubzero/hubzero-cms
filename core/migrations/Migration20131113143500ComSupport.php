<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indices and setting default field value
**/
class Migration20131113143500ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_acl_acos')) {
            $schema->alterTable('#__support_acl_acos')
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyString('model', 100)->notNull()->default('')
                ->modifyInteger('foreign_key')->notNull()->default(0)
                ->execute();
        }

        if ($schema->tableExists('#__support_acl_aros')) {
            $schema->alterTable('#__support_acl_aros')
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyInteger('foreign_key')->notNull()->default(0)
                ->modifyString('alias', 255)->notNull()->default('')
                ->modifyString('model', 100)->notNull()->default('')
                ->addIndex('idx_model_foreign_key', ['model', 'foreign_key'])
                ->execute();
        }

        if ($schema->tableExists('#__support_acl_aros_acos')) {
            $schema->alterTable('#__support_acl_aros_acos')
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyInteger('aro_id', true)->notNull()->default(0)
                ->modifyInteger('aco_id', true)->notNull()->default(0)
                ->modifyTinyInteger('action_read')->notNull()->default(0)
                ->modifyTinyInteger('action_create')->notNull()->default(0)
                ->modifyTinyInteger('action_update')->notNull()->default(0)
                ->modifyTinyInteger('action_delete')->notNull()->default(0)
                ->addIndex('idx_aco_id', 'aco_id')
                ->addIndex('idx_aro_id', 'aro_id')
                ->execute();
        }

        if ($schema->tableExists('#__support_attachments')) {
            $schema->alterTable('#__support_attachments')
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyInteger('ticket', true)->notNull()->default(0)
                ->modifyString('filename', 255)->default('')
                ->modifyString('description', 255)->notNull()->default('')
                ->addIndex('idx_ticket', 'ticket')
                ->execute();
        }

        if ($schema->tableExists('#__support_comments')) {
            $schema->alterTable('#__support_comments')
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyInteger('ticket', true)->notNull()->default(0)
                ->modifyString('created_by', 50)->notNull()->default('')
                ->modifyText('comment')->notNull()
                ->modifyText('changelog')->notNull()
                ->addIndex('idx_ticket', 'ticket')
                ->addIndex('idx_created_by', 'created_by')
                ->execute();
        }

        if ($schema->tableExists('#__support_messages')) {
            $schema->alterTable('#__support_messages')
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyString('title', 250)->notNull()->default('')
                ->modifyText('message')->notNull()
                ->execute();
        }

        if ($schema->tableExists('#__support_queries')) {
            $schema->alterTable('#__support_queries')
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyString('title', 250)->notNull()->default('')
                ->modifyText('conditions')->notNull()
                ->modifyText('query')->notNull()
                ->modifyInteger('user_id', true)->notNull()->default(0)
                ->modifyString('sort', 100)->notNull()->default('')
                ->modifyString('sort_dir', 100)->notNull()->default('')
                ->addIndex('idx_user_id', 'user_id')
                ->addIndex('idx_iscore', 'iscore')
                ->execute();
        }

        if ($schema->tableExists('#__support_resolutions')) {
            $schema->alterTable('#__support_resolutions')
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyString('title', 100)->notNull()->default('')
                ->modifyString('alias', 100)->notNull()->default('')
                ->execute();
        }

        if ($schema->tableExists('#__support_tickets')) {
            $schema->alterTable('#__support_tickets')
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->addIndex('idx_owner', 'owner')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_acl_aros')) {
            $schema->table('#__support_acl_aros')->alter()
                ->dropIndex('idx_model_foreign_key')
                ->execute();
        }

        if ($schema->tableExists('#__support_acl_aros_acos')) {
            $schema->table('#__support_acl_aros_acos')->alter()
                ->dropIndex('idx_aco_id')
                ->dropIndex('idx_aro_id')
                ->execute();
        }

        if ($schema->tableExists('#__support_attachments')) {
            $schema->table('#__support_attachments')->alter()
                ->dropIndex('idx_ticket')
                ->execute();
        }

        if ($schema->tableExists('#__support_comments')) {
            $schema->table('#__support_comments')->alter()
                ->dropIndex('idx_ticket')
                ->dropIndex('idx_created_by')
                ->execute();
        }

        if ($schema->tableExists('#__support_queries')) {
            $schema->table('#__support_queries')->alter()
                ->dropIndex('idx_user_id')
                ->dropIndex('idx_iscore')
                ->execute();
        }

        if ($schema->tableExists('#__support_tickets')) {
            $schema->table('#__support_tickets')->alter()
                ->dropIndex('idx_owner')
                ->execute();
        }
    }
}
