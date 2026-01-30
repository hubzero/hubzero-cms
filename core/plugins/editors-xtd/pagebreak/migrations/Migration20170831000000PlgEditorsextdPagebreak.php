<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\EditorsXtd\Pagebreak\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Editors Extd - Pagebreak plugin
 *
**/
class Migration20170831000000PlgEditorsextdPagebreak extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('editors-xtd', 'pagebreak');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('editors-xtd', 'pagebreak');
    }
}
