<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\Geo\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding User - Geo plugin
 **/
class Migration20170831000000PlgUserGeo extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('user', 'geo');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('user', 'geo');
    }
}
