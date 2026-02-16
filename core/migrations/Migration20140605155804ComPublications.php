<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding logs table
**/
class Migration20140605155804ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Set up curation
        if (!$schema->tableExists('#__publication_logs')) {
            $schema->createTable('#__publication_logs')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('publication_id')
                ->integer('publication_version_id')
                ->integer('month')
                ->integer('year')
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->integer('page_views')->default(0)
                ->integer('primary_accesses')->default(0)
                ->integer('support_accesses')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__publication_logs');
    }
}
