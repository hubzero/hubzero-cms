<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'dashboard');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'dashboard');
    }
}
