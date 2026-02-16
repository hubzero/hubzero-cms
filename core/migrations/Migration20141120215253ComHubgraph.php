<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for cleaning up hubgraph extension entry
 *
*/
class Migration20141120215253ComHubgraph extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $params = array(
            "host"           => "unix:///var/run/hubgraph-server.sock",
            "port"           => null,
            "showTagCloud"   => true,
            "enabledOptions" => ""
        );
        $this->addComponentEntry('Hubgraph', 'com_hubgraph', 1, $params, false);

        if ($schema->tableExists('#__extensions')) {
            // Look for multiple entries
            $ids = $this->db->getQuery(true)
                ->select('extension_id')
                ->from('#__extensions')
                ->where('element', '=', 'com_hubgraph')
                ->order('extension_id', 'ASC')
                ->loadColumn();

            if ($ids && count($ids) > 1) {
                unset($ids[0]);

                foreach ($ids as $id) {
                    $this->db->getQuery(true)
                        ->delete('#__extensions')
                        ->where('extension_id', '=', (int)$id)
                        ->execute();
                }
            }

            // Look for non-json params
            $params = $this->getParams('com_hubgraph', true);

            if (is_null(json_decode($params))) {
                /*$object = unserialize($params);
                $params = $object->settings();

                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['params' => json_encode($params)])
                    ->where('element', '=', 'com_hubgraph')
                    ->execute();*/
            }
        }
    }
}

// Placeholder class needed to parse serialized object/params previously stored in extensions directory
/*class HubgraphConfiguration
{
    private $settings, $idx;

    function settings()
    {
        return $this->settings;
    }
}*/
