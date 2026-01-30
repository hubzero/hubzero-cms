<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Dashboard\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Members - Dashboard plugin
 **/
class Migration20170831000000PlgMembersDashboard extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'dashboard');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'dashboard');
    }
}
