<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Pagenavigation\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Content - Pagenavigation plugin
 *
**/
class Migration20170831000000PlgContentPagenavigation extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('content', 'pagenavigation');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('content', 'pagenavigation');
    }
}
