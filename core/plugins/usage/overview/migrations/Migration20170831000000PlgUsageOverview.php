<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('usage', 'overview');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('usage', 'overview');
    }
}
