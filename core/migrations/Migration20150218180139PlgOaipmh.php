<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding OAI-PMH plugin entries for resources and publications
  *
**/
class Migration20150218180139PlgOaipmh extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__oaipmh_dcspecs');

        $this->addPluginEntry('oaipmh', 'resources', 1);
        $this->addPluginEntry('oaipmh', 'publications', 1);
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__oaipmh_dcspecs')) {
            $schema->createTable('#__oaipmh_dcspecs')
                ->integer('id', ['autoIncrement' => true])
                ->string('name', 255)
                ->text('query')
                ->integer('display')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        $this->deletePluginEntry('oaipmh', 'resources');
        $this->deletePluginEntry('oaipmh', 'publications');
    }
}
