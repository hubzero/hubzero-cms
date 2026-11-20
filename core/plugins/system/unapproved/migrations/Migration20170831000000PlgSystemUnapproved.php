<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Unapproved\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Unapproved plugin
 **/
class Migration20170831000000PlgSystemUnapproved extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'unapproved');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'unapproved');
    }
}
