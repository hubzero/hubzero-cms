<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('wiki', 'editortoolbar');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('wiki', 'editortoolbar');
    }
}
