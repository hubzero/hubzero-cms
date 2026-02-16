<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding comment_id to wish attachments table
 *
*/
class Migration20170323234546PComWishlist extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__wish_attachments')
            && !$schema->hasColumn('#__wish_attachments', 'comment_id')
        ) {
            $schema->addColumn('#__wish_attachments', 'comment_id')
                ->integer()
                ->unsigned()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }

    /**
     * Up
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__wish_attachments')
            && $schema->hasColumn('#__wish_attachments', 'comment_id')
        ) {
            $schema->dropColumn('#__wish_attachments', 'comment_id');
        }
    }
}
