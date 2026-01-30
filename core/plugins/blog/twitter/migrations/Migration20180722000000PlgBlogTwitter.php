<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Blog\Twitter\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Blog - Twitter plugin
 *
*/
class Migration20180722000000PlgBlogTwitter extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('blog', 'twitter');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('blog', 'twitter');
    }
}
