<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding handler association table
  *
**/
class Migration20150113125313ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__publication_handler_assoc')) {
            $schema->createTable('#__publication_handler_assoc')
                ->integer('id', ['autoIncrement' => true])
                ->integer('publication_version_id')
                ->integer('element_id')
                ->integer('handler_id')
                ->text('params')
                ->integer('ordering')->default(1)
                ->tinyInteger('status')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__publication_handler_assoc');
    }
}
