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
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $this->addPluginEntry('filesystem', 'github');

        if ($schema->tableExists('#__projects_connection_providers')) {
            $results = $this->db->getQuery(true)
                ->select('*')
                ->from('#__projects_connection_providers')
                ->where('alias', '=', 'github')
                ->loadObjectList();

            if (count($results) < 1) {
                $this->db->getQuery(true)
                    ->insert('#__projects_connection_providers')
                    ->columns(['alias', 'name'])
                    ->values("'github', 'Github'")
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $this->deletePluginEntry('filesystem', 'github');

        if ($schema->tableExists('#__projects_connection_providers')) {
            $results = $this->db->getQuery(true)
                ->select('*')
                ->from('#__projects_connection_providers')
                ->where('alias', '=', 'github')
                ->loadObjectList();

            foreach ($results as $result) {
                $this->db->getQuery(true)
                    ->delete('#__projects_connections')
                    ->where('provider_id', '=', $result->id)
                    ->execute();
            }

            if (count($results) > 0) {
                $this->db->getQuery(true)
                    ->delete('#__projects_connection_providers')
                    ->where('alias', '=', 'github')
                    ->execute();
            }
        }
    }
}
