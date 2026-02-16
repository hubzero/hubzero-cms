<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding the blacklist table
**/
class Migration20160805180813ComSearch extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__search_blacklist')) {
            $schema->createTable('#__search_blacklist')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('scope', 11)->default('')
                ->integer('scope_id')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->nullable()
                ->primaryKey('id')
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

        $schema->dropTable('#__search_blacklist');
    }
}
