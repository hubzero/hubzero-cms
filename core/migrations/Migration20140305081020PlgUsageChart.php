<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for deleting usage chart plugin
 *
*/
class Migration20140305081020PlgUsageChart extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deletePluginEntry('usage', 'chart');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addPluginEntry('usage', 'chart', 0);
    }
}
