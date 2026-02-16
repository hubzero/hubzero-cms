<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding status to file uploads
**/
class Migration20150218105200PlgGroupsForum extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__forum_attachments')
            && !$schema->hasColumn('#__forum_attachments', 'status')
        ) {
            // adds column status to forum_attachments table
            /* 0 = unpublished, 1 = published, 2 = deleted */
            $schema->addColumn('#__forum_attachments', 'status')
                ->integer()
                ->nullable()
                ->default(1)
                ->after('description')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__forum_attachments')
            && $schema->hasColumn('#__forum_attachments', 'status')
        ) {
            // drops column status from forum_attachments table
            $schema->dropColumn('#__forum_attachments', 'status');
        }
    }
}
