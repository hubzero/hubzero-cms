<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

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

        $query = $this->db->getQuery(true);
        $query->update('#__users_tool_preferences')
            ->set(['params' => Expression::replace('params', 'java', 'novnc')])
            ->execute();
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addPluginEntry('tools', 'java', 0);
    }
}
