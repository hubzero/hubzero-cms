<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Hubzero\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Hubzero plugin
 **/
class Migration20170831000000PlgSystemHubzero extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'hubzero');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'hubzero');
    }
}
