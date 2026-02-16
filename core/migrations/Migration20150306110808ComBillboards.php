<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for billboards table renames to match convention
 *
*/
class Migration20150306110808ComBillboards extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__billboard_collection') && !$schema->tableExists('#__billboards_collections')) {
            $schema->renameTable('#__billboard_collection', '#__billboards_collections');
        }
        if ($schema->tableExists('#__billboards') && !$schema->tableExists('#__billboards_billboards')) {
            $schema->renameTable('#__billboards', '#__billboards_billboards');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__billboard_collection') && $schema->tableExists('#__billboards_collections')) {
            $schema->renameTable('#__billboards_collections', '#__billboard_collection');
        }
        if (!$schema->tableExists('#__billboards') && $schema->tableExists('#__billboards_billboards')) {
            $schema->renameTable('#__billboards_billboards', '#__billboards');
        }
    }
}
