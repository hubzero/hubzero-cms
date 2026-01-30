<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Groups\Blog\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Groups - Blog plugin
 *
*/
class Migration20170831000000PlgGroupsBlog extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('groups', 'blog');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('groups', 'blog');
    }
}
