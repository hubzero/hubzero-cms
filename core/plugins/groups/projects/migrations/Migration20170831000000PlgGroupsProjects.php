<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Groups\Projects\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Groups - Projects plugin
**/
class Migration20170831000000PlgGroupsProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('groups', 'projects');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('groups', 'projects');
    }
}
