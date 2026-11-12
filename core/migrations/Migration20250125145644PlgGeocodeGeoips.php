<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing Geocode - Geoips plugin
 *
*/
class Migration20250125145644PlgGeocodeGeoips extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->deletePluginEntry('geocode', 'geoips');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->addPluginEntry('geocode', 'geoips', 0);
    }
}
