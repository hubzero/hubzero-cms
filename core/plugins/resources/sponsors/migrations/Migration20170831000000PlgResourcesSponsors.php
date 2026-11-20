<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Sponsors\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding entry for Resources - Sponsors plugin
 **/
class Migration20170831000000PlgResourcesSponsors extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'sponsors');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'sponsors');
    }
}
