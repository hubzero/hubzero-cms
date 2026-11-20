<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Memberhome\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Memberhome plugin
 **/
class Migration20170831000000PlgSystemMemberhome extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'memberhome', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'memberhome');
    }
}
