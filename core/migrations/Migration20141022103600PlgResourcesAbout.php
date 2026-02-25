<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing abouttool resources plugin and updating references
**/
class Migration20141022103600PlgResourcesAbout extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__resource_types')) {
            // Get all the "mine" queries
            // Get all the "mine" queries
            $records = $this->db->getQuery(true)
                ->select(['id', 'params'])
                ->from('#__resource_types')
                ->where('category', '=', 27)
                ->whereLike('params', 'plg_abouttool=1')
                ->loadObjectList();
            if ($records) {
                foreach ($records as $record) {
                    $p = new \Hubzero\Config\Registry($record->params);
                    $p->set('plg_about', 1);
                    $p->set('plg_abouttool', 0);

                    $this->db->getQuery(true)
                        ->update('#__resource_types')
                        ->set(['params' => $p->toString()])
                        ->where('id', '=', $record->id)
                        ->execute();
                }
            }
        }

        $this->deletePluginEntry('resources', 'abouttool');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $this->addPluginEntry('resources', 'abouttool');

        if ($schema->tableExists('#__resource_types')) {
            $records = $this->db->getQuery(true)
                ->select(['id', 'params'])
                ->from('#__resource_types')
                ->where('category', '=', 27)
                ->where('alias', '=', 'tools')
                ->loadObjectList();
            if ($records) {
                foreach ($records as $record) {
                    $p = new \Hubzero\Config\Registry($record->params);
                    $p->set('plg_about', 0);
                    $p->set('plg_abouttool', 1);

                    $this->db->getQuery(true)
                        ->update('#__resource_types')
                        ->set(['params' => $p->toString()])
                        ->where('id', '=', $record->id)
                        ->execute();
                }
            }
        }
    }
}
