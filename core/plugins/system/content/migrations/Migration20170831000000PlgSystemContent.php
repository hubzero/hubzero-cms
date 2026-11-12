<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Content\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Content plugin
 **/
class Migration20170831000000PlgSystemContent extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'content');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'content');
    }
}
