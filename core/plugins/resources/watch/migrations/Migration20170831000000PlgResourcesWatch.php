<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Watch\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - Watch plugin
 **/
class Migration20170831000000PlgResourcesWatch extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'watch');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'watch');
    }
}
