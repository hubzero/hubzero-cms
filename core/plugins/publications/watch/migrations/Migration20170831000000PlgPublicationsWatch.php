<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Publications\Watch\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding entry for Publications - Watch plugin
 **/
class Migration20170831000000PlgPublicationsWatch extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('publications', 'watch');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('publications', 'watch');
    }
}
