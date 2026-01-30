<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Publications\Dublincore\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding entry for Publications - Dublincore plugin
 **/
class Migration20170831000000PlgPublicationsDublincore extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('publications', 'dublincore');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('publications', 'dublincore');
    }
}
