<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Usage\Overview\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Usage - Overview plugin
 **/
class Migration20170831000000PlgUsageOverview extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('usage', 'overview');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('usage', 'overview');
    }
}
