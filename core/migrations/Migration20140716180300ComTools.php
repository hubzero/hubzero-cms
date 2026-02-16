<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding unique constraint to tool version zone
**/
class Migration20140716180300ComTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__tool_version_zone')
            && !$schema->hasKey('#__tool_version_zone', 'idx_zoneid_toolversionid')
        ) {
            $schema->addUniqueIndex('#__tool_version_zone', 'idx_zoneid_toolversionid', ['zone_id', 'tool_version_id']);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__tool_version_zone')
            && $schema->hasKey('#__tool_version_zone', 'idx_zoneid_toolversionid')
        ) {
            $schema->dropIndex('#__tool_version_zone', 'idx_zoneid_toolversionid');
        }
    }
}
