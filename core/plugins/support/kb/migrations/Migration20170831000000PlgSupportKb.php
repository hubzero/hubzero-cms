<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Support\Kb\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Support - Kb plugin
 **/
class Migration20170831000000PlgSupportKb extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('support', 'kb');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('support', 'kb');
    }
}
