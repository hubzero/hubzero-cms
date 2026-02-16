<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating wrong client_id on entries
 *
 */
class Migration20150904103241Extensions extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['client_id' => 0])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_feedaggregator')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['client_id' => 1])
                ->where('type', '=', 'module')
                ->where('element', '=', 'mod_grouppages')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['client_id' => 1])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_feedaggregator')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['client_id' => 0])
                ->where('type', '=', 'module')
                ->where('element', '=', 'mod_grouppages')
                ->execute();
        }
    }
}
