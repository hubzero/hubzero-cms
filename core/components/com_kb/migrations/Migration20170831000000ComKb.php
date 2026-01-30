<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Kb\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding component entry for com_kb
 **/
class Migration20170831000000ComKb extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('kb');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('kb');
    }
}
