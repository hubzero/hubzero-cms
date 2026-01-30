<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding component entry for com_dataviewer
 **/
class Migration20170831000000ComDataviewer extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('dataviewer');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('dataviewer');
    }
}
