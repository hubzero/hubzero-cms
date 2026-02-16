<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes to #__support_tickets table
 *
*/
class Migration20160422152647ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_tickets')) {
            $schema->addIndex('#__support_tickets', 'idx_status', 'status');

            $schema->addIndex('#__support_tickets', 'idx_open', 'open');

            $schema->addIndex('#__support_tickets', 'idx_type', 'type');

            $schema->addIndex('#__support_tickets', 'idx_group', 'group');

            $schema->addIndex('#__support_tickets', 'idx_severity', 'severity');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_tickets')) {
            $schema->dropIndex('#__support_tickets', 'idx_status');

            $schema->dropIndex('#__support_tickets', 'idx_open');

            $schema->dropIndex('#__support_tickets', 'idx_type');

            $schema->dropIndex('#__support_tickets', 'idx_group');

            $schema->dropIndex('#__support_tickets', 'idx_severity');
        }
    }
}
