<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes to Hubzero\Item tables
**/
class Migration20140822161900PlgHubzeroComments extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__item_comment_files')) {
            $schema->addIndex('#__item_comment_files', 'idx_comment_id', 'comment_id');
        }

        if ($schema->tableExists('#__item_comments')) {
            $schema->addIndex('#__item_comments', 'idx_item_type_item_id', ['item_type', 'item_id']);

            $schema->addIndex('#__item_comments', 'idx_parent', 'parent');

            $schema->addIndex('#__item_comments', 'idx_state', 'state');
        }

        if ($schema->tableExists('#__item_votes')) {
            $schema->addIndex('#__item_votes', 'idx_item_type_item_id', ['item_type', 'item_id']);

            $schema->addIndex('#__item_votes', 'idx_created_by', 'created_by');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__item_comment_files')) {
            $schema->dropIndex('#__item_comment_files', 'idx_comment_id');
        }

        if ($schema->tableExists('#__item_comments')) {
            $schema->dropIndex('#__item_comments', 'idx_item_type_item_id');

            $schema->dropIndex('#__item_comments', 'idx_parent');

            $schema->dropIndex('#__item_comments', 'idx_state');
        }

        if ($schema->tableExists('#__item_votes')) {
            $schema->dropIndex('#__item_votes', 'idx_item_type_item_id');

            $schema->dropIndex('#__item_votes', 'idx_created_by');
        }
    }
}
