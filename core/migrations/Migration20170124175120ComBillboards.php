<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes to #__billboards_billboards table
  *
**/
class Migration20170124175120ComBillboards extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__billboards_billboards')) {
            $schema->addIndex('#__billboards_billboards', 'idx_collection_id', 'collection_id');

            $schema->addIndex('#__billboards_billboards', 'idx_published', 'published');

            $schema->addIndex('#__billboards_billboards', 'idx_alias', 'alias');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__billboards_billboards')) {
            $schema->dropIndex('#__billboards_billboards', 'idx_collection_id');

            $schema->dropIndex('#__billboards_billboards', 'idx_published');

            $schema->dropIndex('#__billboards_billboards', 'idx_alias');
        }
    }
}
