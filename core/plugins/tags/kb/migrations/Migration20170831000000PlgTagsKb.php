<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Tags\Kb\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Tags - Kb plugin
 **/
class Migration20170831000000PlgTagsKb extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('tags', 'kb');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('tags', 'kb');
    }
}
