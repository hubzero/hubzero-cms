<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Tags\Blogs\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Tags - Blogs plugin
 *
 */
class Migration20170831000000PlgTagsBlogs extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('tags', 'blogs');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('tags', 'blogs');
    }
}
