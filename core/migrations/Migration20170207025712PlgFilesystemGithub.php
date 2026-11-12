<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding GitHub filesystem plugin
 *
*/
class Migration20170207025712PlgFilesystemGithub extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $this->addPluginEntry('filesystem', 'github');

        if ($schema->tableExists('#__projects_connection_providers')) {
            $results = $this->db
                ->getQuery()
                ->select('*')
                ->from('#__projects_connection_providers')
                ->where('alias', '=', 'github')
                ->loadObjectList();

            if (count($results) < 1) {
                $this->db
                    ->getQuery()
                    ->insert('#__projects_connection_providers')
                    ->columns(['alias', 'name'])
                    ->values("'github', 'Github'")
                    ->execute();
            }
        }
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $this->deletePluginEntry('filesystem', 'github');

        if ($schema->tableExists('#__projects_connection_providers')) {
            $results = $this->db
                ->getQuery()
                ->select('*')
                ->from('#__projects_connection_providers')
                ->where('alias', '=', 'github')
                ->loadObjectList();

            foreach ($results as $result) {
                $this->db
                    ->getQuery()
                    ->delete('#__projects_connections')
                    ->where('provider_id', '=', $result->id)
                    ->execute();
            }

            if (count($results) > 0) {
                $this->db
                    ->getQuery()
                    ->delete('#__projects_connection_providers')
                    ->where('alias', '=', 'github')
                    ->execute();
            }
        }
    }
}
