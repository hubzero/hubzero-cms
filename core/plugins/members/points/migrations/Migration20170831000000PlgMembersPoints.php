<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Points\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Members - Points plugin
 **/
class Migration20170831000000PlgMembersPoints extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'points');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'points');
    }
}
