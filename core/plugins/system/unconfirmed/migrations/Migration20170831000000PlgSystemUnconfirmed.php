<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Unconfirmed\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Unconfirmed plugin
 **/
class Migration20170831000000PlgSystemUnconfirmed extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'unconfirmed');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'unconfirmed');
    }
}
