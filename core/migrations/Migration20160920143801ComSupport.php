<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to drop deprecated support resolutions table
 *
*/
class Migration20160920143801ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_resolutions')) {
            $schema->dropTable('#__support_resolutions');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__support_resolutions')) {
            $schema->createTable('#__support_resolutions')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('title', 100)->default('')
                ->string('alias', 100)->default('')
                ->primaryKey('id')
                ->engine('InnoDB')
                ->charset('utf8')
                ->execute();
        }
    }
}
