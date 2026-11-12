<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Tags\Support\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Tags - Support plugin
 **/
class Migration20170831000000PlgTagsSupport extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('tags', 'support');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('tags', 'support');
    }
}
