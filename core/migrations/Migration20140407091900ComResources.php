<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indices
 *
*/
class Migration20140407091900ComResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__resource_assoc')) {
            $schema->addIndex('#__resource_assoc', 'idx_parent_id_child_id', ['parent_id', 'child_id']);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__resource_assoc')) {
            $schema->dropIndex('#__resource_assoc', 'idx_parent_id_child_id');
        }
    }
}
