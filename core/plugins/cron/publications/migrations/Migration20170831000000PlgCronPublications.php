<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Cron\Publications\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Cron - Publications plugin
 **/
class Migration20170831000000PlgCronPublications extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('cron', 'publications');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('cron', 'publications');
    }
}
