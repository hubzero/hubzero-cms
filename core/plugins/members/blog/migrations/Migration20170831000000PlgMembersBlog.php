<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Blog\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Members - Blog plugin
 *
*/
class Migration20170831000000PlgMembersBlog extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'blog');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'blog');
    }
}
