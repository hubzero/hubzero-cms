<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Editortoolbar\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Wiki - Editortoolbar plugin
 **/
class Migration20170831000000PlgWikiEditortoolbar extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('wiki', 'editortoolbar');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('wiki', 'editortoolbar');
    }
}
