<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('blog', 'opengraph');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('blog', 'opengraph');
    }
}
