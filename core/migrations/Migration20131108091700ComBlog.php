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
  *
**/
class Migration20131108091700ComBlog extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__blog_entries')) {
            $schema->table('#__blog_entries')->alter()
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyInteger('created_by', true)->notNull()->default(0)
                ->modifyTinyInteger('state')->notNull()->default(0)
                ->modifyDatetime('publish_up')->notNull()->default('0000-00-00 00:00:00')
                ->modifyDatetime('publish_down')->notNull()->default('0000-00-00 00:00:00')
                ->modifyTinyInteger('allow_comments')->notNull()->default(0)
                ->modifyInteger('hits', true)->notNull()->default(0)
                ->modifyTinyText('params')->notNull()
                ->modifyString('scope', 100)->notNull()->default('')
                ->modifyText('content')->notNull()
                ->modifyString('alias', 255)->notNull()->default('')
                ->modifyString('title', 255)->notNull()->default('')
                ->addIndex('idx_created_by', 'created_by')
                ->addIndex('idx_alias', 'alias')
                ->execute();

            if ($schema->hasColumn('#__blog_entries', 'group_id')) {
                $schema->table('#__blog_entries')->alter()
                    ->modifyInteger('group_id')->notNull()->default(0)
                    ->addIndex('idx_group_id', 'group_id')
                    ->execute();
            }
        }

        if ($schema->tableExists('#__blog_comments')) {
            $schema->table('#__blog_comments')->alter()
                ->modifyInteger('id', true)->notNull()->autoIncrement()
                ->modifyInteger('parent', true)->notNull()->default(0)
                ->modifyInteger('created_by', true)->notNull()->default(0)
                ->modifyDatetime('created')->notNull()->default('0000-00-00 00:00:00')
                ->modifyInteger('entry_id', true)->notNull()->default(0)
                ->modifyText('content')->notNull()
                ->addIndex('idx_created_by', 'created_by')
                ->addIndex('idx_parent', 'parent')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__blog_entries')) {
            $schema->table('#__blog_entries')->alter()
                ->dropIndex('idx_created_by')
                ->dropIndex('idx_group_id')
                ->dropIndex('idx_alias')
                ->execute();
        }

        if ($schema->tableExists('#__blog_comments')) {
            $schema->table('#__blog_comments')->alter()
                ->dropIndex('idx_created_by')
                ->dropIndex('idx_parent')
                ->execute();
        }
    }
}
