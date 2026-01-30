<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Publications\Jsonld\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding entry for Publications - JSON-LD plugin
 **/
class Migration20180914000000PlgPublicationsJsonld extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('publications', 'jsonld');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('publications', 'jsonld');
    }
}
