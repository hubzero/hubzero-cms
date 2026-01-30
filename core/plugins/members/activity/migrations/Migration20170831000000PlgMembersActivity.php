<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Activity\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Members - Activity plugin
 **/
class Migration20170831000000PlgMembersActivity extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'activity');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'activity');
    }
}
