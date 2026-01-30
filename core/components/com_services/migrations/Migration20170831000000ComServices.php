<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Services\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding component entry for com_services
 **/
class Migration20170831000000ComServices extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('services');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('services');
    }
}
