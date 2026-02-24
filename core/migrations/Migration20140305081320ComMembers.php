<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for dropping profile tags table
 *
*/
class Migration20140305081320ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__xprofiles_tags');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__xprofiles_tags')) {
            $schema->createTable('#__xprofiles_tags')
                ->integer('id', ['autoIncrement' => true])
                ->integer('uidNumber')->nullable()
                ->integer('tagid')->nullable()
                ->integer('taggerid')->default(0)
                ->datetime('taggedon')->default('0000-00-00 00:00:00')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
