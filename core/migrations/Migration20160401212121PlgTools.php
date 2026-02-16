<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating default tools plugin values
**/
class Migration20160401212121PlgTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $result = $this->db->getQuery(true)
                ->select('*')
                ->from('#__extensions')
                ->where('element', '=', 'novnc')
                ->where('folder', '=', 'tools')
                ->first();

            if ($result && $result->extension_id) {
                $params = new \Hubzero\Config\Registry($result->params);
                $params->set('browsers', '*, safari 5.1
*, chrome 26.0
*, iceweasel 38.0
*, firefox 30.0
*, opera 23.0
*, mozilla 5.0
iOS, safari 1.0
Windows, msie 10.0
Windows, ie 10.0');

                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['params' => $params->toString()])
                    ->where('extension_id', '=', (int)$result->extension_id)
                    ->execute();
            }

            $result = $this->db->getQuery(true)
                ->select('*')
                ->from('#__extensions')
                ->where('element', '=', 'java')
                ->where('folder', '=', 'tools')
                ->first();

            if ($result && $result->extension_id) {
                $params = new \Hubzero\Config\Registry($result->params);
                $params->set('browsers', '*, chrome 999999.0
*, safari 1.0
*, iceweasel 1.0
*, firefox 1.0
*, opera 1.0
*, IE 3.0
*, mozilla 5.0
iOS, Safari 9999.9');

                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['params' => $params->toString()])
                    ->where('extension_id', '=', (int)$result->extension_id)
                    ->execute();
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
