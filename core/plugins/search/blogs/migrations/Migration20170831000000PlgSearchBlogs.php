<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Search\Blogs\Migrations;

use Hubzero\Content\Migration\Base;

/**
*
 * Migration script for adding entry for Search - Blogs plugin
 *
*/
class Migration20170831000000PlgSearchBlogs extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('search', 'blogs');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('search', 'blogs');
    }
}
