<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing Tools - Java plugin
 *
*/
class Migration20241129223917PlgToolsJava extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deletePluginEntry('tools', 'java');

        $query = "UPDATE `#__users_tool_preferences` SET params=REPLACE(params,'java','novnc');";
        $this->db->setQuery($query);
        $this->db->query();
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addPluginEntry('tools', 'java', 0);
    }
}
