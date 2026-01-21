<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding unique constraint to tool version zone
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 **/
class Migration20140716180300ComTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (
            $this->db->tableExists('#__tool_version_zone')
            && !$this->db->tableHasKey('#__tool_version_zone', 'idx_zoneid_toolversionid')
        ) {
            $query = "ALTER TABLE `#__tool_version_zone` "
                . "ADD CONSTRAINT UNIQUE KEY `idx_zoneid_toolversionid`(zone_id, tool_version_id)";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if (
            $this->db->tableExists('#__tool_version_zone')
            && $this->db->tableHasKey('#__tool_version_zone', 'idx_zoneid_toolversionid')
        ) {
            $query = "ALTER TABLE `#__tool_version_zone` DROP KEY `idx_zoneid_toolversionid`";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
