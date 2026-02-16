<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting up publication building blocks
  *
**/
class Migration20140519120000ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Set up curation history
        if (!$schema->tableExists('#__publication_curation_history')) {
            $schema->createTable('#__publication_curation_history')
                ->integer('id', ['autoIncrement' => true])
                ->integer('publication_version_id')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->text('changelog')->default('')
                ->tinyInteger('curator')->default(0)
                ->integer('oldstatus')->default(0)
                ->integer('newstatus')->default(0)
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

        $schema->dropTable('#__publication_curation_history');
    }
}
