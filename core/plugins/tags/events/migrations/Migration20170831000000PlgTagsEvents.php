<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Tags\Events\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Tags - Events plugin
 **/
class Migration20170831000000PlgTagsEvents extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('tags', 'events');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('tags', 'events');
    }
}
