<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Usage\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding component entry for com_usage
 **/
class Migration20170831000000ComUsage extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addComponentEntry('usage');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteComponentEntry('usage');
    }
}
