<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Editors\Codemirror\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Editors - Codemirror plugin
 **/
class Migration20170831000000PlgEditorsCodemirror extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('editors', 'codemirror', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('editors', 'codemirror');
    }
}
