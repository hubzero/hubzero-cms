<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'sponsors');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'sponsors');
    }
}
