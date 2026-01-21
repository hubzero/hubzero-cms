<?php


/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing Geocode - Googlebusiness plugin
  *
 **/
class Migration20250125150424PlgGeocodeGooglemapsbusiness extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deletePluginEntry('geocode', 'googlemapsbusiness');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addPluginEntry('geocode', 'googlemapsbusiness', 0);
    }
}
