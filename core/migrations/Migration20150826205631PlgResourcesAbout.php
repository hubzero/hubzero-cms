<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for for adding default plg_about and plg_abouttool parameters for tool resource type
 *
*/
class Migration20150826205631PlgResourcesAbout extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__resource_types')) {
            $query = $this->db->getQuery(true)
                ->select(['id', 'params'])
                ->from('#__resource_types')
                ->where('category', '=', 27)
                ->where('alias', '=', 'tools');
            $records = $query->loadObjectList();

            foreach ($records as $record) {
                $params = $record->params;

                $matches = null;

                if (preg_match("/^\s*{/", $params, $matches)) {
                    // Looks like a json format, ignore entry.
                    continue;
                }

                if (!preg_match("/plg_about\s*=/", $params)) {
                    $params .= "\nplg_about=0";
                }

                if (!preg_match("/plg_abouttool\s*=/", $params)) {
                    $params .= "\nplg_abouttool=1";
                }

                if ($params != $record->params) {
                    $this->db->getQuery(true)
                        ->update('#__resource_types')
                        ->set(['params' => $params])
                        ->where('id', '=', (int)$record->id)
                        ->execute();
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
    }
}
