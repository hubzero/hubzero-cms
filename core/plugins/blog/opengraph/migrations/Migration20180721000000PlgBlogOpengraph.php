<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Blog\Opengraph\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Blog - Opengraph plugin
 *
*/
class Migration20180721000000PlgBlogOpengraph extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('blog', 'opengraph');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('blog', 'opengraph');
    }
}
