<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing the Geocode - MaxMindBinary plugin
 *
 * The plugin read MaxMind's GeoIP Legacy .dat databases, which MaxMind
 * stopped distributing in 2019, and could not run: it handed the provider
 * an HTTP client where the path to a database belonged, and had no setting
 * for that path. The plugin's files are gone; this removes its entry.
 */
class Migration20261110120000PlgGeocodeMaxmindbinary extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deletePluginEntry('geocode', 'maxmindbinary');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addPluginEntry('geocode', 'maxmindbinary', 0);
    }
}
