<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Findthistext\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - Findthistext plugin
 **/
class Migration20170831000000PlgResourcesFindthistext extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'findthistext', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'findthistext');
    }
}
