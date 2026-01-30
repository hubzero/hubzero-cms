<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\About\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - About plugin
 **/
class Migration20170831000000PlgResourcesAbout extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'about');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'about');
    }
}
