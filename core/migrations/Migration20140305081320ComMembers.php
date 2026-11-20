<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__xprofiles_tags');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__xprofiles_tags')) {
            $schema
                ->createTable('#__xprofiles_tags')
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
