<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding plugins for Resource metadata
**/
class Migration20160412173638PlgResources extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'googlescholar');
        $this->addPluginEntry('resources', 'opengraph');
        $this->addPluginEntry('resources', 'dublincore');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'googlescholar');
        $this->deletePluginEntry('resources', 'opengraph');
        $this->deletePluginEntry('resources', 'dublincore');
    }
}
