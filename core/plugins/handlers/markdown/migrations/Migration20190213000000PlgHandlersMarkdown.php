<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('handlers', 'markdown');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('handlers', 'markdown');
    }
}
