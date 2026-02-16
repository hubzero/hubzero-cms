<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding venue tables
 *
*/
class Migration20121015000000ComTools extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__venue') && !$schema->tableExists('venue')) {
            $schema->createTable('#__venue')
                ->integer('id', ['autoIncrement' => true])
                ->string('venue', 40)->nullable()
                ->string('network', 40)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__venue_countries') && !$schema->tableExists('venue_countries')) {
            $schema->createTable('#__venue_countries')
                ->integer('id', ['autoIncrement' => true])
                ->string('countrySHORT', 40)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__venue');
        $schema->dropTable('#__venue_countries');
    }
}
