<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add table for Blacklist antispam plugin
 *
*/
class Migration20150617181839PlgAntispamBlacklist extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__antispam_words')) {
            $schema->createTable('#__antispam_words')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('word', 256)->nullable()
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

        $schema->dropTable('#__antispam_words');
    }
}
