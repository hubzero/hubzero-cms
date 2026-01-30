<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Tags\Collections\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Tags - Collections plugin
 **/
class Migration20170831000000PlgTagsCollections extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('tags', 'collections');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('tags', 'collections');
    }
}
