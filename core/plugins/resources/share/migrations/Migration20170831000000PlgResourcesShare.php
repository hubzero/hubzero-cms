<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Share\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - Share plugin
 **/
class Migration20170831000000PlgResourcesShare extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'share');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'share');
    }
}
