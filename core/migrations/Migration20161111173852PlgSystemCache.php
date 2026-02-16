<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing deprecaed disablecache plugin
 *
*/
class Migration20161111173852PlgSystemCache extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $params = $this->db->getQuery(true)
                ->select('params')
                ->from('#__extensions')
                ->where('type', '=', 'plugin')
                ->where('folder', '=', 'system')
                ->where('element', '=', 'disablecache')
                ->value('params');

            if ($params) {
                $params = $this->params($params);

                if (isset($params->definitions)) {
                    $cparams = $this->db->getQuery(true)
                        ->select('params')
                        ->from('#__extensions')
                        ->where('type', '=', 'plugin')
                        ->where('folder', '=', 'system')
                        ->where('element', '=', 'cache')
                        ->value('params');
                    $cparams = $this->params($cparams);

                    $cparams->cacheexempt = $params->definitions;
                    $cparams = json_encode($cparams);

                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set(['params' => $cparams])
                        ->where('type', '=', 'plugin')
                        ->where('folder', '=', 'system')
                        ->where('element', '=', 'cache')
                        ->execute();
                }
            }
        }

        $this->deletePluginEntry('system', 'disablecache');
    }

    /**
     * Convert string to object
     *
     * @param   string  $params
     * @return  object
     **/
    private function params($params)
    {
        $original = $params;
        $params = json_decode($params);
        if (json_last_error() !== JSON_ERROR_NONE && $original !== null && $original !== '') {
            $params = parse_ini_string($original, false, INI_SCANNER_RAW);
        }
        if (!isset($params) || !$params) {
            $params = new \stdClass();
        }
        return $params;
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addPluginEntry('system', 'disablecache');
    }
}
