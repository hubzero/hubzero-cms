<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Resources\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Members - Resources plugin
 **/
class Migration20170831000000PlgMembersResources extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'resources');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'resources');
    }
}
