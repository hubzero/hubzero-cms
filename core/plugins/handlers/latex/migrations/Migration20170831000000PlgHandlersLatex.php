<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Handlers\Latex\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Handlers - Latex plugin
 **/
class Migration20170831000000PlgHandlersLatex extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('handlers', 'latex');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('handlers', 'latex');
    }
}
