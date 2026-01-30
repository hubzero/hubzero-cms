<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Dublincore\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - Dublincore plugin
 **/
class Migration20170831000000PlgResourcesDublincore extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'dublincore');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'dublincore');
    }
}
