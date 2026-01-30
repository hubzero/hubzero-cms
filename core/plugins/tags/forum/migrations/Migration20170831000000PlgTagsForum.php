<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Tags\Forum\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Tags - Forum plugin
 **/
class Migration20170831000000PlgTagsForum extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('tags', 'forum');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('tags', 'forum');
    }
}
