<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
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
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'resources');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'resources');
    }
}
