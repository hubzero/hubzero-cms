<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\P3p\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - P3P plugin
 **/
class Migration20170831000000PlgSystemP3p extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'p3p');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'p3p');
    }
}
