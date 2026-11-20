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
class Migration20160412201638PlgResourcesGooglescholar extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->deletePluginEntry('resources', 'googleschlar');
        $this->deletePluginEntry('resources', 'googlesochlar');
        $this->addPluginEntry('resources', 'googlescholar');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'googlescholar');
        $this->addPluginEntry('resources', 'googleschlar');
    }
}
