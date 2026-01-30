<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding publications cron plugin
 *
*/
class Migration20140121142313PlgCronPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('cron', 'publications');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('cron', 'publications');
    }
}
