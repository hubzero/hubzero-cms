<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__time_hubs') && !$schema->hasColumn('#__time_hubs', 'asset_id')) {
            $schema->addColumn('#__time_hubs', 'asset_id')->integer()->nullable()->default(null);
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

        if ($schema->tableExists('#__time_hubs') && $schema->hasColumn('#__time_hubs', 'asset_id')) {
            $schema->dropColumn('#__time_hubs', 'asset_id');
        }
    }
}
