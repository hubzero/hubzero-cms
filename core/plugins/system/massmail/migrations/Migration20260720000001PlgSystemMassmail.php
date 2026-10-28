<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Massmail\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Register the System - Mass Mail plugin, which keeps the mass-mail delivery
 * cron in lock-step with the component's sending switch.
 **/
class Migration20260720000001PlgSystemMassmail extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'massmail');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'massmail');
    }
}
