<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for add watching table
 *
*/
class Migration20130426074801ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__support_watching')) {
            $schema->createTable('#__support_watching')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('ticket_id')->default(0)
                ->integer('user_id')->default(0)
                ->primaryKey('id')
                ->index('idx_ticket_id', 'ticket_id')
                ->index('idx_user_id', 'user_id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        } else {
            $schema->addIndex('#__support_watching', 'idx_ticket_id', 'ticket_id');
            $schema->addIndex('#__support_watching', 'idx_user_id', 'user_id');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_watching')) {
            $schema->dropTable('#__support_watching');
        }
    }
}
