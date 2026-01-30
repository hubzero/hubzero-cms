<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\EditorsXtd\Image\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Editors Extd - Image plugin
 *
**/
class Migration20170831000000PlgEditorsextdImage extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('editors-xtd', 'image');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('editors-xtd', 'image');
    }
}
