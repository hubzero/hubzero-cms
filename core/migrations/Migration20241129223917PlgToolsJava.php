<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing Tools - Java plugin
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
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
