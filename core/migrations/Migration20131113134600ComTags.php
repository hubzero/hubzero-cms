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
*/
class Migration20131113134600ComTags extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__tags')) {
            $schema->alterTable('#__tags')
                ->modifyColumn('id')->integer()->unsigned()->notNull()->autoIncrement()
                ->modifyColumn('raw_tag')->string(100)->notNull()->default('')
                ->modifyColumn('description')->text()->notNull()
                ->modifyColumn('admin')->tinyInteger()->unsigned()->notNull()->default(0)
                ->addIndex('idx_tag', 'tag')
                ->execute();
        }

        if ($schema->tableExists('#__tags_object')) {
            $schema->alterTable('#__tags_object')
                ->modifyColumn('id')->integer()->unsigned()->notNull()->autoIncrement()
                ->modifyColumn('objectid')->integer()->unsigned()->notNull()->default(0)
                ->modifyColumn('tagid')->integer()->unsigned()->notNull()->default(0)
                ->modifyColumn('strength')->tinyInteger()->notNull()->default(0)
                ->modifyColumn('taggerid')->integer()->unsigned()->notNull()->default(0)
                ->modifyColumn('taggedon')->datetime()->notNull()->default('0000-00-00 00:00:00')
                ->modifyColumn('tbl')->string(255)->notNull()->default('')
                ->modifyColumn('label')->string(30)->notNull()->default('')
                ->execute();
        }

        if ($schema->tableExists('#__tags_group')) {
            $schema->alterTable('#__tags_group')
                ->modifyColumn('id')->integer()->unsigned()->notNull()->autoIncrement()
                ->modifyColumn('groupid')->integer()->unsigned()->notNull()->default(0)
                ->modifyColumn('tagid')->integer()->unsigned()->notNull()->default(0)
                ->modifyColumn('priority')->integer()->notNull()->default(0)
                ->addIndex('idx_tagid', 'tagid')
                ->addIndex('idx_groupid', 'groupid')
                ->execute();
        }

        if ($schema->tableExists('#__tags_log')) {
            $schema->alterTable('#__tags_log')
                ->modifyColumn('id')->integer()->unsigned()->notNull()->autoIncrement()
                ->modifyColumn('tag_id')->integer()->unsigned()->notNull()->default(0)
                ->modifyColumn('action')->string(50)->notNull()->default('')
                ->modifyColumn('comments')->text()->notNull()
                ->modifyColumn('user_id')->integer()->unsigned()->notNull()->default(0)
                ->modifyColumn('actorid')->integer()->unsigned()->notNull()->default(0)
                ->addIndex('idx_tag_id', 'tag_id')
                ->addIndex('idx_user_id', 'user_id')
                ->execute();
        }

        if ($schema->tableExists('#__tags_substitute')) {
            $schema->alterTable('#__tags_substitute')
                ->modifyColumn('tag_id')->integer()->unsigned()->notNull()->default(0)
                ->modifyColumn('created_by')->integer()->unsigned()->notNull()->default(0)
                ->modifyColumn('raw_tag')->string(100)->notNull()->default('')
                ->modifyColumn('tag')->string(100)->notNull()->default('')
                ->addIndex('idx_tag_id', 'tag_id')
                ->addIndex('idx_created_by', 'created_by')
                ->addIndex('idx_tag', 'tag')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__tags')) {
            $schema->table('#__tags')->alter()
                ->dropIndex('idx_tag')
                ->execute();
        }

        if ($schema->tableExists('#__tags_group')) {
            $schema->table('#__tags_group')->alter()
                ->dropIndex('idx_tagid')
                ->dropIndex('idx_groupid')
                ->execute();
        }

        if ($schema->tableExists('#__tags_log')) {
            $schema->table('#__tags_log')->alter()
                ->dropIndex('idx_tag_id')
                ->dropIndex('idx_user_id')
                ->execute();
        }

        if ($schema->tableExists('#__tags_substitute')) {
            $schema->table('#__tags_substitute')->alter()
                ->dropIndex('idx_tag_id')
                ->dropIndex('idx_created_by')
                ->dropIndex('idx_tag')
                ->execute();
        }
    }
}
