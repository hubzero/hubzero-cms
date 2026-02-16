<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for making ticket ID signed to allow
 * negative IDs for temp directories.
 *
*/
class Migration20140627091431ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__support_attachments', 'ticket')) {
            $schema->modifyColumn('#__support_attachments', 'ticket')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__support_attachments', 'ticket')) {
            $schema->modifyColumn('#__support_attachments', 'ticket')
                ->integer()
                ->unsigned()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }
}
