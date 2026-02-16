<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding fields to support attachements
 *
*/
class Migration20140410085610ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // add comment ID
        if (!$schema->hasColumn('#__support_attachments', 'comment_id')) {
            $schema->addColumn('#__support_attachments', 'comment_id')->integer()->notNull()->default(0)->execute();
        }

        // add created
        if (!$schema->hasColumn('#__support_attachments', 'created')) {
            $schema->addColumn('#__support_attachments', 'created')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->execute();
        }

        // add created by
        if (!$schema->hasColumn('#__support_attachments', 'created_by')) {
            $schema->addColumn('#__support_attachments', 'created_by')->integer()->notNull()->default(0)->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // remove comment ID
        if ($schema->hasColumn('#__support_attachments', 'comment_id')) {
            $schema->dropColumn('#__support_attachments', 'comment_id');
        }

        // remove created
        if ($schema->hasColumn('#__support_attachments', 'created')) {
            $schema->dropColumn('#__support_attachments', 'created');
        }

        // remove created by
        if ($schema->hasColumn('#__support_attachments', 'created_by')) {
            $schema->dropColumn('#__support_attachments', 'created_by');
        }
    }
}
