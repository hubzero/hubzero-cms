<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes to #__xmessage tables
 *
*/
class Migration20170129140423PlgXmessage extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xmessage')) {
            $schema->addIndex('#__xmessage', 'idx_created_by', 'created_by');

            $schema->addIndex('#__xmessage', 'idx_type', 'type');
        }

        if ($schema->tableExists('#__xmessage_notify')) {
            $schema->addIndex('#__xmessage_notify', 'idx_type', 'type');
        }

        if ($schema->tableExists('#__xmessage_recipient')) {
            $schema->addIndex('#__xmessage_recipient', 'idx_state', 'state');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xmessage')) {
            $schema->dropIndex('#__xmessage', 'idx_created_by');

            $schema->dropIndex('#__xmessage', 'idx_type');
        }

        if ($schema->tableExists('#__xmessage_notify')) {
            $schema->dropIndex('#__xmessage_notify', 'idx_type');
        }

        if ($schema->tableExists('#__xmessage_recipient')) {
            $schema->dropIndex('#__xmessage_recipient', 'idx_state');
        }
    }
}
