<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding missing index to #__collections_items table
  *
**/
class Migration20160418114900ComCollections extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__collections_items')) {
            $schema->addIndex('#__collections_items', 'idx_type_object_id', ['type', 'object_id']);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__collections_items')) {
            $schema->dropIndex('#__collections_items', 'idx_type_object_id');
        }
    }
}
