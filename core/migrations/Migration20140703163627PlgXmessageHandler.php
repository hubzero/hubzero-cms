<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating xmessage component entries
**/
class Migration20140703163627PlgXmessageHandler extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xmessage_component')) {
            // Old flagged state was 1. Change it to 3.
            $this->db->getQuery(true)
                ->update('#__xmessage_component')
                ->set(['component' => 'com_tools'])
                ->where('component', '=', 'com_contribtool')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xmessage_component')) {
            // Old flagged state was 1. Change it to 3.
            $this->db->getQuery(true)
                ->update('#__xmessage_component')
                ->set(['component' => 'com_contribtool'])
                ->where('component', '=', 'com_tools')
                ->execute();
        }
    }
}
