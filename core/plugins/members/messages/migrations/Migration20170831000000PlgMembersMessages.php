<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Messages\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Members - Messages plugin
 **/
class Migration20170831000000PlgMembersMessages extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'messages');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'messages');
    }
}
