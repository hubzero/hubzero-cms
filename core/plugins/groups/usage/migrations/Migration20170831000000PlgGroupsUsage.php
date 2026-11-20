<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Groups\Usage\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Groups - Usage plugin
**/
class Migration20170831000000PlgGroupsUsage extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('groups', 'usage');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('groups', 'usage');
    }
}
