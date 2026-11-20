<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Handlers\Video\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Handlers - Video plugin
 **/
class Migration20190213000000PlgHandlersVideo extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('handlers', 'video');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('handlers', 'video');
    }
}
