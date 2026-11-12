<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Geocode\Ipstack\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Geocode - IpStack plugin
 **/
class Migration20250124193721PlgGeocodeIpstack extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('geocode', 'ipstack');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('geocode', 'ipstack');
    }
}
