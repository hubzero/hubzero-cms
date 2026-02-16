<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding asset_id field to time hubs table
 *
*/
class Migration20140807200026ComTime extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__time_hubs') && !$schema->hasColumn('#__time_hubs', 'asset_id')) {
            $schema->addColumn('asset_id')->integer()->nullable()->default(null);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__time_hubs') && $schema->hasColumn('#__time_hubs', 'asset_id')) {
            $schema->dropColumn('#__time_hubs', 'asset_id');
        }
    }
}
