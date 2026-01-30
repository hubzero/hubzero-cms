<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Account\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Members - Account plugin
 **/
class Migration20170831000000PlgMembersAccount extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'account');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'account');
    }
}
