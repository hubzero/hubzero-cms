<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Sef\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Sef plugin
 **/
class Migration20170831000000PlgSystemSef extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'sef');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'sef');
    }
}
