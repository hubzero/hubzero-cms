<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add #__cart_downloads table
**/
class Migration20160620151602ComCart extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__cart_downloads')) {
            $schema->createTable('#__cart_downloads')
                ->unsignedInteger('dId', ['autoIncrement' => true])
                ->integer('uId')->nullable()
                ->integer('sId')->nullable()
                ->datetime('dDownloaded')->nullable()
                ->tinyInteger('dStatus')->default(1)
                ->unsignedInteger('dIp')->nullable()
                ->primaryKey('dId')
                ->index('idx_uId', 'uId')
                ->index('idx_sId', 'sId')
                ->engine('MyISAM')
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

        $schema->dropTable('#__cart_downloads');
    }
}
