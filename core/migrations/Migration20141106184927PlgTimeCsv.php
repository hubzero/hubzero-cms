<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for csv time plugin
**/
class Migration20141106184927PlgTimeCsv extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('time', 'csv', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('time', 'csv');
    }
}
