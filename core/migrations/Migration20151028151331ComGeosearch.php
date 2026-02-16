<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding an index to the GeoSearch table
 *
*/
class Migration20151028151331ComGeosearch extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__geosearch_markers')) {
            return;
        }

        if (!$schema->hasPrimaryKey('#__geosearch_markers')) {
            $schema->table('#__geosearch_markers')->alter()
                ->modifyColumn('id')
                ->integer()
                ->notNull()
                ->autoIncrement()
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__geosearch_markers')) {
            return;
        }

        if ($schema->hasPrimaryKey('#__geosearch_markers')) {
            $schema->table('#__geosearch_markers')->alter()
                ->modifyColumn('id')
                ->integer()
                ->notNull()
                ->dropPrimaryKey()
                ->execute();
        }
    }
}
