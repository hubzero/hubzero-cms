<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Cron\Forum\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Cron - Forum plugin
 **/
class Migration20170831000000PlgCronForum extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('cron', 'forum');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('cron', 'forum');
    }
}
