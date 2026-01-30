<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Usage\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Members - Usage plugin
 **/
class Migration20170831000000PlgMembersUsage extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'usage');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'usage');
    }
}
