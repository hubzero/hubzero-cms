<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to rename production_collections primary key
**/
class Migration20160825000001ComCart extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__cart_meta')) {
            $schema->createTable('#__cart_meta')
                ->unsignedInteger('mtId', ['autoIncrement' => true])
                ->integer('scope_id')->default(0)
                ->string('scope', 100)->default('')
                ->string('mtKey', 100)->default('')
                ->text('mtValue')->default('')
                ->primaryKey('mtId')
                ->engine('InnoDB')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__cart_meta')) {
            $schema->dropTable('#__cart_meta');
        }
    }
}
