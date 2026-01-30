<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Handlers\Markdown\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Handlers - Markdown plugin
 **/
class Migration20190213000000PlgHandlersMarkdown extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('handlers', 'markdown');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('handlers', 'markdown');
    }
}
