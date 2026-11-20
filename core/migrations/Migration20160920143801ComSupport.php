<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
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
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__support_resolutions')) {
            $schema
                ->createTable('#__support_resolutions')
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
